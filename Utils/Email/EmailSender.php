<?php
/**
 * Purpose of this class: provide simple interface
 * for sending email messages from the code
 *
 * This class is interface to Email class.
 *
 */

namespace Utils\Email;

use lib\Objects\GeoCache\GeoCache;
use lib\Objects\GeoCache\GeoCacheLog;
use lib\Objects\OcConfig\OcConfig;
use lib\Objects\User\User;
use lib\Objects\User\UserMessage;

class EmailSender
{
    /**
     * This method provide simple interface to send email messages
     * to OC Technical Admins (RT) if unexpected error occurs.
     *
     * @param string $message
     */
    public static function adminOnErrorMessage(
        $message,
        $spamDomain=OcSpamDomain::GENERIC_ERRORS)
    {

        //first check if sending email is allowed
        if(!OcSpamDomain::isEmailAllowed($spamDomain)){
            //skip email
            return;
        }

        //ok, mail allowed - build it
        $email = new Email();

        $email->addToAddr( OcConfig::getTechAdminsEmailAddr());
        $email->setReplyToAddr( OcConfig::getTechAdminsEmailAddr());
        $email->setFromAddr( OcConfig::getNoreplyEmailAddress());

        $email->addSubjectPrefix("OC Admin Email");
        $email->setSubject('Error in domain: '.$spamDomain); //TODO
        $email->setBody($message);

        if( ! $email->send() ){
            trigger_error(__METHOD__.": Email sending failed!", E_USER_NOTICE);
        }
    }

    /**
     * @param $username
     * @param $country
     * @param $code
     * @param $newUserEmailAddress
     * @param $uuid
     */
    public static function sendActivationMessage($username, $country, $code, $newUserEmailAddress, $uuid) {
        $formattedMessage = new EmailFormatter(__DIR__ . '/../../tpl/stdstyle/email/user_activation.email.html');
        $formattedMessage->setVariable("registermail01", tr("registermail01"));
        $formattedMessage->setVariable("useractivationmail1", tr("useractivationmail1"));
        $formattedMessage->setVariable("useractivationmail2", tr("useractivationmail2"));
        $formattedMessage->setVariable("useractivationmail3", tr("useractivationmail3"));
        $formattedMessage->setVariable("useractivationmail4", tr("useractivationmail4"));
        $formattedMessage->setVariable("useractivationmail5", tr("useractivationmail5"));
        $formattedMessage->setVariable("useractivationmail6", tr("useractivationmail6"));
        $formattedMessage->setVariable("user", $username);
        $formattedMessage->setVariable("code", $code);
        $formattedMessage->setVariable("country", $country);
        $formattedMessage->setVariable("email", $newUserEmailAddress);
        $formattedMessage->setVariable("useruid", $uuid);
        $formattedMessage->setVariable("server", OcConfig::getAbsolute_server_URI());
        $formattedMessage->setVariable("sitename", OcConfig::getSiteName());

        $formattedMessage->addFooterAndHeader($username);

        $email = new Email();
        $email->addToAddr($newUserEmailAddress);
        $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
        $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
        $email->setSubject(tr('register_email_subject'));
        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }

    /**
     * @param $username
     * @param $userEmailAddress
     */
    public static function sendPostActivationMail($username, $userEmailAddress) {
        $formattedMessage = new EmailFormatter(__DIR__ . '/../../tpl/stdstyle/email/post_activation.email.html');
        $formattedMessage->setVariable("server", OcConfig::getAbsolute_server_URI());
        $formattedMessage->setVariable("registermail01", tr("registermail01"));
        $formattedMessage->setVariable("postactivationmail01", tr("postactivationmail01"));
        $formattedMessage->setVariable("postactivationmail02", tr("postactivationmail02"));
        $formattedMessage->setVariable("postactivationmail03", tr("postactivationmail03"));
        $formattedMessage->setVariable("user", $username);
        $wikiLinks = OcConfig::getWikiLinks();
        $formattedMessage->setVariable("wikiaddress", $wikiLinks['forBeginers']);
        $formattedMessage->setVariable("sitename", OcConfig::getSiteName());

        $needAproveLimit = OcConfig::getNeedAproveLimit();
        $needFindLimit = OcConfig::getNeedFindLimit();

        if ($needAproveLimit > 0) {
            $formattedMessage->setVariable("postactivationmail05", tr('postactivationmail05'));
            $formattedMessage->setVariable("NEED_APPROVE_LIMIT", $needAproveLimit);
        } else {
            $formattedMessage->setVariable("postactivationmail05", "");
        }

        if ($needFindLimit > 0) {
            $formattedMessage->setVariable("postactivationmail04", tr('postactivationmail04'));
            $formattedMessage->setVariable("NEED_FIND_LIMIT", $needFindLimit);
        } else {
            $formattedMessage->setVariable("postactivationmail04", "");
        }

        $formattedMessage->addFooterAndHeader($username);

        $email = new Email();
        $email->addToAddr($userEmailAddress);
        $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
        $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
        $email->setSubject(tr('post_activation_email_subject'));
        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }

    /**
     * @param GeoCacheLog $log
     * @param User $loggedUser
     */
    public static function sendRemoveLogNotification(GeoCacheLog $log, User $loggedUser)
    {
        $formattedMessage = new EmailFormatter(__DIR__ . '/../../tpl/stdstyle/email/removed_log.email.html');
        $formattedMessage->setVariable("log_owner", $log->getUser()->getUserName());
        $formattedMessage->setVariable("waypointId", $log->getGeoCache()->getWaypointId());
        $formattedMessage->setVariable("serviceUrl", OcConfig::getAbsolute_server_URI());
        $formattedMessage->setVariable("logRemover", $loggedUser->getUserName());
        $formattedMessage->setVariable("logRemoverId", $loggedUser->getUserId());
        $formattedMessage->setVariable("cache_name", $log->getGeoCache()->getCacheName());
        $formattedMessage->setVariable("log_entry", $log->getText());
        $formattedMessage->setVariable("removedLog_01", tr('removedLog_01'));
        $formattedMessage->setVariable("removedLog_02", tr('removedLog_02'));

        $formattedMessage->addFooterAndHeader($log->getUser()->getUserName());

        $email = new Email();
        $email->addToAddr($log->getUser()->getEmail());
        $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
        $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
        $email->setSubject(tr('removed_log_title'));
        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }

    /**
     * @param GeoCache $cache
     * @param User $admin
     * @param $message
     */
    public static function sendNotifyOfOcTeamCommentToCache(GeoCache $cache, User $admin, $message)
    {
        $formattedMessage = new EmailFormatter(__DIR__ . '/../../tpl/stdstyle/email/octeam_comment.email.html');
        $formattedMessage->setVariable("ocTeamComment_01", tr("ocTeamComment_01"));
        $formattedMessage->setVariable("ocTeamComment_02", tr("ocTeamComment_02"));
        $formattedMessage->setVariable("ocTeamComment_03", tr("ocTeamComment_03"));
        $formattedMessage->setVariable("ocTeamComment_04", tr("ocTeamComment_04"));
        $formattedMessage->setVariable("ocTeamComment_05", tr("ocTeamComment_05"));
        $formattedMessage->setVariable("waypointId", $cache->getWaypointId());
        $formattedMessage->setVariable("cachename", $cache->getCacheName());
        $formattedMessage->setVariable("octeam_comment", $message);
        $formattedMessage->setVariable("adminName", $admin->getUserName());
        $formattedMessage->setVariable("adminId", $admin->getUserId());
        $formattedMessage->setVariable("server", OcConfig::getAbsolute_server_URI());

        $formattedMessage->addFooterAndHeader($cache->getOwner()->getUserName(), false);

        $email = new Email();
        $email->addToAddr($cache->getOwner()->getEmail());
        $email->setReplyToAddr(OcConfig::getCogEmailAddress());
        $email->setFromAddr(OcConfig::getCogEmailAddress());
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
        $email->setSubject(tr('octeam_comment_subject'));
        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();

        //Send copy to COG
        $emailCOG = new Email();
        $emailCOG->addToAddr(OcConfig::getCogEmailAddress());
        $emailCOG->setReplyToAddr(OcConfig::getCogEmailAddress());
        $emailCOG->setFromAddr(OcConfig::getCogEmailAddress());
        $emailCOG->addSubjectPrefix(OcConfig::getMailSubjectPrefixForReviewers());
        $emailCOG->setSubject(tr('octeam_comment_subject_copy').' '.$admin->getUserName());
        $emailCOG->setBody($formattedMessage->getEmailContent(), true);
        $emailCOG->send();
    }

    /**
     * @param $cacheName
     * @param $newOwnerUserName
     * @param $oldOwnerUserName
     * @param $userEmail
     */
    public static function sendAdoptionOffer($cacheName, $newOwnerUserName, $oldOwnerUserName, $userEmail) {
        $formattedMessage = new EmailFormatter(__DIR__ . '/../../tpl/stdstyle/email/adoption.email.html');
        $formattedMessage->setVariable("adopt01", tr("adopt_26"));
        $formattedMessage->setVariable("userName", '<b>'.$oldOwnerUserName.'</b>');
        $formattedMessage->setVariable("cacheName", '<b>'.$cacheName.'</b>');

        $formattedMessage->addFooterAndHeader($newOwnerUserName);

        $email = new Email();
        $email->addToAddr($userEmail);
        $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
        $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
        $email->setSubject(tr('adopt_25'));
        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }

    /**
     * @param $cacheName
     * @param $newOwnerUserName
     * @param $oldOwnerUserName
     * @param $oldOwnerEmail
     */
    public static function sendAdoptionSuccessMessage($cacheName, $newOwnerUserName, $oldOwnerUserName,
        $oldOwnerEmail) {
        $formattedMessage = new EmailFormatter(__DIR__ . '/../../tpl/stdstyle/email/adoption.email.html');
        $formattedMessage->setVariable("adopt01", tr("adopt_31"));
        $formattedMessage->setVariable("userName", '<b>'.$newOwnerUserName.'</b>');
        $formattedMessage->setVariable("cacheName", '<b>'.$cacheName.'</b>');

        $formattedMessage->addFooterAndHeader($oldOwnerUserName);

        $email = new Email();
        $email->addToAddr($oldOwnerEmail);
        $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
        $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
        $email->setSubject(tr('adopt_18'));
        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }

    /**
     * @param $cacheName
     * @param $newOwnerUserName
     * @param $oldOwnerUserName
     * @param $oldOwnerEmail
     */
    public static function sendAdoptionRefusedMessage($cacheName, $newOwnerUserName, $oldOwnerUserName,
        $oldOwnerEmail) {
        $formattedMessage = new EmailFormatter(__DIR__ . '/../../tpl/stdstyle/email/adoption.email.html');
        $formattedMessage->setVariable("adopt01", tr("adopt_29"));
        $formattedMessage->setVariable("userName", '<b>'.$newOwnerUserName.'</b>');
        $formattedMessage->setVariable("cacheName", '<b>'.$cacheName.'</b>');

        $formattedMessage->addFooterAndHeader($oldOwnerUserName);

        $email = new Email();
        $email->addToAddr($oldOwnerEmail);
        $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
        $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
        $email->setSubject(tr('adopt_28'));
        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }

    /**
     * Sends user2user message (used by mailTo)
     * Returns true on success;
     *
     * @param User $from
     * @param User $to
     * @param $subject
     * @param $text
     * @param $attachSenderAddress
     * @return bool
     * @internal param UserMessage $msg
     */
    public static function sendUser2UserMessage(User $from, User $to, $subject, $text, $attachSenderAddress)
    {
        // add additional prefix to subject
        $subject = tr('mailto_emailFrom').' '.$from->getUserName().': '.$subject;

        // prepare message text
        if($attachSenderAddress){
            $userMessage = new EmailFormatter(__DIR__ . '/../../tpl/stdstyle/email/user2user/messageWithSenderEmail.email.html', true);
        } else {
            $userMessage = new EmailFormatter(__DIR__ . '/../../tpl/stdstyle/email/user2user/messageWithoutSenderEmail.email.html', true);
        }

        $userMessage->setVariable('toUsername', $to->getUserName());
        $userMessage->setVariable('fromUsername', $from->getUserName());
        $userMessage->setVariable('fromEmail', $from->getEmail());
        $userMessage->setVariable('absoluteServerURI', OcConfig::getAbsolute_server_URI());
        $userMessage->setVariable('fromUserid', $from->getUserId());
        $userMessage->setVariable('text', nl2br($text));
        $userMessage->addFooterAndHeader($to->getUserName(), !$attachSenderAddress);

        // prepare copy for sender
        $senderCopy = new EmailFormatter(__DIR__ . '/../../tpl/stdstyle/email/user2user/messageCopyForSender.email.html', true);

        if(preg_match('/(?:<body[^>]*>)(.*)<\/body>/isU', $userMessage->getEmailContent(), $matches)) {
            $bodyOfMessage = $matches[1];
        }else{
            $bodyOfMessage = '';
        }

        $senderCopy->setVariable('text', $bodyOfMessage);
        $senderCopy->setVariable('toUsername', $to->getUserName());
        $senderCopy->addFooterAndHeader($from->getUserName());

        $noReplyAddress = OcConfig::getNoreplyEmailAddress();

        //send email to Recipient
        $email = new Email();
        $email->addToAddr($to->getEmail());
        if($attachSenderAddress){
            $email->setReplyToAddr($from->getEmail());
            $email->setFromAddr($from->getEmail());
        }else{
            $email->setReplyToAddr($noReplyAddress);
            $email->setFromAddr($noReplyAddress);
        }

        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
        $email->setSubject($subject);
        $email->setBody($userMessage->getEmailContent(), true);
        $result = $email->send();

        if(!$result) {
            error_log(__METHOD__.': Mail sending failure to: '.$to->getEmail());
            return $result;
        } else {
            //send copy of email to sender
            $email = new Email();
            $email->addToAddr($from->getEmail());
            $email->setReplyToAddr($noReplyAddress);
            $email->setFromAddr($noReplyAddress);
            $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
            $email->setSubject($subject);
            $email->setBody($senderCopy->getEmailContent(), true);

            if(!$email->send()){
                error_log(__METHOD__.': Sender copy sending failure to: '.$from->getEmail());
            }
        }

        return $result;
    }

    /**
     * @param User $owner
     * @param $newCacheName
     * @param $newCacheId
     * @param $region
     * @param $country
     */
    public static function sendNotifyAboutNewCacheToOcTeam(User $owner, $newCacheName, $newCacheId, $region, $country) {
        $formattedMessage = new EmailFormatter(__DIR__ . '/../../tpl/stdstyle/email/oc_team_notify_new_cache.email.html');
        $formattedMessage->setVariable("ocTeamNewCache_01", tr("ocTeamNewCache_01"));
        $formattedMessage->setVariable("ocTeamNewCache_02", tr("ocTeamNewCache_02"));
        $formattedMessage->setVariable("ocTeamNewCache_03", tr("ocTeamNewCache_03"));
        $formattedMessage->setVariable("ocTeamNewCache_04", tr("ocTeamNewCache_04"));
        $formattedMessage->setVariable("ocTeamNewCache_05", tr("ocTeamNewCache_05"));
        $formattedMessage->setVariable("ocTeamNewCache_06", tr("ocTeamNewCache_06"));
        $formattedMessage->setVariable("server", OcConfig::getAbsolute_server_URI());
        $formattedMessage->setVariable("userid", $owner->getUserId());
        $formattedMessage->setVariable("username", $owner->getUserName());
        $formattedMessage->setVariable("cacheid", $newCacheId);
        $formattedMessage->setVariable("cachename", $newCacheName);

        $formattedMessage->addFooterAndHeader(OcConfig::getMailSubjectPrefixForReviewers());

        $email = new Email();
        $email->addToAddr(OcConfig::getCogEmailAddress());
        $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
        $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForReviewers());

        if (isset($region) && isset($country)) {
            $email->setSubject(tr('ocTeamNewCache_sub').": ".$country." -> ".$region);
        } else {
            $email->setSubject(tr('ocTeamNewCache_sub').": ".tr('dummy_outside'));
        }

        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }

    /**
     * @param $timestamp
     * @param User $submitter
     * @param GeoCache $reportedCache
     * @param $reason
     * @param $text
     */
    public static function sendNotifyAboutNewReportToOcTeam($timestamp, User $submitter, GeoCache $reportedCache,
        $reason, $text) {
        $formattedMessage = new EmailFormatter(__DIR__ . '/../../tpl/stdstyle/email/oc_team_notify_new_report.email.html');
        $formattedMessage->setVariable("reportcache10", tr("reportcache10"));
        $formattedMessage->setVariable("reportcache11", tr("reportcache11"));
        $formattedMessage->setVariable("reportcache12", tr("reportcache12"));
        $formattedMessage->setVariable("reportcache13", tr("reportcache13"));
        $formattedMessage->setVariable("reportcache14", tr("reportcache14"));
        $formattedMessage->setVariable("reportcache15", tr("reportcache15"));
        $formattedMessage->setVariable("reportcache16", tr("reportcache16"));
        $formattedMessage->setVariable("reportcache17", tr("reportcache17"));
        $formattedMessage->setVariable("reportcache18", tr("reportcache18"));
        $formattedMessage->setVariable("reportcache19", tr("reportcache19"));
        $formattedMessage->setVariable("here", tr("here"));

        $formattedMessage->setVariable("date", $timestamp);
        $formattedMessage->setVariable("server", OcConfig::getAbsolute_server_URI());
        $formattedMessage->setVariable("reason", $reason);
        $formattedMessage->setVariable("text", $text);

        $formattedMessage->setVariable("submitterid", $submitter->getUserId());
        $formattedMessage->setVariable("submitter", $submitter->getUserName());
        $formattedMessage->setVariable("cacheid", $reportedCache->getCacheId());
        $formattedMessage->setVariable("cache_wp", $reportedCache->getWaypointId());
        $formattedMessage->setVariable("cachename", $reportedCache->getCacheName());

        $formattedMessage->addFooterAndHeader(OcConfig::getMailSubjectPrefixForReviewers());

        $email = new Email();
        $email->addToAddr(OcConfig::getCogEmailAddress());
        $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
        $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForReviewers());

        $location = $reportedCache->getCacheLocationObj()->getLocationDesc(" -> ");

        if (empty($location)) {
            $email->setSubject(tr('reportcache07').": ".tr('dummy_outside'));
        } else {
            $email->setSubject(tr('reportcache07').": ".$location);
        }

        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }
}
