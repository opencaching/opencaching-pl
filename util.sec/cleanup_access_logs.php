<?php

use Utils\Database\XDb;
$rootpath = '../';

require_once __DIR__ . '/../lib/ClassPathDictionary.php';

class CleanupAccessLogs
{

    function run()
    {

        $sql = "delete from CACHE_ACCESS_LOGS where date_sub(now(), INTERVAL 5 DAY) > event_date";

        set_time_limit(360);
        $s = XDb::xSql($sql);
        $total_deleted = XDb::xNumRows($s);

        set_time_limit(60);
        unset($db);
        echo "total_deleted=$total_deleted\n";
    }

}

$cal = new CleanupAccessLogs();
$cal->run();

