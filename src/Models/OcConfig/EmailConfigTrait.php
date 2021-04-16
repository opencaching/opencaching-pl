<?php

namespace src\Models\OcConfig;

use Exception;
use src\Utils\Email\Email;

/**
 * Loads configuration from email.*.php.
 *
 * @mixin OcConfig
 */
trait EmailConfigTrait
{
    protected $emailConfig = null;

    /**
     * Get the email address of OcTeam.
     *
     * @param boolean $forWebDisplay Format email address to 'account (at) server'
     */
    public static function getEmailAddrOcTeam(bool $forWebDisplay = false): string
    {
        $email = self::getEmailAddressFromConfig('ocTeamContactEmail');

        return $forWebDisplay
            ? self::emailToDisplay($email)
            : $email;
    }

    /**
     * Get the signature used in OcTeam emails.
     */
    public static function getOcteamEmailsSignature(): string
    {
        return self::getKeyFromEmailConfig('ocTeamEmailSignature');
    }

    /**
     * Get the email address used as sender address for generated emails.
     */
    public static function getEmailAddrNoReply(): string
    {
        return self::getEmailAddressFromConfig('noReplyEmail');
    }

    /**
     * Get the email address used as a technical contact for users.
     */
    public static function getEmailAddrTechAdmin(): string
    {
        return self::getEmailAddressFromConfig('nodeTechContactEmail');
    }

    /**
     * Get the email address used to send technical notifications.
     *
     * @return string[]
     */
    public static function getEmailAddrTechAdminNotification(): array
    {
        return (array) self::getEmailAddressFromConfig('technicalNotificationEmail');
    }

    /**
     * Get the prefix used in subject of emails sent by OC code.
     */
    public static function getEmailSubjectPrefix(): string
    {
        return self::getKeyFromEmailConfig('mailSubjectPrefix');
    }

    /**
     * Get the prefix used in subject of emails send in context of OcTeam operations.
     */
    public static function getEmailSubjectPrefixForOcTeam(): string
    {
        return self::getKeyFromEmailConfig('mailSubjectPrefixForReviewers');
    }

    private function getEmailConfig(): array
    {
        if (! $this->emailConfig) {
            $this->emailConfig = self::getConfig('email');
        }

        return $this->emailConfig;
    }

    /**
     * @return mixed
     */
    private static function getKeyFromEmailConfig(string $key)
    {
        $emailConfig = self::instance()->getEmailConfig();

        return $emailConfig[$key];
    }

    /**
     * Get key from email config, strip hashes from it and make sure it is
     * a valid email address.
     */
    private static function getEmailAddressFromConfig(string $key): string
    {
        $email = self::stripHashes(self::getKeyFromEmailConfig($key));

        if (! Email::isValidEmailAddr($email)) {
            throw new Exception("Invalid {$key} setting: see /config/email.*");
        }

        return $email;
    }

    private static function stripHashes(string $text): string
    {
        return str_replace('#', '', $text);
    }

    /**
     * Format email address to 'address (at) server'
     */
    private static function emailToDisplay(string $email): string
    {
        return str_replace('@', ' (at) ', $email);
    }
}
