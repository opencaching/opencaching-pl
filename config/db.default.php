<?php
/**
 * DB related settings
 *
 * This is a default configuration.
 * It may be customized in node-specific configuration file
 *
 * Please note: PASSWORDS MUST BE STORED OUTSIDE OF REPOSITORY IN "db.local.php" file
 */

$db = [];

//
// Temporary var - it will be removed when all nodes migrate to new config
// Default TRUE means that legacy settings.inc.php DB config needs to be used
// when db.local.php is ready set this var to false there
//
$db['_TMP_useLegacyConfig'] = true;

/**
 * Address of the DB server
 */
$db['dbhost'] = 'localhost';

/**
 * Name of the database
 */
$db['dbname'] = 'oc';

/**
 * Username of the database
 */
$db['dbuser'] = 'ocUser';

/**
 * Username of the database
 * DO NOT STORE PASSWORD IN REPOSITORY place it only in "db.local.php"
 */
$db['dbpass'] = 'secret';


