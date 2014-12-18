<?php

global $watchlistMailfrom;

$mailfrom = $watchlistMailfrom;
$debug = false;
$debug_mailto = 'rt@opencaching.pl';

$logwatch_text = $logowner_text = read_file(dirname(__FILE__) . '/item.email.html');
?>
