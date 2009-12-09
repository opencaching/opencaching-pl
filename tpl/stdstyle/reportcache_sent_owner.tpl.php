<form action="reportcache.php" method="post">
		<input type="hidden" name="cacheid" value="{cacheid}"/>
		<table class="content">
			<colgroup>
				<col width="200">
				<col>
			</colgroup>
			<tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/rproblems.png" border="0" width="32" height="32" align="middle"> <b>	Zgłoszenie problemu dotyczącego skrzynki <a href="viewcache.php?cacheid={cacheid}">{cachename}</a></b></td></tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr><td colspan="2" class="info">
				Zgłoszenie zostało wysłane do właściciela skrzynki.
				<br>
				[<a href="index.php">Strona główna</a>]&nbsp;[<a href="viewcache.php?cacheid={cacheid}">Powrót do skrzynki</a>]
			</td></tr>
			
			<tr><td class="spacer" colspan="2"></td></tr>
		</table>
	</form>
