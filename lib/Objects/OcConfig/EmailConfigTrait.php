<?php

namespace lib\Objects\OcConfig;

use Utils\Email\Email;

trait EmailConfigTrait {

    protected $emailConfig = null;

    public static function getOcteamEmailAddress()
    {
        return self::getEmailVar('ocTeamContactEmail');
    }

    public static function getOcteamEmailsSignature()
    {
        return self::getVar('ocTeamEmailSignature');
    }

    public static function getNoreplyEmailAddress()
    {
        // $this->noreplyEmailAddress = $emailaddr;
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
}
