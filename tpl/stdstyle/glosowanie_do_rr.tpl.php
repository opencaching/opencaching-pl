<table class="content">
<tr>
	<td class="header">
		<img src="tpl/stdstyle/images/cache/traditional.png" class="icon32" alt="" ><font size="4">  <b>Głosowanie do Rady Rejsu 2009</b></font>
	</td>
</tr>
<tr><td class="spacer" colspan="2"></td></tr>
<tr>
	<td>
		<form action="glosowanie_do_rr.php" method="POST" name="glosowanie" enctype="application/x-www-form-urlencoded" dir="ltr" style="display:inline;">
		<br/>
		<b>{vote_warning}Wybierz od 1 do 7 kandydatów z listy i zagłosuj.</b><br/>
		<br/>
		<table>
			<tr><td>#</td><td><b>Kandydat</b></td><td><b>Miejscowość</b></td><td><b>Profil</b></td></tr>
		{candidate_vote_list}
		</table>
		<input type='hidden' name='glosowanie' value='1' />
		<input type='submit' value='Oddaj głos' class="formbuttons" />
		<br/><br/>
		{vote_info}
		</form>
	</td>
</tr>
<tr><td class="spacer" colspan="2"></td></tr>
</table>
