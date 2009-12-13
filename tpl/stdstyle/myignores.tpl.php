<?php
/***************************************************************************
											./tpl/stdstyle/mywatches.tpl.php
															-------------------
		begin                : July 17 2004
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

   Unicode Reminder メモ

	 wachtes of this user

 ****************************************************************************/
?>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/colected.png" class="icon32" alt="{title_text}" title="{title_text}" align="middle">&nbsp;{title_text}</div>
{no_ignores}
<table class="table">
	<colgroup>
		<col width="500px"/>
		<col width="140px"/>
	</colgroup>
	<tr>
			<td class="content-title-noshade">{title_text_tab}</td>
			<td align="right" class="content-title-noshade">&nbsp;</td>
	</tr>
	{ignores}
</table>
