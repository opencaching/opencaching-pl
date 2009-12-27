<?php
/***************************************************************************
	*
	*   This program is free software; you can redistribute it and/or modify
	*   it under the terms of the GNU General Public License as published by
	*   the Free Software Foundation; either version 2 of the License, or
	*   (at your option) any later version.
	*
	***************************************************************************/

 $no_time_set = tr('no_time_indicated');

 $logtype[1] = tr('found');
 $logtype[2] = tr('not_found');
 $logtype[3] = tr('comment');

 $cache_line = '<li style="margin: -0.9em 0px 0.9em 0px; padding: 0px 0px 0px 10px; list-style-type: none; line-height: 1.2em; font-size: 115%;">{cacheimage}&nbsp;{cachestatus} &nbsp; {date} &nbsp; <a href="viewcache.php?cacheid={cacheid}">{cachename}</a></li>';
 $cache_notpublished_line = '<li style="margin: -0.9em 0px 0.9em 0px; padding: 0px 0px 0px 10px; list-style-type: none; line-height: 1.2em; font-size: 115%;">{cacheimage}&nbsp;{cachestatus} &nbsp; <a href="editcache.php?cacheid={cacheid}">{date}</a> &nbsp; <a href="viewcache.php?cacheid={cacheid}">{cachename}</a></li>';
 $log_line = '<li style="margin: -0.9em 0px 0.9em 0px; padding: 0px 0px 0px 10px; list-style-type: none; line-height: 1.2em; font-size: 115%;">{logimage} &nbsp; <img src="tpl/stdstyle/images/{cacheimage}" border="0" alt="" />&nbsp; {date} &nbsp; <a href="viewcache.php?cacheid={cacheid}">{cachename}</a></li>';
 $cache_line_my_caches = '<li style="margin: -0.9em 0px 0.9em 0px; padding: 0px 0px 0px 10px; list-style-type: none; line-height: 1.2em; font-size: 115%;">{logimage} &nbsp; {date} &nbsp; <a href="viewcache.php?cacheid={cacheid}">{cachename}</a> - <a href="viewprofile.php?userid={userid}">{username}</a></li>';
?>
