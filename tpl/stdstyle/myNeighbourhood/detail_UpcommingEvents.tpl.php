<?php
use Utils\Gis\Gis;
use Utils\Text\Formatter;
use Utils\Uri\SimpleRouter;
use lib\Controllers\LogEntryController;
use lib\Objects\GeoCache\GeoCacheLog;

$logController = new LogEntryController();
?>
<div class="content2-container">
  <div class="nbh-pageheader">
    <?=tr('incomming_events')?> (<?=($view->selectedNbh == 0) ? tr('my_neighborhood') : $view->neighbourhoodsList[$view->selectedNbh]->getName()?>)
    <div class="nbh-md-buttons">
      <a href="<?=SimpleRouter::getLink('MyNeighbourhood', 'index', $view->selectedNbh)?>" class="btn btn-md btn-default nbh-back-btn"><?=tr('myn_btn_back')?></a>
    </div>
  </div>
  <table class="table full-width">
    <thead>
      <tr>
        <th><?=tr('cache')?></th>
        <th><?=tr('myn_event_organizer')?></th>
        <th><?=tr('date_event_label')?></th>
        <th><?=tr('new_logs_myn')?></th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($view->caches as $cache) { ?>
      <tr>
        <td onclick="location.href='<?=$cache->getCacheUrl()?>';" style="cursor: pointer;">
          <div class="nbh-image-container">
            <img src="<?=$cache->getCacheIcon($view->user) ?>" class="nbh-icon" title="<?=tr($cache->getCacheTypeTranslationKey()) ?>" alt="<?=tr('cache') ?>">
          </div>
          <div class="nbh-desc-container">
            <strong><?=$cache->getCacheName() ?></strong>
            <span class="nbh-nowrap"><?=round(Gis::distanceBetween($view->coords, $cache->getCoordinates()))?> km
            <img src="/tpl/stdstyle/images/misc/arrow-north.svg" class="nbh-arrow-north" alt="<?=tr('direction')?>" style="transform: rotate(<?=round(Gis::calcBearingBetween($view->coords, $cache->getCoordinates()))?>deg)"></span>
            | <img src="/tpl/stdstyle/images/log/16x16-will_attend.png" alt="<?=tr('will_attend')?>" title="<?=tr('will_attend')?>" class="icon16"> <?=$cache->getNotFounds()?></span>
          </div>
        </td>
        <td>
          <strong><?=$cache->getOwner()->getUserName()?></strong>
        </td>
        <td>
          <?=Formatter::date($cache->getDatePlaced())?>
        </td>
        <?php
          $log = $logController->loadLogs($cache, false, 0, 1); 
          if (! empty($log)) { ?>
            <td onclick="location.href='<?=$log[0]->getLogUrl()?>';" style="cursor: pointer;">
              <div class="lightTipped">
                <img src="<?=GeoCacheLog::GetIconForType($log[0]->getType())?>" class="icon16" alt="<?=tr(GeoCacheLog::typeTranslationKey($log[0]->getType()))?>">
                <?=Formatter::date($log[0]->getDate())?>
                <strong><?=$log[0]->getUser()->getUserName()?></strong>
                <span class="nbh-full-only"><br>
                <?=mb_substr(strip_tags($log[0]->getText()), 0, 35)?><?=(mb_strlen(strip_tags($log[0]->getText())) > 35 ? '...' : '')?></span>
              </div>
              <div class="lightTip"><?=$log[0]->getText()?></div>
            </td>
            <?php
              } else { ?>
            <td></td>
            <?php  }
              unset($log);
            ?>
      </tr>
    <?php } ?>
    </tbody>
  </table>
  <?php $view->callChunkInline('pagination', $view->paginationModel); ?>
  <div class="buffer"></div>
  <div class="notice"><?=tr('myn_distances')?></div>
</div>