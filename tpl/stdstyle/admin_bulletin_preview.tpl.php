<table class="content">
<tr>
	<td class="content2-pagetitle">
		<img src="tpl/stdstyle/images/blue/write.png" border="0" width="32" height="32" alt="" title="" align="middle"><font size="4">  <b>Wyślij biuletyn</b></font>
	</td>
</tr>
<tr><td class="spacer" colspan="2"></td></tr>
<tr>
	<td>
		Podgląd biuletynu:<br>
		<form action='admin_bulletin.php' method='POST'>
			<input type="hidden" name="bulletin_final" value="1">
			{bulletin}
			<br>
			<input type='submit' value='Wyślij biuletyn'>
		</form>
	</td>
</tr>
</table>
