<?php

use lib\Objects\GeoCache\GeoCacheCommons;
use lib\Objects\PowerTrail\PowerTrail;

require_once __DIR__ . '/../lib/common.inc.php';

$powerTrail = new PowerTrail(array('id' => (int) $_REQUEST['ptrail']));

if (isset($_REQUEST['choseFinalCaches'])) {
    $choseFinalCaches = true;
} else {
    $choseFinalCaches = false;
}

print displayAllCachesOfPowerTrail($powerTrail, $choseFinalCaches);

function displayAllCachesOfPowerTrail(PowerTrail $powerTrail, $choseFinalCaches)
{
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : -9999;
    $powerTrailCachesUserLogsByCache = $powerTrail->getFoundCachsByUser($userId);
    $geocacheFoundArr = array();
    foreach ($powerTrailCachesUserLogsByCache as $geocache) {
        $geocacheFoundArr[$geocache['geocacheId']] = $geocache;
    }


    if ($powerTrail->getCacheCount() == 0) {
        return '<br /><br />' . tr('pt082');
    }

    $statusIcons = array(
        1 => '/tpl/stdstyle/images/log/16x16-published.png',
        2 => '/tpl/stdstyle/images/log/16x16-temporary.png',
        3 => '/tpl/stdstyle/images/log/16x16-trash.png',
        5 => '/tpl/stdstyle/images/log/16x16-need-maintenance.png',
        6 => '/tpl/stdstyle/images/log/16x16-stop.png'
    );

    $statusDesc = array(
        1 => tr('pt141'),
        2 => tr('pt142'),
        3 => tr('pt143'),
        5 => tr('pt144'),
        6 => tr('pt244')
    );

    $cacheTypesIcons = cache::getCacheIconsSet();
    $cacheRows = '<table class="ptCacheTable" align="center" width="90%"><tr>
        <th>' . tr('pt075') . '</th>
        <th>' . tr('pt076') . '</th>';
    if ($choseFinalCaches) {
        $cacheRows .= '<th></th>';
    }
    $cacheRows .=
            '   <th>' . tr('pt077') . '</th>
        <th><img src="tpl/stdstyle/images/log/16x16-found.png" /></th>
        <th>' . tr('pt078') . '</th>
        <th><img src="images/rating-star.png" /></th>
    </tr>';
    $totalFounds = 0;
    $totalTopRatings = 0;
    $bgcolor = '#ffffff';
    $cachetypes = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0,);
    $cacheSize = array(2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0,);
    unset($_SESSION['geoPathCacheList']);

    /* @var $geocache GeoCache */
    foreach ($powerTrail->getGeocaches() as $geocache) {
        $_SESSION['geoPathCacheList'][] = $geocache->getCacheId();
        $totalFounds += $geocache->getFounds();
        $totalTopRatings += $geocache->getRecommendations();
        $cachetypes[$geocache->getCacheType()] ++;
        $cacheSize[$geocache->getSizeId()] ++;
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
            $cacheRows .= '<td align="center"><img src="' . $cacheTypesIcons[$geocache->getCacheType()]['iconSet'][1]['iconSmallFound'] . '" /></td>';
        } elseif ($geocache->getOwner()->getUserId() == $userId) {
            $cacheRows .= '<td align="center"><img src="' . $cacheTypesIcons[$geocache->getCacheType()]['iconSet'][1]['iconSmallOwner'] . '" /></td>';
        } else {
            $cacheRows .= '<td align="center"><img src="' . $cacheTypesIcons[$geocache->getCacheType()]['iconSet'][1]['iconSmall'] . '" /></td>';
        }
        //cachename, username
        $cacheRows .= '<td><b><a href="' . $geocache->getWaypointId() . '">' . $fontColor . $geocache->getCacheName() . '</b></a> (' . $geocache->getOwner()->getUserName() . ') ';
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
            $cacheRows .= '<td><span class="ownerFinalChoice"><input type="checkbox" id="fcCheckbox' . $geocache->getCacheId() . '" onclick="setFinalCache(' . $geocache->getCacheId() . ')" ' . $checked . ' /></span></td>';
        }
        //status
        $cacheRows .= '<td align="center"><img src="' . $statusIcons[$geocache->getStatus()] . '" title="' . $statusDesc[$geocache->getStatus()] . '"/></td>';
        //FoundCount
        $cacheRows .= '<td align="center">' . $fontColor . $geocache->getFounds() . '</td>';
        //score, toprating
        $cacheRows .= '<td align="center">' . ratings($geocache->getScore(), $geocache->getRatingVotes()) . '</td>';
        $cacheRows .= '<td align="center">' . $fontColor . $geocache->getRecommendations() . '</td>';

        '</tr>';
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
    $restCaches = $cachetypes[4] + $cachetypes[5] + $cachetypes[6] + $cachetypes[8] + $cachetypes[9] + $cachetypes[10];
    $countCaches = $powerTrail->getCacheCount();
    if($countCaches > 0) {
        $restCachesPercent = round(($restCaches * 100) / $countCaches);
        $cachePercent = [];
        $cacheSizePercent = [];
        foreach ($cachetypes as $key => $value) {
            $cachePercent[$key] = round(($value * 100) / $countCaches);
        }
        foreach ($cacheSize as $key => $value) {
            $cacheSizePercent[$key] = round(($value * 100) / $countCaches);
        }
        $img = '<table align="center"><tr><td align=center width="50%">' . tr('pt107') . '<br /><img src="https://chart.googleapis.com/chart?chs=350x100&chd=t:' . $cachetypes[2] . ',' . $cachetypes[3] . ',' . $cachetypes[7] . ',' . $cachetypes[1] . ',' . $restCaches . '&cht=p3&chl=' . $cachetypes[2] . '|' . $cachetypes[3] . '|' . $cachetypes[7] . '|' . $cachetypes[1] . '|' . $restCaches . '&chco=00aa00|FFEB0D|0000cc|cccccc|eeeeee&&chdl=%20' . tr('pt108') . '%20(' . $cachePercent[2] . '%)|' . tr('pt109') . '%20(' . $cachePercent[3] . '%)|' . tr('pt110') . '%20(' . $cachePercent[7] . '%)|' . urlencode(tr('pt111')) . '%20(' . $cachePercent[1] . '%)|' . urlencode(tr('pt112')) . '%20(' . $restCachesPercent . '%)" /></td>';
        $img .= '<td align=center width="50%">' . tr('pt106') . '<br /><img src="https://chart.googleapis.com/chart?chs=350x100&chd=t:' . $cacheSize[2] . ',' . $cacheSize[3] . ',' . $cacheSize[4] . ',' . $cacheSize[5] . ',' . $cacheSize[6] . '&cht=p3&chl=%20' . $cacheSize[2] . '|' . $cacheSize[3] . '|' . $cacheSize[4] . '|' . $cacheSize[5] . '|' . $cacheSize[6] . '&chco=0000aa|00aa00|aa0000|aaaa00|00aaaa&&chdl=' . urlencode(tr('pt113')) . '%20(' . $cacheSizePercent[2] . '%)|' . urlencode(tr('pt114')) . '%20(' . $cacheSizePercent[3] . '%)|' . urlencode(tr('pt115')) . '%20(' . $cacheSizePercent[4] . '%)|' . urlencode(tr('pt116')) . '%20(' . $cacheSizePercent[5] . '%)|' . urlencode(tr('pt117')) . '%20(' . $cacheSizePercent[6] . '%)" /></td></tr></table><br /><br />';
    } else {
        $img = '';
    }
    return $img . $cacheRows;
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
