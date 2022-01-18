<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;

/**
 * 订单列表
 */
class Order extends Base
{


    public function index()
    {
        $this->status = $status= input('get.status/d',-1);
        $where =[];
        if ($status) {
            $status == -1 ? $status = 0:'';
            $where['xc.status'] = $status;
        }
        $uid = session('user_id');
        $this->balance = Db::name('xy_users')->where('id',$uid)->value('balance');//获取用户今日已充值金额


        $this->_query('xy_convey')
            ->where('xc.uid',session('user_id'))
            ->alias('xc')
            ->leftJoin('xy_goods_list xg','xc.goods_id=xg.id')
            ->field('xc.*,xg.goods_name,xg.shop_name,xg.goods_price,xg.goods_pic')
            ->order('xc.addtime desc')
            ->where($where)
            ->page();
        return $this->fetch();
    }








    /**
     * 获取订单列表
     */
    public function order_list()
    {
        $page = input('post.page/d',1);
        $num = input('post.num/d',400);
        $limit = ( (($page - 1) * $num) . ',' . $num );
        $type = input('post.type/d',1);
        switch($type){
            case 1: //获取待处理订单
                $type = 0;
                break;
            case 2: //获取冻结中订单
                $type = 5;
                break;
            case 3: //获取已完成订单
                $type = 1;
                break;
        }
        $data = db('xy_convey')
                ->where('xc.uid',session('user_id'))
                ->where('xc.status',$type)
                ->alias('xc')
                ->leftJoin('xy_goods_list xg','xc.goods_id=xg.id')
                ->field('xc.*,xg.goods_name,xg.shop_name,xg.goods_price,xg.goods_pic')
                ->order('xc.addtime desc')
                ->limit($limit)
                ->select();
        
        foreach ($data as &$datum) {
            $datum['endtime'] = date('Y/m/d H:i:s',$datum['endtime']);
            $datum['addtime'] = date('Y/m/d H:i:s',$datum['addtime']);
        }


        if(!$data) json(['code'=>1,'info'=>'暂无数据']);
        return json(['code'=>0,'info'=>'请求成功','data'=>$data]);
    }

    /**
     * 获取单笔订单详情
     */
    public function order_info()
    {
        if(\request()->isPost()){
            $oid = input('post.id','');
            $oinfo = db('xy_convey')
                        ->alias('xc')
                        ->leftJoin('xy_member_address ar','ar.uid=xc.uid','ar.is_default=1')
                        ->leftJoin('xy_goods_list xg','xg.id=xc.goods_id')
                        ->leftJoin('xy_users u','u.id=xc.uid')
                        ->field('xc.id oid,xc.commission,xc.addtime,xc.endtime,xc.status,xc.num,xc.goods_count,xc.add_id,xg.goods_name,xg.goods_price,xg.shop_name,xg.goods_pic,ar.name,ar.tel,ar.address,u.balance')
                        ->where('xc.id',$oid)
                        ->where('xc.uid',session('user_id'))
                        ->find();
            if(!$oinfo) return json(['code'=>1,'暂无数据']);
            $oinfo['endtime'] = date('Y/m/d H:i:s', $oinfo['endtime']  );
            $oinfo['addtime'] = date('Y/m/d H:i:s', $oinfo['addtime']  );

            return json(['code'=>0,'info'=>'请求成功','data'=>$oinfo]);
        }
    }
    
    /**
     * 处理订单
     */
    public function do_order()
    {
        if(request()->isPost()){
            $oid = input('post.oid/s','');
            $status = input('post.status/d',1);
            $add_id = input('post.add_id/d',0);
            if(!\in_array($status,[1,2])) json(['code'=>1,'info'=>'参数错误']);

            $res = model('admin/Convey')->do_order($oid,$status,session('user_id'),$add_id);
            return json($res);
        }
        return json(['code'=>1,'info'=>'错误请求']);
    }

    /**
     * 获取充值订单
     */
    public function get_recharge_order()
    {
        $uid = session('user_id');
        $page = input('post.page/d',1);
        $num = input('post.num/d',400);
        $limit = ( (($page - 1) * $num) . ',' . $num );
        $info = db('xy_recharge')->where('uid',$uid)->order('addtime desc')->limit($limit)->select();
        if(!$info) return json(['code'=>1,'info'=>'暂无数据']);
        return json(['code'=>0,'info'=>'请求成功','data'=>$info]);
    }

    /**
     * 验证提现密码
     */
    public function check_pwd2()
    {
        if(!request()->isPost()) return json(['code'=>1,'info'=>'错误请求']);
        $pwd2 = input('post.pwd2/s','');
        $info = db('xy_users')->field('pwd2,salt2')->find(session('user_id'));
        if($info['pwd2']=='') return json(['code'=>1,'info'=>'未设置交易密码']);
        if($info['pwd2']!=sha1($pwd2.$info['salt2'].config('pwd_str'))) return json(['code'=>1,'info'=>'密码错误']);
        return json(['code'=>0,'info'=>'验证通过']);
    }
}