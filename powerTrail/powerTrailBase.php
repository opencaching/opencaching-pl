<?php
require_once __DIR__.'/../lib/db.php';
/**
 * 
 */
class powerTrailBase{
		
	const minimumCacheCount = 5;
	const userMinimumCacheFoundToSetNewPowerTrail = 500; 
	const powerTrailLogoFileName = 'powerTrailLogoId';
	
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
			1 => array ( //sport
				'translate' => 'pt004',
			),
			2 => array ( // touring
				'translate' => 'pt005',
			),
			3 => array (
				'translate' => 'pt067',
			),
			4 => array (
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
	
	public static function checkUserConquestedPt($userId, $ptId){
		$db = new dataBase;
		$q = 'SELECT count(*) AS `c` FROM PowerTrail_comments WHERE userId = :1 AND	PowerTrailId = :2 ';
		$db->multiVariableQuery($q, $userId, $ptId);
		$response = $db->dbResultFetch();
		return $response['c'];
	}
	
	public function getPoweTrailCompletedCountByUser($user_id) {
		$queryPt = "SELECT count(`PowerTrailId`) AS `ptCount` FROM `PowerTrail_comments` WHERE `commentType` =2 AND `deleted` =0 AND `userId` =:1";
		$db = new dataBase;
		$db->multiVariableQuery($queryPt, $user_id);
		$ptCount = $db->dbResultFetch();
		return (int) $ptCount['ptCount'];
	}
	
	public function checkForPowerTrailByCache($cacheId){
		$queryPt = 'SELECT `id`, `name`, `image` FROM `PowerTrail` WHERE `id` IN ( SELECT `PowerTrailId` FROM `powerTrail_caches` WHERE `cacheId` =:1 ) AND `status` = 1 ';
		$db = new dataBase;
		$db->multiVariableQuery($queryPt, $cacheId);
		return $db->dbResultFetchAll();
	}
}
