<?php
/**
 * This script contains configurable variables. Keys of array can be overriden if neccessary in file settings.inc.php
 *
 */

$config = array (
    
	/* url where xml witch most recent blog enterie are placed */
	'blogMostRecentRecordsUrl' => 'http://blog.opencaching.pl/feed/', 
	
	/* to switch cache map v2 on set true otherwise false */
    'map2SwithedOn' => true, 
	
	/* *******************************************************************
	   * Node personalizations
	   ******************************************************************* */
	/* main logo picture (to be placed in tpl/stdstyle/images/) */
    'headerLogo' => 'oc_logo.png', 
	
	/* website icon (favicon); (to be placed in /images/) 
		Format: 16x16 pixels; PNG 8bit indexed or 24bit true color, transparency supported
		A file /favicon.ico (windows icon ICO format, 16x16) should also exist as fallback 
		mainly for MSIE */
		
	'headerFavicon' => 'oc_icon.png', 
	/* winter main logo picture (to be placed in tpl/stdstyle/images/)*/
	'headerLogoWinter' => 'oc_logo_winter.png', 
	
    /* prima aprilis main logo (to be placed in tpl/stdstyle/images/) */
	'headerLogo1stApril' => 'oc_logo_1A.png', 
    
 );

