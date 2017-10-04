
<div class="content2-pagetitle"><?=tr('loginForm_title')?></div>

<div class="content2-container">

  <?php $view->callChunk('infoBar', null, null, $view->errorMsg ); ?>

  <div id="loginForm">
    <form action="/login.php?action=login" method="post" name="login_form">

        <input type="hidden" name="target" value="<?=$view->target?>">

        <label for="userName"><?=tr('loginForm_userOrEmail')?>:</label>
        <input id="userName" name="email" maxlength="80" type="text" value=""
               class="form-control input150">

        <label for="password"><?=tr('password')?>:</label>
        <input id="password" name="password" maxlength="60" type="password" value=""
               class="form-control input150">

        <input type="submit" value="<?=tr('login')?>" class="btn btn-primary">
    </form>
  </div>

  <div id="additionalBtns">
      <?=tr('loginForm_notRegistered')?>&nbsp;&nbsp;
      <a class="btn btn-md btn-success" href="register.php"><?=tr('loginForm_singUp')?></a>
      <br>

      <?=tr('loginForm_lostPassword')?>&nbsp;&nbsp;
      <a class="btn btn-md btn-default" href="newpw.php"><?=tr('loginForm_resetPassword')?></a>
      <br>

      <!-- <?=tr('loginForm_lostEmail')?> -->
  </div>

</div>