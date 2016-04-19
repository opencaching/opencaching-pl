<?php

namespace lib\Controllers;

use lib\Objects\GeoCache\GeoCacheLog;

class EmailController
{

    /**
     * Sending email to log owner that log was deleted.
     *
     * @param GeoCacheLog $log
     * @param array $request
     */
    public static function sendRemoveLogNotification(GeoCacheLog $log, $request, $loggedUser)
    {
        $emailContent = file_get_contents(__DIR__ . '/../../tpl/stdstyle/email/removed_log.email');
        $message = isset($request['logowner_message']) ? $request['logowner_message'] : '';
        if ($message != '') { //message to logowner
            $message = tr('removed_message_title') . ":\n---" . "\n" . $message . "\n" . "---";
        }
        $emailContent = mb_ereg_replace('{log_owner}', $log->getUser()->getUserName(), $emailContent);
        $emailContent = mb_ereg_replace('{waypointId}', $log->getGeoCache()->getWaypointId(), $emailContent);
        $emailContent = mb_ereg_replace('{serviceUrl}', \lib\Objects\ApplicationContainer::Instance()->getOcConfig()->getSiteName(), $emailContent);
        $emailContent = mb_ereg_replace('{logRemover}', $loggedUser->getUserName(), $emailContent);
        $emailContent = mb_ereg_replace('{logRemoverId}', $loggedUser->getUserId(), $emailContent);
        $emailContent = mb_ereg_replace('{cache_name}', $log->getGeoCache()->getCacheName(), $emailContent);
        $emailContent = mb_ereg_replace('{log_entry}', $log->getText(), $emailContent);
        $emailContent = mb_ereg_replace('{comment}', $message, $emailContent);
        $emailContent = mb_ereg_replace('{removedLog_01}', tr('removedLog_01'), $emailContent);
        $emailContent = mb_ereg_replace('{removedLog_02}', tr('removedLog_02'), $emailContent);
        $emailContent = mb_ereg_replace('{removedLog_03}', tr('removedLog_03'), $emailContent);
        $emailContent = mb_ereg_replace('{octeamEmailsSignature}', \lib\Objects\ApplicationContainer::Instance()->getOcConfig()->getOcteamEmailsSignature(), $emailContent);
        $emailContent = mb_ereg_replace('{removedLog_04}', tr('removedLog_04'), $emailContent);
        $emailaddr = \lib\Objects\ApplicationContainer::Instance()->getOcConfig()->getNoreplyEmailAddress();

        $emailheaders = 'MIME-Version: 1.0' . "\r\n";
        $emailheaders .= 'Content-Type: text/html; charset=utf-8' . "\r\n";
        $emailheaders .= 'From: "' . \lib\Objects\ApplicationContainer::Instance()->getOcConfig()->getSiteName() . '" <' . $emailaddr . '>';

        mb_send_mail($log->getUser()->getEmail(), tr('removed_log_title'), $emailContent, $emailheaders);
    }



}
