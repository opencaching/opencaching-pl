<?php
/**
 * User-related configuration
 *
 * This is a default user configuration.
 * It may be customized in node-specific configuration file.
 */

$config = [];

/**
 * Removed user account has anonimized username in format: <removedUserUsernamePrefix>_<date-of-removing>_<userId>
 */
$config['removedUserUsernamePrefix'] = "Account_removed";

/**
 * Text displayed in as description of removed user account - formatted date will be added at the end of the text
 */
$config['removedUserDescription'] = "Account removed on user request.";

/**
 * Default text displayed on statpic images
 */
$config['defaultStatpicText'] = 'Opencaching';
