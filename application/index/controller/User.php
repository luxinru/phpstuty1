<?php

// +----------------------------------------------------------------------
// | ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2014~2019 
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 

// +----------------------------------------------------------------------

namespace app\index\controller;

use library\Controller;
use think\Db;
use think\facade\Cookie;

/**
 * 登录控制器
 */
class User extends Controller
{

    protected $table = 'xy_users';

    /**
     * 空操作 用于显示错误页面
     */
    public function _empty($name){

        return $this->fetch($name);
    }
    public function lang(){
        
         $lang=input('type');
          switch ($lang) {
              case 'en-ww':
                  cookie('think_var', 'en-ww');
              break;
              case 'zh-cn':
                  cookie('think_var', 'zh-cn');
              break;
              case 'en-in':
                 cookie('think_var','en-in');
              break;
              case 'jp-jp':
                 cookie('think_var','jp-jp');
              break;
          }

    }
    //用户登录页面
    public function login()
    {
        $lang=cookie('lang');
        if(cookie('user_id')) $this->redirect('index/index');
        $this->assign('lang',$lang);
        return $this->fetch();
    }

    //用户登录接口
    // 允许使用 用户名 登陆
    public function do_login()
    {
        // $this->applyCsrfToken();//验证令牌
        $tel = input('post.tel/s','');
        /*if(!is_mobile($tel)){
            return json(['code'=>1,'info'=>'手机号码格式不正确']);
        }*/
        // $num = Db::table($this->table)->where(['tel'=>$tel])->count();
        $num = Db::table($this->table)->where('tel', $tel)->whereOr("username", $tel)->count();
        if(!$num){
            return json(['code'=>1,'info'=>lang('账号不存在')]);
        }

        $pwd         = input('post.pwd/s', ''); 
        $keep        = input('post.keep/b', false);    
        $jizhu        = input('post.jizhu/s', 0);

        // $userinfo = Db::table($this->table)->field('id,pwd,salt,pwd_error_num,allow_login_time,status,login_status,headpic,level')->where('tel',$tel)->find();
        $userinfo = Db::table($this->table)->field('id,pwd,salt,pwd_error_num,allow_login_time,status,login_status,headpic,level')->where('tel', $tel)->whereOr("username", $tel)->find();
        if(!$userinfo)return json(['code'=>1,'info'=>lang('用户不存在')]);
        if($userinfo['status'] != 1)return json(['code'=>1,'info'=>lang('用户已被禁用')]);
        //if($userinfo['login_status'])return ['code'=>1,'info'=>'此账号已在别处登录状态'];
        if($userinfo['allow_login_time'] && ($userinfo['allow_login_time'] > time()) && ($userinfo['pwd_error_num'] > config('pwd_error_num')))return ['code'=>1,'info'=>lang('密码连续错误次数太多，请').config('allow_login_min').lang('分钟后再试')];
        if($userinfo['pwd'] != sha1($pwd.$userinfo['salt'].config('pwd_str'))){
            Db::table($this->table)->where('id',$userinfo['id'])->update(['pwd_error_num'=>Db::raw('pwd_error_num+1'),'allow_login_time'=>(time()+(config('allow_login_min') * 60))]);
            return json(['code'=>1,'info'=>lang('密码错误')]);  
        }
        
        Db::table($this->table)->where('id',$userinfo['id'])->update(['pwd_error_num'=>0,'allow_login_time'=>0,'login_status'=>1, 'ip'=>$this->request->ip()]);
        session('user_id',$userinfo['id']);
        session('avatar',$userinfo['headpic']);
        session('level', $userinfo['level']);
        cookie('user_id',$userinfo['id']);

        if ($jizhu) {
            cookie('tel',$tel);
            cookie('pwd',$pwd);
        }

        if($keep){
            Cookie::forever('user_id',$userinfo['id']);
            Cookie::forever('tel',$tel);
            Cookie::forever('pwd',$pwd);
        }
        return json(['code'=>0,'info'=>lang('登录成功!')]);  
    }

    /**
     * 用户注册接口
     */
    public function do_register()
    {
        $tel = input('post.tel/s','');
        $user_name   = input('post.tel/s','');
        //$user_name = '';    //交给模型随机生成用户名
        $verify      = input('post.verify');       //短信验证码
        $pwd         = input('post.pwd/s', '');
        $pwd2        = input('post.deposit_pwd/s', '');
        
        
        $zjpwd        = input('post.pwd2/s', '');
        
        if($pwd2 != $pwd){
            return json(['code'=>1,'info'=>lang('两次密码输入不一致')]);
        }
        
        $invite_code = input('post.invite_code/s', '');     //邀请码
        // if(!$verify || $verify != session('yzm') ) return json(['code'=>1,'info'=>lang('验证码错误')]);
        if(!$invite_code) return json(['code'=>1,'info'=>lang('邀请码不能为空')]);

        // if(config('app.duanxin')['duanxin_status'] != 2){
        //     $verify_msg = Db::table('xy_verify_msg')->field('msg,addtime')->where(['tel'=>$tel,'type'=>1])->find();
        //     if(!$verify_msg)return json(['code'=>1,'info'=>lang('验证码不存在')]);
        //     if($verify != $verify_msg['msg'])return json(['code'=>1,'info'=>lang('验证码错误')]);
        //     if(($verify_msg['addtime'] + (config('app.zhangjun_sms.min')*60)) < time())return json(['code'=>1,'info'=>lang('验证码已失效')]);
        // }

        $pid = 0;
        if($invite_code) {
            $parentinfo = Db::table($this->table)->field('id,status')->where('invite_code',$invite_code)->find();
            if(!$parentinfo) return json(['code'=>1,'info'=>lang('邀请码不存在')]);
            if($parentinfo['status'] != 1) return json(['code'=>1,'info'=>lang('该推荐用户已被禁用')]);

            $pid = $parentinfo['id'];
        }

        $res = model('admin/Users')->add_users($tel,$user_name,$pwd,$pid,'',$zjpwd);
        return json($res);
    }


    public function logout(){
        \Session::delete('user_id');
        Cookie::delete('user_id');
        $this->redirect('login');
    }

    /**
     * 重置密码
     */
    public function do_forget()
    {
        if(!request()->isPost()) return json(['code'=>1,'info'=>lang('错误请求')]);
        $tel = input('post.tel/s','');
        $pwd = input('post.pwd/s','');
        $verify = input('post.verify/d',0);
        if(config('app.verify')){
            $verify_msg = Db::table('xy_verify_msg')->field('msg,addtime')->where(['tel'=>$tel,'type'=>2])->find();
            if(!$verify_msg)return json(['code'=>1,'info'=>lang('验证码不存在')]);
            if($verify != $verify_msg['msg'])return json(['code'=>1,'info'=>lang('验证码错误')]);
            if(($verify_msg['addtime'] + (config('app.zhangjun_sms.min')*60)) < time())return json(['code'=>1,'info'=>lang('验证码已失效')]);
        }
        $res = model('admin/Users')->reset_pwd($tel,$pwd);
        return json($res);
    }

    public function register()
    {
        $param = \Request::param(true);
        $this->invite_code = isset($param[1]) ? trim($param[1]) : '';  
        return $this->fetch();
    }
    
     //异步回调
    public function hui(){
       // echo 1;
        // $post = json_decode($_POST,true);
        $post=file_get_contents("php://input");
        $post=json_decode($post,true);
        $key=array_keys($post);
        if(in_array('qrurl',$key)){
            return "success";
        }
        $oinfo =  db('xy_recharge')->find($post['orderid']);
        if($oinfo['status']== 1){
          if($post['ispay']==1){
            $res = Db::name('xy_recharge')->where('id',$post['orderid'])->update(['endtime'=>time(),'status'=>2]);
             if ($oinfo['is_vip']) {
                    $res1 = Db::name('xy_users')->where('id',$oinfo['uid'])->update(['level'=>$oinfo['level']]);
                }else{
                    $res1 = Db::name('xy_users')->where('id',$oinfo['uid'])->setInc('balance',$oinfo['num']);
                }
                
                $res2 = Db::name('xy_balance_log')
                        ->insert([
                            'uid'=>$oinfo['uid'],
                            'oid'=>$post['orderid'],
                            'num'=>$oinfo['num'],
                            'type'=>1,
                            'status'=>1,
                            'addtime'=>time(),
                        ]);
          
            }else{
                $res = Db::name('xy_recharge')->where('id',$post['orderid'])->update(['endtime'=>time(),'status'=>3]);
                $res1 = Db::name('xy_message')
                        ->insert([
                            'uid'=>$oinfo['uid'],
                            'type'=>2,
                            'title'=>'system notification',
                            'content'=>'Top-up order'.$post['orderid'].'Has been returned, if you have any questions, please contact customer service',
                            'addtime'=>time()
                        ]);
            }
            
                Db::commit();

                if ($oinfo['is_vip']) {
                    goto end;
                }

                /************* 发放推广奖励 *********/
                $uinfo = Db::name('xy_users')->field('id,active')->find($oinfo['uid']);
                if($uinfo['active']===0){
                    Db::name('xy_users')->where('id',$uinfo['id'])->update(['active'=>1]);
                    //将账号状态改为已发放推广奖励
                    $userList = model('Users')->parent_user($uinfo['id'],3);
                    if($userList){
                        foreach($userList as $v){
                            if($v['status']===1 && ($oinfo['num'] * config($v['lv'].'_reward') != 0)){
                                    Db::name('xy_reward_log')
                                    ->insert([
                                        'uid'=>$v['id'],
                                        'sid'=>$uinfo['id'],
                                        'oid'=>$post['orderid'],
                                        'num'=>$oinfo['num'] * config($v['lv'].'_reward'),
                                        'lv'=>$v['lv'],
                                        'type'=>1,
                                        'status'=>1,
                                        'addtime'=>time(),
                                    ]);
                            }
                        }
                    }
                }
                /************* 发放推广奖励 *********/

                end:
                return "success";
            
        }
         return "success";
    }
   public function hui2(){
       // echo 1;
        $post = $_POST;
        $oinfo =  db('xy_recharge')->find($post['mchOrderNo']);
        if($oinfo['status'] == 1){
          if($post['tradeResult']==1){
            $res = Db::name('xy_recharge')->where('id',$post['mchOrderNo'])->update(['endtime'=>time(),'status'=>2]);
             if ($oinfo['is_vip']) {
                    $res1 = Db::name('xy_users')->where('id',$oinfo['uid'])->update(['level'=>$oinfo['level']]);
                }else{
                    $res1 = Db::name('xy_users')->where('id',$oinfo['uid'])->setInc('balance',$oinfo['num']);
                }
                
                $res2 = Db::name('xy_balance_log')
                        ->insert([
                            'uid'=>$oinfo['uid'],
                            'oid'=>$post['mchOrderNo'],
                            'num'=>$oinfo['num'],
                            'type'=>1,
                            'status'=>1,
                            'addtime'=>time(),
                        ]);
          
            }else{
                $res = Db::name('xy_recharge')->where('id',$post['mchOrderNo'])->update(['endtime'=>time(),'status'=>3]);
                $res1 = Db::name('xy_message')
                        ->insert([
                            'uid'=>$oinfo['uid'],
                            'type'=>2,
                            'title'=>'system notification',
                            'content'=>'Top-up order'.$post['mchOrderNo'].'Has been returned, if you have any questions, please contact customer service',
                            'addtime'=>time()
                        ]);
            }
            if($res && $res1){
                Db::commit();

                if ($oinfo['is_vip']) {
                    goto end;
                }

                /************* 发放推广奖励 *********/
                $uinfo = Db::name('xy_users')->field('id,active')->find($oinfo['uid']);
                if($uinfo['active']===0){
                    Db::name('xy_users')->where('id',$uinfo['id'])->update(['active'=>1]);
                    //将账号状态改为已发放推广奖励
                    $userList = model('Users')->parent_user($uinfo['id'],3);
                    if($userList){
                        foreach($userList as $v){
                            if($v['status']===1 && ($oinfo['num'] * config($v['lv'].'_reward') != 0)){
                                    Db::name('xy_reward_log')
                                    ->insert([
                                        'uid'=>$v['id'],
                                        'sid'=>$uinfo['id'],
                                        'oid'=>$post['mchOrderNo'],
                                        'num'=>$oinfo['num'] * config($v['lv'].'_reward'),
                                        'lv'=>$v['lv'],
                                        'type'=>1,
                                        'status'=>1,
                                        'addtime'=>time(),
                                    ]);
                            }
                        }
                    }
                }
                /************* 发放推广奖励 *********/

                end:
                return "SUCCESS";
            }else{
                return 'error';
            }
        }
    }
    public function ti(){
        $post = $_POST;
        if($post){
            $rows=Db::name("xy_deposit")->where('id',$post['merTransferId'])->find();
            if($rows['status']==4){
                if($post['tradeResult']==1){
                    $data=['status'=>2];
                    Db::name("xy_deposit")->where('id',$rows['id'])->update($data);
                    Db::name('xy_balance_log')->where('oid',$rows['id'])->update(['status'=>1]);
                    return "success";
                }else{
                    $data=['status'=>3];
                    Db::name("xy_deposit")->where('id',$rows['id'])->update($data);
                    Db::name('xy_users')->where('id',$rows['uid'])->setInc('balance',$rows['num']);
                    return "error";
                }
            }
        }
    }
    public function ti2(){
        $post=file_get_contents("php://input");
        $post=json_decode($post,true);
        if($post){
            $rows=Db::name("xy_deposit")->where('id',$post['orderid'])->find();
            if($rows['status']==4){
                $key=array_keys($post);
                if(in_array('iscancel',$key)){
                    $data=['status'=>3];
                    Db::name("xy_deposit")->where('id',$rows['id'])->update($data);
                    Db::name('xy_users')->where('id',$rows['uid'])->setInc('balance',$rows['num']);
                    return "success";
                }
                if($post['ispay']==1){
                    $data=['status'=>2];
                    Db::name("xy_deposit")->where('id',$rows['id'])->update($data);
                    Db::name('xy_balance_log')->where('oid',$rows['id'])->update(['status'=>1]);
                    return "success";
                }else{
                    $data=['status'=>3];
                    Db::name("xy_deposit")->where('id',$rows['id'])->update($data);
                    Db::name('xy_users')->where('id',$rows['uid'])->setInc('balance',$rows['num']);
                    return "error";
                }
            }
        }
    }
    
    public function lixibao(){
        $time=time();
        //查询未取出订单  并且今天还未 收益订单
        $starttime=strtotime(date('y-m-d',$time));
        $endtime=strtotime(date('y-m-d',strtotime("+1 day")));
        $rows=Db::name('xy_lixibao')->where('is_qu',0)->whereTime('update_time', 'not between', [$starttime, $endtime])->select();
        //结算时间  到了自动结算   (间隔一天才能结算)
        foreach($rows as $k=>$v){
            // $zhi=Db::name('xy_lixibao_list')->where('id',$v['sid'])->find();
            if(time()>=($v['addtime']+86400) ){
                if(time()>=$v['endtime']){
                    // $money=($v['num']+$v['yuji_num'])-($v['num']*$zhi['shouxu']);
                    $userfo = Db::name('xy_users')->where('id', $v['uid'])->find();
                    $data=[
                      "balance" => ($userfo['balance'] + $v['num'] + $v['yuji_num']),
                      "freeze_balance" => "0.00",
                      "lixibao_balance" => "0",
                      "lixibao_dj_balance" => "0.0000"
                    ];
                    Db::name('xy_users')->where('id',$v['uid'])->update($data);
                    Db::name('xy_lixibao')->where('uid', $v['uid'])->update(['is_qu'=>1]);
                    // $rows=Db::name('xy_lixibao')->where('id',$v['id'])->update($data);
                    // Db::name('xy_users')->where('id',$v['uid'])->setInc('balance',$money);
                    echo '已自动结算';
                }else{
                    //添加昨日收益
                    $z_time=$time-86400;
                    db('xy_balance_log')->insert([
                        'uid'=>$v['uid'],
                         'oid'=>getSn('LXB'),
                          'num'=>$v['bili']*$v['num'],
                          'type'=>23,
                          'status'=>1,
                          'addtime'=>$z_time
                    ]);
                     Db::name('xy_lixibao')->where('uid', $v['uid'])->setInc(['sum_shouyi'=>$v['bili']*$v['num']]);
                    
                    
                     Db::name('xy_lixibao')->where('uid', $v['uid'])->update(['update_time'=>$z_time]);
                     
                    
                    echo '每日收益已发放';
                    
                }
            }
         
        }
        
        
       
        
        
        if(empty($rows)){
            echo '没有数据';
        }
        
    }
      public function reset_qrcode()
    {
        $uinfo = Db::name('xy_users')->field('id,invite_code')->select();
        foreach ($uinfo as $v) {
            $model = model('admin/Users');
            $model->create_qrcode($v['invite_code'],$v['id']);
        }
        return '重新生成用户二维码图片成功';
    } 
}