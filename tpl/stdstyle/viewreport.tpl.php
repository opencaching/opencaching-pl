		<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/rproblems.png" class="icon32" align="middle"/>&nbsp;Przeglądaj zgłoszenie</div />
	<div class="buffer"></div>
	<p>{confirm_resp_change}{confirm_status_change}</p>
	{email_sent}
	<p>Przejdź do: [<a href='viewreports.php'>Listy bieżących zgłoszeń</a>] [<a href='viewreports.php?archiwum=1'>Archiwum zgłoszeń</a>]</p>
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
		<div class="buffer" style="height:50px;"></div>
		<div class="content2-container line-box">
			<p class="content-title-noshade-size1">{report_text_lbl}</p><br/>
			<p>{report_text}</p>
		</div>
		<div class="content2-container line-box">
			<p class="content-title-noshade-size1">{note_lbl}</p><br/>
			<p>{active_form}</p>
			<p>{note_area}</p>
		</div>			
		<div class="buffer"></div>
		<div class="content2-container line-box">
			<p class="content-title-noshade-size1">{perform_action_lbl}</p>
			<ul>
				{mail_actions}
			</ul>
			<ul>
				{actions}
			</ul>
			<br/>
		</div>
			<p>Przejdź do: [<a href='viewreports.php'>Listy bieżących zgłoszeń</a>] [<a href='viewreports.php?archiwum=1'>Archiwum zgłoszeń</a>]</p>
