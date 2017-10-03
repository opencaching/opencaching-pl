<?php
/**
 * This is only a stub for util class used to log debug info...
 * TBD.
 */

namespace Utils\Log;

use Exception;
use Utils\Database\XDb;

$rootpath = __DIR__ . '/../../';
require_once($rootpath . 'lib/settingsGlue.inc.php');

class Log
{
    public static function logentry($module, $eventid, $userid, $objectid1, $objectid2, $logtext, $details)
    {
        self::cleanup('logentries');

        XDb::xSql(
            "INSERT INTO logentries (`module`, `eventid`, `userid`, `objectid1`, `objectid2`, `logtext`, `details`, `logtime`)
            VALUES ( ?, ?, ?, ?, ?, ?, ?, NOW())",
            $module, $eventid, $userid, $objectid1, $objectid2, $logtext, serialize($details));
    }

    public static function cleanup($table)
    {
        global $config;

        switch ($table) {
            case 'logentries':
                $datefield = 'logtime';
                break;
            case 'approval_status':
                $datefield = 'date_approval';
                break;
            case 'email_user':
                $datefield = 'date_generated';
                break;
            case 'CACHE_ACCESS_LOGS':
                $datefield = 'event_date';
                break;
            default:
                throw new Exception('unknown table: ' . $table);
        }
        if ($config['logging_cleanup'][$table] > 0) {
            XDb::xSql(
                "DELETE FROM ".$table."
                WHERE DATEDIFF(NOW(), ".$datefield.") > 30 * ?
                LIMIT 1000",
                $config['logging_cleanup'][$table]);
        }
    }
}
