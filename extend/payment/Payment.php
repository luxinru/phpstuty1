<?php
namespace payment;
class Payment
{
     //代付商户号
    private $df_UID = "ZT21010615739";
    //代付平台公钥
    private $df_PT_KEY ="MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCL9T23ZWBttU9s8BrbKNA4ydyU9XTl5//KH0IcklSZNEqsEC76k1W/l8eNgUnAiela4UB4OLjZ6rlTWh88UhDAbZeFhNHgejv/F+QsUQugixZBcMZ1F92kdMkIZcJ21MpXYmCY/0qWhgvZVEfIRpn0hkVYhdOCw5k2jmbOV8MqIwIDAQAB";
    //代付商户公钥
    private $df_PUB_KEY = "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCNPxpDbi++7EZknL2P5CPr8YFOW1EPjGurMbirSkSFHyw+HWT9sBYGIj2hXqmdQGtJaAuFwzje8H1oLsC8mty62UAwM9OZs8wF0iZsInKT9oFkA4Sns5Ww0wL2b+vz8n3X1erA1TfRDvIXAu4ZyrRYG3fH0kUon/LIw8ug1yYQwQIDAQAB";
    //代付商户私钥
    private $df_KEY = "MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBAI0/GkNuL77sRmScvY/kI+vxgU5bUQ+Ma6sxuKtKRIUfLD4dZP2wFgYiPaFeqZ1Aa0loC4XDON7wfWguwLya3LrZQDAz05mzzAXSJmwicpP2gWQDhKezlbDTAvZv6/PyfdfV6sDVN9EO8hcC7hnKtFgbd8fSRSif8sjDy6DXJhDBAgMBAAECgYBvzddYMMwScKx3z6otUAUvKoeUIqxThm9jn4Px8mOyjC5VLKdYOB321Pu71FamZuuFu5oDU7icn/hkkz11DZBSFfsUbiCDN1S3oe2njfpMZt7GFUcvkbM38BBuC2KR2+xW8kq/spgxfZmHPdAVdDdfo8B31MqyQO/qceHEipCAAQJBAN7IAvHYa2a6RCzMfZFsmncSopV7TpgjrX0tZMS3MyC9ymvuQ5xLW1A1uBhHTz/bjKmaPaVMgPKJnaOyDkF8SUECQQCiTsBtuA9y/TFvysE/J61+0yU2TlVA77mcvH1Wj4I7s0leh3HObiWxaqVFYIXJ4+2tW/4FFdWh6WsSdXynDGeBAkEA0tDQwI087SQ8mWwVM0VjUmSHCA98i0nPxZHsNp7qOgR/he0de8xvp5J+GDN60gHvtRspVL/1kg4Z56r6BIwZQQJAa5C3dL979aAZKFZ+FcXM9HUof0IQvBVjtJ0yj7BSBhC+uhgXc1CKSHc+Cql6YR9eP/rscSBUUZapMDWOpALTgQJBAKYa3fSs7EV9rT9EIkeOSN6rFb6pOuVrNJ0pnwhLjyZCrpC+dHH5zQ8UVMcFflhdEb7shNDfhbwW61TMj+ExhMo=";
    //代收商户号
    private $ds_UID = "ZT20112515666";
    //代收平台公钥
    private $ds_PT_KEY = "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCN/kCttIFRYkvJfzbD26rV2lr0mDq6gFvozVnrDxMC716/5DCRJ9k/tKYg7ANqFTFiBt8Vk0BrkH8GgjCfBuvfAra/pIRVq+mR5krffwxwOmzA1g/grPIRGcSG1D0PFDD0DVqBUbpShL22ft1QqN0ixOlHCgBMGpYw8kDFs0PBXQIDAQAB";
    //代收商户公钥
    private $ds_PUB_KEY = "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDCa4EVuO+7XSe1/4iTb+7oyAVdAWs3256IEWMkStCfrKClXdJC03c1g4DDh4/C1FsDzNCvGKHkl7QxDYhRkIom5URs46RaZ4gsOQiRNHKQzk2iY9WBlegy5ftJlwrxrwWtVa4SlR6vfYV9T2E9061HDC5X9G6Sew42t6BFlAeS8wIDAQAB";
    //代收商户私钥
    private $ds_KEY = "MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAMJrgRW477tdJ7X/iJNv7ujIBV0BazfbnogRYyRK0J+soKVd0kLTdzWDgMOHj8LUWwPM0K8YoeSXtDENiFGQiiblRGzjpFpniCw5CJE0cpDOTaJj1YGV6DLl+0mXCvGvBa1VrhKVHq99hX1PYT3TrUcMLlf0bpJ7Dja3oEWUB5LzAgMBAAECgYAb7uY1AMUw3kl+VKCCzmo898ANhM6qz9sPIbBk3nrq9hp+p+Q1xbwy7Bnr/eYhz/DjsuGoxpk8Bq/U/GTBk0rF27j+l7/dPFawCkLrGs3UiYci9fQ2vXsK0KLZs2SNyHOejmGHYqs9/6pLQWo+vp6knqgTe/hzH5S/zD9kDjHMgQJBAOVur8f7ogv/ZsiME/junFrHONn7dfG22bwFmRu5xbd8si0mrzUDDQk9UaYItYI+v/w87snO9TaCZTJidDOLJakCQQDY7ufvjE8wlGW2cQh+6mqShykE+vppouBZp/pDP71W+vqrPs2erVfFnqgwe4vGTSlkbHV0P/AinopexAIpu907AkAf/m5u0c1IJQZ9QuNqHEQbeJqZ8zIAUgJM8O7n6eEDdFUYbMOT/efseOuQ3rgJAJ0fHJQj+kNBpa9c9lPD+Ex5AkAyoLcSif1lSgze4kxoSk8q+U5ScjyE8NVJN1HJ5hNLPHHKC3MziCMG5Ps9rWe2lQWHAWT8B3wEqFmEulwC32xHAkEAqJwrhYjgPCSBaM90IkRstZ1OcRYKjmF+5fVrZRg2RvtTc9oTnVTRkOykc8WhpjaZ63J2YLk/okz3m29mkaIXZg==";

    //代收接口地址
    private $ds_url = "http://pay.readingcomicsapp.com/shiro/api/international/pay";
    //代收查询接口
    private $ds_query_url = "http://pay.readingcomicsapp.com/shiro/api/international/queryOrder";

   
    
		/*
	reqMap.put("bankCardNo", "05441086");
reqMap.put("ifsc", "CNRB0000011");
reqMap.put("accountName", "Raghavdra");
reqMap.put("phone", "+9663561112");
reqMap.put("email", "raghavdraa1234@gmail.com");
	*/
    public function aa(){
        return 1;
    }
    /**
     * 代收接口
     * @param string $channelName   //通道代码 NB DC CC MW UPI EMI(维护中) OM(维护中) DAP(维护中)
     * @param string $orderNo       //订单号
     * @param string $payMoney      //订单金额(单位为元的整数)
     * @param string $productDetail //商品描述
     * @param string $name          //客户名称
     * @param string $email         //邮箱
     * @param string $phone         //手机号
     * @param string $redirectUrl   //成功页面返回地址
     * @param string $errorReturnUrl//失败页面返回地址
     * @param string $notifyUrl     //异步回调地址
     */
    public function ds_pay($channelName="UPI",$orderNo="",$payMoney="102",$productDetail="1234",$name="asdrsad",$email="khas@foxmail.com"
        ,$phone="+9663561112",$redirectUrl="www.baidu.com",$errorReturnUrl="www.baidu.com",$notifyUrl="www.baidu.com")
    {
      
        $data['merNo'] = 'ZT21010615840';
        $data['channelName'] = $channelName;
        $data['orderNo'] =  $orderNo ;
        $data['payMoney'] = $payMoney;
        $data['productDetail'] = $productDetail;
        $data['name'] = $name;
        $data['email'] = $email;
        $data['phone'] = $phone;
        $data['redirectUrl'] = $redirectUrl;
        $data['errorReturnUrl'] = $errorReturnUrl;
        $data['notifyUrl'] = $notifyUrl;
        

        //验签
        $data = $this->encryptedRsa($data,1);
		
        //请求接口
        $response = $this->curl($data,$this->ds_url);
        return $response;
    }

    /**
     * 代收订单查询
     * @param string $orderNo
     * @return array
     */
    public function ds_query_order($orderNo="")
    {
        $data['merNo'] = $this->ds_UID;
        $data['orderNo'] = $orderNo;
        //验签
        $data = $this->encryptedRsa($data,1);
        //请求接口
        $response = $this->curl($data,$this->ds_query_url);
        return $response;
    }


    /**
     * 代付接口
     * @param string $channelName       //通道代码 UPI IFSC
     * @param string $email             //邮箱
     * @param string $phone             //手机号
     * @param string $beneficiaryType   //账户类型 bank_account upi
     * @param string $accountName       //开户人姓名
     * @param string $paymentCode       //付款方式 IMPS NEFT RTGS UPI
     * @param string $amount            //金额
     * @param string $narration         //备注
     * @param string $notifyUrl         //回调地址
     * @param string $bankCardNo        //卡号  beneficiaryType为bank_account时必传
     * @param string $ifsc              //银行代码 beneficiaryType为bank_account时必传
     * @param string $upiHandle         //UPI账户 beneficiaryType为upi时必传
     */
    public function df_pay($orderNo="",$channelName="UPI",$email="raghavdraa1234@gmail.com",$phone="+9663561112",$beneficiaryType="bank_account",$accountName="Raghavdra",$paymentCode="UPI",$amount="20",$narration="123",$notifyUrl="www.baidu,com",$bankCardNo="05441086",$ifsc="CNRB0000011",$upiHandle="")
    {
        $data['merNo'] = 'ZT21010615739';
        $data['channelName'] = $channelName;
        $data['orderNo'] = $orderNo;
        $data['email'] = $email;
        $data['phone'] = $phone;
        $data['beneficiaryType'] = $beneficiaryType;
        $data['accountName'] = $accountName;
        $data['paymentCode'] = $paymentCode;
        $data['amount'] = $amount;
        $data['narration'] = $narration;
        $data['notifyUrl'] = $notifyUrl;
        if($beneficiaryType == "upi"){
            $data['upiHandle'] = $upiHandle;
        }elseif ($beneficiaryType == "bank_account"){
            $data['bankCardNo'] = $bankCardNo;
            $data['ifsc'] = $ifsc;
        }else{
            return ['code'=>-1,'msg'=>'账户类型不正确'];
        }

        //验签
        $data = $this->encryptedRsa($data,2);

		return $data;
        /*//请求接口
        $response = $this->curl($data,$this->df_url);
        return $response;*/
    }

    public function df_query_order($orderNo="")
    {
        $data['merNo'] = $this->df_UID;
        $data['orderNo'] = $orderNo;
        //验签
        $data = $this->encryptedRsa($data,2);
        //请求接口
        $response = $this->curl($data,$this->df_query_url);
        return $response;
    }

    /**
     * 签名
     * @param $data
     * @param $pay_type 代收为1 代付为2
     * @param string $type
     * @return mixed
     */
    public function encryptedRsa($data, $pay_type,$type = 'array') {
        
        ksort($data);
        $buff = ""; //拼接字符串
        foreach ($data as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        if ($type == 'string') {
            $buff = $data;
        }
    
        $private_key = $this->privateKey($pay_type); //秘钥

        $pi_key = openssl_pkey_get_private($private_key); //这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id
        $str = '';
        foreach (str_split($buff, 117) as $chunk) {
            @openssl_private_encrypt($chunk, $encryptedTemp, $pi_key);  //私钥加密
            $str .= $encryptedTemp;
        }

        $encrypted = base64_encode($str); //加密后的内容通常含有特殊字符，需要编码转换下，在网络间通过url传输时要注意base64编码是否是url安全的
        $data['sign'] = $encrypted;
        return $data;
    }
    /**
     * 返回私钥
     * @return string
     */
    public function privateKey($type=1) {
        if($type == 1){
            $privateKeyString = "-----BEGIN PRIVATE KEY-----\n" .
                wordwrap($this->ds_KEY, 64, "\n", true) .
                "\n-----END PRIVATE KEY-----";
        }elseif($type == 2){
            $privateKeyString = "-----BEGIN PRIVATE KEY-----\n" .
                wordwrap($this->df_KEY, 64, "\n", true) .
                "\n-----END PRIVATE KEY-----";
        }

        return $privateKeyString;
    }
    /**
     * 返回公钥
     */
    public function publicKey($type=1) {
        if($type == 1){//
            $pubPem = chunk_split($this->ds_PT_KEY, 64, "\n");
            $pubPem = "-----BEGIN PUBLIC KEY-----\n" . $pubPem . "-----END PUBLIC KEY-----\n";
            return $pubPem;
        }elseif($type == 2){
            $pubPem = chunk_split($this->df_PUB_KEY, 64, "\n");
            $pubPem = "-----BEGIN PUBLIC KEY-----\n" . $pubPem . "-----END PUBLIC KEY-----\n";
            return $pubPem;
        }
    }
	/**
     * 公钥解密参数，校队。
     */
	public function dencryptedRsa($data,$pub_key='') 
	{
        $decrypted = "";
        $pub_key=$this->publicKey(1);
        $pub_id = openssl_pkey_get_public($pub_key);
        $key_len = openssl_pkey_get_details($pub_id)['bits'];
        $part_len = $key_len / 8;
        $base64_decoded = base64_decode($data['sign']);
        $parts = str_split($base64_decoded, $part_len);
 
        foreach ($parts as $part) {
            $decrypted_temp = '';
            openssl_public_decrypt($part, $decrypted_temp, $pub_key);
            $decrypted .= $decrypted_temp;
        }
        return $decrypted;

	}

    /**
     * @param $data
     * @param string $url
     * @param int $timeout
     * @return array
     */
    private function curl($data,$url="",$timeout=100)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $ret = curl_exec($ch);
        curl_close($ch);
        $return_data = json_decode($ret, true);
        $return = [
            'respose' => $ret,//$ret
            'data' => $return_data,
            'post' => $data,
            'url' => $url,
        ];
        return $return;
    }
}

?>
