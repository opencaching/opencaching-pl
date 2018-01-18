
<h1>This is debug oAuth service configuration.</h1>
This is here for debug purpose only.

Click button to check local config.

<div>
    <?php if($view->fbTestEn){ ?>
    <a class="btn" href="<?=$view->fbLink?>">
      <img class="loginBtnLogo" alt="Facebook"
            src="/images/externalLogo/fb/FB-f-Logo__blue_50.png" />
      <div class="loginBtnTxt">
        Continue with Facebook
      </div>
    </a>
    <?php } //if-fbTestEn ?>

    <?php if($view->gTestEn) { ?>
    <a class="btn" href="<?=$view->gLink?>">
      <img id="googleLogo" class="loginBtnLogo" alt="Google"
            src="/images/externalLogo/google/btn_google_light_normal_ios.svg" />
      <div class="loginBtnTxt">
        Continue with Google
      </div>
    </a>
    <?php } //if-gTestEn ?>

</div>