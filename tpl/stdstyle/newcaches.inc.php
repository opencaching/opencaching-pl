<?php
/***************************************************************************
                                                  ./tpl/stdstyle/newcaches.inc.php
                                                            -------------------
        begin                : Mon November 5 2005
        copyright            : (C) 2005 The OpenCaching Group
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

     set template specific language variables
    <td width="22">&nbsp;<img src="images/flags/{country}.gif" border="0" alt="{country_name}" title="{country_name}" style="margin-top:4px;" /></td>
 ****************************************************************************/

 $next_img = '<img src="'.$stylepath.'/images/action/16x16-next.png" alt="&gt;"/>';
 $prev_img = '<img src="'.$stylepath.'/images/action/16x16-prev.png" alt="&lt;"/>';
 $last_img = '<img src="'.$stylepath.'/images/action/16x16-last.png" alt="&gt;&gt;"/>';
 $first_img = '<img src="'.$stylepath.'/images/action/16x16-first.png" alt="&lt;&lt;"/>';
 $next_img_inactive = '<img src="'.$stylepath.'/images/action/16x16-next_inactive.png" alt="&gt;"/>';
 $prev_img_inactive = '<img src="'.$stylepath.'/images/action/16x16-prev_inactive.png" alt="&lt;"/>';
 $last_img_inactive = '<img src="'.$stylepath.'/images/action/16x16-last_inactive.png" alt="&gt;&gt;"/>';
 $first_img_inactive = '<img src="'.$stylepath.'/images/action/16x16-first_inactive.png" alt="&lt;&lt;"/>';

 $tpl_line = '<tr><td style="width: 70px;">{date}</td><td></td><td width="22">{gkimage}</td><td width="22">{GPicon}</td><td width="22">{log_image}</td><td width="22"><img src="{imglink}" class="icon16" alt="Cache" title="{cachetype}" style="margin-top:4px;" /></td><td><b><a class="links" href="viewcache.php?cacheid={cacheid}">{cachename}</a></b></td><td><span class="txt-blue10">{region}</span></td><td><b><a class="links" href="viewprofile.php?userid={userid}">{username}</a></b></td></tr>';
?>
