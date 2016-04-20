<?php
use Utils\Database\OcDb;

session_start();
if(!isset($_SESSION['user_id'])){
    print 'no hacking please!';
    exit;
}
require_once __DIR__.'/../lib/ClassPathDictionary.php';
$waypoint = $_REQUEST['waypoint'];

// check if user is owner of selected power Trail
$query = 'SELECT  `name` , `cache_id` FROM  `caches` WHERE  `wp_oc` =  :1 LIMIT 1';
$db = OcDb::instance();
$s = $db->multiVariableQuery($query, $waypoint);
$result = $db->dbResultFetchOneRowOnly($s);

print $result['name'].'!1@$%3%7%4@#23557&^%%4#@2$LZA**&6545$###'.$result['cache_id'];

