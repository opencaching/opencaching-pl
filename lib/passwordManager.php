<?php

use Utils\Database\OcDb;
class PasswordManager
{

    /**
     * By default, if hashes stored in the database are considered unsafe,
     * PasswordManager will automatically "upgrade" the stored hashes to a
     * newer version so that they meet our security criteria. This upgrade
     * will be performed automatically during the password verification
     * process.
     *
     * If - for some reason - you don't want the passwords to be upgraded,
     * set this to false, e.g.:
     *
     * $manager = new PasswordManager($user_id);
     * $manager->autoUpgradeOldHashes = false;
     * if ($manager->verify($password)) { ... }
     */
    public $autoUpgradeOldHashes;
    private $user_id;
    private $hash;
    private $salt;
    private $rounds;
    private $wantedHashingRounds;
    private $wantedSaltLength;

    /**
     * This is set only after its verified to be correct.
     */
    private $correctPassword;

    /**
     * Create an instance of PasswordManager for a given user.
     */
    public function __construct($user_id)
    {
        $this->user_id = $user_id;

        /* Get the database handle. */

        $db = OcDb::instance();

        /* Fetch current password state */

        $c = $db->prepare("
            select password, password_salt, password_hashing_rounds
            from `user`
            where user_id = :user_id
        ");
        $c->bindParam(":user_id", $user_id);
        $c->execute();
        $row = $c->fetch();

        if ($row == null) {
            throw new Exception("Invalid user_id");
        }

        $this->hash = $row['password'];
        $this->salt = $row['password_salt'];
        $this->rounds = (int) $row['password_hashing_rounds'];

        /* Initialize the rest of the variables. */

        $this->correctPassword = null;
        $this->autoUpgradeOldHashes = true;
        $this->wantedHashingRounds = 100000;
        $this->wantedSaltLength = 6;
    }

    /**
     * Return true, if the given password matches the one stored in the
     * database.
     */
    public function verify($password)
    {
        $hash = $this->computeHash($password);
        if ($hash == $this->hash) {
            $this->correctPassword = $password;
        }
        if ($this->autoUpgradeOldHashes && $this->needsUpgrade()) {
            $this->performUpgrade();
        }
        return ($this->correctPassword !== null);
    }

    /**
     * Force a password to be changed. Use this in password-change or
     * password-recovery forms.
     */
    public function change($newPassword)
    {
        $this->correctPassword = $newPassword;
        $this->performUpgrade();
    }

    /**
     * Return true, if the current hash is obsolete and needs an upgrade.
     */
    private function needsUpgrade()
    {
        if ($this->rounds != $this->wantedHashingRounds) {
            return true;
        }
        if (strlen($this->salt) != $this->wantedSaltLength) {
            return true;
        }
        return false;
    }

    /**
     * Force the password hash to be recalculated and store the new hash in
     * the database. Return true if the upgrade succeeded.
     */
    private function performUpgrade()
    {
        /* Note, that the upgrade CAN be performed even when we don't know the
         * original password. However, the process in slightly different in
         * such case. */

        if ($this->correctPassword === null) {

            /* We don't know the password. */

            $previousRounds = $this->rounds;
            if ($previousRounds > $this->wantedHashingRounds) {

                /* We would have to reduce the number of rounds, which is
                 * impossible in this case. No update can be performed. */

                return false;
            }
            $this->rounds = $this->wantedHashingRounds;

            if ($previousRounds <= 1) {

                /* Since the first round doesn't use a salt, it is safe for us
                 * to change it. */

                $this->salt = self::generateRandomString($this->wantedSaltLength);
            }

            $this->hash = $this->computeHash(
                    /* This is the current hash (which is the original password
                     * after $previousRounds hashing rounds). */
                    $this->hash,
                    /* The number of hashing rounds that `computeHash` should
                     * skip. */ $previousRounds
            );
        } else {

            /* The correct password is known. In that case, we will generate
             * the hash "from scratch", and with a new salt. */

            $this->rounds = $this->wantedHashingRounds;
            $this->salt = self::generateRandomString($this->wantedSaltLength);
            $this->hash = $this->computeHash($this->correctPassword);
        }

        $db = OcDb::instance();
        $c = $db->prepare("
            update `user`
            set
                password = :password,
                password_salt = :password_salt,
                password_hashing_rounds = :password_hashing_rounds
            where
                user_id = :user_id
        ");
        $c->bindParam(":user_id", $this->user_id);
        $c->bindParam(":password", $this->hash);
        $c->bindParam(":password_salt", $this->salt);
        $c->bindParam(":password_hashing_rounds", $this->rounds);
        $c->execute();

        return true;
    }

    /**
     * Perform a *single* salting & hashing round and return the hash.
     */
    private function singleHashingRound($round_number, $input)
    {
        if ($round_number == 1) {

            /* We'll keeping the unsalted MD5 + SHA512 as the first hashing
             * round, for backward compatibility. (This makes it possible to
             * add salt to existing hashes without knowing the correct
             * passwords.) */

            return hash('sha512', md5($input));
        } else {

            /* All the other rounds are salted. */

            return hash('sha512', $input . $this->salt);
        }
    }

    /**
     * Return a correct hash of the given $password. Salt and rounds are
     * taken into account.
     *
     * If skippedRounds parameter is grater than 0, then the provided password
     * must be previously hashed with the given number of rounds. This is
     * useful for salting yet unsalted hashed without knowing the original
     * password.
     */
    private function computeHash($password, $skippedRounds = 0)
    {
        if ($skippedRounds > $this->rounds) {
            throw new Exception();
        }
        $input = $password;
        for ($i = $skippedRounds; $i < $this->rounds; $i++) {
            $input = $this->singleHashingRound($i + 1, $input);
        }
        return $input;
    }

    /**
     * Utility function. Return a random string of specified length.
     */
    public static function generateRandomString($length)
    {
        $characters = '23456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}
