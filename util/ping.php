<?php

use src\Utils\Database\XDb;

header('Content-type: text/html; charset=utf-8');
require(__DIR__.'/../lib/common.inc.php');

echo XDb::xSimpleQueryValue('SELECT NOW()', 'DB_ERROR!');
