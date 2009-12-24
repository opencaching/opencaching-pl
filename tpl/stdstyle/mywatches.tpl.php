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

   Unicode Reminder ăĄă˘

	 wachtes of this user

 ****************************************************************************/
?>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/search1.png" class="icon32" alt="{title_text}" title="{title_text}" align="middle" />&nbsp;{title_text}</div>
<table class="table" border="0" cellspacing="0">

	<tr>
		<td><p class="content-title-noshade">Geocache</p></td>
		<td>&nbsp;</td>
		<td nowrap="nowrap"><p class="content-title-noshade">{{last_found}}</p></td>
		<td nowrap="nowrap">&nbsp;</td>
	</tr>
	{watches}
	{print_delete_all_watches}
	{export_all_watches}
</table>
