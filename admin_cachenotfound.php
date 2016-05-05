<?php

use Utils\Database\XDb;

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

if ($usr['admin']) {

    if( isset($_REQUEST['show_reported']) ){
        tpl_set_var('show_reported', ($_REQUEST['show_reported'] == '1') ? ' checked="checked"' : '');
        $skipReported = '';
    }else{
        tpl_set_var('show_reported', '');
        $skipReported = 'AND c.cache_id NOT IN ( SELECT r.cache_id FROM reports r WHERE r.status <> 2 )';
    }

    if ( isset($_REQUEST['show_duplicated']) ){
        tpl_set_var('show_duplicated', ($_REQUEST['show_duplicated'] == '1') ? ' checked="checked"' : '');
        $distinct = '';
    }else{
        tpl_set_var('show_duplicated', '');
        $distinct = 'DISTINCT';
    }

    $query = "
        SELECT COUNT( $distinct(cl.date) ) ilosc, c.cache_id, c.name
        FROM cache_logs cl, caches c
        WHERE c.cache_id = cl.cache_id
            AND c.status = 1 /* status:active */
            AND cl.deleted = 0
            AND cl.type = 2 /* type=not-found*/
            AND cl.date > (
                    /* log date is newer than cache 'last_modified' */
                    IFNULL( c.last_modified, str_to_date('2000-01-01', '%Y-%m-%d'))
                    /*SELECT IFNULL( c1.last_modified, str_to_date('2000-01-01', '%Y-%m-%d'))
                    FROM caches c1
                    WHERE c1.cache_id = c.cache_id*/
                )
            AND cl.date > (
                    /* log date is newer than cache 'last_found' */
                    IFNULL(c.last_found, str_to_date('2000-01-01', '%Y-%m-%d'))
                    /*SELECT IFNULL(c2.last_found, str_to_date('2000-01-01', '%Y-%m-%d'))
                    FROM caches c2
                    WHERE c2.cache_id = c.cache_id*/
                )
            AND cl.date > (
                    /* log date is newer than last author comment */
                    SELECT IFNULL( MAX(cl1.date), str_to_date('2000-01-01', '%Y-%m-%d'))
                    FROM cache_logs cl1
                    WHERE cl1.cache_id = c.cache_id
                        AND cl1.user_id = c.user_id
                        AND cl1.type = 3 /* log-comment */
                )
            $skipReported /* = skip reported caches:
            AND c.cache_id NOT IN ( SELECT r.cache_id FROM reports r WHERE r.status <> 2 ) */

        GROUP BY cl.cache_id HAVING COUNT( $distinct(cl.date) ) > 2
        ORDER BY ilosc DESC, cache_id DESC ";

    d($query);

    $rs = XDb::xSql($query);

    $file_content = '';
    $i = 0;
    while( $record = XDb::xFetchArray($rs) ) {
        if (($i % 2) == 0) {
            $bgcolor = '#eeeeee';
        } else {
            $bgcolor = '#e0e0e0';
        }
        $file_content .= '<tr>';
        $file_content .= '<td bgcolor=' . $bgcolor . '>' . ($i + 1) . '</td>';
        $file_content .= '<td bgcolor=' . $bgcolor . '><a href="viewcache.php?cacheid=' . htmlspecialchars($record['cache_id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8') . '</a></td>';
        $file_content .= '<td bgcolor=' . $bgcolor . '>' . htmlspecialchars($record['ilosc'], ENT_COMPAT, 'UTF-8') . '</td>';
        $file_content .= '<td bgcolor=' . $bgcolor . '><a href="reportcache.php?cacheid=' . htmlspecialchars($record['cache_id'], ENT_COMPAT, 'UTF-8') . '">' . tr('report_problem') . '</a></td>';
        $file_content .= '</tr>';

        $i++;
    }

    XDb::xFreeResults($rs);

    tpl_set_var('results', $file_content);

    $tplname = 'admin_cachenotfound';
    tpl_BuildTemplate();
}
