<?php

use src\Controllers\MyNbhInteractiveController;
use src\Models\ApplicationContainer;
use src\Models\Neighbourhood\Neighbourhood;
use src\Models\Coordinates\Coordinates;
use src\Utils\Uri\SimpleRouter;

?>
<div class="content2-container">
  <?php foreach ($view->neighbourhoodsList as $nbh) {
      if ($nbh->getSeq() == $view->selectedNbh) {
          $btnClassMod = 'btn-primary';
      } else {
          $btnClassMod = 'btn-default';
      }
      ?>
    <a class="btn btn-md <?=$btnClassMod?>" href="<?=SimpleRouter::getLink('MyNeighbourhood', 'index', $nbh->getSeq())?>"><?=$nbh->getName()?></a>
  <?php } // end foreach neighbourhoodsList ?>
  <a class="btn btn-md btn-success" href="<?=SimpleRouter::getLink('MyNeighbourhood', 'config', $view->selectedNbh)?>"><img src="/images/free_icons/cog.png" class="icon16" alt="<?=tr('config')?>">&nbsp;<?=tr('config')?></a>
<?php
if (MyNbhInteractiveController::VALIDATION_MODE
    && MyNbhInteractiveController::hasAccess(
        ApplicationContainer::GetAuthorizedUser()
    )
) { ?>
  <a class="btn btn-md btn-info" style="float: right" href="<?=SimpleRouter::getLink('MyNbhInteractiveController')?>"><?=tr('mynbh_interactive')?></a>
<?php
} ?>
  <div class="nbh-sort-list">
  <?php
    $order = [];
    foreach ($view->preferences['items'] as $key => $item) {
        $order[$item['order']] = $item;
        $order[$item['order']]['item'] = $key;
    }
    ksort($order);
    foreach ($order as $item) {
      $classSize = ($item['fullsize'] == 1) ? 'nbh-full' : 'nbh-half';
      switch ($item['item']) {
          case Neighbourhood::ITEM_MAP:
              $subTemplate = '/myNeighbourhood/sub_Map';
              break;
          case Neighbourhood::ITEM_LATESTCACHES:
              $subTemplate = '/myNeighbourhood/sub_LatestCaches';
              break;
          case Neighbourhood::ITEM_UPCOMINGEVENTS:
              $subTemplate = '/myNeighbourhood/sub_UpcommingEvents';
              break;
          case Neighbourhood::ITEM_FTFCACHES:
              $subTemplate = '/myNeighbourhood/sub_FTFCaches';
              break;
          case Neighbourhood::ITEM_LATESTLOGS:
              $subTemplate = '/myNeighbourhood/sub_LatestLogs';
              break;
          case Neighbourhood::ITEM_TITLEDCACHES:
              $subTemplate = '/myNeighbourhood/sub_TitledCaches';
              break;
          case Neighbourhood::ITEM_RECOMMENDEDCACHES:
              $subTemplate = '/myNeighbourhood/sub_RecommendedCaches';
              break;
          default:
              break;
      }
      ?>
      <div class="nbh-block <?=$classSize?>" id="item_<?=$item['item']?>">
          <?=$view->callSubTpl($subTemplate)?>
      </div>
  <?php } ?>
  </div>
  <?php
    list($latNS, $lat_h, $lat_min) = $view->neighbourhoodsList[$view->selectedNbh]->getCoords()->getLatitudeParts(Coordinates::COORDINATES_FORMAT_DEG_MIN);
    list($lonEW, $lon_h, $lon_min) =  $view->neighbourhoodsList[$view->selectedNbh]->getCoords()->getLongitudeParts(Coordinates::COORDINATES_FORMAT_DEG_MIN);
  ?>
  <div class="buffer"></div>
  <div class="align-center">
    <a href="/search.php?searchbydistance&amp;resetqueryid=y&amp;distance=<?=$view->neighbourhoodsList[$view->selectedNbh]->getRadius()?>&amp;latNS=<?=$latNS?>&amp;lat_h=<?=$lat_h?>&amp;lat_min=<?=$lat_min?>&amp;lonEW=<?=$lonEW?>&amp;lon_h=<?=$lon_h?>&amp;lon_min=<?=$lon_min?>#search-by-distance-table" class="btn btn-default btn-md">
      <?=tr('mnu_searchCache')?> (<?=$view->neighbourhoodsList[$view->selectedNbh]->getName()?>)
    </a>
    <a href="<?=SimpleRouter::getLink(MainMapController::class, 'embeded')?>?lat=<?=$view->neighbourhoodsList[$view->selectedNbh]->getCoords()->getLatitude()?>&amp;lon=<?=$view->neighbourhoodsList[$view->selectedNbh]->getCoords()->getLongitude()?>&amp;zoom=10"
       class="btn btn-default btn-md">
      <?=tr('mnu_cacheMap')?> (<?=$view->neighbourhoodsList[$view->selectedNbh]->getName()?>)
    </a>
  </div>
  <div class="buffer"></div>
  <div class="notice"><?=tr('myn_dragdrop')?></div>
  <div class="notice"><?=tr('myn_distances')?></div>
</div>
<script>
let changeOrderUri = "<?=SimpleRouter::getLink('MyNeighbourhood', 'changeOrderAjax')?>"
let changeSizeAjaxUri = "<?=SimpleRouter::getLink('MyNeighbourhood', 'changeSizeAjax')?>"
let changeDisplayAjaxUri = "<?=SimpleRouter::getLink('MyNeighbourhood', 'changeDisplayAjax')?>"
</script>
