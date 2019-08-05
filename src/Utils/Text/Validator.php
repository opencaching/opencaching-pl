<?php
namespace src\Utils\Text;

use Exception;

class Validator
{

    const MAX_EMAIL_LENGTH = 60;

    const MIN_USERNAME_LENGTH = 3;

    const MAX_USERNAME_LENGTH = 60;

    const REGEX_USERNAME = '^[a-zA-Z0-9ęóąśłżźćńĘÓĄŚŁŻŹĆŃăîşţâĂÎŞŢÂșțȘȚéäáöőüűóúÉÄÁÖŐÜŰÓÚ@-][a-zA-ZęóąśłżźćńĘÓĄŚŁŻŹĆŃăîşţâĂÎŞŢÂșțȘȚäéáöőüűóúÉÄÁÖŐÜŰÓÚ0-9\.\-=_ @)(\/\\&*+~#]{2,59}$';

    const MIN_PASSWORD_LENGTH = 6;

    /**
     * Checks if $email is a valid e-mail address
     *
     * @param string $email
     * @return boolean
     */
    public static function isValidEmail($email)
    {
        if (strlen($email) > self::MAX_EMAIL_LENGTH) {
            return false;
        }
        return (false !== filter_var($email, FILTER_VALIDATE_EMAIL));
    }

    /**
     * Checks if $username string meets requirements for username
     * It doesn't check if user exists!
     *
     * @param string $username
     * @return boolean
     */
    public static function isValidUsername($username)
    {
        if (strlen($username) > self::MAX_USERNAME_LENGTH || strlen($username) < self::MIN_USERNAME_LENGTH) {
            return false;
        }
        return (mb_ereg_match(self::REGEX_USERNAME, $username));
    }

    /**
     * Verifies strength of the password.
     * Returns true if password contains of MIN_PASSWORD_LENGTH+ characters AND
     * minimum two of: uppercase letters, lowercase letters, digits, special chars
     *
     * @param string $password
     * @return boolean
     */
    public static function checkStrength($password)
    {
        $diff = 0;
        if (preg_match('/[a-z]/', $password)) {
            $diff += 1;
        }
        if (preg_match('/[A-Z]/', $password)) {
            $diff += 1;
        }
        if (preg_match('/[0-9]/', $password)) {
            $diff += 1;
        }
        if (preg_match('/[!%&@#$^*?_~]/', $password)) {
            $diff += 1;
        }
        if (mb_strlen($password) >= self::MIN_PASSWORD_LENGTH && $diff > 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Verifies the syntax of a geocaching.com cache code.
     *
     * Minimum string length is 4. ILOSU are not used, because they could be
     * confused with other chars/digits. GC maps O => 0 and S => 5.
     *
     * @param string $code
     * @return string|boolean - optimized cache code, or false if invalid
     */
     public static function gcWaypoint($code)
     {
        // try to fix it
        $code = preg_replace('/\s/', '', $code);
        $code = strtoupper($code);
        $code = str_replace(['O', 'S'], ['0', '5'], $code);

        // verify
        if (preg_match('/^GC[0-9A-HJKMNPQRTV-Z]{2,5}$/', $code)) {
            return $code;
        } else {
            return false;
        }
     }

    /**
     * Verifies the syntax of a terracaching.com cache code.
     *
     * Minimum string length is 3 - there actually is a cache TC2!
     * Second letter is the cache category: Traditional, Locationless or Cyber.
     *
     * Note that some OC PL users did enter OpenCaching.com 'OX' codes in the
     * TC waypoint field (total ~75 caches).
     *
     * @param string $code
     * @return string|boolean - optimized cache code, or false if invalid
     */
     public static function tcWaypoint($code)
     {
        // try to fix it
        $code = preg_replace('/\s/', '', $code);
        $code = strtoupper($code);

        // verify
        if (preg_match('/^[TLC]C[0-9A-Z]{1,4}$/', $code)) {
            return $code;
        } else {
            return false;
        }
     }

    /**
     * Verifies the syntax of a gpsgames.org cache code.
     * (4-digit hex numbers)
     *
     * @param string $code
     * @return string|boolean - optimized cache code, or false if invalid
     */
     public static function geWaypoint($code)
     {
        // try to fix it
        $code = preg_replace('/\s/', '', $code);
        $code = strtoupper($code);

        // verify
        if (preg_match('/^GE[0-9A-F]{4}$/', $code)) {
            return $code;
        } else {
            return false;
        }
     }

    /**
     * Pro-forma validator for obsolete NC waypoints
     * Must not return false, because we did not define an NC error message.
     *
     * @param $code
     * @return string
     */
    public static function ncWaypoint($code)
    {
        return strtoupper(trim($code));
    }

    /**
     * Validate waypoint code by type
     *
     * @param $wpType
     * @param $code
     * @return bool|string
     * @throws Exception
     */
    public static function xxWaypoint($wpType, $code)
    {
        switch (strtoupper($wpType)) {
            case 'GC': return self::gcWaypoint($code);
            case 'TC': return self::tcWaypoint($code);
            case 'GE': return self::geWaypoint($code);
            case 'NC': return self::ncWaypoint($code);
            case 'QC': return $code;  // obsolete
            default:   throw new Exception('Invalid waypoint code type: '.$code);
        }
    }
}
