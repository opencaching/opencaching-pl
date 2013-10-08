<?php
$q2 = "UPDATE  `PowerTrail_actionsLog` SET  `actionType` =1,
`description` =  'create new Power Trail' WHERE  `actionType` =2 AND  `cacheId` =0 AND  `description` =  'create new Power Trail'";


$q=" SELECT PowerTrailId, userId, description 
FROM  `PowerTrail_actionsLog` 
WHERE  `actionType` =2
AND  `cacheId` =0
AND  `description` =  'create new Power Trail'";

$q3 = "SELECT * 
FROM  `PowerTrail_actionsLog` 
WHERE  `actionType` =1";

require_once __DIR__.'/lib/db.php';
$db = new dataBase;
$db->multiVariableQuery($q2);
unset($db);
$db = new dataBase;
$db->multiVariableQuery($q);

$result = $db->dbResultFetchAll();
print '<pre> ';
print_r($result);

$db->multiVariableQuery($q3);
$result = $db->dbResultFetchAll();
print 'po: <br> ';
print_r($result);

exit;

require_once 'powerTrail/powerTrailBase.php';
// powerTrailBase::writePromoPt4mainPage();
require_once 'util.sec/geokrety/processGeokretyErrors.php';
$g = new processGeokretyErrors;
$g->run();

?>