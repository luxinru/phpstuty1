<?php /*a:2:{s:61:"E:\phpstudy_pro\WWW\application\index\view\support\index.html";i:1642511671;s:60:"E:\phpstudy_pro\WWW\application\index\view\public\floor.html";i:1642511671;}*/ ?>
<!DOCTYPE html><!-- saved from url=(0036)http://qiang6-www.baomiche.com/#/Msg --><html data-dpr="1" style="font-size: 37.5px;"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1,maximum-scale=1,minimum-scale=1"><title><?php echo lang('客服'); ?></title><link href="/static_new6/css/app.7b22fa66c2af28f12bf32977d4b82694.css" rel="stylesheet"><script charset="utf-8" src="/static_new/js/jquery.min.js"></script><script charset="utf-8" src="/static_new/js/common.js"></script><style type="text/css" title="fading circle style">        .circle-color-9 > div::before {
            background-color: #ccc;
        }
    </style></head><body style="font-size: 12px;"><div id="app"><div data-v-6f96e2e4="" class="main"><div data-v-6f96e2e4="" data-v-54a6fdfd="" class="service_container"><div data-v-6f96e2e4="" data-v-54a6fdfd="" class="customer_service_center"><p data-v-6f96e2e4=""
                                                                                          data-v-54a6fdfd=""><?php echo lang('客服中心'); ?></p><p data-v-6f96e2e4="" data-v-54a6fdfd=""><?php echo lang('如遇到问题需要帮助请您尽快联系在线客服'); ?></p></div><div data-v-6f96e2e4="" data-v-54a6fdfd="" class="customer_type"><div data-v-6f96e2e4="" data-v-54a6fdfd="" class="service_guide"><img data-v-6f96e2e4="" data-v-54a6fdfd="" src="/static_new6/img/msg.b2e6132.png" alt=""></div><div data-v-6f96e2e4="" data-v-54a6fdfd="" class="type_List"><?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;
                       //if($v['id']>2) continue;
                    if($v['url']): ?><div data-v-6f96e2e4="" data-v-54a6fdfd="" onclick="window.location.href=`<?php echo htmlentities($v['url']); ?>`" class="type_item" style="height: 5.2rem"><?php else: ?><div data-v-6f96e2e4="" data-v-54a6fdfd="" class="type_item" style="height: 5.2rem"><?php endif; ?><p style="overflow: hidden;text-overflow: ellipsis;white-space: nowrap;" data-v-6f96e2e4="" data-v-54a6fdfd=""><?php echo lang($v['username']); ?></p><img data-v-6f96e2e4="" data-v-54a6fdfd="" src="/static_new6/img/msg.aeb1ce5.png" alt=""><?php if($v['qq']): ?><p style="margin-top:3px"><img src="/public/img/qq.png" style="width: 20px;margin:0" alt=""><span style="position: relative;top:1px"><?php echo htmlentities($v['qq']); ?></span></p><?php endif; if($v['wechat']): ?><p style="margin-top:2px"><img src="/public/img/wx.png" style="width: 20px;margin:0" alt=""><span style="position: relative;top:1px"><?php echo htmlentities($v['wechat']); ?></span></p><?php endif; ?><p style="margin-top:2px"><img src="/public/img/kefu.png" style="width: 20px;margin:0" alt=""><span style="position: relative;top:1px"><?php echo htmlentities($v['btime']); ?>-<?php echo htmlentities($v['etime']); ?></span></p></div><!--                    <div data-v-6f96e2e4="" data-v-54a6fdfd="" class="type_item">--><!--                        <p data-v-6f96e2e4="" data-v-54a6fdfd="">充值客服</p>--><!--                        <img data-v-6f96e2e4="" data-v-54a6fdfd="" src="/static_new6/img/msg.aeb1ce5.png" alt="">--><!--                    </div>--><?php endforeach; endif; else: echo "" ;endif; ?><!--<div data-v-6f96e2e4="" data-v-54a6fdfd="" class="type_item_qq">--><!--<p data-v-6f96e2e4="" data-v-54a6fdfd="">专属客服(仅处理提现与充值问题)</p>--><!--<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>--><!--<?php if($v['id']<=2) continue;?>--><!--<div data-v-6f96e2e4="" class="am-accordion-item">--><!--<div data-v-6f96e2e4="" tabindex="0" aria-expanded="false" class="am-accordion-header">--><!--<i data-v-6f96e2e4="" class="arrow"></i>--><!--<div data-v-6f96e2e4="" class="c2" style="width: 100%;">--><!--<div data-v-6f96e2e4="" style="float: left;">--><!--<img data-v-6f96e2e4="" src="data:image/png;base64,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"--><!--class="c2img">--><!--</div>--><!--<div data-v-6f96e2e4="" class="t3" style="border-bottom-width: 0.01rem; margin-left: 40px;">--><!--<div data-v-6f96e2e4="">--><!--<?php echo htmlentities($v['username']); ?>--><!--</div>--><!--<div data-v-6f96e2e4="" class="remark"><?php echo htmlentities($v['wechat']); ?></div>--><!--</div>--><!--</div>--><!--</div>--><!--</div>--><!--<?php endforeach; endif; else: echo "" ;endif; ?>--><!--</div>--></div></div></div><div data-v-8755e8fe="" data-v-eebac136="" class="footer"  style="background: none;width: 100%;height: 1.7rem;background-color: white;"><ul data-v-8755e8fe=""><li onclick="window.location.href='<?php echo url('index/home'); ?>'" data-v-8755e8fe=""><?php
                if(cookie('lang') == 'th-th' ){
            ?><img data-v-8755e8fe=""
                 src="/image/571.png"
                 alt=""><img data-v-8755e8fe=""
                 src="/image/638651.png"
                 alt=""><?php }else{ ?><!--if(cookie('lang') == 'en-us' )home--><img data-v-8755e8fe=""
                 src="<?php echo lang('/img/home_before.png','home'); ?>"
                 alt="" style="margin-top: -0.3rem;"><img data-v-8755e8fe=""
                 src="<?php echo lang('/img/home_after.png','home'); ?>"
                 alt="" style="margin-top: -0.3rem;"><span style="display: block;margin: 0 27%;" class="activeText"><?php echo lang('Home'); ?></span><?php
                }
            ?><!--<img data-v-8755e8fe=""--><!--     src="<?php echo lang('img_bar_h',[1]); ?>"--><!--     alt="">--><!--<img data-v-8755e8fe=""--><!--     src="<?php echo lang('img_bar_l',[1]); ?>"--><!--     alt="">--></li><li onclick="window.location.href='<?php echo url('order/index'); ?>'" data-v-8755e8fe=""><?php
                if(cookie('lang') == 'th-th' ){
            ?><img data-v-8755e8fe=""
                 src="/image/6561.png"
                 alt=""><img data-v-8755e8fe=""
                 src="/image/881.png"
                 alt=""><?php }else{ ?><!--if(cookie('lang') == 'en-us' )--><img data-v-8755e8fe=""
                 src="<?php echo lang('/img/order_before.png','order'); ?>"
                 alt="" style="margin-top: -0.3rem;"><img data-v-8755e8fe=""
                 src="<?php echo lang('/img/order_after.png','order'); ?>"
                 alt="" style="margin-top: -0.3rem;"><span style="display: block;margin: 0 20%;"><?php echo lang('Historic'); ?></span><?php
                }
            ?><!--<img data-v-8755e8fe=""--><!--     src="<?php echo lang('img_bar_h',[2]); ?>"--><!--     alt="">--><!--<img data-v-8755e8fe=""--><!--     src="<?php echo lang('img_bar_h',[2]); ?>"--><!--     alt="">--></li><?php
            $level = session('level') ? session('level') : 0;
            $level = $level + 1;
            $url = '/index/ctrl/team';
        ?><li onclick="window.location.href='<?=$url?>'" data-v-8755e8fe=""><?php
                if(cookie('lang') == 'th-th' ){
            ?><img data-v-8755e8fe=""
                 src="/image/5425445-1.png"
                 alt="" style="border-radius:30px;"><img data-v-8755e8fe=""
                 src="/image/5425445-1.png"
                 alt=""  style="border-radius:30px;"><?php }else{ ?><!--if(cookie('lang') == 'en-us' )--><img data-v-8755e8fe=""
                 src="<?php echo lang('/img/team_before.png',[3]); ?> "
                 alt="" style="border-radius:30px;
                               margin-top: -0.3rem;
                               width: auto;
                               height: .933333rem;
                               display: block;
                               margin-bottom: .066667rem;"><img data-v-8755e8fe=""
                 src="<?php echo lang('/img/team_before.png',[3]); ?>"
                 alt=""  style="border-radius:30px;"><span style="display: block;margin: 0 23%;text-align: center;"><?php echo lang('Team'); ?></span><?php
                }
            ?><!--<img data-v-8755e8fe=""--><!--     src="<?php echo lang('img_bar_h1'); ?>"--><!--     alt="" style="border-radius:30px;">--><!--<img data-v-8755e8fe=""--><!--     src="<?php echo lang('img_bar_h1'); ?>"--><!--     alt=""  style="border-radius:30px;">--></li><li onclick="window.location.href='<?php echo url('ctrl/lixibao'); ?>'" data-v-8755e8fe=""><?php
                if(cookie('lang') == 'th-th' ){
            ?><img data-v-8755e8fe=""
                 src="/image/61.png"
                 alt=""><img data-v-8755e8fe=""
                 src="/image/6361.png"
                 alt=""><?php }else{ ?><!--if(cookie('lang') == 'en-us' )--><img data-v-8755e8fe=""
                 src="<?php echo lang('/img/jr_before.png',[4]); ?>"
                 alt="" style="margin-top: -0.3rem;"><img data-v-8755e8fe=""
                 src="<?php echo lang('/img/jr_before.png',[4]); ?>"
                 alt="" style="margin-top: -0.3rem;"><span style="display: block;margin: 0 16%;"><?php echo lang('Financial'); ?></span><?php
                }
            ?><!--<img data-v-8755e8fe=""--><!--     src="<?php echo lang('img_bar_h',[4]); ?>"--><!--     alt="">--><!--<img data-v-8755e8fe=""--><!--     src="<?php echo lang('img_bar_l',[4]); ?>"--><!--     alt="">--></li><li onclick="window.location.href='<?php echo url('my/index'); ?>'" data-v-8755e8fe=""><?php
                if(cookie('lang') == 'th-th' ){
            ?><img data-v-8755e8fe=""
                 src="/image/9681.png"
                 alt="" style="margin-top: -0.3rem;"><img data-v-8755e8fe=""
                 src="/image/6541.png"
                 alt="" style="margin-top: -0.3rem;"><?php }else{ ?><!--if(cookie('lang') == 'en-us' )--><img data-v-8755e8fe=""
                 src="<?php echo lang('/img/person_before.png',[5]); ?>"
                 alt="" style="margin-top: -0.3rem;"><img data-v-8755e8fe=""
                 src="<?php echo lang('/img/person_after.png',[5]); ?>"
                 alt="" style="margin-top: -0.3rem;"><span style="display: block;margin: 0 17%;"><?php echo lang('Personal'); ?></span><?php
                }
            ?><!--<img data-v-8755e8fe=""--><!--     src="<?php echo lang('img_bar_h',[5]); ?>"--><!--     alt="">--><!--<img data-v-8755e8fe=""--><!--     src="<?php echo lang('img_bar_l',[5]); ?>"--><!--     alt="">--></li></ul></div><style>   /* .footer li.activeText{
      color: #347DFF;
  } */
    .footer ui li {
        float: none;
        width: auto;
    }
    .footer ul img {
        height: 0.733rem !important;
    }
    .footer span {
        text-align: center;
    }

</style></div></div><script>    $(function() {
        $('.footer li').eq(3).addClass("on");

    })
</script></body></html>