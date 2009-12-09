<?php
	// Unicode Reminder ăĄă˘

	$mailfrom = 'watch@opencaching.pl';
	$mailsubject = '[OC PL] Watchlist ' . date('d.m.Y');

	$debug = false;
	$debug_mailto = 'ocpl@opencaching.pl';
	
	$nologs = 'Brak wpisów do logu.';
	
	$logowner_text = '{date} {user} zrobił wpis ({logtype}) do logu skrzynki "{cachename}" .' . "\n" . 'http://www.opencaching.pl/viewcache.php?cacheid={cacheid}' . "\n\n" . '{text}' . "\n\n\n\n";
	$logwatch_text = '{date} {user} zrobił wpis ({logtype}) do logu skrzynki "{cachename}" .' . "\n" . 'http://www.opencaching.pl/viewcache.php?cacheid={cacheid}' . "\n\n" . '{text}' . "\n\n\n\n";
?>
