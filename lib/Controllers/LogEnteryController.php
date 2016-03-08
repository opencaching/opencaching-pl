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


}
