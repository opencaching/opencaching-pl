<?php
use Utils\Gis\Gis;
use Utils\Text\Formatter;
use lib\Objects\Neighbourhood\Neighbourhood;
use Utils\Uri\SimpleRouter;

?>
<div class="nbh-block-header">
  <?=tr('incomming_events')?>
  <div class='btn-group nbh-sm-buttons'>
    <?php if (count($view->upcomingEvents) == $view->preferences['style']['caches-count']) { ?>
      <a class="btn btn-xs btn-primary" href="<?SimpleRouter::getLink('MyNeighnourhood','upcommingEvents', $view->selectedNbh)?>" title="<?=tr('myn_hlp_more')?>"><?=tr('more')?></a>
    <?php } // end if ?>
    <button class="btn btn-xs btn-default nbh-hide-toggle" title="<?=tr('myn_hlp_hide')?>"><span class="nbh-eye"></span></button>
    <button class="btn btn-xs btn-default nbh-size-toggle" title="<?=tr('myn_hlp_resize')?>"><span class="ui-icon ui-icon-arrow-2-e-w"></span></button>
  </div>
</div>

<div class="nbh-block-content<?=$view->preferences['items'][Neighbourhood::ITEM_UPCOMINGEVENTS]['show'] == true ? '' : ' nbh-nodisplay'?>">

<?php if (empty($view->upcomingEvents)) { ?>
  <div class="align-center"><?=tr('list_of_events_is_empty')?></div>
<?php } else {
  foreach ($view->upcomingEvents as $cache) {?>
  <div class="nbh-line-container">
    <a href="<?=$cache->getCacheUrl()?>">
      <div class="nbh-image-container">
        <img src="<?=$cache->getCacheIcon($view->user) ?>" class="nbh-icon" title="<?=tr($cache->getCacheTypeTranslationKey()) ?>" alt="<?=tr('cache') ?>">
      </div>
      <div class="nbh-desc-container">
        <strong><?=$cache->getCacheName() ?></strong>
        <span class="nbh-full-only"><?=tr('organized_by')?></span><span class="nbh-min-only">|</span> <strong><?=$cache->getOwner()->getUserName()?></strong>
        <span class="nbh-full-only"><br></span><span class="nbh-min-only">|</span>
        <span class="nbh-nowrap"><?=Formatter::date($cache->getDatePlaced())?></span> |
        <span class="nbh-nowrap"><?=round(Gis::distanceBetween($view->neighbourhoodsList[$view->selectedNbh]->getCoords(), $cache->getCoordinates()))?> km
        <img src="/tpl/stdstyle/images/misc/arrow-north.svg" class="nbh-arrow-north" alt="<?=tr('direction')?>" style="transform: rotate(<?=round(Gis::calcBearingBetween($view->neighbourhoodsList[$view->selectedNbh]->getCoords(), $cache->getCoordinates()))?>deg)"></span>
        <span class="nbh-full-only">| <img src="/tpl/stdstyle/images/log/16x16-will_attend.png" alt="<?=tr('will_attend')?>" title="<?=tr('will_attend')?>" class="icon16"> <?=$cache->getNotFounds()?></span>
      </div>
    </a>
  </div>
  <?php } //end foreach
} ?>
</div>