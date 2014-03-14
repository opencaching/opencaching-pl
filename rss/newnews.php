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
        $content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<rss version=\"2.0\">\n<channel>\n<title>$short_sitename - ".tr('rss_04')."</title>\n<ttl>60</ttl><link>$absolute_server_URI/news.php</link>\n <description><![CDATA[".tr('rss_12')." $site_name]]></description><image>
        <title>$short_sitename - ".tr('rss_04')."</title>
        <url>$absolute_server_URI/images/oc.png</url>
        <link>$absolute_server_URI/news.php</link><width>100</width><height>28</height></image>\n";


            $rsNews = sql('SELECT `date_posted`, `content` FROM `news` WHERE `topic`=2 AND `display`=1 ORDER BY `date_posted` DESC LIMIT ' . $perpage);

            while ($rNews = sql_fetch_array($rsNews))
            {
            $thisline = "<item>\n<title>{date}</title>\n<description>{message}</description>\n<link>$absolute_server_URI/news.php</link>\n</item>\n";

                $thisline =str_replace('{date}',date($dateFormat, strtotime($rNews['date_posted'])), $thisline);
                $thisline = str_replace('{message}', htmlspecialchars($rNews['content']), $thisline);

            $content .= $thisline . "\n";
        }
        mysql_free_result($rsNews);
        $content .= "</channel>\n</rss>\n";

        echo $content;
    }
?>
