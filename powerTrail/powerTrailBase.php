<?php
require_once __DIR__.'/../lib/db.php';
/**
 * 
 */
class powerTrailBase{
		
	const minimumCacheCount = 0;
	
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
		$this->generatePowerTrailTypes();
	}
	
	private function generatePowerTrailTypes()
	{
		/*	
		$this->powerTrailTypes = array(
			1 => array ( // sport power Trail
				'type' =>  tr('pt026'),
			),
			2 => array ( // tourism power Trail
				'type' =>  tr('pt027'),
			),
			
		);	
		*/
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
			
		);
					
	}


}
