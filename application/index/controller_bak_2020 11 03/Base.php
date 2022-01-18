<?php
namespace app\index\controller;

use library\Controller;
use think\facade\Request;
use think\Db;

/**
 * 验证登录控制器
 */
class Base extends Controller
{
    protected $rule = ['__token__' => 'token'];
    protected $msg  = ['__token__'  => '无效token！'];

    function __construct() {
        parent::__construct();
        $uid = session('user_id');
        if (!$uid) {
            $uid = cookie('user_id');
        }

        if(!$uid && request()->isPost()){
            $this->error('请先登录');
        }
        if(!$uid) $this->redirect('User/login');
        /***实时监测账号状态***/
        // $uinfo = db('xy_users')->find($uid);
        // if($uinfo['status']!=1){
        //     \Session::delete('user_id');
        //     $this->redirect('User/login');
        // }
        $this->console = db('xy_script')->where('id',1)->value('script');
        $userinfo = db('xy_users')->find($uid);
        $userinfo['level'];
        session('level', $userinfo['level']);
    }

    /**
     * 空操作 用于显示错误页面
     */
    public function _empty($name){
        return $this->fetch($name);
    }

    //图片上传为base64为的图片
    public function upload_base64($type,$img){
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $img, $result)){
            $type_img = $result[2];  //得到图片的后缀
            //上传 的文件目录

            $App = new \think\App();
            $new_files = $App->getRootPath() . 'upload'. DIRECTORY_SEPARATOR . $type. DIRECTORY_SEPARATOR . date('Y') . DIRECTORY_SEPARATOR . date('m-d') . DIRECTORY_SEPARATOR ;

            if(!file_exists($new_files)) {
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                //服务器给文件夹权限
                mkdir($new_files, 0777,true);
            }
            //$new_files = $new_files.date("YmdHis"). '-' . rand(0,99999999999) . ".{$type_img}";
            $new_files = check_pic($new_files,".{$type_img}");
            if (file_put_contents($new_files, base64_decode(str_replace($result[1], '', $img)))){
                //上传成功后  得到信息
                $filenames=str_replace('\\', '/', $new_files);
                $file_name=substr($filenames,strripos($filenames,"/upload"));
                return $file_name;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * 检查交易状态
     */
    public function check_deal()
    {
        $uid = session('user_id');
        $uinfo = db('xy_users')->field('deal_status,status,balance,level,deal_count,deal_time,deal_reward_count dc')->find($uid);
        if($uinfo['status']==2) return ['code'=>1,'info'=>'该账户已被禁用'];
        if($uinfo['deal_status']==0) return ['code'=>1,'info'=>'该账户交易功能已被冻结'];
        if($uinfo['deal_status']==3) return ['code'=>1,'info'=>'该账户存在未完成订单，无法继续匹配订单！'];
        //if($uinfo['balance']<config('deal_min_balance')) return ['code'=>1,'info'=>'您的账户余额低于'.config('deal_min_balance').'，无法进行订单匹配'];
        //$count = db('xy_convey')->where('addtime','between',[strtotime(date('Y-m-d')),time()])->where('uid',session('user_id'))->where('status',2)->count('id');//统计当天完成交易的订单
        // if($count>=config('deal_count')) return ['code'=>1,'info'=>'今日交易次数已达上限!'];
        //检查是否有冻结订单
        $convey = db('xy_convey')->where("uid", $uid)->where("status", 5)->count();
        if($convey> 0) return ['code'=>1,'info'=>'该账户存在冻结订单，无法继续抢单！'];
        if($uinfo['deal_time']==strtotime(date('Y-m-d'))){
            //交易次数限制
            $level = $uinfo['level'];
            !$uinfo['level'] ? $level = 0 : '';
            $ulevel = Db::name('xy_level')->where('level',$level)->find();
            if ($uinfo['deal_count'] >= $ulevel['order_num']) {
                return ['code'=>1,'info'=>'您的会员等级今日匹配订单次数已达上限!'];
            }

            //if($uinfo['deal_count'] >= config('deal_count')+$uinfo['dc']) return ['code'=>1,'info'=>'今日交易次数已达上限!'];
        }else{
            //重置最后交易时间
            db('xy_users')->where('id',$uid)->update(['deal_time'=>strtotime(date('Y-m-d')),'deal_count'=>0,'recharge_num'=>0,'deposit_num'=>0]);
        }

        return false;
    }

    /**
     * 返回失败的跳转操作
     * @param mixed $info 消息内容
     * @param mixed $url 跳转的URL
     */
    public function error_h($info = '操作失败', $url = "")
    {
        $this->info = $info;
        $this->url = $url;
        $this->fetch('public/error');
    }

}
