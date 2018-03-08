<?php
use Utils\Gis\Gis;
use Utils\Text\Formatter;
use lib\Objects\GeoCache\GeoCacheLog;
use lib\Objects\Neighbourhood\Neighbourhood;
use Utils\Uri\SimpleRouter;
use Utils\Text\UserInputFilter;

?>
<div class="nbh-block-header">
  <?=tr('latest_logs')?>
  <div class="btn-group nbh-sm-buttons">
    <?php if (count($view->latestLogs) == $view->preferences['style']['caches-count']) { ?>
      <a class="btn btn-xs btn-primary" href="<?=SimpleRouter::getLink('MyNeighbourhood','latestLogs', $view->selectedNbh)?>" title="<?=tr('myn_hlp_more')?>"><?=tr('more')?></a>
    <?php } // end if ?>
    <button class="btn btn-xs btn-default nbh-hide-toggle" title="<?=tr('myn_hlp_hide')?>"><span class="nbh-eye"></span></button>
    <button class="btn btn-xs btn-default nbh-size-toggle" title="<?=tr('myn_hlp_resize')?>"><span class="ui-icon ui-icon-arrow-2-e-w"></span></button>
  </div>
</div>

<div class="nbh-block-content<?=$view->preferences['items'][Neighbourhood::ITEM_LATESTLOGS]['show'] == true ? '' : ' nbh-nodisplay'?>">

<?php if (empty($view->latestLogs)) { ?>
    <div class="notice"><?=tr('list_of_latest_logs_is_empty')?></div>
<?php } else {
  foreach ($view->latestLogs as $log) {?>
  <div class="nbh-line-container">
  <div class="lightTipped">
    <div class="nbh-image-container">
      <img src="<?=$log->getLogIcon()?>" class="nbh-icon" alt="<?=tr(GeoCacheLog::typeTranslationKey($log->getType()))?>">
    </div>
    <div class="nbh-desc-container" onclick="location.href='<?=$log->getLogUrl()?>';" style="cursor: pointer;">
      <img src="<?=$log->getGeoCache()->getCacheIcon($view->user) ?>" class="icon16" title="<?=tr($log->getGeoCache()->getCacheTypeTranslationKey()) ?>" alt="<?=tr('cache') ?>">
      <a href="<?=$log->getLogUrl()?>">
        <strong><?=$log->getGeoCache()->getCacheName() ?></strong>
        <?php if ($log->getGeoCache()->isPowerTrailPart()) { ?>
          <img src="<?=$log->getGeoCache()->getPowerTrail()->getFootIcon()?>" alt="<?=tr('pt002')?>" title="<?=htmlspecialchars($log->getGeoCache()->getPowerTrail()->getName())?>">
        <?php } // end of if isPowerTrailPart?>
        <span class="nbh-full-only"><?=tr('hidden_by')?> <strong><?=$log->getGeoCache()->getOwner()->getUserName()?></strong><br></span>
        <?php if ($log->isRecommendedByUser($log->getUser()->getUserId())) { ?>
          <img src="/images/rating-star.png" alt="<?=tr('number_obtain_recommendations')?>"> |
        <?php } // end of if isRecommendedByUser ?>
        <span class="nbh-nowrap"><?=Formatter::date($log->getDate())?></span>
        | <span class="nbh-nowrap"><?=round(Gis::distanceBetween($view->neighbourhoodsList[$view->selectedNbh]->getCoords(), $log->getGeoCache()->getCoordinates()))?> km
        <img src="/tpl/stdstyle/images/misc/arrow-north.svg" class="nbh-arrow-north" alt="<?=tr('direction')?>" style="transform: rotate(<?=round(Gis::calcBearingBetween($view->neighbourhoodsList[$view->selectedNbh]->getCoords(), $log->getGeoCache()->getCoordinates()))?>deg)"></span>
        | <strong><?=$log->getUser()->getUserName()?></strong>
      </a>
      </div>
  </div>
  <div class="lightTip"><?=UserInputFilter::purifyHtmlString($log->getText())?></div>
  </div>
  <?php } //end foreach ?>
<?php } ?>
</div>