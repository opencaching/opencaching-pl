<?php

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
    printf ("This script should be run from command-line only.\n");
    exit(1);
}

$rootpath = __DIR__ . '/../../';
require_once __DIR__ . '/../../lib/common.inc.php';
db_disconnect();

$db = \lib\Database\DataBaseSingleton::Instance();

function remove_watch($cache_id, $user_id)
{
    global $db;

    printf ("Watched cache ID: %s\n", $cache_id);

    $db->beginTransaction();

    // Remove watch
    $db->multiVariableQuery('DELETE FROM cache_watches WHERE cache_id = :1 AND user_id = :2', $cache_id, $user_id);

    // Reduce numer of watchers of cache
    $watchers = $db->multiVariableQueryValue('SELECT watcher FROM caches WHERE cache_id = :1', -1, $cache_id);
    if ($watchers > 0) {
        $watchers -= 1;
        $db->multiVariableQuery('UPDATE caches SET watcher = :1 WHERE cache_id = :2', $watchers, $cache_id);
    }

    $db->commit();
}

$user_name = $argv[1];

// Check that user exists
$user_id = $db->multiVariableQueryValue('SELECT user_id FROM user WHERE username = :1', 0, $user_name);
if ($user_id == 0) {
    printf ("User not found: %s\n", $user_name);
    exit(2);
}

// Clean notify radius - to avoid notification about new caches
$notify_radius = $db->multiVariableQueryValue("SELECT notify_radius FROM user WHERE user_id = :1", -1, $user_id);
printf ("Notify radius: %skm\n", $notify_radius);

if ($notify_radius > 0) {
    printf ("Resetting notify radius\n");
    $db->multiVariableQuery("UPDATE user SET notify_radius = 0 WHERE user_id = :1", $user_id);
}

// Clean all cache_watches
$db->multiVariableQuery('SELECT cache_id FROM cache_watches WHERE user_id = :1', $user_id);
$cache_watches = $db->dbResultFetchAll();
foreach ($cache_watches as $watch) {
    remove_watch($watch['cache_id'], $user_id);
}

