<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Db;

class Ctrl extends Base
{
      //钱包页面
    public function wallet()
    {
        $balance = db('xy_users')->where('id',session('user_id'))->value('balance');
        $this->assign('balance',$balance);
        $balanceT = db('xy_convey')->where('uid',session('user_id'))->where('status',1)->sum('commission');
        $this->assign('balance_shouru',$balanceT);

        //收益
        $startDay = strtotime( date('Y-m-d 00:00:00', time()) );
        $shouyi = db('xy_convey')->where('uid',session('user_id'))->where('addtime','>',$startDay)->where('status',1)->select();

        //充值
        $chongzhi = db('xy_recharge')->where('uid',session('user_id'))->where('addtime','>',$startDay)->where('status',2)->select();

        //提现
        $tixian = db('xy_deposit')->where('uid',session('user_id'))->where('addtime','>',$startDay)->where('status',1)->select();

        $this->assign('shouyi',$shouyi);
        $this->assign('chongzhi',$chongzhi);
        $this->assign('tixian',$tixian);
        return $this->fetch();
    }


    public function recharge_before()
    {
        $pay = db('xy_pay')->where('status',1)->select();

        $this->assign('pay',$pay);
        return $this->fetch();
    }


    public function vip()
    {
        $pay = db('xy_pay')->where('status',1)->select();
        $this->member_level = db('xy_level')->order('level asc')->select();;
        $this->info = db('xy_users')->where('id', session('user_id'))->find();
        $this->member = $this->info;

        //var_dump($this->info['level']);die;

        $level_name = $this->member_level[0]['name'];
        $order_num = $this->member_level[0]['order_num'];
        if (!empty($this->info['level'])){
            $level_name = db('xy_level')->where('level',$this->info['level'])->value('name');;
        }
        if (!empty($this->info['level'])){
            $order_num = db('xy_level')->where('level',$this->info['level'])->value('order_num');;
        }

        $this->level_name = $level_name;
        $this->order_num = $order_num;
        $this->list = $pay;
        return $this->fetch();
    }

    /**
     * @地址      recharge_dovip
     * @说明      利息宝
     * @参数       @参数 @参数
     * @返回      \think\response\Json
     */
    public function lixibao()
    {
        $this->assign('title','利息宝');
        $uinfo = db('xy_users')->field('username,tel,level,id,headpic,balance,freeze_balance,lixibao_balance,lixibao_dj_balance')->find(session('user_id'));

        $this->assign('ubalance',$uinfo['balance']);
        $this->assign('balance',$uinfo['lixibao_balance']);
        $this->assign('balance_total',$uinfo['lixibao_balance'] + $uinfo['lixibao_dj_balance']);
        $balanceT = db('xy_lixibao')->where('uid',session('user_id'))->where('status',1)->where('type',3)->sum('num');

        $balanceT = db('xy_balance_log')->where('uid',session('user_id'))->where('status',1)->where('type',23)->sum('num');

        $yes1 = strtotime( date("Y-m-d 00:00:00",strtotime("-1 day")) );
        $yes2 = strtotime( date("Y-m-d 23:59:59",strtotime("-1 day")) );
        $this->yes_shouyi = db('xy_balance_log')->where('uid',session('user_id'))->where('status',1)->where('type',23)->where('addtime','between',[$yes1,$yes2])->sum('num');

        $this->assign('balance_shouru',$balanceT);


        //收益
        $startDay = strtotime( date('Y-m-d 00:00:00', time()) );
        $shouyi = db('xy_lixibao')->where('uid',session('user_id'))->select();

        foreach ($shouyi as &$item) {
            $type = '';
            if ($item['type'] == 1) {
                $type = '<font color="green">转入利息宝</font>';
            }elseif ($item['type'] == 2) {
                $n = $item['status'] ? '已到账' : '未到账';
                $type = '<font color="red" >利息宝转出('.$n.')</font>';
            }elseif ($item['type'] == 3) {
                $type = '<font color="orange" >每日收益</font>';
            }else{

            }

            $lixbao = Db::name('xy_lixibao_list')->find($item['sid']);

            $name = $lixbao['name'].'('.$lixbao['day'].'天)'.$lixbao['bili']*100 .'% ';

            $item['num'] = number_format($item['num'],2);
            $item['name'] = $type.'　　'.$name;
            $item['shouxu'] = $lixbao['shouxu']*100 .'%';
            $item['addtime'] = date('Y/m/d H:i', $item['addtime']);

            if ($item['is_sy'] == 1) {
                $notice = '正常收益,实际收益'.$item['real_num'];
            }else if ($item['is_sy'] == -1) {
                $notice = '未到期提前提取,未收益,手续费为:'.$item['shouxu'];
            }else{
                $notice = '理财中...';
            }
            $item['notice'] =$notice;
        }

        $this->rililv = config('lxb_bili')*100 .'%' ;
        $this->shouyi=$shouyi;
        if(request()->isPost()) {
            return json(['code'=>0,'info'=>'操作','data'=>$shouyi]);
        }

        $lixibao = Db::name('xy_lixibao_list')->field('id,name,bili,day,min_num')->order('day asc')->select();
        $this->lixibao = $lixibao;
        return $this->fetch();
    }

    public function lixibao_ru()
    {
        $uid = session('user_id');
        $uinfo = Db::name('xy_users')->field('recharge_num,deal_time,balance,level')->find($uid);//获取用户今日已充值金额

        if(request()->isPost()){
            $price = input('post.price/d',0);
            $id = input('post.lcid/d',0);
            $yuji=0;
            if ($id) {
                $lixibao = Db::name('xy_lixibao_list')->find($id);
                if ($price < $lixibao['min_num']) {
                    return json(['code'=>1,'info'=>'该产品最低起投金额'.$lixibao['min_num']]);
                }
                if ($price > $lixibao['max_num']) {
                    return json(['code'=>1,'info'=>'该产品最高可投金额'.$lixibao['max_num']]);
                }
                $yuji = $price * $lixibao['bili'] * $lixibao['day'];
            }else{
                return json(['code'=>1,'info'=>'数据异常']);
            }


            if ( $price <= 0 ) {
                return json(['code'=>1,'info'=>'you are sb']); //直接充值漏洞
            }
            if ($uinfo['balance'] < $price ) {
                return json(['code'=>1,'info'=>'可用余额不足']);
            }
            Db::name('xy_users')->where('id',$uid)->setInc('lixibao_balance',$price);  //利息宝月 +
            Db::name('xy_users')->where('id',$uid)->setDec('balance',$price);  //余额 -

            $endtime = time() + $lixibao['day'] * 24 * 60 * 60;

            $res = Db::name('xy_lixibao')->insert([
                'uid'         => $uid,
                'num'         => $price,
                'addtime'     => time(),
                'endtime'     => $endtime,
                'sid'         => $id,
                'yuji_num'         => $yuji,
                'type'        => 1,
                'status'      => 0,
            ]);
            $oid = Db::name('xy_lixibao')->getLastInsID();
            $res1 = Db::name('xy_balance_log')->insert([
                //记录返佣信息
                'uid'       => $uid,
                'oid'       => $oid,
                'num'       => $price,
                'type'      => 21,
                'addtime'   => time()
            ]);
            if($res) {
                return json(['code'=>0,'info'=>'操作成功']);
            }else{
                return json(['code'=>1,'info'=>'操作失败!请检查账号余额']);
            }
        }

        $this->rililv = config('lxb_bili')*100 .'%' ;
        $this->yue = $uinfo['balance'];
        $isajax = input('get.isajax/d',0);
        
        if ($isajax) {
            $lixibao = Db::name('xy_lixibao_list')->field('id,name,bili,day,min_num')->select();
            $data2=[];
            $str = $lixibao[0]['name'].'('.$lixibao[0]['day'].'天)'.$lixibao[0]['bili']*100 .'% ('.$lixibao[0]['min_num'].'起投)';
            foreach ($lixibao as $item) {
                $data2[] = array(
                    'id'=>$item['id'],
                    'value'=>$item['name'].'('.$item['day'].'天)'.$item['bili']*100 .'% ('.$item['min_num'].'起投)',
                );
            }
            return json(['code'=>0,'info'=>'操作','data'=>$data2,'data0'=>$str]);
        }

        $this->libi =1;

        $this->assign('title','利息宝余额转入');
        return $this->fetch();
    }


    public function deposityj()
    {
        $num = input('post.price/f',0);
        $id = input('post.lcid/d',0);
        if ($id) {
            $lixibao = Db::name('xy_lixibao_list')->find($id);

            $res = $num * $lixibao['day'] * $lixibao['bili'];
            return json(['code'=>0,'info'=>'操作','data'=>$res]);
        }
    }

    public function lixibao_chu()
    {
        $uid = session('user_id');
        $uinfo = Db::name('xy_users')->field('recharge_num,deal_time,balance,level,lixibao_balance')->find($uid);//获取用户今日已充值金额

        if(request()->isPost()){
            $id = input('post.id/d',0);
            $lixibao = Db::name('xy_lixibao')->find($id);
            if (!$lixibao) {
                return json(['code'=>1,'info'=>'数据异常']);
            }
            if ($lixibao['is_qu']) {
                return json(['code'=>1,'info'=>'重复操作']);
            }
            $price = $lixibao['num'];

            if ($uinfo['lixibao_balance'] < $price ) {
                return json(['code'=>1,'info'=>'可用余额不足']);
            }
            //利息宝参数
            $lxbParam = Db::name('xy_lixibao_list')->find($lixibao['sid']);

            //
            $issy = 0;
            if ( time() > $lixibao['endtime'] ) {
                //未到期
                $issy= 1;
            }else{
                $issy= -1;
            }

            Db::name('xy_users')->where('id',$uid)->setDec('lixibao_balance',$price);  //余额 -

            $oldprice = $price;
            $shouxu = $lxbParam['shouxu'];
            if ($shouxu) {
                $price = $price - $price*$shouxu;
            }

            $res = Db::name('xy_lixibao')->where('id',$id)->update([
                'endtime'     => time(),
                'is_qu'      => 1,
                'is_sy'      => $issy,
                'shouxu'     =>$oldprice*$shouxu
            ]);


            Db::name('xy_users')->where('id',$uid)->setInc('balance',$price);  //余额 +
            $res1 = Db::name('xy_balance_log')->insert([
                //记录返佣信息
                'uid'       => $uid,
                'oid'       => $id,
                'num'       => $price,
                'type'      => 22,
                'addtime'   => time()
            ]);

            //利息宝记录转出


            if($res) {
                return json(['code'=>0,'info'=>'操作成功']);
            }else{
                return json(['code'=>1,'info'=>'操作失败!请检查账号余额']);
            }

        }

        $this->assign('title','利息宝余额转出');
        $this->rililv = config('lxb_bili')*100 .'%' ;
        $this->yue = $uinfo['lixibao_balance'] ;
        
        
        $query = $this->_query('xy_lixibao')->where('uid',session('user_id'))->order('addtime desc');
        
        $start_time = input('get.start_time/s','');
        $end_time =input('get.end_time/s','');
        $is_qu = input('get.is_qu/d',2);
        $where = [];
        if( !empty($start_time) && !empty($end_time)){
        	$start_time = strtotime("{$start_time} 0:0:0" );
        	$end_time = strtotime("{$end_time} 23:59:59");
 
        	 $query->where("addtime >={$start_time} and addtime <= {$end_time}");
        }

        if( $is_qu !=2){
        	$query->where(['is_qu'=>$is_qu]);
        }
        
        
  
		$query->page();

        //return $this->fetch();
    }



    //升级vip
    public function recharge_dovip()
    {
        if(request()->isPost()){
            $level = input('post.level/d',1);
            $type = input('post.type/s','');

            $uid = session('user_id');
            $uinfo = db('xy_users')->field('pwd,salt,tel,username,balance')->find($uid);
            if(!$level ) return json(['code'=>1,'info'=>'参数错误']);

            //
            $pay = db('xy_pay')->where('id',$type)->find();
            $level_info = db('xy_level')->where('level',$level)->find();
            $num = $level_info['num'];
            // db('xy_level')->where('level',$level)->value('num');;

            if ($num > $uinfo['balance']) {
                return json(['code'=>1,'info'=>'可用余额不足!']);
            }



            $id = getSn('SY');
            $res = db('xy_recharge')
                ->insert([
                    'id'        => $id,
                    'uid'       => $uid,
                    'tel'       => $uinfo['tel'],
                    'real_name' => $uinfo['username'],
                    'pic'       => '',
                    'num'       => $num,
                    'addtime'   => time(),
                    'pay_name'  => $type,
                    'is_vip'    => 1,
                    'level'     =>$level
                ]);
            if($res){
                if ($type == 999) {
                    $level_validity = $level_info['validity'] > 0 ? date('Y-m-d H:i:s', time() + $level_info['validity'] * 24 * 3600) : 99;
                    $res1 = Db::name('xy_users')->where('id',$uid)->update(['level'=>$level, 'level_validity' => $level_validity]);
                    $res1 = Db::name('xy_users')->where('id',$uid)->setDec('balance',$num);
                    $res = Db::name('xy_recharge')->where('id',$id)->update(['endtime'=>time(),'status'=>2]);


                    $res2 = Db::name('xy_balance_log')
                        ->insert([
                            'uid'=>$uid,
                            'oid'=>$id,
                            'num'=>$num,
                            'type'=>30,
                            'status'=>1,
                            'addtime'=>time(),
                        ]);
                    return json(['code'=>0,'info'=>'升级成功']);
                }



                $pay['id'] = $id;
                $pay['num'] =$num;
                if ($pay['name2'] == 'bipay' ) {
                    $pay['redirect'] = url('/index/Api/bipay').'?oid='.$id;
                }
                if ($pay['name2'] == 'paysapi' ) {
                    $pay['redirect'] = url('/index/Api/pay').'?oid='.$id;
                }

                if ($pay['name2'] == 'card' ) {
                    $pay['master_cardnum']= config('master_cardnum');
                    $pay['master_name']= config('master_name');
                    $pay['master_bank']= config('master_bank');
                }

                return json(['code'=>0,'info'=>$pay]);
            }

            else
                return json(['code'=>1,'info'=>'提交失败，请稍后再试']);
        }
        return json(['code'=>0,'info'=>'请求成功!','data'=>[]]);
    }


    public function recharge(){
        $uid = session('user_id');
        $tel = Db::name('xy_users')->where('id',$uid)->value('tel');//获取用户今日已充值金额
        $this->tel = substr_replace($tel,'****',3,4);
        $this->pay = db('xy_pay')->where('status',1)->select();

        return $this->fetch();
    }

    public function recharge_do_before()
    {
        $num = input('post.price/f',0);
        $type = input('post.type/s','card');

        $uid = session('user_id');
        if(!$num ) return json(['code'=>1,'info'=>'参数错误']);

        //时间限制 //TODO
        $res = check_time(config('chongzhi_time_1'),config('chongzhi_time_2'));
        $str = config('chongzhi_time_1').":00  - ".config('chongzhi_time_2').":00";
        if($res) return json(['code'=>1,'info'=>'禁止在'.$str.'以外的时间段执行当前操作!']);


        //
        $pay = db('xy_pay')->where('name2',$type)->find();
        if ($num < $pay['min']) return json(['code'=>1,'info'=>'充值不能小于'.$pay['min']]);
        if ($num > $pay['max']) return json(['code'=>1,'info'=>'充值不能大于'.$pay['max']]);

        $info = [];
        $info['num'] = $num;
        return json(['code'=>0,'info'=>$info]);
    }
    public function recharge5_do_before()
    {
        $num = input('post.price/f',0);
        $type = input('post.type/s','card');

        $uid = session('user_id');
        if(!$num ) return json(['code'=>1,'info'=>'参数错误']);

        //时间限制 //TODO
        $res = check_time(config('chongzhi_time_1'),config('chongzhi_time_2'));
        $str = config('chongzhi_time_1').":00  - ".config('chongzhi_time_2').":00";
        if($res) return json(['code'=>1,'info'=>'禁止在'.$str.'以外的时间段执行当前操作!']);


        //
        $pay = db('xy_pay')->where('name2',$type)->find();
        if ($num < $pay['min']) return json(['code'=>1,'info'=>'充值不能小于'.$pay['min']]);
        if ($num > $pay['max']) return json(['code'=>1,'info'=>'充值不能大于'.$pay['max']]);

        $info = [];
        $info['num'] = $num;
        return json(['code'=>0,'info'=>$info]);
    }
    public function recharge6_do_before()
    {
        $num = input('post.price/f',0);
        $type = input('post.type/s','card');

        $uid = session('user_id');
        if(!$num ) return json(['code'=>1,'info'=>'参数错误']);

        //时间限制 //TODO
        $res = check_time(config('chongzhi_time_1'),config('chongzhi_time_2'));
        $str = config('chongzhi_time_1').":00  - ".config('chongzhi_time_2').":00";
        if($res) return json(['code'=>1,'info'=>'禁止在'.$str.'以外的时间段执行当前操作!']);


        //
        $pay = db('xy_pay')->where('name2',$type)->find();
        if ($num < $pay['min']) return json(['code'=>1,'info'=>'充值不能小于'.$pay['min']]);
        if ($num > $pay['max']) return json(['code'=>1,'info'=>'充值不能大于'.$pay['max']]);

        $info = [];
        $info['num'] = $num;
        return json(['code'=>0,'info'=>$info]);
    }

    public function recharge2()
    {
        $oid = input('get.oid/s','');
        $num = input('get.num/s','');
        $type = input('get.type/s','');
        $this->pay = db('xy_pay')->where('status',1)->where('name2',$type)->find();
        if(request()->isPost()) {
            $id = input('post.id/s', '');
            $pic = input('post.pic/s', '');

            if (is_image_base64($pic)) {
                $pic = '/' . $this->upload_base64('xy', $pic);  //调用图片上传的方法
            }else{
                return json(['code'=>1,'info'=>'图片格式错误']);
            }

            $res = db('xy_recharge')->where('id',$id)->update(['pic'=>$pic]);
            if (!$res) {
                return json(['code'=>1,'info'=>'提交失败，请稍后再试']);
            }else{
                return json(['code'=>0,'info'=>'请求成功!','data'=>[]]);
            }
        }

   
        $info = [];//db('xy_recharge')->find($oid);
        $info['num'] = $num;//db('xy_recharge')->find($oid);
        
        $this->bank = Db::name('xy_bank')->where('status', 1)->select();
        
        /*
        $info['master_bank'] = config('master_bank');//银行名称
        $info['master_name'] = config('master_name');//收款人
        $info['master_cardnum'] = config('master_cardnum');//银行卡号
        $info['master_bk_address'] = config('master_bk_address');//银行地址
        */

        
        $this->info = $info;

        return $this->fetch();
    }
    public function recharge55()
    {
        $oid = input('get.oid/s','');
        $num = input('get.num/s','');
        $type = input('get.type/s','');
        $this->pay = db('xy_pay')->where('status',1)->where('name2',$type)->find();
        if(request()->isPost()) {
            $id = input('post.id/s', '');
            $pic = input('post.pic/s', '');

            if (is_image_base64($pic)) {
                $pic = '/' . $this->upload_base64('xy', $pic);  //调用图片上传的方法
            }else{
                return json(['code'=>1,'info'=>'图片格式错误']);
            }

            $res = db('xy_recharge')->where('id',$id)->update(['pic'=>$pic]);
            if (!$res) {
                return json(['code'=>1,'info'=>'提交失败，请稍后再试']);
            }else{

                return json(['code'=>0,'info'=>'请求成功!','data'=>[]]);
            }
        }

        $num = $num.'.'.rand(10,99); //随机金额
        $info = [];//db('xy_recharge')->find($oid);
        $info['num'] = $num;//db('xy_recharge')->find($oid);
        
        $this->bank = Db::name('xy_bank')->where('status', 1)->select();
        
        /*
        $info['master_bank'] = config('master_bank');//银行名称
        $info['master_name'] = config('master_name');//收款人
        $info['master_cardnum'] = config('master_cardnum');//银行卡号
        $info['master_bk_address'] = config('master_bk_address');//银行地址
        */

        
        $this->info = $info;

        return $this->fetch();
    }
    public function recharge66()
    {
        $oid = input('get.oid/s','');
        $num = input('get.num/s','');
        $type = input('get.type/s','');
        $this->pay = db('xy_pay')->where('status',1)->where('name2',$type)->find();
        if(request()->isPost()) {
            $id = input('post.id/s', '');
            $pic = input('post.pic/s', '');

            if (is_image_base64($pic)) {
                $pic = '/' . $this->upload_base64('xy', $pic);  //调用图片上传的方法
            }else{
                return json(['code'=>1,'info'=>'图片格式错误']);
            }

            $res = db('xy_recharge')->where('id',$id)->update(['pic'=>$pic]);
            if (!$res) {
                return json(['code'=>1,'info'=>'提交失败，请稍后再试']);
            }else{

                return json(['code'=>0,'info'=>'请求成功!','data'=>[]]);
            }
        }

        $num = $num.'.'.rand(10,99); //随机金额
        $info = [];//db('xy_recharge')->find($oid);
        $info['num'] = $num;//db('xy_recharge')->find($oid);
        
        $this->bank = Db::name('xy_bank')->where('status', 1)->select();
        
        /*
        $info['master_bank'] = config('master_bank');//银行名称
        $info['master_name'] = config('master_name');//收款人
        $info['master_cardnum'] = config('master_cardnum');//银行卡号
        $info['master_bk_address'] = config('master_bk_address');//银行地址
        */

        
        $this->info = $info;

        return $this->fetch();
    }    
    //三方支付
    public function recharge3()
    {

        $type = isset($_REQUEST['type']) ? $_REQUEST['type']: 'wx';
        $pay = db('xy_pay')->where('status',1)->select();
        $this->assign('title',$type=='wx' ? '微信支付': '支付宝支付');
        $this->assign('pay',$pay);
        $this->assign('type',$type);
        return $this->fetch();
    }
    public function recharge5(){
        $uid = session('user_id');
        $tel = Db::name('xy_users')->where('id',$uid)->value('tel');//获取用户今日已充值金额
        $this->tel = substr_replace($tel,'****',3,4);
        $this->pay = db('xy_pay')->where('status',1)->select();

        return $this->fetch();
    }
    public function recharge6(){
        $uid = session('user_id');
        $tel = Db::name('xy_users')->where('id',$uid)->value('tel');//获取用户今日已充值金额
        $this->tel = substr_replace($tel,'****',3,4);
        $this->pay = db('xy_pay')->where('status',1)->select();

        return $this->fetch();
    }
    //钱包页面
    public function bank()
    {
        $balance = db('xy_users')->where('id', session('user_id'))->value('balance');
        $this->assign('balance', $balance);
        $balanceT = db('xy_convey')->where('uid', session('user_id'))->where('status', 2)->sum('commission');
        $this->assign('balance_shouru', $balanceT);
        return $this->fetch();
    }

    //获取提现订单接口
    public function get_deposit()
    {
        $info = db('xy_deposit')->where('uid',session('user_id'))->select();
        if($info) return json(['code'=>0,'info'=>'请求成功','data'=>$info]);
        return json(['code'=>1,'info'=>'暂无数据']);
    }

    public function my_data()
    {
        $uinfo = db('xy_users')->where('id', session('user_id'))->find();
        if ($uinfo['tel']) {
            $uinfo['tel'] = substr_replace($uinfo['tel'], '****', 3, 4);
        }
        $bank = db('xy_bankinfo')->where(['uid'=>session('user_id')])->find();
        $uinfo['cardnum'] = substr_replace($bank['cardnum'],'****',7,7);
        if(request()->isPost()) {
            $username = input('post.username/s', '');
            //$pic = input('post.qq/s', '');

            $res = db('xy_users')->where('id',session('user_id'))->update(['username'=>$username]);
            if (!$res) {
                return json(['code'=>1,'info'=>'提交失败，请稍后再试']);
            }else{
                return json(['code'=>0,'info'=>'请求成功!','data'=>[]]);
            }
        }

        $this->assign('info', $uinfo);

        return $this->fetch();
    }



    public function recharge_do()
    {
        if(request()->isPost()){
            $num = input('post.price/f',0);
            $type = input('post.type/s','card');
            $pic = input('post.pic/s','');

            $uid = session('user_id');
            $uinfo = db('xy_users')->field('pwd,salt,tel,username')->find($uid);
            if(!$num ) return json(['code'=>1,'info'=>'参数错误']);

            if (is_image_base64($pic))
                $pic = '/' . $this->upload_base64('xy',$pic);  //调用图片上传的方法
            else
                return json(['code'=>1,'info'=>'图片格式错误']);

            //
            $pay = db('xy_pay')->where('name2',$type)->find();
            if ($num < $pay['min']) return json(['code'=>1,'info'=>'充值不能小于'.$pay['min']]);
            if ($num > $pay['max']) return json(['code'=>1,'info'=>'充值不能大于'.$pay['max']]);

            $id = getSn('SY');
            $res = db('xy_recharge')
                ->insert([
                    'id'        => $id,
                    'uid'       => $uid,
                    'tel'       => $uinfo['tel'],
                    'real_name' => $uinfo['username'],
                    'pic'       => $pic,
                    'num'       => $num,
                    'addtime'   => time(),
                    'pay_name'  => $type
                ]);
            if($res){
                $pay['id'] = $id;
                $pay['num'] =$num;
                if ($pay['name2'] == 'bipay' ) {
                    $pay['redirect'] = url('/index/Api/bipay').'?oid='.$id;
                }
                if ($pay['name2'] == 'paysapi' ) {
                    $pay['redirect'] = url('/index/Api/pay').'?oid='.$id;
                }
                return json(['code'=>0,'info'=>$pay]);
            }

            else
                return json(['code'=>1,'info'=>'提交失败，请稍后再试']);
        }
        return json(['code'=>0,'info'=>'请求成功!','data'=>[]]);
    }

    function deposit_wx(){

        $user = db('xy_users')->where('id', session('user_id'))->find();
        $this->assign('title','微信提现');

        $this->assign('type','wx');
        $this->assign('user',$user);
        return $this->fetch();
    }

    //提现首页
    function deposit(){

        $user = db('xy_users')->where('id', session('user_id'))->find();
        $user['tel'] = substr_replace($user['tel'],'****',3,4);
        $bank = db('xy_bankinfo')->where(['uid'=>session('user_id')])->find();

        $bank['cardnum'] = !empty($bank['cardnum']) ? substr_replace($bank['cardnum'],'****',7,7) : '';
        $bank['zhifunum'] = !empty($bank['zhifunum']) ? substr_replace($bank['zhifunum'],'****',2,5) : '';
        $this->assign('info',$bank);
        $this->assign('user',$user);

        //提现限制
        $level = $user['level'];
        !$user['level'] ? $level = 0 : '';
        $ulevel = Db::name('xy_level')->where('level',$level)->find();
        $this->shouxu = $ulevel['tixian_shouxu'];;;

        return $this->fetch();
    }
    
    function deposit_zfb(){

        $user = db('xy_users')->where('id', session('user_id'))->find();
        $this->assign('title','支付宝提现');

        $this->assign('type','zfb');
        $this->assign('user',$user);
        return $this->fetch('deposit_zfb');
    }


    //提现接口
    public function do_deposit()
    {
        $res = check_time(config('tixian_time_1'),config('tixian_time_2'));
        $str = config('tixian_time_1').":00  - ".config('tixian_time_2').":00";
        if($res) return json(['code'=>1,'info'=>'禁止在'.$str.'以外的时间段执行当前操作!','a'=>1]);  //'a'=>0 表示刷新提现当前页，1表示跳转回个人页


        $bankinfo = Db::name('xy_bankinfo')->where('uid',session('user_id'))->where('status',1)->find();
        //var_dump($bankinfo);die;
        $type = config('tixian_type');

        if(!$bankinfo) return json(['code'=>1,'info'=>'还没添加收款信息!','a'=>1]);


        if(request()->isPost()){
            $uid = session('user_id');

            //交易密码
            $pwd2 = input('post.paypassword/s','');
            $info = db('xy_users')->field('pwd2,salt2')->find(session('user_id'));
            if($info['pwd2']=='') return json(['code'=>1,'info'=>'未设置交易密码']);
            if($info['pwd2']!=sha1($pwd2.$info['salt2'].config('pwd_str'))) return json(['code'=>1,'info'=>'密码错误' ,'a'=>0]);


            $num = input('post.num/d',0);
            $bkid = input('post.bk_id/d',$bankinfo['id']);
            // $type = input('post.type/s','');
            $token = input('post.token','');
            $data = ['__token__' => $token];
            $validate   = \Validate::make($this->rule,$this->msg);
            if(!$validate->check($data)) return json(['code'=>1,'info'=>$validate->getError(),'a'=>0]);//'a'=>0 表示刷新提现当前页，1表示跳转回个人页

            if ($num <= 0)return json(['code'=>1,'info'=>'参数错误','status'=>0]);

            $uinfo = Db::name('xy_users')->field('recharge_num,deal_time,balance,level')->find($uid);//获取用户今日已充值金额

            //提现限制
            $level = $uinfo['level'];
            !$uinfo['level'] ? $level = 0 : '';
            $ulevel = Db::name('xy_level')->where('level',$level)->find();
            if ($num < $ulevel['tixian_min']) {
                return ['code'=>1,'info'=>'会员等级 提现额度为 '.$ulevel['tixian_min'].'-'.$ulevel['tixian_max'].'!' , 'a'=>0];
            }
            if ($num >= $ulevel['tixian_max']) {
                return ['code'=>1,'info'=>'会员等级 提现额度为 '.$ulevel['tixian_min'].'-'.$ulevel['tixian_max'].'!' ,'a'=>0];//'a'=>0 表示刷新提现当前页，1表示跳转回个人页
            }

            $onum =  db('xy_convey')->where('uid',$uid)->where('addtime','between',[strtotime(date('Y-m-d')),time()])->count('id');
            if ($onum < $ulevel['tixian_nim_order']) {
                return ['code'=>1,'info'=>'当前等级提现需完成  '.$ulevel['tixian_nim_order'].' 笔订单!' ,'a'=>1];
            }
            
            //有未完成订单不允许提现
            $order_status =  db('xy_convey')->where('uid',$uid)->where('status',0)->count('status');  //查询未完成订单
            
            if( $order_status != 0 ){
                return ['code'=>1,'info'=>'您还有未完成的订单！！','a'=>1];
            }
             

            if ($num > $uinfo['balance']) return json(['code'=>1,'info'=>'余额不足','a'=>0]);


            if($uinfo['deal_time']==strtotime(date('Y-m-d'))){
                //if($num > 20000-$uinfo['recharge_num']) return ['code'=>1,'info'=>'今日剩余提现额度为'.( 20000 - $uinfo['recharge_num'])];
                //提现次数限制
                $tixianCi = db('xy_deposit')->where('uid',$uid)->where('addtime','between',[strtotime(date('Y-m-d 00:00:00')),time()])->count();
                if ($tixianCi+1 > $ulevel['tixian_ci'] ) {
                    return ['code'=>1,'info'=>'会员等级 今日提现次数不足!' ,'a'=>0];
                }

            }else{
                //重置最后交易时间
                Db::name('xy_users')->where('id',$uid)->update(['deal_time'=>strtotime(date('Y-m-d')),'deal_count'=>0,'recharge_num'=>0,'deposit_num'=>0]);
            }
            $id = getSn('CO');
            try {
                Db::startTrans();
                $res = Db::name('xy_deposit')->insert([
                    'id'          => $id,
                    'uid'         => $uid,
                    'bk_id'       => $bkid,
                    'num'         => $num,
                    'addtime'     => time(),
                    'type'        => $type,
                    'shouxu'      => $ulevel['tixian_shouxu'],
                    'real_num'    => $num - ($num*$ulevel['tixian_shouxu'])
                ]);

                //提现日志
                $res2 = Db::name('xy_balance_log')
                    ->insert([
                        'uid' => $uid,
                        'oid' => $id,
                        'num' => $num,
                        'type' => 7, //TODO 7提现
                        'status' => 2,
                        'addtime' => time(),
                    ]);


                $res1 = Db::name('xy_users')->where('id',session('user_id'))->setDec('balance',$num);
                if($res && $res1){
                    Db::commit();
                    return json(['code'=>0,'info'=>'操作成功!','a'=>0]);
                }else{
                    Db::rollback();
                    return json(['code'=>1,'info'=>'操作失败!','a'=>0]);
                }
            } catch (\Exception $e){
                Db::rollback();
                return json(['code'=>1,'info'=>'操作失败!请检查账号余额','a'=>0]);
            }
        }
        return json(['code'=>0,'info'=>'请求成功!','data'=>$bankinfo,'a'=>1]);
    }

    //////get请求获取参数，post请求写入数据，post请求传人bkid则更新数据//////////
    public function do_bankinfo()
    {
        if(request()->isPost()){
            $token = input('post.token','');
            $data = ['__token__' => $token];
            $validate   = \Validate::make($this->rule,$this->msg);
            if(!$validate->check($data)) return json(['code'=>1,'info'=>$validate->getError()]);

            $username = input('post.username/s','');
            $bankname = input('post.bankname/s','');
            $cardnum = input('post.cardnum/s','');
            $site = input('post.site/s','');
            $tel = input('post.tel/s','');
            $status = input('post.default/d',0);
            $bkid = input('post.bkid/d',0); //是否为更新数据

            if(!$username)return json(['code'=>1,'info'=>'开户人名称为必填项']);
            if(mb_strlen($username) > 30)return json(['code'=>1,'info'=>'开户人名称长度最大为30个字符']);
            if(!$bankname)return json(['code'=>1,'info'=>'银行名称为必填项']);
            if(!$cardnum)return json(['code'=>1,'info'=>'银行卡号为必填项']);
            if(!$tel)return json(['code'=>1,'info'=>'手机号为必填项']);

            if($bkid)
                $cardn = Db::table('xy_bankinfo')->where('id','<>',$bkid)->where('cardnum',$cardnum)->count();
            else
                $cardn = Db::table('xy_bankinfo')->where('cardnum',$cardnum)->count();
            
            if($cardn)return json(['code'=>1,'info'=>'银行卡号已存在']);

            $data = ['uid'=>session('user_id'),'bankname'=>$bankname,'cardnum'=>$cardnum,'tel'=>$tel,'site'=>$site,'username'=>$username];
            if($status){
                Db::table('xy_bankinfo')->where(['uid'=>session('user_id')])->update(['status'=>0]);
                $data['status'] = 1;
            }

            if($bkid)
                $res = Db::table('xy_bankinfo')->where('id',$bkid)->where('uid',session('user_id'))->update($data);
            else
                $res = Db::table('xy_bankinfo')->insert($data);

            if($res!==false)
                return json(['code'=>0,'info'=>'操作成功']);
            else
                return json(['code'=>1,'info'=>'操作失败']);
        }
        $bkid = input('id/d',0); //是否为更新数据
        $where = ['uid'=>session('user_id')];
        if($bkid!==0) $where['id'] = $bkid;
        $info = db('xy_bankinfo')->where($where)->select();
        if(!$info) return json(['code'=>1,'info'=>'暂无数据']);
        return json(['code'=>0,'info'=>'请求成功','data'=>$info]);
    }

    //切换银行卡状态
    public function edit_bankinfo_status()
    {
        $id = input('post.id/d',0);

        Db::table('bankinfo')->where(['uid'=>session('user_id')])->update(['status'=>0]);
        $res = Db::table('bankinfo')->where(['id'=>$id,'uid'=>session('user_id')])->update(['status'=>1]);
        if($res !== false)
            return json(['code'=>0,'info'=>'操作成功!']); 
        else
            return json(['code'=>1,'info'=>'操作失败！']); 
    }

    //获取下级会员
    public function bot_user()
    {
        if(request()->isPost()){
            $uid = input('post.id/d',0);
            $token = ['__token__' => input('post.token','')];
            $validate   = \Validate::make($this->rule,$this->msg);
            if(!$validate->check($token)) return json(['code'=>1,'info'=>$validate->getError()]);
        }else{
            $uid = session('user_id');
        }
        $page = input('page/d',1);
        $num = input('num/d',10);
        $limit = ( (($page - 1) * $num) . ',' . $num );
        $data = db('xy_users')->where('parent_id',$uid)->field('id,username,headpic,addtime,childs,tel')->limit($limit)->order('addtime desc')->select();
        if(!$data) return json(['code'=>1,'info'=>'暂无数据']);
        return json(['code'=>0,'info'=>'请求成功','data'=>$data]);
    }
    
    //修改密码
    public function set_pwd()
    {
        if(!request()->isPost()) return json(['code'=>1,'info'=>'错误请求']);
        $o_pwd = input('old_pwd/s','');
        $pwd = input('new_pwd/s','');
        $type = input('type/d',1);
        $uinfo = db('xy_users')->field('pwd,salt,tel')->find(session('user_id'));
        if($uinfo['pwd']!=sha1($o_pwd.$uinfo['salt'].config('pwd_str'))) return json(['code'=>1,'info'=>'密码错误!']);
        $res = model('admin/Users')->reset_pwd($uinfo['tel'],$pwd,$type);
        return json($res);
    }

    public function set()
    {
        $uid = session('user_id');
        $this->info = db('xy_users')->find($uid);
        $this->info['tel'] = substr($this->info['tel'], 0, 3) . '****' . substr($this->info['tel'], 7, 10);
        return $this->fetch();
    }



    //我的下级
    public function get_user()
    {

        $uid = session('user_id');

        $type = input('post.type/d',1);

        $page = input('page/d',1);
        $num = input('num/d',10);
        $limit = ( (($page - 1) * $num) . ',' . $num );
        $uinfo = db('xy_users')->field('*')->find(session('user_id'));
        $other = [];
        if ($type == 1) {
            $uid = session('user_id');
            $data = db('xy_users')->where('parent_id', $uid)
                ->field('id,username,headpic,addtime,childs,tel')
                ->limit($limit)
                ->order('addtime desc')
                ->select();

            //总的收入  总的充值
            //$ids1 = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');
            //$cond=implode(',',$ids1);
            //$cond = !empty($cond) ? $cond = " uid in ($cond)":' uid=-1';
            $other = [];
            //$other['chongzhi'] = db('xy_recharge')->where($cond)->where('status', 2)->sum('num');
            //$other['tixian'] = db('xy_deposit')->where($cond)->where('status', 2)->sum('num');
            //$other['xiaji'] = count($ids1);

            $uids = model('admin/Users')->child_user($uid,5);
            $uids ? $where[] = ['uid','in',$uids] : $where[] = ['uid','in',[-1]];
            $uids ? $where2[] = ['uid','in',$uids] : $where2[] = ['uid','in',[-1]];

            $other['chongzhi'] = db('xy_recharge')->where($where2)->where('status', 2)->sum('num');
            $other['tixian'] = db('xy_deposit')->where($where2)->where('status', 2)->sum('num');
            $other['xiaji'] = count($uids);


            //var_dump($uinfo);die;

            $iskou =0;
            foreach ($data as &$datum) {
                $datum['addtime'] = date('Y/m/d H:i', $datum['addtime']);
                empty($datum['headpic']) ? $datum['headpic'] = '/public/img/head.png':'';
                //充值
                $datum['chongzhi'] = db('xy_recharge')->where('uid', $datum['id'])->where('status', 2)->sum('num');
                //提现
                $datum['tixian'] = db('xy_deposit')->where('uid', $datum['id'])->where('status', 2)->sum('num');

                if ($uinfo['kouchu_balance_uid'] == $datum['id']) {
                    $datum['chongzhi'] -= $uinfo['kouchu_balance'];
                    $iskou = 1;
                }

                if ($uinfo['show_tel2']) {
                    $datum['tel'] = substr_replace($datum['tel'], '****', 3, 4);
                }
                if (!$uinfo['show_tel']) {
                    $datum['tel'] = '无权限';
                }
                if (!$uinfo['show_num']) {
                    $datum['childs'] = '无权限';
                }
                if (!$uinfo['show_cz']) {
                    $datum['chongzhi'] = '无权限';
                }
                if (!$uinfo['show_tx']) {
                    $datum['tixian'] = '无权限';
                }
            }

            $other['chongzhi'] -= $uinfo['kouchu_balance'];
            return json(['code'=>0,'info'=>'请求成功','data'=>$data,'other'=>$other]);

        }else if($type == 2) {
            $ids1 = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');
            $cond=implode(',',$ids1);
            $cond = !empty($cond) ? $cond = " parent_id in ($cond)":' parent_id=-1';

            //获取二代ids
            $ids2 = db('xy_users')->where($cond)->field('id')->column('id');
            $cond2=implode(',',$ids2);
            $cond2 = !empty($cond2) ? $cond2 = " uid in ($cond2)":' uid=-1';
            $other = [];
            $other['chongzhi'] = db('xy_recharge')->where($cond2)->where('status', 2)->sum('num');
            $other['tixian'] = db('xy_deposit')->where($cond2)->where('status', 2)->sum('num');
            $other['xiaji'] = count($ids2);



            $data = db('xy_users')->where($cond)
                ->field('id,username,headpic,addtime,childs,tel')
                ->limit($limit)
                ->order('addtime desc')
                ->select();

            //总的收入  总的充值

            foreach ($data as &$datum) {
                empty($datum['headpic']) ? $datum['headpic'] = '/public/img/head.png':'';
                $datum['addtime'] = date('Y/m/d H:i', $datum['addtime']);
                //充值
                $datum['chongzhi'] = db('xy_recharge')->where('uid', $datum['id'])->where('status', 2)->sum('num');
                //提现
                $datum['tixian'] = db('xy_deposit')->where('uid', $datum['id'])->where('status', 2)->sum('num');

                if ($uinfo['show_tel2']) {
                    $datum['tel'] = substr_replace($datum['tel'], '****', 3, 4);
                }
                if (!$uinfo['show_tel']) {
                    $datum['tel'] = '无权限';
                }
                if (!$uinfo['show_num']) {
                    $datum['childs'] = '无权限';
                }
                if (!$uinfo['show_cz']) {
                    $datum['chongzhi'] = '无权限';
                }
                if (!$uinfo['show_tx']) {
                    $datum['tixian'] = '无权限';
                }
            }

            return json(['code'=>0,'info'=>'请求成功','data'=>$data,'other'=>$other]);


        }else if($type == 3) {
            $ids1 = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');
            $cond=implode(',',$ids1);
            $cond = !empty($cond) ? $cond = " parent_id in ($cond)":' parent_id=-1';
            $ids2 = db('xy_users')->where($cond)->field('id')->column('id');

            $cond2=implode(',',$ids2);
            $cond2 = !empty($cond2) ? $cond2 = " parent_id in ($cond2)":' parent_id=-1';

            //获取三代的ids
            $ids22 = db('xy_users')->where($cond2)->field('id')->column('id');
            $cond22=implode(',',$ids22);
            $cond22 = !empty($cond22) ? $cond22 = " uid in ($cond22)":' uid=-1';
            $other = [];
            $other['chongzhi'] = db('xy_recharge')->where($cond22)->where('status', 2)->sum('num');
            $other['tixian'] = db('xy_deposit')->where($cond22)->where('status', 2)->sum('num');
            $other['xiaji'] = count($ids22);

            //获取四代ids
            $cond4 =implode(',',$ids22);
            $cond4 = !empty($cond4) ? $cond4 = " parent_id in ($cond4)":' parent_id=-1';
            $ids4  = db('xy_users')->where($cond4)->field('id')->column('id'); //四代ids

            //充值
            $cond44 =implode(',',$ids4);
            $cond44 = !empty($cond44) ? $cond44 = " uid in ($cond44)":' uid=-1';
            $other['chongzhi4'] = db('xy_recharge')->where($cond44)->where('status', 2)->sum('num');
            $other['tixian4'] = db('xy_deposit')->where($cond44)->where('status', 2)->sum('num');
            $other['xiaji4'] = count($ids4);



            //获取五代
            $cond5 = implode(',',$ids4);
            $cond5 = !empty($cond5) ? $cond5 = " parent_id in ($cond5)":' parent_id=-1';
            $ids5  = db('xy_users')->where($cond5)->field('id')->column('id'); //五代ids

            //充值
            $cond55 =implode(',',$ids5);
            $cond55 = !empty($cond55) ? $cond55 = " uid in ($cond55)":' uid=-1';
            $other['chongzhi5'] = db('xy_recharge')->where($cond55)->where('status', 2)->sum('num');
            $other['tixian5'] = db('xy_deposit')->where($cond55)->where('status', 2)->sum('num');
            $other['xiaji5'] = count($ids5);

            $other['chongzhi_all'] = $other['chongzhi'] + $other['chongzhi4']+ $other['chongzhi5'];
            $other['tixian_all']   = $other['tixian'] + $other['tixian4']+ $other['tixian5'];

            $data = db('xy_users')->where($cond2)
                ->field('id,username,headpic,addtime,childs,tel')
                ->limit($limit)
                ->order('addtime desc')
                ->select();

            //总的收入  总的充值

            foreach ($data as &$datum) {
                $datum['addtime'] = date('Y/m/d H:i', $datum['addtime']);
                empty($datum['headpic']) ? $datum['headpic'] = '/public/img/head.png':'';
                //充值
                $datum['chongzhi'] = db('xy_recharge')->where('uid', $datum['id'])->where('status', 2)->sum('num');
                //提现
                $datum['tixian'] = db('xy_deposit')->where('uid', $datum['id'])->where('status', 2)->sum('num');

                if ($uinfo['show_tel2']) {
                    $datum['tel'] = substr_replace($datum['tel'], '****', 3, 4);
                }
                if (!$uinfo['show_tel']) {
                    $datum['tel'] = '无权限';
                }
                if (!$uinfo['show_num']) {
                    $datum['childs'] = '无权限';
                }
                if (!$uinfo['show_cz']) {
                    $datum['chongzhi'] = '无权限';
                }
                if (!$uinfo['show_tx']) {
                    $datum['tixian'] = '无权限';
                }
            }
            return json(['code'=>0,'info'=>'请求成功','data'=>$data,'other'=>$other]);
        }



        return json(['code'=>0,'info'=>'请求成功','data'=>$data]);
    }

    /**
     * 充值记录
     */
    public function recharge_admin()
    {
        $id = session('user_id');
        $where=[];
        $this->_query('xy_recharge')
            ->where('uid',$id)->where($where)->order('id desc')->page();

    }

    /**
     * 提现记录
     */
    public function deposit_admin()
    {
        $id = session('user_id');
        $where=[];
        $this->_query('xy_deposit')
            ->where('uid',$id)->where($where)->order('id desc')->page();

    }


    /**
     * 团队
     */
    public function junior()
    {
        $uid = session('user_id');
        $where=[];
        $this->level = $level = input('get.level/d',1);
        $this->uinfo = db('xy_users')->where('id', $uid)->find();

        //计算五级团队余额
        $uidAlls5 = model('admin/Users')->child_user($uid,5,1);
        $uidAlls5 ? $whereAll[] = ['id','in',$uidAlls5] : $whereAll[] = ['id','in',[-1]];
        $uidAlls5 ? $whereAll2[] = ['uid','in',$uidAlls5] : $whereAll2[] = ['id','in',[-1]];
        $this->teamyue = db('xy_users')->where($whereAll)->sum('balance');
        $this->teamcz = db('xy_recharge')->where($whereAll2)->where('status',2)->sum('num');
        $this->teamtx = db('xy_deposit')->where($whereAll2)->where('status',2)->sum('num');
        $this->teamls = db('xy_balance_log')->where($whereAll2)->sum('num');
        $this->teamyj = db('xy_convey')->where('status',1)->where($whereAll2)->sum('commission');

        $uids1 = model('admin/Users')->child_user($uid,1,0);
        $this->zhitui = count($uids1);
        $uidsAll = model('admin/Users')->child_user($uid,5,1);
        $this->tuandui = count($uidsAll);

        $start      = input('get.start/s','');
        $end        = input('get.end/s','');
        if ($start || $end) {
            $start ? $start = strtotime($start) : $start = strtotime('2020-01-01');
            $end ? $end = strtotime($end.' 23:59:59') : $end = time();
            $where[] = ['addtime','between',[$start,$end]];
        }

        $this->start = $start ? date('Y-m-d',$start) : '';
        $this->end = $end ? date('Y-m-d',$end) : '';

        $uids5 = model('admin/Users')->child_user($uid,$level,0);
        $uids5 ? $where[] = ['u.id','in',$uids5] : $where[] = ['u.id','in',[-1]];


        $this->_query('xy_users')->alias('u')
            ->where($where)->order('id desc')->page();

    }







}