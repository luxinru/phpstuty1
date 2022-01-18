<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Db;

class My extends Base
{
    protected $msg = ['__token__'  => '请不要重复提交！'];
    /**
     * 首页
     */
    public function index()
    {
        $this->info = db('xy_users')->field('username,tel,level,id,headpic,balance,freeze_balance,lixibao_balance,invite_code,show_td')->find(session('user_id'));
        $this->lv3 = [0,config('vip_3_num')];
        $this->lv2 = [0,config('vip_2_num')];
        $this->sell_y_num = db('xy_convey')->where('status',1)->where('uid',session('user_id'))->sum('commission');

        $level = $this->info['level'];
        !$level ? $level = 0 :'';

        $this->level_name = db('xy_level')->where('level',$level)->value('name');

        $this->info['lixibao_balance'] = number_format($this->info['lixibao_balance'],2);

        $this->rililv = config('lxb_bili')*100 .'%' ;
        $this->lxb_shouyi = db('xy_lixibao')->where('status',1)->where('uid',session('user_id'))->sum('num');

        return $this->fetch();
    }

    /**
     * 获取收货地址
     */
    public function get_address()
    {
        $id = session('user_id');
        $data = db('xy_member_address')->where('uid',$id)->select();
        if($data)
            return json(['code'=>0,'info'=>'获取成功','data'=>$data]);
        else
            return json(['code'=>1,'info'=>'暂未数据']);
    }

    public function reload()
    {
        $id = session('user_id');;
        $user = db('xy_users')->find($id);

        $n = ($id%20);

        $dir = './upload/qrcode/user/'.$n . '/' . $id . '.png';
        if(file_exists($dir)) {
            unlink($dir);
        }

        $res = model('admin/Users')->create_qrcode($user['invite_code'],$id);
        if(0 && $res['code']!==0){
            return $this->error('失败');
        }
        return $this->success('成功');
    }


    /**c
     * 添加收货地址
     */
    public function add_address()
    {
        if(request()->isPost()){
            $uid = session('user_id');
            $name = input('post.name/s','');
            $tel = input('post.tel/s','');
            $address = input('post.address/s','');
            $area = input('post.area/s','');
            $token = input("token");//获取提交过来的令牌
            $data = ['__token__' => $token];
            $validate   = \Validate::make($this->rule,$this->msg);
            if (!$validate->check($data)) {
                return json(['code'=>1,'info'=>$validate->getError()]);
            }
            $data = [
                'uid'       => $uid,
                'name'      => $name,
                'tel'       => $tel,
                'area'      => $area,
                'address'   => $address,
                'addtime'   => time()
            ];
            $tmp = db('xy_member_address')->where('uid',$uid)->find();
            if(!$tmp) $data['is_default']=1;
            $res = db('xy_member_address')->insert($data);
            if($res)
                return json(['code'=>0,'info'=>'操作成功']);
            else
                return json(['code'=>1,'info'=>'操作失败']);
        }
        return json(['code'=>1,'info'=>'错误请求']);
    }

    /**
     * 编辑收货地址
     */
    public function edit_address()
    {
        if(request()->isPost()){
            $uid        = session('user_id');
            $name       = input('post.shoujianren/s','');
            $tel        = input('post.shouhuohaoma/s','');
            $address    = input('post.address/s','');

            $area       = input('post.area/s','');


            $ainfo = db('xy_member_address')->where('uid',$uid)->find();
            if ($ainfo) {
                $res = db('xy_member_address')
                    ->where('id',$ainfo['id'])
                    ->update([
                        'uid'       => $uid,
                        'name'      => $name,
                        'tel'       => $tel,
                        'area'      => $area,
                        'address'   => $address,
                        //'addtime'   => time()
                    ]);
            }else{
                $res = db('xy_member_address')
                    ->insert([
                        'uid'       => $uid,
                        'name'      => $name,
                        'tel'       => $tel,
                        'area'      => $area,
                        'address'   => $address,
                        'addtime'   => time()
                    ]);
            }

            if($res)
                return json(['code'=>0,'info'=>'操作成功']);
            else
                return json(['code'=>1,'info'=>'操作失败']);
        }elseif (request()->isGet()) {
            $id = session('user_id');
            $this->info = db('xy_member_address')->where('uid',$id)->find();
            $this->tel_bak = !empty($this->info['tel']) ? substr($this->info['tel'], 0, 3) . '****' . substr($this->info['tel'], 7, 10) : '';

            return $this->fetch();
        }
    }

    public function team()
    {
        $uid = session('user_id');
        //$this->info = db('xy_member_address')->where('uid',$id)->find();
        $uids = model('admin/Users')->child_user($uid,5);
        array_push($uids,$uid);
        $uids ? $where[] = ['uid','in',$uids] : $where[] = ['uid','in',[-1]];

        $datum['sl'] = count($uids);
        $datum['yj'] = db('xy_convey')->where('status',1)->where($where)->sum('num');
        $datum['cz'] = db('xy_recharge')->where('status',2)->where($where)->sum('num');
        $datum['tx'] = db('xy_deposit')->where('status',2)->where($where)->sum('num');


        //
        $uids1 = model('admin/Users')->child_user($uid,1);
        $uids1 ? $where1[] = ['sid','in',$uids1] : $where1[] = ['sid','in',[-1]];
        $datum['log1'] = db('xy_balance_log')->where('uid',$uid)->where($where1)->where('f_lv',1)->sum('num');

        $uids2 = model('admin/Users')->child_user($uid,2);
        $uids2 ? $where2[] = ['sid','in',$uids2] : $where2[] = ['sid','in',[-1]];
        $datum['log2'] = db('xy_balance_log')->where('uid',$uid)->where($where2)->where('f_lv',2)->sum('num');

        $uids3 = model('admin/Users')->child_user($uid,3);
        $uids3 ? $where3[] = ['sid','in',$uids3] : $where3[] = ['sid','in',[-1]];
        $datum['log3'] = db('xy_balance_log')->where('uid',$uid)->where($where3)->where('f_lv',3)->sum('num');


        $uids5 = model('admin/Users')->child_user($uid,5);
        $uids5 ? $where5[] = ['sid','in',$uids5] : $where5[] = ['sid','in',[-1]];
        $datum['yj2'] = db('xy_convey')->where('status',1)->where($where)->sum('commission');
        $datum['yj3'] = db('xy_balance_log')->where('uid',$uid)->where($where5)->where('type',6)->sum('num');;


        $this->info = $datum;

        return $this->fetch();
    }

    public function caiwu()
    {
        $id = session('user_id');
        $day      = input('get.day/s','');
        $where=[];
        if ($day) {
            $start = strtotime("-$day days");
            $where[] = ['addtime','between',[$start,time()]];
        }

        $start      = input('get.start/s','');
        $end        = input('get.end/s','');
        if ($start || $end) {
            $start ? $start = strtotime($start) : $start = strtotime('2020-01-01');
            $end ? $end = strtotime($end.' 23:59:59') : $end = time();
            $where[] = ['addtime','between',[$start,$end]];
        }


        $this->start = $start ? date('Y-m-d',$start) : '';
        $this->end = $end ? date('Y-m-d',$end) : '';

        $this->type = $type = input('get.type/d',0);

        if ($type == 1) {
            $where['type'] = 7;
        }elseif($type==2) {
            $where['type'] = 1;
        }


        $this->_query('xy_balance_log')
            ->where('uid',$id)->where($where)->order('id desc')->page();
        //var_dump($_REQUEST);die;
    }


    public function headimg()
    {
        $uid = session('user_id');
        if(request()->isPost()) {
            $username = input('post.pic/s', '');
            $res = db('xy_users')->where('id',session('user_id'))->update(['headpic'=>$username]);
            if($res!==false){
                return json(['code'=>0,'info'=>'操作成功']);
            }else{
                return json(['code'=>1,'info'=>'操作失败']);
            }
        }
        $this->info = db('xy_users')->find($uid);
        return $this->fetch();
    }

    public function bind_bank()
    {
        $id = input('post.id/d',0);
        $uid = session('user_id');
        $info = db('xy_bankinfo')->where('uid',$uid)->find();
        $uinfo = db('xy_users')->find($uid);

        if(request()->isPost()){
            $username = input('post.username/s','');
            $bankname = input('post.bankname/s','');
            $cardnum = input('post.card/s','');
            $site  = input('post.zhihang/s','');
            $qq  = input('post.qq/s','');
            $address  = input('post.address/s','');

            //同一姓名和卡号只绑定一次
            $res = db('xy_bankinfo')->where('username',$username)->where('cardnum',$cardnum)->find();
            if ($res && $res['uid'] != $uid) {
                return json(['code'=>1,'info'=>'该姓名和银行卡已绑定其他账号!']);
            }


            $data =array(
                'username' =>$username,
                'bankname' =>$bankname,
                'cardnum' =>$cardnum,
                'site' =>$site,
                'address' =>$address,
                'qq' =>$qq,
                'tel' =>$uinfo['tel'],
                'status' =>1,
                'uid' =>$uid
            );

            if ($info) {
                $res = db('xy_bankinfo')->where('uid',$uid)->update($data);
            }else{
                $res = db('xy_bankinfo')->insert($data);
            }

            if($res){
                return json(['code'=>0,'info'=>'操作成功']);
            }else{
                return json(['code'=>1,'info'=>'操作失败']);
            }
        }


        $this->info = $info;
        $this->uinfo = $uinfo;
        $this->cardnum_bak = !empty($this->info['cardnum']) ? substr_replace($this->info['cardnum'],'****', 7 ,7) : '';
        $this->tel_bak = !empty($this->info['tel']) ? substr($this->info['tel'], 0, 3) . '****' . substr($this->info['tel'], 7, 10) : '';
        return $this->fetch();
    }

    public function bind_zhifubao()
    {
        $uid = session('user_id');
        $info = db('xy_bankinfo')->where('uid',$uid)->find();

        if(request()->isPost()){
            $username = input('post.username/s','');
            $zhifunum = input('post.zhifunum/s','');

            //同一姓名和卡号只绑定一次
            $res = db('xy_bankinfo')->where('username',$username)->where('zhifunum',$zhifunum)->find();
            if ($res && $res['uid'] != $uid) {
                return json(['code'=>1,'info'=>'该姓名和支付宝已绑定其他账号!']);
            }

            $data =array(
                'username' =>$username,
                'zhifunum' =>$zhifunum,
                'status' =>1,
                'uid' =>$uid
            );

            if ($info) {
                $res = db('xy_bankinfo')->where('uid',$uid)->update($data);
            }else{
                $res = db('xy_bankinfo')->insert($data);
            }

            if($res){
                return json(['code'=>0,'info'=>'操作成功']);
            }else{
                return json(['code'=>1,'info'=>'操作失败']);
            }
        }


        $this->info = $info;
        $this->zhifunum_bak = !empty($this->info['zhifunum']) ? substr($this->info['zhifunum'], 0, 3) . '****' . substr($this->info['zhifunum'], 7, 10) : '';
        $uinfo = db('xy_users')->find($uid);
        $this->uinfo = $uinfo;
        return $this->fetch();
    }




    /**
     * 设置默认收货地址
     */
    public function set_address()
    {
        if(request()->isPost()){
            $id = input('post.id/d',0);
            Db::startTrans();
            $res = db('xy_member_address')->where('uid',session('user_id'))->update(['is_default'=>0]);
            $res1 = db('xy_member_address')->where('id',$id)->update(['is_default'=>1]);
            if($res && $res1){
                Db::commit();
                return json(['code'=>0,'info'=>'操作成功']);
            }else{
                Db::rollback();
                return json(['code'=>1,'info'=>'操作失败']);
            }
        }
        return json(['code'=>1,'info'=>'错误请求']);
    }

    /**
     * 删除收货地址
     */
    public function del_address()
    {
        if(request()->isPost()){
            $id = input('post.id/d',0);
            $info = db('xy_member_address')->find($id);
            if($info['is_default']==1){
                return json(['code'=>1,'info'=>'不能删除默认收货地址']);
            }
            $res = db('xy_member_address')->where('id',$id)->delete();
            if($res)
                return json(['code'=>0,'info'=>'操作成功']);
            else
                return json(['code'=>1,'info'=>'操作失败']);
        }
        return json(['code'=>1,'info'=>'错误请求']);
    }

    public function get_bot(){
        $data = model('admin/Users')->get_botuser(session('user_id'),3);
        halt($data);
    }





    public function yue(){
        $uid = session('user_id');
        $this->info = db('xy_users')->find($uid);
        return $this->fetch();
    }


    public function edit_username(){
        $uid = session('user_id');
        if(request()->isPost()) {
            $username = input('post.username/s', '');
            $res = db('xy_users')->where('id',session('user_id'))->update(['username'=>$username]);
            if($res!==false){
                return json(['code'=>0,'info'=>'操作成功']);
            }else{
                return json(['code'=>1,'info'=>'操作失败']);
            }
        }
        $this->info = db('xy_users')->find($uid);
        return $this->fetch();
    }



    /**
     * 用户账号充值
     */
    public function user_recharge()
    {
        $tel = input('post.tel/s','');
        $num = input('post.num/d',0);
        $pic = input('post.pic/s','');
        $real_name = input('post.real_name/s','');
        $uid = session('user_id');

        if(!$pic || !$num ) return json(['code'=>1,'info'=>'参数错误']);
        //if(!is_mobile($tel)) return json(['code'=>1,'info'=>'手机号码格式不正确']);

        if (is_image_base64($pic))
            $pic = '/' . $this->upload_base64('xy',$pic);  //调用图片上传的方法
        else
            return json(['code'=>1,'info'=>'图片格式错误']);
        $id = getSn('SY');
        $res = db('xy_recharge')
            ->insert([
                'id'        => $id,
                'uid'       => $uid,
                'tel'       => $tel,
                'real_name' => $real_name,
                'pic'       => $pic,
                'num'       => $num,
                'addtime'   => time()
            ]);
        if($res)
            return json(['code'=>0,'info'=>'提交成功']);
        else
            return json(['code'=>1,'info'=>'提交失败，请稍后再试']);
    }

    //邀请界面
    public function invite()
    {
        $uid = session('user_id');
        $this->assign('pic','/upload/qrcode/user/'.($uid%20).'/'.$uid.'.png');

        $user = db('xy_users')->find($uid);

        $url = SITE_URL . url('@index/user/register/invite_code/'.$user['invite_code']);
        $this->assign('url',$url);
        $this->assign('invite_code',$user['invite_code']);
        return $this->fetch();
    }

    //我的资料
    public function do_my_info()
    {
        if(request()->isPost()){
            $headpic    = input('post.headpic/s','');
            $wx_ewm    = input('post.wx_ewm/s','');
            $zfb_ewm    = input('post.zfb_ewm/s','');
            $nickname   = input('post.nickname/s','');
            $sign       = input('post.sign/s','');
            $data = [
                'nickname'  => $nickname,
                'signiture' => $sign
            ];

            if($headpic){
                if (is_image_base64($headpic))
                    $headpic = '/' . $this->upload_base64('xy',$headpic);  //调用图片上传的方法
                else
                    return json(['code'=>1,'info'=>'图片格式错误']);
                $data['headpic'] = $headpic;
            }

            if($wx_ewm){
                if (is_image_base64($wx_ewm))
                    $wx_ewm = '/' . $this->upload_base64('xy',$wx_ewm);  //调用图片上传的方法
                else
                    return json(['code'=>1,'info'=>'图片格式错误']);
                $data['wx_ewm'] = $wx_ewm;
            }

            if($zfb_ewm){
                if (is_image_base64($zfb_ewm))
                    $zfb_ewm = '/' . $this->upload_base64('xy',$zfb_ewm);  //调用图片上传的方法
                else
                    return json(['code'=>1,'info'=>'图片格式错误']);
                $data['zfb_ewm'] = $zfb_ewm;
            }




            $res = db('xy_users')->where('id',session('user_id'))->update($data);
            if($res!==false){
                if($headpic) session('avatar',$headpic);
                return json(['code'=>0,'info'=>'操作成功']);
            }else{
                return json(['code'=>1,'info'=>'操作失败']);
            }
        }elseif(request()->isGet()){
            $info = db('xy_users')->field('username,headpic,nickname,signiture sign,wx_ewm,zfb_ewm')->find(session('user_id'));
            return json(['code'=>0,'info'=>'请求成功','data'=>$info]);
        }
    }

    // 消息
    public function activity()
    {
        $where[] = ['title','like','%' . '活动' . '%'];

        $this->msg = db('xy_index_msg')->where($where)->select();
        return $this->fetch();
    }



    // 消息
    public function msg()
    {
        $this->info = db('xy_message')->alias('m')
            // ->leftJoin('xy_users u','u.id=m.sid')
            ->leftJoin('xy_reads r','r.mid=m.id and r.uid='.session('user_id'))
            ->field('m.*,r.id rid')
            ->where('m.uid','in',[0,session('user_id')])
            ->order('addtime desc')
            ->select();

        $this->msg = db('xy_index_msg')->where('status',1)->select();
        return $this->fetch();
    }

    // 消息
    public function detail()
    {
        $id = input('get.id/d',0);

        $this->msg = db('xy_index_msg')->where('id',$id)->find();



        return $this->fetch();
    }

    //记录阅读情况
    public function reads()
    {
        if(\request()->isPost()){
            $id = input('post.id/d',0);
            db('xy_reads')->insert(['mid'=>$id,'uid'=>session('user_id'),'addtime'=>time()]);
            return $this->success('成功');
        }
    }

    public function gonggao()
    {
        
    }

    //修改绑定手机号
    public function reset_tel()
    {
        $pwd = input('post.pwd','');
        $verify = input('post.verify/s','');
        $tel = input('post.tel/s','');
        $userinfo = Db::table('xy_users')->field('id,pwd,salt')->find(session('user_id'));
        if($userinfo['pwd'] != sha1($pwd.$userinfo['salt'].config('pwd_str'))) return json(['code'=>1,'info'=>'登录密码错误']);
        if(config('app.verify')){
            $verify_msg = Db::table('xy_verify_msg')->field('msg,addtime')->where(['tel'=>$tel,'type'=>3])->find();
            if(!$verify_msg) return json(['code'=>1,'info'=>'验证码不存在']);
            if($verify != $verify_msg['msg']) return json(['code'=>1,'info'=>'验证码错误']);
            if(($verify_msg['addtime'] + (config('app.zhangjun_sms.min')*60)) < time())return json(['code'=>1,'info'=>'验证码已失效']);
        }
        $res = db('xy_users')->where('id',session('user_id'))->update(['tel'=>$tel]);
        if($res!==false)
            return json(['code'=>0,'info'=>'操作成功']);
        else
            return json(['code'=>1,'info'=>'操作失败']);
    }

    //团队佣金列表
    public function get_team_reward()
    {
        $uid = session('user_id');
        $lv = input('post.lv/d',1);
        $num = Db::name('xy_reward_log')->where('uid',$uid)->where('addtime','between',strtotime(date('Y-m-d')) . ',' . time())->where('lv',$lv)->where('status',1)->sum('num');

        if($num) return json(['code'=>0,'info'=>'请求成功','data'=>$num]);
        return json(['code'=>1,'info'=>'暂无数据']);
    }
    
    public function xxxx() {
        echo cookie('user_id');
        echo '<br />';
        echo session('user_id');
        echo '<br />';
    }
}