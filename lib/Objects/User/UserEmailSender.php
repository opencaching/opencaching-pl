<?php
namespace lib\Objects\User;

use Utils\Email\Email;
use Utils\Email\EmailFormatter;
use Utils\Uri\SimpleRouter;
use lib\Objects\OcConfig\OcConfig;

class UserEmailSender
{

    const TEMPLATE_PATH = __DIR__ . '/../../../tpl/stdstyle/email/user/';

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

        // prepare message text
        $userMessage = new EmailFormatter(self::TEMPLATE_PATH . 'messageU2U.email.html', true);
        $userMessage->setVariable('fromUserMailUrl', SimpleRouter::getAbsLink('UserProfile', 'mailTo', [
            $from->getUserId(),
            urlencode($subject)
        ]));
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
        // add additional prefix to subject
        $subject = tr('mailto_emailFrom') . ' ' . $from->getUserName() . ': ' . $subject;
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

    /**
     * Sends activation message to user identyfied by nick $username
     *
     * @param string $username
     */
    public static function sendActivationMessage($username)
    {
        $user = User::fromUsernameFactory($username);
        $intro = mb_ereg_replace('{OCsiteLink}', '<a href="' . OcConfig::getAbsolute_server_URI() . '">' . OcConfig::getSiteName() . '</a>', tr('activate_mail_intro'));
        $activateUrl = SimpleRouter::getAbsLink('UserRegistration', 'activate', [
            $user->getUserId(),
            urlencode($user->getActivationCode())
        ]);

        $userMessage = new EmailFormatter(self::TEMPLATE_PATH . 'activation.email.html', true);
        $userMessage->setVariable('activateUrl', $activateUrl);
        $userMessage->setVariable('intro', $intro);
        $userMessage->addFooterAndHeader($username);

        $email = new Email();
        $email->addToAddr($user->getEmail());
        $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
        $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
        $email->setSubject(tr('activate_mail_subject'));
        $email->setBody($userMessage->getEmailContent(), true);
        $email->send();
    }

    /**
     * Sends information e-mail to $user who just activated his account
     *
     * @param User $user
     */
    public static function sendPostActivationMessage(User $user)
    {
        $userMessage = new EmailFormatter(self::TEMPLATE_PATH . 'postactivation.email.html', true);
        $userMessage->addFooterAndHeader($user->getUserName());
        $userMessage->setVariable('wikiaddress', OcConfig::getWikiLink('forBeginers'));
        $userMessage->setVariable('guidesurl', OcConfig::getAbsolute_server_URI() . 'cacheguides.php');
        $userMessage->setVariable('postActivation_mail_04', mb_ereg_replace('{NEED_FIND_LIMIT}', OcConfig::getNeedFindLimit(), tr('postActivation_mail_04')));
        $email = new Email();
        $email->addToAddr($user->getEmail());
        $email->setReplyToAddr(OcConfig::getNoreplyEmailAddress());
        $email->setFromAddr(OcConfig::getNoreplyEmailAddress());
        $email->addSubjectPrefix(OcConfig::getMailSubjectPrefixForSite());
        $email->setSubject(tr('postActivation_mail_subject'));
        $email->setBody($userMessage->getEmailContent(), true);
        $email->send();
    }
}