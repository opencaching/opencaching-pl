<?php
/* * *************************************************************************
  ./tpl/stdstyle/removedesc.tpl.php
  -------------------
  begin                : July 7 2004
  copyright            : (C) 2004 The OpenCaching Group
  forum contact at     : http://www.opencaching.com/phpBB2

 * ************************************************************************* */

/* * *************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 * ************************************************************************* */

/* * **************************************************************************

  Unicode Reminder メモ

  remove a cache description

  desclang_name
  cachename
  cacheid_urlencode
  desclang_urlencode

 * ************************************************************************** */
?>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/trash.png" class="icon32" alt="" title="" align="middle" />&nbsp;{{remove_desc}}</div>
<p>&nbsp;</p>
<p>{{remove_desc_01}} &quot;{desclang_name}&quot; {{remove_desc_02}} &quot;{cachename}&quot;
    {{remove_desc_03}}</p>
<p><a href="removedesc.php?cacheid={cacheid_urlencode}&desclang={desclang_urlencode}&commit=1">{{remove_desc_04}}</a></p>
