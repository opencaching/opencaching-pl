<?php
use src\Utils\Text\Formatter;
use src\Utils\Uri\SimpleRouter;
use src\Utils\View\View;
use src\Models\User\User;

/** @var View $view **/
/** @var User $user **/
$user = $view->user;

?>
<script>
    function rmUserShowConfirmation() {
      $("#removeAccountBtn").hide();
      $("#removeAccountConfirmation").show();
    }

    function rmUserCancel() {
      $("#removeAccountBtn").show();
      $("#removeAccountConfirmation").hide();
    }

    function rmUserConfirmed(){
      $.ajax({
        type: 'GET',
        dataType: 'json',
        url: '/Admin.UserAdminApi/removeUserAccount/<?=$user->getUserId()?>'
      }).done(function(){
        console.log("account removed");
      }).fail(function(){
        console.log("fail");
      })
      location.reload();
    }
</script>

<div class="content2-pagetitle">
  <?=tr('admin_user_management')?> <?=$user->getUserName()?>
</div>

<div class="content2-container">
  <?=$view->callChunk('infoBar', null, $view->infoMsg, $view->errorMsg)?>

  <div id="userButtonsDiv">
    <div id="removeAccountBtn">
      <?php if(!$user->isAlreadyRemoved()) { ?>
        <button class="btn btn-danger btn-sm" onclick="rmUserShowConfirmation()">
      <?php } else { //$user->isAlreadyRemoved() ?>
        <button class="btn btn-disabled btn-sm">
      <?php } //$user->isAlreadyRemoved() ?>
        <?=tr('admin_user_rmUser')?>
      </button>
    </div>
    <div id="removeAccountConfirmation" style="display:none">
      <div>
        <p><?=tr('admin_user_confirmationTxt')?></p>
        <p><strong><?=tr('admin_user_confirmationEmail', [$user->getEmail()])?></strong></p>
      </div>
      <div id="rmUserConfirmationBtns">
          <button class="btn btn-default btn-sm" onclick="rmUserCancel()">
            <?=tr('admin_user_rmUserCancel')?>
          </button>

          <button class="btn btn-danger btn-sm" onclick="rmUserConfirmed()">
            <?=tr('admin_user_rmUserConfirmed')?>
          </button>
      </div>
    </div>
  </div>

  <?=tr('username')?>:
  <a href="<?=$user->getProfileUrl()?>" class="links">
    <?=$user->getUserName()?>
    <img src="/images/misc/linkicon.png" alt="user profile"></a>
  <br>
  <p>
    <?=tr('lastlogins')?>:
    <span class="<?=$user->getLastLoginPeriodClass()?>">
      <?=Formatter::dateTime($user->getLastLoginDate())?>
    </span>
    <br>
    <?=tr('registered_since_label')?>: <strong><?=Formatter::dateTime($user->getDateCreated())?></strong><br>
    <?=tr('email_address')?>:
      <a href="<?=SimpleRouter::getLink('UserProfile', 'mailTo', $user->getUserId())?>" class="links">
          <?=$user->getEmail()?>
          <img src="/images/free_icons/email.png" alt="<?=tr('email_user')?>" title="<?=tr('email_user')?>">
      </a>
      <a href="#" class="js-oc-copy-to-clipboard" data-copy-to-clipboard="<?=$user->getEmail()?>">
          <img src="/images/misc/copy-coords.svg" alt="user profile" width="16px" height="16px">
      </a>
  </p>
  <div class="buffer"></div>


<?php if ($user->isUserActivated()) { ?>

  <?php if ($user->getNotifyCaches()) { ?>
    <a href="<?=SimpleRouter::getLink('Admin.UserAdmin','notifyCaches', [$user->getUserId(), "0"])?>">
      <img src="/images/misc/on.svg" alt="<?=tr('yes')?>" title="<?=tr('yes')?>" class="admin-onoff-switch"></a>
  <?php } else { ?>
    <a href="<?=SimpleRouter::getLink('Admin.UserAdmin','notifyCaches', [$user->getUserId(), "1"])?>">
      <img src="/images/misc/off.svg" alt="<?=tr('no')?>" title="<?=tr('no')?>" class="admin-onoff-switch"></a>
  <?php } // if ?>
  <?=tr('admin_user_notifycaches')?>
  <div class="buffer"></div>

  <?php if ($user->getNotifyLogs()) { ?>
    <a href="<?=SimpleRouter::getLink('Admin.UserAdmin','notifyLogs', [$user->getUserId(), "0"])?>">
      <img src="/images/misc/on.svg" alt="<?=tr('yes')?>" title="<?=tr('yes')?>" class="admin-onoff-switch"></a>
  <?php } else { ?>
    <a href="<?=SimpleRouter::getLink('Admin.UserAdmin','notifyLogs', [$user->getUserId(), "1"])?>">
      <img src="/images/misc/off.svg" alt="<?=tr('no')?>" title="<?=tr('no')?>" class="admin-onoff-switch"></a>
  <?php } // if ?>
  <?=tr('admin_user_notifylogs')?>
    <div class="buffer"></div>

    <div class="content-title-noshade-size1"><?=tr('admin_user_restrictions')?></div>

  <?php if ($user->getIsActive()) { ?>
    <a href="<?=SimpleRouter::getLink('Admin.UserAdmin','userBan', [$user->getUserId(), "1"])?>">
      <img src="/images/misc/off.svg" alt="<?=tr('no')?>" title="<?=tr('no')?>" class="admin-onoff-switch"></a>
  <?php } else { ?>
    <a href="<?=SimpleRouter::getLink('Admin.UserAdmin','userBan', [$user->getUserId(), "0"])?>">
      <img src="/images/misc/on.svg" alt="<?=tr('yes')?>" title="<?=tr('yes')?>" class="admin-onoff-switch"></a>
  <?php } // if ?>
  <?=tr('admin_user_userban')?>
  <div class="buffer"></div>

  <?php if ($user->getStatBan()) { ?>
    <a href="<?=SimpleRouter::getLink('Admin.UserAdmin','statBan', [$user->getUserId(), "0"])?>">
      <img src="/images/misc/on.svg" alt="<?=tr('yes')?>" title="<?=tr('yes')?>" class="admin-onoff-switch"></a>
  <?php } else { ?>
    <a href="<?=SimpleRouter::getLink('Admin.UserAdmin','statBan', [$user->getUserId(), "1"])?>">
      <img src="/images/misc/off.svg" alt="<?=tr('no')?>" title="<?=tr('no')?>" class="admin-onoff-switch"></a>
  <?php } // if ?>
  <?=tr('admin_user_statban')?>
  <div class="buffer"></div>

  <?php if ($user->getVerifyAll()) { ?>
    <a href="<?=SimpleRouter::getLink('Admin.UserAdmin','verifyAll', [$user->getUserId(), "0"])?>">
      <img src="/images/misc/on.svg" alt="<?=tr('yes')?>" title="<?=tr('yes')?>" class="admin-onoff-switch"></a>
  <?php } else { ?>
    <a href="<?=SimpleRouter::getLink('Admin.UserAdmin','verifyAll', [$user->getUserId(), "1"])?>">
      <img src="/images/misc/off.svg" alt="<?=tr('no')?>" title="<?=tr('no')?>" class="admin-onoff-switch"></a>
  <?php } // if ?>
  <?=tr('admin_user_verifyall')?>
  <div class="buffer"></div>

  <?php if ($user->getNewCachesNoLimit()) { ?>
    <a href="<?=SimpleRouter::getLink('Admin.UserAdmin','createNoLimit', [$user->getUserId(), "0"])?>">
      <img src="/images/misc/on.svg" alt="<?=tr('yes')?>" title="<?=tr('yes')?>" class="admin-onoff-switch"></a>
  <?php } else { ?>
    <a href="<?=SimpleRouter::getLink('Admin.UserAdmin','createNoLimit', [$user->getUserId(), "1"])?>">
      <img src="/images/misc/off.svg" alt="<?=tr('no')?>" title="<?=tr('no')?>" class="admin-onoff-switch"></a>
  <?php } // if ?>
  <?=tr('admin_user_nolimit')?>
  <div class="buffer"></div>

  <div class="content-title-noshade-size1"><?=tr('admin_user_addnote')?></div>

  <form action="<?=SimpleRouter::getLink('Admin.UserAdmin', 'addNote', $user->getUserId())?>" method="post">
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
    <a href="<?=SimpleRouter::getLink('Admin.UserAdmin','activateUser', $user->getUserId())?>" class="btn btn-md btn-default"><?=tr('activate_mail_btn')?></a>
  </div>

<?php } // end if $user->isUserActivated() ?>
</div>
