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
        $emailContent = read_file(__DIR__ . '/../../tpl/stdstyle/email/removed_log.email.html');
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

    /**
     * @param $username
     * @param $email
     * @param $country_name
     * @param $code
     * @param $uid
     */
    public static function sendActivationLink($username, $email, $country_name, $code, $uid)
    {
        $emailContent = read_file(__DIR__ . '/../../tpl/stdstyle/email/user_activation.email.html');
        $emailContent = mb_ereg_replace('{server}', \lib\Objects\ApplicationContainer::Instance()->getOcConfig()->getAbsolute_server_URI(), $emailContent);
        $emailContent = mb_ereg_replace('{registermail01}', tr('registermail01'), $emailContent);
        $emailContent = mb_ereg_replace('{registermail02}', tr('registermail02'), $emailContent);
        $emailContent = mb_ereg_replace('{useractivationmail1}', tr('useractivationmail1'), $emailContent);
        $emailContent = mb_ereg_replace('{useractivationmail2}', tr('useractivationmail2'), $emailContent);
        $emailContent = mb_ereg_replace('{useractivationmail3}', tr('useractivationmail3'), $emailContent);
        $emailContent = mb_ereg_replace('{useractivationmail4}', tr('useractivationmail4'), $emailContent);
        $emailContent = mb_ereg_replace('{useractivationmail5}', tr('useractivationmail5'), $emailContent);
        $emailContent = mb_ereg_replace('{useractivationmail6}', tr('useractivationmail6'), $emailContent);
        $emailContent = mb_ereg_replace('{user}', $username, $emailContent);
        $emailContent = mb_ereg_replace('{mailtitle}', tr('register_email_subject'), $emailContent);
        $emailContent = mb_ereg_replace('{useruid}', $uid, $emailContent);
        $emailContent = mb_ereg_replace('{email}', $email, $emailContent);
        $emailContent = mb_ereg_replace('{country}', $country_name, $emailContent);
        $emailContent = mb_ereg_replace('{code}', $code, $emailContent);
        $emailContent = mb_ereg_replace('{octeamEmailsSignature}', \lib\Objects\ApplicationContainer::Instance()->getOcConfig()->getOcteamEmailsSignature(), $emailContent);

        $emailAddr = \lib\Objects\ApplicationContainer::Instance()->getOcConfig()->getNoreplyEmailAddress();

        $emailHeaders = 'MIME-Version: 1.0' . "\r\n";
        $emailHeaders .= 'Content-Type: text/html; charset=utf-8' . "\r\n";
        $emailHeaders .= 'From: "' . \lib\Objects\ApplicationContainer::Instance()->getOcConfig()->getSiteName() . '" <' . $emailAddr . '>';

        $subject = tr('register_email_subject')." ".\lib\Objects\ApplicationContainer::Instance()->getOcConfig()->getSiteName();

        mb_send_mail($email, $subject, $emailContent, $emailHeaders);
    }

    /**
     * @param $username
     * @param $email
     */
    public static function sendPostActivationMail($username, $email)
    {
        $emailContent = read_file(__DIR__ . '/../../tpl/stdstyle/email/post_activation.email.html');
        $emailContent = mb_ereg_replace('{server}', \lib\Objects\ApplicationContainer::Instance()->getOcConfig()->getAbsolute_server_URI(), $emailContent);
        $emailContent = mb_ereg_replace('{registermail01}', tr('registermail01'), $emailContent);
        $emailContent = mb_ereg_replace('{registermail02}', tr('registermail02'), $emailContent);
        $emailContent = mb_ereg_replace('{postactivationmail01}', tr('postactivationmail01'), $emailContent);
        $emailContent = mb_ereg_replace('{postactivationmail02}', tr('postactivationmail02'), $emailContent);
        $emailContent = mb_ereg_replace('{postactivationmail03}', tr('postactivationmail03'), $emailContent);
        $emailContent = mb_ereg_replace('{postactivationmail04}', tr('postactivationmail04'), $emailContent);
        $emailContent = mb_ereg_replace('{postactivationmail05}', tr('postactivationmail05'), $emailContent);
        $emailContent = mb_ereg_replace('{user}', $username, $emailContent);
        $emailContent = mb_ereg_replace('{mailtitle}', tr('post_activation_email_subject'), $emailContent);
        $wikiLinks = \lib\Objects\ApplicationContainer::Instance()->getOcConfig()->getWikiLinks();
        $emailContent = mb_ereg_replace('{wikiaddress}', $wikiLinks['forBeginers'], $emailContent);
        $emailContent = mb_ereg_replace('{octeamEmailsSignature}', \lib\Objects\ApplicationContainer::Instance()->getOcConfig()->getOcteamEmailsSignature(), $emailContent);

        $emailAddr = \lib\Objects\ApplicationContainer::Instance()->getOcConfig()->getNoreplyEmailAddress();

        $emailHeaders = 'MIME-Version: 1.0' . "\r\n";
        $emailHeaders .= 'Content-Type: text/html; charset=utf-8' . "\r\n";
        $emailHeaders .= 'From: "' . \lib\Objects\ApplicationContainer::Instance()->getOcConfig()->getSiteName() . '" <' . $emailAddr . '>';

        $subject = tr('post_activation_email_subject')." ".\lib\Objects\ApplicationContainer::Instance()->getOcConfig()->getSiteName()."!";

        mb_send_mail($email, $subject, $emailContent, $emailHeaders);
    }

}
