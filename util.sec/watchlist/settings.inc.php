<?php
	// Unicode Reminder ăĄă˘

	$mailfrom = 'watch@opencaching.pl';
	$mailsubject = '[OC PL] Watchlist ' . date('d.m.Y');

	$debug = false;
	$debug_mailto = 'ocpl@opencaching.pl';
	
	$nologs = 'Brak wpisów do logu.';
	
	$logowner_text = '{date} {user} zrobił wpis ({logtype}) do logu skrzynki "{wp}: {cachename}" .' . "\n" . 'OC link: http://www.opencaching.pl/viewcache.php?wp={wp}'. "\n" . 'OC Mobile link: http://m.opencaching.pl/viewcache.php?wp={wp}'. "\n\n" . '{text}' . "\n\n\n\n";
	// $logowner_text = '{date} {user} '.tr('runwatch01').' ({logtype}) do logu skrzynki "{wp}: {cachename}" .' . "\n" . 'OC link: '.$absolute_server_URI.'{wp}'. "\n" . 'OC Mobile link: http://m.opencaching.pl/viewcache.php?wp={wp}'. "\n\n" . '{text}' . "\n\n\n\n";
	$logwatch_text = '{date} {user} zrobił wpis ({logtype}) do logu skrzynki "{wp}: {cachename}" .' . "\n" . 'OC link: http://www.opencaching.pl/viewcache.php?wp={wp}'. "\n" . 'OC Mobile link: http://m.opencaching.pl/viewcache.php?wp={wp}' . "\n\n" . '{text}' . "\n\n\n\n";
?>
