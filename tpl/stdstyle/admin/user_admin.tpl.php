<?php
use Utils\Text\Formatter;
use Utils\Uri\SimpleRouter;

?>
<div class="content2-pagetitle">
  <?=tr('admin_user_management')?> <?=$view->user->getUserName()?>
</div>

<div class="content2-container">
  <?=$view->callChunk('infoBar', null, $view->infoMsg, $view->errorMsg)?>

  <?=tr('username')?>:
  <a href="<?=$view->user->getProfileUrl()?>" class="links">
    <?=$view->user->getUserName()?>
    <img src="/tpl/stdstyle/images/misc/linkicon.png" alt="user profile"></a>
  <br>
  <p><?=tr('lastlogins')?>: <span class="<?=$view->user->getLastLoginPeriodClass()?>"><?=Formatter::dateTime($view->user->getLastLoginDate())?></span><br>
  <?=tr('registered_since_label')?>: <strong><?=Formatter::dateTime($view->user->getDateCreated())?></strong><br>
  <?=tr('email_address')?>:
  <a href="<?=SimpleRouter::getLink('UserProfile', 'mailTo', $view->user->getUserId())?>" class="links">
    <?=$view->user->getEmail()?>
    <img src="/tpl/stdstyle/images/free_icons/email.png" alt="<?=tr('email_user')?>" title="<?=tr('email_user')?>"></a>
  <div class="buffer"></div>


<?php if ($view->user->isUserActivated()) { ?>

  <?php if ($view->user->getNotifyCaches()) { ?>
    <a href="<?=SimpleRouter::getLink('Admin.UserAdmin','notifyCaches', [$view->user->getUserId(), "0"])?>">
      <img src="/tpl/stdstyle/images/misc/on.svg" alt="<?=tr('yes')?>" title="<?=tr('yes')?>" class="admin-onoff-switch"></a>
  <?php } else { ?>
    <a href="<?=SimpleRouter::getLink('Admin.UserAdmin','notifyCaches', [$view->user->getUserId(), "1"])?>">
      <img src="/tpl/stdstyle/images/misc/off.svg" alt="<?=tr('no')?>" title="<?=tr('no')?>" class="admin-onoff-switch"></a>
  <?php } // if ?>
  <?=tr('admin_user_notifycaches')?>
  <div class="buffer"></div>

  <?php if ($view->user->getNotifyLogs()) { ?>
    <a href="<?=SimpleRouter::getLink('Admin.UserAdmin','notifyLogs', [$view->user->getUserId(), "0"])?>">
      <img src="/tpl/stdstyle/images/misc/on.svg" alt="<?=tr('yes')?>" title="<?=tr('yes')?>" class="admin-onoff-switch"></a>
  <?php } else { ?>
    <a href="<?=SimpleRouter::getLink('Admin.UserAdmin','notifyLogs', [$view->user->getUserId(), "1"])?>">
      <img src="/tpl/stdstyle/images/misc/off.svg" alt="<?=tr('no')?>" title="<?=tr('no')?>" class="admin-onoff-switch"></a>
  <?php } // if ?>
  <?=tr('admin_user_notifylogs')?>
    <div class="buffer"></div>

    <div class="content-title-noshade-size1"><?=tr('admin_user_restrictions')?></div>

  <?php if ($view->user->getIsActive()) { ?>
    <a href="<?=SimpleRouter::getLink('Admin.UserAdmin','userBan', [$view->user->getUserId(), "1"])?>">
      <img src="/tpl/stdstyle/images/misc/off.svg" alt="<?=tr('no')?>" title="<?=tr('no')?>" class="admin-onoff-switch"></a>
  <?php } else { ?>
    <a href="<?=SimpleRouter::getLink('Admin.UserAdmin','userBan', [$view->user->getUserId(), "0"])?>">
      <img src="/tpl/stdstyle/images/misc/on.svg" alt="<?=tr('yes')?>" title="<?=tr('yes')?>" class="admin-onoff-switch"></a>
  <?php } // if ?>
  <?=tr('admin_user_userban')?>
  <div class="buffer"></div>

  <?php if ($view->user->getStatBan()) { ?>
    <a href="<?=SimpleRouter::getLink('Admin.UserAdmin','statBan', [$view->user->getUserId(), "0"])?>">
      <img src="/tpl/stdstyle/images/misc/on.svg" alt="<?=tr('yes')?>" title="<?=tr('yes')?>" class="admin-onoff-switch"></a>
  <?php } else { ?>
    <a href="<?=SimpleRouter::getLink('Admin.UserAdmin','statBan', [$view->user->getUserId(), "1"])?>">
      <img src="/tpl/stdstyle/images/misc/off.svg" alt="<?=tr('no')?>" title="<?=tr('no')?>" class="admin-onoff-switch"></a>
  <?php } // if ?>
  <?=tr('admin_user_statban')?>
  <div class="buffer"></div>

  <?php if ($view->user->getVerifyAll()) { ?>
    <a href="<?=SimpleRouter::getLink('Admin.UserAdmin','verifyAll', [$view->user->getUserId(), "0"])?>">
      <img src="/tpl/stdstyle/images/misc/on.svg" alt="<?=tr('yes')?>" title="<?=tr('yes')?>" class="admin-onoff-switch"></a>
  <?php } else { ?>
    <a href="<?=SimpleRouter::getLink('Admin.UserAdmin','verifyAll', [$view->user->getUserId(), "1"])?>">
      <img src="/tpl/stdstyle/images/misc/off.svg" alt="<?=tr('no')?>" title="<?=tr('no')?>" class="admin-onoff-switch"></a>
  <?php } // if ?>
  <?=tr('admin_user_verifyall')?>
  <div class="buffer"></div>

  <?php if ($view->user->getNewCachesNoLimit()) { ?>
    <a href="<?=SimpleRouter::getLink('Admin.UserAdmin','createNoLimit', [$view->user->getUserId(), "0"])?>">
      <img src="/tpl/stdstyle/images/misc/on.svg" alt="<?=tr('yes')?>" title="<?=tr('yes')?>" class="admin-onoff-switch"></a>
  <?php } else { ?>
    <a href="<?=SimpleRouter::getLink('Admin.UserAdmin','createNoLimit', [$view->user->getUserId(), "1"])?>">
      <img src="/tpl/stdstyle/images/misc/off.svg" alt="<?=tr('no')?>" title="<?=tr('no')?>" class="admin-onoff-switch"></a>
  <?php } // if ?>
  <?=tr('admin_user_nolimit')?>
  <div class="buffer"></div>

  <div class="content-title-noshade-size1"><?=tr('admin_user_addnote')?></div>

  <form action="<?=SimpleRouter::getLink('Admin.UserAdmin', 'addNote', $view->user->getUserId())?>" method="post">
    <textarea name="note_content" class="form-control admin-note-textarea"></textarea>
    <div class="align-center">
      <button type="submit" name="save" value="save" class="btn btn-primary btn-md"><?=tr('save')?></button>
    </div>
  </form>
  <table class="table table-striped full-width">
    <tr>
      <th colspan="2"><?=tr('admin_notes_table_title')?></th>
    </tr>
    <?php if (empty($view->userNotes)) {?>
        <tr>
          <td colspan="2"><?=tr('admin_notes_no_infos')?></td>
        </tr>
    <?php } else {
        foreach ($view->userNotes as $note) { ?>
            <tr>
              <td>
                <?=Formatter::dateTime($note->getDateTime())?>
                - <a class="links" href="<?=$note->getAdmin()->getProfileUrl()?>"><?=$note->getAdmin()->getUserName()?></a></td>
              </td>
              <td>
                <?php if ($note->isAutomatic()) {?>
                  <img title="<?=tr("admin_notes_auto")?>" alt="" class="icon16" src="<?=$note->getAutomaticPictureUrl()?>">
                  <?=tr($note->getContentTranslationKey())?>
                    <?php if (! empty($note->getCacheId())) {?>
                      <a class="links" href="<?=$note->getCache()->getCacheUrl()?>"><?=$note->getCache()->getCacheName()?> (<?=$note->getCache()->getGeocacheWaypointId()?>)</a>
                    <?php } // if (! empty($note->getCacheId())) ?>
                <?php } else { //if ($note->isAutomatic()) ?>
                  <img title="<?=tr("admin_notes_man")?>" alt="" class="icon16" src="<?=$note->getAutomaticPictureUrl()?>">
                  <?=$note->getContent()?>
                <?php }?>
              </td>
            </tr>
    <?php    } // foreach $view->userNotes
    } // if (empty($view->userNotes)) ?>
  </table>

<?php } else { ?>

  <div class="callout callout-warning align-center">
    <?=tr('account_not_activated')?><br>
    <a href="<?=SimpleRouter::getLink('Admin.UserAdmin','activateUser', $view->user->getUserId())?>" class="btn btn-md btn-default"><?=tr('activate_mail_btn')?></a>
  </div>

<?php } // end if $view->user->isUserActivated() ?>
</div>