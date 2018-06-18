<?php
use Utils\Text\Formatter;
use lib\Objects\User\User;

?>

<style>
body {
    background-color: #fff !important;
}
</style>

<div class="popup-container">
  <div class="callout callout-info">
    <div class="callout-title">
      <img src="<?=$view->cache->getCacheIcon()?>" class="icon32" alt="<?=tr($view->cache->getCacheTypeTranslationKey())?>">
      <?=$view->cache->getCacheName()?>
      - <?=$view->cache->getWaypointId()?>
    </div>
    <div class="buffer"></div>
    <p><?=tr('event_attendance_02')?>: <strong><?=Formatter::date($view->cache->getDatePlaced())?></strong></p>
    <p><?=tr('event_attendance_03')?>: <strong><?=$view->cache->getOwner()->getUserName()?></strong></p>
    <div class="buffer"></div>
    <p><?=tr('event_attendance_04')?> <strong>(<?=count($view->attenders)?>)</strong>:</p>
    <div class="tab-text">
      <?php
      foreach ($view->attenders as $user) { ?>
        <p><a href="<?=User::GetUserProfileUrl($user['user_id'])?>" class="links" target="_blank"><?=$user['username']?></a></p>
      <?php } ?>
    </div>
    <div class="align-center">
      <button class="btn btn-primary" onclick="window.close()"><?=tr('newCacheWpClose')?></button>
    </div>
  </div>
</div>