<?php

use src\Utils\Database\OcDb;

if (! isset($_REQUEST['u'])) {
    exit;
}

require __DIR__ . '/../vendor/autoload.php';

$db = OcDb::instance();
$q = 'SELECT SUM(`topratings`) AS s FROM `caches` WHERE `user_id` =:1';
$s = $db->multiVariableQuery($q, $_REQUEST['u']);
$r = $db->dbResultFetchOneRowOnly($s);
echo $r['s'];
