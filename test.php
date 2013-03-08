<?php 
require_once 'lib/db.php';

$a = new dataBase(true);

$cwpt = $a->paramQuery("SELECT `wp_oc` FROM `caches` WHERE `cache_id` = :cache_id", array('cache_id' => array ('value' => 4576, 'data_type' => 'integer')));
$cache_waypt = $cwpt['result']['wp_oc'];

print "one result: $cache_waypt <br>";
print 'uzyta pamiec: ' . memory_get_peak_usage () .'<br/><br/>';


$zmienna = $a->multiVariableQuery('SELECT * from `caches` where  `user_id` between :1 and :2 and type = :3', 1, 5000, 2);

print 'uzyta pamiec: ' . memory_get_peak_usage ();


?>