<?php
namespace lib\Objects\User;

use Utils\Email\Email;
use Utils\Email\EmailFormatter;
use Utils\Uri\SimpleRouter;
use lib\Objects\OcConfig\OcConfig;

class U2UEmailSender
{

    const TEMPLATE_PATH = __DIR__ . '/../../../tpl/stdstyle/email/user2user/';

    /**
     * Sends user2user message (used by mailTo)
     * Returns true on success;
     *
     * @param User $from
     * @param User $to
     * @param string $subject
     * @param string $text
     * @param boolean $attachSenderAddress
     * @return boolean
     */
    public static function sendU2UMessage(User $from, User $to, $subject, $text, $attachSenderAddress)
    {
        // add additional prefix to subject
        $subject = tr('mailto_emailFrom') . ' ' . $from->getUserName() . ': ' . $subject;
        // prepare message text
        $userMessage = new EmailFormatter(self::TEMPLATE_PATH . 'messageU2U.email.html', true);
        $userMessage->setVariable('fromUserMailUrl', SimpleRouter::getLink('UserProfile', 'mailTo', $from->getUserId()));
        $userMessage->setVariable('fromUserProfileUrl', $from->getProfileUrl());
        $userMessage->setVariable('fromUsername', $from->getUserName());
        $userMessage->setVariable('absoluteServerURI', rtrim(OcConfig::getAbsolute_server_URI(), '/'));
        $userMessage->setVariable('serverName', OcConfig::getSiteName());
        $userMessage->setVariable('text', nl2br($text));
        if ($attachSenderAddress) {
            $userMessage->setVariable('mailReply', tr('mailto_respByEmail'));
        } else {
            $userMessage->setVariable('mailReply', tr('mailto_respByOc'));
        }
        $userMessage->addFooterAndHeader($to->getUserName(), ! $attachSenderAddress);
        // send email to Recipient
        $email = new Email();
        $email->addToAddr($to->getEmail());
        if ($attachSenderAddress) {
            $email->setReplyToAddr($from->getEmail());
            $email->setFromAddr($from->getEmail());
        } else {
            $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
            $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        }
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
        $email->setSubject($subject);
        $email->setBody($userMessage->getEmailContent(), true);
        $result = $email->send();
        if (! $result) {
            error_log(__METHOD__ . ': Mail sending failure to: ' . $to->getEmail());
        }
        return $result;
    }

    /**
     * Sends copy of U2U mail to its sender
     * Params from and to functions like in sendU2UMessage -
     * they refer to oryginal message, not copy!
     *
     * @param User $from
     * @param User $to
     * @param string $subject
     * @param string $text
     * @return boolean
     */
    public static function sendU2UCopy(User $from, User $to, $subject, $text)
    {
        // add additional prefix to subject
        $subject = tr('mailto_emailTo') . ' ' . $to->getUserName() . ': ' . $subject;
        // prepare message text
        $userMessage = new EmailFormatter(self::TEMPLATE_PATH . 'messageCopyForSender.email.html', true);
        $userMessage->setVariable('absoluteServerURI', rtrim(OcConfig::getAbsolute_server_URI(), '/'));
        $userMessage->setVariable('text', nl2br($text));
        $userMessage->setVariable('toUserProfileUrl', $to->getProfileUrl());
        $userMessage->setVariable('toUsername', $to->getUserName());
        $userMessage->addFooterAndHeader($from->getUserName());
        // send email to Recipient
        $email = new Email();
        $email->addToAddr($from->getEmail());
        $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
        $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
        $email->setSubject($subject);
        $email->setBody($userMessage->getEmailContent(), true);
        $result = $email->send();
        if (! $result) {
            error_log(__METHOD__ . ': Sender copy sending failure to: ' . $from->getEmail());
        }
        return $result;
    }
}