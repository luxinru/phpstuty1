<?php /*a:2:{s:56:"D:\phpstudy_pro\WWW\application\index\view\my\index.html";i:1615915022;s:60:"D:\phpstudy_pro\WWW\application\index\view\public\floor.html";i:1615775208;}*/ ?>



<!DOCTYPE html><!-- saved from url=(0035)http://qiang6-www.baomiche.com/#/My --><html data-dpr="1" style="font-size: 37.5px;"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1,maximum-scale=1,minimum-scale=1"><title>my</title><link href="/static_new6/css/app.7b22fa66c2af28f12bf32977d4b82694.css" rel="stylesheet"><link rel="stylesheet" href="/static_new/css/public.css"><script charset="utf-8" src="/static_new/js/jquery.min.js"></script><script charset="utf-8" src="/static_new/js/dialog.min.js"></script><script charset="utf-8" src="/static_new/js/common.js"></script><style type="text/css" title="fading circle style">        .circle-color-8 > div::before {
            background-color: #ccc;
        }
        .login_nav li a{
            display: block;
            width: 100%;
        }
    </style></head><body style="font-size: 12px;"><div id="app"><div data-v-d5f15326="" class="main"><div data-v-d5f15326="" class="header"><div data-v-d5f15326="" class="img"><a data-v-d5f15326="" href="" class=""><img data-v-d5f15326=""
                         src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACwAAAAsCAMAAAApWqozAAAAP1BMVEVHcExCQkJCQkJDQ0NCQkJCQkJCQkJDQ0NCQkJCQkJCQkJCQkJCQkJFRUVCQkJDQ0NCQkJCQkJCQkJCQkJDQ0NLP8oPAAAAFHRSTlMAzyoRvkD5X+/eGrJyCYM4T5Ok5lh/mIcAAAE3SURBVHhezZTHjsQwDEPd4hK3lPf/37qLxWLg8SjJdd6R4kGkBatvxZScvD9d68/WzAu93XubZ8TZa6t1ADkYW812RCCZK2/VgHuN7Q7EK3cG2igsEdIqegsQprgRsuRdI7RZXDxInRygP9UdktCEhy7IJ2zSxlpsXtraQRCjgK+z6EEuScMySX0MMkcskxTgEJzypEGTzctn8jzmmxPGSUrQlUyco5uhIKHUMgtZXVAgru/10K/M9QRdxyUo6pLux/E+LSHdjfCiMnUsJIJVd+gh0gnrk9lI5/l4pgXOuz0yuJdgI6Sw/LKFN7Y/zfFWwMYDuxrYIjf46XptOZxzGkgvPKRfMTdz2f42/Dr3b7VD7P9eDVrdYU/we6/VtAh+UbeYIakP6oHV8U9a1DN919GnI6hv5QcUoxqZt07UFQAAAABJRU5ErkJggg=="
                         alt=""></a><a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`/index/ctrl/set`" class=""><img data-v-d5f15326=""
                         src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACwAAAAsCAMAAAApWqozAAAALVBMVEVHcEw+Pj5CQkJCQkJCQkJDQ0NDQ0NCQkJCQkJCQkJCQkJDQ0NDQ0NDQ0NEREQBqyziAAAAD3RSTlMAFJHuc/9P2yq/1khttlZE4SfSAAAA00lEQVR42s2V2xLEEAxAN0RLi///3EW3DbOadHhpXtrh4LjF570BSmsFz1jUJoXGB+iyml+si4Bal7Ft37f8dZaVLQI+//sic6+OVE8tkZENtnIKpN6VbUv76iRbB6n/sQruJt3SupGFiBihUdcVnNo2dedUqboPe3OFZ2BinVKu0DxsM2qvtbQsHBJ7FiY6cDAcvdEowMDx6Ji6jgyMxqhmq3AUljXmJygvnbwp09stH6TRIyof/sFrJV/YoVQgJ5m59CUnRjnlTiZz+ZmQH6DXxhfGzQxZusjycwAAAABJRU5ErkJggg=="
                         alt=""></a></div><div data-v-d5f15326="" class="info"><img data-v-d5f15326="" src="<?php echo htmlentities($info['headpic']); ?>" onerror="this.src='/public/img/head.png'" onclick="location.href='/index/my/headimg.html';" alt="" class="headerImg"><div data-v-d5f15326="" class="name"><strong data-v-d5f15326=""><?php echo htmlentities($info['username']); ?></strong><em data-v-d5f15326="">LV<?php echo htmlentities($info['level'] + 1); ?></em><small data-v-d5f15326=""><?php echo htmlentities(app('lang')->get('yqm')); ?>: <?php echo htmlentities($info['invite_code']); ?></small></div></div><div data-v-d5f15326="" class="balance"><p data-v-d5f15326=""><span data-v-d5f15326=""><?php echo htmlentities(app('lang')->get('Account_balance')); ?></span><em data-v-d5f15326=""><small data-v-d5f15326=""><?php echo htmlentities($info['balance']); ?></small></em></p><div class="button-wrapper"><button data-v-d5f15326="" onclick="window.location.href=`/index/ctrl/deposit`"  class=""><?php echo htmlentities(app('lang')->get('withdraw')); ?></button><button data-v-d5f15326="" onclick="window.location.href=`/index/ctrl/recharge`"  class=""><?php echo htmlentities(app('lang')->get('Recharge')); ?></button></div></div></div><style>            .login_nav li{
                height: 2.6rem;
            }
            body {
                background: #f6f6f8 !important;
            }
            .header {
                background: none !important;
                overflow: initial !important;
            }
            .info {
                display: flex !important;
                align-items: center !important;
            }
            .info img {
                height: 2rem !important;
                width: 2rem !important;
                overflow: hidden !important;
                border-radius: 50% !important;
            }
            .info .name strong{
                font-size: 0.7rem !important;
                color: #000000 !important;
                font-weight: normal !important;
            }
            .info .name em {
                color: #dc4f54!important;
                border: 1px solid #dc4f54 !important;
                width: 1.64rem !important;
                height: 0.6rem !important;
                padding: .05rem !important;
                color: #dc4f54 !important;
                border: 1px solid #dc4f54 !important;
                border-radius: 0.2rem !important;
                background: none !important;
            }
            .info .name small {
                font-size: .4rem !important;
            }
            .balance {
                display: flex !important;
                justify-content: space-between;
                width: 95% !important;
                background: #fff !important;
                border-radius: 0.3rem !important;
            }
            .balance small {
                color: #000 !important;
                font-weight: normal !important;
            }
            .balance span {
                color: #000 !important;
                white-space: nowrap !important;
            }
            .balance .button-wrapper {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-left: .3rem;
            }
            .balance button {
                color: #000 !important;
                background: #f6f6f8 !important;
                box-shadow: 0 0 4px #aaa !important;
                margin: 0 .3rem 0 0 !important;
                float: none !important;
            }
        </style><ul data-v-d5f15326="" class="login_nav"><li data-v-d5f15326=""><a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`/index/ctrl/set`" class="dd"><div class="wrapper"><div class="p p1"><img data-v-d5f15326=""
                            src="/image-self/my_index/person.png"
                            alt=""></div></div><p data-v-d5f15326=""><?php echo htmlentities(app('lang')->get('Personalinformation')); ?></p></a></li><li data-v-d5f15326=""><a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`/index/order/index`" class=""><div class="wrapper"><div class="p p2"><img data-v-d5f15326=""
                            src="/image-self/my_index/order.png"
                            alt=""></div></div><p data-v-d5f15326=""><?php echo htmlentities(app('lang')->get('Graborderrecord')); ?></p></a></li><li data-v-d5f15326=""><a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`/index/my/caiwu`" class=""><div class="wrapper"><div class="p p3"><img data-v-d5f15326=""
                            src="/image-self/my_index/accountbook.png"
                            alt=""></div></div><p data-v-d5f15326=""><?php echo htmlentities(app('lang')->get('Accountdetails')); ?></p></a></li><li data-v-d5f15326=""><a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`/index/ctrl/recharge_admin`"   class=""><div class="wrapper"><div class="p p4"><img data-v-d5f15326=""
                            src="/image-self/my_index/recharge.png"
                            alt=""></div></div><p data-v-d5f15326=""><?php echo htmlentities(app('lang')->get('Rechargerecord')); ?></p></a></li><li data-v-d5f15326=""><a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`/index/ctrl/deposit_admin`"  class=""><div class="wrapper"><div class="p p5"><img data-v-d5f15326=""
                            src="/image-self/my_index/withdrawal.png"
                            alt=""></div></div><p data-v-d5f15326=""><?php echo htmlentities(app('lang')->get('Withdrawalsrecord')); ?></p></a></li><li data-v-d5f15326=""><a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`/index/my/invite`" class=""><div class="wrapper"><div class="p p6"><img data-v-d5f15326=""
                            src="/image-self/my_index/present.png"
                            alt=""></div></div><p data-v-d5f15326=""><?php echo htmlentities(app('lang')->get('Accountdetails22')); ?></p></a></li><li data-v-d5f15326=""><a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`/index/my/msg`" class=""><div class="wrapper"><div class="p p7"><img data-v-d5f15326=""
                            src="/image-self/my_index/information.png"
                            alt=""></div></div><p data-v-d5f15326=""><?php echo htmlentities(app('lang')->get('systeminformation')); ?></p></a></li><li data-v-d5f15326=""><a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`/index/ctrl/junior`" class=""><div class="wrapper"><div class="p p8"><img data-v-d5f15326=""
                            src="/image-self/my_index/report.png"
                            alt=""></div></div><p data-v-d5f15326=""><?php echo htmlentities(app('lang')->get('Startorders22')); ?></p></a></li><li data-v-d5f15326=""><a data-v-d5f15326="" href="javascript:void(0)" onclick="window.location.href=`/index/my/downsoft`" class=""><div class="wrapper"><div class="p p8" style="background-color:#2A9987;"><img data-v-d5f15326=""
                            src="/image-self/download/ef6d275d92bcf3fa916d7645c859301.png"
                            alt=""></div></div><p data-v-d5f15326=""><?php echo lang('download App'); ?></p></a></li><!--   <li data-v-d5f15326="">--><!--        <img data-v-d5f15326=""--><!--            src="/static_new6/img/l5.png"--><!--            alt="" width="50" height="80">--><!--        <p data-v-d5f15326="">--><!--        <select id="qwerty">--><!--        <option value="zh-cn" <?php echo $lang=='zh-cn' ? 'selected' : ''; ?>>中文</option>--><!--        <option value="en-ww"  <?php echo $lang=='en-ww' ? 'selected' : ''; ?>>English</option>--><!--         <option value="en-in"  <?php echo $lang=='en-in' ? 'selected' : ''; ?>>हिंदीName</option>--><!--    </select>--><!--        </p>--><!--</li>--></ul><div data-v-d5f15326="" class="LoginOut"><button data-v-d5f15326="" class="tabs_btn1" style="background-color: #255cbd;"><?php echo htmlentities(app('lang')->get('signout')); ?></button></div><style>            .login_nav {
                padding-bottom: 0.56rem;
            }
            .login_nav li {
                border: none !important;
            }
            .login_nav .wrapper {
                display: flex;
                justify-content: center;
            }
            .login_nav .p {
                display: flex;
                justify-content: center;
                align-items: center;
                width: 1.6rem;
                height: 1.6rem;
                border-radius: 0.4rem;
            }
            .login_nav .p1 {
                background: #347DFF;
            }
            .login_nav .p2 {
                background: #FA6633;
            }
            .login_nav .p3 {
                background: #891616;
            }
            .login_nav .p4 {
                background: #9CA0FA;
            }
            .login_nav .p5 {
                background: #75C07E;
            }
            .login_nav .p6 {
                background: #50CAEF;
            }
            .login_nav .p7 {
                background: #FA4115;
            }
            .login_nav .p8 {
                background: #2A3D70;
            }
            .LoginOut {
                margin-bottom: 1rem;
            }
        </style><div data-v-8755e8fe="" data-v-eebac136="" class="footer"  style="background: none;width: 100%;height: 1.7rem;background-color: white;"><ul data-v-8755e8fe=""><li onclick="window.location.href='<?php echo url('index/home'); ?>'" data-v-8755e8fe=""><?php
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

</style></div></div><script>$(function() {
        $('.footer li').eq(4).addClass("on");
        $(".qwerty").val("pxx");
        $('#qwerty').change(function(){
            var lang=$('#qwerty').val();
            $.ajax({
                url:'/index/index/lang',
                type:'post',
                data:{type:lang},
                success:function(data){
                    // settimeout(function(){
                        location.reload();
                    // },500);
                }
            });
        }
			
				
			);
    });
    $(function() {
        $('.footer li').eq(4).addClass("on");

    })

    $('.tabs_btn1').click(function () {
        $(document).dialog({
            type: 'confirm',
            titleText: "<?php echo htmlentities(app('lang')->get('Areout')); ?>",
            autoClose: 0,
          buttonTextConfirm:'<?php echo htmlentities(app('lang')->get('ok')); ?>',
         buttonTextCancel:'<?php echo htmlentities(app('lang')->get('no')); ?>',
            onClickConfirmBtn: function () {
                window.location.href="<?php echo url('user/logout'); ?>";
            },
            onClickCancelBtn: function () {

            }
        });
//  $('document').dialog({
//                 autoOpen: false,
//                 width: 600,
//                 modal: true,
//                 title: 'cs',
//                 buttons: {
//                     "确定新增": function () {
//                         alert('确定');
//                     },                   
//                     "关闭": function () {
//                         $(this).dialog("close");
//                     }
//                 }
//               });
        
    });
    
</script></body></html>