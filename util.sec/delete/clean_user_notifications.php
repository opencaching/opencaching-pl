<?php

use Utils\Database\OcDb;
/*
 * Simple script to disable notifications send by OC server to a specific user.
 * The script is intended for admins only, in cases when the user wants to opt-out
 * but the account cannot be deleted (user has already logged some activity).
 *
 * This script should be run from command-line on the server console.
 *
 * Usage:
 *      php clean_user_notifications.php <username>
 *
 */

if (php_sapi_name() != "cli") {
    printf("This script should be run from command-line only.\n");
    exit(1);
}

$rootpath = __DIR__ . '/../../';
require_once __DIR__ . '/../../lib/common.inc.php';

$db = OcDb::instance();

// inline options
$shortopts = "u:";
$shortopts .= "h";
$longopts  = array(
    "userId:"           // No value
);
$options = getopt($shortopts, $longopts);


if( isset($options['h']) ){
    printUsageAndExit();
}


$userName = isset($options['u']) ?            $options['u']     : null;
$userId =   isset($options['userId']) ? (int) $options['userId']: null;

if( is_null($userId) && is_null($userName) ){
    echo "Error: No param given?!\n\n";
    printUsageAndExit();
}

if(!is_null($userId) && !is_integer($userId)){
    echo "Error: userId param has to be integer value!\n\n";
    printUsageAndExit();
}

if(!is_null($userId) && !is_null($userName)){
    echo "Warning: Both username and userId params given - username will be skipped!\n\n";
    $userName = null;
}

// find userId
if(is_null($userId)){
    // Check that user exists
    $userId = $db->multiVariableQueryValue('SELECT user_id FROM user WHERE username = :1', 0, $userName);
    if ($userId == 0) {
        printf("Error: User not found: %s\n", $userName);
        exit(2);
    }
}


// Clean notify radius - to avoid notification about new caches
$notify_radius = $db->multiVariableQueryValue("SELECT notify_radius FROM user WHERE user_id = :1", -1, $userId);
printf("Notify radius: %skm\n", $notify_radius);

if ($notify_radius > 0) {
    printf("Resetting notify radius\n");
    $db->multiVariableQuery("UPDATE user SET notify_radius = 0 WHERE user_id = :1", $userId);
}

// Clean all cache_watches
$s = $db->multiVariableQuery('SELECT cache_id FROM cache_watches WHERE user_id = :1', $userId);
$cache_watches = $db->dbResultFetchAll($s);
foreach ($cache_watches as $watch) {
    remove_watch($watch['cache_id'], $userId);
}

function printUsageAndExit(){

    echo "Usage: \n";
    echo " php clean_user_notifications.php -u <username>\n";
    echo "   or \n";
    echo " php clean_user_notifications.php --userId=<userId>\n";
    exit(1);

}

function remove_watch($cache_id, $userId) {
    global $db;

    printf("Watched cache ID: %s\n", $cache_id);

    // Remove watch
    $db->multiVariableQuery('DELETE FROM cache_watches WHERE cache_id = :1 AND user_id = :2', $cache_id, $userId);

}