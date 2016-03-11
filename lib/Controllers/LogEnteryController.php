<?php

namespace lib\Controllers;

use lib\Objects\GeoCache\GeoCacheLog;
use lib\Objects\OcConfig\OcConfig;

class LogEnteryController
{

	private $errors = array();

	public function removeLogById($logId, $request = null)
	{
		$log = $this->buildLog($logId);
		return $this->removeLog($log, $request);
	}

	public function removeLog(GeoCacheLog $log, $request = null)
	{
		$result = false;
		if ($log === false) {
			$this->errors[] = 'No such log';
			return false;
		}
		if ($log->getNode() != OcConfig::Instance()->getOcNodeId()) {
			$this->errors[] = 'Wrong Node';
			return false;
		}

		$loggedUser = \lib\Objects\ApplicationContainer::Instance()->getLoggedUser();

		if($loggedUser === false){
			$this->errors[] = 'User is not looged-in';
			return false;
		}

		if (( $log->getUser()->getUserId() == $loggedUser->getUserId()) || ($log->getGeoCache()->getOwner()->getUserId() == $loggedUser->getUserId()) || $loggedUser->getIsAdmin()) {
			EmailController::sendRemoveLogNotification($log, $request, $loggedUser);
			$updateQuery = "UPDATE `cache_logs` SET deleted = 1, `del_by_user_id` = :1 , `last_modified`=NOW(), `last_deleted`=NOW() WHERE `cache_logs`.`id`=:2 LIMIT 1";
			$db = \lib\Database\DataBaseSingleton::Instance();
			$db->multiVariableQuery($updateQuery, $loggedUser->getUserId(), $log->getId());
			$log->getUser()->recalculateAndUpdateStats();

			if ($log->getType() == GeoCacheLog::LOGTYPE_MOVED) {
				$this->handleMobileGeocachesAfterLogDelete($log);
			}

			if ($log->getType() == GeoCacheLog::LOGTYPE_FOUNDIT || $log->getType() == GeoCacheLog::LOGTYPE_ATTENDED) {
				$this->cacheScoreHandlingAfterRemoveLog($log);
			}

			//call eventhandler
			require_once(__DIR__ . '/../eventhandler.inc.php');
            event_remove_log($log->getGeoCache()->getCacheId(), $loggedUser->getUserId());

			$this->updateGeocacheAfterLogRemove($log, $db);
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
		$db = \lib\Database\DataBaseSingleton::Instance();
		$logQuery = "SELECT * FROM `cache_logs` WHERE `cache_logs`.`id` = :1 LIMIT 1";
		$db->multiVariableQuery($logQuery, $logId);
		$logRow = $db->dbResultFetchOneRowOnly();

		$geoCacheLog = false;
		if ($logRow) {
			$geoCacheLog = new GeoCacheLog();
			$geoCacheLog
					->setGeoCache($logRow['cache_id'])
					->setDate(new \DateTime($logRow['date']))
					->setDateCreated(new \DateTime($logRow['date_created']))
					->setDelByUserId($logRow['del_by_user_id'])
					->setDeleted($logRow['deleted'])
					->setEditByUserId($logRow['edit_by_user_id'])
					->setEditCount($logRow['edit_count'])
					->setEncrypt($logRow['encrypt'])
					->setLastDeleted($logRow['last_deleted'])
					->setLastModified(new \DateTime($logRow['last_modified']))
					->setId($logRow['id'])
					->setMp3count($logRow['mp3count'])
					->setNode($logRow['node'])
					->setOkapiSyncbase(new \DateTime($logRow['okapi_syncbase']))
					->setOwnerNotified($logRow['owner_notified'])
					->setPicturesCount($logRow['picturescount'])
					->setText($logRow['text'])
					->setTextHtml($logRow['text_html'])
					->setTextHtmlEdit($logRow['text_htmledit'])
					->setType($logRow['type'])
					->setUser($logRow['user_id'])
					->setUuid($logRow['uuid'])
			;
		}
		return $geoCacheLog;
	}

	private function cacheScoreHandlingAfterRemoveLog(GeoCacheLog $log)
	{
		$db = \lib\Database\DataBaseSingleton::Instance();

		// remove cache from users top caches, because the found log was deleted for some reason
		$query = "DELETE FROM `cache_rating` WHERE `user_id` = :1 AND `cache_id` = :2 ";
		$db->multiVariableQuery($query, $log->getUser()->getUserId(), $log->getGeoCache()->getCacheId());

		// Notify OKAPI's replicate module of the change.
		// Details: https://github.com/opencaching/okapi/issues/265
		require_once(__DIR__ . '/../../okapi/facade.php');
		\okapi\Facade::schedule_user_entries_check($log->getGeoCache()->getCacheId(), $log->getUser()->getUserId());
		\okapi\Facade::disable_error_handling();

		// recalc scores for this cache
		$queryDel = "DELETE FROM `scores` WHERE `user_id` = :1 AND `cache_id` = :2 ";
		$db->multiVariableQuery($queryDel, $log->getUser()->getUserId(), $log->getGeoCache()->getCacheId());

		$sqlQuery = "SELECT count(*) FROM scores WHERE cache_id= :1 ";
		$liczba = $db->multiVariableQueryValue($sqlQuery,0, $log->getGeoCache()->getCacheId());

		$sqlQuerySel = "SELECT SUM(score) FROM scores WHERE cache_id= :1 ";
		$suma = $db->multiVariableQueryValue($sqlQuerySel, 0, $log->getGeoCache()->getCacheId());

		// obliczenie nowej sredniej
		if ($liczba != 0) {
			$srednia = $suma / $liczba;
		} else {
			$srednia = 0;
		}

		$sqlUpdateQuery = "UPDATE caches SET votes = :1 , score= :2 WHERE cache_id= :3 ";
		$db->multiVariableQuery($sqlUpdateQuery, $liczba, $srednia, $log->getGeoCache()->getCacheId());
	}

	private function handleMobileGeocachesAfterLogDelete(GeoCacheLog $log)
	{
		$db = \lib\Database\DataBaseSingleton::Instance();
		$checkcmlQuery = "SELECT `latitude`,`longitude`,`id` FROM `cache_moved` WHERE `log_id`= :1";
		$db->multiVariableQuery($checkcmlQuery, $log->getId);
		if ($db->rowCount() != 0) {
			$xy_log = $db->dbResultFetchOneRowOnly();
			$geoCache = $log->getGeoCache();
			if ($geoCache->getLatitude() == $xy_log['latitude'] && $geoCache->getLongitude() == $xy_log['longitude']) {
				$delQuery = "DELETE FROM `cache_moved` WHERE `log_id`=:1 LIMIT 1";
				$db->multiVariableQuery($delQuery, $log->getId());

				$getxyQuery = "SELECT `latitude`,`longitude` FROM `cache_moved` WHERE `cache_id`='&1' ORDER BY `date` DESC LIMIT 1";
				$db->multiVariableQuery($getxyQuery, $geoCache->getCacheId());
				$old_xy = $db->dbResultFetchOneRowOnly();
				if (($old_xy['longitude'] != '') && ($old_xy['latitude'] != '')) {
					$updateQuery = "UPDATE `caches` SET `last_modified`=NOW(), `longitude`=:1', `latitude`=:2 WHERE `cache_id`=:3";
					$db->multiVariableQuery($updateQuery, $old_xy['longitude'], $old_xy['latitude'], $geoCache->getCacheId());
				}
			} else {
				$delQuery = "DELETE FROM `cache_moved` WHERE `log_id`=:1 LIMIT 1";
				$db->multiVariableQuery($delQuery, $log->getId());
			}
		}
	}

	private function updateGeocacheAfterLogRemove(GeoCacheLog $log, \dataBase $db)
	{
		$geoCache = $log->getGeoCache();
		if ($log->getType() == GeoCacheLog::LOGTYPE_FOUNDIT || $log->getType() == GeoCacheLog::LOGTYPE_ATTENDED) {
			$geoCache->setFounds($geoCache->getFounds()-1);
		} elseif ($log->getType() == GeoCacheLog::LOGTYPE_DIDNOTFIND || $log->getType() == 8) {
			$geoCache->setNotFounds($geoCache->getNotFounds()-1);
		} elseif ($log->getType() == GeoCacheLog::LOGTYPE_COMMENT) {
			$geoCache->setNotesCount($geoCache->getNotesCount()-1);
		}

		//Update last found
		$lastfoundQuery = "SELECT MAX(`cache_logs`.`date`) AS `date` FROM `cache_logs` WHERE ((cache_logs.`type`=1) AND (cache_logs.`cache_id`= :1 ))";
		$db->multiVariableQuery($lastfoundQuery, $geoCache->getCacheId());
		$lastfoundRecord = $db->dbResultFetchOneRowOnly();
		if ($lastfoundRecord['date'] === NULL) {
			$lastFound = null;
		} else {
			$lastFound = $lastfoundRecord['date'];
		}
		$geoCache->setLastFound($lastFound)->updateGeocacheLogenteriesStats();
	}

	public function getErrors()
	{
		return $this->errors;
	}

    public function loadLogsFromDb($geocacheId, $includeDeletedLogs = false, $offset = 0, $limit = -1, $logId = false)
    {
        $sql = $this->generateGetLogsQuery($includeDeletedLogs, $logId);
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
        $db = \lib\Database\DataBaseSingleton::Instance();
        $db->paramQuery($sql, $params);
        $logEnteries = $db->dbResultFetchAll();

        return $logEnteries;
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
