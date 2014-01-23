<?php
session_start();
if(!isset($_SESSION['user_id'])){
    print 'no hacking please!';
    exit;
}
require_once __DIR__.'/../lib/db.php';
// require_once __DIR__.'/powerTrailController.php';
// $ptAPI = new powerTrailBase;

$waypoint = $_REQUEST['waypoint'];

// check if user is owner of selected power Trail
$query = 'SELECT  `name` , `cache_id` FROM  `caches` WHERE  `wp_oc` =  :1 LIMIT 1';
$db = new dataBase(false);
$db->multiVariableQuery($query, $waypoint);
$result = $db->dbResultFetch();
print $result['name'].'!1@$%3%7%4@#23557&^%%4#@2$LZA**&6545$###'.$result['cache_id'];

?>