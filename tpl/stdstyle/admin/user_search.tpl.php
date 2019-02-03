<?php
use Utils\Uri\SimpleRouter;
use Utils\Text\Formatter;
use lib\Objects\GeoCache\GeoCacheLog;
?>
<div class="content2-pagetitle">
  <?=tr('search_user')?>
</div>

<div class="content2-container">
  <?php $view->callChunk('infoBar', null, null, $view->errorMsg); ?>
  <form style="display:inline;" method="POST">
    <p>
      <label for="username"><?=tr('loginForm_userOrEmail')?>:</label>
      <input type="text" name="username" value="<?=$view->userName?>" class="form-control input300">
      <button type="submit" name="submit" class="btn btn-default">
        <img class="icon16" src="/tpl/stdstyle/images/misc/magnifying-glass.svg" alt="<?=tr('search')?>">
        <?=tr('search')?>
      </button>
    </p>
  </form>
  <?php if (sizeof($view->usersTable) > 1) { ?>
    <table class="table table-striped full-width">
      <thead>
        <tr>
          <th><?=tr('username')?></th>
          <th>
            <img src="<?=GeoCacheLog::GetIconForType(GeoCacheLog::LOGTYPE_FOUNDIT)?>" alt="<?=tr(GeoCacheLog::typeTranslationKey(GeoCacheLog::LOGTYPE_FOUNDIT))?>" class="icon16" title="<?=tr(GeoCacheLog::typeTranslationKey(GeoCacheLog::LOGTYPE_FOUNDIT))?>">
            /
            <img src="<?=GeoCacheLog::GetIconForType(GeoCacheLog::LOGTYPE_DIDNOTFIND)?>" alt="<?=tr(GeoCacheLog::typeTranslationKey(GeoCacheLog::LOGTYPE_DIDNOTFIND))?>" class="icon16" title="<?=tr(GeoCacheLog::typeTranslationKey(GeoCacheLog::LOGTYPE_DIDNOTFIND))?>">
          </th>
          <th><?=tr('registered_since_label')?></th>
          <th><?=tr('lastlogins')?></th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($view->usersTable as $user) { ?>
        <tr>
          <td>
            <a href="<?=SimpleRouter::getLink('Admin.UserAdmin','index', $user->getUserId())?>" class="links">
              <?=$user->getUserName()?>
            </a>
          </td>
          <td>
            <?=$user->getFoundGeocachesCount()?>
            /
            <?=$user->getNotFoundGeocachesCount()?>
          </td>
          <td><?=Formatter::date($user->getDateCreated())?>
          <td>
            <span class="<?=$user->getLastLoginPeriodClass()?>">
              <?=Formatter::date($user->getLastLoginDate())?>
            </span>
        </tr>
      <?php } ?>
      </tbody>
    </table>
  <?php } ?>
</div>
