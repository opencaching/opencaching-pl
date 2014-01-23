<?php
if (!isset($_REQUEST['u'])) exit;
require_once __DIR__.'/powerTrailBase.php';
$db = new dataBase;
$q = 'SELECT SUM(`topratings`) AS s FROM `caches` WHERE `user_id` =:1';
$db->multiVariableQuery($q, $_REQUEST['u']);
$r = $db->dbResultFetch();
echo $r['s'];
?>