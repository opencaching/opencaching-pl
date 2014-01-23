<?php
/***************************************************************************
                                                  ./tpl/stdstyle/news.inc.php
                                                            -------------------
        begin                : Mon October 12 2005
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

   Unicode Reminder ăĄă˘

     set template specific language variables

 ****************************************************************************/

 $tpl_newstopic = '<p class="content-title-noshade-size3">{date}</p><p class="content-txtbox-noshade-size5">{message}</p><br/>';
 $tpl_newstopic_without_topics = '<p class="content-title-noshade-size2"><span style="color: black;">{date}</span></p><p style="line-height: 1.6em;">{message} <br/> <b><a class="links" href="news.php">['.tr('more_news').' ...]</a></b></p><p>&nbsp;</p>';
 $tpl_newstopic_without_topic = '<p class="content-title-noshade-size1">{date}</p><p style="line-height: 1.6em;">{message}</p>';
 $tpl_newstopic_header = '<p class="content-title-noshade-size3"><b>'.tr('news').'</b></p><br/>';
?>
