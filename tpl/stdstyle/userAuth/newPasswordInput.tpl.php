<?php
?>
<div class="content2-pagetitle">
  <?=tr('newpw_title')?> - <?=tr('step2')?>
</div>
<div class="content2-container">
  <?php $view->callChunk('infoBar', null, null, $view->errorMsg ); ?>
  <div class="callout callout-info">
    <?=tr('password_requirements')?>
    <div class="buffer"></div>
    <?=tr('password_recommendations')?>
  </div>
  <form action="<?=$view->returnUrl?>" method="post" class="form-group-md">
    <div class="input-group input-group-md">
      <label for="newpw-password" class="input-group-addon"><?=tr('new_password')?></label>
      <span class="newpw-showpass newpw-eyeopen" id="newpw-showpass-switch" title="<?=tr('password_showhide')?>"></span>
      <span class="newpw-pass-meter"><meter id="newpw-meter" value="0" min="0" max="10"></meter></span>
      <input id="newpw-password" name="password" type="password" class="form-control input200" value="" autocomplete="new-password" maxlength="60" required>
    </div>
    <div class="buffer"></div>
    <div class="align-center">
      <button type="submit" class="btn btn-md btn-primary newpw-nodisplay" name="submitNewPw" id="newpw-submit-btn" disabled><?=tr('newpw_change_btn')?></button>
    </div>
  </form>
</div>
<link rel="prefetch" href="/tpl/stdstyle/images/misc/eye-off.svg">