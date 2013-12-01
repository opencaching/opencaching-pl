<?php
// mail('lza@tlen.pl', 'test from xamp', 'email do testów');
//require_once 'util.sec/geokrety/processGeokretyErrors.php';

require_once(__DIR__.'/lib/db.php');
$q = 'UPDATE `PowerTrail` SET `status`=1 WHERE `id` IN(1,2,3,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,31,32,33,34,35,36,38,40,41,42,43,47,48,49,50,51,53,54,55,56,57,59,60,61,62,63,64,65,66,67,68,69,70,72,73,75,76,77,78,81,82,83,85,86,87,88)';
$q2 = "DELETE FROM `PowerTrail_comments` WHERE `commentType` IN (3,4) AND `dbInsertDateTime` BETWEEN '2013-12-01 2:00:00' AND  NOW()";
$db=new dataBase;
$db->multiVariableQuery($q);
$db->multiVariableQuery($q2);

exit;
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
?>