<?php

use Utils\Database\XDb;

function genChartDataCacheTypes()
{
    global $lang;
    if (Xdb::xContainsColumn('cache_type', $lang))
        $lang_db = XDb::xEscape($lang);
    else
        $lang_db = "en";

    // Get data
    $rsTypes = XDb::xSql(
        "SELECT COUNT(`caches`.`type`) `count`, `cache_type`.`$lang_db` AS `type`
        FROM `caches` INNER JOIN `cache_type` ON (`caches`.`type`=`cache_type`.`id`)
        WHERE `status`=1
        GROUP BY `caches`.`type`
        ORDER BY `count` DESC");

    $rows = array();
    $table = array();
    $table['cols'] = array (
        array('label' => tr('cache_type'), 'type' => 'string'),
        array('label' => tr('number_of_caches'), 'type' => 'number'),
    );

    while ($rTypes = XDb::xFetchArray($rsTypes)) {
        $temp = array();
        $temp[] = array('v' => (string) $rTypes['type']);
        $temp[] = array('v' => (int) $rTypes['count']);
        $rows[] = array('c' => $temp);
    }

    $table['rows'] = $rows;

    return json_encode($table);
}
