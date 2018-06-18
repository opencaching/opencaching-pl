<?php

namespace lib\Controllers;

use lib\Objects\GeoCache\GeoCacheLog;
use lib\Objects\OcConfig\OcConfig;
use Utils\Database\OcDb;
use Utils\Email\EmailSender;
use Utils\Gis\Gis;
use lib\Objects\ApplicationContainer;
use lib\Controllers\MeritBadgeController;
use lib\Objects\GeoCache\GeoCache;
use okapi\Facade;
use lib\Objects\Coordinates\Coordinates;
use Utils\EventHandler\EventHandler;

class LogEntryController
{

    private $errors = array();

    public function removeLogById($logId, $request = null)
    {
        $log = $this->buildLog($logId);
        return $this->removeLog($log, $request);
    }

    public function removeLog(GeoCacheLog $log, $request = null)
    {
        global $config;

        $result = false;
        if ($log === false) {
            $this->errors[] = 'No such log';
            return false;
        }
        if ($log->getNode() != OcConfig::instance()->getOcNodeId()) {
            $this->errors[] = 'Wrong Node';
            return false;
        }

        $loggedUser = ApplicationContainer::Instance()->getLoggedUser();

        if($loggedUser === null){
            $this->errors[] = 'User is not looged-in';
            return false;
        }

        if (( $log->getUser()->getUserId() === $loggedUser->getUserId()) || ($log->getGeoCache()->getOwner()->getUserId() == $loggedUser->getUserId()) || $loggedUser->getIsAdmin()) {
            if($log->getUser()->getUserId() !== $loggedUser->getUserId()){
                EmailSender::sendRemoveLogNotification(__DIR__ . '/../../tpl/stdstyle/email/removed_log.email.html',
                    $log, $loggedUser);
            }
            $updateQuery = "UPDATE `cache_logs` SET deleted = 1, `del_by_user_id` = :1 , `last_modified`=NOW(), `last_deleted`=NOW() WHERE `cache_logs`.`id`=:2 LIMIT 1";
            $db = OcDb::instance();
            $db->multiVariableQuery($updateQuery, $loggedUser->getUserId(), $log->getId());
            $log->getUser()->recalculateAndUpdateStats();

            if ($log->getType() == GeoCacheLog::LOGTYPE_MOVED) {
                $this->handleMobileGeocachesAfterLogDelete($log);
            }

            if ($log->getType() == GeoCacheLog::LOGTYPE_FOUNDIT || $log->getType() == GeoCacheLog::LOGTYPE_ATTENDED) {
                $this->cacheScoreHandlingAfterRemoveLog($log);

                if ($config['meritBadges']){
                    $ctrlMeritBadge = new MeritBadgeController;
                    $ctrlMeritBadge->updateTriggerLogCache($log->getGeoCache()->getCacheId(), $loggedUser->getUserId() );
                    $ctrlMeritBadge->updateTriggerTitledCache($log->getGeoCache()->getCacheId(), $loggedUser->getUserId());
                    $ctrlMeritBadge->updateTriggerCacheAuthor($log->getGeoCache()->getCacheId());
                }
            }

            //call eventhandler
            EventHandler::logRemove($log);

            $log->getGeoCache()->recalculateCacheStats();
            $result = true;

        } else {
            $this->errors[] = 'User has no privileages to delete this log';
        }

        return $result;
    }

    /**
     *
     * @param integer $logId
     * @return GeoCacheLog
     */
    private function buildLog($logId)
    {
        $db = OcDb::instance();
        $logQuery = "SELECT * FROM `cache_logs` WHERE `cache_logs`.`id` = :1 LIMIT 1";
        $s = $db->multiVariableQuery($logQuery, $logId);
        $logRow = $db->dbResultFetchOneRowOnly($s);

        $geoCacheLog = false;
        if ($logRow) {
            $geoCacheLog = $this->buildLogFromDbRow($logRow);
        }
        return $geoCacheLog;
    }

    private function buildLogFromDbRow($row)
    {
        $log = new GeoCacheLog();
        $log
            ->setGeoCache($row['cache_id'])
            ->setDate(new \DateTime($row['date']))
            ->setDateCreated(new \DateTime($row['date_created']))
            ->setDelByUserId($row['del_by_user_id'])
            ->setDeleted($row['deleted'])
            ->setEditByUserId($row['edit_by_user_id'])
            ->setEditCount($row['edit_count'])
            ->setLastDeleted($row['last_deleted'])
            ->setLastModified(new \DateTime($row['last_modified']))
            ->setId($row['id'])
            ->setMp3count($row['mp3count'])
            ->setNode($row['node'])
            ->setOkapiSyncbase(new \DateTime($row['okapi_syncbase']))
            ->setOwnerNotified($row['owner_notified'])
            ->setPicturesCount($row['picturescount'])
            ->setText($row['text'])
            ->setTextHtml($row['text_html'])
            ->setTextHtmlEdit($row['text_htmledit'])
            ->setType($row['type'])
            ->setUser($row['user_id'])
            ->setUuid($row['uuid']);
    return $log;
    }

    private function cacheScoreHandlingAfterRemoveLog(GeoCacheLog $log)
    {
        $db = OcDb::instance();

        // remove cache from users top caches, because the found log was deleted for some reason
        $query = "DELETE FROM `cache_rating` WHERE `user_id` = :1 AND `cache_id` = :2 ";
        $db->multiVariableQuery($query, $log->getUser()->getUserId(), $log->getGeoCache()->getCacheId());

        // Notify OKAPI's replicate module of the change.
        // Details: https://github.com/opencaching/okapi/issues/265
        Facade::schedule_user_entries_check($log->getGeoCache()->getCacheId(), $log->getUser()->getUserId());
        Facade::disable_error_handling();

        // recalc scores for this cache
        $queryDel = "DELETE FROM `scores` WHERE `user_id` = :1 AND `cache_id` = :2 ";
        $db->multiVariableQuery($queryDel, $log->getUser()->getUserId(), $log->getGeoCache()->getCacheId());

        $query = "SELECT count(*) FROM scores WHERE cache_id= :1 ";
        $liczba = $db->multiVariableQueryValue($query,0, $log->getGeoCache()->getCacheId());

        $querySel = "SELECT SUM(score) FROM scores WHERE cache_id= :1 ";
        $suma = $db->multiVariableQueryValue($querySel, 0, $log->getGeoCache()->getCacheId());

        // obliczenie nowej sredniej
        if ($liczba != 0) {
            $srednia = $suma / $liczba;
        } else {
            $srednia = 0;
        }

        $updateQuery = "UPDATE caches SET votes = :1 , score= :2 WHERE cache_id= :3 ";
        $db->multiVariableQuery($updateQuery, $liczba, $srednia, $log->getGeoCache()->getCacheId());
    }

    private function handleMobileGeocachesAfterLogDelete(GeoCacheLog $log)
    {
        $db = OcDb::instance();
        $delQuery = "DELETE FROM `cache_moved` WHERE `log_id`=:1 LIMIT 1";
        $db->multiVariableQuery($delQuery, $log->getId());
        self::recalculateMobileMoves($log->getGeoCache());
    }

    /**
     * Method recalculates all moves of geocache (in cache_moved table)
     * and updates cache coordinates and region from last move.
     *
     * You can safely remove cache_moved entry without any recalculations
     * and next call this method to recalculate all (distances, coords, regions)
     * You can also insert item into cache_moved without calculating distance
     * and without setting new cords and next call this method.
     * It also works while editing cache log.
     *
     * @param GeoCache $cache
     * @return boolean - true is set when cache_moved or cache was changed, false - otherwise
     */
    public static function recalculateMobileMoves(GeoCache $cache)
    {
        $db = OcDb::instance();
        $changed = false;

        $query = "SELECT `id`, `user_id`, `latitude`,`longitude`, `km` FROM `cache_moved` WHERE `cache_id`= :1 ORDER BY `date` ASC";
        $stmt = $db->multiVariableQuery($query, $cache->getCacheId());
        $logMovedCount = $db->rowCount($stmt);
        if ($logMovedCount == 0) { // Nothing to do. There are no cache_moved entries, we also cannot check cache coords
            return $changed;
        }

        // Step 1 - ensure, that first log has distance 0km
        $logMoved = $db->dbResultFetch($stmt);
        if ($logMoved['km'] != '0') {
            $db->multiVariableQuery("UPDATE `cache_moved` SET `km` = 0 WHERE `id` = :1", $logMoved['id']);
            $changed = true;
        }

        // Step 2 - recalculate cache_moved distances
        if ($logMovedCount > 1) {
            while ($newLogMoved = $db->dbResultFetch($stmt)) {
                $distance = Gis::distance($logMoved['latitude'], $logMoved['longitude'], $newLogMoved['latitude'], $newLogMoved['longitude']);
                $distance = round($distance, 2);
                if ($distance != $newLogMoved['km']) { // save corrected distance in DB
                    $db->multiVariableQuery("UPDATE `cache_moved` SET `km` = :1 WHERE `id` = :2", floatval($distance), $newLogMoved['id']);
                    Facade::schedule_user_entries_check($cache->getCacheId(), $newLogMoved['user_id']);
                    Facade::disable_error_handling();
                    $changed = true;
                }
                $logMoved = $newLogMoved;
            }
        }

        // Step 3 - set correct cache coordinates based on last cache_moved log
        $newCoords = Coordinates::FromCoordsFactory( $logMoved['latitude'], $logMoved['longitude'] );

        if(!$cache->getCoordinates()->areSameAs($newCoords)){

            $cache->updateCoordinates($newCoords);
            $changed = true;
        }

        return $changed;
    }

    /**
     * Method is similar to recalculateMobileMoves(), but param is cacheId, not GeoCache object
     *
     * @param int $cacheId
     * @return boolean - true is set when cache_moved or cache was changed, false - otherwise
     */
    public static function recalculateMobileMovesByCacheId($cacheId)
    {
        $cache = new GeoCache(array('cacheId' => $cacheId));
        return self::recalculateMobileMoves($cache);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Returns array of GeoCacheLogs objects - newest logs meeting criteria given as parameters
     *
     * @param GeoCache $cache
     * @param bool $includeDeletedLogs
     * @param int $offset
     * @param int $limit
     * @return \lib\Objects\GeoCache\GeoCacheLog[]
     */
    public function loadLogs(GeoCache $cache, $includeDeletedLogs = false, $offset = 0, $limit = -1)
    {
        $query = 'SELECT * FROM `cache_logs` WHERE `cache_logs`.`cache_id` = :cacheid';
        if (! $includeDeletedLogs) {
            $query .= ' AND `cache_logs`.`deleted` = 0';
        }
        $query .= ' ORDER BY `cache_logs`.`date` DESC
            LIMIT :limit OFFSET :offset';
        $params = array(
            'cacheid' => array(
                'value' => (integer) $cache->getCacheId(),
                'data_type' => 'integer',
            ),
            'limit' => array(
                'value' => (integer) $limit,
                'data_type' => 'integer',
            ),
            'offset' => array(
                'value' => (integer) $offset,
                'data_type' => 'integer',
            ),
        );
        $db = OcDb::instance();
        $stmt = $db->paramQuery($query, $params);
        $logs = $db->dbResultFetchAll($stmt);
        $result = array();
        foreach ($logs as $logitem) {
            $result[] = $this->buildLogFromDbRow($logitem);
        }
        return $result;
    }

    public function loadLogsFromDb($geocacheId, $includeDeletedLogs = false, $offset = 0, $limit = -1, $logId = false)
    {
        $query = $this->generateGetLogsQuery($includeDeletedLogs, $logId);
        $params = array(
            'v1' => array(
                'value' => (integer) $geocacheId,
                'data_type' => 'integer',
            ),
            'v2' => array(
                'value' => (integer) $limit,
                'data_type' => 'integer',
            ),
            'v3' => array(
                'value' => (integer) $offset,
                'data_type' => 'integer',
            ),
        );
        if($logId){
           $params['v4'] = array(
                'value' => (integer) $logId,
                'data_type' => 'integer',
           );
        }
        $db = OcDb::instance();

        //Test JG
        $s = $db->paramQuery($query, $params);
        //$s = $db->paramQuery($query, NULL);
        $logEntries = $db->dbResultFetchAll($s);

        return $logEntries;
    }

    private function generateGetLogsQuery($includeDeletedLogs, $logId)
    {
        if($includeDeletedLogs){
            $showDeletedLogsSql = '`cache_logs`.`deleted` `deleted`,';
            $showDeletedLogsSql2 = '';
        } else {
            $showDeletedLogsSql = '';
            $showDeletedLogsSql2 = ' AND `cache_logs`.`deleted` = 0 ';
        }
        if($logId){
            $showOneLogSql = " AND `cache_logs`.`id` =:v4 ";
        } else {
            $showOneLogSql = '';
        }
        return  "SELECT `cache_logs`.`user_id` `userid`, $showDeletedLogsSql
            `cache_logs`.`id` `logid`,
            `cache_logs`.`date` `date`,
            `cache_logs`.`type` `type`,
            `cache_logs`.`text` `text`,
            `cache_logs`.`text_html` `text_html`,
            `cache_logs`.`picturescount` `picturescount`,
            `cache_logs`.`mp3count` `mp3count`,
            `cache_logs`.`last_modified` AS `last_modified`,
            `cache_logs`.`last_deleted` AS `last_deleted`,
            `cache_logs`.`edit_count` AS `edit_count`,
            `cache_logs`.`date_created` AS `date_created`,
            `user`.`username` `username`,
            `user`.`user_id` `user_id`,
            `user`.`admin` `admin`,
            `user`.`hidden_count` AS    `ukryte`,
            `user`.`founds_count` AS    `znalezione`,
            `user`.`notfounds_count` AS `nieznalezione`,
            `u2`.`username` AS `del_by_username`,
            `u2`.`admin` AS `del_by_admin`,
            `u3`.`username` AS `edit_by_username`,
            `u3`.`admin` AS `edit_by_admin`,
            `log_types`.`icon_small` `icon_small`,
            `cache_moved`.`longitude` AS `mobile_longitude`,
            `cache_moved`.`latitude` AS `mobile_latitude`,
            `cache_moved`.`km` AS `km`,

            IF(ISNULL(`cache_rating`.`cache_id`), 0, 1) AS `recommended`
            FROM `cache_logs` INNER JOIN `log_types` ON `cache_logs`.`type`=`log_types`.`id`

            INNER JOIN `user` ON `cache_logs`.`user_id`=`user`.`user_id`
            LEFT JOIN `cache_rating` ON `cache_logs`.`cache_id`=`cache_rating`.`cache_id` AND `cache_logs`.`user_id`=`cache_rating`.`user_id`
            LEFT JOIN `cache_moved` ON `cache_moved`.`log_id` = `cache_logs`.`id`
            LEFT JOIN `user` `u2` ON `cache_logs`.`del_by_user_id`=`u2`.`user_id`
            LEFT JOIN `user` `u3` ON `cache_logs`.`edit_by_user_id`=`u3`.`user_id`
            WHERE `cache_logs`.`cache_id`=:v1
                   $showDeletedLogsSql2 $showOneLogSql
            ORDER BY `cache_logs`.`date` DESC, `cache_logs`.`id` DESC
            LIMIT :v2 OFFSET :v3";
    }

}
