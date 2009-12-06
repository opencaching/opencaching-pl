<?php
/***************************************************************************
											./tpl/stdstyle/myhome.tpl.php
															-------------------
		begin                : Mon June 14 2004
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

	 user home

 ****************************************************************************/
?>
<table class="content">
	<colgroup>
		<col width="100">
		<col>
	</colgroup>
	<tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/blue.png" border="0" width="32" height="32" alt="Moje konto" title="Moje konto" align="middle"><font size="4">  <b>Logi {username}</b></font></td></tr>
	<tr><td class="spacer" colspan="2"></td></tr>

	<tr>
		<td class="header-small" colspan="2">
		<img src="tpl/stdstyle/images/blue/logs.png" width="32" height="32" align="middle" border="0" alt="Logs" title="Logs">&nbsp;
		Znalazłeś {founds} skrzynek.
		{events}
		<span style="font-weight: 400;">
		[<a href="search.php?showresult=1&amp;expert=0&amp;output=HTML&amp;sort=byname&amp;finderid={userid}&amp;searchbyfinder=&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0">Pokaż wszystkie</a>]</span>
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td colspan="2">
			{reports}<br><br>
			<b>Twoje najnowsze wpisy w LOGach:</b><br>
			<table>
			{lastlogs}
			</table>
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>



</table>