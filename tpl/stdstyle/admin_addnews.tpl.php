<?php
 /***************************************************************************
											./tpl/stdstyle/newstopic.tpl.php
															-------------------
		begin                : Wed October 12 2005
		copyright            : (C) 2005 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

   Unicode Reminder メモ

	***************************************************************************/
?>
<form action="admin_addnews.php" method="post" enctype="application/x-www-form-urlencoded">
<input type="hidden" name="submit" value="1" />

<table class="content">
<tr>
	<td class="content2-pagetitle">
		<img src="tpl/stdstyle/images/blue/write.png" border="0" width="32" height="32" alt="" title="" align="middle"><font size="4">  <b>Dodaj newsa</b></font>
	</td>
</tr>
<tr><td class="spacer" colspan="2"></td></tr>
<tr>
	<td>
		Wyslany news bedzie na stronie glownej po zatwierdzeniu<br />
		Newsy moga byc tylko zwiazane z OpenCaching, Geocaching, inne nie beda publikowane.
	</td>
</tr>
<tr><td class="spacer" colspan="2"></td></tr>
<tr>
	<td>
		<img src="tpl/stdstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="Hinweis" title="Hinweis" align="middle">
		<span style="color:#666666; font-size:10px;">
		</span>
	</td>
</tr>
<tr><td class="spacer" colspan="2"></td></tr>
<tr>
	<td>
		Temat: 
		<select name="topic">
			{topics}
		</select>
	</td>
</tr>
<tr><td>Tresc newsa:</td></tr>
<tr>
	<td>
		<textarea name="newstext" cols="80" rows="10">{newstext}</textarea>
	</td>
</tr>
<tr><td><input type="checkbox" name="newshtml" id="newshtml" value="1" style="border:0;" {newshtml} /> <label for="newshtml">Tresc zawiera kod HTML</label></td></tr>
<tr>
	<td>
		<img src="tpl/stdstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="Hinweis" title="Hinweis" align="middle">
		<span style="color:#666666; font-size:10px;">
		</span>
	</td>
</tr>
<tr><td class="spacer" colspan="2"></td></tr>
<tr><td>Adres email wysylajacego: <input type="text" name="email" size="40" value="{email}" />{email_error}</td></tr>
<tr><td class="spacer" colspan="2"></td></tr>
<tr>
	<td>
		<input type="submit" value="OK" />
	</td>
</tr>
</table>

</form>
