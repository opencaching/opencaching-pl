<?php
/***************************************************************************
                                                  ./tpl/stdstyle/myhome.inc.php
                                                            -------------------
        begin                : July 25 2004
        copyright            : (C) 2004 The OpenCaching Group
        forum contact at     : http://www.opencaching.com/phpBB2

    ***************************************************************************/

/***************************************************************************
    *
    *   This program is free software; you can redistribute it and/or modify
    *   it under the terms of the GNU General Public License as published by
    *   the Free Software Foundation; either version 2 of the License, or
    *   (at your option) any later version.
    *
    ***************************************************************************/

/****************************************************************************

   Unicode Reminder メモ

     set template specific variables

 ****************************************************************************/

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
