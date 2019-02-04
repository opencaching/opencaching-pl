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
use lib\Objects\Coordinates\Coordinates;
use Utils\EventHandler\EventHandler;
use lib\Objects\User\User;

 class LogEntryController
{

    private $errors = array();

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
    public function loadLogs(GeoCache $cache, $includeDeletedLogs = false, $offset = 0, $limit = 0)
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
                'value' => OcDb::quoteLimit($limit),
                'data_type' => 'integer',
            ),
            'offset' => array(
                'value' => OcDb::quoteOffset($offset),
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

    public function loadLogsFromDb($geocacheId, $includeDeletedLogs = false, $offset = 0, $limit = 0, $logId = false)
    {
        $query = $this->generateGetLogsQuery($includeDeletedLogs, $logId);
        $params = array(
            'v1' => array(
                'value' => (integer) $geocacheId,
                'data_type' => 'integer',
            ),
            'v2' => array(
                'value' => OcDb::quoteLimit($limit),
                'data_type' => 'integer',
            ),
            'v3' => array(
                'value' => OcDb::quoteOffset($offset),
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
            `user`.role&".User::ROLE_OC_TEAM.">0 AS `admin`,
            `user`.`hidden_count` AS    `ukryte`,
            `user`.`founds_count` AS    `znalezione`,
            `user`.`notfounds_count` AS `nieznalezione`,
            `u2`.`username` AS `del_by_username`,
            `u2`.role&".User::ROLE_OC_TEAM.">0 AS `del_by_admin`,
            `u3`.`username` AS `edit_by_username`,
            `u3`.role&".User::ROLE_OC_TEAM.">0 AS `edit_by_admin`,
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
