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
 * 商城
 * Class Index
 * @package app\index\controller
 */
class Shop extends Base
{
    /**
     * 入口跳转链接
     */
    public function index2()
    {
        $this->redirect('home');
    }

    public function index()
    {
        $this->banner = Db::name('xy_banner')->select();
        //if($this->banner) $this->banner = explode('|',$this->banner);
        $this->gundong = db('xy_index_msg')->where('id',8)->value('content');;;
        $this->shoplist = db('xy_shop_goods_list')->where('is_tj',1)->limit(10)->select();;



        $this->assign('pic','/upload/qrcode/user/'.(session('user_id')%20).'/'.session('user_id').'-1.png');
        $this->cate = db('xy_shop_goods_cate')->order('id asc')->select();

        //一天的
        $this->lixibao = db('xy_lixibao_list')->order('id asc')->find();

        return $this->fetch();
    }


    public function goodslist()
    {
        $where = [];
        if(input('cid/d',0))$where[] = ['cid','=',input('cid/d',0)];
        if(input('name/s',''))$where[] = ['goods_name','like','%' . input('name/s','') . '%'];

        //一天的

        $this->_query('xy_shop_goods_list')->where($where)->page();
        //return $this->fetch();
    }


    public function orderlist()
    {
        $where = [];
        if(input('cid/d',0))$where[] = ['cid','=',input('cid/d',0)];
        if(input('name/s',''))$where[] = ['goods_name','like','%' . input('name/s','') . '%'];

        //一天的
        if(request()->isPost()) {
            $uid = session('user_id');
            $page = input('post.page/d',1);
            $num = input('post.num/d',10);
            $limit = ( (($page - 1) * $num) . ',' . $num );
            $status = input('post.status/d',1);

            $data = db('xy_shop_order')
                ->where('uid',session('user_id'))
                ->where('status',$status)
                ->order('addtime desc')
                ->limit($limit)
                ->select();

            foreach ($data as &$datum) {
                //$datum['endtime'] = date('Y/m/d H:i:s',$datum['endtime']);
                $datum['addtime'] = date('Y/m/d H:i:s',$datum['addtime']);
            }


            if(!$data) json(['code'=>1,'info'=>'暂无数据']);
            return json(['code'=>0,'info'=>'请求成功','data'=>$data]);
        }
        return $this->fetch();
    }


    public function detail()
    {
        $id      = input('get.id/d',1);
        $this->info = db('xy_shop_goods_list')->find($id);;

        return $this->fetch();
    }

    public function order_info()
    {
        $uid = session('user_id');
        $id      = input('get.id/d',1);
        $this->endtime = date('Y/m/d H:i:s', time()+30*60);
        $this->address = db('xy_member_address')->where('uid',$uid)->find();
        $this->balance = db('xy_users')->where('id',$uid)->value('balance');
        $this->goods = db('xy_shop_goods_list')->find($id);

        return $this->fetch();
    }



     public function order_detail()
    {
        $uid = session('user_id');
        $id      = input('get.oid/s',1);
        $this->endtime = date('Y/m/d H:i:s', time()+3*24*60*60);
        $this->address = db('xy_member_address')->where('uid',$uid)->find();
        $this->balance = db('xy_users')->where('id',$uid)->value('balance');

        $order = db('xy_shop_order')->where('id',$id)->find();
//ar_dump($order);die;

        $this->goods = db('xy_shop_goods_list')->find($order['gid']);
        $this->order = $order;


        return $this->fetch();
    }


    //生成订单号
    function getSn($head='')
    {
        @date_default_timezone_set("PRC");
        $order_id_main = date('YmdHis') . mt_rand(1000, 9999);
        //唯一订单号码（YYMMDDHHIISSNNN）
        $osn = $head.substr($order_id_main,2); //生成订单号
        return $osn;
    }

    public function do_order()
    {
        $id = input('post.id','');
        $uid = session('user_id');
        $num = input('post.num',1);
        $goods = db('xy_shop_goods_list')->find($id);
        if (!$goods) {
            return json(['code'=>1,'info'=>'商品参数异常','data'=>[]]);
        }

        if ($num <= 0) {
            return json(['code'=>1,'info'=>'you are sb','data'=>[]]);
        }




        if (!$num) return json(['code'=>1,'info'=>'参数异常','data'=>[]]);
        $balance = db('xy_users')->where('id',$uid)->value('balance');
        if ( $balance < ($goods['goods_price']*$num) ) {
            return json(['code'=>1,'info'=>'可用余额不足,请先充值','data'=>[]]);
        }

        if( $goods['goods_price']*$num < 0 ) {
            return json(['code'=>1,'info'=>'you are sb','data'=>[]]);
        }

        $id1 = getSn('SP');
        $data = [
            'id'        =>$id1,
            'uid'       => $uid,
            'gid'       => $id,
            'price'       => $goods['goods_price'],
            'num'      => $num,
            'price2'      => $goods['goods_price']*$num,
            'status'   => 1,
            'addtime'   => time()
        ];
        $res = Db::name('xy_users')->where('id',$uid)->setDec('balance',$goods['goods_price']*$num);

        //
        $res1 = Db::name('xy_balance_log')->insert([
            //记录返佣信息
            'uid'       => $uid,
            'oid'       => $id1,
            'num'       => $goods['goods_price']*$num,
            'type'      => 11,
            'addtime'   => time()
        ]);

        $res = db('xy_shop_order')->insert($data);
        if($res)
            return json(['code'=>0,'info'=>'操作成功']);
        else
            return json(['code'=>1,'info'=>'操作失败']);

    }




}
