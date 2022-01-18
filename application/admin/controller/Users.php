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
 * 会员管理
 * Class Users
 * @package app\admin\controller
 */
class Users extends Controller
{

    /**
     * 指定当前数据表
     * @var string
     */
    protected $table = 'xy_users';

    /**
     * 会员列表
     * @auth true
     * @menu true
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function index()
    {
        $this->isjia = $isjia = input('jia/s','0');
        $this->title = '会员列表';

        $query = $this->_query($this->table)->alias('u');
        $where = [];
        if(input('tel/s',''))$where[] = ['u.tel','like','%' . input('tel/s','') . '%'];
        if(input('username/s',''))$where[] = ['u.username','like','%' . input('username/s','') . '%'];
        if($isjia !== '') {
            $where[] = ['u.is_jia','=',$isjia ];
        }
        if(input('addtime/s','')){
            $arr = explode(' - ',input('addtime/s',''));
            $where[] = ['u.addtime','between',[strtotime($arr[0]),strtotime($arr[1])]];
        }

        $user = session('admin_user');
        if($user['authorize']== 2 && !empty($user['nodes']) ){
            //获取直属下级
            $mobile = $user['phone'];
            $uid = db('xy_users')->where('tel', $mobile)->value('id');

            $ids1  = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');

            $ids1 ? $ids2  = db('xy_users')->where('parent_id','in', $ids1)->field('id')->column('id') : $ids2 = [];

            $ids2 ? $ids3  = db('xy_users')->where('parent_id','in', $ids2)->field('id')->column('id') : $ids3 = [];

            $ids3 ? $ids4  = db('xy_users')->where('parent_id','in', $ids3)->field('id')->column('id') : $ids4 = [];
            $uid = !$uid ? [] : [$uid];
            // array_push($idsAll, $ids1,$ids2 ,$ids3 ,$ids4);
            $idsAll = array_merge([],$uid,$ids1,$ids2 ,$ids3 ,$ids4);  //所有ids
            if(count($idsAll) > 0) $where[] = ['u.id','in',$idsAll];
        }

        $query->field('u.id,u.tel,u.username,u.lixibao_balance,u.id_status,u.ip,u.is_jia,u.addtime,u.invite_code,u.freeze_balance,u.status,u.balance,u.pipei_min, u.pipei_max,u1.username as parent_name,le.name as level_name')
            ->leftJoin('xy_users u1','u.parent_id=u1.id')
            ->leftJoin('xy_level le','u.level=le.level')
			->group('u.id')
            ->where($where)
            ->order('u.id desc')
            ->page();
    }

    protected function _index_page_filter(&$data)
    {
		foreach ( $data as &$v){
			$v['recharge_sum'] = Db::name('xy_recharge')->where(['uid'=>$v['id'], 'status'=>2])->sum('num');
			
			$v['deposit_sum'] = Db::name('xy_deposit')->where(['uid'=>$v['id'], 'status'=>2])->sum('num');			
		}
    }

    /**
     * 会员等级列表
     * @auth true
     * @menu true
     */
    public function level()
    {
        $this->title = '用户等级';
        $this->_query('xy_level')->page();
    }


    /**
     * 账变
     * @auth true
     */
    public function caiwu()
    {
        $uid = input('get.id/d',1);
        $this->uid = $uid;
        $this->uinfo = db('xy_users')->find($uid);
        //
        if ( isset($_REQUEST['iasjax']) ) {
            $page = input('get.page/d', 1);
            $num = input('get.num/d', 10);
            $level = input('get.level/d', 1);
            $limit = ((($page - 1) * $num) . ',' . $num);
            $where = [];
            if ($level == 1) {$where[] = ['uid', '=', $uid];}

            if(input('type/d',0))$where[] = ['type','=', input('type/d',0) ];
            if(input('addtime/s','')){
                $arr = explode(' - ',input('addtime/s',''));
                $where[] = ['addtime','between',[strtotime($arr[0]),strtotime($arr[1])]];
            }


            $count = $data = db('xy_balance_log')->where($where)->count('id');
            $data = db('xy_balance_log')
                ->where($where)
                ->order('id desc')
                ->limit($limit)
                ->select();

            if ($data) {
                foreach ($data as &$datum) {
                    $datum['tel'] = $this->uinfo['tel'];
                    $datum['addtime'] = date('Y/m/d H:i', $datum['addtime']);;
                    // 0系统 1充值 2交易 3返佣 4强制交易 5推广返佣 6下级交易返佣  7 利息宝
                    switch ($datum['type']){
                        case 0:
                            $text = '<span class="layui-btn layui-btn-sm layui-btn-danger">系统</span>';break;
                        case 1:
                            $text = '<span class="layui-btn layui-btn-sm layui-btn-warm">充值</span>';break;
                        case 2:
                            $text = '<span class="layui-btn layui-btn-sm layui-btn-danger">交易</span>';break;
                        case 3:
                            $text = '<span class="layui-btn layui-btn-sm layui-btn-normal">返佣</span>';break;
                        case 4:
                            $text = '<span class="layui-btn layui-btn-sm ">强制交易</span>';break;
                        case 5:
                            $text = '<span class="layui-btn layui-btn-sm layui-btn-danger">推广返佣</span>';break;
                        case 6:
                            $text = '<span class="layui-btn layui-btn-sm layui-btn-normal">下级交易返佣</span>';break;
                        case 7:
                            $text = '<span class="layui-btn layui-btn-sm layui-btn-danger">利息宝收益</span>';break;
                        default:
                            $text = '其他';
                    }

                    $datum['type'] = $text;
                    $datum['status'] = '正常';
                }
            }

            if (!$data) json(['code' => 1, 'info' => '暂无数据']);
            return json(['code' => 0, 'count' => $count, 'info' => '请求成功', 'data' => $data, 'other' => $limit]);
        }


        return $this->fetch();
    }
    /**
     * 添加会员
     * @auth true
     * @menu true
     */
    public function add_users()
    {
        if(request()->isPost()){
            $tel = input('post.tel/s','');
            $user_name = input('post.user_name/s','');
            $pwd = input('post.pwd/s','');
            $parent_id= input('post.parent_id/d',0);
            $token = input('__token__',1);
            $res = model('Users')->add_users($tel,$user_name,$pwd,$parent_id,$token);
            if($res['code']!==0){
                return $this->error($res['info']);
            }
            return $this->success($res['info']);
        }
        return $this->fetch();
    }
    
    public function randphone()
    {
        $tel_arr = array(
            '130','131','132','133','134','135','136','137','138','139','144','147','150','151','152','153','155','156','157','158','159','176','177','178','180','181','182','183','184','185','186','187','188','189',
        );
        return $tel_arr[array_rand($tel_arr)].mt_rand(1000,9999).mt_rand(1000,9999);
    }
    
    public function dd()
    {
        echo $this->randphone();
        exit;
    }

    /**
     * 批量设置假人密码
     * @auth true
     * @menu true
     */
    public function set_betch_jia_password()
    {
        if(request()->isPost()) {
            $pwd  = input('post.pwd/s', '');
             
            $num = input('post.num/s', '');
            $data['parent_id'] = input('post.parent_id/s', '');
            $data = [];
            $data['balance'] = input('post.balance/s', '0');
            $salt         = rand(0, 99999);  //生成盐
            $data['pwd']  = sha1($pwd . $salt . config('pwd_str'));
            $data['salt'] = $salt;
             
            
            
            //银行卡
             $yhk  = input('post.yhk/s', '');
            
            //名字
             $username  = input('post.username/s', '');
            //资金密码
             $zjpwd  = input('post.zjpwd/s', '');
              $data['salt2'] = $salt;
             
            $data['pwd2']  = sha1($zjpwd . $salt . config('pwd_str'));
            
            // if ($pwd2) {
            //     $salt2         = rand(0, 99999);  //生成盐
            //     $data['pwd2']  = sha1($pwd2 . $salt2 . config('pwd_str'));
            //     $data['salt2'] = $salt2;
            // }
            for($i=0;$i<$num;$i++){
                
                $data['tel'] = $this->randphone();
                $data['username'] = $data['tel'];
                 $data['is_jia'] = 1;
   
                 
                $data['invite_code'] = model('Users')->create_invite_code();
                $data['addtime']=time();
                
// ->data($data)
                
                $res = DB::name('xy_users')->insertGetId($data);
                
                 $data2 =array(
                'username' =>$username,
                'bankname' =>'no',
                'cardnum' =>$yhk,
                'status' =>1,
                'uid' =>$res
                 );
                //添加银行卡
                $res2 = db('xy_bankinfo')->insert($data2);
                
            }

            return $this->success('操作成功');
        }
        return $this->fetch();
    }

    /**
     * 编辑会员
     * @auth true
     * @menu true
     */
    public function edit_users()
    {
        $id = input('get.id',0);
        if(request()->isPost()){
            $id = input('post.id/d',0);
            $tel = input('post.tel/s','');
            $user_name = input('post.user_name/s','');
            $pwd = input('post.pwd/s','');
            $pwd2 = input('post.pwd2/s','');
            $parent_id = input('post.parent_id/d',0);
            $level = input('post.level/d',0);
            $balance = input('post.balance/f',0);
            
            $pipei_min = input('post.pipei_min/f',0);
            $pipei_max = input('post.pipei_max/f',0);
            
            $deal_status = input('post.deal_status/d',1);
            $freeze_balance = input('post.freeze_balance/f',0);
            $token = input('__token__');
            if($balance<0) $this->error('余额不能低于0');
            $res = model('Users')->edit_users($id,$tel,$user_name,$pwd,$parent_id,$balance,$pipei_min, $pipei_max,$freeze_balance,$token,$pwd2);
            $res2 = Db::table($this->table)->where('id',$id)->update(['deal_status'=>$deal_status,'level'=>$level]);

            if($res['code']!==0){
                if($res2) {
                    return $this->success('编辑成功!');
                }else{
                    return $this->error($res['info']);
                }
            }
            return $this->success($res['info']);
        }
        if(!$id) $this->error('参数错误');
        $this->info = Db::table($this->table)->find($id);
        $this->level = Db::table('xy_level')->select();
        return $this->fetch();
    }


    public function delete_user()
    {
        $this->applyCsrfToken();
        $id = input('post.id/d',0);
        $res = Db::table('xy_users')->where('id',$id)->delete();
        if($res)
            $this->success('删除成功!');
        else
            $this->error('删除失败!');
    }

    /**
     * 编辑会员_暗扣
     * @auth true
     * @menu true
     */
    public function edit_users_ankou()
    {
        $id = input('get.id',0);
        if(request()->isPost()){
            $id = input('post.id/d',0);
//            $show_td = input('post.show_td/d',0);  //显示我的团队
//            $show_cz = input('post.show_cz/d',0);  //显示充值
//            $show_tx = input('post.show_tx/d',0);  //显示提现
//            $show_num = input('post.show_num/d',0);  //显示推荐人数
//            $show_tel = input('post.show_tel/d',0);  //显示电话
//            $show_tel2 = input('post.show_tel2/d',0);  //显示电话隐藏
            $kouchu_balance_uid = input('post.kouchu_balance_uid/d',0); //扣除人
            $kouchu_balance =  input('post.kouchu_balance/f',0); //扣除金额


            $show_td = ( isset($_REQUEST['show_td']) && $_REQUEST['show_td'] == 'on' ) ?  1 : 0;//显示我的团队
            $show_cz = ( isset($_REQUEST['show_cz']) && $_REQUEST['show_cz'] == 'on' ) ?  1 : 0;//显示充值
            $show_tx = ( isset($_REQUEST['show_tx']) && $_REQUEST['show_tx'] == 'on' ) ?  1 : 0;//显示提现
            $show_num = ( isset($_REQUEST['show_num']) && $_REQUEST['show_num'] == 'on' ) ?  1 : 0;//显示推荐人数
            $show_tel = ( isset($_REQUEST['show_tel']) && $_REQUEST['show_tel'] == 'on' ) ?  1 : 0;//显示电话
            $show_tel2 = ( isset($_REQUEST['show_tel2']) && $_REQUEST['show_tel2'] == 'on' ) ?  1 : 0;//显示电话隐藏


            $token = input('__token__');
            $data = [
                '__token__'         => $token,
                'show_td'               => $show_td,
                'show_cz'               => $show_cz,
                'show_tx'               => $show_tx,
                'show_num'               => $show_num,
                'show_tel'               => $show_tel,
                'show_tel2'               => $show_tel2,
                'kouchu_balance_uid'           => $kouchu_balance_uid,
                'kouchu_balance'               => $kouchu_balance,
            ];

            //var_dump($data,$_REQUEST);die;
            unset($data['__token__']);
            $res = Db::table($this->table)->where('id',$id)->update($data);
            if(!$res){
                return $this->error('编辑失败!');
            }
            return $this->success('编辑成功!');
        }

        if(!$id) $this->error('参数错误');
        $this->info = Db::table($this->table)->find($id);

        //
        $uid = $id;
        $data = db('xy_users')->where('parent_id', $uid)
            ->field('id,username,headpic,addtime,childs,tel')
            ->order('addtime desc')
            ->select();

        foreach ($data as &$datum) {
            //充值
            $datum['chongzhi'] = db('xy_recharge')->where('uid', $datum['id'])->where('status', 2)->sum('num');
            //提现
            $datum['tixian'] = db('xy_deposit')->where('uid', $datum['id'])->where('status', 1)->sum('num');
        }

        //var_dump($data,$uid);die;

        //$this->cate = db('xy_goods_cate')->order('addtime asc')->select();
        $this->assign('cate',$data);

        return $this->fetch();
    }

    /**
     * 编辑会员登录状态
     * @auth true
     */
    public function edit_users_status()
    {
        $id = input('id/d',0);
        $status = input('status/d',0);
        if(!$id || !$status) return $this->error('参数错误');
        $res = model('Users')->edit_users_status($id,$status);
        if($res['code']!==0){
            return $this->error($res['info']);
        }
        return $this->success($res['info']);
    }

    /**
     * 编辑银行卡信息
     * @auth true
     * @menu true
     */
    public function edit_users_address()
    {
        if(request()->isPost()){
            $this->applyCsrfToken();
            $id = input('post.id/d',0);
            $tel = input('post.tel/s','');
            $name = input('post.name/s','');
            $address = input('post.address/s','');

            $res = db('xy_member_address')->where('id',$id)->update(
                ['tel'=>$tel,
                    'name'=>$name,
                    'address'=>$address
                ]);
            if($res!==false){
                return $this->success('操作成功');
            }else{
                return $this->error('操作失败');
            }
        }

        //$data = db('xy_member_address')->where('uid',$id)->select();
        $uid = input('id/d',0);
        $this->bk_info = Db::name('xy_member_address')->where('uid',input('id/d',0))->select();
        if(!$this->bk_info) {
            //$this->error('没有数据');
            $data = [
                'uid'       => input('id/d',0),
                'name'      => '',
                'tel'       => '',
                'area'      => '',
                'address'   => '',
                'is_default'=> 1,
                'addtime'   => time()
            ];
            $tmp = db('xy_member_address')->where('uid',$uid)->find();
            if(!$tmp) $data['is_default']=1;
            $res = db('xy_member_address')->insert($data);

            $this->bk_info = Db::name('xy_member_address')->where('uid',input('id/d',0))->select();

        }
        return $this->fetch();
    }

    /**
     * 编辑会员登录状态
     * @auth true
     */
    public function edit_users_status2()
    {
        $id = input('id/d',0);
        $status = input('status/d',0);
        if(!$id || !$status) return $this->error('参数错误');
        $status == -1 ? $status = 0:'';

        $res = Db::table($this->table)->where('id',$id)->update(['is_jia'=>$status]);

        if(!$res){
            echo '<pre>';
            var_dump($res,$status,$_REQUEST);
            return $this->error('更新失败!');
        }
        return $this->success('更新成功');
    }

    /**
     * 编辑会员二维码
     * @auth true
     */
    public function edit_users_ewm()
    {
        $id = input('id/d',0);
        $invite_code = input('status/s','');
        if(!$id || !$invite_code) return $this->error('参数错误');

        $n = ($id%20);

        $dir = './upload/qrcode/user/'.$n . '/' . $id . '.png';
        if(file_exists($dir)) {
            unlink($dir);
        }

        $res = model('Users')->create_qrcode($invite_code,$id);
        if(0 && $res['code']!==0){
            return $this->error('失败');
        }
        return $this->success('成功');
    }


    /**
     * 查看团队
     * @auth true
     */
    public function tuandui()
    {

        $uid = input('get.id/d',1);


        if ( isset($_REQUEST['iasjax']) ) {
            $page = input('get.page/d',1);
            $num = input('get.num/d',10);
            $level = input('get.level/d',1);
            
            $is_order = input('get.is_order/d',0);
            
            $limit = ( (($page - 1) * $num) . ',' . $num );


            $where = [];
            if ($level == -1){
                $uids = model('Users')->child_user($uid,5);
                $uids ? $where[] = ['u.id','in',$uids] : $where[] = ['u.id','in',[-1]];
            } else if ($level ==1) {
                $uids1 = model('Users')->child_user($uid,1,0);
                $uids1 ? $where[] = ['u.id','in',$uids1] : $where[] = ['u.id','in',[-1]];
            }else if ($level == 2) {
                $uids2 = model('Users')->child_user($uid,2,0);
                $uids2 ? $where[] = ['u.id','in',$uids2] : $where[] = ['u.id','in',[-1]];
            }else if ($level == 3) {
                $uids3 = model('Users')->child_user($uid,3,0);
                $uids3 ? $where[] = ['u.id','in',$uids3] : $where[] = ['u.id','in',[-1]];
            }else if ($level == 4) {
                $uids4 = model('Users')->child_user($uid,4,0);
                $uids4 ? $where[] = ['u.id','in',$uids4] : $where[] = ['u.id','in',[-1]];
            }else if ($level == 5) {
                $uids5 = model('Users')->child_user($uid,5,0);
                $uids5 ? $where[] = ['u.id','in',$uids5] : $where[] = ['u.id','in',[-1]];
            }else if ($level == 6) {//今日首充用户
            
               $uids = model('Users')->child_user($uid,5);
                $uids ? $where[] = ['u.id','in',$uids] : $where[] = ['u.id','in',[-1]];
               $start_time=strtotime(date('y-m-d'));
               $end_time=strtotime(date("Y-m-d",strtotime("+1 day")));
                     $where[] = ['u.s_cz_time','between',[$start_time,$end_time]];
                    //  dump($where);
                
            }else if ($level == 7) {//历史充值用户
               $uids = model('Users')->child_user($uid,5);
                $uids ? $where[] = ['u.id','in',$uids] : $where[] = ['u.id','in',[-1]];
                     $where[] = ['u.cz_time','>',0];
            }else if ($level == 8) {//今日提现用户
               $uids = model('Users')->child_user($uid,5);
                $uids ? $where[] = ['u.id','in',$uids] : $where[] = ['u.id','in',[-1]];
               $start_time=strtotime(date('y-m-d'));
               $end_time=strtotime(date("Y-m-d",strtotime("+1 day")));
                     $where[] = ['u.tx_time','between',[$start_time,$end_time]];
            }else if ($level == 9) {//今日充值用户
               $uids = model('Users')->child_user($uid,5);
                $uids ? $where[] = ['u.id','in',$uids] : $where[] = ['u.id','in',[-1]];
               $start_time=strtotime(date('y-m-d'));
               $end_time=strtotime(date("Y-m-d",strtotime("+1 day")));
                     $where[] = ['u.cz_time','between',[$start_time,$end_time]];
            }

            if(input('tel/s',''))$where[] = ['u.tel','like','%' . input('tel/s','') . '%'];
            if(input('username/s',''))$where[] = ['u.username','like','%' . input('username/s','') . '%'];
            if(input('addtime/s','')){
                $arr = explode(' - ',input('addtime/s',''));
                $where[] = ['u.addtime','between',[strtotime($arr[0]),strtotime($arr[1])]];
            }
            if($is_order!=0){
                if($is_order==1){
                    $where[] = ['u.is_order_num','=',0];
                }elseif($is_order==-1){
                    $where[] = ['u.is_order_num','>',0];
                }
                
            }

            $count = $data = db('xy_users')->alias('u')->where($where)->count('id');
            $query = db('xy_users')->alias('u');
            $data = $query->field('u.id,u.tel,u.username,u.id_status,u.childs,u.ip,u.is_jia,u.addtime,u.invite_code,u.freeze_balance,u.status,u.balance,u1.username as parent_name')
                ->leftJoin('xy_users u1','u.parent_id=u1.id')
                ->where($where)
                ->order('u.id desc')
                ->limit($limit)
                ->select();
            if ($data) {
                //
                $uid1s = model('Users')->child_user($uid,1,0);
                $uid2s = model('Users')->child_user($uid,2,0);
                $uid3s = model('Users')->child_user($uid,3,0);
                $uid4s = model('Users')->child_user($uid,4,0);
                $uid5s = model('Users')->child_user($uid,5,0);

                foreach ($data as &$datum) {
                    //佣金
                    $datum['yj'] = db('xy_convey')->where('status',1)->where('uid',$datum['id'])->sum('num');
                    $datum['cz'] = db('xy_recharge')->where('status',2)->where('uid',$datum['id'])->sum('num');
                    $datum['tx'] = db('xy_deposit')->where('status',2)->where('uid',$datum['id'])->sum('num');
                    $datum['addtime'] = date('Y/m/d H:i', $datum['addtime']);;
                    $datum['jb'] = '三级';
                    $color = '#92c7ef';


                    if (in_array($datum['id'],$uid1s)  ){
                        $datum['jb'] = '一级';
                        $color = '#1E9FFF';
                    }
                    if (in_array($datum['id'],$uid2s)  ){
                        $datum['jb'] = '二级';
                        $color = '#2b9aec';
                    }
                    if (in_array($datum['id'],$uid3s)  ){
                        $datum['jb'] = '三级';
                        $color = '#1E9FFF';
                    }
                    if (in_array($datum['id'],$uid4s)  ){
                        $datum['jb'] = '四级';
                        $color = '#76c0f7';
                    }
                    if (in_array($datum['id'],$uid5s)  ){
                        $datum['jb'] = '五级';
                        $color = '#92c7ef';
                    }

                    $datum['jb'] = '<span class="layui-btn layui-btn-xs layui-btn-danger" style="background: '.$color.'">'.$datum['jb'].'</span>';
                }
            }
            //var_dump($page,$limit);die;

            if(!$data) json(['code'=>1,'info'=>'暂无数据']);
            return json(['code'=>0,'count'=>$count,'info'=>'请求成功','data'=>$data,'other'=>$limit]);
        }else{
            //
            $this->uid = $uid;
            $this->uinfo = db('xy_users')->find($uid);
            
            // 查询 
            //团队总人数
          
          
           $uids = model('Users')->child_user($uid,5);
           $this->user_num=count($uids);
            //团队总余额
            $this->user_price =db('xy_users')->whereIn('id',$uids)->sum('balance');
            //团队总充值
            $this->user_cz= db('xy_recharge')->where('status',2)->wherein('uid',$uids)->sum('num');
            //团队总提现
           $this->user_tx = db('xy_deposit')->where('status',2)->wherein('uid',$uids)->sum('num');
            
            
            
            

        }



        return $this->fetch();
    }
    /**
     * 封禁/解封会员
     * @auth true
     */
    public function open()
    {
        $uid = input('post.id/d',0);
        $status = input('post.status/d',0);
        $type = input('post.type/d',0);
        $info=[];
        if ($uid) {
            if (!$type) {
                $status2 = $status ? 0 : 1;
                $res = db('xy_users')->where('id',$uid)->update(['status'=>$status2]);
                return json(['code'=>1,'info'=>'请求成功','data'=>$info]);
            }else{
                //

                $wher  =[] ;
                $wher2 =[] ;


                $ids1 = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');
                $ids1 ? $wher[] = ['parent_id','in',$ids1] : '';

                $ids2 = db('xy_users')->where($wher)->field('id')->column('id');
                $ids2 ? $wher2[] = ['parent_id','in',$ids2] :'';

                $ids3 = db('xy_users')->where($wher2)->field('id')->column('id');

                $idsAll = array_merge([$uid],$ids1,$ids2 ,$ids3);  //所有ids
                $idsAll = array_filter($idsAll);

                $wherAll[]= ['id','in',$idsAll];
                $users = db('xy_users')->where($wherAll)->field('id')->select();

                //var_dump($users);die;
                $status2 = $status ? 0 : 1;
                foreach ($users as $item) {
                    $res = db('xy_users')->where('id',$item['id'])->update(['status'=>$status2]);
                }

                return json(['code'=>1,'info'=>'请求成功','data'=>$info]);
            }


        }

        return json(['code'=>1,'info'=>'暂无数据']);
    }


    //查看图片
    public function picinfo(){
        $this->pic = input('get.pic/s','');
        if(!$this->pic)return;
        $this->fetch();
    }

    /**
     * 客服管理
     * @auth true
     * @menu true
     */
    public function cs_list()
    {
        $this->title = '客服列表';
        $where = [];
        if(input('tel/s',''))$where[] = ['tel','like','%' . input('tel/s','') . '%'];
        if(input('username/s',''))$where[] = ['username','like','%' . input('username/s','') . '%'];
        if(input('addtime/s','')){
            $arr = explode(' - ',input('addtime/s',''));
            $where[] = ['addtime','between',[strtotime($arr[0]),strtotime($arr[1])]];
        }
        $this->_query('xy_cs')
            ->where($where)
            ->page();
    }

    /**
     * 添加客服
     * @auth true
     * @menu true
     */
    public function add_cs()
    {
        if(request()->isPost()){
            $this->applyCsrfToken();
            $username = input('post.username/s','');
            $tel = input('post.tel/s','');
            $pwd = input('post.pwd/s','');
            $qq = input('post.qq/d',0);
            $wechat = input('post.wechat/s','');
            $qr_code = input('post.qr_code/s','');
            $time = input('post.time');
            $arr = explode('-', $time);
            $btime = substr($arr[0],0,5);
            $etime = substr($arr[1],1,5);
            $data = [
                'username'  => $username,
                'tel'       => $tel,
                'pwd'       => $pwd,    //需求不明确，暂时以明文存储密码数据
                'qq'        => $qq,
                'wechat'    => $wechat,
                'qr_code'   => $qr_code,
                'btime'     => $btime,
                'etime'     => $etime,
                'addtime'   => time(),
            ];
            $res = db('xy_cs')->insert($data);
            if($res) return $this->success('添加成功');
            return $this->error('添加失败，请刷新再试');
        }
        return $this->fetch();
    }

    /**
     * 客服登录状态
     * @auth true
     */
    public function edit_cs_status()
    {
        $this->applyCsrfToken();
        $this->_save('xy_cs', ['status' => input('post.status/d',1)]);
    }

    /**
     * 编辑客服信息
     * @auth true
     * @menu true
     */
    public function edit_cs()
    {
        if(request()->isPost()){
            $this->applyCsrfToken();
            $id = input('post.id/d',0);
            $username = input('post.username/s','');
            $tel = input('post.tel/s','');
            $pwd = input('post.pwd/s','');
            $qq = input('post.qq/d',0);
            $wechat = input('post.wechat/s','');
            $url = input('post.url/s','');
            $qr_code = input('post.qr_code/s','');
            $time = input('post.time');
            $arr = explode('-', $time);
            $btime = substr($arr[0],0,5);
            $etime = substr($arr[1],1,5);
            $data = [
                'username'  => $username,
                'tel'       => $tel,
                'qq'        => $qq,
                'wechat'    => $wechat,
                'url'    => $url,
                'qr_code'   => $qr_code,
                'btime'     => $btime,
                'etime'     => $etime,
            ];
            if($pwd) $data['pwd'] = $pwd;
            $res = db('xy_cs')->where('id',$id)->update($data);
            if($res!==false) return $this->success('编辑成功');
            return $this->error('编辑失败，请刷新再试');
        }
        $id = input('id/d',0);
        $this->list = db('xy_cs')->find($id);
        return $this->fetch();
    }

    /**
     * 客服调用代码
     * @auth true
     * @menu true
     */
    public function cs_code()
    {
        if(request()->isPost()){
            $this->applyCsrfToken();
            $code = input('post.code');
            $res = db('xy_script')->where('id',1)->update(['script'=>$code]);
            if($res!==false){
                $this->success('操作成功!');
            }
            $this->error('操作失败!');
        }
        $this->code = db('xy_script')->where('id',1)->value('script');
        return $this->fetch();
    }

    /**
     * 编辑银行卡信息
     * @auth true
     * @menu true
     */
    public function edit_users_bk()
    {
        if(request()->isPost()){
            $this->applyCsrfToken();
            $id = input('post.id/d',0);
            $tel = input('post.tel/s','');
            $site = input('post.site/s','');
            $cardnum = input('post.cardnum/s','');
            $bankname = input('post.bankname/s','');
            $username = input('post.username/s','');
            $zhifunum = input('post.zhifunum/s','');
            $res = db('xy_bankinfo')->where('id',$id)->update(['tel'=>$tel,'site'=>$site,'cardnum'=>$cardnum,'username'=>$username,'bankname'=>$bankname,'zhifunum'=>$zhifunum]);
            if($res!==false){
                return $this->success('操作成功');
            }else{
                return $this->error('操作失败');
            }
        }
        $this->bk_info = Db::name('xy_bankinfo')->where('uid',input('id/d',0))->select();
        if(!$this->bk_info) $this->error('没有数据');
        return $this->fetch();
    }




    /**
     * 编辑会员等级
     * @auth true
     * @menu true
     */
    public function edit_users_level()
    {
        if(request()->isPost()){
            $this->applyCsrfToken();
            $id    = input('post.id/d',0);
            $name  = input('post.name/s','');
            $level = input('post.level/d',0);
            $num   = input('post.num/s','');
            $order_num   = input('post.order_num/s','');
            $bili   = input('post.bili/s','');
            $tixian_ci   = input('post.tixian_ci/s','');
            $deal_min_balance   = input('post.deal_min_balance/s','');
            $tixian_min   = input('post.tixian_min/s','');
            $tixian_max   = input('post.tixian_max/s','');
            $validity   = input('post.validity/s','');
            $auto_vip_xu_num   = input('post.auto_vip_xu_num/s','');
            $num_min   = input('post.num_min/s','');
            $tixian_nim_order   = input('post.tixian_nim_order/d',0);
            $tixian_shouxu   = input('post.tixian_shouxu/f',0);
            $pic   = input('post.pic/s','');
            $pic2   = input('post.pic2/s','');


            $cate = Db::name('xy_goods_cate')->select();

            $cids = [];
            foreach ($cate as $item) {
                $k = 'cids'.$item['id'];
                if (isset($_REQUEST[$k]) && $_REQUEST[$k]=='on') {
                    $cids[]= $item['id'];
                }
            }

            $cidsstr = implode(',',$cids);
            //var_dump($cidsstr);die;

            $res = db('xy_level')->where('id',$id)->update(
                [
                    'name' => $name,
                    'level'=> $level,
                    'num'  => $num,
                    'order_num'=>$order_num,
                    'bili'=>$bili,
                    'tixian_ci'=>$tixian_ci,
                    'deal_min_balance'=>$deal_min_balance,
                    'tixian_min'=>$tixian_min,
                    'tixian_max'=>$tixian_max,
                    'validity'=>$validity,
                    'num_min'=>$num_min,
                    'cids' => $cidsstr,
                    'tixian_nim_order' => $tixian_nim_order,
                    'auto_vip_xu_num' => $auto_vip_xu_num,
                    'tixian_shouxu' => $tixian_shouxu,
                    'pic'=>$pic,
                    'pic2'=>$pic2
                ]);
            if($res!==false){
                return $this->success('操作成功');
            }else{
                return $this->error('操作失败');
            }
        }
        $this->bk_info = Db::name('xy_level')->where('id',input('id/d',0))->select();
        $this->cate = Db::name('xy_goods_cate')->select();
        if(!$this->bk_info) $this->error('没有数据');
        return $this->fetch();
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


        $list = Db::name('xy_users u')->field('u.id,u.tel,u.username,u.lixibao_balance,u.id_status,u.ip,u.is_jia,u.addtime,u.invite_code,u.freeze_balance,u.status,u.balance,u1.username as parent_name')
            ->leftJoin('xy_users u1','u.parent_id=u1.id')
            ->where($map)
            ->order('u.id desc')
            ->select();

        //$list = $list[0];


        //echo '<pre>';
        //var_dump($list);die;

        foreach( $list as $k=>&$_list ) {
            //var_dump($_list);die;
            $_list['addtime'] ? $_list['addtime'] = date('m/d H:i', $_list['addtime']) : '';
        }




        //echo '<pre>';
        //var_dump($list);die;

        //3.实例化PHPExcel类
        $objPHPExcel = new PHPExcel();
        //4.激活当前的sheet表
        $objPHPExcel->setActiveSheetIndex(0);
        //5.设置表格头（即excel表格的第一行）
        //$objPHPExcel
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'ID');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', '账号');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', '用户名');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', '账号余额');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', '冻结金额');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', '利息宝余额');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', '上级用户');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', '邀请码');
        $objPHPExcel->getActiveSheet()->setCellValue('I1', '注册时间');
        $objPHPExcel->getActiveSheet()->setCellValue('J1', '最后登录IP');

        //设置A列水平居中
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('A')->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //设置单元格宽度
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(10);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(30);


        //6.循环刚取出来的数组，将数据逐一添加到excel表格。
        for($i=0;$i<count($list);$i++){
            $objPHPExcel->getActiveSheet()->setCellValue('A'.($i+2),$list[$i]['id']);//ID
            $objPHPExcel->getActiveSheet()->setCellValue('B'.($i+2),$list[$i]['tel']);//标签码
            $objPHPExcel->getActiveSheet()->setCellValue('C'.($i+2),$list[$i]['username']);//防伪码
            $objPHPExcel->getActiveSheet()->setCellValue('D'.($i+2),$list[$i]['balance']);//防伪码
            $objPHPExcel->getActiveSheet()->setCellValue('E'.($i+2),$list[$i]['freeze_balance']);//防伪码
            $objPHPExcel->getActiveSheet()->setCellValue('F'.($i+2),$list[$i]['lixibao_balance']);//防伪码
            $objPHPExcel->getActiveSheet()->setCellValue('G'.($i+2),$list[$i]['parent_name']);//防伪码
            $objPHPExcel->getActiveSheet()->setCellValue('H'.($i+2),$list[$i]['invite_code']);//防伪码
            $objPHPExcel->getActiveSheet()->setCellValue('I'.($i+2),$list[$i]['addtime']);//防伪码
            $objPHPExcel->getActiveSheet()->setCellValue('J'.($i+2),$list[$i]['ip']);//防伪码
        }

        //7.设置保存的Excel表格名称
        $filename = 'user'.date('ymd',time()).'.xls';
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















}