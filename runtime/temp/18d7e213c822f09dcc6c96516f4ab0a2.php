<?php /*a:2:{s:59:"E:\phpstudy_pro\WWW\application\index\view\order\index.html";i:1642511671;s:60:"E:\phpstudy_pro\WWW\application\index\view\public\floor.html";i:1643022066;}*/ ?>
<!DOCTYPE html><!-- saved from url=(0043)http://qiang6-www.baomiche.com/#/GrabRecord --><html data-dpr="1" style="font-size: 37.5px;"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1,maximum-scale=1,minimum-scale=1"><title><?php echo htmlentities(app('lang')->get('Orderrecord')); ?></title><link href="/static_new6/css/app.7b22fa66c2af28f12bf32977d4b82694.css" rel="stylesheet"><link rel="stylesheet" href="/static_new/css/public.css"><script charset="utf-8" src="/static_new/js/jquery.min.js"></script><script charset="utf-8" src="/static_new/js/dialog.min.js"></script><script charset="utf-8" src="/static_new/js/common.js"></script><link rel="stylesheet" href="/public/js/layer_mobile/need/layer.css"><script src="/public/js/layer_mobile/layer.js"></script><style type="text/css" title="fading circle style">        .circle-color-9 > div::before {
            background-color: #ccc;
        }

        /* 通用分页 */
        .pagination-container {
            line-height: 40px;
            text-align: right;
            display: none;
        }
        /*.pagination-container > span {*/
        /*    color: #666;*/
        /*    font-size: 9pt;*/
        /*}*/
        /*.pagination-container > ul {*/
        /*    float: right;*/
        /*    display: inline-block;*/
        /*    margin: 0;*/
        /*    padding: 0;*/
        /*}*/
        /*.pagination-container > ul > li {*/
        /*    z-index: 1;*/
        /*    display: inline-block;*/
        /*}*/
        /*.pagination-container > ul > li > a, .pagination-container > ul > li > span {*/
        /*    color: #333;*/
        /*    width: 33px;*/
        /*    height: 30px;*/
        /*    border: 1px solid #dcdcdc;*/
        /*    display: inline-block;*/
        /*    margin-left: -1px;*/
        /*    text-align: center;*/
        /*    line-height: 28px;*/
        /*}*/
        .pagination-container > ul > li > span {
            background: #dcdcdc;
            cursor: default;
        }
        .van-tab--active span{
            color: #ff9a2c;
        }
        .circle-color-23 > div::before {
            background-color: #ccc;
        }
        .dialog {
            position: fixed;
            left: 0;
            top: 0;
            z-index: 10001;
            width: 100%;
            height: 100%;
        }
        .van-tab{
            background-color: #347DFF;
            /* border-radius: 9%; */
        }
        .van-tabs_nav{

        }
        .records .van-tab {
            color: white;
        }
        .van-tab--active span {
            color: #347DFF;
            width: 60%;
            background-color: white;
            height: 20%;
            padding: 0.5rem;
            border-radius: 9%;
        }
        .records .records_box .records_tabs .records_tabs_box[data-v-35ad745e] {
            background: none;
        }

    </style></head><body style="font-size: 12px;"><div id="app"><div class="mint-indicator" id="load" style="display: block;"><div class="mint-indicator-wrapper" style="padding: 20px;z-index:999"><span class="mint-indicator-spin"><div class="mint-spinner-fading-circle circle-color-23" style="width: 32px; height: 32px;"><div class="mint-spinner-fading-circle-circle is-circle2"></div><div class="mint-spinner-fading-circle-circle is-circle3"></div><div class="mint-spinner-fading-circle-circle is-circle4"></div><div class="mint-spinner-fading-circle-circle is-circle5"></div><div class="mint-spinner-fading-circle-circle is-circle6"></div><div class="mint-spinner-fading-circle-circle is-circle7"></div><div class="mint-spinner-fading-circle-circle is-circle8"></div><div class="mint-spinner-fading-circle-circle is-circle9"></div><div class="mint-spinner-fading-circle-circle is-circle10"></div><div class="mint-spinner-fading-circle-circle is-circle11"></div><div class="mint-spinner-fading-circle-circle is-circle12"></div><div class="mint-spinner-fading-circle-circle is-circle13"></div></div></span><span class="mint-indicator-text"style=""><?php echo htmlentities(app('lang')->get('Loading')); ?>...</span></div><div class="mint-indicator-mask"></div></div><div data-v-35ad745e="" class="main" style="padding-bottom: 55px;"><div data-v-35ad745e="" class="records"><!-- <div data-v-35ad745e="" class="jianbian"></div><div data-v-35ad745e="" class="records_top"><div data-v-35ad745e="" class="records_top1"><p data-v-35ad745e=""><?php echo htmlentities(app('lang')->get('Orderrecord')); ?></p><p data-v-35ad745e="" style="font-size: 0.56rem;"><?php echo htmlentities($balance); ?></p></div><div data-v-35ad745e="" class="records_top1"><p data-v-35ad745e=""><?php echo htmlentities(app('lang')->get('Orderrecord')); ?><?php echo htmlentities(app('lang')->get('Officialoffer')); ?></p><p data-v-35ad745e=""><?php echo htmlentities(app('lang')->get('Account_balance')); ?></p></div></div> --><div data-v-35ad745e="" class="records_box"><div data-v-35ad745e="" class="mint-loadmore"><div class="mint-loadmore-content"><div class="mint-loadmore-top"><!----><span class="mint-loadmore-text"><?php echo lang('Pull Refresh'); ?></span></div><div data-v-35ad745e="" class="van-tabs van-tabs--line"><div data-v-35ad745e=""><div data-v-35ad745e="" class="van-sticky"><div data-v-35ad745e="" class="van-tabs__wrap van-hairline--top-bottom"><div data-v-35ad745e="" role="tablist" class="van-tabs__nav van-tabs__nav--line" ><!--<div data-v-35ad745e="" role="tab" class="van-tab van-ellipsis <?php echo !$status ? 'van-tab--active' : ''?>" onclick="window.location.href='/index/order/index'"><span data-v-35ad745e="" class="van-tab__text">全部</span></div>--><div data-v-35ad745e="" role="tab" style=" border-top-left-radius: 8px;border-bottom-left-radius: 8px;" class="van-tab van-ellipsis <?php echo $status == -1 ? 'van-tab--active' : ''?>" onclick="window.location.href='/index/order/index.html?status=-1'"><span data-v-35ad745e="" class="van-tab__text"><?php echo lang('Pendant'); ?></span></div><div data-v-35ad745e="" role="tab" style="" class="van-tab van-ellipsis <?php echo $status == 1 ? 'van-tab--active' : ''?>" onclick="window.location.href='/index/order/index.html?status=1'"><span data-v-35ad745e="" class="van-tab__text"><?php echo lang('Concluded'); ?></span></div><div data-v-35ad745e="" role="tab" style=" border-top-right-radius: 8px;border-bottom-right-radius: 8px;"  class="van-tab van-ellipsis <?php echo $status == 5 ? 'van-tab--active' : ''?>" onclick="window.location.href='/index/order/index.html?status=5'"><span data-v-35ad745e="" class="van-tab__text"><?php echo lang('Freezen'); ?></span></div><!--<div data-v-35ad745e="" class="van-tabs__line"
                                                 style="background-color: rgb(255, 154, 44); transition-duration: 0.3s;"></div>--></div></div></div></div><div data-v-35ad745e="" class="van-tabs__content"><div data-v-35ad745e="" role="tabpanel" class="van-tab__pane"><div data-v-35ad745e="" role="feed" class="van-list"><?php if($list): if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;
                                        $img= '/image/succ.png';
                                        if(cookie('lang') == 'th-th'){
                                            if($v['status']==0) $img= '/image/t6.png';
                                        }else{
                                            if($v['status']==0) $img= '/image/dai.png';
                                        }
                                        
                                        if($v['status']==1) $img= '/image/succ.png';
                                        if($v['status']==5) $img= '/image/dongjie.png';
                                        ?><!--item--><div data-v-35ad745e=""><div data-v-35ad745e="" class="records_tabs"><div data-v-35ad745e="" class="records_tabs_box"><div data-v-35ad745e="" class="records_tabs_box_top"><?php echo htmlentities(app('lang')->get('Collectiontime')); ?>：<?php echo date("Y-m-d h:i:s",$v['addtime']);?><br><?php echo htmlentities(app('lang')->get('Ordernumber')); ?>：<?php echo htmlentities($v['id']); ?></div><!--  src="<?php echo htmlentities($img); ?>"--><img data-v-35ad745e="" class="records_tabs_box_img"><div data-v-35ad745e="" class="records_tabs_box_des"><div data-v-35ad745e="" class="tabs_box_des_img"><img data-v-35ad745e="" src="<?php echo htmlentities($v['goods_pic']); ?>"></div><div data-v-35ad745e="" class="tabs_box_des_r"><p data-v-35ad745e="" class="tabs_box_des_r_tit"><?php echo htmlentities($v['goods_name']); ?></p><div data-v-35ad745e="" class="tabs_box_des_r_pic"><p data-v-35ad745e=""><?php echo lang('yuan'); ?>:  <?php echo htmlentities($v['goods_price'] * lang('duna')); ?></p><?php if($v['status'] == 5) {?><p data-v-35ad745e="" style="color: red;"><?php echo htmlentities(app('lang')->get('Freezing')); ?></p><?php } ?><p data-v-35ad745e="">x <?php echo htmlentities($v['goods_count']); ?></p></div></div></div><div data-v-35ad745e="" class="tabs_box_des_r_pic"><p data-v-35ad745e="" class="gray"><?php echo htmlentities(app('lang')->get('Ordertotal')); ?></p><p data-v-35ad745e=""><?php echo lang('yuan'); ?>:  <?php echo htmlentities($v['num'] * lang('duna')); ?></p></div><div data-v-35ad745e="" class="tabs_box_des_r_pic"><p data-v-35ad745e="" class="gray"><?php echo htmlentities(app('lang')->get('commission')); ?></p><p data-v-35ad745e=""><?php echo lang('yuan'); ?>:  <?php echo htmlentities($v['commission'] * lang('duna')); ?></p></div><div data-v-35ad745e="" class="tabs_box_des_r_pic"><p data-v-35ad745e="" class="txt"><?php echo htmlentities(app('lang')->get('Refundamount')); ?></p><p data-v-35ad745e="" class="txt1" style="color: #347dfe;"><?php echo lang('yuan'); ?>:
                                                                 <?php echo ($v['commission'] + $v['num']) * lang('duna')  ?></p></div><?php if($v['status'] == 5) {?><div data-v-4c2dcb20="" class="tabs_box_des_r_pic"><button data-v-35ad745e="" class="redb J_jiedong" style="padding: 3px 10px;border-radius: 5px;"><?php echo htmlentities(app('lang')->get('Applyforthawing')); ?></button></div><?php } ?><button data-v-35ad745e="" onclick="tijiao('<?php echo htmlentities($v['id']); ?>')" class="redb" style="display: <?php echo $v['status']==0 ? 'block':'none'; ?>;padding: 3px 10px;border-radius: 5px;background: #347dfe;color: #fff;"><?php echo htmlentities(app('lang')->get('SubmitOrder')); ?></button></div><!----></div></div><!--item--><?php endforeach; endif; else: echo "" ;endif; else: ?><?php endif; if(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty())): ?><span class="notdata"><?php echo htmlentities(app('lang')->get('Thereisnorecordonispage')); ?></span><?php else: ?><?php echo (isset($pagehtml) && ($pagehtml !== '')?$pagehtml:''); ?><?php endif; ?><div data-v-35ad745e="" class="van-list__placeholder"><div data-v-35ad745e="" class="no_more" style="display: none;"><?php echo htmlentities(app('lang')->get('Thereisnorecordonispage')); ?></div></div></div></div></div></div></div></div></div></div><div data-v-4c2dcb20="" class="" style="" id="orderDetail"><div data-v-4c2dcb20="" class="van-overlay" style="z-index: 1000;padding: 0 10px;display:none ;overflow-y:scroll; overflow-x:hidden;height：100rem;"><div data-v-4c2dcb20="" class="grabSuccess" style="width: 100%;background: #347dfe;color: white;"><div data-v-4c2dcb20="" class="title" style="height: 1.586667rem;"><img data-v-4c2dcb20=""
                             src="/image/ppcg.png"
                             alt=""></div><div data-v-4c2dcb20="" class="records_tabs_box"><div data-v-4c2dcb20="" class="records_tabs_box_top"><?php echo htmlentities(app('lang')->get('Collectiontime')); ?>：<span id="otime">2020-03-17T17:11:41</span><br><?php echo htmlentities(app('lang')->get('Ordernumber')); ?>：<span id="oid">202003171711414080</span></div><img data-v-4c2dcb20=""
                             src="/image/succ.png"
                             class="records_tabs_box_img"><div data-v-4c2dcb20="" class="records_tabs_box_des"><div data-v-4c2dcb20="" class="tabs_box_des_img"><img data-v-4c2dcb20="" alt="" id="oimg" src="/static_new6/img/wenhao.png"></div><div data-v-4c2dcb20="" class="tabs_box_des_r"><p data-v-4c2dcb20="" class="tabs_box_des_r_tit" id="otitle"><?php echo htmlentities(app('lang')->get('Gettingproductinformation')); ?>～～～
                                </p><div data-v-4c2dcb20="" class="tabs_box_des_r_pic"><p data-v-4c2dcb20="" id="oprice"><?php echo lang('yuan'); ?>: ???</p><p data-v-4c2dcb20="" id="onum">x ?</p></div></div></div><div data-v-4c2dcb20="" class="tabs_box_des_r_pic"><p data-v-4c2dcb20="" class="gray"><?php echo htmlentities(app('lang')->get('Ordertotal')); ?></p><p data-v-4c2dcb20="" id="ototal"><?php echo lang('yuan'); ?>:  ???</p></div><div data-v-4c2dcb20="" class="tabs_box_des_r_pic"><p data-v-4c2dcb20="" class="gray"><?php echo htmlentities(app('lang')->get('commission')); ?></p><p data-v-4c2dcb20=""><?php echo lang('yuan'); ?>:  <span id="yongjin">???</span></p></div><div data-v-4c2dcb20="" class="tabs_box_des_r_pic"><p data-v-4c2dcb20="" class="txt"><?php echo htmlentities(app('lang')->get('Refundamount')); ?></p><p data-v-4c2dcb20="" class="txt1" style="color: #347dfe;"><?php echo lang('yuan'); ?>:  <span id="yuji">???</span></p></div></div><div data-v-4c2dcb20="" class="tabs_btn"><div data-v-4c2dcb20="" class="tabs_btn1" style="border: 1px #fff solid;
    background: white;color:#000;" ><?php echo htmlentities(app('lang')->get('Notsubmit')); ?></div><div data-v-4c2dcb20="" class="tabs_btn2" style="background: white;
    color: #347dfe;"><?php echo htmlentities(app('lang')->get('Submitnow')); ?></div></div></div><img data-v-4c2dcb20=""
                     src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAMAAACdt4HsAAAAP1BMVEVHcEz///////////////////////////////////////////////////////////////////////////////9KjZoYAAAAFXRSTlMAEFN/o8rX6/sxkf9z88Q9XSlotgUhojDzAAABp0lEQVR4AaSSVwKzIBgEkfYBS1vN/a/690oitnlK1Bmq2mPRxjovIYh31uhFXSImjwGf4ll7yR5AqSnr1mJsTedUCwCflzO6EUBsj/yP2K0AYg4TXQC38iOrA6RPdVbAde7SHVC57+sCyZySBUXv+QnYIg+IG5A+6i+LYHgCE2BfH/wNpfMUvWB7L1iI5km0wL6vv2ieRpdxHzRC5wV6gP7XZ4HhJQwK/wlUbLzIhqr+0CGRF4mCP7d6EWReJkMW9RMDxxs4mD8T6LxB/z2FDMdbOGT1HY91vCfOrxxYvdPjI/jvfoS8pxHysGUB7xMVxB+X2HLA43th9OE5YJF+rKBzYA0/CoMfVg7072tYUP5cokHI49+RWLAojUpOChOfrNDKIHFSmPpMMMoic1qY+DSwykFzVpj51HDKo3FWmPls8ErQOCvMfDaICoicFiY+I8LjwOMlPN7EZ8fY4Z5dpAz7/Cp/Lb0OBAAAABiE+VsPYhB/tWPqOe+h9KX1qe6tN1gebQ/Xx/sLxivOS9Zr3ovmq+7L9uv+B8cnz0fXZ9+HZ6ZvxHfkP9pUwubvmZnSAAAAAElFTkSuQmCC"
                     alt="" class="close"></div></div><div data-v-8755e8fe=""
     data-v-eebac136=""
     class="footer"><ul data-v-8755e8fe=""><li onclick="window.location.href='<?php echo url('index/home'); ?>'"
        data-v-8755e8fe=""><img data-v-8755e8fe=""
           src="<?php echo lang('/img/home_before.png','home'); ?>"
           alt=""><img data-v-8755e8fe=""
           src="<?php echo lang('/img/home_after.png','home'); ?>"
           alt=""><span class="activeText"><?php echo lang('Home'); ?></span></li><li onclick="window.location.href='<?php echo url('order/index'); ?>'"
        data-v-8755e8fe=""><img data-v-8755e8fe=""
           src="<?php echo lang('/img/order_before.png','order'); ?>"
           alt=""><img data-v-8755e8fe=""
           src="<?php echo lang('/img/order_after.png','order'); ?>"
           alt=""><span><?php echo lang('Historic'); ?></span></li><?php
            $level = session('level') ? session('level') : 0;
            $level = $level + 1;
            $url = '/index/ctrl/team';
        ?><li onclick="window.location.href='<?=$url?>'"
        data-v-8755e8fe=""><img data-v-8755e8fe=""
           src="<?php echo lang('/img/team_before.png',[3]); ?> "
           alt=""><img data-v-8755e8fe=""
           src="<?php echo lang('/img/team_after.png',[3]); ?>"
           alt=""><span><?php echo lang('Team'); ?></span></li><li onclick="window.location.href='<?php echo url('ctrl/lixibao'); ?>'"
        data-v-8755e8fe=""><img data-v-8755e8fe=""
           src="<?php echo lang('/img/jr_before.png',[4]); ?>"
           alt=""><img data-v-8755e8fe=""
           src="<?php echo lang('/img/jr_after.png',[4]); ?>"
           alt=""><span><?php echo lang('Financial'); ?></span></li><li onclick="window.location.href='<?php echo url('my/index'); ?>'"
        data-v-8755e8fe=""><img data-v-8755e8fe=""
           src="<?php echo lang('/img/person_before.png',[5]); ?>"
           alt=""><img data-v-8755e8fe=""
           src="<?php echo lang('/img/person_after.png',[5]); ?>"
           alt=""><span><?php echo lang('Personal'); ?></span></li></ul></div></div></div><script type="text/javascript" src="/static_new6/js/manifest.3ad1d5771e9b13dbdad2.js"></script><!--<script type="text/javascript" src="/static_new6/js/vendor.832c1df07e64f8264dc6.js"></script>--><!--<script type="text/javascript" src="/static_new6/js/app.1ef0c51eb4b45b9f8d06.js"></script>--><script>    var oid,add_id='';
    $(function () {
        $('#load').hide();
    });
    $('.pagination li').click(function () {
        var class2= $(this).attr('class');
        if( class2 == 'active' || class2 == 'disabled' ) {

        }else{
            var url = $(this).find('a').attr('href');
            window.location.href = url;
        }
    });
    $(function () {
        $('.pagination-container select').attr('disabled','disabled');
    })

    $('.tabs_btn1').click(function () {
        $('#orderDetail .van-overlay').hide();
    });
    $('.close').click(function () {
        $('#orderDetail .van-overlay').hide();
    });
    function tijiao(id) {
        oid = id;
        $('#orderDetail .van-overlay').show();
        $.ajax({
            url: "/index/order/order_info",
            type: "POST",
            dataType: "JSON",
            data: { id: id },
            beforeSend: function () {
                loading = $(document).dialog({
                    type: 'notice',
                    infoIcon: '/static_new/img/loading.gif',
                    infoText: '<?php echo htmlentities(app('lang')->get('z_Loading')); ?>',
                    autoClose: 0
                });
            },
            success: function(res) {
                console.log(res);
                loading.close();
                var data = res.data;
                if (res.code == 0) {
                    $('#otime').html(data.addtime)
                    $('#oid').html(data.oid)
                    $('#otitle').html(data.goods_name)
                    $('#oimg').attr('src',data.goods_pic)
                    $('#oprice').html((data.goods_price * "<?php echo lang('duna'); ?>").toFixed(2))
                    $('#onum').html(data.goods_count)
                    $('#ototal').html('<?php echo lang('yuan'); ?>:  '+(data.num * "<?php echo lang('duna'); ?>").toFixed(2))
                    $('#yongjin').html(''+(data.commission * "<?php echo lang('duna'); ?>").toFixed(2))
                    var yuji = ( (data.commission * 1 +  data.num * 1 ) * "<?php echo lang('duna'); ?>");
                    yuji = yuji.toFixed(2);
                    $('#yuji').html(yuji)
                    oid = data.oid;
                    add_id = data.add_id;
                }

            },
            error: function(err) {
                loading.close();
                console.log(err) }
        })
    }
    var zhujiTime = "<?php echo config('deal_zhuji_time'); ?>";
    var shopTime = "<?php echo config('deal_shop_time'); ?>";

    zhujiTime = zhujiTime *1000;
    shopTime = shopTime *1000;

    //提交
    $('.tabs_btn2').click(function () {
        //--------------------------------
        var i = 0;
        layer.open({
            type: 2
            , content: '<?php echo htmlentities(app('lang')->get('Ordersubmission')); ?>',
            time: zhujiTime,
            shadeClose: false,
        });

        //--------------------------------
        var i = 0;
        layer.open({
            type: 2
            , content: '<?php echo htmlentities(app('lang')->get('Ordersubmission')); ?>',
            time: zhujiTime,
            shadeClose: false,
        });
        var timer = setInterval(function() {
            i++;
            if (i == 1) {
                layer.open({
                    type: 2
                    , content: '<?php echo htmlentities(app('lang')->get('Theremotehostisbeingallocated')); ?>',
                    time: zhujiTime,
                    shadeClose: false,
                })
            } else if (i == 2) {
                layer.open({
                    type: 2
                    , content: '<?php echo htmlentities(app('lang')->get('Waitforthemerchantsystemtorespond')); ?>',
                    time: shopTime,
                    shadeClose: false,
                })
                var ajaxT = setTimeout(function(){
                    $.ajax({
                        url: "/index/order/do_order",
                        type: "POST",
                        dataType: "JSON",
                        data: { oid:oid, add_id:add_id },
                        success: function(res) {
                            layer.closeAll();
                            console.log(res)
                            if (res.code == 0) {
                                $(document).dialog({
                                    infoText: "<?php echo htmlentities(app('lang')->get('Theordericompletednthesubmissionssuccessful')); ?>",
                                    autoClose: 2000
                                });
                                clearInterval(timer);
                                var linkTime = setTimeout(function() {
                                    location.reload()
                                }, 1800);
                            }else {
                                $(document).dialog({
                                    infoText: res.info,
                                    autoClose: 2000
                                });
                            }
                            sumbit = true;
                        },
                        error: function(err) {
                            layer.closeAll();
                            console.log(err); sumbit = true;
                        }
                    })
                },shopTime)
            }
        }, zhujiTime)


    });

    $(".J_jiedong").click(function () {
        $(document).dialog({
            infoText: "<?php echo htmlentities(app('lang')->get('Pleasecontactonlinecustomerservice')); ?>",
            autoClose: 2000
        });
    });
</script></body></html>