<?php
use src\Controllers\MyNbhInteractiveController;
use src\Controllers\MyNbhInteractiveApiController;
use src\Models\Coordinates\Coordinates;
use src\Models\GeoCache\CacheTitled;
use src\Models\GeoCache\GeoCacheCommons;
use src\Models\GeoCache\GeoCacheLog;
use src\Models\GeoCache\GeoCacheLogCommons;
use src\Models\Neighbourhood\Neighbourhood;
use src\Utils\Gis\Gis;
use src\Utils\Text\Formatter;
use src\Utils\Text\UserInputFilter;
use src\Utils\Uri\SimpleRouter;

?>
<div class="content2-container">
<?php
foreach ($view->neighbourhoodsList as $nbh) {
    if ($nbh->getSeq() == $view->selectedNbh) {
        $btnClassMod = 'btn-primary';
    } else {
        $btnClassMod = 'btn-default';
    } ?>
    <a class="btn btn-md <?= $btnClassMod; ?>" href="<?= SimpleRouter::getLink($view->controller, 'index', $nbh->getSeq()); ?>"><?= $nbh->getName(); ?></a>
<?php
} // end of foreach-neighbourhoodsList?>
    <a class="btn btn-md btn-success" href="<?= SimpleRouter::getLink($view->controller, 'config', $view->selectedNbh); ?>"><img src="/images/free_icons/cog.png" class="icon16" alt="<?= tr('config'); ?>">&nbsp;<?= tr('config'); ?></a>
<?php if (MyNbhInteractiveController::VALIDATION_MODE) { ?>
  <a class="btn btn-md btn-info" style="float: right" href="<?=SimpleRouter::getLink('MyNeighbourhoodController')?>"><?=tr('mynbh_original')?></a>
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

    $sectionSettings = [
        'header' => [],
        'contents' => [
            'empty' => 'list_of_caches_is_empty',
            'showDate' => 'placed',
            'details' => null,
        ],
        'columns' => [],
        'items' => [],
        'rowSub' => 'showCaches',
    ];

    switch ($item['item']) {
        case Neighbourhood::ITEM_MAP:
            $sectionSettings['header'] = [
                'title' => 'map',
                'buttonHideId' => 'nbh-map-hide',
                'buttonResizeId' => 'nbh-map-resize',
            ];
            break;
        case Neighbourhood::ITEM_LATESTCACHES:
            $sectionSettings['header'] = [
                'title' => 'newest_caches',
            ];
            $sectionSettings['contents']['details'] = 'latestCaches';
            $sectionSettings['items'] = $view->latestCaches;
            break;
        case Neighbourhood::ITEM_UPCOMINGEVENTS:
            $sectionSettings['header'] = [
                'title' => 'incomming_events',
            ];
            $sectionSettings['contents'] = [
                'empty' => 'list_of_events_is_empty',
                'details' => 'upcommingEvents',
            ];
            $sectionSettings['items'] = $view->upcomingEvents;
            //$sectionSettings['rowSub'] = null;
            break;
        case Neighbourhood::ITEM_FTFCACHES:
            $sectionSettings['header'] = [
                'title' => 'ftf_awaiting',
            ];
            $sectionSettings['contents']['details'] = 'ftfCaches';
            $sectionSettings['contents']['showDate'] = null;
            $sectionSettings['items'] = $view->FTFCaches;
            break;
        case Neighbourhood::ITEM_LATESTLOGS:
            $sectionSettings['header'] = [
                'title' => 'latest_logs',
            ];
            $sectionSettings['contents'] = [
                'empty' => 'list_of_latest_logs_is_empty',
                'details' => 'latestLogs',
            ];
            $sectionSettings['items'] = $view->latestLogs;
            $sectionSettings['rowSub'] = 'showLogs';
            break;
        case Neighbourhood::ITEM_TITLEDCACHES:
            $sectionSettings['header'] = [
                'title' => 'startPage_latestTitledCaches',
            ];
            $sectionSettings['contents']['details'] = 'titledCaches';
            $sectionSettings['contents']['showDate'] = 'titled';
            $sectionSettings['items'] = $view->latestTitled;
            break;
        case Neighbourhood::ITEM_RECOMMENDEDCACHES:
            $sectionSettings['header'] = [
                'title' => 'top_recommended',
                'showRecommendedImg' => true,
            ];
            $sectionSettings['contents']['details'] = 'mostRecommended';
            $sectionSettings['contents']['showDate'] = null;
            $sectionSettings['items'] = $view->topRatedCaches;
            break;
    } ?>
        <div class="nbh-block <?= $classSize; ?>" id="item_<?= $item['item']; ?>" section="<?= $item['item']; ?>">
<?php
    sectionHeader($sectionSettings);

    if ($item['item'] == Neighbourhood::ITEM_MAP) {
        showMap($view, $sectionSettings);
    } else {
        showRows($item['item'], $view, $sectionSettings);
    } ?>
        </div>
<?php
} // end of foreach-order?>
    </div>
<?php
[$latNS, $lat_h, $lat_min] = $view->neighbourhoodsList[$view->selectedNbh]->getCoords()->getLatitudeParts(Coordinates::COORDINATES_FORMAT_DEG_MIN);
[$lonEW, $lon_h, $lon_min] = $view->neighbourhoodsList[$view->selectedNbh]->getCoords()->getLongitudeParts(Coordinates::COORDINATES_FORMAT_DEG_MIN);
?>
    <div class="buffer"></div>
    <div class="align-center">
        <a href="/search.php?searchbydistance&amp;resetqueryid=y&amp;distance=<?= $view->neighbourhoodsList[$view->selectedNbh]->getRadius(); ?>&amp;latNS=<?= $latNS; ?>&amp;lat_h=<?= $lat_h; ?>&amp;lat_min=<?= $lat_min; ?>&amp;lonEW=<?= $lonEW; ?>&amp;lon_h=<?= $lon_h; ?>&amp;lon_min=<?= $lon_min; ?>#search-by-distance-table" class="btn btn-default btn-md">
            <?= tr('mnu_searchCache'); ?> (<?= $view->neighbourhoodsList[$view->selectedNbh]->getName(); ?>)
        </a>
        <a href="<?= SimpleRouter::getLink(MainMapController::class, 'embeded'); ?>?lat=<?= $view->neighbourhoodsList[$view->selectedNbh]->getCoords()->getLatitude(); ?>&amp;lon=<?= $view->neighbourhoodsList[$view->selectedNbh]->getCoords()->getLongitude(); ?>&amp;zoom=10" class="btn btn-default btn-md">
            <?= tr('mnu_cacheMap'); ?> (<?= $view->neighbourhoodsList[$view->selectedNbh]->getName(); ?>)
        </a>
    </div>
    <div class="buffer"></div>
    <div class="notice"><?= tr('myn_dragdrop'); ?></div>
    <div class="notice"><?= tr('myn_distances'); ?></div>
</div>
<script>
let changeOrderUri = "<?= SimpleRouter::getLink(MyNbhInteractiveApiController::class, 'changeOrder'); ?>"
let changeSizeUri = "<?= SimpleRouter::getLink(MyNbhInteractiveApiController::class, 'changeSize'); ?>"
let changeDisplayUri = "<?= SimpleRouter::getLink(MyNbhInteractiveApiController::class, 'changeDisplay'); ?>"
</script>
<?php
function sectionHeader($sectionSettings)
{ ?>
            <div class="nbh-block-header">
                <?= tr($sectionSettings['header']['title']); ?>
<?php
    if (
        isset($sectionSettings['header']['showRecommendedImg'])
        && $sectionSettings['header']['showRecommendedImg']
    ) { ?>
                <img src="/images/rating-star.png" alt="<?= tr('number_obtain_recommendations'); ?>">
<?php
    } ?>
                <div class='btn-group nbh-sm-buttons'>
                    <button class="btn btn-xs btn-default nbh-hide-toggle" title="<?= tr('myn_hlp_hide'); ?>"<?= isset($sectionSettings['header']['buttonHideId']) ? ' id="' . $sectionSettings['header']['buttonHideId'] . '"' : ''; ?>><span class="nbh-eye"></span></button>
                    <button class="btn btn-xs btn-default nbh-size-toggle" title="<?= tr('myn_hlp_resize'); ?>"<?= isset($sectionSettings['header']['buttonResizeId']) ? ' id="' . $sectionSettings['header']['buttonResizeId'] . '"' : ''; ?>><span class="ui-icon ui-icon-arrow-2-e-w"></span></button>
                </div>
            </div>
<?php
} //end of sectionHeader

function showMap($view, $sectionSettings)
{ ?>
            <div id="nbhmap" class="nbh-block-content nbh-usermap<?= $view->preferences['items'][Neighbourhood::ITEM_MAP]['show'] == true ? '' : ' nbh-nodisplay'; ?>"></div>
<?php
    $view->callChunk('interactiveMap/interactiveMap', $view->mapModel, 'nbhmap');
} //end of nbhMap

function showRows($sectionId, $view, $sectionSettings)
{ ?>
            <div class="nbh-block-content<?= $view->preferences['items'][$sectionId]['show'] == true ? '' : ' nbh-nodisplay'; ?>">
<?php
    if (
        ! empty($sectionSettings['rowSub'])
        && function_exists($sectionSettings['rowSub'])
    ) {
        $sectionSettings['rowSub']($sectionId, $view, $sectionSettings);
    } ?>
            </div>
<?php
} //end of showTable

function showCaches($sectionId, $view, $sectionSettings)
{
    if (empty($sectionSettings['items'])) { ?>
                <div class="align-center"><?= tr($sectionSettings['contents']['empty']); ?></div>
<?php
    } else {
        foreach ($sectionSettings['items'] as $cache) { ?>
                <div id="mynbh_item_<?= $sectionId; ?>_cacheMarker_<?= $cache->getCacheId(); ?>" class="nbh-line-container">
                    <a href="<?= $cache->getCacheUrl(); ?>">
                        <div class="nbh-image-container">
                            <img src="<?= $cache->getCacheIcon($view->user); ?>" class="nbh-icon" title="<?= tr($cache->getCacheTypeTranslationKey()); ?>" alt="<?= tr('cache'); ?>">
                        </div>
                        <div class="nbh-desc-container">
<?php
            if ($cache->getCacheType() === GeoCacheCommons::TYPE_EVENT) {
                showEventDesc($cache, $sectionId, $view, $sectionSettings);
            } else {
                showCacheDesc($cache, $sectionId, $view, $sectionSettings);
            } ?>
                        </div>
                    </a>
                </div>
<?php
        } // end of foreach-settings-items

        if (count($sectionSettings['items']) == $view->preferences['style']['caches-count']) { ?>
                <a class="btn btn-sm btn-default" href="<?= SimpleRouter::getLink($view->controller, $sectionSettings['contents']['details'], $view->selectedNbh); ?>" title="<?= tr('myn_hlp_more'); ?>"><?= tr('more'); ?></a>
<?php
        } // end if-count-settings-items
    } // end of if-empty-settings-items
} //end of showCaches

function showCacheDesc($cache, $sectionId, $view, $sectionSettings)
{ ?>
                            <strong><?= $cache->getCacheName(); ?></strong>
<?php
    if ($cache->isPowerTrailPart()) { ?>
                            <img src="<?= $cache->getPowerTrail()->getFootIcon(); ?>" alt="<?= tr('pt002'); ?>" title="<?= htmlspecialchars($cache->getPowerTrail()->getName()); ?>">
<?php
    } // end of if isPowerTrailPart?>
                            <span class="nbh-full-only"><?= tr('hidden_by'); ?></span><span class="nbh-min-only">|</span> <strong><?= $cache->getOwner()->getUserName(); ?></strong>
                            <span class="nbh-full-only"><br>
                                <img src="<?= $cache->getDifficultyIcon(); ?>" alt="<?= tr('task_difficulty'); ?>: <?= $cache->getDifficulty() / 2; ?>" title="<?= tr('task_difficulty'); ?>: <?= $cache->getDifficulty() / 2; ?>">
                                <img src="<?= $cache->getTerrainIcon(); ?>" alt="<?= tr('terrain_difficulty'); ?>: <?= $cache->getTerrain() / 2; ?>" title="<?= tr('terrain_difficulty'); ?>: <?= $cache->getTerrain() / 2; ?>"></span>
<?php
    if ($sectionSettings['contents']['showDate'] === 'placed') { ?>
                            <span class="nbh-nowrap"><?= Formatter::date($cache->getDatePlaced()); ?></span> |
<?php
    } elseif ($sectionSettings['contents']['showDate'] === 'titled') {
        $cacheTitled = CacheTitled::fromCacheIdFactory($cache->getCacheId()); ?>
                            <span class="nbh-min-only"> | </span><span class="nbh-nowrap"><?= Formatter::date($cacheTitled->getTitledDate()); ?></span> |
<?php
    } else { ?>
                            <span class="nbh-min-only"> | </span>
<?php
    } //end of if-settings-showDate?>
                            <span class="nbh-full-only"><?= tr($cache->getSizeTranslationKey()); ?> |</span>
                            <span class="nbh-nowrap"><?= round(Gis::distanceBetween($view->neighbourhoodsList[$view->selectedNbh]->getCoords(), $cache->getCoordinates())); ?> km
                                <img src="/images/misc/arrow-north.svg" class="nbh-arrow-north" alt="<?= tr('direction'); ?>" style="transform: rotate(<?= round(Gis::calcBearingBetween($view->neighbourhoodsList[$view->selectedNbh]->getCoords(), $cache->getCoordinates())); ?>deg)"></span>
<?php
    if ($cache->getRecommendations() > 0) { ?>
                            | <img src="/images/rating-star.png" alt="<?= tr('number_obtain_recommendations'); ?>"> (<?= $cache->getRecommendations(); ?>)
<?php
    } // end of if-getRecommendations
} // end of showCacheDesc

function showEventDesc($cache, $sectionId, $view, $sectionSettings)
{ ?>
                            <strong><?= $cache->getCacheName(); ?></strong>
                            <span class="nbh-full-only"><?= tr('organized_by'); ?></span><span class="nbh-min-only">|</span> <strong><?= $cache->getOwner()->getUserName(); ?></strong>
                            <span class="nbh-full-only"><br></span><span class="nbh-min-only">|</span>
                            <span class="nbh-nowrap"><?= Formatter::date($cache->getDatePlaced()); ?></span> |
                            <span class="nbh-nowrap"><?= round(Gis::distanceBetween($view->neighbourhoodsList[$view->selectedNbh]->getCoords(), $cache->getCoordinates())); ?> km
                            <img src="/images/misc/arrow-north.svg" class="nbh-arrow-north" alt="<?= tr('direction'); ?>" style="transform: rotate(<?= round(Gis::calcBearingBetween($view->neighbourhoodsList[$view->selectedNbh]->getCoords(), $cache->getCoordinates())); ?>deg)"></span>
                            <span class="nbh-full-only">| <img src="/images/log/16x16-will_attend.png" alt="<?= tr('will_attend'); ?>" title="<?= tr('will_attend'); ?>" class="icon16"> <?= $cache->getNotFounds(); ?></span>
<?php
} //end of showEventDesc

function showLogs($sectionId, $view, $sectionSettings)
{
    if (empty($sectionSettings['items'])) { ?>
                <div class="align-center"><?= tr($sectionSettings['contents']['empty']); ?></div>
<?php
    } else {
        foreach ($sectionSettings['items'] as $log) {
            $cache = $log->getGeoCache(); ?>
                <div id="mynbh_item_<?= $sectionId; ?>_logMarker_<?= $log->getId(); ?>" class="nbh-line-container">
                    <div class="lightTipped">
                        <div class="nbh-image-container">
                            <img src="<?= $log->getLogIcon(); ?>" class="nbh-icon" alt="<?= tr(GeoCacheLog::typeTranslationKey($log->getType())); ?>">
                        </div>
                        <div class="nbh-desc-container" onclick="location.href='<?= $log->getLogUrl(); ?>';" style="cursor: pointer;">
                            <img src="<?= $cache->getCacheIcon($view->user); ?>" class="icon16" title="<?= tr($cache->getCacheTypeTranslationKey()); ?>" alt="<?= tr('cache'); ?>">
                            <a href="<?= $log->getLogUrl(); ?>">
                                <strong><?= $cache->getCacheName(); ?></strong>
<?php
            if ($log->getGeoCache()->isPowerTrailPart()) { ?>
                                <img src="<?= $cache->getPowerTrail()->getFootIcon(); ?>" alt="<?= tr('pt002'); ?>" title="<?= htmlspecialchars($cache->getPowerTrail()->getName()); ?>">
<?php
            } // end of if isPowerTrailPart?>
                                <span class="nbh-full-only"><?= tr('hidden_by'); ?> <strong><?= $cache->getOwner()->getUserName(); ?></strong><br></span>
<?php
            if ($log->getType() == GeoCacheLogCommons::LOGTYPE_FOUNDIT && $log->isRecommendedByUser($log->getUser())) { ?>
                                <img src="/images/rating-star.png" alt="<?= tr('number_obtain_recommendations'); ?>"> |
<?php
            } // end of if-isRecommendedByUser?>
                                <span class="nbh-nowrap"><?= Formatter::date($log->getDate()); ?></span>
                                | <span class="nbh-nowrap"><?= round(Gis::distanceBetween($view->neighbourhoodsList[$view->selectedNbh]->getCoords(), $cache->getCoordinates())); ?> km
                                    <img src="/images/misc/arrow-north.svg" class="nbh-arrow-north" alt="<?= tr('direction'); ?>" style="transform: rotate(<?= round(Gis::calcBearingBetween($view->neighbourhoodsList[$view->selectedNbh]->getCoords(), $cache->getCoordinates())); ?>deg)"></span>
                                | <strong><?= $log->getUser()->getUserName(); ?></strong>
                            </a>
                        </div>
                    </div>
                    <div class="lightTip"><?= UserInputFilter::purifyHtmlString($log->getText()); ?></div>
                </div>
<?php
        } // end of foreach-settings-items

        if (count($sectionSettings['items']) == $view->preferences['style']['caches-count']) { ?>
                <a class="btn btn-sm btn-default" href="<?= SimpleRouter::getLink($view->controller, $sectionSettings['contents']['details'], $view->selectedNbh); ?>" title="<?= tr('myn_hlp_more'); ?>"><?= tr('more'); ?></a>
<?php
        } // end if-count-settings-items
    } // end of if-empty-settings-items
} //end of showLogs
?>
