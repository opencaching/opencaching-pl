<?php

namespace src\Models\OcConfig;

use Exception;
use src\Utils\Email\Email;

/**
 * This trait group access to email settings stored in /config/email.* conf. files
 * BEWARE OF FUNCTIONS NAME COLLISION BETWEEN CONFIG TRAITS!
 */
trait EmailConfigTrait
{
    protected $emailConfig = null;

    /**
     * Retuns email address of OcTeam
     *
     * @param boolean $forWebDisplay - if set change email to format 'account (at) server'
     * @return string email address
     */
    public static function getEmailAddrOcTeam($forWebDisplay = false)
    {
        $email = self::getEmailAddrVar('ocTeamContactEmail');

        if ($forWebDisplay) {
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
        return self::getEmailVar('ocTeamEmailSignature');
    }

    /**
     * Retruns email address used as sender address for generated emails.
     *
     * @return string - noReply address
     */
    public static function getEmailAddrNoReply()
    {
        return self::getEmailAddrVar('noReplyEmail');
    }

    /**
     * Returns email address used as a technical contact for users
     *
     * @return string - admin address
     */
    public static function getEmailAddrTechAdmin()
    {
        return self::getEmailAddrVar('nodeTechContactEmail');
    }

    /**
     * Returns email address used to send technical notifications
     *
     * @return array - array of addresses to send techNotify emails
     */
    public static function getEmailAddrTechAdminNotification()
    {
        $email = self::getEmailAddrVar('technicalNotificationEmail');

        if (! is_array($email)) {
            return [$email];
        } else {
            return $email;
        }
    }

    /**
     * Returns prefix used in subject of emails send by OC code
     *
     * @return string - prefix
     */
    public static function getEmailSubjectPrefix()
    {
        return self::getEmailVar('mailSubjectPrefix');
    }

    /**
     * Returns prefix used in subject of emails send in context of OcTeam operations
     *
     * @return string
     */
    public static function getEmailSubjectPrefixForOcTeam()
    {
        return self::getEmailVar('mailSubjectPrefixForReviewers');
    }

    /**
     * Read config from files
     *
     * @return array
     */
    private function getEmailConfig()
    {
        if ($this->emailConfig == null) {
            $this->emailConfig = self::getConfig('email');
        }

        return $this->emailConfig;
    }

    /**
     * Get Var from email.* files
     *
     * @param string $varName
     * @return string
     * @throws Exception
     */
    private static function getEmailVar($varName)
    {
        $emailConfig = self::instance()->getEmailConfig();

        if (! is_array($emailConfig)) {
            throw new Exception("Invalid {$varName} setting: see /config/email.*");
        }

        return $emailConfig[$varName];
    }

    /**
     * Get Var from email.* files without hashes + check if this is proper email addr.
     *
     * @param string $varName
     * @return mixed
     * @throws Exception
     */
    private static function getEmailAddrVar($varName)
    {
        $emailConfig = self::instance()->getEmailConfig();

        if (! is_array($emailConfig)) {
            throw new Exception("Invalid {$varName} setting: see /config/email.*");
        }

        $email = self::removeHashes($emailConfig[$varName]);

        if (! Email::isValidEmailAddr($email)) {
            throw new Exception("Invalid {$varName} setting: see /config/email.*");
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
        return str_replace('#', '', $text);
    }

    /**
     * Convert email address to form 'addres (at) server'
     *
     * @param string $email
     * @return mixed
     */
    private static function emailToDisplay($email)
    {
        return str_replace('@', ' (at) ', $email);
    }
}
