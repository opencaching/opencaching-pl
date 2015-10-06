<?php

global $titled_cache_nr_found, $titled_cache_period_prefix;

require_once('./lib/common.inc.php');

if ( !isset( $_REQUEST[ 'CRON' ] ) )
    exit;

$dbc = new dataBase();

$start_date_alg = date("Y-m-d");
$date_alg = $start_date_alg;

    $queryS =" 
    select
    top.cacheId, top.cacheName, top.cacheRegion, ifnull( nrT.nrTinR, 0) nrTinR,
    top.RATE, top.ratio, 
    top.cRating, top.cFounds, top.cNrDays, top.cDateCrt
    
    from
    (
    SELECT caches.cache_id cacheId , caches.name cacheName, adm3 cacheRegion,
    user.user_id userId, user.username userName,
    
    round((r.rating/f.nr_founds) + DATEDIFF(caches.date_created, :1 )/5000,4) RATE, 
            round((r.rating/f.nr_founds), 4) ratio,
    
    r.rating cRating, f.nr_founds cFounds, caches.date_created cDateCrt,
    DATEDIFF(caches.date_created, :1 ) cNrDays
    
    FROM `caches`
    
    JOIN
    (
    SELECT lcaches.cache_id cid, count(*) rating
    FROM `caches` lcaches
    INNER JOIN `cache_logs` ON `cache_logs`.`cache_id` = lcaches .`cache_id`
    JOIN cache_rating ON `cache_rating`.`cache_id` = `cache_logs`.`cache_id`
    AND `cache_rating`.`user_id` = `cache_logs`.user_id
    where
    `cache_logs`.`deleted` =0 AND `cache_logs`.`type` =1
    and cache_logs.date_created < :1
    
    group by 1
    )
    as r ON r.cid = caches.cache_id
    
    JOIN
    (
    SELECT fcaches.cache_id cid, count(*) nr_founds
    FROM
    caches fcaches
    JOIN cache_logs ON cache_logs.cache_id = fcaches.cache_id
    
    where
    cache_logs.deleted=0 AND cache_logs.type=1
    and cache_logs.date_created < :1
    
    group by 1
    )
    as f ON f.cid = caches.cache_id
    
    JOIN user ON `caches`.`user_id` = `user`.`user_id`
    JOIN `cache_location` ON `caches`.`cache_id` = `cache_location`.`cache_id`
    left JOIN cache_titled ON cache_titled.cache_id = caches.cache_id
    
    WHERE
    `status` =1
    AND `caches`.`type` <>4 AND `caches`.`type` <>5 AND caches.type <>6
    and f.nr_founds >= :2 and caches.date_created < :1
    and cache_titled.cache_id is NULL
    
    ORDER BY RATE DESC, founds DESC, caches.date_created DESC
    LIMIT 10) as top
    
    left join
    (
    select adm3 cacheRegion, count(*) nrTinR from cache_titled
    JOIN cache_location ON cache_titled.cache_id = cache_location.cache_id
    group by adm3
    ) as nrT on top.cacheRegion = nrT.cacheRegion
    order by nrTinR, cFounds DESC, cDateCrt, RATE DESC
    ";
    
    $dbc->multiVariableQuery($queryS, $date_alg, $titled_cache_nr_found );
    $rec = $dbc->dbResultFetch();
    
    
    $queryL = "
    SELECT i.id logId
    FROM
    (select cache_logs.id, cache_logs.cache_id from
        cache_logs
        where
        cache_logs.cache_id = :1 and
        cache_logs.id = 
            (select id from cache_logs cl
            JOIN cache_rating ON `cache_rating`.`cache_id` = cl.`cache_id`
            AND `cache_rating`.`user_id` = cl.user_id
            where cl.cache_id = cache_logs.cache_id
            ORDER BY length(cl.text) DESC LIMIT 1 ) 
    ) as i";
    
    $dbc->multiVariableQuery($queryL, $rec[ "cacheId" ] );
    $recL = $dbc->dbResultFetch();
    
    $queryI = "INSERT INTO cache_titled 
        (cache_id, rate, ratio, rating, found, days, date_alg, log_id) 
        VALUES (:1, :2, :3, :4, :5, :6, :7, :8)";
    
    $dbc->multiVariableQuery($queryI, $rec[ "cacheId" ], $rec[ "RATE" ], $rec[ "ratio" ],
            $rec[ "cRating" ], $rec[ "cFounds" ], $rec[ "cNrDays" ], $date_alg, $recL["logId"] );

    
    $queryLogI = 
    "INSERT INTO cache_logs 
            (cache_id, user_id, type, date, 
            text, text_html, text_htmledit, last_modified , okapi_syncbase, uuid, picturescount, mp3count, 
            date_created, owner_notified, node, deleted, encrypt, 
            del_by_user_id, last_deleted, edit_by_user_id, edit_count )
    VALUES ( :1, :2, :3, :4, :5, :6, :7, :8 , :9 , :10, :11, :12, :13, :14, :15, '0', '0', NULL , NULL , NULL , '0' )";
    
    $SystemUser = -1;
    $LogType = 12; //OCTeam
    $ntitled_cache = $titled_cache_period_prefix.'_titled_cache_congratulations';
    $msgText = tr($ntitled_cache);
    $LogUuid = create_uuid();
    
    $dbc->multiVariableQuery($queryLogI, $rec[ "cacheId" ], $SystemUser, $LogType, $date_alg,
            $msgText, '1', '1', $date_alg, $date_alg, $LogUuid, '0', '0',
            $date_alg, '0', $oc_nodeid );
        
unset($dbc);

?>
