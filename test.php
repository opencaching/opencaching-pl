<?php 
require_once 'lib/db.php';

$a = new dataBase(true);
$a->multiVariableQuery('SELECT * from `caches` where  `user_id` in (:1, :2) and type = :3', 1, 13, 2);

?>