<?php
/**
 * 
 */
class powerTrailApi{
		
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
}
