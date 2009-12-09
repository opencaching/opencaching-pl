<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/rproblems.png" border="0" width="32" height="32" align="middle"/>&nbsp;Zgłoszenia problemów</div>
	<div class="buffer"></div>
	<p>[Przejdź do <a href="viewreports.php?archiwum={archiwum}">{arch_curr}</a>]</p>
		<input type="hidden" name="cacheid" value="{cacheid}"/>
		<table border='1' class="table" width="90%">
			<tr>
				<th >ID</th>
				<th >Data zgłoszenia</th>
				<th >Cache</th>
				<th >Status skrzynki</th>
				<th >Rodzaj problemu</th>
				<th >Zgłaszający</th>
				<th >Prowadzący</th>
				<th >Status</th>
				<th >Zmieniony przez</th>
			</tr>
			{content}
		</table>
		
