<?php
/***************************************************************************
		./tpl/stdstyle/usertops.tpl.php
		-------------------
		begin                : January 16 2007
		copyright            : (C) 2007 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

   Unicode Reminder メモ

	***************************************************************************/
?>
<table class="content">
	<colgroup>
		<col width="100">
		<col>
	</colgroup>
	<tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/recommendation.png" border="0" width="32" height="32" alt="Rekomendowane" title="Rekomendowane" align="middle"> <b>Rekomendowane przez <a href="viewprofile.php?userid={userid}">{username}</a></b></td></tr>
	<tr><td class="spacer" colspan="2"></td></tr>
	<tr>
		<td colspan="2">
			<table class="null" border="0" cellspacing="0">
				<tr>
					<td class="header-small">Nazwa</td>
					<td class="header-small" width="50px">&nbsp;</td>
					<td class="header-small" width="50px">&nbsp;</td>
				</tr>
				{top5}
			</table>
		</td>
	</tr>
</table>
