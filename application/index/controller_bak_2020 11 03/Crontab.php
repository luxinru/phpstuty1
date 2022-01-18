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

/**
 * 定时器
 */
class Crontab extends Controller
{
    //冻结订单
    public function freeze_order()
    {
        $timeout = time()-config('deal_timeout');//超时订单
        $oinfo = Db::name('xy_convey')->where('status',0)->where('addtime','<=',$timeout)->field('id')->select();
        if($oinfo){
            foreach ($oinfo as $v) {
                Db::name('xy_convey')->where('id',$v['id'])->update(['status'=>5,'endtime'=>time()]);
            }
        }
        // 2020 10 27 仅冻结
        //$this->cancel_order();
        //$this->reset_deal();
    }

    //强制取消订单并冻结账户 
    public function cancel_order()
    {
        $timeout = time()-config('deal_timeout');//超时订单
        //$oinfo = Db::name('xy_convey')->field('id oid,uid')->where('status',5)->where('endtime','<=',$timeout)->select();
        $oinfo = Db::name('xy_convey')->field('id oid,uid')->where('status',0)->where('endtime','<=',$timeout)->select();
        if($oinfo){
            foreach ($oinfo as $v) {
                Db::name('xy_convey')->where('id',$v['oid'])->update(['status'=>4,'endtime'=>time()]);
                $tmp =Db::name('xy_users')->field('deal_error,deal_status')->find($v['uid']);
                //记录违规信息
                if($tmp['deal_status']!=0){
                    if($tmp['deal_error'] < (int)config('deal_error')){
                        Db::name('xy_users')->where('id',$v['uid'])->update(['deal_status'=>1,'deal_error'=>Db::raw('deal_error+1')]);
                        Db::name('xy_user_error')->insert(['uid'=>$v['uid'],'oid'=>$v['oid'],'addtime'=>time(),'type'=>2]);
                    }elseif ($tmp['deal_error'] >= (int)config('deal_error')) {
                        Db::name('xy_users')->where('id',$v['uid'])->update(['deal_status'=>1,'deal_error'=>0]);
                        Db::name('xy_user_error')->insert(['uid'=>$v['uid'],'oid'=>$v['oid'],'addtime'=>time(),'type'=>3]);
                        //记录交易冻结信息
                    }
                }
            }
        }
    }

    //解冻账号
    public function reset_deal()
    {
        $uinfo = Db::name('xy_users')->where('deal_status',0)->field('id')->select();
        if($uinfo){
            foreach ($uinfo as $v) {
                $time = Db::name('xy_user_error')->where('uid',$v['id'])->where('type',3)->order('addtime desc')->limit(1)->value('addtime');
                if($time || $time <= time()-config('deal_feedze')){ 
                    //解封账号
                    Db::name('xy_users')->where('id',$v['id'])->update(['deal_status'=>1]);
                    Db::name('xy_user_error')->insert(['uid'=>$v['id'],'oid'=>'-','addtime'=>time(),'type'=>1]);
                }
            }
        }
    }

    //发放佣金
    public function do_reward()
    {
        try {
            $time = strtotime(date('Y-m-d', time()));//获取当天凌晨0点的时间戳
            $data = Db::name('xy_reward_log')->where('addtime','between', time()-3600*24 . ',' . time() )->where('status',1)->select();//获取当天佣金
            if($data){
                foreach ($data as $k => $v) {
                    Db::name('xy_users')->where('id',$v['uid'])->setInc('balance',$v['num']);
                    Db::name('xy_reward_log')->where('id',$v['id'])->update(['status'=>2,'endtime'=>time()]);
                }
            }
            echo 1;
        } catch (\Throwable $th) {
            //throw $th;
        }
    }


    //定时器 解除冻结 反还佣金和本金
    public function start333()
    {
        $oinfo = Db::name('xy_convey')->where('status',5)->where('endtime','<=',time())->select();
        if ($oinfo) {
            //
            foreach ($oinfo as $v) {
                //
                Db::name('xy_convey')->where('id',$v['id'])->update(['status'=>1]);

                //
                $res1 = Db::name('xy_users')
                    ->where('id', $v['uid'])
                    //->dec('balance',$info['num'])//
                    ->inc('balance',$v['num']+$v['commission'])
                    //->inc('freeze_balance',$info['num']+$info['commission']) //冻结商品金额 + 佣金//
                    ->dec('freeze_balance',$v['num']+$v['commission']) //冻结商品金额 + 佣金
                    ->update(['deal_status'=>1]);
                $this->deal_reward($v['uid'],$v['id'],$v['num'],$v['commission']);

                //
            }
        }
        $this->cancel_order();
        $this->reset_deal();
        //$this->lixibao_chu();
        //var_dump($oinfo,time(),date('Y-m-d H:i:s', 1577812622));die;
        return json(['code'=>1,'info'=>'执行成功！']);
    }



    //------------------------------------------------------------------------------

    //强制取消订单并冻结账户
    public function start() {
        $timeout = time()-config('deal_timeout');//超时订单
        $timeout = time();//超时订单
        //$oinfo = Db::name('xy_convey')->field('id oid,uid')->where('status',5)->where('endtime','<=',$timeout)->select();
        $oinfo = Db::name('xy_convey')->where('status',0)->where('endtime','<=',$timeout)->select();
        if($oinfo){
            $djsc = config('deal_feedze'); //冻结时长 单位小时
            foreach ($oinfo as $v) {
                Db::name('xy_convey')->where('id',$v['id'])->update(['status'=>5,'endtime'=>time()+ $djsc * 60 *60]);
                //$res = Db::name('xy_convey')->where('id',$oid)->update($tmp);
                $res1 = Db::name('xy_users')
                    ->where('id', $v['uid'])
                    ->dec('balance',$v['num'])
                    ->inc('freeze_balance',$v['num']+$v['commission']) //冻结商品金额 + 佣金
                    ->update(['deal_status' => 1,'status'=>1]);

                $res2 = Db::name('xy_balance_log')->insert([
                    'uid'           => $v['uid'],
                    'oid'           => $v['id'],
                    'num'           => $v['num'],
                    'type'          => 2,
                    'status'        => 2,
                    'addtime'       => time()
                ]);
            }
        }

        //解冻
        $this->jiedong();
    }

    public function jiedong()
    {
        $oinfo = Db::name('xy_convey')->where('status',5)->where('endtime','<=',time())->select();
        if ($oinfo) {
            //
            foreach ($oinfo as $v) {
                //
                Db::name('xy_convey')->where('id',$v['id'])->update(['status'=>1]);

                //
                $res1 = Db::name('xy_users')
                    ->where('id', $v['uid'])
                    //->dec('balance',$info['num'])//
                    ->inc('balance',$v['num']+$v['commission'])
                    //->inc('freeze_balance',$info['num']+$info['commission']) //冻结商品金额 + 佣金//
                    ->dec('freeze_balance',$v['num']+$v['commission']) //冻结商品金额 + 佣金
                    ->update(['deal_status'=>1]);
                $this->deal_reward($v['uid'],$v['id'],$v['num'],$v['commission']);

                //
            }
        }
    }


    /**
     * 交易返佣
     *
     * @return void
     */
    public function deal_reward($uid,$oid,$num,$cnum)
    {
        ///$res = Db::name('xy_users')->where('id',$uid)->where('status',1)->setInc('balance',$num+$cnum);

//        $res1 = Db::name('xy_balance_log')->insert([
//            //记录返佣信息
//            'uid'       => $uid,
//            'oid'       => $oid,
//            'num'       => $num+$cnum,
//            'type'      => 3,
//            'addtime'   => time()
//        ]);
        Db::name('xy_balance_log')->where('oid',$oid)->update(['status'=>1]);


        //将订单状态改为已返回佣金
        Db::name('xy_convey')->where('id',$oid)->update(['c_status'=>1]);
        Db::name('xy_reward_log')->insert(['oid'=>$oid,'uid'=>$uid,'num'=>$num,'addtime'=>time(),'type'=>2]);//记录充值返佣订单
        /************* 发放交易奖励 *********/
        $userList = model('admin/Users')->parent_user($uid,5);
        //echo '<pre>';
        //var_dump($userList);die;
        if($userList){
            foreach($userList as $v){
                if($v['status']===1){
                    Db::name('xy_reward_log')
                        ->insert([
                            'uid'       => $v['id'],
                            'sid'       => $v['pid'],
                            'oid'       => $oid,
                            'num'       => $num*config($v['lv'].'_d_reward'),
                            'lv'        => $v['lv'],
                            'type'      => 2,
                            'status'    => 1,
                            'addtime'   => time(),
                        ]);

                    //
                    $res1 = Db::name('xy_balance_log')->insert([
                        //记录返佣信息
                        'uid'       => $v['id'],
                        'oid'       => $oid,
                        'sid'       => $uid,
                        'num'       => $cnum*config($v['lv'].'_d_reward'),
                        'type'      => 6,
                        'status'    => 1,
                        'f_lv'        => $v['lv'],
                        'addtime'   => time()
                    ]);

                }

                //
                $num3 = $num*config($v['lv'].'_d_reward'); //佣金
                $res = Db::name('xy_users')->where('id',$v['id'])->where('status',1)->setInc('balance',$num3);
                $res2 = Db::name('xy_balance_log')->insert([
                    'uid'           => $v['id'],
                    'oid'           => $oid,
                    'num'           => $num3,
                    'type'          => 6,
                    'status'        => 1,
                    'addtime'       => time()
                ]);

            }
        }
        /************* 发放交易奖励 *********/

    }


   //----------------------------利息宝---------------------------------
    //1 转入 2转出  3每日收益
    public function lixibao_chu()
    {
        //处理从余额里转出的到账时间
        $addMax = time() - ( (config('lxb_time')) * 60*60 ); //向前退一个小时
        $res = Db::name('xy_lixibao')->where('status',0)->where('addtime','<=',$addMax)->where('type',2)->select();
        if ($res) {
            foreach ($res as $re) {
                $uid = $re['uid'];
                $num = $re['num'];

                Db::name('xy_users')->where('id',$re['id'])->setDec('lixibao_dj_balance',$num);  //利息宝月 -
                Db::name('xy_users')->where('id',$uid)->setInc('balance',$num);  //余额 +
                Db::name('xy_lixibao')->where('id',$re['id'])->update(['status'=>1]);  //利息宝月 -
            }
        }
    }

    public function lxb_jiesuan()
    {
        $now = time();
        $now = strtotime( date('Y-m-d 00:00:00', time()) );; //小于今天的 12点
        $lxb = Db::name('xy_lixibao')->where('endtime','<',$now)
            ->where('is_qu',0)
            ->where('is_sy',0)
            ->where('type',1)->select();  //利息宝月
   

        if ($lxb) {
            foreach ($lxb as $item) {
                //----------------------------------
                $lixibao = Db::name('xy_lixibao_list')->find($item['sid']);
                $price = $item['num'];
                $uid   = $item['uid'];
                $id    = $item['id'];
                $sy = $price * $lixibao['bili'] * $lixibao['day'];
                


                Db::name('xy_users')->where('id',$uid)->setDec('lixibao_balance',$price);  //利息宝余额 -
                Db::name('xy_users')->where('id',$uid)->setInc('balance',$price+$sy);  //余额 +  没有手续费

                $res = Db::name('xy_lixibao')->where('id',$id)->update([
                    'is_qu'      => 1,
                    'is_sy'      => 1,
                    'real_num'     => $sy
                ]);
                $res1 = Db::name('xy_balance_log')->insert([
                    //记录返佣信息
                    'uid'       => $uid,
                    'oid'       => $id,
                    'num'       => $sy,
                    'type'      => 23,
                    'addtime'   => time()
                ]);
                $res2 = Db::name('xy_balance_log')->insert([
                    //记录返佣信息
                    'uid'       => $uid,
                    'oid'       => $id,
                    'num'       => $price,
                    'type'      => 22,
                    'addtime'   => time()
                ]);
				
					
                $res3 = Db::name('xy_lixibao')->insert([
                    'uid'         => $uid,
                    'num'         => $sy,
                    'addtime'     => time(),
                    'type'        => 3,
                    'yuji_num'	  =>0,
                    'is_qu'		  =>1,
                    'is_sy'		=>-1,
                    'status'      => 1,
                ]);
                

                //----------------------------------
            }
        }
        return json(['code'=>1,'info'=>'执行成功！']);
    }



    /**
     * @地址      lixibao_js
     * @说明      每天12点 10分左右计算 前一天的收益  切莫重复
     * @说明      域名为  http://域名/index/crontab/lixibao_js
     * @参数      @参数 @参数
     */
    //结算 //
    public function lixibao_js()
    {
        $uinfo = Db::name('xy_users')->where('lixibao_balance','>',0)->select();  //余额 +

        if ($uinfo) {
            foreach ($uinfo as $item) {
                $uid = $item['id'];
                //今日的购买不计算
                $yes1159 = strtotime( date("Y-m-d 23:59:59",strtotime("-1 day")) ); //昨天11:59
                //有效yue
                $yue = $item['lixibao_balance'];
                //59以后购买的e
                $after1159 = Db::name('xy_lixibao')->where('id',$item['id'])->where('addtime','>',$yes1159)->where('type',3)->sum('num');  //利息宝月 -
                !$after1159? $after1159 =0 :'';
                $yue = $yue - $after1159;

                //收益
                $shouyi = $yue * config('lxb_bili')*1;
                Db::name('xy_users')->where('id',$uid)->setInc('balance',$shouyi);  //余额 +

                $res = Db::name('xy_lixibao')->insert([
                    'uid'         => $uid,
                    'num'         => $shouyi,
                    'addtime'     => time(),
                    'type'        => 3,
                    'status'      => 1,
                ]);
            }
        }
    }



    /**
     * 会员有效期
     * 1分钟一次
     * /index/crontab/member_level_validity
     */
    public function member_level_validity() {
        $date = date("Y-m-d H:i:s");
        $list = Db::name('xy_users')->where("level > 1 and level_validity != 99 and level_validity < '{$date}'")->select();
        if (!empty($list)) {
            $ids = array_column($list, 'id');
            $up = [
                'level' => 1
            ];
            Db::name('xy_users')->where("id", "in", $ids)->update($up);
        }
        exit('end');
    }
}