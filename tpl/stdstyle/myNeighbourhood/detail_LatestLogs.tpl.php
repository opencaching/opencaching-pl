<?php
use Utils\Gis\Gis;
use Utils\Text\Formatter;
use Utils\Text\UserInputFilter;
use Utils\Uri\SimpleRouter;
use lib\Objects\GeoCache\GeoCacheLog;

?>
<div class="content2-container">
  <div class="nbh-pageheader">
    <?=tr('latest_logs')?> (<?=($view->selectedNbh == 0) ? tr('my_neighborhood') : $view->neighbourhoodsList[$view->selectedNbh]->getName()?>)
    <div class="nbh-md-buttons">
      <a href="<?=SimpleRouter::getLink('MyNeighbourhood', 'index', $view->selectedNbh)?>" class="btn btn-md btn-default nbh-back-btn"><?=tr('myn_btn_back')?></a>
     </div>
  </div>

  <table class="table full-width">
    <thead>
      <tr>
        <th><?=tr('cache')?></th>
        <th><?=tr('myn_log_txt')?></th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($view->logs as $log) { ?>
      <tr>
        <td onclick="location.href='<?=$log->getGeoCache()->getCacheUrl()?>';" style="cursor: pointer;">
          <div class="nbh-image-container">
            <img src="<?=$log->getGeoCache()->getCacheIcon($view->user) ?>" class="nbh-icon" title="<?=tr($log->getGeoCache()->getCacheTypeTranslationKey()) ?>" alt="<?=tr('cache') ?>">
          </div>
          <div class="nbh-desc-container">
            <a href="<?=$log->getGeoCache()->getCacheUrl()?>">
              <strong><?=$log->getGeoCache()->getCacheName() ?></strong>
              <?php if ($log->getGeoCache()->isPowerTrailPart()) { ?>
                <img src="<?=$log->getGeoCache()->getPowerTrail()->getFootIcon()?>" alt="<?=tr('pt002')?>" title="<?=htmlspecialchars($log->getGeoCache()->getPowerTrail()->getName())?>">
              <?php } // end of if isPowerTrailPart?>
              <span class="nbh-full-only"><?=tr('hidden_by')?></span><span class="nbh-min-only">|</span> <strong><?=$log->getGeoCache()->getOwner()->getUserName()?></strong>
              <span class="nbh-full-only"><br>
              <img src="<?=$log->getGeoCache()->getDifficultyIcon()?>" alt="<?=tr('task_difficulty')?>: <?=$log->getGeoCache()->getDifficulty() / 2?>" title="<?=tr('task_difficulty')?>: <?=$log->getGeoCache()->getDifficulty() / 2?>">
              <img src="<?=$log->getGeoCache()->getTerrainIcon()?>" alt="<?=tr('terrain_difficulty')?>: <?=$log->getGeoCache()->getTerrain() / 2?>" title="<?=tr('terrain_difficulty')?>: <?=$log->getGeoCache()->getTerrain() / 2?>">
              <?=tr($log->getGeoCache()->getSizeTranslationKey())?></span> |
              <span class="nbh-nowrap"><?=round(Gis::distanceBetween($view->coords, $log->getGeoCache()->getCoordinates()))?> km
              <img src="/tpl/stdstyle/images/misc/arrow-north.svg" class="nbh-arrow-north" alt="<?=tr('direction')?>" style="transform: rotate(<?=round(Gis::calcBearingBetween($view->coords, $log->getGeoCache()->getCoordinates()))?>deg)"></span>
            </a>
          </div>
        </td>
        <td>
          <div class="nbh-desc-container lightTipped" onclick="location.href='<?=$log->getLogUrl()?>';" style="cursor: pointer;">
            <img src="<?=$log->getLogIcon()?>" class="icon16" alt="<?=tr(GeoCacheLog::typeTranslationKey($log->getType()))?>">
            <a href="<?=$log->getLogUrl()?>">
              <span class="nbh-nowrap"><?=Formatter::date($log->getDate())?></span>
              <strong><?=$log->getUser()->getUserName()?></strong>
              <?php if ($log->isRecommendedByUser($log->getUser()->getUserId())) { ?>
                <img src="/images/rating-star.png" alt="<?=tr('number_obtain_recommendations')?>">
              <?php } // end of if isRecommendedByUser ?>
              <span class="nbh-full-only"><br>
              <?=mb_substr(strip_tags($log->getText()), 0, 70)?><?=(mb_strlen(strip_tags($log->getText())) > 70 ? '...' : '')?></span>
            </a>
          </div>
          <div class="lightTip"><?=UserInputFilter::purifyHtmlString($log->getText())?></div>
        </td>
      </tr>
    <?php } ?>
    </tbody>
  </table>
  <?php $view->callChunkInline('pagination', $view->paginationModel);?>
  <div class="buffer"></div>
  <div class="notice"><?=tr('myn_distances')?></div>
</div>