<?php
use Utils\Gis\Gis;
use lib\Objects\Neighbourhood\Neighbourhood;
use Utils\Text\Formatter;
use Utils\Uri\SimpleRouter;

?>
<div class="nbh-block-header">
  <?=tr('newest_caches')?>
  <div class="btn-group nbh-sm-buttons">
    <?php if (count($view->latestCaches) == $view->preferences['style']['caches-count']) { ?>
      <a class="btn btn-xs btn-primary" href="<?=SimpleRouter::getLink('MyNeighbourhood','latestCaches', $view->selectedNbh)?>" title="<?=tr('myn_hlp_more')?>"><?=tr('more')?></a>
    <?php } // end if ?>
    <button class="btn btn-xs btn-default nbh-hide-toggle" title="<?=tr('myn_hlp_hide')?>"><span class="nbh-eye"></span></button>
    <button class="btn btn-xs btn-default nbh-size-toggle" title="<?=tr('myn_hlp_resize')?>"><span class="ui-icon ui-icon-arrow-2-e-w"></span></button>
  </div>
</div>

<div class="nbh-block-content<?=$view->preferences['items'][Neighbourhood::ITEM_LATESTCACHES]['show'] == true ? '' : ' nbh-nodisplay'?>">

<?php if (empty($view->latestCaches)) { ?>
  <div class="notice"><?=tr('list_of_caches_is_empty')?></div>
<?php } else {
  foreach ($view->latestCaches as $cache) {?>
  <div class="nbh-line-container">
    <a href="<?=$cache->getCacheUrl()?>">
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
        <img src="<?=$cache->getTerrainIcon()?>" alt="<?=tr('terrain_difficulty')?>: <?=$cache->getTerrain() / 2?>" title="<?=tr('terrain_difficulty')?>: <?=$cache->getTerrain() / 2?>"></span>
        <span class="nbh-nowrap"><?=Formatter::date($cache->getDatePlaced())?></span> |
        <span class="nbh-full-only"><?=tr($cache->getSizeTranslationKey())?> |</span>
        <span class="nbh-nowrap"><?=round(Gis::distanceBetween($view->neighbourhoodsList[$view->selectedNbh]->getCoords(), $cache->getCoordinates()))?> km
        <img src="/tpl/stdstyle/images/misc/arrow-north.svg" class="nbh-arrow-north" alt="<?=tr('direction')?>" style="transform: rotate(<?=round(Gis::calcBearingBetween($view->neighbourhoodsList[$view->selectedNbh]->getCoords(), $cache->getCoordinates()))?>deg)"></span>
        <?php if ($cache->getRecommendations() > 0) { ?>
          | <img src="/images/rating-star.png" alt="<?=tr('number_obtain_recommendations')?>">
          (<?=$cache->getRecommendations()?>)
        <?php } // end of if getRecommendations() ?>
      </div>
    </a>
  </div>
  <?php } //end foreach ?>

<?php } ?>
</div>