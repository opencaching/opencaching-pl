<?php
/**
 * Purpose of this class: provide simple interface
 * for sending email messages from the code
 *
 * This class is interface to Email class.
 *
 */

namespace Utils\Email;

use lib\Objects\OcConfig\OcConfig;

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

        $email->setSubject('[Oc Admin Email] Error in domain: '.$spamDomain); //TODO
        $email->setBody($message);

        if( ! $email->send() ){
            trigger_error(__METHOD__.": Email sending failed!", E_USER_NOTICE);
        }
    }

    /**
     * @param $emailTemplateFile
     * @param $username
     * @param $country
     * @param $code
     * @param $newUserEmailAddress
     * @param $uuid
     */
    public static function sendActivationMessage($emailTemplateFile, $username, $country, $code, $newUserEmailAddress, $uuid) {
        $formattedMessage = new EmailFormatter($emailTemplateFile);
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
        $email->setSubject(tr('register_email_subject'));
        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }

    /**
     * @param $emailTemplateFile
     * @param $username
     * @param $userEmailAddress
     */
    public static function sendPostActivationMail($emailTemplateFile, $username, $userEmailAddress) {
        $formattedMessage = new EmailFormatter($emailTemplateFile);
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
        $email->setSubject(tr('post_activation_email_subject'));
        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }
}
