<?php /*a:1:{s:61:"E:\phpstudy_pro\WWW\application\index\view\user\register.html";i:1642515565;}*/ ?>
<!DOCTYPE html><!-- saved from url=(0052)http://qiang6-www.baomiche.com/#/Register?code=79053 --><html data-dpr="1"><head><meta http-equiv="Content-Type"
        content="text/html; charset=UTF-8"><meta name="viewport"
        content="width=device-width,user-scalable=no,initial-scale=1,maximum-scale=1,minimum-scale=1"><title><?php echo htmlentities(app('lang')->get('register')); ?></title><link rel="stylesheet"
        type="text/less"
        href="/static_new/less/login.less"><link rel="stylesheet"
        type="text/less"
        href="/static_new/less/public.less"><script charset="utf-8"
          src="/static_new/js/less.min.js"></script><script charset="utf-8"
          src="/static_new/js/jquery.min.js"></script><script charset="utf-8"
          src="/static_new/js/dialog.min.js"></script><script charset="utf-8"
          src="/static_new/js/common.js"></script></head><body><div id="app"><section class="title">      Register
    </section><section class="form"><form action=""
            id="forgetpwd-form" style="width: 100%"><div class="item"><span>Cell phone</span><input type="text"
                 name="user_name"
                 placeholder="Please enter phone number"></div><div class="item"><span>login password</span><input type="password"
                 name="pwd"
                 placeholder="Please enter your password"></div><div class="item"><span>Confirm Password</span><input type="password"
                 name="pwd2"
                 placeholder="Please enter again"></div><div class="item"><span>资金密码</span><input type="password"
                 name="deposit_pwd"
                 placeholder="请输入资金密码"></div><div class="item"><span>Invitation code</span><input type="text"
                 name="invite_code"
                 placeholder="Please enter Invitation code"></div></form></section><section class="btns"><div class="submit">Sign up now</div><div class="register"
           onclick="window.location.href=`/index/user/login`">Existing account</div></section></div></body><script type="application/javascript">  /*手机号码验证*/
  function check_phone() {
    if ($("input[name=user_name]").val() == '') {
      $(document).dialog({ infoText: '<?php echo htmlentities(app('lang')->get('number_tel')); ?>' });
      return false;
    }
    var myreg = /^([0-9|A-Z|a-z]|[\u4E00-\u9FA5\uF900-\uFA2D]){2,12}$/;
    if (!myreg.test($("input[name=user_name]").val())) {
      $(document).dialog({ infoText: '<?php echo htmlentities(app('lang')->get('number_tel_error')); ?>' });
      return false;
    }
    // var myreg=/^[1][3,4,5,6,7,8,9][0-9]{9}$/;
    // if (!myreg.test($("input[name=tel]").val())) {
    //     $(document).dialog({infoText: '<?php echo htmlentities(app('lang')->get('number_tel_error')); ?>'});
    //     return false;
    // }
    return true;
  }

  /*检查表单*/
  function check() {
    if (!check_phone()) return false;

    if ($("input[name=pwd]").val() == '') {
      $(document).dialog({ infoText: '<?php echo htmlentities(app('lang')->get('login_password')); ?>' });
      return false;
    }
    if ($("input[name=pwd2]").val() == '') {
      $(document).dialog({ infoText: '<?php echo htmlentities(app('lang')->get('money_password')); ?>' });
      return false;
    }
    if ($("input[name=deposit_pwd]").val() == '') {
      $(document).dialog({ infoText: '<?php echo htmlentities(app('lang')->get('money_password')); ?>' });
      return false;
    }
    if ($("input[name=invite_code]").val() == '') {
      $(document).dialog({ infoText: '<?php echo htmlentities(app('lang')->get('yqm')); ?>' });
      return false;
    }
    return true;
  }

  /*提交*/
  $(".submit").on('click', function () {
    if (check()) {
      $.ajax({
        url: "<?php echo url('do_register'); ?>",
        data: $("#forgetpwd-form").serialize(),
        type: 'POST',
        beforeSend: function () {
          loading = $(document).dialog({
            type: 'notice',
            infoIcon: '/static_new/img/loading.gif',
            autoClose: 0
          });
        },
        success: function (data) {
          loading.close();
          if (data.code == 0) {
            $(document).dialog({ infoText: '<?php echo htmlentities(app('lang')->get('register_succes')); ?>' });
            setTimeout(function () {
              location.href = "<?php echo url('user/login'); ?>"
            }, 1500);
          } else {
            $(document).dialog({ infoText: data.info });
          }
        }
      });
    }
    return false;
  })
</script></html>