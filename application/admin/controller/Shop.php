<?php

// +----------------------------------------------------------------------
// | ThinkAdmin
// +----------------------------------------------------------------------
// | 源码来自九牛网(www.9nw.cc) 会员投稿
// +----------------------------------------------------------------------
// |  注：在使用本系统禁止用于一切非法行为。使用用途仅限于测试、实验、研究为目的，禁止用于一切商业运营，本团队不承担使用者在使用过程中的任何违法行为负责。
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\service\NodeService;
use library\Controller;
use library\tools\Data;
use think\Db;
use PHPExcel;//tp5.1用法
use PHPExcel_IOFactory;

/**
 * 商城管理
 * Class Users
 * @package app\admin\controller
 */
class Shop extends Controller
{

    /**
     * 订单列表
     *@auth true
     *@menu true
     */
    public function order_list()
    {
        $this->title = '订单列表';
        $where = [];
        if(input('oid/s','')) $where[] = ['xc.id','like','%'.input('oid','').'%'];
        if(input('username/s','')) $where[] = ['u.username','like','%' . input('username/s','') . '%'];
        if(input('addtime/s','')){
            $arr = explode(' - ',input('addtime/s',''));
            $where[] = ['fc.addtime','between',[strtotime($arr[0]),strtotime($arr[1])]];
        }

        $user = session('admin_user');
        if($user['authorize'] > 0 && !empty($user['nodes']) ){
            //获取直属下级
            $mobile = $user['phone'];
            $uid = db('xy_users')->where('tel', $mobile)->value('id');

            $ids1  = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');

            $ids1 ? $ids2  = db('xy_users')->where('parent_id','in', $ids1)->field('id')->column('id') : $ids2 = [];

            $ids2 ? $ids3  = db('xy_users')->where('parent_id','in', $ids2)->field('id')->column('id') : $ids3 = [];

            $ids3 ? $ids4  = db('xy_users')->where('parent_id','in', $ids3)->field('id')->column('id') : $ids4 = [];

            $idsAll = array_merge([$uid],$ids1,$ids2 ,$ids3 ,$ids4);  //所有ids
            $where[] = ['xc.uid','in',$idsAll];


            //echo '<pre>';
            //var_dump($where,$idsAll,$ids3,$ids4);die;
        }

        $this->_query('xy_shop_order')
            ->alias('xc')
            ->leftJoin('xy_users u','u.id=xc.uid')
            ->leftJoin('xy_goods_list g','g.id=xc.gid')
            ->field('xc.*,u.username,g.goods_name,g.goods_price')
            ->where($where)
            ->order('addtime desc')
            ->page();
    }




    /**
     * 商品管理
     *@auth true
     *@menu true
     */
    public function goods_list()
    {
        $this->title = '商品管理';

        $this->cate = db('xy_shop_goods_cate')->order('addtime asc')->select();
        $where = [];
        //var_dump($this->cate);die;
        $query = $this->_query('xy_shop_goods_list');
        if(input('title/s',''))$where[] = ['goods_name','like','%' . input('title/s','') . '%'];
        if(input('cid/d',''))$where[] = ['cid','=',input('cid/d','')];
        if(input('lang/d',''))$where[] = ['lang','=',input('lang/d','')];
        
        $this->assign('lang',input('lang/d',''));
        
        //var_dump($where);die;
        $query->where($where)->page();;


    }

    /**
     * 发货
     *@auth true
     *@menu true
     */
    public function fahuo()
    {
        $this->applyCsrfToken();
        $this->_save('xy_shop_order', ['status' => '2']);
    }



    /**
     * 商品分类
     *@auth true
     *@menu true
     */
    public function goods_cate()
    {
        $this->title = '分类管理';
        $this->_query('xy_shop_goods_cate')->page();
    }

    /**
     * 添加商品
     *@auth true
     *@menu true
     */
    public function add_goods()
    {
        if(\request()->isPost()){
            $this->applyCsrfToken();//验证令牌
            $shop_name      = input('post.shop_name/s','');
            $goods_name     = input('post.goods_name/s','');
            $goods_price    = input('post.goods_price/f',0);
            $goods_pic      = input('post.goods_pic/s','');
            $goods_info     = input('post.goods_info/s','');
            $lang    = input('post.lang/f',1);
            $cid     = input('post.cid/d',1);
            $res = $this->submit_goods($shop_name,$goods_name,$goods_price,$goods_pic,$goods_info,$lang,$cid);
            if($res['code']===0)
                return $this->success($res['info'],'/admin.html#/admin/shop/goods_list.html');
            else 
                return $this->error($res['info']);
        }
        $this->cate = db('xy_shop_goods_cate')->order('addtime asc')->select();
        return $this->fetch();
    }


    /**
     * 添加分类
     *@auth true
     *@menu true
     */
    public function add_cate()
    {
        if(\request()->isPost()){
            $this->applyCsrfToken();//验证令牌
            $name      = input('post.name/s','');
            $pic     = input('post.cate_pic/s','');
            $info    = input('post.cate_info/s','');
            $min    = input('post.min/s','');

            $res = $this->submit_cate($name,$pic,$info,$min,0);
            if($res['code']===0)
                return $this->success($res['info'],'/admin.html#/admin/shop/goods_cate.html');
            else
                return $this->error($res['info']);
        }
        return $this->fetch();
    }


    /**
     * 提交商品
     *
     * @param string $shop_name
     * @param string $goods_name
     * @param string $goods_price
     * @param string $goods_pic
     * @param string $goods_info
     * @param string $id 传参则更新数据,不传则写入数据
     * @return array
     */
    public function submit_goods($shop_name,$goods_name,$goods_price,$goods_pic,$goods_info,$lang,$cid,$id='')
    {
        if(!$goods_pic) return ['code'=>1,'info'=>('请上传商品图片')];
        if(!$goods_name) return ['code'=>1,'info'=>('请输入商品名称')];
        if(!$shop_name) return ['code'=>1,'info'=>('请输入店铺名称')];
        if(!$goods_price) return ['code'=>1,'info'=>('请填写正确的商品价格')];
        $data = [
            'shop_name'     => $shop_name,
            'goods_name'    => $goods_name,
            'goods_price'   => $goods_price,
            'goods_pic'     => $goods_pic,
            'goods_info'    => $goods_info,
            'lang' => $lang,
            'cid'    => $cid,
            'addtime'       => time()
        ];
        if(!$id){
            $res = Db::table('xy_shop_goods_list')->insert($data);
        }else{
            $res = Db::table('xy_shop_goods_list')->where('id',$id)->update($data);
        }
        if($res)
            return ['code'=>0,'info'=>'操作成功!'];
        else
            return ['code'=>1,'info'=>'操作失败!'];
    }

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
    public function submit_cate($name,$pic,$info,$min,$id)
    {
        if(!$name) return ['code'=>1,'info'=>('请输入分类名称')];
        //if(!$pic) return ['code'=>1,'info'=>('请输入比例')];

        $data = [
            'name'     => $name,
            'cate_pic'    => $pic,
            'bili'        => 0,
            'cate_info'   => $info,
            'addtime'       => time(),
            'min'           =>$min
        ];
        if(!$id){
            $res = Db::table('xy_shop_goods_cate')->insert($data);
        }else{
            $res = Db::table('xy_shop_goods_cate')->where('id',$id)->update($data);
        }
        if($res)
            return ['code'=>0,'info'=>'操作成功!'];
        else
            return ['code'=>1,'info'=>'操作失败!'];
    }

    /**
     * 编辑商品信息
     * @auth true
     * @menu true
     */
    public function edit_goods($id)
    {
        $id = (int)$id;
        if(\request()->isPost()){
            $this->applyCsrfToken();//验证令牌
            $shop_name      = input('post.shop_name/s','');
            $goods_name     = input('post.goods_name/s','');
            $goods_price    = input('post.goods_price/f',0);
            $goods_pic      = input('post.goods_pic/s','');
            $goods_info     = input('post.goods_info/s','');
            $id             = input('post.id/d',0);
            $cid             = input('post.cid/d',0);
            $res = $this->submit_goods($shop_name,$goods_name,$goods_price,$goods_pic,$goods_info,$cid,$id);
            if($res['code']===0)
                return $this->success($res['info'],'/admin.html#/admin/shop/goods_list.html');
            else 
                return $this->error($res['info']);
        }
        $info = db('xy_shop_goods_list')->find($id);
        $this->cate = db('xy_shop_goods_cate')->order('addtime asc')->select();
        $this->assign('cate',$this->cate);
        $this->assign('info',$info);
        return $this->fetch();
    }
    /**
     * 编辑分类信息
     * @auth true
     * @menu true
     */
    public function edit_cate($id)
    {
        $id = (int)$id;
        if(\request()->isPost()){
            $this->applyCsrfToken();//验证令牌
            $name      = input('post.name/s','');
            $pic     = input('post.cate_pic/s','');
            $info    = input('post.cate_info/s','');
            $min    = input('post.min/s','');

            $res = $this->submit_cate($name,$pic,$info,$min,$id);
            if($res['code']===0)
                return $this->success($res['info'],'/admin.html#/admin/shop/goods_cate.html');
            else
                return $this->error($res['info']);
        }
        $info = db('xy_shop_goods_cate')->find($id);
        $this->assign('info',$info);
        return $this->fetch();
    }

    /**
     * 更改商品状态
     * @auth true
     */
    public function edit_goods_status()
    {
        $this->applyCsrfToken();
        $this->_form('xy_goods_list', 'form');
    }

    /**
     * 删除商品
     * @auth true
     */
    public function del_goods()
    {
        $this->applyCsrfToken();
        $this->_delete('xy_shop_goods_list');
    }
    /**
     * 删除分类
     * @auth true
     */
    public function del_cate()
    {
        $this->applyCsrfToken();
        $this->_delete('xy_shop_goods_cate');
    }








    /**
     * 禁用产品
     * @auth true
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function forbid()
    {
        $this->applyCsrfToken();
        $this->_save('xy_shop_goods_list', ['is_tj' => '0']);
    }

    /**
     * 启用产品
     * @auth true
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function resume()
    {
        $this->applyCsrfToken();
        $this->_save('xy_shop_goods_list', ['is_tj' => '1']);
    }



    /**
     * 导出xls
     * @auth true
     */
    public function daochu(){
        $map = array();
        //搜索时间
        if( !empty($start_date) && !empty($end_date) ) {
            $start_date = strtotime($start_date . "00:00:00");
            $end_date = strtotime($end_date . "23:59:59");
            $map['_string'] = "( a.create_time >= {$start_date} and a.create_time < {$end_date} )";
        }

        $list = Db::name('xy_deposit')
            ->alias('xd')
            ->leftJoin('xy_users u','u.id=xd.uid')
            ->leftJoin('xy_bankinfo bk','bk.id=xd.bk_id')
            ->field('xd.*,u.username,u.tel mobile,bk.bankname,bk.cardnum,u.id uid')
            ->order('addtime desc,endtime desc')->select();

        //$list = $list[0];

        //echo '<pre>';
        //var_dump($list);

        foreach( $list as $k=>&$_list ) {
            //var_dump($_list);die;

            $_list['endtime'] ? $_list['endtime'] = date('m/d H:i', $_list['endtime']) : '';
            $_list['addtime'] ? $_list['addtime'] = date('m/d H:i', $_list['addtime']) : '';

            if ($_list['type'] == 'zfb') {
                $_list['type'] = '支付宝';
            }else if ($_list['type'] == 'wx') {
                $_list['type'] = '微信 ';
            }else  {
                $_list['type'] = '银行卡';
            }

            if ($_list['status'] == 1) {
                $_list['status'] = '待审核';
            }else if ($_list['status'] == 2) {
                $_list['status'] = '审核通过 ';
            }else  {
                $_list['status'] = '审核驳回';
            }

            unset($list[$k]['bk_id']);
        }

        //echo '<pre>';
        //var_dump($list);die;

        //3.实例化PHPExcel类
        $objPHPExcel = new PHPExcel();
        //4.激活当前的sheet表
        $objPHPExcel->setActiveSheetIndex(0);
        //5.设置表格头（即excel表格的第一行）
        //$objPHPExcel
            $objPHPExcel->getActiveSheet()->setCellValue('A1', '订单号');
            $objPHPExcel->getActiveSheet()->setCellValue('B1', '用户昵称');
            $objPHPExcel->getActiveSheet()->setCellValue('C1', '电话');
            $objPHPExcel->getActiveSheet()->setCellValue('D1', '提现金额');
            $objPHPExcel->getActiveSheet()->setCellValue('E1', '提现账户');
            $objPHPExcel->getActiveSheet()->setCellValue('F1', '提现银行');
            $objPHPExcel->getActiveSheet()->setCellValue('G1', '实际到账');
            $objPHPExcel->getActiveSheet()->setCellValue('H1', '提交时间');
            $objPHPExcel->getActiveSheet()->setCellValue('I1', '提现方式');
            $objPHPExcel->getActiveSheet()->setCellValue('J1', '状态');


//        $objPHPExcel->getActiveSheet()->SetCellValue('A1', '订单号');
//        $objPHPExcel->getActiveSheet()->SetCellValue('B1', '标题');
//        $objPHPExcel->getActiveSheet()->SetCellValue('C1', '金额');

        //设置A列水平居中
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('A')->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //设置单元格宽度
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(10);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(30);


        //6.循环刚取出来的数组，将数据逐一添加到excel表格。
        for($i=0;$i<count($list);$i++){
            $objPHPExcel->getActiveSheet()->setCellValue('A'.($i+2),$list[$i]['id']);//ID
            $objPHPExcel->getActiveSheet()->setCellValue('B'.($i+2),$list[$i]['username']);//标签码
            $objPHPExcel->getActiveSheet()->setCellValue('C'.($i+2),$list[$i]['mobile']);//防伪码
            $objPHPExcel->getActiveSheet()->setCellValue('D'.($i+2),$list[$i]['num']);//防伪码
            $objPHPExcel->getActiveSheet()->setCellValue('E'.($i+2),$list[$i]['cardnum']);//防伪码
            $objPHPExcel->getActiveSheet()->setCellValue('F'.($i+2),$list[$i]['bankname']);//防伪码
            $objPHPExcel->getActiveSheet()->setCellValue('G'.($i+2),$list[$i]['endtime']);//防伪码
            $objPHPExcel->getActiveSheet()->setCellValue('H'.($i+2),$list[$i]['addtime']);//防伪码
            $objPHPExcel->getActiveSheet()->setCellValue('I'.($i+2),$list[$i]['type']);//防伪码
            $objPHPExcel->getActiveSheet()->setCellValue('J'.($i+2),$list[$i]['status']);//防伪码
        }

        //7.设置保存的Excel表格名称
        $filename = 'tixian'.date('ymd',time()).'.xls';
        //8.设置当前激活的sheet表格名称；

        $objPHPExcel->getActiveSheet()->setTitle('sheet'); // 设置工作表名

        //8.设置当前激活的sheet表格名称；
        $objPHPExcel->getActiveSheet()->setTitle('防伪码');
        //9.设置浏览器窗口下载表格
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="'.$filename.'"');
        //生成excel文件
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //下载文件在浏览器窗口
        $objWriter->save('php://output');
        exit;
    }



    /**
     * 批量拒绝
     * @auth true
     */
    public function do_deposit3()
    {
        $ids =[];
        if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
            $ids = explode(',',$_REQUEST['id']);
            foreach ($ids as $id) {
                $t = Db::name('xy_deposit')->where('id',$id)->find();
                if ($t['status'] == 1) {
                    //通过
                    Db::name('xy_deposit')->where('id',$id)->update(['status'=>3,'endtime'=>time()]);
                    //驳回订单的业务逻辑
                    Db::name('xy_users')->where('id',$t['uid'])->setInc('balance',input('num/f',0));
                }
            }

            $this->success('处理成功','/admin.html#/admin/deal/deposit_list.html');
        }
    }
}