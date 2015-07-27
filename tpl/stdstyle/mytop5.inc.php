<?php
/***************************************************************************
		./tpl/stdstyle/mytop5.inc.php
		-------------------
		begin                : November 4 2005
		copyright            : (C) 2005 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

   Unicode Reminder ??

	***************************************************************************/

	$viewtop5_line = '<tr>
				<td bgcolor="{bgcolor}"><a href="viewcache.php?cacheid={cacheid}">{cachename}</a></td>
				<td bgcolor="{bgcolor}">[<a href="mytop5.php?action=delete&amp;cacheid={cacheid}">Usuń rekomendacje</a>]</td>
			</tr>
			';

	$notop5 = '<div class="notice">Nie masz rekomendowanych skrzynek.</div>';
	$msg_delete = '<div class="notice">Twoja rekomendacja dla skrzynki "<a href="viewcache.php?cacheid={cacheid}">{cachename}</a>" została usunięta!</div>
			<div class="buffer"></div>';

	$bgcolor1 = '#eeeeee';
	$bgcolor2 = '#e0e0e0';
?>
