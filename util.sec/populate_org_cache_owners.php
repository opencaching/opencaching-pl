<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder ăĄă˘
 ***************************************************************************/
    $rootpath = '../';
    require_once($rootpath.'lib/clicompatbase.inc.php');
    require_once($rootpath.'lib/db.php');
    require_once($rootpath.'lib/cache_owners.inc.php');

    $db = new dataBase();
    $db->beginTransaction();
    $pco = new OrgCacheOwners($db);
    $pco->populateAll();
    $db->commit();
?>
