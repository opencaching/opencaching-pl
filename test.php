<?php
$q = "
SELECT wp_tc
FROM `caches`
WHERE wp_tc != ''
LIMIT 0 , 30";

require_once 'lib/db.php';
$db = new dataBase;

$db->simpleQuery($q);
$arr = $db->dbResultFetchAll();

echo 'wpisów: '. $db->rowCount();
print '<pre>';
print_r($arr);
