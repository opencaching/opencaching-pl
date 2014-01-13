<?php
require_once __DIR__.'/../lib/db.php';
require_once __DIR__.'/sendEmail.php';
/**
 * 
 */
class powerTrailBase{
		
	const powerTrailLogoFileName = 'powerTrailLogoId';
	const commentsPaginateCount = 5;
	const cCountForMaxMagnifier = 50;
	
	public static function minimumCacheCount(){
		include __DIR__.'/../lib/settings.inc.php';
		return $powerTrailMinimumCacheCount['current'];
	} 
	
	public static function historicMinimumCacheCount(){
		include __DIR__.'/../lib/settings.inc.php';
		$min = $powerTrailMinimumCacheCount['current'];
		foreach ($powerTrailMinimumCacheCount['old'] as $date){
			//var_dump($date['dateFrom'], $ptPublished, $date['dateTo']);
			if ($min > $date['limit']) $min = $date['limit'];
		}
		return $min; 
	}
	public static function userMinimumCacheFoundToSetNewPowerTrail(){
		include __DIR__.'/../lib/settings.inc.php';
		return $powerTrailUserMinimumCacheFoundToSetNewPowerTrail;
	} 
	
	public $logActionTypes = array (
		1 => array (
			'type' => 'create new Power Trail',
		),
		2 => array (
			'type' => 'attach cache to PowerTrail',
		),
		3 => array (
			'type' => 'remove cache from PowerTrail',
		),
		4 => array (
			'type' => 'add another owner to PowerTrail',
		),
		5 => array (
			'type' => 'remove owner from PowerTrail',
		),
		6 => array (
			'type' => 'change PowerTrail status',
		),
	);
	
	private $powerTrailTypes;
	
	function __construct() {
		//include __DIR__.'/../lib/settings.inc.php';
		//$this->userMinimumCacheFoundToSetNewPowerTrail = $userMinimumCacheFoundToSetNewPowerTrail;
	}
	
	/**
	 * check if user $userId is owner of $powerTrailId.
	 * @return 0 or 1
	 */
	public static function checkIfUserIsPowerTrailOwner($userId, $powerTrailId){
		$db = new dataBase;
		$query = 'SELECT count(*) AS `checkResult` FROM `PowerTrail_owners` WHERE `PowerTrailId` = :1 AND `userId` = :2' ;
		$db->multiVariableQuery($query, $powerTrailId, $userId);
		$result = $db->dbResultFetchAll();
		return $result[0]['checkResult'];
	}

	/**
	 * here power Trail types
	 */
	public static function getPowerTrailTypes(){
		return array (
			1 => array ( //sport (map shape)
				'translate' => 'pt004',
			),
			2 => array ( // touring
				'translate' => 'pt005',
			),
			3 => array ( // nature (?)
				'translate' => 'pt067',
			),
			4 => array ( // tematic (?)
				'translate' => 'pt079',
			),
			
		);
					
	}

	/**
	 * here power Trail status
	 */
	public static function getPowerTrailStatus(){
		return array (
			1 => array ( // public
				'translate' => 'pt006',
			),
			2 => array ( // not yet available
				'translate' => 'pt007',
			),
			4 => array ( // service
				'translate' => 'pt219',
			),
			3 => array ( // archived
				'translate' => 'pt212',
			),
			
		);
					
	}

	/**
	 * here comment types
	 */
	public static function getPowerTrailComments(){
		return array (
			1 => array ( //comment
				'translate' => 'pt056',
				'color' => '#000000',
			),
			2 => array ( // conquested
				'translate' => 'pt057',
				'color' => '#00CC00',
			),
			3 => array ( // geoPath Publishing
				'translate' => 'pt214',
				'color' => '#0000CC',
			),
			4 => array ( // geoPath temp. closed
				'translate' => 'pt216',
				'color' => '#CC0000',
			),
			5 => array ( // geoPath Closure (permanent)
				'translate' => 'pt213',
				'color' => '#CC0000',
			),
			

			
		);
					
	}
	
	public static function cacheSizePoints() {
		return array (
		2 => 2.5,	# Micro
		3 => 2,	# Small 
		4 => 1.5,	# Normal [from 1 to 3 litres]	
		5 => 1,	# Large [from 3 to 10 litres]	
		6 => 0.5,	# Very large [more than 10 litres]	
		7 => 0,	# Bez pojemnika
	);
	}	
	
	public static function cacheTypePoints() {
		return array (
			1 => 2, #Other
			2 => 2, #Trad.
			3 => 3, #Multi
			4 => 1, #Virt.
			5 => 0.2, #ICam.
			6 => 2.3, #Event
			7 => 4, #Quiz
			8 => 2, #Moving
			9 => 1, #podcast
			10 => 1, #own
		);
	}
	
	public static function checkUserConquestedPt($userId, $ptId){
		$db = new dataBase;
		$q = 'SELECT count(*) AS `c` FROM PowerTrail_comments WHERE userId = :1 AND	PowerTrailId = :2 AND `commentType` =2 AND deleted !=1 ';
		$db->multiVariableQuery($q, $userId, $ptId);
		$response = $db->dbResultFetch();
		return $response['c'];
	}
	
	public static function getPoweTrailCompletedCountByUser($userId) {
		$queryPt = "SELECT count(`PowerTrailId`) AS `ptCount` FROM `PowerTrail_comments` WHERE `commentType` =2 AND `deleted` =0 AND `userId` =:1";
		$db = new dataBase;
		$db->multiVariableQuery($queryPt, $userId);
		$ptCount = $db->dbResultFetch();
		return (int) $ptCount['ptCount'];
	}
	
	public static function getPowerTrailsCompletedByUser($userId){
		$queryPtList = 'SELECT `id`, `name`, `image`, `type` FROM `PowerTrail` WHERE `id` IN (SELECT `PowerTrailId` FROM `PowerTrail_comments` WHERE `commentType` =2 AND `deleted` =0 AND `userId` =:1)';
		$db = new dataBase;
		$db->multiVariableQuery($queryPtList, $userId);
		return $db->dbResultFetchAll();
	}
	
	public static function getUserPoints($userId) {
		$queryPt = "SELECT sum( `points` ) AS sum
					FROM powerTrail_caches
					WHERE `PowerTrailId` IN (
						SELECT `PowerTrailId` AS `ptCount`
						FROM `PowerTrail_comments`
						WHERE `commentType` =2
						AND `deleted` =0
						AND `userId` =:1
					)
					AND `cacheId` IN (
						SELECT `cache_id`
						FROM `cache_logs`
						WHERE `type` =1
						AND `user_id` =:1
					)";
		$db = new dataBase;
		$db->multiVariableQuery($queryPt, $userId);
		$points = $db->dbResultFetch();
		return round($points['sum'],2);
	}
	
	/**
	 * calculate magnifier used for counting points for placing caches of geoPath
	 * 
	 * math function y=ax+b
	 * where x1=1 y1=1 and x2=$w, y2=2
	 */
	public static function calculateMagnifier($x){
			$w = self::cCountForMaxMagnifier;
			$b = (2-$w)/(-$w+1);
			return (1-$b)*$x+$b;
		}
	
	public static function getOwnerPoints($userId){
		$query = 'SELECT 
					round(sum(`powerTrail_caches`.`points`),2) AS `pointsSum`, 
					count( `powerTrail_caches`.`cacheId` ) AS `cacheCount`, 
					`powerTrail_caches`.`PowerTrailId`, 
					`PowerTrail`.`name` 
				FROM
				 	`powerTrail_caches`, 
				 	`PowerTrail` 
				WHERE 
						`powerTrail_caches`.`cacheId` 
				IN ( SELECT `cache_id` FROM `caches` WHERE `user_id` =:1) 
				AND 	`PowerTrail`.`id` = `powerTrail_caches`.`PowerTrailId` 
				AND 	`PowerTrail`.`status` != 2 
				GROUP BY `PowerTrailId`';	
		$db = new dataBase;
		$db->multiVariableQuery($query, $userId);
		$points = $db->dbResultFetchAll();
		$totalPoint = 0;
		$geoPathCount = 0;
		$pointsDetails = array();
		foreach ($points as $ptPoints) {
			$magnifier = self::calculateMagnifier($ptPoints['cacheCount']);
			$earnedPoints = $ptPoints['pointsSum']*$magnifier;
			$pointsDetails[$ptPoints['PowerTrailId']] = array(
				'cacheCount' => $ptPoints['cacheCount'],
				'pointsSum' => $ptPoints['pointsSum'],
				'magnifier' => $magnifier,
				'pointsEarned' => $earnedPoints,
				'ptName' => $ptPoints['name'],
			);
			$totalPoint += $earnedPoints;
			$geoPathCount++;
		}
		return array('totalPoints' => round($totalPoint,2), 'geoPathCount' => $geoPathCount, 'pointsDetails' => $pointsDetails);
	}
	
	public static function checkForPowerTrailByCache($cacheId){
		$queryPt = 'SELECT `id`, `name`, `image` FROM `PowerTrail` WHERE `id` IN ( SELECT `PowerTrailId` FROM `powerTrail_caches` WHERE `cacheId` =:1 ) AND `status` = 1 ';
		$db = new dataBase;
		$db->multiVariableQuery($queryPt, $cacheId);
		return $db->dbResultFetchAll();
	}
	
	public static function getPtOwners($ptId) {
		$query = 'SELECT user_id, username, email FROM `user` WHERE user_id IN (SELECT `userId` FROM `PowerTrail_owners` WHERE `PowerTrailId` = :1 ) ';
		$db = new dataBase;
		$db->multiVariableQuery($query, $ptId);
		return $db->dbResultFetchAll();
	}
	
	public static function getPtDbRow($ptId) {
		$query = 'SELECT * FROM `PowerTrail` WHERE `id` = :1 LIMIT 1';
		$db = new dataBase;
		$db->multiVariableQuery($query, $ptId);
		return $db->dbResultFetch();
	}
	
	public static function getPtCacheCount($ptId) {
		$q = 'SELECT count( * ) AS `count` FROM `powerTrail_caches` WHERE `PowerTrailId` =:1';
		$db = new dataBase;
		$db->multiVariableQuery($q, $ptId);
		$answer = $db->dbResultFetch();
		return $answer['count'];
	}
	
	public static function getUserDetails($userId) {
		$q = 'SELECT * FROM `user` WHERE `user_id` =:1 LIMIT 1';
		$db = new dataBase;
		$db->multiVariableQuery($q, $userId);
		$answer = $db->dbResultFetch();
		return $answer;
	}
	
	public static function getSingleComment($commentId) {
		$query = 'SELECT * FROM `PowerTrail_comments` WHERE `id` = :1 LIMIT 1';
		$db = new dataBase;
		$db->multiVariableQuery($query, $commentId);
		return $db->dbResultFetch();
	}
	
	public static function getCachePoints($cacheData){
		$typePoints = self::cacheTypePoints();
		$sizePoints = self::cacheSizePoints();
		$typePoints = $typePoints[$cacheData['type']];
		$sizePoints = $sizePoints[$cacheData['size']];
		$url = 'http://maps.googleapis.com/maps/api/elevation/xml?locations='.$cacheData['latitude'].','.$cacheData['longitude'].'&sensor=false';
		$altitude = simplexml_load_file($url);
		$altitude = round($altitude->result->elevation);
		if ($altitude <= 400) $altPoints = 1;
		else $altPoints = 1+($altitude-400)/200 ;
		$difficPoint = round($cacheData['difficulty']/3,2);
		$terrainPoints = round($cacheData['terrain']/3,2);
		return ($altPoints + $typePoints + $sizePoints + $difficPoint + $terrainPoints);
	}


	public static function recalculateCenterAndPoints($caches){
		$points = 0;
		$lat = 0;
		$lon = 0;
		$counter = 0;
		foreach ($caches as $cache){
			$points += self::getCachePoints($cache);
	        $lat += $cache['latitude'];
			$lon += $cache['longitude'];
			$counter++;
		}
	
		if($counter>0){
			$result['avgLat'] = $lat/$counter;
			$result['avgLon'] = $lon/$counter;
		} else {
			$result['avgLat'] = 0;
			$result['avgLon'] = 0;
		}
		$result['points'] = $points;
		$result['cacheCount'] = $counter;
		return $result;
	}
	
	public static function writePromoPt4mainPage($oldPtId){
		$q = 'SELECT * FROM `PowerTrail` WHERE `id` != :1 AND `status` = 1 AND `cacheCount` >= '.self::historicMinimumCacheCount().' ORDER BY `id` ASC';
		$db = new dataBase;
		$db->multiVariableQuery($q, $oldPtId);
		$r = $db->dbResultFetchAll();
		foreach ($r as $pt) {
			if ($pt['id'] > $oldPtId){
				return $pt;
			}
		}
		return $r[0];
	}
	
	public static function getPtCaches($PtId){
		$db = new dataBase;
		$q = 'SELECT powerTrail_caches.isFinal, caches . * , user.username FROM  `caches` , user, powerTrail_caches WHERE cache_id IN ( SELECT  `cacheId` FROM  `powerTrail_caches` WHERE  `PowerTrailId` =:1) AND user.user_id = caches.user_id AND powerTrail_caches.cacheId = caches.cache_id ORDER BY caches.name';
		$db->multiVariableQuery($q, $PtId);
		return $db->dbResultFetchAll();
	}
	
	public static function getPtCachesIds($PtId){
		$q = 'SELECT `cacheId` FROM `powerTrail_caches` WHERE `PowerTrailId` =:1';
		$db = new dataBase;
		$db->multiVariableQuery($q, $PtId);
		$r = $db->dbResultFetchAll();
		//return $r;
		foreach ($r as $c) {
			$result[] = $c['cacheId'];
		}
		return $result;
	}
	
	/**
	 * remove unwanted chars from pt names 
	 * (for gpx filenames)
	 */
	public static function clearPtNames($ptName){
		$ptName = ucwords(strtolower($ptName)); 
		$ptName = str_replace('♥', 'Serduszko', $ptName);
		$ptName = str_replace(' ', '', $ptName);
		$ptName = trim($ptName);
		return $ptName;
	}
	
	public static function getLeadingUser($ptId){
		$q = "SELECT  `username`, `user_id` FROM  `user` WHERE  `user_id` = ( SELECT  `userId` FROM  `PowerTrail_actionsLog` WHERE  `actionType` =1 AND  `PowerTrailId` =:1 LIMIT 1) LIMIT 1"; 
		$db = new dataBase;
		$db->multiVariableQuery($q, $ptId);
		return $db->dbResultFetch();
	}

	public static function getAllPt($filter){
		$sortOder = 'ASC';
		$sortBy = 'name';
		
		$q = 'SELECT * FROM `PowerTrail` WHERE cacheCount >= '.self::minimumCacheCount() .' '.$filter.' ORDER BY '.$sortBy.' '.$sortOder.' ';
		$db = new dataBase();
		$db->multiVariableQuery($q);
		return $db->dbResultFetchAll();
	}
	
	/**
	 * used to set geoPath status to inactive, when has too small amount of caches,
	 * etc.
	 */
	public static function cleanGeoPaths() {
		
		$getPtQuery = 'SELECT * FROM `PowerTrail` WHERE `status` =1';
		$db = new dataBase();
		$db->simpleQuery($getPtQuery);
		$ptToClean = $db->dbResultFetchAll();
		$checkPt = new checkPt();
		foreach ($ptToClean as $pt) {
			self::checkCacheCountInPt($pt);
			if(!self::disableUncompletablePt($pt)){
				self::disablePtByCacheCount($pt, $checkPt);
			}
		}
		
		$archiveAbandonQuery = 'SELECT `id` FROM `PowerTrail` WHERE `id` NOT IN (SELECT PowerTrailId FROM `PowerTrail_owners` WHERE 1 GROUP BY PowerTrailId)';
		$db->simpleQuery($archiveAbandonQuery);
		if($db->rowCount()>0){ // close all abandon geoPaths
			$ptToClose = $db->dbResultFetchAll();
			$updateArr = array();
			foreach ($ptToClose as $pt) {
				array_push ($updateArr, $pt['id']);
			}
			$updateArr = implode(',', $updateArr);
			$updQuery = 'UPDATE `PowerTrail` SET `status` =3 WHERE `id` IN ( :1 )';
			$db->multiVariableQuery($updQuery, $updateArr); 
		}
	}

	/**
	 * check if real cache count in pt is equal stored in db.
	 */
	private function checkCacheCountInPt($pt){
		$countQuery = 'SELECT count(*) as `cacheCount` FROM `caches` WHERE `cache_id` IN (SELECT `cacheId` FROM `powerTrail_caches` WHERE `PowerTrailId` =:1) AND `status` IN ( 1, 2, 4, 5 )';
		$db = new dataBase;
		$db->multiVariableQuery($countQuery, $pt['id']);
		$answer = $db->dbResultFetch();
		if($answer['cacheCount'] != $pt['cacheCount']) {
			$updateQuery = 'UPDATE `PowerTrail` SET `cacheCount` =:1  WHERE `id` = :2 ';
			$db->multiVariableQuery($updateQuery, $answer['cacheCount'], $pt['id']);
		}
	}
	
	/**
	 * disable geoPaths, when its WIS > active caches count.
	 */
	private function disableUncompletablePt($pt){
		$countQuery = 'SELECT count(*) as `cacheCount` FROM `caches` WHERE `cache_id` IN (SELECT `cacheId` FROM `powerTrail_caches` WHERE `PowerTrailId` =:1) AND `status` = 1';
		$db = new dataBase;
		$db->multiVariableQuery($countQuery, $pt['id']);
		$answer = $db->dbResultFetch();
		
     //print '#'.__LINE__.'<pre>';
     //print "active cc: ".$answer['cacheCount'].' / required caches: '. $pt['cacheCount']*$pt['perccentRequired']/100;
			
		if($answer['cacheCount'] < ($pt['cacheCount']*$pt['perccentRequired'])/100) {
			print 'put in service geoPath #'.$pt['id'].' (uncompletable)<br/>';
			
			//$queryStatus = 'UPDATE `PowerTrail` SET `status`= :1 WHERE `id` = :2';
			// $db->multiVariableQuery($queryStatus, 4, $pt['id']);
			//$query = 'INSERT INTO `PowerTrail_comments`(`userId`, `PowerTrailId`, `commentType`, `commentText`, `logDateTime`, `dbInsertDateTime`, `deleted`) VALUES (-1, :1, 4, :2, NOW(), NOW(),0)';
			//$text = tr('pt227').tr('pt234');
			// $db->multiVariableQuery($query, $pt['id'], $text);
			//emailOwners($pt['id'], 4, date('Y-m-d H:i:s'), $text, 'newComment');
			return true;
		}
		return false;
	}
	
	/**
	 * disable (set status to 4) geoPaths witch has not enough cacheCount.
	 */
	private function disablePtByCacheCount($pt, $checkPt){
		
		print 'disablePtByCacheCount #'.__LINE__;
		var_dump($pt['cacheCount'], $checkPt->getPtMinCacheCountLimit($pt));
		
		if($pt['cacheCount'] < $checkPt->getPtMinCacheCountLimit($pt)){
			$text = tr('pt227').tr('pt228'); 
			print 'put in service geoPath #'.$pt['id'].' (geoPtah cache count is lower than minimum) <br/>';
			$db = new dataBase;
			$queryStatus = 'UPDATE `PowerTrail` SET `status`= :1 WHERE `id` = :2';
			$db->multiVariableQuery($queryStatus, 4, $pt['id']);
			$query = 'INSERT INTO `PowerTrail_comments`(`userId`, `PowerTrailId`, `commentType`, `commentText`, `logDateTime`, `dbInsertDateTime`, `deleted`) VALUES 
			(-1, :1, 4, :2, NOW(), NOW(),0)';
			$db->multiVariableQuery($query, $pt['id'], $text);
			emailOwners($pt['id'], 4, date('Y-m-d H:i:s'), $text, 'newComment');
			return true;
		}
	return false;
	}
}


class checkPt {
	private $config;
	 
	function __construct(){
		include __DIR__.'/../lib/settings.inc.php';
		$this->config = $powerTrailMinimumCacheCount;
		foreach ($this->config['old'] as &$date) {
			$date['dateFrom'] = strtotime($date['dateFrom']);
			$date['dateTo'] = strtotime($date['dateTo']);
		}
	}
	
	/**
	 * get minimum cache limit from period of time when ptWas published
	 */
	public function getPtMinCacheCountLimit($pt){
		$ptId = $pt['id'];
		$ptPublished = strtotime($pt['dateCreated']);
		
		$done=false;
		foreach ($this->config['old'] as $date){ //find interval path was created
			if ($ptPublished >= $date['dateFrom'] && $ptPublished < $date['dateTo']){ // patch was created here
				return $date['limit'];
			}
		}
		return false;
	}
}


