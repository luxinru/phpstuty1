<?php /*a:2:{s:60:"D:\phpstudy_pro\WWW\application\index\view\ctrl\lixibao.html";i:1643104044;s:60:"D:\phpstudy_pro\WWW\application\index\view\public\floor.html";i:1643103954;}*/ ?>
<!DOCTYPE html><!-- saved from url=(0038)http://qiang6-www.baomiche.com/#/YuBao --><html data-dpr="1"><head><meta http-equiv="Content-Type"
        content="text/html; charset=UTF-8"><meta name="viewport"
        content="width=device-width,user-scalable=no,initial-scale=1,maximum-scale=1,minimum-scale=1"><title><?php echo lang('余额宝'); ?></title><link rel="stylesheet"
        type="text/less"
        href="/static_new/less/public.less"><link rel="stylesheet"
        type="text/less"
        href="/static_new/less/style/lixibao.less"><script charset="utf-8"
          src="/static_new/js/less.min.js"></script><script charset="utf-8"
          src="/static_new/js/jquery.min.js"></script><script charset="utf-8"
          src="/static_new/js/dialog.min.js"></script><script charset="utf-8"
          src="/static_new/js/common.js"></script></head><body><div id="app"><div class="main"><div class="header"><img src="/static_new/img/right.png"
             onclick="window.history.back(-1)"><?php echo lang('余额宝'); ?></div><div class="report"><div class="assets-wrapper"><p><?php echo lang('总资产'); ?>（<?php echo lang('元'); ?>）</p><span><?php echo htmlentities($ubalance); ?></span></div><div class="lev_box"><div class="lev_box_item"><p><?php echo lang('余额宝'); ?>（<?php echo lang('元'); ?>）</p><span>+<?php echo htmlentities($balance); ?></span></div><div class="lev_box_item"><p><?php echo lang('总收益'); ?></p><span>+<?php echo htmlentities($balance_shouru); ?></span></div><div class="lev_box_item"><p><?php echo lang('昨日收益'); ?></p><span>+<?php echo htmlentities($yes_shouyi); ?></span></div></div></div><div class="Cash_num"><div class="wdal-box"><h3><?php echo lang('余额转入'); ?></h3><div class="Cash_num_money"><span>$</span><input type="text"
                   name="price"
                   id="price"
                   placeholder="<?php echo lang('请输入转入金额'); ?>"></div></div><div class="wdal-box"><div class="Cash_num_money"><span><?php echo lang('预计收益'); ?></span><input type="text"
                   name="yuji"
                   id="yjsy"
                   disabled
                   value=""></div></div><h4><?php echo lang('收益标准'); ?></h4><div class="Cash_num_password"><?php if($lixibao): if(is_array($lixibao) || $lixibao instanceof \think\Collection || $lixibao instanceof \think\Paginator): $i = 0; $__LIST__ = $lixibao;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><span data-id="<?php echo htmlentities($v['id']); ?>"
                class="col-xs-3 ch5"><p class="c2"><?php echo htmlentities($v['name']); ?></p><p class="c2">+<?php echo htmlentities($v['bili']*100); ?>&nbsp;<small>%</small></p><p class="c1">[<?php echo lang('定'); ?>] <?php echo htmlentities($v['day']); ?>&nbsp;<small><?php echo lang('天'); ?></small></p></span><?php endforeach; endif; else: echo "" ;endif; else: ?><?php endif; ?></div></div><div class="postForm"><!--<button  class="auto" onclick="window.location.href=`/index/ctrl/lixibao_chu`"><?php echo lang('转出'); ?></button>--><button class="auto"><?php echo lang('转出'); ?></button><button class="save-btn"><?php echo lang('转入'); ?></button></div></div><div class="footer"><ul><li onclick="window.location.href='<?php echo url('index/home'); ?>'"><img src="<?php echo lang('/img/home_before.png','home'); ?>"
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
      $('.footer li').eq(3).addClass("on");
    });

    function check() {
      if ($("input[name=price]").val() == '') {
        $(document).dialog({ infoText: "<?php echo lang('存入金额不符合系统要求'); ?>" });
        return false;
      }
      return true;
    }
    $(function () {
      $('.Cash_num_password>span').eq(0).trigger("click")


    });

    $('.Cash_num_password>span').click(function () {
      cid = $(this).data('id');
      $('.Cash_num_password').attr('data-id', cid);
      $('.Cash_num_password>span').removeClass('active');
      $(this).addClass('active');
      yjsy();
    });

    function yjsy() {
      var price = $("#price").val();
      if (price <= 0) return false;
      $.ajax({
        url: '/index/ctrl/deposityj',
        data: { price: price, cid: cid },
        type: 'POST',
        success: function (data) {
          $("#yjsy").val(data.data);
        }
      });
    }


    $("#price").keyup(function () {
      yjsy();
    })

    $('.auto').click(function () {
      $(document).dialog({
        type: 'confirm',
        titleText: "<h2><?php echo lang('提醒'); ?></h2><br><?php echo lang('是否要取出所有利息宝余额?'); ?>",
        autoClose: 0,
        onClickConfirmBtn: function () {
          $.post("<?php echo url('lixibao_chu'); ?>", { type: 1 }, function (data) {
            if (data.code == 0) {
              window.location.href = "<?php echo url('lixibao'); ?>";
            } else {
              $(document).dialog({ infoText: data.info });
            }
          });
        },
        onClickCancelBtn: function () {
        }
      });
    });

    $(".save-btn").on('click', function () {
      if (check()) {
        var loading = null;
        var price = $("input[name=price]").val();
        $.ajax({
          url: "<?php echo url('lixibao_ru'); ?>",
          data: { price: price, cid: cid },
          type: 'POST',
          // beforeSend: function () {
          //     loading = $(document).dialog({
          //         type: 'notice',
          //         infoIcon: '/static_new/img/loading.gif',
          //         infoText: <?php echo lang('正在加载中'); ?>,
          //         autoClose: 0
          //     });
          // },
          success: function (data) {
            if (data.code == 0) {
              $(document).dialog({ infoText: data.info });
              setTimeout(function () {
                // loading.close();
                window.location.href = '/index/ctrl/lixibao';
              }, 2000);

            } else {
              $(document).dialog({ infoText: data.info });
              loading.close();
            }
          }
        });
      }
    })
  </script></body></html>