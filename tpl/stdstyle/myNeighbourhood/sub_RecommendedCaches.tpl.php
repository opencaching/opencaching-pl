<?php
use Utils\Gis\Gis;
use lib\Objects\Neighbourhood\Neighbourhood;
use Utils\Uri\SimpleRouter;

?>
<div class="nbh-block-header">
  <?=tr('top_recommended')?> <img src="/images/rating-star.png" alt="<?=tr('number_obtain_recommendations')?>">
  <div class='btn-group nbh-sm-buttons'>
    <?php if (count($view->topRatedCaches) == $view->preferences['style']['caches-count']) { ?>
      <a class="btn btn-xs btn-primary" href="<?=SimpleRouter::getLink('MyNeighbourhood', 'mostRecommended', $view->selectedNbh)?>" title="<?=tr('myn_hlp_more')?>"><?=tr('more')?></a>
    <?php } // end if ?>
    <button class="btn btn-xs btn-default nbh-hide-toggle" title="<?=tr('myn_hlp_hide')?>"><span class="nbh-eye"></span></button>
    <button class="btn btn-xs btn-default nbh-size-toggle" title="<?=tr('myn_hlp_resize')?>"><span class="ui-icon ui-icon-arrow-2-e-w"></span></button>
  </div>
</div>

<div class="nbh-block-content<?=$view->preferences['items'][Neighbourhood::ITEM_RECOMMENDEDCACHES]['show'] == true ? '' : ' nbh-nodisplay'?>">

<?php if (empty($view->topRatedCaches)) { ?>
  <div class="align-center"><?=tr('list_of_caches_is_empty')?></div>
<?php } else {
  foreach ($view->topRatedCaches as $cache) {?>
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
        <img src="<?=$cache->getTerrainIcon()?>" alt="<?=tr('terrain_difficulty')?>: <?=$cache->getTerrain() / 2?>" title="<?=tr('terrain_difficulty')?>: <?=$cache->getTerrain() / 2?>">
        <?=tr($cache->getSizeTranslationKey())?></span> |
        <span class="nbh-nowrap"><?=round(Gis::distanceBetween($view->neighbourhoodsList[$view->selectedNbh]->getCoords(), $cache->getCoordinates()))?> km
        <img src="/tpl/stdstyle/images/misc/arrow-north.svg" class="nbh-arrow-north" alt="<?=tr('direction')?>" style="transform: rotate(<?=round(Gis::calcBearingBetween($view->neighbourhoodsList[$view->selectedNbh]->getCoords(), $cache->getCoordinates()))?>deg)"></span>
        | <img src="/images/rating-star.png" alt="<?=tr('number_obtain_recommendations')?>">
        (<?=$cache->getRecommendations()?>)
      </div>
    </a>
  </div>
  <?php } //end foreach ?>
<?php } ?>
</div>