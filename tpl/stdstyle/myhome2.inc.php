<?php

$no_hiddens = '<tr><td>{{no_hidden_caches}}</td></tr>';
$no_notpublished = '<tr><td>{{no_not_published_caches}}</td></tr>';
$no_logs = '<tr><td>{{no_log_entries}}</td></tr>';
$no_time_set = 'Still no time indicated';

$logtype[1] = 'Znalezine';
$logtype[2] = 'Nie znalezione';
$logtype[3] = 'Komentarz';

$cache_line = '<tr><td style="background-color: {bgcolor}">{cacheimage}&nbsp;{cachestatus}</td><td style="background-color: {bgcolor}">{date}</td><td style="background-color: {bgcolor}"><a href="viewcache.php?cacheid={cacheid}">{cachename}</a></td></tr>';
$cache_notpublished_line = '<tr><td>{cacheimage}&nbsp;{cachestatus}</td><td><a href="editcache.php?cacheid={cacheid}">{date}</a></td><td><a href="viewcache.php?cacheid={cacheid}">{cachename}</a></td></tr>';
$log_line = '<tr><td style="background-color: {bgcolor}">{logimage}&nbsp;{logtype}</td><td style="background-color: {bgcolor}">{date}</td><td style="background-color: {bgcolor}"><a href="viewcache.php?cacheid={cacheid}">{cachename}</a></td></tr>';

$bgcolor1 = '#ffffff';
$bgcolor2 = '#eeeeee';
?>
