<?php

use Utils\Database\XDb;
//prepare the templates and include all neccessary
global $dateFormat;
$rootpath = '../';
require_once($rootpath . 'lib/common.inc.php');

//Preprocessing
if ($error == false) {

    if (isset($_REQUEST['userid'])) {
        $user_id = $_REQUEST['userid'];
        tpl_set_var('userid', $user_id);
    }else{
        echo "No userid param... exit.";
        exit;
    }

    $user_record['username'] = XDb::xMultiVariableQueryValue(
        "SELECT username FROM user WHERE user_id= :1 LIMIT 1", '-no-name-', $user_id);

    header('Content-type: application/xml; charset="utf-8"');

    $content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<rss version=\"2.0\">\n<channel>\n<title>$short_sitename - " . tr('rss_13') . " {username}</title>\n<ttl>60</ttl><description>" . tr('rss_14') . " {username}</description>\n<link>$absolute_server_URI</link><image>
        <title>$short_sitename - " . tr('rss_13') . ": {username}</title>
        <url>$absolute_server_URI/images/oc.png</url>
        <link>$absolute_server_URI</link><width>100</width><height>28</height></image>\n\n";

    $content = str_replace('{username}', htmlspecialchars($user_record['username']), $content);

    $rs = XDb::xSql(
        "SELECT cache_logs.id, cache_logs.cache_id AS cache_id, cache_logs.type AS log_type,
                cache_logs.date AS log_date, caches.name AS cache_name, user.username AS user_name,
                user.user_id AS user_id, caches.wp_oc AS wp_name, caches.type AS cache_type,
                (
                    SELECT text_combo FROM log_types_text
                    WHERE log_types_id = cache_logs.type AND lang = ?
                ) AS log_name,
                IF(ISNULL(`cache_rating`.`cache_id`), 0, 1) AS `recommended`,
                COUNT(gk_item.id) AS geokret_in
        FROM (cache_logs INNER JOIN caches ON (caches.cache_id = cache_logs.cache_id))
            INNER JOIN user ON (cache_logs.user_id = user.user_id)
            INNER JOIN log_types ON (cache_logs.type = log_types.id)
            INNER JOIN cache_type ON (caches.type = cache_type.id)
            LEFT JOIN `cache_rating` ON `cache_logs`.`cache_id`=`cache_rating`.`cache_id`
                AND `cache_logs`.`user_id`=`cache_rating`.`user_id`
            LEFT JOIN gk_item_waypoint ON gk_item_waypoint.wp = caches.wp_oc
            LEFT JOIN gk_item ON gk_item.id = gk_item_waypoint.id
                AND gk_item.stateid<>1 AND gk_item.stateid<>4 AND gk_item.typeid<>2 AND gk_item.stateid !=5
        WHERE cache_logs.deleted=0 AND `cache_logs`.`user_id`= ?
        GROUP BY cache_logs.id
        ORDER BY cache_logs.date_created DESC LIMIT 20", $lang, $user_id);

    while ($r = XDb::xFetchArray($rs) ){

        $thisline = "<item>\n<title>{cachename}</title>\n<description>  " . tr('rss_08') . ": {date} - " . tr('rss_07') . ": {logtype}{rate}{gk}</description>\n<link>$absolute_server_URI/viewlogs.php?cacheid={cacheid}</link>\n</item>\n";

        if ($r['geokret_in'] != '0') {
            $thisline = str_replace('{gk}', ' - ' . tr('rss_15'), $thisline);
        } else {
            $thisline = str_replace('{gk}', ' - ' . tr('rss_16'), $thisline);
        }

        //$rating_picture
        if ($r['recommended'] == 1) {
            $thisline = str_replace('{rate}', ' - ' . tr('rss_17'), $thisline);
        } else {
            $thisline = str_replace('{rate}', ' - ' . tr('rss_18'), $thisline);
        }
        $thisline = str_replace('{cacheid}', $r['cache_id'], $thisline);
        ;
        $thisline = str_replace('{cachename}', htmlspecialchars($r['cache_name']), $thisline);
        $thisline = str_replace('{logtype}', htmlspecialchars($r['log_name']), $thisline);
        $thisline = str_replace('{date}', date($dateFormat, strtotime($r['log_date'])), $thisline);
        $content .= $thisline . "\n";
    }
    XDb::xFreeResults($rs);
    $content .= "</channel>\n</rss>\n";

    echo $content;
}
