<?php
/**
 * This is only a stub for util class used to log debug info...
 * TBD.
 */
namespace Utils\Log;

use Utils\Database\OcDb;
use lib\Objects\User\User;

class Log
{

    const EVENT_OWNERNOTIFY = 1;

    const EVENT_MAILWATCHLIST = 2;

    const EVENT_REMINDERMAIL = 3;

    const EVENT_DELETECACHE = 4;

    const EVENT_AUTOARCHIVE = 6;

    /**
     * Inserts log entry to email_user table - about mails send user 2 user
     *
     * @param User $from
     * @param User $to
     * @param string $subject
     * @param boolean $attachSenderAddress
     */
    public static function logEmail(User $from, User $to, $subject, $attachSenderAddress)
    {
        OcDb::instance()->multiVariableQuery("
            INSERT INTO `email_user`
            SET `ipaddress`= :1 , `date_generated`=NOW(), `from_user_id`= :2 ,
                `from_email`= :3, `to_user_id`= :4, `to_email`= :5,
                `mail_subject`= :6, `send_emailaddress`= :7",
            $_SERVER['REMOTE_ADDR'], $from->getUserId(), $from->getEmail(),
            $to->getUserId(), $to->getEmail(), $subject,
            ($attachSenderAddress) ? '1' : '0');
    }

    public static function logentry($eventid, $userid, $objectid1, $objectid2, $logtext, $details)
    {
        OcDb::instance()->multiVariableQuery("
            INSERT INTO `logentries`
                (`module`, `eventid`, `userid`, `objectid1`, `objectid2`, `logtext`, `details`, `logtime`)
            VALUES
                ( :1, :2, :3, :4, :5, :6, :7, NOW())",
            self::getEventModule($eventid), $eventid, $userid, $objectid1,
            $objectid2, $logtext, serialize($details));
    }

    // TODO: Remove this after drop logentries.module column.
    private static function getEventModule($eventId)
    {
        switch ($eventId) {
            case self::EVENT_OWNERNOTIFY:
            case self::EVENT_MAILWATCHLIST:
                return 'watchlist';
                break;
            case self::EVENT_REMINDERMAIL:
                return 'reminderemail';
                break;
            case self::EVENT_DELETECACHE:
                return 'approving';
                break;
            case self::EVENT_AUTOARCHIVE:
                return 'autoarchive';
                break;
            default:
                return '';
                break;
        }
    }
}