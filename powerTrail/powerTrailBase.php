<?php
require_once __DIR__.'/../lib/db.php';

/**
 * 
 */
class powerTrailBase{
		
	const powerTrailLogoFileName = 'powerTrailLogoId';

	public static function minimumCacheCount(){
		include __DIR__.'/../lib/settings.inc.php';
		// var_dump($powerTrailMinimumCacheCount);
		return $powerTrailMinimumCacheCount;
	} 
	public static function userMinimumCacheFoundToSetNewPowerTrail(){
		include __DIR__.'/../lib/settings.inc.php';
		// var_dump($powerTrailMinimumCacheCount);
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
			1 => array ( //sport
				'translate' => 'pt006',
			),
			2 => array ( // touring
				'translate' => 'pt007',
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
		$q = 'SELECT count(*) AS `c` FROM PowerTrail_comments WHERE userId = :1 AND	PowerTrailId = :2 ';
		$db->multiVariableQuery($q, $userId, $ptId);
		$response = $db->dbResultFetch();
		return $response['c'];
	}
	
	public static function getPoweTrailCompletedCountByUser($user_id) {
		$queryPt = "SELECT count(`PowerTrailId`) AS `ptCount` FROM `PowerTrail_comments` WHERE `commentType` =2 AND `deleted` =0 AND `userId` =:1";
		$db = new dataBase;
		$db->multiVariableQuery($queryPt, $user_id);
		$ptCount = $db->dbResultFetch();
		return (int) $ptCount['ptCount'];
	}
	
	public static function checkForPowerTrailByCache($cacheId){
		$queryPt = 'SELECT `id`, `name`, `image` FROM `PowerTrail` WHERE `id` IN ( SELECT `PowerTrailId` FROM `powerTrail_caches` WHERE `cacheId` =:1 ) AND `status` = 1 ';
		$db = new dataBase;
		$db->multiVariableQuery($queryPt, $cacheId);
		return $db->dbResultFetchAll();
	}
	
	public static function getPtOwners($ptId) {
		$query = 'SELECT username, email FROM `user` WHERE user_id IN (SELECT `userId` FROM `PowerTrail_owners` WHERE `PowerTrailId` = :1 ) ';
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
		// print "alt: $altPoints / type: $typePoints / size: $sizePoints / dif: $difficPoint / ter: $difficPoint"; 
		return ($altPoints + $typePoints + $sizePoints + $difficPoint + $difficPoint);
	}
	
}
