<?php


//use lib\Objects\MeritBadge\MeritBadge; //for static functions
//use lib\Controllers\MeritBadgeController;
use Utils\Database\OcDb;

require_once('./lib/common.inc.php');



if ($usr == false) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);
    exit;
}

$usrid = -1;

if (isset($_REQUEST['user_id'])) {
    $userid = $_REQUEST['user_id'];
} else {
    $userid = $usr['userid'];
}

$code = $_REQUEST['code'];

//$badge_id = $_REQUEST['badge_id'];

//$meritBadgeCtrl = new \lib\Controllers\MeritBadgeController;
//$userMeritBadge = $meritBadgeCtrl->buildUserBadge($userid, $badge_id);

// $condition = "nuts_layer.code = 'PL63' and caches.type<>8 and caches.type<>10 and caches.status = 1 and
// ST_Contains(shape, GeomFromText( concat( 'POINT(', caches.longitude, ' ', caches.latitude, ')')))";

$condition=" cache_location.code3 = 'PL$code' and
caches.type<>8 and caches.type<>10 and caches.status = 1";


//oPomorskie
$cacheQuery = "SELECT caches.cache_id FROM cache_location
join caches on caches.cache_id = cache_location.cache_id
WHERE " . $condition;

// $cacheQuery = "SELECT caches.cache_id FROM nuts_layer, caches
// WHERE " . $condition;



$borderQuery = "SELECT MAX(caches.longitude) AS maxlongitude, MAX(caches.latitude) AS maxlatitude,
MIN(caches.longitude) AS minlongitude, MIN(caches.latitude) AS minlatitude
FROM cache_location
join caches on caches.cache_id = cache_location.cache_id
WHERE " . $condition;

// $borderQuery = "SELECT MAX(caches.longitude) AS maxlongitude, MAX(caches.latitude) AS maxlatitude,
// MIN(caches.longitude) AS minlongitude, MIN(caches.latitude) AS minlatitude
// FROM nuts_layer, caches
// WHERE " . $condition;


$db = OcDb::instance();
$db->setDebug(true);

$stmt= $db->simpleQuery($borderQuery);
$r = $db->dbResultFetchOneRowOnly($stmt);
$minlat = $r['minlatitude'];
$minlon = $r['minlongitude'];
$maxlat = $r['maxlatitude'];
$maxlon = $r['maxlongitude'];


$stmt = $db->simpleQuery($cacheQuery);
$hash = uniqid();
$f = fopen($dynbasepath . "searchdata/" . $hash, "w");
while ($r = $db->dbResultFetch($stmt)) {
    fprintf($f, "%s\n", $r['cache_id']);
}
fclose($f);

tpl_redirect("cachemap3.php?searchdata=" . $hash . "&fromlat=" . $minlat . "&fromlon=" . $minlon . "&tolat=" . $maxlat . "&tolon=" . $maxlon);

?>
