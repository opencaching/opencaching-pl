<?php 
require_once 'lib/db.php';

$a = new dataBase(true);
$a->multiVariableQuery('SELECT * from `caches` where  `user_id` between :1 and :2 and type = :3', 1, 40, 2);

?>