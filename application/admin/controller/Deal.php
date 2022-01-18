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
use payment\Payment;

/**
 * 交易中心
 * Class Users
 * @package app\admin\controller
 */
class Deal extends Controller
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
        $isjia = input('jia/s','0');
        $where[] = ['u.is_jia', '=' ,$isjia];
        if(input('oid/s','')) $where[] = ['xc.id','like','%'.input('oid','').'%'];
         $wheres = [];
         if(input('status')){
             switch(input('status')){
                case 1:
                    break;
                case 2:
                    $wheres = ['xc.status'=>0];
                    break;
                case 3:
                    $wheres = ['xc.status'=>1];
                case 5:
                    $wheres = ['xc.status'=>5];
                break;
             }

         }
          $res = Db::table('xy_balance_log')->select();
        $this->assign('res',$res);
        //  if(input('status/s','')) $where[] = ['xc.status','=',input('status/s')];
        if(input('username/s','')) $where[] = ['u.username','like','%' . input('username/s','') . '%'];
        if(input('addtime/s','')){
            $arr = explode(' - ',input('addtime/s',''));
            $where[] = ['xc.addtime','between',[strtotime($arr[0]),strtotime($arr[1])]];
        }
        if(input('tel/s','')) $where[] = ['u.tel','like','%'.input('tel','').'%'];

        $user = session('admin_user');
        if($user['authorize']==2  && !empty($user['nodes']) ){
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
        
        $order_tongji = Db::name('xy_convey')->alias('xc')->leftJoin('xy_users u','u.id=xc.uid')->where($where)->where(['xc.status'=>1])->field('sum(num) as sum_num, sum(commission) as sum_commission')->find();
        

        $this->assign('order_tongji', $order_tongji);
        
        $limit = input('limit/s', 20);
        $page = input('page/s', 1);
        $convey_list = Db::name('xy_convey')
            ->alias('xc')
            ->leftJoin('xy_users u','u.id=xc.uid')
            ->leftJoin('xy_goods_list g','g.id=xc.goods_id')
            ->field('xc.*,u.username,u.tel,g.goods_name,g.goods_price,u.parent_id')
            ->where($where)
            ->where($wheres)
            ->order('addtime desc')
            ->limit(($page - 1) * $limit, $limit)
            ->select();
        $this->today_num_list = [];
        foreach ($convey_list as $v) {
            $_this_date = date("Y-m-d", $v['addtime']);
            $_this_time = strtotime($_this_date . ' 00:00:00');
            $this->today_num_list[$v['id']]['date'] = $_this_date;
            $this->today_num_list[$v['id']]['today_num'] = Db::name('xy_convey')->where('uid', $v['uid'])->where("addtime", ">", $_this_time)->where("addtime", "<", $v['addtime'])->count() + 1;
            $this->today_num_list[$v['id']]['parent_id'] = $v['parent_id'];
            $this->today_num_list[$v['id']]['parent_username'] = '';
            if ($v['parent_id']) {
                $this->today_num_list[$v['id']]['parent_username'] = Db::name('xy_users')->where('id', $v['parent_id'])->value('username');
            }
        }
        
        $this->_query('xy_convey')
            ->alias('xc')
            ->leftJoin('xy_users u','u.id=xc.uid')
            ->leftJoin('xy_goods_list g','g.id=xc.goods_id')
            ->field('xc.*,u.username,u.tel,g.goods_name,g.goods_price,xc.user_current_balance')
            ->where($where)
             ->where($wheres)
            ->order('addtime desc')
            ->page();
    }

    /**
     * 手动解冻
     * @auth true
     * @menu true
     */
    public function jiedong()
    {
        $this->applyCsrfToken();
        $oid = input('post.id/s','');

        if ($oid) {
            $oinfo = Db::name('xy_convey')->where('id',$oid)->find();
            if ( $oinfo['status'] != 5 ) {
                return $this->error('该订单未冻结!');
            }
            Db::name('xy_convey')->where('id',$oinfo['id'])->update(['status'=>0]);
            /*Db::name('xy_convey')->where('id',$oinfo['id'])->update(['status'=>1]);
            //
            $res1 = Db::name('xy_users')
                ->where('id', $oinfo['uid'])
                ->inc('balance',$oinfo['num']+$oinfo['commission'])
                ->dec('freeze_balance',$oinfo['num']+$oinfo['commission']) //冻结商品金额 + 佣金
                ->update(['deal_status'=>1]);
            $this->deal_reward($oinfo['uid'],$oinfo['id'],$oinfo['num'],$oinfo['commission']);*/
            return $this->success('解冻成功!');
        }
        return $this->success('解冻成功!');
    }

    /**
     * 交易返佣
     * @return void
     */
    public function deal_reward($uid,$oid,$num,$cnum)
    {

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


    /**
     * 处理用户交易订单
     */
    public function do_user_order()
    {
        $this->applyCsrfToken();
        $oid = input('post.id/s','');
        $status = input('post.status/d',1);
        if(!\in_array($status,[3,4])) return $this->error('参数错误');
        $res = model('Convey')->do_order($oid,$status);
        if($res['code']===0)
            return $this->success('操作成功');
        else
            return $this->error($res['info']);
    }

    /**
     * 交易控制
     * @auth true
     * @menu true
     */
    public function deal_console()
    {
        $this->title = '交易控制';
$this->default_lang=config('app.default_lang');
        if(request()->isPost()){
            $deal_min_balance = input('post.deal_min_balance/d',0);
            $deal_timeout     = input('post.deal_timeout/d',0);
            $deal_min_num     = input('post.deal_min_num/d',0);   
            $deal_max_num     = input('post.deal_max_num/d',0);
            $deal_count       = input('post.deal_count/d',0);
            $deal_reward_count= input('post.deal_reward_count/d',0);
            $deal_feedze      = input('post.deal_feedze/d',0);
            $deal_error       = input('post.deal_error/d',0);
            $deal_commission  = input('post.deal_commission/f',0);
            $_1reward  = input('post.1_reward/f',0);
            $_2reward  = input('post.2_reward/f',0);
            $_3reward  = input('post.3_reward/f',0);
            $_1_d_reward  = input('post.1_d_reward/f',0);
            $_2_d_reward  = input('post.2_d_reward/f',0);
            $_3_d_reward  = input('post.3_d_reward/f',0);
            $_4_d_reward  = input('post.4_d_reward/f',0);
            $_5_d_reward  = input('post.5_d_reward/f',0);
            
            
             $default_lang  = input('post.default_lang/s',0);
            //可以加上限制条件
            if($deal_commission>1||$deal_commission<0) return $this->error('参数错误'); 
            config('default_lang',$default_lang);
             setconfig(['default_lang'],[$default_lang]);
            
            
            setconfig(['beisa1'],[input('post.beisa1')]);
            setconfig(['beisa2'],[input('post.beisa2')]);
            setconfig(['beisa3'],[input('post.beisa3')]);
            setconfig(['beisa4'],[input('post.beisa4')]);
            setconfig(['beisa5'],[input('post.beisa5')]);
            setconfig(['beisa6'],[input('post.beisa6')]);
            
            
            
            setconfig(['deal_min_balance'],[$deal_min_balance]);
            setconfig(['deal_timeout'],[$deal_timeout]);
            setconfig(['deal_min_num'],[$deal_min_num]);
            setconfig(['deal_max_num'],[$deal_max_num]);
            setconfig(['deal_reward_count'],[$deal_reward_count]);
            setconfig(['deal_count'],[$deal_count]);
            setconfig(['deal_feedze'],[$deal_feedze]);
            setconfig(['deal_error'],[$deal_error]);
            setconfig(['deal_commission'],[$deal_commission]);
            setconfig(['1_reward'],[$_1reward]);
            setconfig(['2_reward'],[$_2reward]);
            setconfig(['3_reward'],[$_3reward]);
            setconfig(['1_d_reward'],[$_1_d_reward]);
            setconfig(['2_d_reward'],[$_2_d_reward]);
            setconfig(['3_d_reward'],[$_3_d_reward]);
            setconfig(['4_d_reward'],[$_4_d_reward]);
            setconfig(['5_d_reward'],[$_5_d_reward]);
            setconfig(['vip_1_commission'],[input('post.vip_1_commission/f')]);
            setconfig(['vip_2_commission'],[input('post.vip_2_commission/f')]);
            setconfig(['vip_2_num'],[input('post.vip_2_num/f')]);
            setconfig(['vip_3_commission'],[input('post.vip_3_commission/f')]);
            setconfig(['vip_3_num'],[input('post.vip_3_num/f')]);
            setconfig(['master_cardnum'],[input('post.master_cardnum')]);
            setconfig(['master_name'],[input('post.master_name')]);
            setconfig(['master_bank'],[input('post.master_bank')]);
            setconfig(['master_bk_address'],[input('post.master_bk_address')]);
            setconfig(['deal_zhuji_time'],[input('post.deal_zhuji_time')]);
            setconfig(['deal_shop_time'],[input('post.deal_shop_time')]);
            setconfig(['app_url'],[input('post.app_url')]);
            setconfig(['version'],[input('post.version')]);

            setconfig(['tixian_time_1'],[input('post.tixian_time_1')]);
            setconfig(['tixian_time_2'],[input('post.tixian_time_2')]);
            setconfig(['tixian_type'],[input('post.tixian_type')]);
            setconfig(['chongzhi_type'],[input('post.chongzhi_type')]);
            setconfig(['chongzhi_time_1'],[input('post.chongzhi_time_1')]);
            setconfig(['chongzhi_time_2'],[input('post.chongzhi_time_2')]);

            setconfig(['order_time_1'],[input('post.order_time_1')]);
            setconfig(['order_time_2'],[input('post.order_time_2')]);

            setconfig(['duanxin_status'],[input('post.duanxin_status')]);
            setconfig(['duanxin_type'],[input('post.duanxin_type')]);
            setconfig(['user'],[input('post.user')]);
            setconfig(['pass'],[input('post.pass')]);
            setconfig(['sign'],[input('post.sign')]);
            setconfig(['aliyun_access'],[input('post.aliyun_access')]);
            setconfig(['aliyun_key'],[input('post.aliyun_key')]);
            setconfig(['aliyun_sign'],[input('post.aliyun_sign')]);
            setconfig(['aliyun_template'],[input('post.aliyun_template')]);
            setconfig(['yunzhixun_sid'],[input('post.yunzhixun_sid')]);
            setconfig(['yunzhixun_token'],[input('post.yunzhixun_token')]);
            setconfig(['yunzhixun_appid'],[input('post.yunzhixun_appid')]);
            setconfig(['yunzhixun_templateid'],[input('post.yunzhixun_templateid')]);


            setconfig(['lxb_bili'],[input('post.lxb_bili')]);
            setconfig(['lxb_time'],[input('post.lxb_time')]);
            setconfig(['lxb_sy_bili1'],[input('post.lxb_sy_bili1')]);
            setconfig(['lxb_sy_bili2'],[input('post.lxb_sy_bili2')]);
            setconfig(['lxb_sy_bili3'],[input('post.lxb_sy_bili3')]);
            setconfig(['lxb_sy_bili4'],[input('post.lxb_sy_bili4')]);
            setconfig(['lxb_sy_bili5'],[input('post.lxb_sy_bili5')]);
            setconfig(['lxb_ru_max'],[input('post.lxb_ru_max')]);
            setconfig(['lxb_ru_min'],[input('post.lxb_ru_min')]);

            setconfig(['shop_status'],[input('post.shop_status')]);

            setconfig(['tankuang_status'],[input('post.tankuang_status')]);

            setconfig(['bank'],[input('post.bank')]);
            //var_dump(input('post.bank'));die;
            //
            $fileurl = APP_PATH . "../config/bank.txt";
            file_put_contents($fileurl, input('post.bank')); // 写入配置文件


            return $this->success('操作成功!');
        }

       // var_dump(config('master_name'));die;
        $fileurl = APP_PATH . "../config/bank.txt";
        $this->bank = file_get_contents($fileurl); // 写入配置文件

        return $this->fetch();
    }

    /**
     * 商品管理
     *@auth true
     *@menu true
     */
    public function goods_list()
    {
        $this->title = '商品管理';

        $cate = db('xy_goods_cate')->order('id asc')->select();
        foreach ($cate as $k=>$v){
            $cate[$k]['lvx']= db('xy_level')->where('id',$v['level_id'])->value('name');
        }
         $this->assign('cate',$cate);
        $where = [];
        //var_dump($this->cate);die;
        $query = $this->_query('xy_goods_list');
        if(input('title/s',''))$where[] = ['goods_name','like','%' . input('title/s','') . '%'];
        if(input('cid/d',''))$where[] = ['cid','=',input('cid/d','')];
        if(input('lang/d',''))$where[] = ['lang','=',input('lang/d','')];
        
        $this->assign('lang',input('lang/d',''));
        //var_dump($where);die;
        $query->where($where)->page();;


    }


    /**
     * 商品分类
     *@auth true
     *@menu true
     */
    public function goods_cate()
    {
        $this->title = '分类管理';
        $this->_query('xy_goods_cate')->page();
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
           // $goods_info     = input('post.goods_info/s','');
              $lang    = input('post.lang/f',1);
            $cid     = input('post.cid/d',1);
            $res = model('GoodsList')->submit_goods($shop_name,$goods_name,$goods_price,$goods_pic,$lang,$cid);
            if($res['code']===0)
                return $this->success($res['info'],'/admin.html#/admin/deal/goods_list.html');
            else 
                return $this->error($res['info']);
        }
           
        $cate = db('xy_goods_cate')->order('id asc')->select();
        
        foreach ($cate as $k=>$v){
            $cate[$k]['lvx']= db('xy_level')->where('id',$v['level_id'])->value('name');
        }
        
        $this->assign('cate',$cate);
      
        return $this->fetch();
    }


    /**
     * 添加商品
     *@auth true
     *@menu true
     */
    public function add_cate()
    {
       
        if(\request()->isPost()){
            $this->applyCsrfToken();//验证令牌
            $name      = input('post.name/s','');
            $bili     = input('post.bili/s','');
            $pipei_min = input('post.pipei_min/s','');
            $pipei_max = input('post.pipei_max/s','');
            $info    = input('post.cate_info/s','');
            $min    = input('post.min/s','');
           
           
            $res = $this->submit_cate($name,$bili,$pipei_min, $pipei_max,$info,$min,0);
            if($res['code']===0)
                return $this->success($res['info'],'/admin.html#/admin/deal/goods_cate.html');
            else
                return $this->error($res['info']);
        }
        return $this->fetch();
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
    public function submit_cate($name,$bili,$pipei_min, $pipei_max,$info,$min,$id)
    {
        if(!$name) return ['code'=>1,'info'=>('请输入分类名称')];
        if(!$bili) return ['code'=>1,'info'=>('请输入比例')];

        $data = [
            'name'     => $name,
            'bili'    => $bili,
            'pipei_min' =>$pipei_min,
            'pipei_max' =>$pipei_max,
            'cate_info'   => $info,
            'addtime'       => time(),
            'min'           =>$min
        ];
        if(!$id){
            $res = Db::table('xy_goods_cate')->insert($data);
        }else{
            $res = Db::table('xy_goods_cate')->where('id',$id)->update($data);
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
            $res = model('GoodsList')->submit_goods($shop_name,$goods_name,$goods_price,$goods_pic,$goods_info,$cid,$id);
            if($res['code']===0)
                return $this->success($res['info'],'/admin.html#/admin/deal/goods_list.html');
            else 
                return $this->error($res['info']);
        }
        $info = db('xy_goods_list')->find($id);
        $this->cate = db('xy_goods_cate')->order('addtime asc')->select();
        $this->assign('cate',$this->cate);
        $this->assign('info',$info);
        return $this->fetch();
    }
 /**
     * 编辑商品信息
     * @auth true
     * @menu true
     */
    public function edit_cate($id)
    {
        $id = (int)$id;
        if(\request()->isPost()){
            $this->applyCsrfToken();//验证令牌
            $name      = input('post.name/s','');
            $bili     = input('post.bili/s','');
            
            $pipei_min = input('post.pipei_min/s','');
            $pipei_max = input('post.pipei_max/s','');
            
            $info    = input('post.cate_info/s','');
            $min    = input('post.min/s','');

            $res = $this->submit_cate($name,$bili,$pipei_min, $pipei_max,$info,$min,$id);
            if($res['code']===0)
                return $this->success($res['info'],'/admin.html#/admin/deal/goods_cate.html');
            else
                return $this->error($res['info']);
        }
        $info = db('xy_goods_cate')->find($id);
      
        $this->assign('info',$info);

        $this->level = Db::table('xy_level')->select();

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
        $this->_delete('xy_goods_list');
    }
    /**
     * 删除商品
     * @auth true
     */
    public function del_cate()
    {
        $this->applyCsrfToken();
        $this->_delete('xy_goods_cate');
    }

    /**
     * 充值管理
     * @auth true
     * @menu true
     */
    public function user_recharge()
    {
        $this->title = '充值管理';
        $query = $this->_query('xy_recharge')->alias('xr');
        $where = [];
        if(input('oid/s','')) $where[] = ['xr.id','like','%'.input('oid','').'%'];
       
        if(input('tel/s','')) $where[] = ['xr.tel','=',input('tel/s','')];
        if(input('username/s','')) $where[] = ['u.username','like','%' . input('username/s','') . '%'];
        // if(input('is_jia/s','')) $where[] = ['u.is_jia','like','%'.input('is_jia','').'%'];
        $isjia = input('jia/s','0');
        $where[] = ['u.is_jia', '=' ,$isjia];
        if(input('addtime/s','')){
            $arr = explode(' - ',input('addtime/s',''));
            $begin_time = "{$arr[0]} 0:0:0";
            $end_time = "{$arr[1]} 23:59:59";
            $where[] = ['xr.addtime','between',[strtotime($begin_time),strtotime($end_time)]];
        }

        $user = session('admin_user');
        if($user['authorize'] ==2  && !empty($user['nodes']) ){
            //获取直属下级
            $mobile = $user['phone'];
            $uid = db('xy_users')->where('tel', $mobile)->value('id');

            $ids1  = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');

            $ids1 ? $ids2  = db('xy_users')->where('parent_id','in', $ids1)->field('id')->column('id') : $ids2 = [];

            $ids2 ? $ids3  = db('xy_users')->where('parent_id','in', $ids2)->field('id')->column('id') : $ids3 = [];

            $ids3 ? $ids4  = db('xy_users')->where('parent_id','in', $ids3)->field('id')->column('id') : $ids4 = [];

            $idsAll = array_merge([$uid],$ids1,$ids2 ,$ids3 ,$ids4);  //所有ids
            $where[] = ['xr.uid','in',$idsAll];
        }

        $this->recharge_ok_list = [];
        $recharge_ok_list = Db::name('xy_recharge')->field('uid, COUNT(*) AS num')->where('status', 2)->where('level', null)->group('uid')->select();
        foreach ($recharge_ok_list as $v) {
            $this->recharge_ok_list[$v['uid']] = $v['num'];
        }

        $recharge_sum = Db::name('xy_recharge')->alias('xr')->leftJoin('xy_users u','u.id=xr.uid')
            ->where($where)->where(['xr.status'=>2, 'xr.is_vip'=>0])->sum('xr.num');
		$this->assign('recharge_sum', $recharge_sum);
		 $where2=[];
		if(input('addtime/s','')){
            $arr = explode(' - ',input('addtime/s',''));
            $begin_time = "{$arr[0]} 0:0:0";
            $end_time = "{$arr[1]} 23:59:59";
            $where2[] = ['addtime','between',[strtotime($begin_time),strtotime($end_time)]];
        }
		$this->assign('jyts',DB::name('xy_recharge')->where($where2)->count('*'));
        $this->assign('jycswy',DB::name('xy_recharge')->field('uid,nums')->where($where2)->group('uid')
        ->having('max(nums)=1')
        ->count('*'));   
        
        $query->leftJoin('xy_users u','u.id=xr.uid')
            ->leftJoin('xy_users up','up.id=u.parent_id')
            ->leftJoin('xy_level le','u.level=le.level')
            ->field('xr.*,u.username,up.username as up_username,le.name as level_name')
            ->where($where)
            ->order('addtime desc')
            ->page();
    }


    /**
     * 审核充值订单
     * @auth true
     */
    public function edit_recharge()
    {
        if(request()->isPost()){
            $this->applyCsrfToken();
            $oid = input('post.id/s','');
            $status = input('post.status/d',1);
            $oinfo = Db::name('xy_recharge')->find($oid);
            
             if($oinfo['status'] != 1){
                $this->success('该订单已被处理！','');
                exit;
            }
            Db::startTrans();
            $res = Db::name('xy_recharge')->where('id',$oid)->update(['endtime'=>time(),'status'=>$status,'remark' => input('post.remark', '')]);
            //var_dump($res,$oinfo,$status);die;
            if($status==2){

                //var_dump($res,$oinfo['is_vip'],$oinfo['level']);die;

                if ($oinfo['is_vip']) {
                    $res1 = Db::name('xy_users')->where('id',$oinfo['uid'])->update(['level'=>999]);//$oinfo['level']
                }else{
                    $res1 = Db::name('xy_users')->where('id',$oinfo['uid'])->setInc('balance',$oinfo['num']);
                     Db::name('xy_users')->where('id',$oinfo['uid'])->setInc('recharge_num',$oinfo['num']);
                     
                     //首充用户 记录时间
                     $s_cz_time=Db::name('xy_users')->where('id',$oinfo['uid'])->value('s_cz_time');
                     if($s_cz_time==0){
                         Db::name('xy_users')->where('id',$oinfo['uid'])->update(['s_cz_time'=>time()]);
                     }
                     //历史充值用户 今日充值用户 记录时间
                     Db::name('xy_users')->where('id',$oinfo['uid'])->update(['cz_time'=>time()]);
                     
                    //  dump($oinfo);
                     //余额达到一定自动升级会员
                    $user_level=Db::name('xy_users')->where('id',$oinfo['uid'])->field('level,balance')->find();//用户当前等级
                    
                    $xy_level_arr=Db::name('xy_level')->order('level desc')->select();
                    foreach ($xy_level_arr as $v){
                         if($user_level['balance']>=$v['num']){
                            //满足升级条件 
                            // dump($v['level']);
                            // dump($oinfo['uid']);
                           $re= Db::name('xy_users')->where('id',$oinfo['uid'])->update(['level'=>$v['level'],'level_validity'=>time()+99999]);
                        //   dump($re);
                             break;
                        }
                    }
                   
                }

                $res2 = Db::name('xy_balance_log')
                        ->insert([
                            'uid'=>$oinfo['uid'],
                            'oid'=>$oid,
                            'num'=>$oinfo['num'],
                            'type'=>1,
                            'status'=>1,
                            'addtime'=>time(),
                        ]);


                //发放注册奖励
            }elseif($status==3){
                $res1 = Db::name('xy_message')
                        // ->insert([
                        //     'uid'=>$oinfo['uid'],
                        //     'type'=>2,
                        //     'title'=>'系统通知',
                        //     'content'=>'充值订单'.$oid.'已被退回，如有疑问请联系客服',
                        //     'addtime'=>time()
                        // ]);
                        ->insert([
                            'uid'=>$oinfo['uid'],
                            'type'=>2,
                            'title'=>'系统通知',
                            'content'=>'充值订单',
                            'content2'=>$oid,
                            'content3'=>'已被退回，如有疑问请联系客服',
                            'addtime'=>time()
                        ]);
            }
            if($res && $res1){
                Db::commit();

                if ($oinfo['is_vip']) {
                    goto end;
                }

                /************* 发放推广奖励 *********/
                $uinfo = Db::name('xy_users')->field('id,active')->find($oinfo['uid']);
                if($uinfo['active']===0){
                    Db::name('xy_users')->where('id',$uinfo['id'])->update(['active'=>1]);
                    //将账号状态改为已发放推广奖励
                    $userList = model('Users')->parent_user($uinfo['id'],3);
                    if($userList){
                        foreach($userList as $v){
                            if($v['status']===1 && ($oinfo['num'] * config($v['lv'].'_reward') != 0)){
                                    Db::name('xy_reward_log')
                                    ->insert([
                                        'uid'=>$v['id'],
                                        'sid'=>$uinfo['id'],
                                        'oid'=>$oid,
                                        'num'=>$oinfo['num'] * config($v['lv'].'_reward'),
                                        'lv'=>$v['lv'],
                                        'type'=>1,
                                        'status'=>1,
                                        'addtime'=>time(),
                                    ]);
                            }
                        }
                    }
                }
                /************* 发放推广奖励 *********/

                end:

                $this->success('操作成功!');
            }else{
                Db::rollback();
                $this->error('操作失败!');
            }
        }
    }

    /**
     * 提现管理
     * @auth true
     * @menu true
     */
    public function deposit_list()
    {
        $this->title = '提现列表';
        $query = $this->_query('xy_deposit')->alias('xd');
        $where =[];
        if(input('username/s','')) $where[] = ['u.username','like','%' . input('username/s','') . '%'];
        // if(input('is_jia/s','')) $where[] = ['u.is_jia','like','%'.input('is_jia','').'%'];
        $isjia = input('jia/s','0');
        $where[] = ['u.is_jia', '=' ,$isjia];
        if(input('tel/s','')) $where[] = ['u.tel','=',input('tel/s','')];
        
        if(input('addtime/s','')){
            $arr = explode(' - ',input('addtime/s',''));
            $begin_time = "{$arr[0]} 0:0:0";
            $end_time = "{$arr[1]} 23:59:59";
            $where[] = ['xd.addtime','between',[strtotime($begin_time),strtotime($end_time)]];
        }
        $user = session('admin_user');
        if($user['authorize'] ==2 && !empty($user['nodes']) ){
            //获取直属下级
            $mobile = $user['phone'];
            $uid = db('xy_users')->where('tel', $mobile)->value('id');

            $ids1  = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');

            $ids1 ? $ids2  = db('xy_users')->where('parent_id','in', $ids1)->field('id')->column('id') : $ids2 = [];

            $ids2 ? $ids3  = db('xy_users')->where('parent_id','in', $ids2)->field('id')->column('id') : $ids3 = [];

            $ids3 ? $ids4  = db('xy_users')->where('parent_id','in', $ids3)->field('id')->column('id') : $ids4 = [];

            $idsAll = array_merge([$uid],$ids1,$ids2 ,$ids3 ,$ids4);  //所有ids
            $where[] = ['xd.uid','in',$idsAll];
        }
        
        $depoist_sum = Db::name('xy_deposit')->alias('xd')->leftJoin('xy_users u','u.id=xd.uid')
            ->where($where)->where(['xd.status'=>2])->sum('xd.num');
		$this->assign('depoist_sum', $depoist_sum);
		
        $query->leftJoin('xy_users u','u.id=xd.uid')
            ->leftJoin('xy_bankinfo bk','bk.id=xd.bk_id')
            ->leftJoin('xy_users up','up.id=u.parent_id')
            ->field('xd.*,u.username,u.wx_ewm,u.zfb_ewm,bk.bankname,bk.username as khname,bk.tel,bk.cardnum,u.id uid,up.username as up_username')
            ->where($where)
            ->order('addtime desc,endtime desc')
            ->page();
    }




    /**
     * 利息宝管理
     * @auth true
     * @menu true
     */
    public function lixibao_log()
    {
        $this->title = '利息宝列表';
        $query = $this->_query('xy_lixibao')->alias('xd');
        $where =[];
        if(input('username/s','')) $where[] = ['u.username','like','%' . input('username/s','') . '%'];
        if(input('type/s','')) $where[] = ['xd.type','=',input('type/s',0)];
        if(input('addtime/s','')){
            $arr = explode(' - ',input('addtime/s',''));
            $begin_time = "{$arr[0]} 0:0:0";
            $end_time = "{$arr[1]} 23:59:59";            
            $where[] = ['xd.addtime','between',[strtotime($begin_time),strtotime($end_time)]];
        }


        $user = session('admin_user');
        if($user['authorize'] ==2  && !empty($user['nodes']) ){
            //获取直属下级
            $mobile = $user['phone'];
            $uid = db('xy_users')->where('tel', $mobile)->value('id');

            $ids1  = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');

            $ids1 ? $ids2  = db('xy_users')->where('parent_id','in', $ids1)->field('id')->column('id') : $ids2 = [];

            $ids2 ? $ids3  = db('xy_users')->where('parent_id','in', $ids2)->field('id')->column('id') : $ids3 = [];

            $ids3 ? $ids4  = db('xy_users')->where('parent_id','in', $ids3)->field('id')->column('id') : $ids4 = [];

            $idsAll = array_merge([$uid],$ids1,$ids2 ,$ids3 ,$ids4);  //所有ids
            $where[] = ['xd.uid','in',$idsAll];
        }

        $lixibao_sum = Db::name('xy_lixibao')->alias('xd')->leftJoin('xy_users u','u.id=xd.uid')
            ->where($where)->sum('xd.num');
		$this->assign('lixibao_sum', $lixibao_sum);
		
		
        $query->leftJoin('xy_users u','u.id=xd.uid')
            ->field('xd.*,u.tel,u.username,u.wx_ewm,u.zfb_ewm,u.id uid')
            ->where($where)
            ->order('addtime desc,endtime desc')
            ->page();
    }

    /**
     * 添加利息宝
     * @auth true
     * @menu true
     */
    public function add_lixibao()
    {
        if(\request()->isPost()){
            $this->applyCsrfToken();//验证令牌
            $name      = input('post.name/s','');
            $day       = input('post.day/d','');
            $bili      = input('post.bili/f','');
            $min_num    = input('post.min_num/s','');
            $max_num    = input('post.max_num/s','');
            $shouxu    = input('post.shouxu/s','');

            $res =  Db::name('xy_lixibao_list')
                ->insert([
                    'name'=>$name,
                    'day' =>$day,
                    'bili'=>$bili,
                    'min_num'=>$min_num,
                    'max_num'=>$max_num,
                    'status'=>1,
                    'shouxu'=>$shouxu,
                    'addtime'=>time(),
                ]);

            if($res)
                return $this->success('提交成功','/admin.html#/admin/deal/lixibao_list.html');
            else
                return $this->error('提交失败');
        }
        return $this->fetch();
    }
    /**
     * 编辑利息宝
     * @auth true
     * @menu true
     */
    public function edit_lixibao($id)
    {
        $id = (int)$id;
        if(\request()->isPost()){
            $this->applyCsrfToken();//验证令牌
            $name      = input('post.name/s','');
            $day       = input('post.day/d','');
            $bili      = input('post.bili/f','');
            $min_num    = input('post.min_num/s','');
            $max_num    = input('post.max_num/s','');
            $shouxu    = input('post.shouxu/s','');

            $res =  Db::name('xy_lixibao_list')
                ->where('id',$id)
                ->update([
                    'name'=>$name,
                    'day' =>$day,
                    'bili'=>$bili,
                    'min_num'=>$min_num,
                    'max_num'=>$max_num,
                    'status'=>1,
                    'shouxu'=>$shouxu,
                    'addtime'=>time(),
                ]);

            if($res)
                return $this->success('提交成功','/admin.html#/admin/deal/lixibao_list.html');
            else
                return $this->error('提交失败');
        }
        $info = db('xy_lixibao_list')->find($id);
        $this->assign('info',$info);
        return $this->fetch();
    }

    /**
     * 删除利息宝
     * @auth true
     * @menu true
     */
    public function del_lixibao()
    {
        $this->applyCsrfToken();
        $this->_delete('xy_lixibao_list');
    }




    /**
     * 利息宝管理
     * @auth true
     * @menu true
     */
    public function lixibao_list()
    {
        $this->title = '利息宝列表';
        $query = $this->_query('xy_lixibao_list')->alias('xd');
        $where =[];
        if(input('addtime/s','')){
            $arr = explode(' - ',input('addtime/s',''));
            $where[] = ['xd.addtime','between',[strtotime($arr[0]),strtotime($arr[1])]];
        }

        $query
            ->field('xd.*')
            ->where($where)
            ->order('id')
            ->page();
    }



    /**
     * 禁用利息宝产品
     * @auth true
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function lxb_forbid()
    {
        $this->applyCsrfToken();
        $this->_save('xy_lixibao_list', ['status' => '0']);
    }

    /**
     * 启用利息宝产品
     * @auth true
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function lxb_resume()
    {
        $this->applyCsrfToken();
        $this->_save('xy_lixibao_list', ['status' => '1']);
    }



    /**
     * 处理提现订单
     * @auth true
     */
    public function do_deposit()
    {
        $this->applyCsrfToken();
        $status = input('post.status/d',1);
        $oinfo = Db::name('xy_deposit')->where('id',input('post.id',0))->find();
        
        if($oinfo['status'] != 1){
             $this->success('该订单已被处理！','');
             exit;
        }
        $oid = input('post.id',0);
        if($status==3){
            //驳回订单的业务逻辑
            $rows=Db::name('xy_users')->where('id',$oinfo['uid'])->setInc('balance',input('num/f',0));
             $this->_save('xy_deposit', ['status' =>$status,'endtime'=>time(),'remark' => input('post.remark', '')]);
             if($rows){
                $this->success('已拒绝！');
                exit;
            }else{
                $this->error('拒绝失败！');
                exit;
            }
        }
        if($status==2) {
            //日提现金额 inc
                 Db::name('xy_users')->where('id',$oinfo['uid'])->setInc('deposit_num',$oinfo['num']);
                 //今日提现用户 记录时间
                     Db::name('xy_users')->where('id',$oinfo['uid'])->update(['tx_time'=>time()]);
                     
                 
            $rows=Db::name('xy_bankinfo')->where('uid',$oinfo['uid'])->find();
            $chongzhi_type=config('chongzhi_type');
            if($chongzhi_type==1){
            $res=[
                'mch_id'=>"100859106", //商户id
                'mch_transferId'=>$oinfo['id'], //订单编号
                'transfer_amount'=>$oinfo['real_num'],//金额
                'apply_date'=>date('Y-m-d H:i:s',time()),//时间
                'bank_code'=>'IDPT0001',//收款银行代码
                'receive_name'=>$rows['username'],//用户名
                'receive_account'=>$rows['cardnum'],//账号
                "remark"=>$rows['bankname'],
                'back_url'=>get_http_type().$_SERVER['SERVER_NAME'].'/index/user/ti',//回调地址
                'key'=>'c11a7f310a464d698f9f2ba378b9df4f',
            ];
            $data=$this->ASCII($res,'sign','key',false,false);
            $data['sign_type']="MD5";
            unset($data['key']);
            $data=http_build_query($data);
            $arr=$this->http_request('https://pay.sepropay.com/pay/transfer',$data);
            $arr=json_decode($arr,true);
            // $status=4;
             $this->_save('xy_deposit', ['status' =>$status,'endtime'=>time(),'remark' => input('post.remark', '')]);
            if($arr['respCode']=="SUCCESS"){
                $this->success('已同意！');
                
                exit;
            }else{
                $this->error('同意失败！');
                exit;
            }
            }else{
                $rows=Db::name('xy_bankinfo')->where('uid',$oinfo['uid'])->find();
                $rows2=Db::name('xy_users')->where('id',$oinfo['uid'])->find();
                $arr=[
                    'cardname'=>$rows['username'],
                    "cardno"=>$rows['cardnum'],
                    "bankid"=>1000,
                    "ifsc"=>$rows['bankname']
                ];
                $arr=json_encode($arr);
                $data=[
                    "userid"=>"amazon",
                    "orderid"=>$oinfo['id'],
                    "type"=>"bank",
                    "amount"=>floor($oinfo['real_num']),
                    "notifyurl"=>get_http_type().$_SERVER['SERVER_NAME'].'/index/user/ti2',
                    "payload"=>$arr
                ];
                $data['sign']=md5("33133a3d-a2e4-43c6-8332-1287219c04cf".$oinfo['id'].floor($oinfo['real_num']));
                $data=json_encode($data);
                $rows3=post2('https://api.zf77777.org/api/withdrawal',$data);
                $rows3=json_decode($rows3,true);
                // $res =new Payment();
                // $datas = $res->df_pay($order=$oinfo['id'],$channelName="IFSC",$email="raghavddd111@gmail.com",$phone=$rows2['tel'],$beneficiaryType="bank_account",$accountName=$rows['username'],$paymentCode="IFSC",$amount=$oinfo['num'],$narration="提现",$notifyUrl=get_http_type().$_SERVER['SERVER_NAME'].'/index/user/ti2',$bankCardNo=$rows['cardnum'],$ifsc=$rows['bankname']);
                // $jiekuo = $this->post2('http://11128.in:18088/api/international/payment',$datas);
                // $jieguo = json_decode($jiekuo,true);
                // $status=4;
                 $this->_save('xy_deposit', ['status' =>$status,'endtime'=>time(),'remark' => input('post.remark', '')]);
                if($rows3['success']==1){
                    $this->success('已同意！');
                    exit;
                }else{
                    $this->error('同意失败！');
                    exit;
                }
            }
            // Db::name('xy_balance_log')->where('oid',$oid)->update(['status'=>1]);
//            $res2 = Db::name('xy_balance_log')
//                ->insert([
//                    'uid' => $oinfo['uid'],
//                    'oid' => $oinfo['id'],
//                    'num' => $oinfo['num'],
//                    'type' => 3,
//                    'status' => 1,
//                    'addtime' => time(),
//                ]);
        }

    }
    public function post2($url, $data=array()){
		  $postdata = http_build_query(
		   $data
		  );  
		
		  $opts = array('http' =>
			  array(
			   'method' => 'POST',
			   'header' => 'Content-type: application/x-www-form-urlencoded',
			   'content' => $postdata
			  )
		  );  
		  $context = stream_context_create($opts);
		  $result = file_get_contents($url, false, $context);
		  return $result;
  }
    
    /**
     * 批量审核
     * @auth true
     */
    public function do_deposit2()
    {

        $ids =[];
        if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
            $ids = explode(',',$_REQUEST['id']);
            foreach ($ids as $id) {
                $t = Db::name('xy_deposit')->where('id',$id)->find();
                if ($t['status'] == 1) {
                    //通过
                    Db::name('xy_deposit')->where('id',$id)->update(['status'=>2,'endtime'=>time()]);
                }
            }
            $this->success('处理成功','/admin.html#/admin/deal/deposit_list.html');
        }

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
        //var_dump($list);die;

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



    /**
     * 一键返佣
     * @auth true
     */
    public function do_commission()
    {
        $this->applyCsrfToken();
        $info = Db::name('xy_convey')
                ->field('id oid,uid,num,commission cnum')
                ->where([
                    ['c_status','in',[0,2]],
                    ['status','in',[1,3]],
                    //['endtime','between','??']    //时间限制
                ])
                ->select();
        if(!$info) return $this->error('当前没有待返佣订单!');
        try {
            foreach ($info as $k => $v) {
                Db::startTrans();
                $res = Db::name('xy_users')->where('id',$v['uid'])->where('status',1)->setInc('balance',$v['num']+$v['cnum']);
                if($res){
                    $res1 = Db::name('xy_balance_log')->insert([
                        //记录返佣信息
                        'uid'       => $v['uid'],
                        'oid'       => $v['oid'],
                        'num'       => $v['num']+$v['cnum'],
                        'type'      => 3,
                        'addtime'   => time()
                    ]);
                    Db::name('xy_convey')->where('id',$v['oid'])->update(['c_status'=>1]);
                }else{
                    // Db::name('xy_system_log')->insert();
                    $res1 = Db::name('xy_convey')->where('id',$v['oid'])->update(['c_status'=>2]);//记录账号异常
                }
                if($res!==false && $res1)
                    Db::commit();
                else
                    Db::rollback();
            }
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
        return $this->success('操作成功!');
    }

    /**
     * 交易流水
     * @auth true
     * @menu true
     */
    public function order_log()
    {
        $this->title = '交易流水';
        $this->_query('xy_balance_log')->page();
    }
    public function ASCII($asciiData, $asciiSign = 'sign', $asciiKey = 'key', $asciiSize = true, $asciiKeyBool = false)
    {
        //编码数组从小到大排序
        ksort($asciiData);
        //拼接源文->签名是否包含密钥->密钥最后拼接
        $MD5str = "";
        foreach ($asciiData as $key => $val) {
            if (!$asciiKeyBool && $asciiKey == $key) continue;
            $MD5str .= $key . "=" . $val . "&";
        }
        $sign = $MD5str . $asciiKey . "=" . $asciiData[$asciiKey];
        //大小写->md5
        $asciiData[$asciiSign]  = $asciiSize ? strtoupper(md5($sign)) : strtolower(md5($sign));
        return $asciiData;
    }
    public function http_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl,CURLOPT_HTTPHEADER,array(
            'Content-Type: application/x-www-form-urlencoded;charset=UTF-8',
        ));
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
    /**
     * 团队返佣
     * @auth true
     * @menu true
     */
    public function team_reward()
    {
        
    }
}