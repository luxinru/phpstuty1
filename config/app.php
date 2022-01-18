<?php



return [
            // 默认语言
  'default_lang'=>'jp-jp',
//   'default_lang'=>'jp-jp',
    // 开启语言切换
    'lang_switch_on' => true,
    //语言列表
    'lang_list' => ['en-us','zh-cn','en-in','th-th'],

    // 应用调试模式

    'app_debug'                 => true,
    
    // 应用Trace调试

    'app_trace'                 => false,

    // 0按名称成对解析 1按顺序解析

    'url_param_type'            => 1,

    // 当前 ThinkAdmin 版本号

    'thinkadmin_ver'            => 'v5',



    'empty_controller'          => 'Error',



    'pwd_str'                   => '!qws6F!xffD2vx80?95jt',  //盐



    'pwd_error_num'             => 10,    //密码连续错误次数



    'allow_login_min'           => 5,     //密码连续错误达到次数后的冷却时间，分钟



    'default_filter'            => 'trim',

// 开启语言切换
    //'lang_switch_on' => true,   
    //  //开启多语言
    // 'default_lang'=>'jp-jp',
    
    //'lang_list' => ['zh-cn','en-ww'],
    
    'default_timezone'=>'Asia/Kolkata', // 设置印度时区
    
    
    'zhangjun_sms' => [

        'userid'        => '????',

        'account'       => '?????',

        'pwd'           => '????',

        'content'       => '【????】您的验证码为：',

        'min'           => 5,  //短信有效时间，分钟

    ],

    // 短信相关

    'duanxin' => [
        'duanxin_status'=>'2',
        'duanxin_type'=>'1',
    ],
    // 短信宝
    'smsbao' => [
        'user'=>'xiaoc', //账号  无需md5
        'pass'=>'qwe12', //密码
        'sign'=>'Leea', //签名
    ],
    'aliyun' => [
        'aliyun_access'=>'111',
        'aliyun_key'=>'222',
        'aliyun_sign'=>'亚马逊',
        'aliyun_template'=>'444',
    ],
    'yunzhixun' => [
        'yunzhixun_sid'=>'b9255c845de8f653',
        'yunzhixun_token'=>'67d36e6feb',
        'yunzhixun_appid'=>'3f1851bb83614',
        'yunzhixun_templateid'=>'573508',
    ],
    
    // 提现 1 银行卡 2 支付宝
    'tixian_type'=>'1',
    
    'chongzhi_type'=>'2',//1是sepro 2是其他
    //bi支付

    'bipay' => [

        'appKey' => '2ed2c4347fa70847',          //bi支付 商户appkey

        'appSecret' => 'b471e157a6bcafea74360dbc0b7ba523', //密钥

    ],

    //paysapi支付

    'paysapi' => [

        'uid'    => '362c5d32770407de2f009c54',          //bi支付 商户appkey

        'token'  => 'bedfd347390e127bd675c18dc92dfa16', //密钥

        'istype' => 1, //默认支付方式  1 支付宝  2 微信  3 比特币

    ],



    'app_only' => 0,            //只允许app访问

    'vip_sj_bu'=> 1,            //vip升级 是否补交

    'app_url'=>'https://',          //app下载地址

    'version'=>'20100106',  //版本号





    'verify'    => true,

    'mix_time'=>'5',                    //匹配订单最小延迟

    'max_time'=>'10',                   //匹配订单最大延迟

    'min_recharge'=>'100',              //最小充值金额

    'max_recharge'=>'5000',             //最大充值金额

    'deal_min_balance'=>'0',          //交易所需最小余额

    'deal_min_num'=>'9',               //匹配区间

    'deal_max_num'=>'85',               //匹配区间

    'deal_count'=>'40',                 //当日交易次数限制

    'deal_reward_count'=>'0',          //推荐新用户获得额外的交易次数

    'deal_timeout'=>'600',              //订单超时时间

    'deal_feedze'=>'60',              //交易冻结时长

    'deal_error'=>'2',                  //允许违规操作次数

    'vip_1_commission'=>'0',          //交易佣金

    'min_deposit'=>'100',               //最低提现额度

    '1_reward'=>'0',                  //直推上级推荐奖励

    '2_reward'=>'0',                 //上两级推荐奖励

    '3_reward'=>'0',                 //上三级推荐奖励

    '1_d_reward'=>'0.1',               //上级会员获得交易奖励

    '2_d_reward'=>'0.08',               //上二级会员获得交易奖励

    '3_d_reward'=>'0.05',               //上三级会员获得交易奖励

    '4_d_reward'=>'0',               //上四级会员获得交易奖励

    '5_d_reward'=>'0',                  //上五级会员获得交易奖励

    'master_cardnum'=>'',             //银行卡号

    'master_name'=>'',                              //收款人

    'master_bank'=>'',                          //所属银行

    'master_bk_address'=>'',         //银行地址
    
    'beisa1'=>'11',             //银行卡号

    'beisa2'=>'22',                              //收款人

    'beisa3'=>'33',                          //所属银行

    'beisa4'=>'44',         //银行地址
    
 'beisa5'=>'11',             //银行卡号
 'beisa6'=>'11',  
 
    'deal_zhuji_time'=>'1',         //远程主机分配时间

    'deal_shop_time'=>'1',          //等待商家响应时间

    'tixian_time_1'=>'12',           //提现开始时间

    'tixian_time_2'=>'17',          //提现结束时间



    'chongzhi_time_1'=>'0',           //充值开始时间

    'chongzhi_time_2'=>'24',          //充值结束时间





    'order_time_1'=>'0',           //抢单结束时间

    'order_time_2'=>'24',          //抢单结束时间



    //利息宝

    'lxb_bili'=>'0.005',         //利息宝 日利率

    'lxb_time'=>'1',             //利息宝 转出到余额  实际 /小时

    'lxb_sy_bili1'=>'0',         //利息宝 上一级会员收益比例

    'lxb_sy_bili2'=>'0',         //利息宝 上一级会员收益比例

    'lxb_sy_bili3'=>'0',         //利息宝 上一级会员收益比例

    'lxb_sy_bili4'=>'0',         //利息宝 上一级会员收益比例

    'lxb_sy_bili5'=>'0',         //利息宝 上一级会员收益比例

    'lxb_ru_max'=>'99999999',         //利息宝 转入最大金额

    'lxb_ru_min'=>'1',         //利息宝 转入最低金额


        'default_timezone'=>'Asia/Tokyo' ,// 设置日本时区


    'shop_status'=>'1',         //商城状态',
    'tankuang_status'=>'1',         //首页弹框',

];

