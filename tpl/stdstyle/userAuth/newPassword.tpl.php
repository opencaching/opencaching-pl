<?php
use Utils\Uri\SimpleRouter;

?>

<div class="content2-pagetitle">
  <?=tr('newpw_title')?> - <?=tr('step1')?>
</div>
<div class="content2-container">
  <?php $view->callChunk('infoBar', null, null, $view->errorMsg ); ?>
  <div class="callout callout-info">
    <?=tr('newpw_info')?>
  </div>
  <form action="<?=SimpleRouter::getLink('UserAuthorization', 'newPassword')?>" method="post" class="form-group-md">
    <div class="input-group input-group-md">
      <label for="userName" class="input-group-addon"><?=tr('loginForm_userOrEmail')?></label>
      <input id="userName" name="userName" type="text" class="form-control input200" value="<?=$view->username?>" required>
    </div>
    <div class="buffer"></div>
    <div class="align-center">
      <button type="submit" class="btn btn-md btn-primary" name="submitNewPw"><?=tr('email_submit')?></button>
    </div>
  </form>
</div>