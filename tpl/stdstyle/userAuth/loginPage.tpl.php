<?php

use Utils\Uri\SimpleRouter;

?>
<div class="content2-pagetitle"><?=tr('loginForm_title')?></div>

<div class="content2-container">
  <?php $view->callChunk('infoBar', null, null, $view->errorMsg ); ?>
  <div class="buffer"></div>
  <form action="<?=SimpleRouter::getLink('UserAuthorization', 'login')?>" method="post" name="login_form">
    <div class="input-group input-group-md">
      <label for="userName" class="input-group-addon loginLabel"><?=tr('loginForm_userOrEmail')?></label>
      <input id="userName" name="email" maxlength="80" type="text" value="<?=$view->prevEmail?>"
             class="form-control input300" autocomplete="username"  required>
    </div>
    <div class="buffer"></div>
    <div class="input-group input-group-md">
      <label for="password" class="input-group-addon loginLabel"><?=tr('loginForm_password')?></label>
      <input id="password" name="password" maxlength="60" type="password" value="<?=$view->prevPassword?>"
             class="form-control input300" autocomplete="current-password" required>
    </div>
    <div class="buffer"></div>
    <input type="hidden" name="target" value="<?=$view->target?>">
    <div class="align-center">
      <div class="btn-group">
        <input type="submit" value="<?=tr('login')?>" class="btn btn-primary btn-md">
        <a class="btn btn-md btn-success" href="/register.php"><?=tr('loginForm_signUp')?></a>
        <a class="btn btn-md btn-default" href="/newpw.php"><?=tr('loginForm_resetPassword')?></a>
      </div>
    </div>
  </form>
</div>