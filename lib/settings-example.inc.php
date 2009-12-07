<?php
/***************************************************************************
														 ./lib/settings.inc.php
															-------------------
		begin                : Mon June 14 2004
		copyright            : (C) 2004 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

	***************************************************************************/

/***************************************************************************
	*                                         				                                
	*   This program is free software; you can redistribute it and/or modify  	
	*   it under the terms of the GNU General Public License as published by  
	*   the Free Software Foundation; either version 2 of the License, or	    	
	*   (at your option) any later version.
	*
	***************************************************************************/

/****************************************************************************

	Unicode Reminder メモ

	server specific settings

 ****************************************************************************/
 
 /* YOU HAVE TO RENAME THIS FILE TO settings.inc.php */
 
 	//relative path to the root directory
	if (!isset($rootpath)) $rootpath = './';

	//default used language
	if (!isset($lang)) $lang = 'pl';
	
	//default used style
	if (!isset($style)) $style = 'stdstyle';

	//pagetitle
	if (!isset($pagetitle)) $pagetitle = 'Opencaching - Geocaching w Polsce';


	/* Well known node id's - required for synchronization
	 * 1 Opencaching Germany (www.opencaching.de)
	 * 2 Opencaching Poland (www.opencaching.pl)
	 * 3 Opencaching Tschechien (www.opencaching.cz)
	 * 4 Local Development
	 * 5 Opencaching Entwicklung Deutschland (devel.opencaching.de)
	 * 6 OC UK
	 * 7 OC SE
	 */
	$oc_nodeid = 2;
	//OC Waypoint OC for DE, OP for PL, OZ for CZ, OS for OC SE, OK for OC UK
	$oc_waypoint = OP;
	
        //name of the cookie
        $opt['cookie']['name'] = 'ocpl';
        $opt['cookie']['path'] = '/';
        $opt['cookie']['domain'] = '';

        //name of the cookie
        if (!isset($cookiename)) $cookiename = 'ocpl';
        if (!isset($cookiepath)) $cookiepath = '/';
        if (!isset($cookiedomain)) $cookiedomain = '';


	// Hide coordinates for users not login
	$hide_coords = false;
	// scores range
	$MIN_SCORE = 0;
	$MAX_SCORE = 6;
	//Debug?
	if (!isset($debug_page)) $debug_page = false;
	$develwarning = '';
	
	//site in service? Set to false when doing bigger work on the database to prevent error's
	if (!isset($site_in_service)) $site_in_service = true;
	
	//if you are running this site on a other domain than staging.opencaching.de, you can set
	//this in private_db.inc.php, but don't forget the ending /
	$absolute_server_URI = '';
	
	// EMail address of the sender
	if (!isset($emailaddr)) $emailaddr = 'noreply@opencaching.pl';
	
	// location for dynamically generated files
	$dynbasepath = '/var/www/ocpl-data/';
	$dynstylepath = $dynbasepath . 'tpl/stdstyle/html/';

	// location of cache images
	if (!isset($picdir)) $picdir = $dynbasepath . 'images/uploads';
	if (!isset($picurl)) $picurl = 'http://www.opencaching.pl/images/uploads';

	// Thumbsize
	$thumb_max_width = 175;
	$thumb_max_height = 175;

	// maximal size of images
	if (!isset($maxpicsize)) $maxpicsize = 152400;
	
	// allowed extensions of images
	if (!isset($picextensions)) $picextensions = 'jpg;jpeg;gif;png;bmp';

	// location of cache mp3 files
	if (!isset($mp3dir)) $mp3dir = $dynbasepath . 'mp3/uploads';
	if (!isset($mp3url)) $mp3url = 'http://www.opencaching.pl/mp3/uploads';

	// maximal size of mp3 for PodCache
	if (!isset($maxmp3size)) $maxmp3size = 200000;
	
	// allowed extensions of images
	if (!isset($mp3extensions)) $mp3extensions = 'mp3';	
	
	// news settings
	$use_news_approving = true;
	$news_approver_email = 'ocpl@opencaching.pl';
	
	//local database settings
	$dbusername = '[DB USERNAME]';
	$dbname = 'ocpl';
	$dbserver = 'localhost';
	$dbpasswd = '[ENTER YOUR DB PASSWORD HERE]';
	$dbpconnect = false;

	$tmpdbname = 'temp';

	// warnlevel for sql-execution
	$sql_errormail = 'ocpl@opencaching.pl';
	$sql_warntime = 1;

	// replacements for sql()
	$sql_replacements['db'] = $dbname;
	$sql_replacements['tmpdb'] = 'temp';

	// safemode_zip-binary
	$safemode_zip = '/var/www/ocpl/bin/phpzip.php';
	$zip_basedir = $dynbasepath . 'download/zip/';
	$zip_wwwdir = '/download/zip/';

	$googlemap_key = "ABQIAAAAKzfMHoyn1s1VSuNTwlFfzhTqTxhHAgqKNaAck663VX5jr8OSJBQrTiL58t4Rt3olsGRlxSuqVkU5Xg"; // key for opencaching.pl
	$googlemap_type = "G_MAP_TYPE"; // alternativ: _HYBRID_TYPE
	
	$super_admin_id = 2619; // user_id of admin who can delete all user logs on viewprofile.php page.
	$dberrormail = 'ocpl@opencaching.pl';

  $opt['db']['server'] = 'localhost';
  $opt['db']['name'] = 'ocpl';
  $opt['db']['username'] = '[DB USERNAME]';
  $opt['db']['password'] = '[ENTER YOUR DB PASSWORD HERE]';

  // cache_maps-settings
//  $cachemap_wms_url = 'http://10.0.0.1/cgi-bin/mapserv?map=/var/www/maps.geocaching.de/mapserver/caches.map&&REQUEST=GetMap&SERVICE=WMS&VERSION=1.1.1&LAYERS=relief,builtup_areas,inland_water,watercourses,staatsgrenze,strassen,places&SRS=EPSG:4326&BBOX={min_lon},{min_lat},{max_lon},{max_lat}&WIDTH=200&HEIGHT=200&FORMAT=image/jpeg&STYLES=&EXCEPTIONS=XML';
 $cachemap_wms_url = 'http://maps.geocaching.de/cgi-bin/mapserv?map=/var/www/maps.geocaching.de/mapserver/caches.map&&REQUEST=GetMap&SERVICE=WMS&VERSION=1.1.1&LAYERS=relief,builtup_areas,inland_water,watercourses,staatsgrenze,strassen,places&SRS=EPSG:4326&BBOX={min_lon},{min_lat},{max_lon},{max_lat}&WIDTH=200&HEIGHT=200&FORMAT=image/jpeg&STYLES=&EXCEPTIONS=XML';
   $cachemap_size_lat = 0.4;
  $cachemap_size_lon = 0.4;
  $cachemap_pixel_x = 200;
  $cachemap_pixel_y = 200;
  $cachemap_url = 'images/cachemaps/';
  $cachemap_dir = $rootpath . $cachemap_url;
?>
