<?php
namespace Utils\Text;

class Validator
{

    const MAX_EMAIL_LENGTH = 60;

    const MIN_USERNAME_LENGTH = 3;

    const MAX_USERNAME_LENGTH = 60;

    const REGEX_USERNAME = '^[a-zA-Z0-9ęóąśłżźćńĘÓĄŚŁŻŹĆŃăîşţâĂÎŞŢÂșțȘȚéáöőüűóúÉÁÖŐÜŰÓÚ@-][a-zA-ZęóąśłżźćńĘÓĄŚŁŻŹĆŃăîşţâĂÎŞŢÂșțȘȚéáöőüűóúÉÁÖŐÜŰÓÚ0-9\.\-=_ @ęóąśłżźćńĘÓĄŚŁŻŹĆŃăîşţâĂÎŞŢÂșțȘȚéáöőüűóúÉÁÖŐÜŰÓÚäüöÄÜÖ=)(\/\\\ -=&*+~#]{2,59}$';

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
        if (preg_match('/[!,%,&,@,#,$,^,*,?,_,~]/', $password)) {
            $diff += 1;
        }
        if (mb_strlen($password) >= self::MIN_PASSWORD_LENGTH && $diff > 1) {
            return true;
        } else {
            return false;
        }
    }
}