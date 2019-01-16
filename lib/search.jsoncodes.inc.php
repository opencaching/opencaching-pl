<?php

/*
 * This is a "plugin" for search.php. It is supposed to be executed (i.e.
 * "included") by search.php only.
 *
 * Since search.php is creating temporary MySQL tables, it is important for
 * this script to reuse search.php's MySQL session. That's why we're using
 * $dbcSearch object for making queries here. More on this here:
 * https://github.com/opencaching/opencaching-pl/issues/1039
 */

global $content, $dbcSearch;

$rs = $dbcSearch->simpleQuery("
    select wp_oc
    from caches
    where cache_id in (".$queryFilter.")
");
$result = [];
foreach ($dbcSearch->dbResultFetchAll($rs) as &$row_ref) {
    $result[] = $row_ref['wp_oc'];
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($result);
exit;
