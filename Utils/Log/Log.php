<?php
/**
 * This is only a stub for util class used to log debug info...
 * TBD.
 */

namespace Utils\Log;

use Utils\Database\OcDb;

class Log
{
    /**
     * SQL query used for inserting a single tuple to the logentries table
     */
    private static $logentrySql = "INSERT INTO logentries
        (`module`, `eventid`, `userid`, `objectid1`, `objectid2`, `logtext`, `details`, `logtime`)
        VALUES ( ?, ?, ?, ?, ?, ?, ?, NOW())";

    /**
     * SQL query used for inserting a buch of tuples to the logentries table - starting part
     */
    private static $logentriesSql = "INSERT IGNORE INTO logentries
        (`module`, `eventid`, `userid`, `objectid1`, `objectid2`, `logtext`, `details`, `logtime`)
        VALUES";

    /**
     * Inserts one tuple to the logentries table
     */
    public static function logentry( $module, $eventid, $userid, $objectid1, $objectid2, $logtext, $details )
    {
        $db = OcDb::instance();
        $stmt = $db->prepare(self::$logentrySql);
        $stmt->execute(array($module, $eventid, $userid, $objectid1, $objectid2, $logtext, serialize($details)));
    }

    /**
     * Inserts multiple tuples to the logentries table
     *
     * @param array $entries the LogEntry instances to store
     */
    public static function logentries( array $entries )
    {
        $db = OcDb::instance();
        $dataString = "";
        foreach ($entries as $entry) {
            if ($entry instanceof LogEntry) {
                $entrySql = "("
                    .$db->quote($entry->module)
                    .",".intval($entry->eventid).",".intval($entry->userid).",".intval($entry->objectid1).",".intval($entry->objectid2)
                    .",".$db->quote($entry->logtext).",".$db->quote(serialize($entry->details)).",".$db->quote($entry->logtime)
                    .")";
                if (mb_strlen($dataString) > 0) {
                    $dataString .= ",";
                }
                $dataString .= $entrySql;
            }
        }
        if (mb_strlen($dataString) > 0) {
            $db->exec(self::$logentriesSql.$dataString);
        }
    }
}
