<?php
use src\Controllers\LogEntryController;
use src\Models\GeoCache\CacheTitled;
use src\Models\GeoCache\GeoCache;
use src\Models\GeoCache\GeoCacheLog;
use src\Models\Neighbourhood\Neighbourhood;
use src\Utils\Gis\Gis;
use src\Utils\Text\Formatter;
use src\Utils\Text\UserInputFilter;
use src\Utils\Uri\SimpleRouter;
use src\Utils\View\View;

$sectionSettings = [
    'columns' => [],
    'items' => [],
];

switch ($view->sectionId) {
    case Neighbourhood::ITEM_LATESTCACHES:
    case Neighbourhood::ITEM_RECOMMENDEDCACHES:
        $sectionSettings = [
            'columns' => ['cache', 'date_hidden_label', 'new_logs_myn'],
            'items' => $view->caches,
            'itemDisplayFunctions' => [
                'showCache',
                'showDatePlaced',
                'showNewLogs',
            ],
            'showRecommendations' => true,
        ];
        break;
    case Neighbourhood::ITEM_UPCOMINGEVENTS:
        $sectionSettings = [
            'columns' => [
                'cache',
                'myn_event_organizer',
                'date_event_label',
                'new_logs_myn',
            ],
            'items' => $view->caches,
            'itemDisplayFunctions' => [
                'showEvent',
                'showOwner',
                'showDatePlaced',
                'showNewLogs',
            ],
        ];
        break;
    case Neighbourhood::ITEM_FTFCACHES:
        $sectionSettings = [
            'columns' => ['cache', 'date_hidden_label'],
            'items' => $view->caches,
            'itemDisplayFunctions' => [
                'showCache',
                'showDatePlaced',
            ],
            'addLinkToDatePlaced' => true,
        ];
        break;
    case Neighbourhood::ITEM_LATESTLOGS:
        $sectionSettings = [
            'columns' => ['cache', 'myn_log_txt'],
            'items' => $view->logs,
            'itemDisplayFunctions' => [
                'showCache',
                'showLog',
            ],
        ];
        break;
    case Neighbourhood::ITEM_TITLEDCACHES:
        $sectionSettings = [
            'columns' => [
                'cache',
                'date_hidden_label',
                'myn_titleddate',
                'new_logs_myn',
            ],
            'items' => $view->caches,
            'itemDisplayFunctions' => [
                'showCache',
                'showDatePlaced',
                'showDateTitled',
                'showNewLogs',
            ],
            'showRecommendations' => true,
        ];
        break;
}

$sectionSettings['logController'] = null;
?>
<div class="content2-container">
    <div class="nbh-pageheader">
        <?= tr($view->sectionTranslationKey); ?> (<?= ($view->selectedNbh == 0) ? tr('my_neighborhood') : $view->neighbourhoodsList[$view->selectedNbh]->getName(); ?>)
        <div class="nbh-md-buttons">
            <a href="<?= SimpleRouter::getLink($view->controller, 'index', $view->selectedNbh); ?>" class="btn btn-md btn-default nbh-back-btn"><?= tr('myn_btn_back'); ?></a>
        </div>
    </div>
    <table class="table full-width">
        <thead>
            <tr>
<?php
foreach ($sectionSettings['columns'] as $col) { ?>
                <th><span class="nbh-nowrap"><?= tr($col); ?></span></th>
<?php
} ?>
            </tr>
        </thead>
        <tbody>
<?php
foreach ($sectionSettings['items'] as $item) {
    $cache = $item instanceof GeoCacheLog ? $item->getGeoCache() : $item; ?>
            <tr>
<?php
    foreach ($sectionSettings['itemDisplayFunctions'] as $displayFunction) {
        $reflectionFunc = new ReflectionFunction($displayFunction);
        $reflectionParams = $reflectionFunc->getParameters();

        if (sizeof($reflectionParams) == 3) {
            $callParameter = null;

            if ($reflectionParams[0]->getType() == GeoCache::class) {
                $callParameter = $cache;
            } elseif (
                $reflectionParams[0]->getType() == GeoCacheLog::class
                && $item instanceof GeoCacheLog
            ) {
                $callParameter = $item;
            }

            if ($callParameter) {
                $reflectionFunc->invoke($callParameter, $sectionSettings, $view);
            }
        }
    } // end of foreach-settings-itemDisplayFunctions ?>
            </tr>
<?php
} // end of foreach-settings-items ?>
        </tbody>
    </table>
<?php
$view->callChunkInline('pagination', $view->paginationModel); ?>
    <div class="buffer"></div>
    <div class="notice"><?= tr('myn_distances'); ?></div>
</div>
<?php
function showCache(GeoCache $cache, array $sectionSettings, View $view)
{ ?>
                <td onclick="location.href='<?= $cache->getCacheUrl(); ?>';" style="cursor: pointer;">
                    <div class="nbh-image-container">
                        <img src="<?= $cache->getCacheIcon($view->user); ?>" class="nbh-icon" title="<?= tr($cache->getCacheTypeTranslationKey()); ?>" alt="<?= tr('cache'); ?>">
                    </div>
                    <div class="nbh-desc-container">
                        <a href="<?= $cache->getCacheUrl(); ?>">
                            <strong><?= $cache->getCacheName(); ?></strong>
<?php
    if ($cache->isPowerTrailPart()) { ?>
                            <img src="<?= $cache->getPowerTrail()->getFootIcon(); ?>" alt="<?= tr('pt002'); ?>" title="<?= htmlspecialchars($cache->getPowerTrail()->getName()); ?>">
<?php
    } // end of if-cache-isPowerTrailPart ?>
                            <span class="nbh-full-only"><?= tr('hidden_by'); ?></span><span class="nbh-min-only">|</span> <strong><?= $cache->getOwner()->getUserName(); ?></strong>
                            <span class="nbh-full-only"><br>
                                <img src="<?= $cache->getDifficultyIcon(); ?>" alt="<?= tr('task_difficulty'); ?>: <?= $cache->getDifficulty() / 2; ?>" title="<?= tr('task_difficulty'); ?>: <?= $cache->getDifficulty() / 2; ?>">
                                <img src="<?= $cache->getTerrainIcon(); ?>" alt="<?= tr('terrain_difficulty'); ?>: <?= $cache->getTerrain() / 2; ?>" title="<?= tr('terrain_difficulty'); ?>: <?= $cache->getTerrain() / 2; ?>">
                                <?= tr($cache->getSizeTranslationKey()); ?></span> |
                            <span class="nbh-nowrap"><?= round(Gis::distanceBetween($view->coords, $cache->getCoordinates())); ?> km
                                <img src="/images/misc/arrow-north.svg" class="nbh-arrow-north" alt="<?= tr('direction'); ?>" style="transform: rotate(<?= round(Gis::calcBearingBetween($view->coords, $cache->getCoordinates())); ?>deg)"></span>
<?php
    if (
        isset($sectionSettings['showRecommendations'])
        && $sectionSettings['showRecommendations']
        && $cache->getRecommendations() > 0
    ) { ?>
                                | <img src="/images/rating-star.png" alt="<?= tr('number_obtain_recommendations'); ?>">
                                (<?= $cache->getRecommendations(); ?>)
<?php
    } // end of if-settings-showRecommendations ?>
                        </a>
                    </div>
                </td>
<?php
} // end of showCache

function showEvent(GeoCache $cache, array $sectionSettings, View $view)
{ ?>
                <td onclick="location.href='<?= $cache->getCacheUrl(); ?>';" style="cursor: pointer;">
                    <div class="nbh-image-container">
                        <img src="<?= $cache->getCacheIcon($view->user); ?>" class="nbh-icon" title="<?= tr($cache->getCacheTypeTranslationKey()); ?>" alt="<?= tr('cache'); ?>">
                    </div>
                    <div class="nbh-desc-container">
                        <a href="<?= $cache->getCacheUrl(); ?>">
                            <strong><?= $cache->getCacheName(); ?></strong>
                            <span class="nbh-nowrap"><?= round(Gis::distanceBetween($view->coords, $cache->getCoordinates())); ?> km
                                <img src="/images/misc/arrow-north.svg" class="nbh-arrow-north" alt="<?= tr('direction'); ?>" style="transform: rotate(<?= round(Gis::calcBearingBetween($view->coords, $cache->getCoordinates())); ?>deg)"></span>
                            | <img src="/images/log/16x16-will_attend.png" alt="<?= tr('will_attend'); ?>" title="<?= tr('will_attend'); ?>" class="icon16"> <?= $cache->getNotFounds(); ?></span>
                        </a>
                    </div>
                </td>
<?php
} // end of showEvent

function showDatePlaced(GeoCache $cache, array $sectionSettings, View $view)
{ ?>
                <td<?= isset($sectionSettings['addLinkToDatePlaced']) && $sectionSettings['addLinkToDatePlaced'] ? ' onclick="location.href=' . $cache->getCacheUrl() . ';" style="cursor: pointer;"' : ''; ?>>
                    <?= Formatter::date($cache->getDatePlaced()); ?>
                </td>
<?php
}  // end of showDatePlaced

function showOwner(GeoCache $cache, array $sectionSettings, View $view)
{ ?>
                <td>
                    <strong><?= $cache->getOwner()->getUserName(); ?></strong>
                </td>
<?php
} // end of showOwner

function showDateTitled(GeoCache $cache, array $sectionSettings, View $view)
{
    $cacheTitled = CacheTitled::fromCacheIdFactory($cache->getCacheId()); ?>
                <td>
                    <?= Formatter::date($cacheTitled->getTitledDate()); ?>
                </td>
<?php
} // end of showTitledDate

function showNewLogs(GeoCache $cache, array $sectionSettings, View $view)
{
    if (empty($sectionSettings['logController'])) {
        $sectionSettings['logController'] = new LogEntryController();
    }
    $log = $sectionSettings['logController']->loadLogs($cache, false, 0, 1);

    if (! empty($log)) { ?>
                <td onclick="location.href='<?= $log[0]->getLogUrl(); ?>';" style="cursor: pointer;">
                    <div class="lightTipped">
                        <img src="<?= GeoCacheLog::GetIconForType($log[0]->getType()); ?>" class="icon16" alt="<?= tr(GeoCacheLog::typeTranslationKey($log[0]->getType())); ?>">
                        <?= Formatter::date($log[0]->getDate()); ?>
                        <strong><?= $log[0]->getUser()->getUserName(); ?></strong>
                        <span class="nbh-full-only"><br>
                            <?= mb_substr(strip_tags($log[0]->getText()), 0, 35); ?><?= mb_strlen(strip_tags($log[0]->getText())) > 35 ? '...' : ''; ?></span>
                    </div>
                    <div class="lightTip"><?= $log[0]->getText(); ?></div>
                </td>
<?php
    } else { ?>
                <td></td>
<?php
    } // end of if-not-empty-log
} // end of showNewLogs

function showLog(GeoCacheLog $log, array $sectionSettings, View $view)
{ ?>
                <td>
                    <div class="nbh-desc-container lightTipped" onclick="location.href='<?= $log->getLogUrl(); ?>';" style="cursor: pointer;">
                        <img src="<?= $log->getLogIcon(); ?>" class="icon16" alt="<?= tr(GeoCacheLog::typeTranslationKey($log->getType())); ?>">
                        <a href="<?= $log->getLogUrl(); ?>">
                            <span class="nbh-nowrap"><?= Formatter::date($log->getDate()); ?></span>
                            <strong><?= $log->getUser()->getUserName(); ?></strong>
<?php
    if ($log->getType() == GeoCacheLog::LOGTYPE_FOUNDIT && $log->isRecommendedByUser($log->getUser())) { ?>
                            <img src="/images/rating-star.png" alt="<?= tr('number_obtain_recommendations'); ?>">
<?php
    } // end of if-isRecommendedByUser ?>
                            <span class="nbh-full-only"><br>
                                <?= mb_substr(strip_tags($log->getText()), 0, 70); ?><?= mb_strlen(strip_tags($log->getText())) > 70 ? '...' : ''; ?></span>
                        </a>
                    </div>
                    <div class="lightTip"><?= UserInputFilter::purifyHtmlString($log->getText()); ?></div>
                </td>
<?php
} // end of showLog ?>
