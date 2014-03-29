<?php
/***************************************************************************
    *
    *   This program is free software; you can redistribute it and/or modify
    *   it under the terms of the GNU General Public License as published by
    *   the Free Software Foundation; either version 2 of the License, or
    *   (at your option) any later version.
    *
    ***************************************************************************/
/* this file to be run witch CRON. generate last blog entery list on main page */
error_reporting(-1);
setlocale(LC_TIME, 'pl_PL.UTF-8');

global $lang, $rootpath, $config;
if (!isset($rootpath)) {
    $rootpath = __DIR__.'/../../../';
}

//include template handling
require_once($rootpath . 'lib/common.inc.php');
require_once($rootpath . 'lib/cache_icon.inc.php');
require_once($rootpath . 'lib/rss_php.php');

d($config);

$rss = new rss_php;

$rss->load($config['blogMostRecentRecordsUrl']);

$items = $rss->getItems();

$html = '<ul style="font-size: 11px;">';
$n=0;
foreach($items as $index => $item) {
    $pubDate = $item['pubDate'];
    $pubDate = strftime("%d-%m-%Y", strtotime($pubDate));
    $html .= '<li class="newcache_list_multi" style="margin-bottom:8px;"><img src="/tpl/stdstyle/images/free_icons/page.png" class="icon16" alt="news" title="news" /> '.$pubDate.' <a class=links href="'.$item['link'].'" title="'.$item['title'].'"><strong>'.$item['title'].'</strong></a></li>';
    $n=$n+1;
    if ($n==5) {
        break;
    }
}
$html.="</ul>";
$n_file = fopen($dynstylepath . "start_newblogs.inc.php", 'w');
fwrite($n_file, $html);
fclose($n_file);


echo $html;
