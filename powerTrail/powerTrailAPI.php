<?php
/**
 * 
 */
class powerTrailApi{
		
	const minimumCacheCount = 0;
	
	public $logActionTypes = array (
		1 => array (
			'type' => 'create new Power Trail'
		),
		2 => array (
			'type' => 'attach cache to PowerTrail'
		),
		3 => array (
			'type' => 'remove cache from PowerTrail'
		),
	);
	
	function __construct() {
		
	}
}
