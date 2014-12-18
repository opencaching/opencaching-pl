<?php

$rootpath = '../';
require('../lib/common.inc.php');

$rs = mysql_query('SELECT NOW()', $dblink);
$r = mysql_fetch_array($rs);

echo $r[0];
?>
