<?php

namespace app\admin\model;

use think\Model;
use think\Db;

class Convey extends Model
{

    protected $table = 'xy_convey';

    /**
     * 创建订单
     *
     * @param int $uid
     * @return array
     */
    public function create_order($uid,$cid=1)
    {
        $add_id = Db::name('xy_member_address')->where('uid',$uid)->value('id');//获取收款地址信息s
        if(!$add_id)$add_id=0;
        // if(!$add_id) return ['code'=>1,'info'=>lang('还没有设置收货地址')];
        $uinfo = Db::name('xy_users')->field('deal_status,balance,level,pipei_min, pipei_max')->find($uid);
        if($uinfo['deal_status']!=2) return ['code'=>1,'info'=>lang('抢单已终止')];
        
        if( $uinfo['pipei_min'] ==0 && $uinfo['pipei_max']==0  ){
        	$cate = Db::name('xy_goods_cate')->find($cid);
        	$min = $uinfo['balance']*$cate['pipei_min']/100;
        	$max = $uinfo['balance']*$cate['pipei_max']/100; 
        } else {
        	$min = $uinfo['balance']*$uinfo['pipei_min']/100;
        	$max = $uinfo['balance']*$uinfo['pipei_max']/100;
   	
        }
        // return ['code'=>1,'info'=>lang('抢单已终止')];
        $goods = $this->rand_order($min,$max,$cid);


        $level = $uinfo['level'];
        !$uinfo['level'] ? $level = 0 : '';
        $ulevel = Db::name('xy_level')->where('level',$level)->find();
        if ($uinfo['balance'] < $ulevel['num_min']) {
            return ['code'=>1,'info'=>lang('会员等级余额不足!')];
        }
        $id = getSn('UB');
        Db::startTrans();
        $res = Db::name('xy_users')->where('id',$uid)->update(['deal_status'=>3,'deal_time'=>strtotime(date('Y-m-d')),'deal_count'=>Db::raw('deal_count+1')]);//将账户状态改为交易中
        //通过商品id查找 佣金比例
        $cate = Db::name('xy_goods_cate')->find($goods['cid']);;
        //if($goods['num'] > $uinfo['balance']) return ['code'=>1,'info'=>'可用余额不足!'];
        //var_dump($cate,123,$goods);die;

        $res1 = Db::name($this->table)
                ->insert([
                    'id'            => $id,
                    'uid'           => $uid,
                    'num'           => $goods['num'],
                    'addtime'       => time(),
                    'endtime'       => time()+config('deal_timeout'),
                    'add_id'        => $add_id,
                    'goods_id'      => $goods['id'],
                    'goods_count'   => $goods['count'],
                    //'commission'    => $goods['num']*config('vip_1_commission'),
                    //'commission'    => $goods['num']*$cate['bili'],  //交易佣金按照分类
                    'commission'    => $goods['num']*$ulevel['bili'],  //交易佣金按照会员等级
                ]);
        if($res && $res1){
            Db::commit();
            
            //成功 添加 未完成任务数量 
            Db::name('xy_users')->where('id',$uid)->setInc('is_order_num',1);
            return ['code'=>0,'info'=>lang('抢单成功!'),'oid'=>$id];
        }else{
            Db::rollback();
            return ['code'=>1,'info'=>lang('抢单失败!请稍后再试')];
        }
    }

    /**
     * 随机生成订单
     */
    private function rand_order($min,$max,$cid)
    {
        $num = mt_rand($min,$max);//随机交易额
        $goods = Db::name('xy_goods_list')
            ->where('cid','=',$cid)
                ->orderRaw('rand()')
                ->where('goods_price','between',[$num/50,$num])
                
                ->find();
        if (!$goods) {
            echo json_encode(['code'=>1,'info'=>lang('抢单失败, 该分类库存不足!').$min.':'.$max]);die;
        }

        $count = round($num/$goods['goods_price']);
        if($count*$goods['goods_price']<$min||$count*$goods['goods_price']>$max){
            self::rand_order($min,$max,$cid);
        }
        return ['count'=>$count,'id'=>$goods['id'],'num'=>$count*$goods['goods_price'],'cid'=>$goods['cid']];
    }

    /**
     * 处理订单
     *
     * @param string $oid      订单号
     * @param int    $status   操作      1会员确认付款 2会员取消订单 3后台强制付款 4后台强制取消
     * @param int    $uid      用户ID    传参则进行用户判断
     * @param int    $uid      收货地址
     * @return array
     */
    public function do_order($oid,$status,$uid='',$add_id='')
    {
        $info = Db::name('xy_convey')->find($oid);
        if(!$info) return ['code'=>1,'info'=>lang('订单号不存在')];
        if($uid && $info['uid']!=$uid) return ['code'=>1,'info'=>lang('参数错误，请确认订单号!')];
        if(!in_array($info['status'],[0,5])) return ['code'=>1,'info'=>lang('该订单已处理！请刷新页面')];

        //TODO 判断余额是否足够
        $userPrice = Db::name('xy_users')->where('id',$info['uid'])->value('balance');
        if ($userPrice < $info['num'] *lang('duna')) return ['code'=>1,'info'=>lang('账户可用余额不足!')];

        //$tmp = ['endtime'=>time(),'status'=>$status];
        $tmp = ['endtime'=>time()+config('deal_feedze'),'status'=>5];
        $add_id?$tmp['add_id']=$add_id:'';
        Db::startTrans();
        $res = Db::name('xy_convey')->where('id',$oid)->update($tmp);
        if(in_array($status,[1,3])){
            //确认付款
            try {$res1 = Db::name('xy_users')
                        ->where('id', $info['uid'])
                        ->dec('balance',$info['num']*lang('duna'))
                        ->inc('freeze_balance',($info['num']+$info['commission'])*lang('duna')) //冻结商品金额 + 佣金
                        ->update(['deal_status' => 1,'status'=>1]);
            } catch (\Throwable $th) {
                Db::rollback();
                return ['code'=>1,'info'=>lang('请检查账户余额!')];
            }
            
            if(lang('duna') == 0.14){
                $resa = 3; //3是美元至
            }else if(lang('duna') == 11){
                 $resa = 2; //2是卢比至
            }else{
                $resa = 1; //1是人民币至
            }
            $res2 = Db::name('xy_balance_log')->insert([
                'uid'           => $info['uid'],
                'oid'           => $oid,
                'num'           => $info['num'] *lang('duna'),
                'type'          => 2,
                'status'        => 2,
                'addtime'       => time(),
                'dun'           => $resa
            ]);
            if($status==3) Db::name('xy_message')->insert(['uid'=>$info['uid'],'type'=>2,'title'=>lang('系统通知'),'content'=>lang('交易订单').$oid.lang('已被系统强制付款，如有疑问请联系客服'),'addtime'=>time()]);
            //系统通知
            if($res && $res1 && $res2){
                Db::commit();
                $c_status = Db::name('xy_convey')->where('id',$oid)->value('c_status');
                //判断是否已返还佣金
                if($c_status===0) $this->deal_reward($info['uid'],$oid,$info['num']*lang('duna'),$info['commission']);
                
                 Db::name('xy_users')->where('id', $info['uid'])->setDec('is_order_num',1);
                        
                return ['code'=>0,'info'=>lang('操作成功!')];
            }else {
                Db::rollback();
                return ['code'=>1,'info'=>lang('操作失败')];
            }
        }elseif (in_array($status,[2,4])) {
            $res1 = Db::name('xy_users')->where('id',$info['uid'])->update(['deal_status'=>1]);
            if($status==4) Db::name('xy_message')->insert(['uid'=>$info['uid'],'type'=>2,'title'=>lang('系统通知'),'content'=>lang('交易订单').$oid.lang('已被系统强制取消，如有疑问请联系客服'),'addtime'=>time()]);
            //系统通知
            if($res && $res1!==false){
                Db::commit();
                return ['code'=>0,'info'=>lang('操作成功!')];
            }else {
                Db::rollback();
                return ['code'=>1,'info'=>lang('操作失败'),'data'=>$res1];
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
        //$res = Db::name('xy_users')->where('id',$uid)->where('status',1)->setInc('balance',$num+$cnum);
        $res = Db::name('xy_users')->where('id',$uid)->where('status',1)->setInc('balance',$num+$cnum);
        $res2 = Db::name('xy_users')->where('id',$uid)->where('status',1)->setDec('freeze_balance',$num+$cnum);

        // 获取用户最新余额并更新到 xy_convey 的 user_current_balance 2020 10 28
        $u_balance = Db::name('xy_users')->where('id', $uid)->value('balance');
        $res = Db::name('xy_convey')->where('id',$oid)->update(['user_current_balance' => $u_balance]);

        if($res){
                $res1 = Db::name('xy_balance_log')->insert([
                    //记录返佣信息
                    'uid'       => $uid,
                    'oid'       => $oid,
                    //'num'       => $num+$cnum,
                    'num'       => $cnum,
                    'type'      => 3,
                    'addtime'   => time()
                ]);
                //将订单状态改为已返回佣金
                Db::name('xy_convey')->where('id',$oid)->update(['c_status'=>1,'status'=>1]);
                Db::name('xy_reward_log')->insert(['oid'=>$oid,'uid'=>$uid,'num'=>$num,'addtime'=>time(),'type'=>2]);//记录充值返佣订单
                 /************* 发放交易奖励 *********/
                    $userList = model('admin/Users')->parent_user($uid,5);
                    if($userList){
                        foreach($userList as $v){
                            if($v['status']===1){
                                Db::name('xy_reward_log')
                                ->insert([
                                    'uid'       => $v['id'],
                                    'sid'       => $uid,
                                    'oid'       => $oid,
                                    'num'       => $cnum*config($v['lv'].'_d_reward'),
                                    'lv'        => $v['lv'],
                                    'type'      => 2,
                                    'status'    => 1,
                                    'addtime'   => time(),
                                ]);
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

                                $num3 = $cnum*config($v['lv'].'_d_reward'); //佣金
                                $res = Db::name('xy_users')->where('id',$v['id'])->where('status',1)->setInc('balance',$num3);
                            }
                        }
                    }
                 /************* 发放交易奖励 *********/
        }else{
            $res1 = Db::name('xy_convey')->where('id',$oid)->update(['c_status'=>2]);//记录账号异常
        }
    }
}