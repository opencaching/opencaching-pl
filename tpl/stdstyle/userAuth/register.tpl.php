<?php
use Utils\Uri\SimpleRouter;

?>

<div class="content2-pagetitle">
  {{register_pageTitle}}
</div>

<div class="content2-container">
  <?php $view->callChunk('infoBar', null, null, $view->errorMsg ); ?>

  <div class="callout callout-info">
    <?=tr('register_intro')?>
  </div>

  <form action="<?=SimpleRouter::getLink('userAuthorization','registerSubmit')?>" method="post">
    <div class="input-group input-group-md">
      <label for="username-input" class="input-group-addon loginLabel"><?=tr('username_label')?></label>
      <input id="username-input" name="username" type="text" value="<?=$view->username?>" class="form-control input200" maxlength="60" autocomplete="username" required>
    </div>

    <div class="buffer"></div>

    <div class="input-group input-group-md">
      <label for="email-input" class="input-group-addon loginLabel"><?=tr('email_address')?></label>
      <input id="email-input" name="email" type="email"  value="<?=$view->email?>" class="form-control input200" maxlength="60" autocomplete="email" required>
    </div>

    <div class="buffer"></div>

    <div class="input-group input-group-md">
      <label for="newpw-password" class="input-group-addon loginLabel"><?=tr('password')?></label>
      <span class="newpw-showpass newpw-eyeopen" id="newpw-showpass-switch" title="<?=tr('password_showhide')?>"></span>
      <span class="newpw-pass-meter"><meter id="newpw-meter" value="0" min="0" max="10"></meter></span>
      <input id="newpw-password" name="password" type="password" class="form-control input200" maxlength="60" autocomplete="new-password" required>
    </div>

    <div class="buffer"></div>

    <input type="checkbox" name="rules" id="rules-checkbox" required>
    <label for="rules-checkbox"><?=tr('register_rulesLbl')?></label>

    <div class="buffer"></div>

    <input type="checkbox" name="age" id="age-checkbox" required>
    <label for="age-checkbox"><?=mb_ereg_replace('{min_age}', $view->min_age, tr('register_ageLbl')) ?></label>

    <div class="buffer"></div>

    <div class="align-center">
      <input type="submit" class="btn btn-md btn-primary newpw-nodisplay" value="<?=tr('registration')?>" id="newpw-submit-btn">
    </div>
  </form>

  <div class="buffer"></div>

  <div class="notice"><?=tr('password_requirements')?></div>
  <div class="notice"><?=tr('password_recommendations')?></div>
  <div class="notice"><?=tr('register_info')?></div>
</div>