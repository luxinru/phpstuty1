<?php /*a:2:{s:56:"D:\phpstudy_pro\WWW\application\index\view\my\index.html";i:1643104026;s:60:"D:\phpstudy_pro\WWW\application\index\view\public\floor.html";i:1643103954;}*/ ?>
<!DOCTYPE html><!-- saved from url=(0035)http://qiang6-www.baomiche.com/#/My --><html data-dpr="1"><head><meta http-equiv="Content-Type"
        content="text/html; charset=UTF-8"><meta name="viewport"
        content="width=device-width,user-scalable=no,initial-scale=1,maximum-scale=1,minimum-scale=1"><title>my</title><link rel="stylesheet"
        type="text/less"
        href="/static_new/less/public.less"><link rel="stylesheet"
        type="text/less"
        href="/static_new/less/style/index.less"><script charset="utf-8"
          src="/static_new/js/less.min.js"></script><script charset="utf-8"
          src="/static_new/js/jquery.min.js"></script><script charset="utf-8"
          src="/static_new/js/dialog.min.js"></script><script charset="utf-8"
          src="/static_new/js/common.js"></script></head><body><div id="app"><div class="main"><div class="header"><!-- <div 
             class="img"><a 
             href=""
             class=""><img 
                 src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACwAAAAsCAMAAAApWqozAAAAP1BMVEVHcExCQkJCQkJDQ0NCQkJCQkJCQkJDQ0NCQkJCQkJCQkJCQkJCQkJFRUVCQkJDQ0NCQkJCQkJCQkJCQkJDQ0NLP8oPAAAAFHRSTlMAzyoRvkD5X+/eGrJyCYM4T5Ok5lh/mIcAAAE3SURBVHhezZTHjsQwDEPd4hK3lPf/37qLxWLg8SjJdd6R4kGkBatvxZScvD9d68/WzAu93XubZ8TZa6t1ADkYW812RCCZK2/VgHuN7Q7EK3cG2igsEdIqegsQprgRsuRdI7RZXDxInRygP9UdktCEhy7IJ2zSxlpsXtraQRCjgK+z6EEuScMySX0MMkcskxTgEJzypEGTzctn8jzmmxPGSUrQlUyco5uhIKHUMgtZXVAgru/10K/M9QRdxyUo6pLux/E+LSHdjfCiMnUsJIJVd+gh0gnrk9lI5/l4pgXOuz0yuJdgI6Sw/LKFN7Y/zfFWwMYDuxrYIjf46XptOZxzGkgvPKRfMTdz2f42/Dr3b7VD7P9eDVrdYU/we6/VtAh+UbeYIakP6oHV8U9a1DN919GnI6hv5QcUoxqZt07UFQAAAABJRU5ErkJggg=="
                 ></a><a 
             href="javascript:void(0)"
             onclick="window.location.href=`/index/ctrl/set`"
             class=""><img 
                 src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACwAAAAsCAMAAAApWqozAAAALVBMVEVHcEw+Pj5CQkJCQkJCQkJDQ0NDQ0NCQkJCQkJCQkJCQkJDQ0NDQ0NDQ0NEREQBqyziAAAAD3RSTlMAFJHuc/9P2yq/1khttlZE4SfSAAAA00lEQVR42s2V2xLEEAxAN0RLi///3EW3DbOadHhpXtrh4LjF570BSmsFz1jUJoXGB+iyml+si4Bal7Ft37f8dZaVLQI+//sic6+OVE8tkZENtnIKpN6VbUv76iRbB6n/sQruJt3SupGFiBihUdcVnNo2dedUqboPe3OFZ2BinVKu0DxsM2qvtbQsHBJ7FiY6cDAcvdEowMDx6Ji6jgyMxqhmq3AUljXmJygvnbwp09stH6TRIyof/sFrJV/YoVQgJ5m59CUnRjnlTiZz+ZmQH6DXxhfGzQxZusjycwAAAABJRU5ErkJggg=="
                 ></a></div> --><div class="info"><div class="name"><strong><?php echo htmlentities($info['username']); ?></strong><!-- <em >LV<?php echo htmlentities($info['level'] + 1); ?></em> --><small>Invitation Code: <?php echo htmlentities($info['invite_code']); ?></small></div><img src="<?php echo htmlentities($info['headpic']); ?>"
               onerror="this.src='/public/img/head.png'"
               onclick="location.href='/index/my/headimg.html';"
               class="headerImg"></div><div class="info_data"><div class="item"><span>0.00</span><span>Commission</span></div><div class="item"><span>0.00</span><span>Skypay balance</span></div><div class="item"><span>0.00</span><span>Skypay income</span></div></div><div class="balance"><p><span><?php echo htmlentities(app('lang')->get('Account_balance')); ?></span><small><?php echo htmlentities($info['balance']); ?></small></p><div class="button-wrapper"><div onclick="window.location.href=`/index/ctrl/deposit`"><?php echo htmlentities(app('lang')->get('withdraw')); ?></div><div onclick="window.location.href=`/index/ctrl/recharge`"><?php echo htmlentities(app('lang')->get('Recharge')); ?></div></div></div></div><ul class="login_nav"><div class="title"><span></span>Service Center</div><li onclick="window.location.href=`/index/ctrl/set`"><img src="/image-self/my_index/person.png"><p><?php echo htmlentities(app('lang')->get('Personalinformation')); ?></p></li><li onclick="window.location.href=`/index/order/index`"><img src="/image-self/my_index/report.png"><p><?php echo htmlentities(app('lang')->get('Graborderrecord')); ?></p></li><li onclick="window.location.href=`/index/my/caiwu`"><img src="/image-self/my_index/accountbook.png"><p><?php echo htmlentities(app('lang')->get('Accountdetails')); ?></p></li><li onclick="window.location.href=`/index/ctrl/recharge_admin`"><img src="/image-self/my_index/recharge.png"><p><?php echo htmlentities(app('lang')->get('Rechargerecord')); ?></p></li><li onclick="window.location.href=`/index/ctrl/deposit_admin`"><img src="/image-self/my_index/withdrawal.png"><p><?php echo htmlentities(app('lang')->get('Withdrawalsrecord')); ?></p></li><li onclick="window.location.href=`/index/my/invite`"><img src="/image-self/my_index/share.png"><p><?php echo htmlentities(app('lang')->get('Accountdetails22')); ?></p></li><li onclick="window.location.href=`/index/my/msg`"><img src="/image-self/my_index/notice.png"><p><?php echo htmlentities(app('lang')->get('systeminformation')); ?></p></li><li onclick="window.location.href=`/index/ctrl/junior`"><img src="/image-self/my_index/report.png"><p><?php echo htmlentities(app('lang')->get('Startorders22')); ?></p></li><!-- <li 
            onclick="window.location.href=`/index/my/downsoft`"><img 
               src="/image-self/download/ef6d275d92bcf3fa916d7645c859301.png"
               ><p ><?php echo lang('download App'); ?></p></li> --><li class="LoginOut"><img src="/image-self/my_index/icon_set up.png"><p>Set Up</p></li></ul></div><div class="footer"><ul><li onclick="window.location.href='<?php echo url('index/home'); ?>'"><img src="<?php echo lang('/img/home_before.png','home'); ?>"
           alt=""><img src="<?php echo lang('/img/home_after.png','home'); ?>"
           alt=""><span class="activeText"><?php echo lang('Home'); ?></span></li><li onclick="window.location.href='<?php echo url('order/index'); ?>'"><img src="<?php echo lang('/img/order_before.png','order'); ?>"
           alt=""><img src="<?php echo lang('/img/order_after.png','order'); ?>"
           alt=""><span><?php echo lang('Historic'); ?></span></li><?php
            $level = session('level') ? session('level') : 0;
            $level = $level + 1;
            $url = '/index/ctrl/team';
        ?><li onclick="window.location.href='<?=$url?>'"><img src="<?php echo lang('/img/team_before.png',[3]); ?> "
           alt=""><img src="<?php echo lang('/img/team_after.png',[3]); ?>"
           alt=""><span><?php echo lang('Team'); ?></span></li><li onclick="window.location.href='<?php echo url('ctrl/lixibao'); ?>'"><img src="<?php echo lang('/img/jr_before.png',[4]); ?>"
           alt=""><img src="<?php echo lang('/img/jr_after.png',[4]); ?>"
           alt=""><span><?php echo lang('Financial'); ?></span></li><li onclick="window.location.href='<?php echo url('my/index'); ?>'"><img src="<?php echo lang('/img/person_before.png',[5]); ?>"
           alt=""><img src="<?php echo lang('/img/person_after.png',[5]); ?>"
           alt=""><span><?php echo lang('Personal'); ?></span></li></ul></div></div><script>    $(function () {
      $('.footer li').eq(4).addClass("on");
    });

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