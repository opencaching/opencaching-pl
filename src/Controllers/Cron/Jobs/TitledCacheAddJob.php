<?php

use src\Controllers\Cron\Jobs\Job;
use src\Models\GeoCache\GeoCacheLog;
use src\Utils\Generators\Uuid;
use src\Controllers\MeritBadgeController;
use src\Models\OcConfig\OcConfig;

class TitledCacheAddJob extends Job
{
    public function mayRunNow()
    {
        return $this->isDue();
    }

    public function run()
    {
        global $titled_cache_nr_found, $titled_cache_period_prefix;

        $daysSinceLastTitle = $this->db->simpleQueryValue(
            "SELECT DATEDIFF(NOW(), MAX(date_alg)) FROM cache_titled",
            999
        );

        if ($titled_cache_period_prefix == "week") {
            $securityPeriod = 7;
        } elseif ($titled_cache_period_prefix == "month") {
            $securityPeriod = 28;
        } else {
            $securityPeriod = 0;
        }

        if ($daysSinceLastTitle < $securityPeriod) {
            return
                'Titled cache security period underrun ('.
                $daysSinceLastTitle.' vs '.$securityPeriod.' days)';
        }
        unset($daysSinceLastTitle);
        unset($securityPeriod);

        $date_alg = date("Y-m-d");

        $queryS = "
            SELECT
                top.cacheId, top.cacheName, top.userName,
                top.cacheRegion, IFNULL(nrT.nrTinR, 0) nrTinR,
                top.RATE, top.ratio,
                top.cRating, top.cFounds, top.cNrDays, top.cDateCrt

            FROM
            (
                SELECT
                    caches.cache_id cacheId , caches.name cacheName, adm3 cacheRegion,
                    user.user_id userId, user.username userName,

                    ROUND((r.rating/f.nr_founds) + DATEDIFF(caches.date_created, :1 )/5000,4) RATE,
                    ROUND((r.rating/f.nr_founds), 4) ratio,

                    r.rating cRating, f.nr_founds cFounds, caches.date_created cDateCrt,
                    DATEDIFF(caches.date_created, :1 ) cNrDays

                FROM caches

                JOIN
                (
                    SELECT lcaches.cache_id cid, COUNT(*) rating
                    FROM caches lcaches
                    INNER JOIN cache_logs ON cache_logs.cache_id = lcaches.cache_id
                    JOIN cache_rating
                        ON cache_rating.cache_id = cache_logs.cache_id
                        AND cache_rating.user_id = cache_logs.user_id
                    WHERE
                        cache_logs.deleted = 0 AND cache_logs.type = 1
                        AND cache_logs.date_created < :1
                    GROUP BY 1
                )
                AS r ON r.cid = caches.cache_id

                JOIN
                (
                    SELECT fcaches.cache_id cid, COUNT(*) nr_founds
                    FROM caches fcaches
                    JOIN cache_logs ON cache_logs.cache_id = fcaches.cache_id
                    WHERE
                        cache_logs.deleted=0 AND cache_logs.type = 1
                        AND cache_logs.date_created < :1
                    GROUP BY 1
                )
                AS f ON f.cid = caches.cache_id

                JOIN user ON caches.user_id = user.user_id
                JOIN cache_location ON caches.cache_id = cache_location.cache_id
                LEFT JOIN cache_titled ON cache_titled.cache_id = caches.cache_id

                WHERE
                    status = 1
                    AND caches.type <> 4 AND caches.type <> 5 AND caches.type <> 6
                    AND f.nr_founds >= :2 and caches.date_created < :1
                    AND cache_titled.cache_id is NULL

                ORDER BY RATE DESC, founds DESC, caches.date_created DESC
                LIMIT 35
            ) AS top

            LEFT JOIN
            (
                SELECT adm3 cacheRegion, COUNT(*) nrTinR
                FROM cache_titled
                JOIN cache_location ON cache_titled.cache_id = cache_location.cache_id
                GROUP BY adm3
            ) AS nrT ON top.cacheRegion = nrT.cacheRegion
            ORDER BY nrTinR, cFounds DESC, cDateCrt, RATE DESC
        ";

        $s = $this->db->multiVariableQuery($queryS, $date_alg, $titled_cache_nr_found );
        $rec = $this->db->dbResultFetch($s);

        if (!$rec) {
            // No cache matches the criteria.
            return;
        }

        $queryL = "
            SELECT i.id logId
            FROM
            (
                SELECT cache_logs.id, cache_logs.cache_id
                FROM cache_logs
                WHERE
                    cache_logs.cache_id = :1
                    AND cache_logs.id = (
                        SELECT id
                        FROM cache_logs cl
                        JOIN cache_rating ON `cache_rating`.`cache_id` = cl.`cache_id`
                        AND `cache_rating`.`user_id` = cl.user_id
                        WHERE cl.cache_id = cache_logs.cache_id
                        ORDER BY length(cl.text) DESC
                        LIMIT 1
                    )
            ) AS i";

        $s = $this->db->multiVariableQuery($queryL, $rec['cacheId']);
        $recL = $this->db->dbResultFetchOneRowOnly($s);

        $queryI = "
            INSERT INTO cache_titled
                (cache_id, rate, ratio, rating, found, days, date_alg, log_id)
            VALUES (:1, :2, :3, :4, :5, :6, :7, :8)";

        $this->db->multiVariableQuery($queryI, $rec[ "cacheId" ], $rec[ "RATE" ], $rec[ "ratio" ],
                $rec[ "cRating" ], $rec[ "cFounds" ], $rec[ "cNrDays" ], $date_alg, $recL["logId"] );

        $SystemUser = -1;
        $LogType = GeoCacheLog::LOGTYPE_ADMINNOTE;
        $ntitled_cache = $titled_cache_period_prefix.'_titled_cache_congratulations';
        $msgText = str_replace('{ownerName}', htmlspecialchars($rec['userName']), tr($ntitled_cache));
        $LogUuid = Uuid::create();

        $this->db->multiVariableQuery("
            INSERT INTO cache_logs (
                cache_id, user_id, type, date, text,
                text_html, last_modified, okapi_syncbase, uuid, picturescount,
                mp3count, date_created, owner_notified, node, deleted,
                del_by_user_id, last_deleted, edit_by_user_id, edit_count
            )
            VALUES (
                :1, :2, :3, :4, :5,
                :6, :7, :8, :9, :10,
                :11, :12, :13, :14,
                '0', NULL, NULL, NULL , '0'
            )", $rec['cacheId'], $SystemUser, $LogType, $date_alg,
            $msgText, '2', $date_alg, $date_alg, $LogUuid, '0', '0',
            $date_alg, '0', OcConfig::getSiteNodeId()
        );

        $ctrlMeritBadge = new MeritBadgeController;
        $ctrlMeritBadge->updateTriggerByNewTitledCache($rec['cacheId']);
    }
}
