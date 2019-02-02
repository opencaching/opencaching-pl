<?php
namespace lib\Objects\OcConfig;

use Utils\Email\Email;


/**
 * This trait group access to email settings stored in /config/email.* conf. files
 */
trait EmailConfigTrait {

    protected $emailConfig = null;

    /**
     * Retuns email address of OcTeam
     *
     * @param boolean $forWebDisplay - if set change email to format 'account (at) server'
     * @return string email address
     */
    public static function getEmailAddrOcTeam($forWebDisplay=false)
    {
        $email = self::getEmailVar('ocTeamContactEmail');
        if($forWebDisplay){
            return self::emailToDisplay($email);
        } else {
            return $email;
        }
    }

    /**
     * Returns signature used in OcTeam emails
     *
     * @return string - signature of OcTeam
     */
    public static function getOcteamEmailsSignature()
    {
        return self::getVar('ocTeamEmailSignature');
    }

    /**
     * Retruns email address used as sender address for generated emails.
     *
     * @return string - noReply address
     */
    public static function getEmailAddrNoReply()
    {
        return self::getEmailVar('noReplyEmail');
    }

    /**
     * Returns email address used as a technical contact for users
     * @return string - admin address
     */
    public static function getEmailAddrTechAdmin()
    {
        return self::getEmailVar('nodeTechContactEmail');
    }

    /**
     * Returns email address used to send technical notifications
     * @return string - techNotify addr.
     */
    public static function getEmailAddrTechAdminNotification()
    {
        return self::getEmailVar('technicalNotificationEmail');
    }

    /**
     * Returns prefix used in subject of emails send by OC code
     * @return string - prefix
     */
    public static function getEmailSubjectPrefix()
    {
        return self::getVar('mailSubjectPrefix');
    }

    /**
     * Returns prefix used in subject of emails send in context of OcTeam operations
     * @return string
     */
    public static function getEmailSubjectPrefixForOcTeam()
    {
        return self::getVar('mailSubjectPrefixForReviewers');
    }

    /**
     * Read config from files
     * @return unknown
     */
    private function getEmailConfig(){
        if ($this->emailConfig == null) {
            $this->emailConfig = self::getConfig('email');
        }
        return $this->emailConfig;
    }

    /**
     * Get Var from email.* files
     *
     * @param string $varName
     * @throws \Exception
     * @return string
     */
    private static function getVar($varName)
    {
        $emailConfig = self::instance()->getEmailConfig();
        if (!is_array($emailConfig)) {
            throw new \Exception("Invalid $varName setting: see /config/email.*");
        }
        return $emailConfig[$varName];
    }

    /**
     * Get Var from email.* files without hashes + check if this is proper email addr.
     *
     * @param string $varName
     * @throws \Exception
     * @return mixed
     */
    private static function getEmailVar($varName)
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

    /**
     * Strip hashes from text
     *
     * @param string $text
     * @return string
     */
    private static function removeHashes($text)
    {
        return str_replace('#', "", $text);
    }

    /**
     * Convert email address to form 'addres (at) server'
     * @param string $email
     * @return mixed
     */
    private static function emailToDisplay($email)
    {
        return str_replace('@', " (at) ", $email);
    }
}
