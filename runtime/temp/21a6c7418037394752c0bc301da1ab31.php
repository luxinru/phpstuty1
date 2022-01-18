<?php /*a:1:{s:58:"E:\phpstudy_pro\WWW\application\index\view\user\login.html";i:1642515136;}*/ ?>
<!DOCTYPE html><html data-dpr="1"><head><meta http-equiv="Content-Type"
        content="text/html; charset=UTF-8"><meta name="viewport"
        content="width=device-width,user-scalable=no,initial-scale=1,maximum-scale=1,minimum-scale=1"><title><?php echo htmlentities(app('lang')->get('login')); ?></title><link rel="stylesheet"
        type="text/less"
        href="/static_new/less/login.less"><link rel="stylesheet"
        type="text/less"
        href="/static_new/less/public.less"><script charset="utf-8"
          src="/static_new/js/less.min.js"></script><script charset="utf-8"
          src="/static_new/js/jquery.min.js"></script><script charset="utf-8"
          src="/static_new/js/dialog.min.js"></script><script charset="utf-8"
          src="/static_new/js/common.js"></script></head><body><div id="app"><section class="title">      Sign in
    </section><section class="form"><div class="item"><span>Cell phone</span><input type="text"
               name="tel"
               value="<?php echo htmlentities((app('cookie')->get('tel') ?: '')); ?>"
               placeholder="Please enter phone number"></div><div class="item"><span>login password</span><input type="password"
               name="pwd"
               value="<?php echo htmlentities((app('cookie')->get('pwd') ?: '')); ?>"
               placeholder="Please enter your password"></div></section><section class="tip"
             onclick="window.location.href=`/index/user/forget`">      Forget password
    </section><section class="btns"><div class="submit">Login</div><div class="register"
           onclick="window.location.href=`/index/user/register`">Register</div></section></div></body><script type="application/javascript">  /*检查表单*/
  function check() {
    if ($("input[name=tel]").val() == '' || $("input[name=pwd]").val() == '') {
      $(document).dialog({ infoText: '<?php echo htmlentities(app('lang')->get('Pleaseaccountpassword')); ?>' });
      return false;
    }
    return true;
  }

  /*点击登录*/
  $(".submit").on('click', function () {

    if (check()) {
      let loading = null;
      const tel = $("input[name=tel]").val();
      const pwd = $("input[name=pwd]").val();
      $.ajax({
        url: "<?php echo url('do_login'); ?>",
        data: { tel: tel, pwd: pwd, jizhu: true },
        type: 'POST',
        beforeSend: function () {
          loading = $(document).dialog({
            type: 'notice',
            infoIcon: '/static_new/img/loading.gif',
            autoClose: 0
          });
        },
        success: function (data) {
          //loading.close();
          if (data.code == 0) {
            $(document).dialog({ infoText: data.info });
            setTimeout(function () {
              location.href = "<?php echo url('index/home'); ?>"
            }, 2000);
          } else {
            loading.close();
            if (data.info) {
              $(document).dialog({ infoText: data.info });
            } else {
              $(document).dialog({ infoText: "<?php echo htmlentities(app('lang')->get('error')); ?>", autoClose: 2000 });
            }
          }
        },
        error: function (data) {
          loading.close();
        }
      });
    }
  })
</script></html>