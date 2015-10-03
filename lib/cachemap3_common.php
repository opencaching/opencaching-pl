<?php
/*
 * 
 * This is common code for mapv3
 *  this is used by: 
 *    -- /cachemap3.php
 *    -- /cachemap-full.php
 *    -- /cachemap-mini.php
 */


function onTheList($theArray, $item)
{
    for ($i = 0; $i < count($theArray); $i++) {
        if ($theArray[$i] == $item)
            return $i;
    }
    return -1;
}


function getDBFilter($user_id)
{

    global $MIN_SCORE, $MAX_SCORE, $powerTrailModuleSwitchOn; //defined in settings.inc/php

    $filter = array(
    	"h_u" => 1,
        "h_t" => 1,
        "h_m" => 1,
        "h_v" => 1,
        "h_w" => 1,
        "h_e" => 1,
        "h_q" => 1,
        "h_o" => 1,
        "h_owncache" => 1,
        "h_ignored" => 0,
        "h_own" => 1,
        "h_found" => 1,
        "h_noattempt" => 1,
        "h_nogeokret" => 1,
        "h_avail" => 0,
        "h_temp_unavail" => 1,
        "map_type" => 1,
        "h_arch" => 0,
        "be_ftf" => 0,
        "powertrail_only" => 0,
        "min_score" => $MIN_SCORE,
        "max_score" => $MAX_SCORE,
        "h_noscore" => 1
    ); // default filter
    $query = mysql_query("SELECT * from map_settings WHERE `user_id`=$user_id LIMIT 1");
    while ($row = mysql_fetch_assoc($query)) {
        $filter["h_u"] = $row['unknown'];
        $filter["h_t"] = $row['traditional'];
        $filter["h_m"] = $row['multicache'];
        $filter["h_v"] = $row['virtual'];
        $filter["h_w"] = $row['webcam'];
        $filter["h_e"] = $row['event'];
        $filter["h_q"] = $row['quiz'];
        $filter["h_o"] = $row['mobile'];
        $filter["h_owncache"] = $row['owncache'];
        $filter["h_ignored"] = $row['ignored'];
        $filter["h_own"] = $row['own'];
        $filter["h_found"] = $row['found'];
        $filter["h_noattempt"] = $row['notyetfound'];
        $filter["h_nogeokret"] = $row['geokret'];
        $filter["h_avail"] = $row['active'];
        $filter["h_temp_unavail"] = $row['notactive'];
        $filter["map_type"] = $row['maptype'];
        $filter["h_arch"] = $row['archived'];
        $filter["be_ftf"] = $row['be_ftf'];

        if($powerTrailModuleSwitchOn){
            $filter["powertrail_only"] = $row['powertrail_only'];
        }

        $filter["min_score"] = $row['min_score'];
        $filter["max_score"] = $row['max_score'];
        $filter["h_noscore"] = $row['noscore'];
    }

    return $filter;
}

/**
 * parse $_REQUEST['userid'] and return user for which map is displayed
 */
function getMapUserId(){
	global $usr; //$usr is set in common.inc.php
	
	//check if map is for logged user or user want to preview someone else
	if ( isset($_REQUEST['userid']) ){
		$previewUserId = intval($_REQUEST['userid']);
	
		tpl_set_var('extrauserid', "&userid=$previewUserId");
		return $previewUserId;
	
	} else {
		
		//this is map for logged user
		tpl_set_var('extrauserid', "");
		return $usr['userid'];
		
	}	
}

/**
 * Cache can be add to the printList stored in session by request in GET
 */
function parsePrintList(){
	
	if (isset($_REQUEST['print_list']) && $_REQUEST['print_list'] == 'y') {
		// add cache to print (do not duplicate items)
		
		if (!is_array($_SESSION['print_list']))
			$_SESSION['print_list'] = array();
		
		if ( in_array( $_REQUEST['cacheid'], $_SESSION['print_list'] ) )
		    array_push($_SESSION['print_list'], $_REQUEST['cacheid']);
	}
	
	if (isset($_REQUEST['print_list']) && $_REQUEST['print_list'] == 'n') {
		// remove cache from print list
		if(is_array($_SESSION['print_list'])){
			$_SESSION['print_list'] = 
				array_diff( $_SESSION['print_list'], array($_REQUEST['cacheid']));
		}
	}
	
}

function parseCordsAndZoom($userObj){
	
	global $country_coordinates; //from global settings
	global $default_country_zoom; //from global settings
	
	if ( isset( $_REQUEST['lat'] ) && $_REQUEST['lat'] != "" &&
	     isset( $_REQUEST['lon'] ) && $_REQUEST['lon'] != "" ) {
	
	    //use cords from request  	
	    tpl_set_var('coords', $_REQUEST['lat'] . "," . $_REQUEST['lon']);
		
		if ( isset( $_REQUEST['inputZoom'] ) && $_REQUEST['inputZoom'] != "")
	        tpl_set_var('zoom', $_REQUEST['inputZoom']);
		else
		    tpl_set_var('zoom', 11); //this is default zoom
	     	
	}else{
		
		//no cords in request - try user defaults
		if( $userObj->getHomeCordsObj()->areCordsReasonable() ){
			
			//user set proper home cords			
			$lat = $userObj->getHomeCordsObj()->getLatitude();
			$lon = $userObj->getHomeCordsObj()->getLongitude();
			tpl_set_var('coords', "$lat,$lon");			
			
			tpl_set_var('zoom', 11);
		}else{
			//no reasonable user home cords - use node defaults
			tpl_set_var('coords', $country_coordinates);
			tpl_set_var('zoom', $default_country_zoom);
		}
			
	}
}

function setFilterSettings($filter){
	
	global $powerTrailModuleSwitchOn;
	
	//reset all min-score options
	tpl_set_var("min_sel1", "");
	tpl_set_var("min_sel2", "");
	tpl_set_var("min_sel3", "");
	tpl_set_var("min_sel4", "");
	tpl_set_var("min_sel5", "");
	tpl_set_var("max_sel1", "");
	tpl_set_var("max_sel2", "");
	tpl_set_var("max_sel3", "");
	tpl_set_var("max_sel4", "");
	tpl_set_var("max_sel5", "");
	
	
	//go through filter values
	foreach ($filter as $key => $value) {
		
		$value = intval($value);
		if ($key == "min_score" || $key == "max_score") {
			if ($key == "min_score")
				$minmax = "min";
				else
					$minmax = "max";
		
					tpl_set_var($minmax . "_sel" . intval(score2ratingnum($value) + 1), 'selected="selected"');
					tpl_set_var($key, $value);
					continue;
		}
		
	
		if (! ( $key == "h_avail" || $key == "h_temp_unavail" ||
				$key == "be_ftf" || $key == "powertrail_only" ||
				$key == "map_type" || $key == "h_noscore" )
				) {
			
			// workaround for reversed values
			$value = 1 - $value;
		}
		
		if ($value)
			$chk = ' checked="checked"';
		else
			$chk = "";
		
		tpl_set_var($key . "_checked", $chk);
			
		if ($key == "map_type")
			tpl_set_var($key, $value);
		else
			tpl_set_var($key, $value ? "true" : "");
	}
	
	
	// hide powerTrails filter if powerTrails are disabled in config
	if($powerTrailModuleSwitchOn){
		tpl_set_var("powerTrails_display", "");
	} else {
		tpl_set_var("powerTrails_display", "display:none");
	}
}

function parseSearchData(){
	
	if (isset($_GET['searchdata']) && preg_match('/^[a-f0-9]+/', $_GET['searchdata'])) {
		
		tpl_set_var('filters_hidden', "display: none;");
		tpl_set_var('searchdata', 'searchdata=' . $_GET['searchdata']);
		tpl_set_var('fromlat', floatval($_GET['fromlat']));
		tpl_set_var('fromlon', floatval($_GET['fromlon']));
		tpl_set_var('tolat', floatval($_GET['tolat']));
		tpl_set_var('tolon', floatval($_GET['tolon']));
		tpl_set_var('boundsurl', '&amp;fromlat=' . floatval($_GET['fromlat']) . '&amp;fromlon=' . floatval($_GET['fromlon']) . '&amp;tolat=' . floatval($_GET['tolat']) . '&amp;tolon=' . floatval($_GET['tolon']));
	} else {
		
		//there is no searchdata for this map
		tpl_set_var('filters_hidden', "");
		tpl_set_var('searchdata', '');
		tpl_set_var('fromlat', '0');
		tpl_set_var('fromlon', '0');
		tpl_set_var('tolat', '0');
		tpl_set_var('tolon', '0');
		tpl_set_var('boundsurl', '');
	}
	
}

function setTheRestOfCommonVars(){
	
	
	//circle is used to draw circle on the map for visualise 150m distance (used in newcache.tpl.php)
	if (isset($_REQUEST['circle']) && $_REQUEST['circle'] == "1") {
		tpl_set_var('circle', $_REQUEST['circle']);
	} else {
		tpl_set_var('circle', "0");
	}
	
	tpl_set_var('doopen', isset($_REQUEST['cacheid']) ? "true" : "false");
	
	setCommonMap3Vars();
}

function setCommonMap3Vars(){
	
	global $rootpath, $lang, $cachemap_mapper; //from global settings.inc.php
	
	tpl_set_var("cachemap_mapper", $cachemap_mapper);
	/* SET YOUR MAP CODE HERE */
	tpl_set_var('cachemap_header', '<script src="//maps.googleapis.com/maps/api/js?v=3.21&amp;language=' . $lang . '" type="text/javascript"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">');
	
	
	/*
	 * Generate dynamic URL to cachemap3.js file, this will make sure it will be reloaded by the browser.
	 * The time-stamp will be stripped by a rewrite rule in lib/.htaccess.
	 * */
	$cacheMapVersion = filemtime($rootpath . 'lib/cachemap3.js') % 1000000;
	$cacheMapVersion += filemtime($rootpath . 'lib/cachemap3.php') % 1000000;
	$cacheMapVersion += filemtime($rootpath . 'lib/cachemap3lib.inc.php') % 1000000;
	$cacheMapVersion += filemtime($rootpath . 'lib/settings.inc.php') % 1000000;
	tpl_set_var('lib_cachemap3_js', "lib/cachemap3." . $cacheMapVersion . ".js");
	
}

function handleUserLogged(){
	//check if user logged in - $usr is set in common.inc.php
	global $usr;
	if ($usr == false) {
		//user not logged - redirect to login page...
		$target = urlencode(tpl_get_current_page());
		tpl_redirect('login.php?target=' . $target);
		exit;
	}
}

