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

//check if user logged in
handleUserLogged();

$tplname = 'cachemap-full';

// locate user for which map is displayed
$mapForUserObj = getMapUserObj();
tpl_set_var('userid', $mapForUserObj->getUserId());


// parse cords and zoom setings
parseCordsAndZoom($mapForUserObj);

// parse eventually printList changes
parsePrintList();

//parse PowerTrail filter in url
parsePowerTrailFilter(true);

//read from DB map settings and apply to the map
$filter = getDBFilter($usr['userid']); //logged user or preview user?
setFilterSettings($filter);

//handle search-data - visualization of search results
parseSearchData();

tpl_set_var('username', $mapForUserObj->getUserName()); //actually not used in map-full now...

setTheRestOfCommonVars();

//...and lest run template in fullscrean mode...
tpl_BuildTemplate(true, true);
