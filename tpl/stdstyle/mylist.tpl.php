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
<table class="content">
	<colgroup>
		<col width="100">
		<col>
	</colgroup>
	<tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/colected.png" class="icon32" alt="{title_text}" title="{title_text}" align="middle" /> <b>{title_text}</b></td></tr>
	<tr><td class="spacer" colspan="2"></td></tr>

	<tr>
		<td colspan="2">
<div class="searchdiv">
			<table class="null" border="0" cellspacing="0">
				<colgroup>
					<col>
					<col width="10">
					<col width="130">
					<col width="90">
				</colgroup>
				<tr>
					<td class="header-small">Nazwa skrzynki</td>
					<td class="header-small">&nbsp;</td>
					<td class="header-small" nowrap="nowrap">Ostatnio znalezione</td>
					<td class="header-small" nowrap="nowrap">&nbsp;</td>
				</tr>
				{list}
				{print_delete_list}
				{export_list}
			</table>
<div>
		</td>
	</tr>

</table>
