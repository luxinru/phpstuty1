<?php /*a:1:{s:59:"D:\phpstudy_pro\WWW\application\index\view\user\forget.html";i:1642995518;}*/ ?>
<!DOCTYPE html><!-- saved from url=(0052)http://qiang6-www.baomiche.com/#/Register?code=79053 --><html data-dpr="1"><head><meta http-equiv="Content-Type"
        content="text/html; charset=UTF-8"><meta name="viewport"
        content="width=device-width,user-scalable=no,initial-scale=1,maximum-scale=1,minimum-scale=1"><title><?php echo htmlentities(app('lang')->get('w_pwd')); ?></title><link rel="stylesheet"
        type="text/less"
        href="/static_new/less/login.less"><link rel="stylesheet"
        type="text/less"
        href="/static_new/less/public.less"><script charset="utf-8"
          src="/static_new/js/less.min.js"></script><script charset="utf-8"
          src="/static_new/js/jquery.min.js"></script><script charset="utf-8"
          src="/static_new/js/dialog.min.js"></script><script charset="utf-8"
          src="/static_new/js/common.js"></script></head><body><div id="app"><section class="top_bar"><img class="back" src="/static_new/img/返回 拷贝 5.png"
           alt=""></section><section class="title"
             style="font-size: 2.5625rem;">      Reset Password
    </section><section class="form"><form action=""
            id="forgetpwd-form"
            style="width: 100%"><div class="item"><span>Cell phone</span><input type="text"
                 name="tel"
                 placeholder="Please enter phone number"></div><div class="item"><span>login password</span><input type="password"
                 name="pwd"
                 placeholder="Please enter your password"></div><div class="item"><span>Confirm Password</span><input type="password"
                 name="pwd_re"
                 placeholder="Please enter again"></div><div class="item"><span>verification code</span><div class="input_box"><input type="text"
                   name="verify"
                   placeholder="Please enter verification code"><div id="code"
                 class="code">send</div></div></div></form></section><section class="btns"><div class="submit">Reset Password</div></section></div></body><script type="application/javascript">  let flag = true
  let countdown = 60
  let loading = null;

  $(".back").on('click', function () {
    location.href = "<?php echo url('user/login'); ?>"
  })

  /*检查表单*/
  function check() {
    if (!check_phone()) return false;

    if ($("input[name=verify]").val() == '') {
      $(document).dialog({ infoText: '<?php echo htmlentities(app('lang')->get('PleaseentertheSMS')); ?>' });
      return false;
    }

    if ($("input[name=pwd]").val() == '' || $("input[name=pwd_re]").val() == '') {
      $(document).dialog({ infoText: '<?php echo htmlentities(app('lang')->get('password')); ?>' });
      return false;
    }
    if ($("input[name=pwd]").val() !== $("input[name=pwd_re]").val()) {
      $(document).dialog({ infoText: '<?php echo htmlentities(app('lang')->get('is_Pleasepassword')); ?>' });
      return false;
    }
    return true;
  }

  /*手机号码验证*/
  function check_phone() {
    if ($("input[name=tel]").val() == '') {
      $(document).dialog({ infoText: '<?php echo htmlentities(app('lang')->get('number_tel')); ?>' });
      return false;
    }
    var myreg = /^[1][3,4,5,6,7,8,9][0-9]{9}$/;
    if (!myreg.test($("input[name=tel]").val())) {
      $(document).dialog({ infoText: '<?php echo htmlentities(app('lang')->get('number_tel_error')); ?>' })
      return false;
    }
    return true;
  }

  /*验证码倒计时*/
  function time_down(obj) {
    if (countdown == 0) {
      flag = true;
      obj.text("<?php echo htmlentities(app('lang')->get('Sendverif')); ?>");
      countdown = 60;
      return;
    } else {
      flag = false;
      obj.text(countdown + "s");
      countdown--;
    }
    setTimeout(function () { time_down(obj) }, 1000);
  }

  /*获取验证码*/
  $("#code").on('click', function () {
    if (check_phone() && flag) {
      $.ajax({
        url: '/index/send/sendsms',
        data: { 'tel': $("input[name=tel]").val(), 'type': 2 },
        type: 'POST',
        success: function (data) {
          if (data.code == 0) {
            $(document).dialog({ infoText: data.info });
            time_down($("#code"));
          } else {
            $(document).dialog({ infoText: data.msg });
          }
        }
      });
    }
  })

  /*提交*/
  $(".submit").on('click', function () {
    if (check()) {
      $.ajax({
        url: "<?php echo url('do_forget'); ?>",
        data: $("#forgetpwd-form").serialize(),
        type: 'POST',
        beforeSend: function () {
          loading = $(document).dialog({
            type: 'notice',
            infoIcon: '/static_new/img/loading.gif',
            infoText: '<?php echo htmlentities(app('lang')->get('z_Loading')); ?>',
            autoClose: 0
          });
        },
        success: function (data) {

          if (data.code == 0) {
            $(document).dialog({ infoText: '<?php echo htmlentities(app('lang')->get('edit_succes')); ?>' });
            setTimeout(function () {
              location.href = "<?php echo url('user/login'); ?>"
            }, 1500);
          } else {
            loading.close();
            $(document).dialog({ infoText: data.info });
          }
        }
      });
    }

    return false;
  })
</script></html>