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
	/* main logo picture (to be placed in /images/) */
    'headerLogo' => 'oc_logo.png', 
	/* main logo; winter version, displayed during december and january. */
	'headerLogoWinter' => 'oc_logo_winter.png', 
    /* main logo; prima aprilis version (april fools), displayed only on april 1st. */
	'headerLogo1stApril' => 'oc_logo_1A.png', 
	
	/* website icon (favicon); (to be placed in /images/) 
		Format: 16x16 pixels; PNG 8bit indexed or 24bit true color, transparency supported
		A file /favicon.ico (windows icon ICO format, 16x16) should also exist as fallback 
		mainly for MSIE */
	'headerFavicon' => 'oc_icon.png', 
    'defaultLangugaeList' => array (
        'PL', 'EN', 'FR', 'DE', 'NL'
    )
    
 );

