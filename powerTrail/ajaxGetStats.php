<?php
use Utils\Database\OcDb;

$rootpath = __DIR__.'/../';
require_once __DIR__.'/../lib/common.inc.php';

$db = OcDb::instance();

$query = 'SELECT user.username, `userId`
FROM `PowerTrail_comments` , user
WHERE `commentType` =2
AND `deleted` =0
AND `PowerTrail_comments`.`userId` = user.user_id
GROUP BY `userId` ';

$s = $db->simpleQuery($query);
$result = $db->dbResultFetchAll($s);

foreach ($result as $user) {
    $resArr[$user['userId']] = array (
        'username' => $user['username'],
        'userPoints' =>  powerTrailBase::getUserPoints($user['userId'])
    );
}

echo '<pre>';
print_r($resArr);

