<?php

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

//


if ($usr['admin']) {
    $options['show_reported'] = isset($_REQUEST['show_reported']) ? $_REQUEST['show_reported'] : 0;
    $options['show_duplicated'] = isset($_REQUEST['show_duplicated']) ? $_REQUEST['show_duplicated'] : 0;

    if (!isset($options['show_reported'])) {
        $options['show_reported'] = 0;
    }
    if (!isset($options['show_duplicated'])) {
        $options['show_duplicated'] = 0;
    }

    // Zapytanie do bazy - początek

    $query = "SELECT ";

    if ($options['show_duplicated'] == 0) {
        $query .= " COUNT(DISTINCT(cl.date)) ilosc, c.cache_id, c.name";
    } else {
        $query .= " COUNT(cl.date) ilosc, c.cache_id, c.name";
    }

    $query .= " FROM
                cache_logs cl, caches c
            WHERE
                c.cache_id = cl.cache_id
                AND c.status =1
                AND cl.deleted = 0
                AND cl.type = 2
                AND cl.date > (SELECT IFNULL(c1.last_modified, str_to_date('2000-01-01', '%Y-%m-%d')) FROM caches c1 WHERE c1.cache_id = c.cache_id)
                AND cl.date > (SELECT IFNULL(c2.last_found, str_to_date('2000-01-01', '%Y-%m-%d')) FROM caches c2 WHERE c2.cache_id = c.cache_id)
                AND cl.date > (SELECT IFNULL(MAX(cl1.date), str_to_date('2000-01-01', '%Y-%m-%d')) FROM cache_logs cl1 where cl1.user_id = c.user_id AND cl1.cache_id = c.cache_id AND cl1.type = 3)";

    if ($options['show_reported'] == 0) {
        $query .= " AND c.cache_id NOT IN (SELECT r.cache_id FROM reports r WHERE r.status <> 2)";
    }

    $query .= " GROUP BY
                cl.cache_id
            HAVING";

    if ($options['show_duplicated'] == 0) {
        $query .= " COUNT(DISTINCT(cl.date)) > 2";
    } else {
        $query .= " COUNT(cl.date) > 2";
    }

    $query .= " ORDER BY
                ilosc DESC,
                cache_id DESC
            ";

    // Zapytanie do bazy - koniec

    $rs = sql($query);

    $file_content = '';

    for ($i = 0; $i < mysql_num_rows($rs); $i++) {
        if (($i % 2) == 0) {
            $bgcolor = '#eeeeee';
        } else {
            $bgcolor = '#e0e0e0';
        }
        $record = sql_fetch_array($rs);
        $file_content .= '<tr>';
        $file_content .= '<td bgcolor=' . $bgcolor . '>' . ($i + 1) . '</td>';
        $file_content .= '<td bgcolor=' . $bgcolor . '><a href="viewcache.php?cacheid=' . htmlspecialchars($record['cache_id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8') . '</a></td>';
        $file_content .= '<td bgcolor=' . $bgcolor . '>' . htmlspecialchars($record['ilosc'], ENT_COMPAT, 'UTF-8') . '</td>';
        $file_content .= '<td bgcolor=' . $bgcolor . '><a href="reportcache.php?cacheid=' . htmlspecialchars($record['cache_id'], ENT_COMPAT, 'UTF-8') . '">' . tr('report_problem') . '</a></td>';
        $file_content .= '</tr>';
    }

    mysql_free_result($rs);

    tpl_set_var('show_reported', ($options['show_reported'] == '1') ? ' checked="checked"' : '');
    tpl_set_var('show_duplicated', ($options['show_duplicated'] == '1') ? ' checked="checked"' : '');

    tpl_set_var('results', $file_content);

    $tplname = 'admin_cachenotfound';
    tpl_BuildTemplate();
}
?>
