<?php

/* cachemap-mini.php */
require_once ('./lib/common.inc.php');
db_disconnect();
$tplname = 'cachemap-mini';
$user_id = (isset($_REQUEST['userid']) && $_REQUEST['userid'] != '') ? $_REQUEST['userid'] : -1;

if (($_REQUEST['lat'] != "" && $_REQUEST['lon'] != "") && ($_REQUEST['lat'] != 0 && $_REQUEST['lon'] != 0)) {
    $coordsXY = $_REQUEST['lat'] . "," . $_REQUEST['lon'];
    $coordsX = $_REQUEST['lat'];
    if ($_REQUEST['inputZoom'] != "") {
        tpl_set_var('zoom', $_REQUEST['inputZoom']);
    } else {
        tpl_set_var('zoom', 11);
    }
} else {
    $rs = dataBase::select(array('latitude', 'longitude'), 'user', array(0 => array('fieldName' => 'user_id', 'fieldValue' => $user_id, 'operator' => '=')));
    $record = $rs[0];
    $coordsXY = "$record[latitude],$record[longitude]";
    $coordsX = "$record[latitude]";
    if ($coordsX == "" || $coordsX == 0) {
        $coordsXY = $country_coordinates;
        tpl_set_var('zoom', $default_country_zoom);
    } else {
        tpl_set_var('zoom', 11);
    }
}

tpl_set_var('userid', $user_id);
tpl_set_var('doopen', "false");
tpl_set_var('coords', $coordsXY);
tpl_set_var("map_type", "0");
tpl_set_var('cachemap_mapper', $cachemap_mapper);

/* SET YOUR MAP CODE HERE */
tpl_set_var('cachemap_header', '<script src="//maps.googleapis.com/maps/api/js?sensor=false&amp;v=3.21&amp;language=' . $lang . '" type="text/javascript"></script>');
/*
 * Generate dynamic URL to cachemap3.js file, this will make sure it will be reloaded by the browser.
 * The time-stamp will be stripped by a rewrite rule in lib/.htaccess.
 * */
$cacheMapVersion = filemtime($rootpath . 'lib/cachemap3.js') % 1000000;
$cacheMapVersion += filemtime($rootpath . 'lib/cachemap3.php') % 1000000;
$cacheMapVersion += filemtime($rootpath . 'lib/cachemap3lib.inc.php') % 1000000;
$cacheMapVersion += filemtime($rootpath . 'lib/settings.inc.php') % 1000000;
tpl_set_var('lib_cachemap3_js', "lib/cachemap3." . $cacheMapVersion . ".js");
tpl_BuildTemplate(true, true);
?>