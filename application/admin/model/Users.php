<?php

namespace app\admin\model;

use think\Model;
use think\Db;

class Users extends Model
{
    protected $table = 'xy_users';
    protected $rule = [
                    'tel'           => 'require',
                    'username'      => 'require|length:2,100',
                    'pwd'           => 'require|length:6,16',
                    '__token__'     => 'token',
                    ];
    protected $info = [
                    'tel.require'       => '手机号不能为空！',
                    //'tel.mobile'        => '手机号格式错误！',
                    'username.length'   => '用户名长度为2-100字符！',
                    'username.require'  => '用户名不能为空！',
                    'pwd.require'       => '密码不能为空！',
                    'pwd.length'        => '密码长度为6-16位字符！',
                    '__token__'         => '令牌已过期，请刷新页面再试！',
                    ];
    /**
     * 添加会员
     *
     * @param string $tel
     * @param string $user_name
     * @param string $pwd
     * @param int    $parent_id
     * @param string $token
     * @return array
     */
    public function add_users($tel,$user_name,$pwd,$parent_id,$token='',$pwd2='',$is_jia='',$balance='')
    {
        $tmp = Db::table($this->table)->where(['tel'=>$tel])->count();
        if($tmp){
            return ['code'=>1,'info'=>lang('手机号码已注册')];
        }
        $tmp = Db::table($this->table)->where(['username'=>$user_name])->count();
        if($tmp){
            return ['code'=>1,'info'=>lang('用户名重复')];
        }
        if(!$user_name) $user_name=get_username();
        $data = [
            'tel'           => $tel,
            'username'      => $user_name,
            'pwd'           => $pwd,
            'parent_id'     => $parent_id,
            'pipei_min'=>10,
            'pipei_max'=>70,
        ];
        if($token) $data['__token__'] = $token;
if($is_jia==1){
    $data['is_jia'] = 1;
}
if(!empty($balance)){
    $data['balance'] = $balance;
}
        //验证表单
        $validate   = \Validate::make($this->rule,$this->info);
        if (!$validate->check($data)) {
            return ['code'=>1,'info'=>lang($validate->getError())];
        }
        if($parent_id){
            $parent_id = Db::table($this->table)->where('id',$parent_id)->value('id');
            if(!$parent_id){
                return ['code'=>1,'info'=>lang('上级ID不存在')];
            }  
        }
        
        $salt = rand(0,99999);  //生成盐
        $invite_code = self::create_invite_code();//生成邀请码

        $data['pwd'] = sha1($pwd.$salt.config('pwd_str'));
        $data['salt'] = $salt;
        $data['addtime'] = time();
        $data['invite_code'] = $invite_code;
        if($pwd2){
            $salt2 = rand(0,99999);  //生成盐
            $data['pwd2'] = sha1($pwd2.$salt2.config('pwd_str'));
            $data['salt2'] = $salt2;
        }
        //开启事务
        unset($data['__token__']);
        Db::startTrans();
        $res = Db::table($this->table)->insertGetId($data);
        if($parent_id){
            $res2 = Db::table($this->table)->where('id',$data['parent_id'])->update(['childs'=>Db::raw('childs+1'),'deal_reward_count'=>Db::raw('deal_reward_count+'.config('deal_reward_count'))]);
        }else{
            $res2 = true;
        }
        //生成二维码
        self::create_qrcode($invite_code,$res);

        if($res && $res2){
            // 提交事务
            Db::commit();
            return ['code'=>0,'info'=>lang('操作成功')];
        }else
            // 回滚事务
            Db::rollback();
            return ['code'=>1,'info'=>lang('操作失败')];
    }
    
//     public function dds($tel,$user_name,$pwd,$parent_id,$token='',$pwd2='',$is_jia='',$balance='')
//     {
     
//         $tmp = Db::table($this->table)->where(['tel'=>$tel])->count();
//         if($tmp){
//             return ['code'=>1,'info'=>lang('手机号码已注册')];
//         }
//         $tmp = Db::table($this->table)->where(['username'=>$user_name])->count();
//         if($tmp){
//             return ['code'=>1,'info'=>lang('用户名重复')];
//         }
//         if(!$user_name) $user_name=get_username();
//         $data = [
//             'tel'           => $tel,
//             'username'      => $user_name,
//             'pwd'           => $pwd,
//             'parent_id'     => $parent_id,
//         ];
//         if($token) $data['__token__'] = $token;
// if($is_jia==1){
//     $data['is_jia'] = 1;
// }
// if(!empty($balance)){
//     $data['balance'] = $balance;
// }
//         // //验证表单
//         // $validate   = \Validate::make($this->rule,$this->info);
//         // if (!$validate->check($data)) {
//         //     return ['code'=>1,'info'=>lang($validate->getError())];
//         // }
//         if($parent_id){
//             $parent_id = Db::table($this->table)->where('id',$parent_id)->value('id');
//             if(!$parent_id){
//                 return ['code'=>1,'info'=>lang('上级ID不存在')];
//             }  
//         }
        
//         $salt = rand(0,99999);  //生成盐
//         $invite_code = self::create_invite_code();//生成邀请码

//         $data['pwd'] = sha1($pwd.$salt.config('pwd_str'));
//         $data['salt'] = $salt;
//         $data['addtime'] = time();
//         $data['invite_code'] = $invite_code;
//         if($pwd2){
//             $salt2 = rand(0,99999);  //生成盐
//             $data['pwd2'] = sha1($pwd2.$salt2.config('pwd_str'));
//             $data['salt2'] = $salt2;
//         }
//         //开启事务
//         unset($data['__token__']);
  
//         $res = Db::table($this->table)->insertGetId($data);
//         if($parent_id){
//             $res2 = Db::table($this->table)->where('id',$data['parent_id'])->update(['childs'=>Db::raw('childs+1'),'deal_reward_count'=>Db::raw('deal_reward_count+'.config('deal_reward_count'))]);
//         }else{
//             $res2 = true;
//         }
//         //生成二维码
//         self::create_qrcode($invite_code,$res);
        

//     }

    /**
     * 编辑用户
     *
     * @param int       $id
     * @param string    $tel
     * @param string    $user_name
     * @param string    $pwd
     * @param int       $parent_id
     * @param string    $token
     * @return array
     */
    public function edit_users($id,$tel,$user_name,$pwd,$parent_id,$balance,$pipei_min, $pipei_max, $freeze_balance,$token,$pwd2=''){
        $tmp = Db::table($this->table)->where(['tel'=>$tel])->where('id','<>',$id)->count();
        if($tmp){
            return ['code'=>1,'info'=>lang('手机号码已注册')];
        }
        $data = [
            'tel'               => $tel,
            'balance'           => $balance,
            'pipei_min'			=> $pipei_min,
            'pipei_max'			=> $pipei_max,
            'freeze_balance'    => $freeze_balance,
            'username'          => $user_name,
            'parent_id'         => $parent_id,
            '__token__'         => $token,
        ];
        if($pwd){
            //不提交密码则不改密码
            $data['pwd'] = $pwd;
        }else{
            $this->rule['pwd'] = '';
        }
        if($parent_id){
            $parent_id = Db::table($this->table)->where('id',$parent_id)->value('id');
            if(!$parent_id){
                return ['code'=>1,'info'=>lang('上级ID不存在')];
            }  
            $data['parent_id'] = $parent_id;
        }

        $validate   = \Validate::make($this->rule,$this->info);//验证表单
        if (!$validate->check($data)) return ['code'=>1,'info'=>lang($validate->getError())];

        if($pwd){
            $salt = rand(0,99999); //生成盐
            $data['pwd']    = sha1($pwd.$salt.config('pwd_str'));
            $data['salt']   = $salt;
        }
        if($pwd2){
            $salt2 = rand(0,99999); //生成盐
            $data['pwd2']    = sha1($pwd2.$salt2.config('pwd_str'));
            $data['salt2']   = $salt2;
        }


        unset($data['__token__']);
        $res = Db::table($this->table)->where('id',$id)->update($data);
        if($res)
            return ['code'=>0,'info'=>lang('编辑成功')];
        else
            return ['code'=>1,'info'=>lang('操作失败')];
    }

    public function edit_users_status($id,$status)
    {
        $status = intval($status);
        $id = intval($id);

        if(!in_array($status,[1,2])) return ['code'=>1,'info'=>lang('参数错误')];

        if($status == 2){
            //查看有无未完成的订单
            // if($num > 0)$this->error('该用户尚有未完成的支付订单！');
        }

        $res = Db::table($this->table)->where('id',$id)->update(['status'=>$status]);
        if($res !== false)
            return ['code'=>0,'info'=>lang('操作成功')];
        else
            return ['code'=>1,'info'=>lang('操作失败')];
    }

    //生成邀请码
    public static function create_invite_code(){
        $str = '123456789qwertyuiopasdfghjklzxcvbnm';
        $rand_str = substr(str_shuffle($str),0,5);
        $num = Db::table('xy_users')->where('invite_code',$rand_str)->count();
        if($num)
            // return $this->create_invite_code();
            return self::create_invite_code();
        else
            return $rand_str;
    }

    //生成用户二维码
    public static function create_qrcode($invite_code,$user_id){ 
        $n = ($user_id%20);    
        
        $dir = './upload/qrcode/user/'.$n . '/' . $user_id . '.png';
        if(file_exists($dir)) {
            return;
        }

        $qrCode = new \Endroid\QrCode\QrCode(SITE_URL . url('@index/user/register/invite_code/'.$invite_code));
        //设置前景色
        $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' =>0, 'a' => 0]);
        //设置背景色
        $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
        //设置二维码大小
        $qrCode->setSize(230);
        $qrCode->setPadding(5);
        $qrCode->setLogoSize(40);
        $qrCode->setLabelFontSize(14);
        $qrCode->setLabelHalign(100);

        $dir = './upload/qrcode/user/'.$n;
        if(!file_exists($dir)) {
            mkdir($dir, 0777,true);
        }
        $qrCode->save($dir . '/' . $user_id . '.png');

        $qr = \Env::get('root_path').'public/upload/qrcode/user/' . $n . '/' . $user_id . '.png';  
        $bgimg1 = \Env::get('root_path').'public/public/img/userqr1.png';

        $image = \think\Image::open($bgimg1);  
        $image->water($qr,[255,743])->text($invite_code,\Env::get('root_path').'public/public/fz.TTF',22,'#ffffff',[(678-(24*strlen($user_id)))/2,685])->save(\Env::get('root_path').'public/upload/qrcode/user/'.$n.'/'.$user_id.'-1.png');
    }

    /**
     * 重置密码
     */
    public function reset_pwd($tel,$pwd,$type=1)
    {
        $data = [
            'tel'   => $tel,
            'pwd'   => $pwd,
        ];
        unset($this->rule['username']);
        $validate   = \Validate::make($this->rule,$this->info);//验证表单
        if (!$validate->check($data)) return ['code'=>1,'info'=>lang($validate->getError())];

        $user_id = Db::table($this->table)->where(['tel'=>$tel])->value('id');
        if(!$user_id){
            return ['code'=>1,'info'=>lang('用户不存在')];
        }
        
        $salt = mt_rand(0,99999);  
        if($type == 1){
            $data = [
                'pwd'       => sha1($pwd.$salt.config('pwd_str')),
                'salt'      => $salt,
            ];
        }elseif($type == 2){
            $data = [
                'pwd2'       => sha1($pwd.$salt.config('pwd_str')),
                'salt2'      => $salt,
            ];
        }
// ->data($data)
        $res = Db::table($this->table)->where('id',$user_id)->update($data);

        if($res)
            return ['code'=>0,'info'=>lang('修改密码成功')];
        else
            return ['code'=>1,'info'=>lang('修改密码失败')];

    }

    //获取上级会员
    public function parent_user($uid,$num=1,$lv=1)
    {
        $pid = db($this->table)->where('id',$uid)->value('parent_id');
        $uinfo = db($this->table)->where('id',$pid)->find();
        if($uinfo){
            if($uinfo['parent_id']&&$num>1) $data = self::parent_user($uinfo['id'],$num-1,$lv+1);
            $data[] = ['id'=>$uinfo['id'],'pid'=>$uinfo['parent_id'],'lv'=>$lv,'status'=>$uinfo['status']];
            return $data;
        }
        return false;
    }


    //获取下级会员
    public function child_user($uid,$num=1,$lv=1)
    {

        $data=[];
        $where = [];
        if ($num ==1) {
            $where[] = ['parent_id','=',$uid];
            $data = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');

        }else if ($num == 2) {
            //二代
            $ids1 = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');
            $ids1 ? $where[] = ['parent_id','in',$ids1] : $where[] = ['parent_id','in',[-1]];
            $data = db('xy_users')->where($where)->field('id')->column('id');
            $data = $lv ? array_merge($ids1 ,$data) : $data;
        }else if ($num == 3) {
            //三代

            $ids1 = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');
            $ids1 ? $wher[] =  ['parent_id','in',$ids1] : $wher[] =  ['parent_id','in',[-1]];
            $ids2 = db('xy_users')->where($wher)->field('id')->column('id');
            $ids2 ? $where[] = ['parent_id','in',$ids2] : $where[] = ['parent_id','in',[-1]];
            $data = db('xy_users')->where($where)->field('id')->column('id');
            $data = $lv ? array_merge($ids1 ,$ids2, $data) : $data;
        }else if ($num == 4) {
            //四带
            $ids1 = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');
            $ids1 ? $wher[] =  ['parent_id','in',$ids1] : $wher[] =['parent_id','in',[-1]];
            $ids2 = db('xy_users')->where($wher)->field('id')->column('id');
            $ids2 ? $where2[] = ['parent_id','in',$ids2] : $where2[] = ['parent_id','in',[-1]];
            $ids3 = db('xy_users')->where($where2)->field('id')->column('id');
            $ids3 ? $where[] = ['parent_id','in',$ids3] : $where[] = ['parent_id','in',[-1]];
            $data = db('xy_users')->where($where)->field('id')->column('id');
            $data = $lv ? array_merge($ids1 ,$ids2,$ids3, $data): $data;

        }else if ($num == 5) {
            //四带
            $ids1 = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');
            $ids1 ? $wher[] =  ['parent_id','in',$ids1] : $wher[] =  ['parent_id','in',[-1]];
            $ids2 = db('xy_users')->where($wher)->field('id')->column('id');
            $ids2 ? $where2[] = ['parent_id','in',$ids2] : $where2[] = ['parent_id','in',[-1]];
            $ids3 = db('xy_users')->where($where2)->field('id')->column('id');
            $ids3 ? $where3[] = ['parent_id','in',$ids3] : $where3[] = ['parent_id','in',[-1]];
            $ids4 = db('xy_users')->where($where3)->field('id')->column('id');
            $ids4 ? $where[] = ['parent_id','in',$ids4] : $where[] = ['parent_id','in',[-1]];
            $data = db('xy_users')->where($where)->field('id')->column('id');

            $data = $lv ? array_merge($ids1 ,$ids2,$ids3,$ids4, $data) :$data;
        }

        return $data;
    }





}
