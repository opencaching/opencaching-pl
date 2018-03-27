<?php
namespace lib\Objects\User;

use Utils\Email\Email;
use Utils\Email\EmailFormatter;
use Utils\Uri\SimpleRouter;
use lib\Objects\OcConfig\OcConfig;

class UserEmailSender
{

    const TEMPLATE_PATH = __DIR__ . '/../../../tpl/stdstyle/email/user/';

    public static function sendActivationMessage($username)
    {
        $user = User::fromUsernameFactory($username);
        $intro = mb_ereg_replace('{OCsiteLink}', '<a href="' . OcConfig::getAbsolute_server_URI() . '">' . OcConfig::getSiteName() . '</a>', tr('activate_mail_intro'));
        $activateUrl = SimpleRouter::getAbsLink('UserAuthorization', 'activate', [$user->getUserId(), urlencode($user->getActivationCode())]);

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