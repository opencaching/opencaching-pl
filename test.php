<?php
require_once(__DIR__.'/lib/db.php');;
require_once __DIR__.'/powerTrail/powerTrailBase.php';

$user_id = addslashes($_REQUEST['u']);

$db = new dataBase;
print 'punkty: '. powerTrailBase::getUserPoints($user_id);
print '<br>Pt: '.powerTrailBase::getPoweTrailCompletedCountByUser($user_id);
$pointsEarnedForPlacedCaches = powerTrailBase::getOwnerPoints($user_id);


print '<pre>';
var_dump($pointsEarnedForPlacedCaches);

exit;


require_once 'powerTrail/powerTrailBase.php';
// powerTrailBase::writePromoPt4mainPage();	35	// powerTrailBase::writePromoPt4mainPage();
require_once 'util.sec/geokrety/processGeokretyErrors.php';	
require_once 'util.sec/geokrety/processGeokretyErrors.php';
$g = new processGeokretyErrors;	
$g->run();
?>