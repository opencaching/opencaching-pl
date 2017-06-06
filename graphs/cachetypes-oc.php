<?php

use Utils\Database\XDb;

function normTo100($value, $sum)
{
    return $value * 100 / $sum;
}

function genStatPieUrl()
{
    $startDate = mktime(0, 0, 0, 1, 1, 2006);
    global $lang;
    if (Xdb::xContainsColumn('cache_type', $lang))
        $lang_db = XDb::xEscape($lang);
    else
        $lang_db = "en";

    // Get data
    $rsTypes = XDb::xSql(
        "SELECT COUNT(`caches`.`type`) `count`, `cache_type`.`$lang_db` AS `type`, `cache_type`.`color`
        FROM `caches` INNER JOIN `cache_type` ON (`caches`.`type`=`cache_type`.`id`)
        WHERE `status`=1
        GROUP BY `caches`.`type`
        ORDER BY `count` DESC");

    $yData = array();
    $xData = array();
    $colors = array();
    $url = "https://chart.googleapis.com/chart?chs=550x200&chd=t:";
    $sum = 0;
    while ($rTypes = XDb::xFetchArray($rsTypes)) {
        $yData[] = ' (' . $rTypes['count'] . ') ' . $rTypes['type'];
        $xData[] = $rTypes['count'];
        $colors[] = substr($rTypes['color'], 1);
        $sum += $rTypes['count'];
    }
    XDb::xFreeResults($rsTypes);
    foreach ($xData as $count) {
        $url .= normTo100($count, $sum) . ",";
    }

    $url = substr($url, 0, -1);
    $url .= "&cht=p3&chl=";

    foreach ($yData as $label) {
        $url .= urlencode($label) . "|";
    }
    $url = substr($url, 0, -1);

    $url .= "&chco=";
    foreach ($colors as $color) {
        $url .= urlencode($color) . ",";
    }
    return $url = substr($url, 0, -1);
}
