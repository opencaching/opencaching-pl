<form action="reportcache.php" method="post">
		<input type="hidden" name="cacheid" value="{cacheid}"/>
		<table class="content">
			<colgroup>
				<col width="200" />
				<col/>
			</colgroup>
			<tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/rproblems.png" border="0" width="32" height="32" align="middle" alt="" /> <b>	Zgłoszenie problemu dotyczącego skrzynki <a href="viewcache.php?cacheid={cacheid}">{cachename}</a></b></td></tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr><td colspan="2" class="info">				
			</td></tr>
			<tr><td colspan="2">
				<b>Wyślij zgłoszenie problemu do:</b><br />
				<input type="radio" name="adresat" id="adresat1" value="owner" checked="checked" /><label for="adresat1">Właściciela skrzynki</label><br />
				<input type="radio" name="adresat" id="adresat2" value="rr" /><label for="adresat2">Rady Opencaching.pl (właściciel skrzynki otrzyma kopię zgłoszenia)</label>
				<br />
				<font color="#ff0000"><b>UWAGA!</b> Zanim zdecydujesz się wysłać zgłoszenie do Rady Opencaching.pl, skontaktuj się z właścicielem skrzynki w celu podjęcia próby rozwiązania problemu.
				<br /><br />
				</font>
				
			</td></tr>
			<tr>
				<td colspan="2">Rodzaj problemu: 
				<select name="reason">
					<option value="0" selected="selected">=== Proszę wybrać ===</option>
					<option value="1" >Uwaga co do lokalizacji skrzynki</option>
					<option value="2" >Nieodpowiedni wpis w logu</option>
					<option value="3" >Nieodpowiednia zawartość skrzynki</option>
					<option value="4" >Inny</option>
				</select>{noreason_error}
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>
			<tr>
				<td colspan="2">Szczegóły zgłoszenia</td>
			</tr>
			<tr>
				<td colspan="2">
					<textarea class="logs" name="text" cols="68" rows="15"></textarea>
				</td>
			</tr>

			<tr><td class="spacer" colspan="2"></td></tr>
			
			<tr><td class="spacer" colspan="2"></td></tr>

			
			<tr>
				<td class="header-small" colspan="2">
					<input type="reset" name="cancel" value="Wyczyść" class="formbuttons"/>&nbsp;&nbsp;
					<input type="submit" name="ok" value="Wyślij zgłoszenie" class="formbuttons"/>
				</td>
			</tr>

			<tr><td class="spacer" colspan="2"></td></tr>
		</table>
	</form>
