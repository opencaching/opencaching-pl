<?php

//prepare the templates and include all neccessary
$rootpath = '../../';
require_once($rootpath . 'lib/clicompatbase.inc.php');
require_once($rootpath . 'lib/ftsearch.inc.php');

db_connect();

ftsearch_refresh();
?>
