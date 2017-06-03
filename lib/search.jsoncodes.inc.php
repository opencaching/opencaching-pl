<?php

use Utils\Database\OcPdo;

/**
 * This script is used (can be loaded) by /search.php
 */

global $content, $dbcSearch, $lang;

$pdo = OcPdo::instance();

// Assuming $queryFilter is safe (as other search.* files seem to do).
$stmt = $pdo->query("
    select wp_oc
    from caches
    where cache_id in (".$queryFilter.")
", PDO::FETCH_COLUMN, 0);
$result = [];
foreach ($stmt as $wp_oc) {
    $result[] = $wp_oc;
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($result);
exit;
