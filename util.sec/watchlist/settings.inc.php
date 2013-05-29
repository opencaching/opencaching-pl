<?php
	// Unicode Reminder ăĄă˘
 	global $watchlistMailfrom;
 
	$mailfrom = $watchlistMailfrom;

	$debug = false;
	$debug_mailto = 'rt@opencaching.pl';
	
	$logwatch_text = $logowner_text = '
	  <tr>
	  	<td valign="top" style="border-right: 1px solid gray; padding-right:4px; padding-left:4px; font-size: 10px; font-family: Verdana; width: 8%;" align="center">{date}</td>
	  	<td valign="top" style="border-right: 1px solid gray; padding-right:4px; padding-left:4px; font-size: 10px; font-family: Verdana; " align="center"><b>{user}</b></td>
	  	<td valign="top" style="border-right: 1px solid gray; padding-right:4px; padding-left:4px; font-size: 10px; font-family: Verdana; " align="center"><span style="color: {logtypeColor}"><b>{logtype}</b></span></td>
	  	<td valign="top" style="border-right: 1px solid gray; padding-right:4px; padding-left:4px; font-size: 10px; font-family: Verdana; width: 20%;" align="center"><a href="{absolute_server_URI}{wp}">{wp}<br/> {cachename}</a><br/></td>
	  	<td valign="top" style="padding-right:4px; padding-left:4px; font-size: 10px; font-family: Verdana;">
	  	    {text}
	  	</td>
	  </tr>
	  <tr>
	  	<td colspan="5" valign="top" style="border-top: 1px solid gray; height: 3px"></td>
	  </tr>
	';
	
	//$logowner_text = '{date} {user} zrobił wpis ({logtype}) do logu skrzynki "{wp}: {cachename}" .' . "\n" . 'OC link: http://www.opencaching.pl/viewcache.php?wp={wp}'. "\n" . 'OC Mobile link: http://m.opencaching.pl/viewcache.php?wp={wp}'. "\n\n" . '{text}' . "\n\n\n\n";
	//$logwatch_text = '{date} {user} zrobił wpis ({logtype}) do logu skrzynki "{wp}: {cachename}" .' . "\n" . 'OC link: http://www.opencaching.pl/viewcache.php?wp={wp}'. "\n" . 'OC Mobile link: http://m.opencaching.pl/viewcache.php?wp={wp}' . "\n\n" . '{text}' . "\n\n\n\n";
?>
