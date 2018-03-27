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

    public static function sendRemoveLogNotification($emailTemplateFile, GeoCacheLog $log, User $loggedUser)
    {
        $formattedMessage = new EmailFormatter($emailTemplateFile);
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

    public static function sendNotifyOfOcTeamCommentToCache(GeoCache $cache, User $admin, $message)
    {
        $emailTemplateFile = __DIR__ . '/../../tpl/stdstyle/email/octeam_comment.email.html';

        $formattedMessage = new EmailFormatter($emailTemplateFile);
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

    public static function sendAdoptionOffer($emailTemplateFile, $cacheName, $newOwnerUserName,
        $oldOwnerUserName, $userEmail) {
        $formattedMessage = new EmailFormatter($emailTemplateFile);
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

    public static function sendAdoptionSuccessMessage($emailTemplateFile, $cacheName, $newOwnerUserName,
        $oldOwnerUserName, $oldOwnerEmail) {
        $formattedMessage = new EmailFormatter($emailTemplateFile);
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

    public static function sendAdoptionRefusedMessage($emailTemplateFile, $cacheName, $newOwnerUserName,
        $oldOwnerUserName, $oldOwnerEmail) {
        $formattedMessage = new EmailFormatter($emailTemplateFile);
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

    public static function sendNotifyAboutNewCacheToOcTeam($emailTemplateFile, User $owner, $newCacheName, $newCacheId,
                                                           $region, $country) {
        $formattedMessage = new EmailFormatter($emailTemplateFile);
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

}