<?php
$rootpath = '';
require_once('./lib/common.inc.php');

if (isset($_SESSION['one_hundred_percent_unset_variable'])) { 
	print 'system zwraca true dla nieistniejącej zmiennej';
} else print 'wszytsko dziala w porzadku';

?>