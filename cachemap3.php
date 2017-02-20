<?php
/**
 *
 * Params in request - all are optional:
 *
 * @param userid - Id of the user, from which point of view the map is presented
 *
 * map location:
 * @param lat,lon - coordinates where the map is centered
 * @param inputZoom - zoom to set on the map
 *
 * handling of the cache clipboard operations:
 * @param print_list -
 * @param cacheid -
 *
 * visualization of the search results:
 * @param searchdata + ... -
 *
 */
require_once('./lib/common.inc.php');
require_once('./lib/cachemap3_common.php');

// check if user logged in
handleUserLogged();

$tplname = 'cachemap3';

// locate user for which map is displayed
$mapForUserObj = getMapUserObj();
tpl_set_var('userid', $mapForUserObj->getUserId());

// parse cords and zoom setings
parseCordsAndZoom($mapForUserObj);

// parse eventually printList changes
PrintList::HandleRequest( $_REQUEST['cacheid']);

// parse PowerTrail filter in url
parsePowerTrailFilter(true);

// read from DB map settings and apply to the map
$filter = getDBFilter($usr['userid']); // logged user or preview user?
setFilterSettings($filter);

// handle search-data - visualization of search results
parseSearchData();

tpl_set_var('username', $mapForUserObj->getUserName());
setTheRestOfCommonVars();

// ...and lest run template...
tpl_BuildTemplate();
