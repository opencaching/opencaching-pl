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

function genChartDataCachesFound()
{
    global $lang;

    // Get data
    $rsStats = XDb::xSql(
        "SELECT c.caches, l.founds, c.month, c.year FROM
            (SELECT COUNT(*) `caches`, MONTH(`date_created`) `month`, YEAR(`date_created`) `year` FROM `caches`
                WHERE caches.status=1
                GROUP BY MONTH(`date_created`), YEAR(`date_created`)) c
        LEFT JOIN
            (SELECT COUNT(*) `founds`, MONTH(`date_created`) `month`, YEAR(`date_created`) `year` FROM `cache_logs`
                WHERE (`type`=1 OR `type`=7) AND `deleted`=0
                GROUP BY MONTH(`date_created`), YEAR(`date_created`)) l
            USING(year, month)
        ORDER BY year ASC, month ASC");

    $caches = 0;
    $finds = 0;
    $rows = array();
    $table = array();
    $table['cols'] = array (
        array('label' => tr('graph_statistics_04'), 'type' => 'date'),
        array('label' => tr('graph_statistics_02'), 'type' => 'number'),
        array('label' => tr('graph_statistics_03'), 'type' => 'number'),
    );
            
            while ($rStats = XDb::xFetchArray($rsStats)) {
                $temp = array();
                $caches += (int) $rStats['caches'];
                $finds += (int) $rStats['founds'];
                $temp[] = array('v' => 'Date(' . (string) $rStats['year'] . ', ' . (string) ($rStats['month'] - 1) . ')');
                $temp[] = array('v' => $caches);
                $temp[] = array('v' => $finds);
                $rows[] = array('c' => $temp);
            }
            
            $table['rows'] = $rows;
            
            return json_encode($table);
}
