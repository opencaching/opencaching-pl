<?php

/**
 * cachemap-mini.php
 *
 *  Used only from viewcache.php to get small map for cache on a way:
 * cachemap-mini.php?
 *     inputZoom=14&amp;
 *     lat={latitude}&amp;
 *     lon={longitude}&amp;
 *
 * Params (all required):
 * @param inputZoom
 * @param lat
 * @param lon
 *
 */
require_once('./lib/common.inc.php');
require_once('./lib/cachemap3_common.php');

// check if user logged in
handleUserLogged();

$tplname = 'cachemap-mini';

// only logged user point of view is supported here
$mapForUserId = $usr['userid']; // $usr is stored in sessions
tpl_set_var('userid', $mapForUserId);

// lat & lon params are required here
if (isset($_REQUEST['lat']) && $_REQUEST['lat'] != "" && isset($_REQUEST['lon']) && $_REQUEST['lon'] != "") {
    // use cords from request
    tpl_set_var('coords', $_REQUEST['lat'] . "," . $_REQUEST['lon']);
} else {
    tpl_set_var('coords', $country_coordinates);
}

// zoom param is required here
if (isset($_REQUEST['inputZoom']) && $_REQUEST['inputZoom'] != "") {
    tpl_set_var('zoom', $_REQUEST['inputZoom']);
} else {
    tpl_set_var('zoom', $default_country_zoom); // this is default zoom
}

// parse PowerTrail filter in url
parsePowerTrailFilter(false);

setTheRestOfCommonVars(); // open current cache
tpl_set_var('map_type', "0"); // fixed to default map

setCommonMap3Vars();

tpl_set_var('cachemap_mapper', $cachemap_mapper);

// ...and lest run template in fullscrean mode...
tpl_BuildTemplate(true, true);
