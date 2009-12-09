		<font size="1">
		<table class="content">
			<colgroup>
				<col width="200">
				<col>
			</colgroup>
			<tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/email.png" border="0" width="32" height="32" align="middle"> <b>	Przekazujesz skrzynkę: <font color="#ff0000">{cachename}</font></b></td></tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr>
				<td>
				<form action="chowner.php" method="post">
					<table border='0' width='800'>
					<tr>
						<td colspan="2">
							Proszę wpisać nazwę użytkownika, który ma stać się nowym właścicielem Twojej skrzynki. Gdy tylko potwierdzi on chęć przejęcia skrzynki, procedura zostanie zakończona.<br>
							Aby unieważnić prośbę o zmianę właściciela, wróć do <a href='chowner.php'>listy skrzynek</a> i wybierz odnośnik "anuluj przekazanie".<br><br>
							<font color="#ff0000">UWAGA! Opcja ta będzie aktywna tylko dopóki nowy właściciel nie potwierdzi zmiany.</font>
						</td>
					</tr>
					<tr>
						<td height="20" colspan="2">
						</td>
					</tr>
					<tr>
						<td width="40%">
							<b>Podaj nazwę nowego właściciela:</b>
						</td>
						<td align="left" width="*">
							<input tabindex="1" type="text" size="30" name="username">
						</td>
					</tr>
					<tr>
						<td>
						</td>
						<td align="left">
							<input type="hidden" name ="cacheid" value="{cacheid}">
							<input type="submit" value="Zmień właściciela">
						</td>
					</tr>
					</table>
				</form>
				</td>
			</tr>
			<br>
		</table>
		</font>
