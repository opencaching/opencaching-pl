<?php
/**
 * Purpose of this class: provide simple interface
 * for sending email messages from the code
 *
 * This class is interface to Email class.
 *
 */

namespace Utils\Email;

use lib\Objects\ApplicationContainer;
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
        $email->setFromAddr( OcConfig::getSenderEmailAddr());

        $email->setSubject('[Oc Admin Email] Error in domain: '.$spamDomain); //TODO
        $email->setBody($message);

        if( ! $email->send() ){
            trigger_error(__METHOD__.": Email sending failed!", E_USER_NOTICE);
        }
    }

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
        $formattedMessage->setVariable("server", ApplicationContainer::Instance()->getOcConfig()->getAbsolute_server_URI());
        $formattedMessage->setVariable("sitename", ApplicationContainer::Instance()->getOcConfig()->getSiteName());

        $formattedMessage->addFooterAndHeader($username);

        $email = new Email();
        $email->addToAddr($newUserEmailAddress);
        $email->setReplyToAddr(ApplicationContainer::Instance()->getOcConfig()->getNoreplyEmailAddress());
        $email->setFromAddr(ApplicationContainer::Instance()->getOcConfig()->getNoreplyEmailAddress());
        $email->setSubject(tr('register_email_subject')." ".ApplicationContainer::Instance()->getOcConfig()->getSiteName());
        $email->setBody($formattedMessage->getEmailContent(), true);
        $email->send();
    }
}
