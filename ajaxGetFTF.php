<?php
require_once __DIR__.'/lib/ClassPathDictionary.php';

$userId = (int) $_REQUEST['id'];
$database = \lib\Database\DataBaseSingleton::Instance();
$ftfQuery = 'SELECT clftf.cache_id, caches.name, clftf.date
FROM (
    SELECT cache_logs.cache_id, MIN(cache_logs.date) AS date, cache_logs.user_id
    FROM cache_logs INNER JOIN (
        SELECT DISTINCT cache_id FROM cache_logs WHERE deleted = 0 AND type = 1 AND user_id = :1
    ) cl_u ON cache_logs.cache_id = cl_u.cache_id
    WHERE cache_logs.deleted = 0 AND cache_logs.type = 1
    GROUP BY cache_logs.cache_id) AS clftf INNER JOIN caches ON clftf.cache_id = caches.cache_id
WHERE clftf.user_id = :1
ORDER BY clftf.date';

$database->multiVariableQuery($ftfQuery, $userId);
$ftfResult = $database->dbResultFetchAll();

print json_encode($ftfResult);

