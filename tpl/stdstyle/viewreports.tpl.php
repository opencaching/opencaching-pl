		<input type="hidden" name="cacheid" value="{cacheid}"/>
		<font size="1">
		<table class="content">
			<colgroup>
				<col width="200">
				<col>
			</colgroup>
			<tr><td class="header" colspan="2"><img src="tpl/stdstyle/images/profile/22x22-email.png" border="0" width="22" height="22" align="middle"> <b>	Zgłoszenia problemów</b></td></tr>
			<tr>
				<td colspan="2">
					<br>[Przejdź do <a href="viewreports.php?archiwum={archiwum}">{arch_curr}</a>]
					<!--<br>[<a href="reportcache.php?archiwum={archiwum}">Wystaw zgłoszenie nie związane ze skrzynką</a>]-->
				</td>
			</tr>
		</table>
		<table border='0' width='1000'>
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
		</table>
		</font>
