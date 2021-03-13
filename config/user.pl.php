<?php
/**
 * User-related configuration
 *
 * Those are configuration overrides for OCPL node only.
 */

/**
 * Removed user account has anonimized username in format: <removedUserUsernamePrefix>_<date-of-removing>_<userId>
 */
$config['removedUserUsernamePrefix'] = "Konto_usuniete";

/**
 * Text displayed in as description of removed user account - formatted date will be added at the end of the text
 */
$config['removedUserDescription'] = "Konto usunięte";
