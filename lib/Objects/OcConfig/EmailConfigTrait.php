<?php

namespace lib\Objects\OcConfig;

use Utils\Email\Email;

trait EmailConfigTrait {

    protected $emailConfig = null;

    public static function getOcteamEmailAddress($forWebDisplay=false)
    {
        $email = self::getEmailVar('ocTeamContactEmail');
        if($forWebDisplay){
            return self::emailToDisplay($email);
        } else {
            return $email;
        }
    }

    public static function getOcteamEmailsSignature()
    {
        return self::getVar('ocTeamEmailSignature');
    }

    public static function getNoreplyEmailAddress()
    {
        return self::getEmailVar('noReplyEmail');
    }

    public static function getTechAdminsEmailAddr()
    {
        return self::getEmailVar('nodeTechContactEmail');
    }

    public function getEmailConfig(){
        if ($this->emailConfig == null) {
            $this->emailConfig = self::getConfig('email');
        }
        return $this->emailConfig;
    }

    public static function getMailSubjectPrefixForSite()
    {
        return self::getVar('mailSubjectPrefix');
    }

    public static function getMailSubjectPrefixForReviewers()
    {
        return self::getVar('mailSubjectPrefixForReviewers');
    }

    protected static function getVar($varName)
    {
        $emailConfig = self::instance()->getEmailConfig();
        if (!is_array($emailConfig)) {
            throw new \Exception("Invalid $varName setting: see /config/email.*");
        }
        return $emailConfig[$varName];
    }

    protected static function getEmailVar($varName)
    {
        $emailConfig = self::instance()->getEmailConfig();
        if (!is_array($emailConfig)) {
            throw new \Exception("Invalid $varName setting: see /config/email.*");
        }

        $email = self::removeHashes($emailConfig[$varName]);
        if (!Email::isValidEmailAddr($email)) {
            throw new \Exception("Invalid $varName setting: see /config/email.*");
        }

        return $email;
    }

    protected static function removeHashes($text)
    {
        return str_replace('#', "", $text);
    }

    protected static function emailToDisplay($email)
    {
        return str_replace('@', " (at) ", $email);
    }
}
