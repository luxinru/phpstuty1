<?php /*a:2:{s:57:"E:\phpstudy_pro\WWW\application\index\view\ctrl\team.html";i:1642511671;s:60:"E:\phpstudy_pro\WWW\application\index\view\public\floor.html";i:1642511671;}*/ ?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width, initial-scale=1.0"><link href="/static_new6/css/app.7b22fa66c2af28f12bf32977d4b82694.css" rel="stylesheet"><link rel="stylesheet" href="/static_new/css/public.css"><script charset="utf-8" src="/static_new/js/jquery.min.js"></script><script charset="utf-8" src="/static_new/js/dialog.min.js"></script><script charset="utf-8" src="/static_new/js/common.js"></script><link rel="stylesheet" href="/public/css/swiper.min.css"><title>team</title><style>		body,
		h1,
		h2,
		h3,
		h4,
		h5,
		h6,
		hr,
		p,
		blockquote,
		dl,
		dt,
		dd,
		ul,
		ol,
		li,
		pre,
		fieldset,
		button,
		input,
		textarea,
		form,
		th,
		td {
			margin: 0;
			padding: 0;
		}

		* {
			box-sizing: border-box;
			font-family: sans-serif;
		}

		html {
			max-width: 750px;
			display: block;
			margin: auto;
		}
		.container {
			margin-bottom: 5rem;
		}

		.swiper-container {
			height: 10.8rem;
			--swiper-pagination-color: #FCB28E;
		}

		.swiper-slide img {
			width: 100%;
			height: 100%;
			border: none;
		}

		.swiper-pagination-bullet-active {
			background-color: #FCB28E;
		}


		.team_one {
			overflow: hidden;
			border-radius: 8px;
			/* width: 100%; */
			padding: 3%;
			color: #000;
			font-size: 16px;
			text-align: center;
			background: #C8D8FF;
			margin: 2%;
		}

		.team_left {
			float: left;
			width: 27%;
			border-radius: 15px;
			padding: 2%;
			margin-right: 2%;
		}

		.t_t {
			height: 30px;
			line-height: 30px;
			font-size: 15px;
		}

		.team_one span {
			color: #000;
		}

		.team_right {
			float: left;
			width: 34%;
			border-radius: 15px;
			padding: 2%;
			margin-right: 2%;
		}

		.team_ttp {
			width: 100%;
			text-align: center;
		}

		.team_ttp span {
			margin-top: -4%;
			display: block;
			color: #FCB28E;
		}

		.team_two {
			overflow: hidden;
			width: 100%;
		}

		.list_on {
			display: flex;
			flex-wrap: wrap;
			margin: 10px;
			justify-content: space-between;
		}

		.first_one {
			overflow: hidden;
			background: #F8FCFF;
			width: 100%;
			border-radius: 8px;
			color: #fff;
			margin-bottom: 10px;
		}

		.lv_l {
			background: #BBCEFD;
			overflow: hidden;
			border-radius: 8px;
		}

		.ap_01 {
			float: left;
			text-align: left;
			padding: 2%;
		}

		.first_one img {
			width: 20px;
			height: 20px;
			vertical-align: middle;
			margin-right: 5%;
		}

		.ap_02 {
			float: left;
			margin-top: 3%;
			color: #000;
		}

		.lv_l span {
			display: block;
		}

		.ap_03 {
			float: right;
			margin: 5%;
		}

		.list_on .text {
			padding: 10px;
			width: 50%;
			font-size: 14px;
			float: left;
			margin-top: 15%;
		}

		.p_m {
			height: 30px;
			border-radius: 15px;
			line-height: 30px;
			padding-left: 10px;
			font-size: 18px;
			color: #000;
			margin-top: -50px;
			text-align: left;
		}

		.text01 {
			padding: 10px;
			width: 40%;
			float: right;
			text-align: right;
			color: #000;
			margin-top: 5%;
		}
	</style><style>	    .footer {
	        height: 4rem !important;
	    }
		.footer ul {
			height: 100%;
			margin-top: 0.53333rem;
		}
		.footer ul li > img {
			height: 27.4px !important;
		}
		.footer ul li span {
			font-size: 12px;
			padding-top: 3%;
		}
		.footer ul li.on img:nth-child(1) {
			display: block !important;
		}
		.footer ul li.on img:nth-child(2) {
			display: none !important;
		}
	</style></head><body><div><div class="container"><!-- 团队 页面 --><!-- 轮播图 --><div class="swiper-container"><div class="swiper-wrapper"><div class="swiper-slide"><img src="/image-self/ctrl_team/zu.png" alt=""></div><div class="swiper-slide"><img src="/image-self/ctrl_team/zu.png" alt=""></div><div class="swiper-slide"><img src="/image-self/ctrl_team/zu.png" alt=""></div></div><div class="swiper-pagination"></div></div><!-- 内容一 --><div class="team_one"><div class="team_left"><p><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><?php echo htmlentities(app('lang')->get('RMB')); ?><?php echo htmlentities($teamyue); ?></font></font></p><div class="t_t"><span><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><?php echo lang("团队余额"); ?></font></font></span></div></div><div class="team_right"><p><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><?php echo htmlentities(app('lang')->get('RMB')); ?><?php echo htmlentities($teamls); ?></font></font></p><div class="t_t"><span><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><?php echo lang("团队流水"); ?></font></font></span></div></div><div class="team_right invt_num" style="margin-right:0"><p><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><?php echo htmlentities($tuandui); ?><?php echo lang('人'); ?></font></font></p><div class="t_t" style="height:auto;line-height:24px"><span class="invt_num_val"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><?php echo lang('团队人数'); ?></font></font></span></div></div></div><div class="team_ttp"><p>Bonus</p><span>__</span></div><!-- 内容二 --><div class="team_two"><div class="list_on"><div class="first_one"><?php foreach($data as $v): ?><div class="lv_l" style="margin-bottom:25px;"><p class="ap_01"><img src="/image-self/ctrl_team/s-logo.png" alt="" style="width:50px;height:50px"></p><p class="ap_02"><span><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">VIP<?php echo htmlentities($v['id']); ?></font></font></span><span style="font-size: 14px"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><?php echo htmlentities($v['name']); ?></font></font></span></p><p class="ap_03"><a style="font-size:14px;color:#3b81fe;" href="<?php echo url('rot_order/index'); ?>?type=<?php echo htmlentities($v['id']); ?>"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><?php echo lang('Detail'); ?>&gt;</font></font></a></p></div><?php endforeach; ?></div></div></div></div><div data-v-8755e8fe="" data-v-eebac136="" class="footer"  style="background: none;width: 100%;height: 1.7rem;background-color: white;"><ul data-v-8755e8fe=""><li onclick="window.location.href='<?php echo url('index/home'); ?>'" data-v-8755e8fe=""><?php
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

</style></div><script src="/public/js/swiper.min.js"></script><script>		// 动态创建html字体大小
// 		function getHtmlFontSize() {
// 			let html = document.querySelector('html');
// 			html.style.fontSize = (document.documentElement.clientWidth / 10) + 'px';
// 		}
// 		window.addEventListener('resize', getHtmlFontSize);

		// 轮播图
		var mySwiper = new Swiper('.swiper-container', {
			direction: 'horizontal',
			loop: true,
			autoplay: true,
			pagination: {
				el: '.swiper-pagination',
			}
		})


		$(function () {
			$('.footer li').eq(4).addClass("on");
			$(".qwerty").val("pxx");
			$('#qwerty').change(function () {
				var lang = $('#qwerty').val();
				$.ajax({
					url: '/index/index/lang',
					type: 'post',
					data: { type: lang },
					success: function (data) {
						// settimeout(function(){
						location.reload();
						// },500);
					}
				});
			}


			);
		});
		$(function () {
			$('.footer li').eq(4).addClass("on");

		})

		$('.tabs_btn1').click(function () {
			$(document).dialog({
				type: 'confirm',
				titleText: "<?php echo htmlentities(app('lang')->get('Areout')); ?>",
				autoClose: 0,
				buttonTextConfirm: '<?php echo htmlentities(app('lang')->get('ok')); ?>',
				buttonTextCancel: '<?php echo htmlentities(app('lang')->get('no')); ?>',
				onClickConfirmBtn: function () {
					window.location.href = "<?php echo url('user/logout'); ?>";
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