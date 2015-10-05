<?php
/**
 * 
 * Params in request - all are optional:
 * 
 * @param userid - Id of the user from who the map is presented 
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

//locate userId for which map is displayed
$mapForUserId = getMapUserId();
tpl_set_var('userid', $mapForUserId);


//load User data from DB
$userObj = new \lib\Objects\User\User(
        array(
                'userId' => $mapForUserId, 
                'fieldsStr' => 'user_id,latitude,longitude,username'
        ));

// parse cords and zoom setings
parseCordsAndZoom($userObj);

// parse eventually printList changes
parsePrintList();

//parse PowerTrail filter in url
parsePowerTrailFilter();

//read from DB map settings and apply to the map
$filter = getDBFilter($usr['userid']); //logged user or preview user?
setFilterSettings($filter);

//handle search-data - visualization of search results
parseSearchData();

tpl_set_var('username', $userObj->getUserName()); //actually not used in map-full now...
    
setTheRestOfCommonVars();

//...and lest run template in fullscrean mode...
tpl_BuildTemplate(true, true);
