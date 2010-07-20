<?php


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

	$viewtop5_line = '<tr>
				<td bgcolor="{bgcolor}"><a href="viewcache.php?cacheid={cacheid}">{cachename}</a></td>
				<td bgcolor="{bgcolor}">[<a href="mytop5.php?action=delete&amp;cacheid={cacheid}">'.tr('delete_recommend').'</a>]</td>
			</tr>
			';
	$bgcolor1 = '#eeeeee';
	$bgcolor2 = '#e0e0e0';
	
	$ignoree = '<tr><td><a href="viewcache.php?cacheid={urlencode_cacheid}">{cachename}</a></td><td>&nbsp;</td><td nowrap="nowrap">[<a href="removeignore.php?cacheid={cacheid}&target=myignores.php">'.tr('off_ignore').'</a>]</td></tr>';
	$ignoreo = '<tr><td><a href="viewcache.php?cacheid={urlencode_cacheid}">{cachename}</a></td><td>&nbsp;</td><td nowrap="nowrap">[<a href="removeignore.php?cacheid={cacheid}&target=myignores.php">Wyłącz ignorowanie</a>]</td></tr>';
	$no_ignores = '<div class="notice">'.tr('no_ignores').'</div>';
	$title_text = tr('ignored_caches');
	

?>
