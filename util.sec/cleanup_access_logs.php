<?php

$rootpath = '../';
require_once($rootpath . 'lib/clicompatbase.inc.php');
require_once($rootpath . 'lib/db.php');
require_once __DIR__ . '/../lib/ClassPathDictionary.php';

class CleanupAccessLogs
{

    function run()
    {
        $db = new dataBase();
        $db->switchDebug(false);

        $sql = "delete from CACHE_ACCESS_LOGS where date_sub(now(), INTERVAL 5 DAY) > event_date";

        set_time_limit(360);
        $db->simpleQuery($sql);

        $total_deleted = $db->rowCount();

        set_time_limit(60);
        unset($db);
        echo "total_deleted=$total_deleted\n";
    }

}

$cal = new CleanupAccessLogs();
$cal->run();
?>
