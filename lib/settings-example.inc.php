<?php
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
 
   // Please replay <domain> to real your domain name site for example www.opencaching.pl
 
 	//relative path to the root directory
	if (!isset($rootpath)) $rootpath = './';

	//default used language
	if (!isset($lang)) $lang = 'en';
	
	//default used style
	if (!isset($style)) $style = 'stdstyle';

	//pagetitle
	if (!isset($pagetitle)) $pagetitle = 'Opencaching - Geocaching in ......';


	/* Well known node id's - required for synchronization
	 * 1 Opencaching Germany (www.opencaching.de)
	 * 2 Opencaching Poland (www.opencaching.pl)
	 * 3 Opencaching Tschechien (www.opencaching.cz)
	 * 4 Local Development
	 * 5 Opencaching Entwicklung Deutschland (devel.opencaching.de)
	 * 6 OC UK
	 * 7 OC SE
	 * 8 OC NO
	 */
	$oc_nodeid = 2;
	
	//OC Waypoint  name unique for every OC site.: OC for DE, OP for PL, OZ for CZ, OS for OC SE, OK for OC UK,
	$ocWP = OP;
	
        //name of the cookie
        $opt['cookie']['name'] = 'oc';
        $opt['cookie']['path'] = '/';
        $opt['cookie']['domain'] = '';

        //name of the cookie
        if (!isset($cookiename)) $cookiename = 'oc';
        if (!isset($cookiepath)) $cookiepath = '/';
        if (!isset($cookiedomain)) $cookiedomain = '';


	// Hide coordinates for users not login
	$hide_coords = false;
	// scores range
	$MIN_SCORE = 0;
	$MAX_SCORE = 4;
	//Debug?
	if (!isset($debug_page)) $debug_page = false;
	$develwarning = '';
	
	//site in service? Set to false when doing bigger work on the database to prevent error's
	if (!isset($site_in_service)) $site_in_service = true;
	
	//if you are running this site on a other domain than staging.opencaching.de, you can set
	//this in private_db.inc.php, but don't forget the ending /
	$absolute_server_URI = 'http://<domain>';
	
	// EMail address of the sender
	if (!isset($emailaddr)) $emailaddr = 'noreply@<domain>';
	
	// location for dynamically generated files
	$dynbasepath = '/var/www/ocpl-data/';
	$dynstylepath = $dynbasepath . 'tpl/stdstyle/html/';

	// location of cache images
	if (!isset($picdir)) $picdir = $dynbasepath . 'images/uploads';
	if (!isset($picurl)) $picurl = 'http://<domain>/images/uploads';

	// Thumbsize
	$thumb_max_width = 175;
	$thumb_max_height = 175;

     // Small thumbsize
    $thumb2_max_width = 64;
     $thumb2_max_height = 64;

	// default coordinates for cachemap, set to your country's center of gravity
	$country_coordinates = "52.5,19.2";
	// zoom at which your whole country/region is visible
	$default_country_zoom = 6;

	// maximal size of images
	if (!isset($maxpicsize)) $maxpicsize = 152400;
	
	// allowed extensions of images
	if (!isset($picextensions)) $picextensions = ';jpg;jpeg;gif;png;';

	// location of cache mp3 files
	if (!isset($mp3dir)) $mp3dir = $dynbasepath . 'mp3';
	if (!isset($mp3url)) $mp3url = 'http://<domain>/mp3';

	// maximal size of mp3 for PodCache 5 Mb ?
	if (!isset($maxmp3size)) $maxmp3size = 5000000;
	
	// allowed extensions of images
	if (!isset($mp3extensions)) $mp3extensions = ';mp3;';	
	
	// news settings
	$use_news_approving = true;
	$news_approver_email = 'octeam@<domain>';
	
	//local database settings
	$dbusername = '[DB USERNAME]';
	$dbname = 'ocpl';
	$dbserver = 'localhost';
	$dbpasswd = '[ENTER YOUR DB PASSWORD HERE]';
	$dbpconnect = false;
 
  $opt['db']['server'] = 'localhost';
  $opt['db']['name'] = 'ocpl';
  $opt['db']['username'] = '[DB USERNAME]';
  $opt['db']['password'] = '[ENTER YOUR DB PASSWORD HERE]';

	$tmpdbname = 'temp';

	// warnlevel for sql-execution
	$sql_errormail = 'octeam@<domain>';
	$sql_warntime = 1;

	// replacements for sql()
	$sql_replacements['db'] = $dbname;
	$sql_replacements['tmpdb'] = 'temp';

	// safemode_zip-binary
	$safemode_zip = '/var/www/ocpl/bin/phpzip.php';
	$zip_basedir = $dynbasepath . 'download/zip/';
	$zip_wwwdir = '/download/zip/';

	// Please generate google map key for site name
	$googlemap_key = "ABQIAAAAKzfMHoyn1s1VSuNTwlFfzhTqTxhHAgqKNaAck663VX5jr8OSJBQrTiL58t4Rt3olsGRlxSuqVkU5Xg"; // key for opencaching.pl
	$googlemap_type = "G_MAP_TYPE"; // alternativ: _HYBRID_TYPE
	
	$super_admin_id = 2619; // user_id of admin who can delete all user logs on viewprofile.php page.
	$dberrormail = 'octeam@<domain>';


    // Changee to mapper.cgi if you don't have FastCGI installed or to some other custom script
     $cachemap_mapper = "lib/cgi-bin/mapper.fcgi";
 
 //old code???
  // $cachemap_size_lat = 0.4;
  //$cachemap_size_lon = 0.4;
  //$cachemap_pixel_x = 200;
  //$cachemap_pixel_y = 200;
  //$cachemap_url = 'images/cachemaps/';
  //$cachemap_dir = $rootpath . $cachemap_url;

  $site_name = 'Opencaching.pl';
  $wiki_url  = 'http://wiki.opencaching.pl';
  $rules_url = 'http://wiki.opencaching.pl/index.php/Regulamin_OC_PL';
  $cache_params_url = 'http://wiki.opencaching.pl/index.php/Parametry_skrzynki';
  $rating_desc_url = 'http://wiki.opencaching.pl/index.php/Oceny_skrzynek';
  $contact_mail = 'ocpl (at) opencaching.pl'
  global $octeam_email;
  // E-mail address group of people from OC Team who solve problems, verify cache
  $octeam_email = 'octeam@<domain>';
  
?>
