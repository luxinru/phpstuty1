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
 * 支付控制器
 */
class Api extends Controller
{

    public $BASE_URL = "https://bapi.app";
    public $appKey = '';
    public $appSecret = '';

    const POST_URL = "https://pay.bbbapi.com/";


    public function __construct()
    {
        $this->appKey = config('app.bipay.appKey');
        $this->appSecret = config('app.bipay.appSecret');
    }

    public function bipay()
    {

        $oid = isset($_REQUEST['oid']) ? $_REQUEST['oid']: '';
        if ($oid) {
            $r = db('xy_recharge')->where('id',$oid)->find();
            if ($r) {
                $server_url = $_SERVER['SERVER_NAME']?"http://".$_SERVER['SERVER_NAME']:"http://".$_SERVER['HTTP_HOST'];
                $notifyUrl = $server_url.url('/index/api/bipay_notify');
                $returnUrl = $server_url.url('/index/api/bipay_return');
                $price = $r['num'] * 100;
                $res = $this->create_order($oid,$price,'用户充值',$notifyUrl, $returnUrl);

                if ($res && $res['code']==200) {
                    $url = $res['data']['pay_url'];
                    $this->redirect($url);
                }
            }
        }
    }

    public function bipay_return()
    {
        return $this->fetch();
    }


    public function bipay_notify()
    {

        $content = file_get_contents('php://input');
        $post    = (array)json_decode($content, true);
        file_put_contents("bipay_notify.log",$content."\r\n",FILE_APPEND);

        if (!$post['order_id']) {
            die('fail');
        }
        $oid = $post['order_id'];
        $r = db('xy_recharge')->where('id',$oid)->find();
        if (!$r) {
            die('fail');
        }
        if ($post['order_state']!=1) {
            die('fail');
        }

        if ($r['status'] == 2){
            die('SUCCESS');
        }

        if ($post['order_state']) {
            $res = Db::name('xy_recharge')->where('id',$oid)->update(['endtime'=>time(),'status'=>2]);
            $oinfo = $r;
            $res1 = Db::name('xy_users')->where('id',$oinfo['uid'])->setInc('balance',$oinfo['num']);
            $res2 = Db::name('xy_balance_log')
                ->insert([
                    'uid'=>$oinfo['uid'],
                    'oid'=>$oid,
                    'num'=>$oinfo['num'],
                    'type'=>1,
                    'status'=>1,
                    'addtime'=>time(),
                ]);
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
            die('SUCCESS');
        }
    }


    public function create_order(
        $orderId, $amount, $body, $notifyUrl, $returnUrl, $extra = '', $orderIp = '', $amountType = 'CNY', $lang = 'zh_CN')
    {
        $reqParam = [
            'order_id' => $orderId,
            'amount' => $amount,
            'body' => $body,
            'notify_url' => $notifyUrl,
            'return_url' => $returnUrl,
            'extra' => $extra,
            'order_ip' => $orderIp,
            'amount_type' => $amountType,
            'time' => time() * 1000,
            'app_key' => $this->appKey,
            'lang' => $lang
        ];
        $reqParam['sign'] = $this->create_sign($reqParam, $this->appSecret);
        $url = $this->BASE_URL . '/api/v2/pay';

        return $this->http_request($url, 'POST', $reqParam);
    }

    /**
     * @return {
     * bapp_id: "2019081308272299266f",
     * order_id: "1565684838",
     * order_state: 0,
     * body: "php-sdk sample",
     * notify_url: "https://sdk.b.app/api/test/notify/test",
     * order_ip: "",
     * amount: 1,
     * amount_type: "CNY",
     * amount_btc: 0,
     * pay_time: 0,
     * create_time: 1565684842076,
     * order_type: 2,
     * app_key: "your_app_key",
     * extra: ""
     * }
     */
    public function get_order($orderId)
    {
        $reqParam = [
            'order_id' => $orderId,
            'time' => time() * 1000,
            'app_key' => $this->appKey
        ];
        $reqParam['sign'] = $this->create_sign($reqParam, $this->appSecret);
        $url = $this->BASE_URL . '/api/v2/order';
        return $this->http_request($url, 'GET', $reqParam);
    }

    public function is_sign_ok($params)
    {
        $sign = $this->create_sign($params, $this->appSecret);
        return $params['sign'] == $sign;
    }

    public function create_sign($params, $appSecret)
    {
        $signOriginStr = '';
        ksort($params);
        foreach ($params as $key => $value) {
            if (empty($key) || $key == 'sign') {
                continue;
            }
            $signOriginStr = "$signOriginStr$key=$value&";
        }
        return strtolower(md5($signOriginStr . "app_secret=$appSecret"));
    }

    private function http_request($url, $method = 'GET', $params = [])
    {
        $curl = curl_init();

        if ($method == 'POST') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
            $jsonStr = json_encode($params);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonStr);
        } else if ($method == 'GET') {
            $url = $url . "?" . http_build_query($params, '', '&');
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);


        $output = curl_exec($curl);

        if (curl_errno($curl) > 0) {
            return [];
        }
        curl_close($curl);
        $json = json_decode($output, true);

        //var_dump($output,curl_errno($curl));die;

        return $json;
    }


    //----------------------------------------------------------------
    //  paysapi
    //----------------------------------------------------------------

    public function pay(){

        $oid = isset($_REQUEST['oid']) ? $_REQUEST['oid']: '';
        if ($oid) {
            $r = db('xy_recharge')->where('id',$oid)->find();
            if ($r) {

                //var_dump($r);die;

                $server_url = $_SERVER['SERVER_NAME']?"http://".$_SERVER['SERVER_NAME']:"http://".$_SERVER['HTTP_HOST'];
                $notify_url = $server_url.url('/index/api/pay_notify');
                $return_url = $server_url.url('/index/api/bipay_return');
                $price = $r['num'] * 100;


                $uid   = config('app.paysapi.uid');    //"此处填写Yipay的uid";
                $token = config('app.paysapi.token');;     //"此处填写Yipay的Token";

                $orderid = $r['id'];
                $goodsname= '用户充值';
                $istype =  config('app.paysapi.istype');
                $orderuid = session('user_id');

                $key = md5($goodsname. $istype . $notify_url . $orderid . $orderuid . $price . $return_url . $token. $uid);

                $data = array(
                    'goodsname'=>$goodsname,
                    'istype'=>$istype,
                    'key'=>$key,
                    'notify_url'=>$notify_url,
                    'orderid'=>$orderid,
                    'orderuid'=>$orderuid,
                    'price'=>$price,
                    'return_url'=>$return_url,
                    'uid'=>$uid
                );
                $this->assign('data',$data);
                $this->assign('post_url',self::POST_URL);
                return $this->fetch();
            }
        }

    }


    /**
     * notify_url接收页面
     */
    public function pay_notify(){

        $paysapi_id = $_POST["paysapi_id"];
        $orderid = $_POST["orderid"];
        $price = $_POST["price"];
        $realprice = $_POST["realprice"];
        $orderuid = $_POST["orderuid"];
        $key = $_POST["key"];

        file_put_contents(RUNTIME_PATH.'/paysapi_notify.log', json_encode($_REQUEST)."\r\n",FILE_APPEND);


        //校验传入的参数是否格式正确，略
        $d = $payType = array();
        if ($orderid) {
            $out_trade_no = $orderid;
            //$res = Db::name('xy_recharge')->where('id',$oid)->update(['endtime'=>time(),'status'=>2]);

            //$d = M('recharge')->where(array('order_no'=>$out_trade_no))->find();
            //$payType = M('pay_type')->find($d['payment_type']);

        }
        $token = config('app.paysapi.token');;
        $temps = md5($orderid . $orderuid . $paysapi_id . $price . $realprice . $token);

        if ($temps != $key){
            return exit("key值不匹配");
        }else{
            //校验key成功
            $oid = $orderid;
            $r = db('xy_recharge')->where('id',$oid)->find();
            $res = Db::name('xy_recharge')->where('id',$oid)->update(['endtime'=>time(),'status'=>2]);
            $oinfo = $r;
            $res1 = Db::name('xy_users')->where('id',$oinfo['uid'])->setInc('balance',$oinfo['num']);
            $res2 = Db::name('xy_balance_log')
                ->insert([
                    'uid'=>$oinfo['uid'],
                    'oid'=>$oid,
                    'num'=>$oinfo['num'],
                    'type'=>1,
                    'status'=>1,
                    'addtime'=>time(),
                ]);
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
            die('SUCCESS');

        }
    }



}