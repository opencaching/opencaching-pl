<?php

use Utils\Database\XDb;

//prepare the templates and include all neccessary
global $dateFormat;
$rootpath = '../';
require_once($rootpath . 'lib/common.inc.php');

//Preprocessing
if ($error == false) {
    //get the news
    $perpage = 20;

    header('Content-type: application/xml; charset="utf-8"');
    $content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<rss version=\"2.0\">\n<channel>\n<title>$short_sitename - " . tr('rss_02') . "</title>\n<ttl>60</ttl><description>" . tr('rss_09') . " $site_name </description>\n<link>$absolute_server_URI</link><image>
        <title>$short_sitename - " . tr('rss_09') . "</title>\n
        <url>$absolute_server_URI/images/oc.png</url>
        <link>$absolute_server_URI</link><width>100</width><height>28</height></image>\n\n";

    $rs = XDb::xSql(
        'SELECT `caches`.`cache_id` `cacheid`, `user`.`user_id` `userid`, `caches`.`country` `country`, `caches`.`name` `cachename`,
                `user`.`username` `username`, `caches`.`date_created` `date_created`,
                IF((`caches`.`date_hidden`>`caches`.`date_created`), `caches`.`date_hidden`, `caches`.`date_created`) AS `date`,
                `cache_type`.`icon_large` `icon_large`
        FROM `caches`, `user`, `cache_type`
        WHERE `caches`.`status`=1 AND `caches`.`user_id`=`user`.`user_id`
            AND `caches`.`type`=`cache_type`.`id` AND `caches`.`date_hidden` <= NOW()
            AND `caches`.`date_created` <= NOW()
        ORDER BY IF((`caches`.`date_hidden`>`caches`.`date_created`), `caches`.`date_hidden`, `caches`.`date_created`) DESC, `caches`.`cache_id` DESC
        LIMIT ' . $perpage);

    while ($r = XDb::xFetchArray($rs)) {
        $thisline = "<item>\n<title>{cachename}</title>\n<description> " . tr('rss_10') . ": {cachename} - " . tr('rss_06') . ": {username} -  " . tr('rss_08') . ": {date} - " . tr('rss_11') . ": {country}</description>\n<link>$absolute_server_URI/viewcache.php?cacheid={cacheid}</link>\n</item>\n";

        $thisline = str_replace('{cacheid}', $r['cacheid'], $thisline);
        $thisline = str_replace('{userid}', $r['userid'], $thisline);
        $thisline = str_replace('{cachename}', htmlspecialchars($r['cachename']), $thisline);
        $thisline = str_replace('{username}', htmlspecialchars($r['username']), $thisline);
        $thisline = str_replace('{date}', date($dateFormat, strtotime($r['date'])), $thisline);
        $thisline = str_replace('{country}', htmlspecialchars($r['country']), $thisline);

        $content .= $thisline . "\n";
    }
    XDb::xFreeResults($rs);
    $content .= "</channel>\n</rss>\n";

    echo $content;
}

