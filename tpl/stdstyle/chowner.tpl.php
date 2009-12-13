		<input type="hidden" name="cacheid" value="{cacheid}"/>
		<font size="1">
		<table class="content">
			<colgroup>
				<col width="200">
				<col>
			</colgroup>
			{start_przejmij}
			<tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/email.png" class="icon32" align="middle" /> <b>	Przejmij skrzynkę</b></td></tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr>
				<td>
					<table border='0' width='800'>
					<tr>
						<td colspan="2">
							Poniżej znajduje się lista skrzynek, które oczekują na Twoją akceptację. Wybierz [akceptuj] albo [odrzuć], aby przejąć skrzynkę lub odrzucić zaproszenie.<br /><br />
						</td>
					</tr>
					<tr>
						<td bgcolor='#D5D9FF'>Nazwa skrzynki</td>
						<td bgcolor='#D5D9FF'>Data ukrycia</td>
					</tr>
					{acceptList}
					<tr><td colspan='2' height="30"></td></tr>
					</table>
				</td>
			</tr>
			{end_przejmij}
			<tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/email.png" border="0" align="middle" /> <b>	Przekaż skrzynkę</b></td></tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr>
				<td>
					<table border='0' width='800'>
					<tr>
						<td colspan="2">
							<font color="#ff0000">{error_msg}</font>
							<font color="green">{info_msg}</font>
							Za pośrednictwem tej strony możesz przekazać swoją skrzynkę innemu użytkownikowi. Wybierz skrzynkę z listy:<br /><br />
						</td>
					</tr>
					<tr>
						<td bgcolor='#D5D9FF'>Nazwa skrzynki</td>
						<td bgcolor='#D5D9FF'>Data ukrycia</td>
					</tr>
					{cacheList}
					<tr><td colspan='2' bgcolor='#D5D9FF'></td></tr>
					</table>
				</td>
			</tr>
			<br />
		</table>
		</font>
