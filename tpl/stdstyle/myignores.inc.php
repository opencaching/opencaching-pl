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

	$bgcolor2 = '#eeeeee';
	$bgcolor1 = '#ffffff';
	
	$ignore = '<tr>
					<td style="background-color: {bgcolor}"><a href="viewcache.php?cacheid={urlencode_cacheid}">{cachename}</a></td>
					<td style="background-color: {bgcolor}">&nbsp;</td>
					<td nowrap="nowrap" style="background-color: {bgcolor}; text-align: center" ><a href="removeignore.php?cacheid={cacheid}&target=myignores.php" onclick="return confirm(\''.tr("myignores_1").'\');"><img style="vertical-align: middle;" src="tpl/stdstyle/images/log/16x16-trash.png" alt="" title='.tr('off_ignore').' /> </a></td>
				</tr>';
	
	$no_ignores = '<div class="notice">'.tr('no_ignores').'</div>';
	$title_text = tr('ignored_caches');
	

?>
