<?php

require_once('./lib/common.inc.php');

$tplname = 'cachemap-mini';

global $usr;
global $get_userid;
global $filter;
global $caches_list;
global $language;
global $lang;

$user_id = '';

$get_userid = $_REQUEST['userid'];
//user logged in?
    session_start();


    tpl_set_var('sc', intval($_GET['sc']));

    if( $get_userid == '')
        $user_id = -1;
    else
        $user_id = $get_userid;

    tpl_set_var('userid', $user_id);

    $rs = mysql_query("SELECT `latitude`, `longitude`, `username` FROM `user` WHERE `user_id`='$user_id'");
    $record = mysql_fetch_array($rs);
    if( ($_REQUEST['lat'] != "" && $_REQUEST['lon'] != "") && ($_REQUEST['lat'] != 0 && $_REQUEST['lon'] != 0))
    {
        $coordsXY=$_REQUEST['lat'].",".$_REQUEST['lon'];
        $coordsX=$_REQUEST['lat'];
        if( $_REQUEST['inputZoom'] != "" )
            tpl_set_var('zoom', $_REQUEST['inputZoom']);
        else
            tpl_set_var('zoom', 11);
    }
    else
    {
        $coordsXY="$record[latitude],$record[longitude]";
        $coordsX="$record[latitude]";
        if ($coordsX=="" || $coordsX==0)
        {
            $coordsXY=$country_coordinates;
            tpl_set_var('zoom', $default_country_zoom);
        }
        else
            tpl_set_var('zoom', 11);
    }


//  tpl_set_var('doopen', $_REQUEST['cacheid']?"true":"false");
    tpl_set_var('doopen', "false");
    tpl_set_var('coords', $coordsXY);
    tpl_set_var('username', $record[username]);

    tpl_set_var("map_type", "0");

    tpl_set_var('cachemap_mapper', $cachemap_mapper);

//  foreach($filter as $key)
    /*if( isset( $_POST['submit'] ) )
    {
            $makeFilterResult = makeDBFilter();
            setDBFilter($usr['userid'],$makeFilterResult);
            $filter = $makeFilterResult;
    }*/

    /*SET YOUR MAP CODE HERE*/
    tpl_set_var('cachemap_header', '<script src="//maps.googleapis.com/maps/api/js?sensor=false&amp;language='.$lang.'" type="text/javascript"></script>');
    /*
     * Generate dynamic URL to cachemap3.js file, this will make sure it will be reloaded by the browser.
     * The time-stamp will be stripped by a rewrite rule in lib/.htaccess.
     * */
    tpl_set_var('lib_cachemap3_js', "lib/cachemap3." . date("YmdHis", filemtime($rootpath . 'lib/cachemap3.js')) . ".js");
    tpl_BuildTemplate(true, true);

?>
