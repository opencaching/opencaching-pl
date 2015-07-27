		<input type="hidden" name="cacheid" value="{cacheid}"/>
		<font size="1">
		<table class="table">
			<colgroup>
				<col width="200">
				<col>
			</colgroup>
			<tr><td colspan="2"><img src="tpl/stdstyle/images/profile/22x22-email.png" border="0" width="22" height="22" align="middle"> <b>	Przeglądaj zgłoszenie</b></td></tr>
			<tr><td colspan="2">{confirm_resp_change}{confirm_status_change}</td></tr>
			<tr><td colspan="2" class="info"></tr>
			{email_sent}
			
			<table border='0' width='1000'>
			<tr><td colspan='10'>
			Przejdź do: [<a href='viewreports.php'>Listy bieżących zgłoszeń</a>] [<a href='viewreports.php?archiwum=1'>Archiwum zgłoszeń</a>]
			</td></tr>			
			<tr>
				<td bgcolor='#D5D9FF'>ID zgłoszenia</td>
				<td bgcolor='#D5D9FF'>Data zgłoszenia</td>
				<td bgcolor='#D5D9FF'>Cache</td>
				<td bgcolor='#D5D9FF'>Status skrzynki</td>
				<td bgcolor='#D5D9FF'>Rodzaj problemu</td>
				<td bgcolor='#D5D9FF'>Zgłaszający</td>
				<td bgcolor='#D5D9FF'>Prowadzący</td>
				<td bgcolor='#D5D9FF'>Status</td>
				<td bgcolor='#D5D9FF'>Zmieniony przez</td>
				<td bgcolor='#D5D9FF'>Ostatnio zmieniony</td>
			</tr>
			{content}
			<tr><td colspan='10' bgcolor='#D5D9FF'></td></tr>
			
			</table>
			<br>
			Podejmij działania:<br>
			<ul>
				{mail_actions}
			</ul>
			<ul>
				{actions}
			</ul>
			<br>
			Przejdź do: [<a href='viewreports.php'>Listy bieżących zgłoszeń</a>] [<a href='viewreports.php?archiwum=1'>Archiwum zgłoszeń</a>]
			<tr><td class="spacer" colspan="2"></td></tr>
		</table>
		</font>