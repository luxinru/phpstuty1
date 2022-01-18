<?php

namespace app\admin\model;

use think\Model;
use think\Db;

class GoodsList extends Model
{

    protected $tabel = 'xy_goods_list';

    /**
     * 添加商品
     *
     * @param string $shop_name
     * @param string $goods_name
     * @param string $goods_price
     * @param string $goods_pic
     * @param string $goods_info
     * @param string $id 传参则更新数据,不传则写入数据
     * @return array
     */
    public function submit_goods($shop_name,$goods_name,$goods_price,$goods_pic,$lang,$cid,$id='')
    {
        if(!$goods_pic) return ['code'=>1,'info'=>lang('请上传商品图片')];
        if(!$goods_name) return ['code'=>1,'info'=>lang('请输入商品名称')];
        if(!$shop_name) return ['code'=>1,'info'=>lang('请输入店铺名称')];
        if(!$goods_price) return ['code'=>1,'info'=>lang('请填写正确的商品价格')];
        $data = [
            'shop_name'     => $shop_name,
            'goods_name'    => $goods_name,
            'goods_price'   => $goods_price,
            'goods_pic'     => $goods_pic,
           // 'goods_info'    => $goods_info,
            'lang' => $lang,
            'cid'    => $cid,
            'addtime'       => time()
        ];
        if(!$id){
            $res = Db::table('xy_goods_list')->insert($data);
        }else{
            $res = Db::table('xy_goods_list')->where('id',$id)->update($data);
        }
        if($res)
            return ['code'=>0,'info'=>lang('操作成功')];
        else 
            return ['code'=>1,'info'=>lang('操作失败')];
    }
}