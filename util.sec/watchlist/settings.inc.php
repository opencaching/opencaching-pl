<?php
	// Unicode Reminder ăĄă˘
 	global $watchlistMailfrom;
 
	$mailfrom = $watchlistMailfrom;

	$debug = false;
	$debug_mailto = 'rt@opencaching.pl';
	
	$nologs = 'Brak wpisów do logu.';
	
	$logwatch_text = $logowner_text = '
	  <tr>
	  	<td valign="top" style="border-right: 1px solid gray; padding-right:4px; padding-left:4px; font-size: 9px;" align="center">{date}</td>
	  	<td valign="top" style="border-right: 1px solid gray; padding-right:4px; padding-left:4px; font-size: 9px;"><b>{user}</b></td>
	  	<td valign="top" style="border-right: 1px solid gray; padding-right:4px; padding-left:4px; font-size: 9px;"><span style="color: {logtypeColor}"><b>{logtype}</b></span></td>
	  	<td valign="top" style="border-right: 1px solid gray; padding-right:4px; padding-left:4px; font-size: 9px;"><a href="{absolute_server_URI}{wp}">{wp}<br/> {cachename}</a><br/><!--(<a href="http://m.opencaching.pl/viewcache.php?wp={wp}">OC Mobile</a>)--> </td>
	  	<td valign="top" style="padding-right:4px; padding-left:4px;">
	  	    {text}
	  	</td>
	  </tr>
	  <tr>
	  	<td valign="top" style="border-right: 1px solid gray;"> </td>
	  	<td valign="top" style="border-right: 1px solid gray;"> </td>
	  	<td valign="top" style="border-right: 1px solid gray;"> </td>
	  	<td valign="top" style="border-right: 1px solid gray;"> </td>
	  	<td valign="top" >
	  </tr>
	';
	
	//$logowner_text = '{date} {user} zrobił wpis ({logtype}) do logu skrzynki "{wp}: {cachename}" .' . "\n" . 'OC link: http://www.opencaching.pl/viewcache.php?wp={wp}'. "\n" . 'OC Mobile link: http://m.opencaching.pl/viewcache.php?wp={wp}'. "\n\n" . '{text}' . "\n\n\n\n";
	//$logwatch_text = '{date} {user} zrobił wpis ({logtype}) do logu skrzynki "{wp}: {cachename}" .' . "\n" . 'OC link: http://www.opencaching.pl/viewcache.php?wp={wp}'. "\n" . 'OC Mobile link: http://m.opencaching.pl/viewcache.php?wp={wp}' . "\n\n" . '{text}' . "\n\n\n\n";
?>
