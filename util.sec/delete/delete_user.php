<?php

use Utils\Database\OcDb;
/*
 * Simple script to delete user account. The script is intended for admins only,
 * in cases when the user wants to opt-out and no activity has been recorded.
 *
 * This script should be run from command-line on the server console.
 *
 * Script based on original delete script created by the OpenCaching Group
 * Usage:
 *      php delete_user.php <username>
 *
 */

if (php_sapi_name() != "cli") {
    printf("This script should be run from command-line only.\n");
    exit(1);
}

$rootpath = __DIR__ . '/../../';
require_once __DIR__ . '/../../lib/common.inc.php';


$db = OcDb::instance();

function remove_watch($cache_id, $user_id) {

    printf("Watched cache ID: %s\n", $cache_id);

    // Remove watch
    $db->multiVariableQuery('DELETE FROM cache_watches WHERE cache_id = :1 AND user_id = :2', $cache_id, $user_id);

}

if (!isset($argv[1])) {
    echo "Usage: php delete_user.php <username>\n";
    exit(1);
}

$user_name = $argv[1];

// Check that user exists
$user_id = $db->multiVariableQueryValue('SELECT user_id FROM user WHERE username = :1', 0, $user_name);
if ($user_id == 0) {
    printf("User not found: %s\n", $user_name);
    exit(2);
}

// Check if user owns any caches
$cache_count = $db->multiVariableQueryValue('SELECT COUNT(*) FROM caches WHERE user_id = :1', 0, $user_id);
if ($cache_count > 0) {
    printf("Cannot delete: user %s owns %d cache(s)\n", $user_name, $cache_count);
    exit(3);
}

// Check if user created any logs
$log_count = $db->multiVariableQueryValue('SELECT COUNT(*) FROM cache_logs WHERE user_id = :1', 0, $user_id);
if ($log_count > 0) {
    printf("Cannot delete: user %s created %d log(s)\n", $user_name, $log_count);
    exit(4);
}

// Check if user saved any pictures
// Probably an overkill after checking that there are no caches/no logs.
$pic_count = $db->multiVariableQueryValue('SELECT COUNT(*) FROM pictures WHERE user_id = :1', 0, $user_id);
if ($pic_count > 0) {
    printf("Cannot delete: user %s saved %d picture(s)\n", $user_name, $pic_count);
    exit(5);
}

// Record user removal
$db->multiVariableQuery(
        "INSERT INTO removed_objects (localid, uuid, `type`, removed_date, node)" .
        " SELECT user_id, uuid, 4, NOW(), :2 FROM user WHERE user_id = :1", $user_id, $oc_nodeid);

// Delete saved queries
$db->multiVariableQuery('DELETE FROM queries WHERE user_id = :1', $user_id);

// Clean all cache_watches
$s = $db->multiVariableQuery('SELECT cache_id FROM cache_watches WHERE user_id = :1', $user_id);
$cache_watches = $db->dbResultFetchAll($s);
foreach ($cache_watches as $watch) {
    remove_watch($watch['cache_id'], $user_id);
}

// Delete user
$db->multiVariableQuery('DELETE FROM user WHERE user_id = :1', $user_id);

printf("User %s deleted\n", $user_name);
