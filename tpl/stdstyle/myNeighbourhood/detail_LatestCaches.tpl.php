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
    <?=tr('newest_caches')?> (<?=($view->selectedNbh == 0) ? tr('my_neighborhood') : $view->neighbourhoodsList[$view->selectedNbh]->getName()?>)
    <div class="nbh-md-buttons">
      <a href="<?=SimpleRouter::getLink('MyNeighbourhood', 'index', $view->selectedNbh)?>" class="btn btn-md btn-default nbh-back-btn"><?=tr('myn_btn_back')?></a>
    </div>
  </div>
  <table class="table full-width">
    <thead>
      <tr>
        <th><?=tr('cache')?></th>
        <th><span class="nbh-nowrap"><?=tr('date_hidden_label')?></span></th>
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
            <?php if ($cache->isPowerTrailPart()) { ?>
              <img src="<?=$cache->getPowerTrail()->getFootIcon()?>" alt="<?=tr('pt002')?>" title="<?=htmlspecialchars($cache->getPowerTrail()->getName())?>">
            <?php } // end of if isPowerTrailPart?>
            <span class="nbh-full-only"><?=tr('hidden_by')?></span><span class="nbh-min-only">|</span> <strong><?=$cache->getOwner()->getUserName()?></strong>
            <span class="nbh-full-only"><br>
            <img src="<?=$cache->getDifficultyIcon()?>" alt="<?=tr('task_difficulty')?>: <?=$cache->getDifficulty() / 2?>" title="<?=tr('task_difficulty')?>: <?=$cache->getDifficulty() / 2?>">
            <img src="<?=$cache->getTerrainIcon()?>" alt="<?=tr('terrain_difficulty')?>: <?=$cache->getTerrain() / 2?>" title="<?=tr('terrain_difficulty')?>: <?=$cache->getTerrain() / 2?>">
            <?=tr($cache->getSizeTranslationKey())?></span> | 
            <span class="nbh-nowrap"><?=round(Gis::distanceBetween($view->coords, $cache->getCoordinates()))?> km
            <img src="/tpl/stdstyle/images/misc/arrow-north.svg" class="nbh-arrow-north" alt="<?=tr('direction')?>" style="transform: rotate(<?=round(Gis::calcBearingBetween($view->coords, $cache->getCoordinates()))?>deg)"></span>
            <?php if ($cache->getRecommendations() > 0) { ?>
              | <img src="/images/rating-star.png" alt="<?=tr('number_obtain_recommendations')?>">
              (<?=$cache->getRecommendations()?>)
            <?php } // end of if getRecommendations() ?>
          </div>
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