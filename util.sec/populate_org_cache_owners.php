<?php

$rootpath = '../';
require_once($rootpath . 'lib/clicompatbase.inc.php');
require_once __DIR__ . '/../lib/ClassPathDictionary.php';
require_once($rootpath . 'lib/cache_owners.inc.php');

$db = new dataBase();
$db->beginTransaction();
$pco = new OrgCacheOwners($db);
$pco->populateAll();
$db->commit();

