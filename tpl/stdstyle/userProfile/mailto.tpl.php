<?php
use Utils\Uri\SimpleRouter;

?>
<div class="content2-pagetitle">
  <?=tr('email_user')?> <?=$view->requestedUser->getUserName()?>
</div>
<div class="content2-container">
  <?php $view->callChunk('infoBar', '', $view->infoMsg, $view->errorMsg ); ?>
  <form action="<?=SimpleRouter::getLink('UserProfile', 'mailTo', $view->requestedUser->getUserId()) ?>" method="post">
    <div class="input-group input-group-sm">
      <label for="subject" class="input-group-addon btn btn-sm btm-default"><?=tr('titles')?></label>
      <input type="text" name="subject" id="subject" class="form-control" maxlength="150" value="<?=$view->subject?>" required>
    </div>
    <div class="buffer"></div>
    <div class="input-group input-group-sm">
      <label for="contentArea" class="input-group-addon btn btn-sm btm-default"><?=tr('content')?></label>
      <textarea name="content" id="contentArea" class="form-control" required><?=$view->content?></textarea>
    </div>
    <div class="buffer"></div>
    <div class="input-group input-group-sm">
      <label class="input-group-addon btn btn-sm btm-default"><?=tr('options')?></label>
      <div class="form-control" id="checkbox-container">
        <input type="checkbox" id="recieveCopy" name="recieveCopy"<?=($view->preferences['email']['recieveCopy'] == true) ? ' checked' : ''?>>
        <label for="recieveCopy"><?=tr('mail_cc')?></label>
        <br>
        <input type="checkbox" id="showMyEmail" name="showMyEmail"<?=($view->preferences['email']['showMyEmail'] == true) ? ' checked' : ''?>>
        <label for="showMyEmail"><?=tr('my_email_will_send')?></label>
        <div class="notice"><?=tr('email_publish')?></div>
      </div>
    </div>
    <div class="buffer"></div>
    <div class="align-center">
      <div class="btn-group">
        <input type="submit" name="sendEmailAction" value="<?=tr('email_submit')?>" class="btn btn-md btn-primary">
        <a href="<?=$view->requestedUser->getProfileUrl()?>" class="btn btn-md btn-default"><?=tr('user_profile')?></a>
      </div>
    </div>
  </form>
</div>