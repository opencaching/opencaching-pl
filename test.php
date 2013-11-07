<?php
/*
require_once(__DIR__.'/lib/db.php');
$ptId = addslashes($_REQUEST['id']);

$q1 = 'SELECT `PowerTrail_actionsLog` . * , `user`.`username` FROM `PowerTrail_actionsLog` , `user` WHERE `PowerTrail_actionsLog`.`userId` = `user`.`user_id` AND `PowerTrailId` =:1 ORDER BY actionDateTime ASC ';
$q2 = 'SELECT * FROM `PowerTrail` WHERE `id` =:1'; 
$q3 = 'SELECT * FROM `PowerTrail_owners` WHERE `PowerTrailId` = :1';

$db=new dataBase;
$db->multiVariableQuery($q1, $ptId);
$r1 = $db->dbResultFetchAll();
$db->multiVariableQuery($q2, $ptId);
$r2 = $db->dbResultFetchAll();
$db->multiVariableQuery($q3, $ptId);
$r3 = $db->dbResultFetchAll();

print '<pre>';
print '$r1<br>';
print_r($r1);
print '<hr>$r2<br>';
print_r($r2);
print '<hr>$r3<br>';
print_r($r3);
print '</pre><hr>';

print serialize(array($r1,$r2,$r3));
exit;
/*
require_once(__DIR__.'/lib/db.php');
require_once __DIR__.'/powerTrail/powerTrailBase.php';

$user_id = addslashes($_REQUEST['u']);

$db = new dataBase;
print 'punkty: '. powerTrailBase::getUserPoints($user_id);
print '<br>Pt: '.powerTrailBase::getPoweTrailCompletedCountByUser($user_id);
$pointsEarnedForPlacedCaches = powerTrailBase::getOwnerPoints($user_id);


print '<pre>';
var_dump($pointsEarnedForPlacedCaches);

exit;
*/

require_once 'util.sec/geokrety/processGeokretyErrors.php';
$g = new processGeokretyErrors;	
$g->run();
?>