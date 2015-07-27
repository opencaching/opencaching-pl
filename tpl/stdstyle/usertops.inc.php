<?php
/***************************************************************************
		./tpl/stdstyle/usertops.inc.php
		-------------------
		begin                : January 16 2007
		copyright            : (C) 2007 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

   Unicode Reminder メモ

	***************************************************************************/

	$viewtop5_line = '<tr>
				<td bgcolor="{bgcolor}"><a href="viewcache.php?cacheid={cacheid}">{cachename}</a> założył {ownername}</td>
				<td bgcolor="{bgcolor}">&nbsp;</td>
				<td bgcolor="{bgcolor}">&nbsp;</td>
			</tr>
			<tr><td class="spacer" colspan="3" bgcolor="{bgcolor}"></td></tr>
			';

	$notop5 = '<tr><td colspan="2">{username} nie ma jeszcze rekomendowanych skrzynek.</td></tr>';
	$user_notfound = 'Nie znaleziono użytkownika.';

	$bgcolor1 = '#eeeeee';
	$bgcolor2 = '#e0e0e0';
?>