<?php

use Utils\Database\XDb;

$rootpath = '../';
header('Content-type: text/html; charset=utf-8');
require('../lib/common.inc.php');

echo XDb::xSimpleQueryValue('SELECT NOW()', 'DB_ERROR!');
