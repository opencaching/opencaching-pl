<?php
/***************************************************************************
    *
    *   This program is free software; you can redistribute it and/or modify
    *   it under the terms of the GNU General Public License as published by
    *   the Free Software Foundation; either version 2 of the License, or
    *   (at your option) any later version.
    *
    ***************************************************************************/


    //prepare the templates and include all neccessary
    global $dateFormat;
    $rootpath = '../';
    require_once($rootpath . 'lib/common.inc.php');

    //Preprocessing
    if ($error == false)
    {
        //get the news
        $perpage = 20;

header('Content-type: application/xml; charset="utf-8"');
        $content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<rss version=\"2.0\">\n<channel>\n<title>$short_sitename - ".tr('rss_03')."</title>\n<ttl>60</ttl><description>".tr('rss_05')." $site_name </description>\n<link>$absolute_server_URI/newlogs.php</link><image>
        <title>$short_sitename - ".tr('rss_03')."</title>
        <url>$absolute_server_URI/images/oc.png</url>
        <link>$absolute_server_URI/newlogs.php</link><width>100</width><height>28</height></image>\n\n";

        $rs = sql("SELECT  cache_logs.id,
       cache_logs.cache_id AS cache_id,
       cache_logs.type AS log_type,
       cache_logs.date AS log_date,
       (SELECT name FROM caches
       WHERE cache_id = cache_logs.cache_id) AS cache_name,
       (SELECT username FROM user
       WHERE user_id = cache_logs.user_id) AS user_name,
       (SELECT text_combo FROM log_types_text
       WHERE log_types_id = cache_logs.type AND
               lang = '$lang') AS log_name
FROM    cache_logs
WHERE   cache_logs.deleted = 0
ORDER BY cache_logs.date_created DESC
LIMIT 20");

    //while ($r = sql_fetch_array($rs))
        for ($i = 0; $i < mysql_num_rows($rs); $i++)
        {
            $r = sql_fetch_array($rs);

  // ukrywanie autora komentarza COG przed zwykłym userem
     // (Łza)
        if ($r['log_type'] == 12)
              {
                   $r['user_name'] = tr('cog_user_name');
                              }
                // koniec ukrywania autora komentarza COG przed zwykłym userem
            $thisline = "<item>\n<title>{cachename}</title>\n<description> ".tr('rss_06').": {username} - ".tr('rss_07').": {logtype} - ".tr('rss_08').": {date} </description>\n<link>$absolute_server_URI/viewlogs.php?cacheid={cacheid}</link>\n</item>\n";

            $thisline = str_replace('{cacheid}', $r['cache_id'], $thisline);
//          $thisline = str_replace('{userid}', $r['userid'], $thisline);
            $thisline = str_replace('{cachename}', htmlspecialchars($r['cache_name']), $thisline);
            $thisline = str_replace('{logtype}', htmlspecialchars($r['log_name']), $thisline);
            $thisline = str_replace('{username}', htmlspecialchars($r['user_name']), $thisline);
            $thisline = str_replace('{date}', date($dateFormat, strtotime($r['log_date'])), $thisline);
            //$thisline = str_replace('{imglink}', 'tpl/stdstyle/images/'.getSmallCacheIcon($r['icon_large']), $thisline);

            $content .= $thisline . "\n";
        }
        mysql_free_result($rs);
        $content .= "</channel>\n</rss>\n";

        echo $content;
    }
?>
