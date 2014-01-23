<?php
/***************************************************************************
                                                  ./stdstyle/myhome.inc.php
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

   Unicode Reminder ??

     set template specific variables

 ****************************************************************************/

 $no_hiddens = "<tr><td>".tr('no_hidden_caches')."</td></tr>";
 $no_notpublished = "<tr><td>".tr('no_not_published_caches')."</td></tr>";
 $no_logs = "<tr><td>".tr('no_logs')."</td></tr>";
 $no_time_set = tr('no_time_indicated');

 $logtype[1] = tr('found');
 $logtype[2] = tr('not_found');
 $logtype[3] = tr('comment');

 $cache_line = '<tr><td>{cacheimage}&nbsp;{cachestatus}</td><td>{date}</td><td><a href="viewcache.php?cacheid={cacheid}">{cachename}</a></td></tr>';
 $cache_notpublished_line = '<tr><td>{cacheimage}&nbsp;{cachestatus}</td><td><a href="editcache.php?cacheid={cacheid}">{date}</a></td><td><a href="viewcache.php?cacheid={cacheid}">{cachename}</a></td></tr>';
 $log_line = '<tr><td>{logimage}&nbsp;{logtype}</td><td>{date}</td><td><a href="viewcache.php?cacheid={cacheid}">{cachename}</a></td></tr>';
 $cache_line_my_caches = '<tr><td>{logimage}&nbsp;{logtype}</td><td>{date}</td><td><a href="viewcache.php?cacheid={cacheid}">{cachename}</a> - <a href="viewprofile.php?userid={userid}">{username}</a></td></tr>';
?>
