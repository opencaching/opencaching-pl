<?php
class OpensprawdzaczSetup
/**
 * initial setup - setting default values used in OpenSprawdzacz
 */
{
 function __construct()
 {
 	$this->scriptname = 'opensprawdzacz2.php';
 	
 	$this->ile_prob = 10;        // declaration how many times user can try his answer per hour/session
 	$this->limit_czasu = 60;     // [in minutes] - time which must elapse until next guess is possible.
 	
 }

}// end of init Opensprawdzacz setup.

class convertLangLat
{
 var $CoordsDecimal;
 
 function __construct($degree, $minutes)
 {
  $this->CoordsDecimal =  $degree + $minutes / 60;
 }		
	
}
?>