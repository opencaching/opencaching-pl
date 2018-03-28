<?php

use lib\Objects\GeoCache\GeoCache;
use Utils\Database\OcDb;

$rootpath = __DIR__ . '/../';
require_once __DIR__ . '/../lib/common.inc.php';


/**
 * This script allow repair of altitude of caches
 */

if (php_sapi_name() != "cli") {
    printf("This script should be run from command-line only.\n");
    exit(1);
}

if (!isset($argv[1]) || !isset($argv[2])) {
    echo "Usage: php repairGeocacheAltitude <cacheId-to-begin> <number-of-caches-to-repair>\n";
    exit(1);
}

$cacheIdToStart = $argv[1];
$cachesToRepair = $argv[2];


echo "Find set of caches to update\n";

$db = OcDb::instance();

list($limit, $offset) = $db->quoteLimitOffset($cachesToRepair);

// find caches-id to repair
$rs = $db->multiVariableQuery(
    "SELECT cache_id FROM caches_additions
    WHERE cache_id > :1
        AND altitude = 0
    LIMIT $limit", $cacheIdToStart);

$cacheIds = $db->dbFetchAllAsObjects($rs, function($row){
    return $row['cache_id'];
});

echo "Lets start altitude repair\n";

foreach($cacheIds as $cacheId){

    $geocache = GeoCache::fromCacheIdFactory($cacheId);

    echo " - repair for geocache id=".$geocache->getCacheId()." old=".$geocache->getAltitude();
    $geocache->updateAltitude();
    echo " new=".$geocache->getAltitude()."</br/>\n";
}

echo "Done!\n";
