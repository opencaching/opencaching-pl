<?php
/**
 * This is only a stub for util class used to log debug info...
 * TBD.
 */

namespace Utils\Log;

use Utils\Database\XDb;

class Log
{
    public static function logentry( $module, $eventid, $userid, $objectid1, $objectid2, $logtext, $details ){

        XDb::xSql(
            "INSERT INTO logentries (`module`, `eventid`, `userid`, `objectid1`, `objectid2`, `logtext`, `details`, `logtime`)
        VALUES ( ?, ?, ?, ?, ?, ?, ?, NOW())",
            $module, $eventid, $userid, $objectid1, $objectid2, $logtext, serialize($details));
    }

}