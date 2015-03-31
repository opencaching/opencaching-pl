<!--
// On touch devices use full-screen map by default
// **** Check for touch device below should be kept in sync with analogous check in lib/cachemap3.js ****
-->
<script type='text/javascript'>

    if (('ontouchstart' in window) || (navigator.msMaxTouchPoints > 0)){
        //check cookie to allow user to come back to non-full screen mode
        if( document.cookie.indexOf("forceFullScreenMap=off") == -1){
            //touch device + cookie not set => redirect to full screen map
            window.location = 'cachemap-full.php'+window.location.search;
        }
    }
</script>

<?php
require_once('./lib/common.inc.php');

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
    global $MIN_SCORE, $MAX_SCORE;

    $filter = array("h_u" => 1,
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
        
        global $powerTrailModuleSwitchOn;
        if( $powerTrailModuleSwitchOn===true ){ //skip this setting powerTrails are 
            $filter["powertrail_only"] = $row['powertrail_only'];
        }
        
        $filter["min_score"] = $row['min_score'];
        $filter["max_score"] = $row['max_score'];
        $filter["h_noscore"] = $row['noscore'];
    }

    return $filter;
}

$tplname = 'cachemap3';

global $usr;
global $get_userid;
global $filter;
global $caches_list;
global $language;
global $lang;

$user_id = '';
$get_userid;

if (isset($_REQUEST['userid']))
    $get_userid = intval($_REQUEST['userid']);

//user logged in?
if ($usr == false) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);
} else {

    if (isset($_GET['sc'])) //TODO: what is it - dead code?
        tpl_set_var('sc', intval($_GET['sc']));

    if ($get_userid == '') {
        $user_id = $usr['userid'];
        tpl_set_var('extrauserid', "");
    } else {
        $user_id = $get_userid;
        tpl_set_var('extrauserid', "&userid=$user_id");
    }

    tpl_set_var('userid', $user_id);

    if (isset($_REQUEST['circle']) && $_REQUEST['circle'] == "1") {
        tpl_set_var('circle', $_REQUEST['circle']);
    } else {
        tpl_set_var('circle', "0");
    }

    $rs = mysql_query("SELECT `latitude`, `longitude`, `username` FROM `user` WHERE `user_id`='$user_id' LIMIT 1");
    $record = mysql_fetch_array($rs);
    if (isset($_REQUEST['lat']) && $_REQUEST['lat'] != "" && isset($_REQUEST['lon']) && $_REQUEST['lon'] != "") {
        $coordsXY = $_REQUEST['lat'] . "," . $_REQUEST['lon'];
        $coordsX = $_REQUEST['lat'];
        if ($_REQUEST['inputZoom'] != "")
            tpl_set_var('zoom', $_REQUEST['inputZoom']);
        else
            tpl_set_var('zoom', 11);
    }
    else {
        $coordsXY = "$record[latitude],$record[longitude]";
        $coordsX = "$record[latitude]";
        if ($coordsX == "" || $coordsX == 0) {
            $coordsXY = $country_coordinates;
            tpl_set_var('zoom', $default_country_zoom);
        } else
            tpl_set_var('zoom', 11);
    }

    if (isset($_REQUEST['print_list']) && $_REQUEST['print_list'] == 'y') {
        // add cache to print (do not duplicate items)
        if (count($_SESSION['print_list']) == 0)
            $_SESSION['print_list'] = array();
        if (onTheList($_SESSION['print_list'], $_REQUEST['cacheid']) == -1)
            array_push($_SESSION['print_list'], $_REQUEST['cacheid']);
    }
    if (isset($_REQUEST['print_list']) && $_REQUEST['print_list'] == 'n') {
        // remove cache from print list
        while (onTheList($_SESSION['print_list'], $_REQUEST['cacheid']) != -1)
            unset($_SESSION['print_list'][onTheList($_SESSION['print_list'], $_REQUEST['cacheid'])]);
        $_SESSION['print_list'] = array_values($_SESSION['print_list']);
    }

    tpl_set_var('doopen', isset($_REQUEST['cacheid']) ? "true" : "false");
    tpl_set_var('coords', $coordsXY);
    tpl_set_var('username', $record['username']);
    tpl_set_var('map_width', isset($_GET['print']) ? ($x_print . "px") : ("99%"));
    tpl_set_var('map_height', isset($_GET['print']) ? $y_print : ("512") . "px");

    $filter = getDBFilter($usr['userid']);
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

    foreach ($filter as $key => $value) {
        if ($key == "min_score" || $key == "max_score") {
            if ($key == "min_score")
                $minmax = "min";
            else
                $minmax = "max";

            tpl_set_var($minmax . "_sel" . intval(score2ratingnum($value) + 1), 'selected="selected"');
            tpl_set_var($key, $value);
            continue;
        }

        if ( !(
                $key == "h_avail" || 
                $key == "h_temp_unavail" || 
                $key == "be_ftf" ||
                $key == "powertrail_only" ||
                $key == "map_type" || 
                $key == "h_noscore")
        ) {
            // workaround for reversed values
            $value = 1 - $value;
        }

        $value = intval($value);
        if ($value)
            $chk = ' checked="checked"';
        else
            $chk = "";
        //echo "k=".$key.":".$chk."(".($value).")<br />";
        tpl_set_var($key . "_checked", $chk);
        if ($key == "map_type")
            tpl_set_var($key, $value);
        else
            tpl_set_var($key, $value ? "true" : "");
    }


    if (isset($_GET['searchdata']) && preg_match('/^[a-f0-9]+/', $_GET['searchdata'])) {
        tpl_set_var('filters_hidden', "display: none;");
        tpl_set_var('searchdata', 'searchdata=' . $_GET['searchdata']);
        tpl_set_var('fromlat', floatval($_GET['fromlat']));
        tpl_set_var('fromlon', floatval($_GET['fromlon']));
        tpl_set_var('tolat', floatval($_GET['tolat']));
        tpl_set_var('tolon', floatval($_GET['tolon']));
        tpl_set_var('boundsurl', '&amp;fromlat=' . floatval($_GET['fromlat']) . '&amp;fromlon=' . floatval($_GET['fromlon']) . '&amp;tolat=' . floatval($_GET['tolat']) . '&amp;tolon=' . floatval($_GET['tolon']));
    } else {
        tpl_set_var('filters_hidden', "");
        tpl_set_var('searchdata', '');
        tpl_set_var('fromlat', '0');
        tpl_set_var('fromlon', '0');
        tpl_set_var('tolat', '0');
        tpl_set_var('tolon', '0');
        tpl_set_var('boundsurl', '');
    }

    // hide powerTrails filter if powerTrails are disabled in config
    if($powerTrailModuleSwitchOn){
        tpl_set_var("powerTrails_display", "");
    } else {
        tpl_set_var("powerTrails_display", "display:none;");
    }

    tpl_set_var("cachemap_mapper", $cachemap_mapper);

    /* if( isset( $_POST['submit'] ) )
      {
      $makeFilterResult = makeDBFilter();
      setDBFilter($usr['userid'],$makeFilterResult);
      $filter = $makeFilterResult;
      } */

    /* SET YOUR MAP CODE HERE */
    tpl_set_var('cachemap_header', '<script src="//maps.googleapis.com/maps/api/js?sensor=false&amp;language=' . $lang . '" type="text/javascript"></script>');
    /*
     * Generate dynamic URL to cachemap3.js file, this will make sure it will be reloaded by the browser.
     * The time-stamp will be stripped by a rewrite rule in lib/.htaccess.
     * */
    $cacheMapVersion = filemtime($rootpath . 'lib/cachemap3.js') % 1000000;
    $cacheMapVersion += filemtime($rootpath . 'lib/cachemap3.php') % 1000000; 
    $cacheMapVersion += filemtime($rootpath . 'lib/cachemap3lib.inc.php') % 1000000;
    $cacheMapVersion += filemtime($rootpath . 'lib/settings.inc.php') % 1000000;
    tpl_set_var('lib_cachemap3_js', "lib/cachemap3." . $cacheMapVersion . ".js");
    tpl_BuildTemplate();
}
