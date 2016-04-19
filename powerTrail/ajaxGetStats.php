<?php
$rootpath = __DIR__.'/../';
require_once __DIR__.'/../lib/common.inc.php';


$db = \lib\Database\DataBaseSingleton::Instance();
$query = 'SELECT user.username, `userId`
FROM `PowerTrail_comments` , user
WHERE `commentType` =2
AND `deleted` =0
AND `PowerTrail_comments`.`userId` = user.user_id
GROUP BY `userId` ';

$db->simpleQuery($query);
$result = $db->dbResultFetchAll();

foreach ($result as $user) {
    $resArr[$user['userId']] = array (
        'username' => $user['username'],
        'userPoints' =>  powerTrailBase::getUserPoints($user['userId'])
    );
}

echo '<pre>';
print_r($resArr);

?>