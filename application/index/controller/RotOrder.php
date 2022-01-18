<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Db;

/**
 * 下单控制器
 */
class RotOrder extends Base
{
    /**
     * 首页
     */
    public function index()
    {
        $uinfo = db('xy_users')->where('id', cookie('user_id'))->find();
        $type = $uinfo['level'] + 1;
        $level_info = db('xy_level')->where('level', $type)->find();
        /*if ($uinfo['balance'] < $level_info['deal_min_balance']) {
            $this->error_h('金额不足，最低需要余额' . $level_info['deal_min_balance'] . '元方可进入此专区', "/index/index/home.html");
        }*/
        $this->num_min = $level_info['num_min'];
        $where = [
            ['uid','=',cookie('user_id')],
            ['addtime','between',strtotime(date('Y-m-d')).','.time()],
        ];
        $this->day_deal = Db::name('xy_convey')->where($where)->where('status','in',[1,3,5])->sum('commission');
//        $this->day_l_count = Db::name('xy_convey')->where($where)->where('status',5)->count('num');//交易冻结单数

        $yes1 = strtotime( date("Y-m-d 00:00:00",strtotime("-1 day")) );
        $yes2 = strtotime( date("Y-m-d 23:59:59",strtotime("-1 day")) );
        $this->price = Db::name('xy_users')->where('id',cookie('user_id'))->sum('balance');

        $this->day_d_count = Db::name('xy_convey')->where($where)->where('status','in',[0,1,3,5])->count('id');
        $this->lock_deal = Db::name('xy_users')->where('id',cookie('user_id'))->sum('freeze_balance');
        $this->yes_team_num = Db::name('xy_reward_log')->where('uid',cookie('user_id'))->where('addtime','between',[$yes1,$yes2])->where('status',1)->sum('num');//获取下级返佣数额
        $this->today_team_num = Db::name('xy_reward_log')->where('uid',cookie('user_id'))->where('addtime','between',[strtotime('Y-m-d'),time()])->where('status',1)->sum('num');//获取下级返佣数额

        //分类
        $this->cate = Db::name('xy_goods_cate')->alias('c')
            ->leftJoin('xy_level u','u.id=c.level_id')
            ->field('c.name,c.cate_info,c.cate_pic,u.name as levelname,u.pic,u.level,u.bili,u.order_num')
            ->find($type);
        $this->beizhu = db('xy_index_msg')->where('id',9)->value('content');;
        

        $this->yes_user_yongjin = db('xy_convey')->where('uid',cookie('user_id'))->where('status',1)->where('addtime','between',[$yes1,$yes2])->sum('commission');
        $this->user_yongjin = db('xy_convey')->where('uid',cookie('user_id'))->where('status',1)->sum('commission');

$this->dengji = input("get.type");
        $member_level = db('xy_level')->order('level asc')->select();;
        $order_num = $member_level[0]['order_num'];
        if (!empty($uinfo['level'])){
            $order_num = db('xy_level')->where('level',$uinfo['level'])->value('order_num');;
        }
        $this->order_num = $order_num;

        $goods_pic_list = db('xy_goods_list')->field('goods_pic')->limit(100)->select();
        $this->goods_pic_list = array_column($goods_pic_list, 'goods_pic');
        
        $ll = [];
        $date_time_list = [];
        $h = date('H');
        // 会员动态 白天10点到22点
        $_this_h = date('H');
        if ($_this_h >= 10 && $_this_h < 22) {
            for ($i = 0; $i < 30; $i ++) {
                $_this_i = date('i');
                $iii = mt_rand(0, $_this_i);
                $_this_s = $iii == $_this_i ? date('s') : 60;
                $date_time_list[] = strtotime(date("Y-m-d") . ' ' . $_this_h . ':' . $iii . ':' . mt_rand(0, $_this_s));
            }
        } else if ($_this_h >= 22) {
            $_this_h = 21;
            for ($i = 0; $i < 30; $i ++) {
                $date_time_list[] = strtotime(date("Y-m-d") . ' ' . $_this_h . ':' . mt_rand(0, 59) . ':' . mt_rand(0, 59));
            }
        } else {
            $_this_h = 21;
            $_this_ymd = date('Y-m-d', strtotime(date("Y-m-d")) - 24 * 3600);
            for ($i = 0; $i < 30; $i ++) {
                $date_time_list[] = strtotime($_this_ymd . ' ' . $_this_h . ':' . mt_rand(0, 59) . ':' . mt_rand(0, 59));
            }
        }
        rsort($date_time_list);

        for ($i = 0; $i < 30; $i ++) {
            $mobile = 1 . mt_rand(3, 8) . mt_rand(1, 9) . '****' . mt_rand(1, 9) . mt_rand(1, 9) . mt_rand(1, 9) . mt_rand(1, 9);
            $type = mt_rand(0, 100);
            $ll[] = [
                'date_time' => date('m-d H:i:s', $date_time_list[$i]),
                'mobile' => $mobile,
                'type' => $type,
            ];
        }
        $this->ll = $ll;

        $member_address = db('xy_member_address')->where('uid', cookie('user_id'))->select();
        $this->has_member_address = !empty($member_address) ? 1 : 0;
        
        return $this->fetch();
    }
  /**
    *提交抢单
    */
    public function submit_order()
    {
        $tmp = $this->check_deal();
        if($tmp) return json($tmp);
        $res = check_time(9,22);
        //if($res) return json(['code'=>1,'info'=>'禁止在9:00~22:00以外的时间段执行当前操作!']);

        $res = check_time(config('order_time_1'),config('order_time_2'));
        $str = config('order_time_1').":00  - ".config('order_time_2').":00";
        if($res) return json(['code'=>1,'info'=>lang('禁止在').$str.lang('以外的时间段执行当前操作!')]);

        $uid = cookie('user_id');
        $add_id = db('xy_member_address')->where('uid',$uid)->value('id');//获取收款地址信息
       // if(!$add_id) return json(['code'=>1,'info'=>lang('还没有设置收货地址')]);

        $uinfo = db('xy_users')->where('id', cookie('user_id'))->find();
        $level = $uinfo['level'];
        $level_info = db('xy_level')->where('level', $level)->find();
        if ($uinfo['balance'] < $level_info['num_min']) {
            return json(['code'=>1,'info'=>lang('您的会员专区所对应的匹配订单任务要求账户可用余额最低为') . $level_info['num_min'] . lang('元，该账户余额未满足会员专区任务订单匹配要求，无法继续进行匹配订单～请充值后再试！')]);
        }
        // dump($uinfo);
        // dump($level_info);
        // exit;
        //检查交易状态
        // $sleep = mt_rand(config('min_time'),config('max_time'));
        $res = db('xy_users')->where('id',$uid)->update(['deal_status'=>2]);//将账户状态改为等待交易
        if($res === false) return json(['code'=>1,'info'=>lang('抢单失败，请稍后再试！')]);
        // session_write_close();//解决sleep造成的进程阻塞问题
        // sleep($sleep);
        //
        $cid = input('get.cid/d',1);
        $count = db('xy_goods_list')->where('cid','=',$cid)->count();
        if($count < 1) return json(['code'=>1,'info'=>lang('抢单失败，商品库存不足！')]);
        $time=strtotime(date("Y-m-d",time()));
        $where=[
            ['uid','=',$uid],
            // 'b.cid'=>$cid
            ['addtime','>',$time]
        ];
        $number=Db::name('xy_convey')->where($where)->count();
        if($number>=$level_info['order_num']){
            return json(['code'=>1,'info'=>"The number of orders for this level today has been capped"]);
        }
        $res = model('admin/Convey')->create_order($uid,$cid);
        return json($res);
    }

    /**
     * 停止抢单
     */
    public function stop_submit_order()
    {
        $uid = cookie('user_id');
        $res = db('xy_users')->where('id',$uid)->where('deal_status',2)->update(['deal_status'=>1]);
        if($res){
            return json(['code'=>0,'info'=>lang('操作成功')]);
        }else{
            return json(['code'=>1,'info'=>lang('操作失败')]);
        }
    }

    public function xxxx() {
        $goods_pic_list = db('xy_goods_list')->select();
        foreach ($goods_pic_list as $v) {
            echo $v['id'] . ' | ';
            echo '<img style="width: 20px; height: 20px" src="' . $v['goods_pic'] . '?id=' . $v['id'] . '">';
            echo '<br />';
        }
    }

}