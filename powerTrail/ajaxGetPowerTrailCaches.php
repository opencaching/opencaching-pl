<?php

use src\Models\GeoCache\GeoCache;
use src\Models\GeoCache\GeoCacheCommons;
use src\Models\GeoCache\GeoCacheLog;
use src\Models\PowerTrail\PowerTrail;

require_once __DIR__ . '/../lib/common.inc.php';

$powerTrail = new PowerTrail(['id' => (int) $_REQUEST['ptrail']]);

if (isset($_REQUEST['choseFinalCaches'])) {
    $choseFinalCaches = true;
} else {
    $choseFinalCaches = false;
}

displayAllCachesOfPowerTrail($powerTrail, $choseFinalCaches);

function displayAllCachesOfPowerTrail(PowerTrail $powerTrail, $choseFinalCaches)
{
    $userId = $_SESSION['user_id'] ?? -9999;
    $powerTrailCachesUserLogsByCache = $powerTrail->getFoundCachsByUser($userId);
    $geocacheFoundArr = [];

    foreach ($powerTrailCachesUserLogsByCache as $geocache) {
        $geocacheFoundArr[$geocache['geocacheId']] = $geocache;
    }

    if ($powerTrail->getCacheCount() == 0) {
        return '<br /><br />' . tr('pt082');
    }

    $statusIcons = [
        1 => '/images/log/16x16-published.png',
        2 => '/images/log/16x16-temporary.png',
        3 => '/images/log/16x16-trash.png',
        5 => '/images/log/16x16-need-maintenance.png',
        6 => '/images/log/16x16-stop.png',
    ];

    $statusDesc = [
        1 => tr('pt141'),
        2 => tr('pt142'),
        3 => tr('pt143'),
        5 => tr('pt144'),
        6 => tr('pt244'),
    ];

    $cacheRows = '<table class="ptCacheTable" align="center" width="90%"><tr>
        <th>' . tr('pt075') . '</th>
        <th>' . tr('pt076') . '</th>';

    if ($choseFinalCaches) {
        $cacheRows .= '<th></th>';
    }
    $cacheRows
            .= '   <th>' . tr('pt077') . '</th>
        <th><img src="images/log/16x16-found.png" /></th>
        <th>' . tr('pt078') . '</th>
        <th><img src="images/rating-star.png" /></th>
    </tr>';
    $totalFounds = 0;
    $totalTopRatings = 0;
    $bgcolor = '#ffffff';

    $cachetypes = array_fill_keys(GeoCache::CacheTypesArray(), 0); // array of all types
    $cacheSize = array_fill_keys(GeoCache::CacheSizesArray(), 0); // array of all types

    unset($_SESSION['geoPathCacheList']);

    // @var $geocache GeoCache
    foreach ($powerTrail->getGeocaches() as $geocache) {
        $_SESSION['geoPathCacheList'][] = $geocache->getCacheId();
        $totalFounds += $geocache->getFounds();
        $totalTopRatings += $geocache->getRecommendations();
        $cachetypes[$geocache->getCacheType()]++;
        $cacheSize[$geocache->getSizeId()]++;

        // powerTrailController::debug($cache); exit;
        if ($bgcolor == '#eeeeff') {
            $bgcolor = '#ffffff';
        } else {
            $bgcolor = '#eeeeff';
        }

        if ($geocache->isIsPowerTrailFinalGeocache()) {
            $bgcolor = '#000000';
            $fontColor = '<font color ="#ffffff">';
        } else {
            $fontColor = '';
        }
        $cacheRows .= '<tr bgcolor="' . $bgcolor . '">';

        //display icon found/not found depend on current user
        if (isset($geocacheFoundArr[$geocache->getCacheId()])) {
            $iconSrc = GeoCacheCommons::CacheIconByType(
                $geocache->getCacheType(),
                GeoCacheCommons::STATUS_READY,
                GeoCacheLog::LOGTYPE_FOUNDIT
            );
        } elseif ($geocache->getOwner()->getUserId() == $userId) {
            $iconSrc = GeoCacheCommons::CacheIconByType(
                $geocache->getCacheType(),
                GeoCacheCommons::STATUS_READY,
                null,
                false,
                true
            );
        } else {
            $iconSrc = GeoCacheCommons::CacheIconByType(
                $geocache->getCacheType(),
                GeoCacheCommons::STATUS_READY
            );
        }
        $cacheRows .= '<td align="center"><img src="' . $iconSrc . '" /></td>';

        //cachename, username
        $cacheRows .= '<td><b>'
                           . '<a href="' . $geocache->getWaypointId() . '">'
                                . $fontColor . $geocache->getCacheName()
                           . '</a></b> (' . $geocache->getOwner()->getUserName() . ') ';

        if ($geocache->isIsPowerTrailFinalGeocache()) {
            $cacheRows .= '<span class="finalCache">' . tr('pt148') . '</span>';
        }

        $cacheRows .= '</td>';

        //chose final caches
        if ($choseFinalCaches) {
            if ($geocache->isIsPowerTrailFinalGeocache()) {
                $checked = 'checked = "checked"';
            } else {
                $checked = '';
            }
            $cacheRows .= '<td>
                                <span class="ownerFinalChoice">
                                    <input type="checkbox" id="fcCheckbox' . $geocache->getCacheId() . '"
                                           onclick="setFinalCache(' . $geocache->getCacheId() . ')" ' . $checked . ' />
                                </span>
                           </td>';
        }
        //status
        $cacheRows .= '<td align="center"><img src="' . $statusIcons[$geocache->getStatus()] . '" title="' . $statusDesc[$geocache->getStatus()] . '"/></td>';
        //FoundCount
        $cacheRows .= '<td align="center">' . $fontColor . $geocache->getFounds() . '</td>';
        //score, toprating
        $cacheRows .= '<td align="center">' . ratings($geocache->getScore(), $geocache->getRatingVotes()) . '</td>';
        $cacheRows .= '<td align="center">' . $fontColor . $geocache->getRecommendations() . '</td>';
    }
    $cacheRows .= '
    <tr bgcolor="#efefff">
        <td></td>
        <td style="font-size: 9px;">' . tr('pt085') . '</td>
        <td></td>
        <td align="center" style="font-size: 9px;">' . $totalFounds . '</td>
        <td></td>
        <td align="center" style="font-size: 9px;">' . $totalTopRatings . '</td>
    </tr>
    </table>';

    $countCaches = $powerTrail->getCacheCount();

    if ($countCaches > 0) {
        // filter-out absent types and sizes
        $typesToShow = [GeoCache::TYPE_TRADITIONAL, GeoCache::TYPE_MULTICACHE, GeoCache::TYPE_QUIZ, GeoCache::TYPE_OTHERTYPE];
        $typesNumberList = [];
        $typesLabelsList = [];

        foreach ($typesToShow as $type) {
            if ($cachetypes[$type] > 0) {
                // there is at least one cache of such type
                $typesNumberList[] = $cachetypes[$type];
                $typesLabelsList[] = tr(GeoCache::CacheTypeTranslationKey($type)) . ' (' . round(($cachetypes[$type] * 100) / $countCaches) . '%)';
            }
        }

        // count the rest of types
        $restOfTypes = 0;

        foreach (array_diff(GeoCache::CacheTypesArray(), $typesToShow) as $type) {
            $restOfTypes += $cachetypes[$type];
        }

        if ($restOfTypes > 0) {
            // there is at least one cache of such type
            $typesNumberList[] = $restOfTypes;
            $typesLabelsList[] = tr('pt112') . ' (' . round(($restOfTypes * 100) / $countCaches) . '%)';
        }

        // same for sizes
        $sizesToShow = [GeoCache::SIZE_NANO, GeoCache::SIZE_MICRO, GeoCache::SIZE_SMALL,
            GeoCache::SIZE_REGULAR, GeoCache::SIZE_LARGE, GeoCache::SIZE_XLARGE, GeoCache::SIZE_NONE];
        $sizesNumberList = [];
        $sizesLabelsList = [];

        foreach ($sizesToShow as $size) {
            if ($cacheSize[$size] > 0) {
                // there is at least one cache of such type
                $sizesNumberList[] = $cacheSize[$size];
                $sizesLabelsList[] = tr(GeoCache::CacheSizeTranslationKey($size)) . ' (' . round(($cacheSize[$size] * 100) / $countCaches) . '%)';
            }
        }

        echo ' <table align="center">
                    <tr>
                        <td align=center width="50%">
                            ' . tr('pt107') . '<br />
                            <canvas id="cacheTypeChart" width="370" height="120"></canvas>
                        </td>
                        <td align=center width="50%">
                            ' . tr('pt106') . '<br />
                            <canvas id="cacheSizeChart" width="370" height="120"></canvas>
                        </td>
                    </tr>
                </table><br /><br />
            
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    var typeCtx = document.getElementById(\'cacheTypeChart\').getContext(\'2d\');
                    var sizeCtx = document.getElementById(\'cacheSizeChart\').getContext(\'2d\');
            
                    var typeChart = new Chart(typeCtx, {
                        type: \'pie\',
                        data: {
                            labels: ' . json_encode($typesLabelsList) . ',
                            datasets: [{
                                data: ' . json_encode($typesNumberList) . ',
                                backgroundColor: [
                                    \'#00aa00\',
                                    \'#FFEB0D\',
                                    \'#0000cc\',
                                    \'#cccccc\',
                                    \'#eeeeee\'
                                ]
                            }]
                        },
                        options: {
                            responsive: false,
                            plugins: {
                                legend:{
                                    position: \'left\'
                                }
                            }
                        }
                    });
            
                    var sizeChart = new Chart(sizeCtx, {
                        type: \'pie\',
                        data: {
                            labels: ' . json_encode($sizesLabelsList) . ',
                            datasets: [{
                                data: ' . json_encode($sizesNumberList) . ',
                                backgroundColor: [
                                    \'#FFEB0D\',
                                    \'#0000aa\',
                                    \'#00aa00\',
                                    \'#aa0000\',
                                    \'#aaaa00\',
                                    \'#00aaaa\',
                                    \'#cccccc\'
                                ]
                            }]
                        },
                        options: {
                            responsive: false,
                            plugins: {
                                legend:{
                                    position: \'left\'
                                }
                            }
                        }
                    });
                </script>';
    }

    echo $cacheRows;
}

function ratings($score, $votes)
{
    if ($votes < 3) {
        return '<span style="color: gray">' . tr('pt083') . '</span>';
    }
    $scoreNum = GeoCacheCommons::ScoreAsRatingNum($score);

    switch ($scoreNum) {
        case 1: return '<span style="color: #790000">' . tr('pt074') . '</span>';
        case 2: return '<span style="color: #BF3C3C">' . tr('pt073') . '</span>';
        case 3: return '<span style="color: #505050">' . tr('pt072') . '</span>';
        case 4: return '<span style="color: #518C00">' . tr('pt071') . '</span>';
        case 5: return '<span style="color: #009D00">' . tr('pt070') . '</span>';
    }
}
