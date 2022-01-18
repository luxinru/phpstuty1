<?php

namespace app\index\controller;

use library\Controller;
use app\index\controller\SignatureHelper;

class AliCode extends Controller {

    public $accessKeyId;
    public $accessKeySecret;
    public $SignName;
    public $CodeId;

    public function _initialize() {
        $AccessKeyId     = config('app.aliyun.aliyun_access');
        $AccessKeySecret = config('app.aliyun.aliyun_key');
        $SignName        = config('app.aliyun.aliyun_sign');
        $CodeId          = config('app.aliyun.aliyun_template');

        $this->accessKeyId     = $AccessKeyId; //AccessKeyId
        $this->accessKeySecret = $AccessKeySecret; //AccessKeySecret
        $this->SignName        = $SignName; //签名
        $this->CodeId          = $CodeId; //验证码模板ID
    }

    //发送验证码
    public function code($phone, $codeNum) {
        $params["PhoneNumbers"] = $phone;
        $params["TemplateCode"] = $this->CodeId; //模板
        $params['TemplateParam'] = ["code" => $codeNum]; //验证码
        return $this->send($params, $msg);
    }

    //发送短信消息
    private function send($params = [], &$msg) {
        $params["SignName"] = $this->SignName;
        if (!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }
        $helper  = new SignatureHelper();
        $content = $helper->request(
            $this->accessKeyId,
            $this->accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action"   => "SendSms",
                "Version"  => "2017-05-25",
            ))
        );

        $d = [
            'status' => 0,
            'msg' => '',
        ];

        if ($content === false) {
            $d['msg'] = "发送异常";
            return $d;
        } else {
            $data = (array)$content;
            if ($data['Code'] == "OK") {
                $d['status'] = 1;
                $d['msg'] = "发送成功";
                return $d;
            } else {
                $d['msg'] = "发送失败 " . $data['Message'];
                return $d;
            }
        }
    }
}