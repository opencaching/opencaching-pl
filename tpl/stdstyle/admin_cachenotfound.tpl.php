<?
global $usr;
?>
<!--<form name="optionsform" style="display:inline;">-->
<form name="optionsform" style="display:inline;" action='admin_cachenotfound.php' method="GET">
<table class="content" border="0" cellspacing="0px" cellpadding="0px">
<tr><td class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/cache.png" border="0" width="32" height="32" alt="" title="" align="middle"><font size="4">  <b>Skrzynki nieznalezione</b></font></td></tr>
<tr><td class="spacer"></td></tr>
<tr>
	<td>
		<input type="checkbox" name="show_reported" value="1" id="l_show_reported" class="checkbox" {show_reported} /> <label for="l_show_reported">Pokaż zgłoszone skrzynki</label><br>
		<input type="checkbox" name="show_duplicated" value="1" id="l_show_duplicated" class="checkbox" {show_duplicated} /> <label for="l_show_duplicated">Pokaż nieznalezienia z tą samą datą</label><br>
		<input type="submit" value="Filtruj" class="formbuttons" />
	</td>
</tr>
<tr>
	<td style="padding-left: 0px; padding-right: 0px;">
		<table border="0" cellspacing="0px" cellpadding="0px" class="null">
		<tr>
			<td width="18" height="13" bgcolor="#E6E6E6">#</td>
			<td width="200" height="13" bgcolor="#E6E6E6"><b>Nazwa</b></td>
			<td width="60" height="13" bgcolor="#E6E6E6"><b>Nieznalezienia</b></td>
			<td width="60" height="13" bgcolor="#E6E6E6"><b>Zgłoś problem</b></td>
		</tr>
		<!--a-->{results}<!--z-->
		</table>
	</td>
</tr>
</table>
</form>
