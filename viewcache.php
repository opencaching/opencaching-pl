<?php

use lib\Objects\GeoCache\GeoCache;
use lib\Objects\OcConfig\OcConfig;
use lib\Objects\GeoCache\Waypoint;
use Utils\Database\XDb;
use Utils\Email\EmailSender;
use Utils\Gis\Gis;
use Utils\Log\CacheAccessLog;
use lib\Objects\GeoCache\GeoCacheDesc;
use lib\Objects\GeoCache\OpenChecker;
use lib\Objects\Coordinates\Coordinates;
use lib\Objects\GeoCache\PrintList;



//prepare the templates and include all neccessary
if (!isset($rootpath)){
    global $rootpath;
}
require_once('./lib/common.inc.php');
require_once('lib/cache_icon.inc.php');




global $usr, $hide_coords, $cache_menu;
global $powerTrailModuleSwitchOn, $titled_cache_period_prefix;
global $config;


$applicationContainer = \lib\Objects\ApplicationContainer::Instance();
$loggedUser = $applicationContainer->getLoggedUser();
$ocConfig = $applicationContainer->getOcConfig();
$dbc = $applicationContainer->db;

$view = tpl_getView();

require_once($stylepath . '/lib/icons.inc.php');
require($stylepath . '/viewcache.inc.php');
require($stylepath . '/viewlogs.inc.php');


/** @var GeoCache $geocache */
$geocache = null;

if (isset($_REQUEST['cacheid'])) {
    $geocache = GeoCache::fromCacheIdFactory( $_REQUEST['cacheid'] );

} elseif (isset($_REQUEST['uuid'])) {
    $geocache = GeoCache::fromUUIDFactory( $_REQUEST['uuid'] );

} elseif (isset($_REQUEST['wp'])) {
    $geocache = GeoCache::fromWayPointFactory( $_REQUEST['wp'] );
}


/* check if there is cache to display */
if( $geocache == null || (
        (
            $geocache->getStatus() == GeoCache::STATUS_WAITAPPROVERS ||
            $geocache->getStatus() == GeoCache::STATUS_NOTYETAVAILABLE ||
            $geocache->getStatus() == GeoCache::STATUS_BLOCKED
        ) && (
            $loggedUser == null ||
            ( $loggedUser->getUserId() != $geocache->getOwnerId() && !$loggedUser->isAdmin() )
        )
    ) || (
        $geocache->getStatus() == GeoCache::STATUS_WAITAPPROVERS && $loggedUser->isGuide()
    )
  ){
    // there is no cache to display...
    tpl_set_tplname('viewcache_error');
    tpl_BuildTemplate();
    exit(0);
}

//set here the template to process
if (isset($_REQUEST['print']) && $_REQUEST['print'] == 'y'){
    tpl_set_tplname('viewcache_print');
}else{
    tpl_set_tplname('viewcache');
}

$cache_id = $geocache->getCacheId(); //TODO: refactor to $geocache...

$view->setVar('geoCache', $geocache);

$view->setVar('isUserAuthorized', is_object($loggedUser) );
$view->setVar('isAdminAuthorized', $loggedUser && $loggedUser->isAdmin() );



if ($loggedUser) {
    tpl_set_var('uType', $loggedUser->isAdmin());
} else {

}





// detailed cache access logging
if (@$enable_cache_access_logs) {
    $userId = $loggedUser ? $loggedUser->getUserId() : null;
    CacheAccessLog::logBrowserCacheAccess($geocache->getCacheId(), $userId, 'view_cache');
}


if ($loggedUser && $geocache->getOwnerId() == $loggedUser->getUserId()) {
    $show_edit = true;
    $show_ignore = false;
    $show_watch = false;
} else {
    if ($loggedUser && $loggedUser->isAdmin()) {
        $show_edit = true;
    } else {
        $show_edit = false;
    }
    $show_ignore = true;
    $show_watch = true;
}



/** @var Coordinates $userModifiedCacheCoords */
$userModifiedCacheCoords = null;
if ($loggedUser && (
    $geocache->getCacheType() == GeoCache::TYPE_OTHERTYPE ||
    $geocache->getCacheType() == GeoCache::TYPE_QUIZ ||
    $geocache->getCacheType() == GeoCache::TYPE_MULTICACHE )) {

    $view->setVar('cacheCoordsModificationAllowed',true);

    // insert/edit modified coordinates
    if (isset($_POST['userModifiedCoordsSubmited']) &&
        isset($_POST['userCoordsFinalLatitude']) &&
        isset($_POST['userCoordsFinalLongitude']) ) {

            $userModifiedCacheCoords = Coordinates::FromCoordsFactory($_POST['userCoordsFinalLatitude'], $_POST['userCoordsFinalLongitude']);
            if($userModifiedCacheCoords){
                $geocache->saveUserCoordinates($userModifiedCacheCoords, $loggedUser->getUserId());
            }else{
                //TODO: improper coords!?
            }

    }elseif ( isset($_POST['deleteUserModifiedCoords']) ){
        // user requested to delete user-modified-ccords
        $geocache->deleteUserCoordinates($loggedUser->getUserId());

    }else{ //there are no new userCoords for this cache - check if user set something before
        $userModifiedCacheCoords = $geocache->getUserCoordinates($loggedUser->getUserId());
    }


}else{
    $view->setVar('cacheCoordsModificationAllowed', false);
}

$view->setVar('userModifiedCacheCoords', $userModifiedCacheCoords);






PrintList::HandleRequest();




$owner_id = $geocache->getOwner()->getUserId();
tpl_set_var('owner_id', $owner_id);




if ( $loggedUser && $loggedUser->getHomeCoordinates()->areCordsReasonable() ) {

    $view->setVar('distanceToCache', sprintf("%.2f", Gis::distanceBetween($loggedUser->getHomeCoordinates(), $geocache->getCoordinates())));
    $view->setVar('displayDistanceToCache', true);

} else {
    $view->setVar('displayDistanceToCache', false);
}

if ($geocache->getRatingVotes() < 3) {
    // DO NOT show cache's score
    $score = tr('not_available');
    $scoreColor = "#000000";
} else {
    switch($geocache->getScoreAsRatingNum()){
        case 0: $scoreColor = "#DD0000"; break;
        case 1: $scoreColor = "#F06464"; break;
        case 2: $scoreColor = "#DD7700"; break;
        case 3: $scoreColor = "#77CC00"; break;
        case 4: $scoreColor = "#00DD00"; break;
    }
    $score = $geocache->getScoreNameTranslation();
}

$view->setVar('scoreColor', $scoreColor);
$view->setVar('score', $score);



// begin visit-counter
// delete cache_visits older 1 day 60*60*24 = 86400
$dbc->multiVariableQuery("DELETE FROM `cache_visits` WHERE `cache_id`=:1 AND `user_id_ip` != '0' AND NOW()-`last_visited` > 86400", $cache_id);

// first insert record for visit counter if not in db
$chkuserid = isset($usr['userid']) ? $usr['userid'] : $_SERVER["REMOTE_ADDR"];

// note the visit of this user
$dbc->multiVariableQuery(
    "INSERT INTO `cache_visits` (`cache_id`, `user_id_ip`, `count`, `last_visited`)
    VALUES (:1, :2, 1, NOW()) ON DUPLICATE KEY UPDATE `count`=`count`+1", $cache_id, $chkuserid);

if ($chkuserid != $owner_id) {
    // increment the counter for this cache
    $dbc->multiVariableQuery(
        "INSERT INTO `cache_visits` (`cache_id`, `user_id_ip`, `count`, `last_visited`)
        VALUES (:1, '0', 1, NOW()) ON DUPLICATE KEY UPDATE `count`=`count`+1, `last_visited`=NOW()", $cache_id);
}

// end visit-counter

$view->setVar('alwaysShowCoords', !$hide_coords);


$icons = $geocache->dictionary->getCacheTypeIcons();

//cache data
list($iconname) = getCacheIcon($usr['userid'], $geocache->getCacheId(), $geocache->getStatus(), $geocache->getOwner()->getUserId(), $icons[$geocache->getCacheType()]['icon']);
list($lat_dir, $lat_h, $lat_min) = help_latToArray($geocache->getCoordinates()->getLatitude());
list($lon_dir, $lon_h, $lon_min) = help_lonToArray($geocache->getCoordinates()->getLongitude());

$tpl_subtitle = htmlspecialchars($geocache->getCacheName(), ENT_COMPAT, 'UTF-8') . ' - ';








tpl_set_var('cacheid_urlencode', htmlspecialchars(urlencode($cache_id), ENT_COMPAT, 'UTF-8'));
tpl_set_var('cachename', htmlspecialchars($geocache->getCacheName(), ENT_COMPAT, 'UTF-8'));

if ( $geocache->isTitled() ){
    $ntitled_cache = $titled_cache_period_prefix.'_titled_cache';
    tpl_set_var('icon_titled', '&nbsp;&nbsp;&nbsp;&nbsp;<img src="tpl/stdstyle/images/free_icons/award_star_gold_1.png" class="icon16" alt="'.tr($ntitled_cache).'" title="'.tr($ntitled_cache).'">');
} else {
    tpl_set_var('icon_titled', '');
}



if ($usr || !$hide_coords) {
    if ($geocache->getCoordinates()->getLongitude() < 0) {
        $longNC = $geocache->getCoordinates()->getLongitude() * (-1);
        tpl_set_var('longitudeNC', $longNC);
    } else {
        tpl_set_var('longitudeNC', $geocache->getCoordinates()->getLongitude());
    }

    tpl_set_var('longitude', $geocache->getCoordinates()->getLongitude());
    tpl_set_var('latitude', $geocache->getCoordinates()->getLatitude());
    tpl_set_var('lon_h', $lon_h);
    tpl_set_var('lon_min', $lon_min);
    tpl_set_var('lonEW', $lon_dir);
    tpl_set_var('lat_h', $lat_h);
    tpl_set_var('lat_min', $lat_min);
    tpl_set_var('latNS', $lat_dir);
}
tpl_set_var('cacheid', $cache_id);


$iconname = str_replace("mystery", "quiz", $iconname);
tpl_set_var('icon_cache', htmlspecialchars("$stylepath/images/cache/$iconname", ENT_COMPAT, 'UTF-8'));


// cache_rating list of users
// no geokrets in this cache

tpl_set_var('altitude', $geocache->getAltitudeObj()->getAltitude());




$externalMaps = [];
if(!$userModifiedCacheCoords){
    $lat = $geocache->getCoordinates()->getLatitude();
    $lon = $geocache->getCoordinates()->getLongitude();
}else{
    $lat = $userModifiedCacheCoords->getLatitude();
    $lon = $userModifiedCacheCoords->getLongitude();
}
foreach($config['maps']['external'] as $key => $value){
    if ( $value == 1 ) {
        $externalMaps[] = sprintf($config['maps']['external'][$key.'_URL'],
                            $lat, $lon,
                            $geocache->getCacheId(), $geocache->getWaypointId(),
                            urlencode($geocache->getCacheName()),
                            $key);
    }
}
$view->setVar('externalMaps', $externalMaps);


$zoom = $config['maps']['cache_page_map']['zoom'];
$mapType = $config['maps']['cache_page_map']['source'];
$view->setVar('mapImgLink', "lib/staticmap.php?center=$lat,$lon&amp;zoom=$zoom&amp;size=170x170&amp;maptype=$mapType&amp;markers=$lat,$lon,mark-small-blue");

$view->setVar('loginToSeeMapMsg', mb_ereg_replace("{target}", urlencode("viewcache.php?cacheid=".$geocache->getCacheId()), tr('map_msg')));






$view->setVar('cacheHiddenDate', $geocache->getDatePlaced()->format($ocConfig->getDateFormat()));
$view->setVar('cacheCreationDate', $geocache->getDateCreated()->format($ocConfig->getDateFormat()));
$view->setVar('cacheLastModifiedDate', $geocache->getLastModificationDate()->format($ocConfig->getDateFormat()));



if ($loggedUser && $loggedUser->getFoundGeocachesCount() >= $config['otherSites_minfinds']) {
    $view->setVar('otherSitesListing', $geocache->getFullOtherWaypointsList() );
    $view->setVar('searchAtOtherSites', true);
}else{
    $view->setVar('otherSitesListing', []);
    $view->setVar('searchAtOtherSites', false);

}





tpl_set_var('difficulty_icon_diff', icon_difficulty("diff", $geocache->getDifficulty()));
tpl_set_var('difficulty_icon_terr', icon_difficulty("terr", $geocache->getTerrain()));


tpl_set_var('total_number_of_logs', htmlspecialchars($geocache->getFounds() + $geocache->getNotFounds() + $geocache->getNotesCount(), ENT_COMPAT, 'UTF-8'));






if($loggedUser){

    $userNoteText = '';

    if(isset($_POST['saveUserNote'])){

        $userNoteText = $_POST['userNoteText'];

        if(!empty($userNoteText)){
            $geocache->saveUserNote($loggedUser->getUserId(), $userNoteText);
        } else {
            // empty update = delete note
            $geocache->deleteUserNote($loggedUser->getUserId());
        }
    }elseif(isset($_POST['removeUserNote'])){
        $geocache->deleteUserNote($loggedUser->getUserId());

    }else{
        $userNoteText = $geocache->getUserNote($loggedUser->getUserId());
    }

    $view->setVar('userNoteText', $userNoteText);
}



$displayDeletedLogs = true;
if( $loggedUser && $loggedUser->isAdmin() || !$geocache->hasDeletedLog() ){
    $showDeletedLogsDisplayLink = false; //admin always see deleted logs

}else{
    $showDeletedLogsDisplayLink = true;

    if ( isset($_SESSION['showdel']) && $_SESSION['showdel'] == 'y'){
        //hide-link
        $deletedLogsDisplayLink = 'viewcache.php?cacheid=' . $geocache->getCacheId() . '&amp;showdel=n' . $linkargs . '#log_start';
        $deletedLogsDisplayText = tr('vc_HideDeletions');
    }else{
        //show link
        $deletedLogsDisplayLink = 'viewcache.php?cacheid=' . $geocache->getCacheId() . '&amp;showdel=y' . $linkargs . '#log_start';
        $deletedLogsDisplayText = tr('vc_ShowDeletions');

        $displayDeletedLogs = false;

    }

    $view->setVar('deletedLogsDisplayLink',$deletedLogsDisplayLink);
    $view->setVar('deletedLogsDisplayText',$deletedLogsDisplayText);
}

$logEnteriesCount = intval($geocache->getLogEntriesCount($displayDeletedLogs));

$view->setVar('showDeletedLogsDisplayLink', $showDeletedLogsDisplayLink);
$view->setVar('displayAllLogsLink', 0 < $logEnteriesCount );
$view->setVar('logEnteriesCount', $logEnteriesCount);
$view->setVar('displayDeletedLogs', $displayDeletedLogs);


tpl_set_var('cache_watcher', '');
if ($geocache->getWatchingUsersCount() > 0) {
    tpl_set_var('cache_watcher', mb_ereg_replace('{watcher}', htmlspecialchars($geocache->getWatchingUsersCount(), ENT_COMPAT, 'UTF-8'), isset($cache_watchers) ? $cache_watchers : 10 ));
}

tpl_set_var('owner_name', htmlspecialchars($geocache->getOwner()->getUserName(), ENT_COMPAT, 'UTF-8'));
tpl_set_var('userid_urlencode', htmlspecialchars(urlencode($geocache->getOwner()->getUserId()), ENT_COMPAT, 'UTF-8'));


if($geocache->isAdopted()){
    tpl_set_var('creator_userid', $geocache->getFounder()->getUserId());
    tpl_set_var('creator_name', htmlspecialchars($geocache->getFounder()->getUserName(), ENT_COMPAT, 'UTF-8'));
}





// determine description language
$availableDescLangs = mb_split(',', $geocache->getDescLanguagesList());


// check if user requests other lang of cache desc...
if ( isset($_REQUEST['desclang']) && (array_search($_REQUEST['desclang'], $availableDescLangs) !== false)) {
    $descLang = $_REQUEST['desclang'];

} elseif (array_search( mb_strtoupper($lang) , $availableDescLangs) === false) { // or try current lang
    $descLang = mb_strtoupper($lang);

}else{ // use first available otherwise
    $descLang = $availableDescLangs[0];
}

$view->setVar('usedDescLang', $descLang); // lang of presented description
$view->setVar('availableDescLangs', $availableDescLangs);



// add OC Team comment
if ($loggedUser && $loggedUser->isAdmin() && isset($_POST['rr_comment']) && !empty($_POST['rr_comment']) && !$_SESSION['submitted']) {
    GeoCacheDesc::UpdateAdminComment( $geocache, $_POST['rr_comment'], $loggedUser);
    $_SESSION['submitted'] = true;
}

// remove OC Team comment
if ($loggedUser && $loggedUser->isAdmin() && isset($_GET['rmAdminComment']) && isset($_GET['cacheid'])) {
    GeoCacheDesc::RemoveAdminComment($geocache);
}


$geoCacheDesc = $geocache->getCacheDescription($descLang);
$view->setVar('geoCacheDesc', $geoCacheDesc);




if(OpenChecker::isEnabledInConfig()){
    $openChecker = OpenChecker::ForCacheIdFactory($geocache->getCacheId());
}else{
    $openChecker = null;
}
$view->setVar('openChecker', $openChecker);



$waypointsList = Waypoint::GetWaypointsForCacheId($geocache);
$view->setVar('waypointsList', $waypointsList);
$view->setVar('cacheWithStages',
    $geocache->getCacheType() == GeoCache::TYPE_OTHERTYPE ||
    $geocache->getCacheType() == GeoCache::TYPE_QUIZ ||
    $geocache->getCacheType() == GeoCache::TYPE_MULTICACHE );







$picturesToDisplay = null;
if ($geocache->getPicturesCount() != 0 &&
     !( isset($_REQUEST['print']) && isset($_REQUEST['pictures']) && $_REQUEST['pictures'] == 'no' )) {

    //there are any pictures to display

    $hideSpoilersForAnonims = !$loggedUser && $hide_coords;
    $showOnlySpoilers = isset($_REQUEST['spoiler_only']) && $_REQUEST['spoiler_only'] == 1;
    $unHideSpoilersThumbs = $loggedUser && isset($_REQUEST['print']) &&  $_REQUEST['print'] = 'big' || $_REQUEST['print'] = 'small';

    $picturesToDisplay = $geocache->getPicturesList($showOnlySpoilers, $hideSpoilersForAnonims, $unHideSpoilersThumbs);

    $view->setVar('displayBigPictures',
        isset($_REQUEST['print']) && $_REQUEST['print']=='y' && isset($_REQUEST['pictures']) && $_REQUEST['pictures'] == 'big');
}
$view->setVar('picturesToDisplay', $picturesToDisplay);




$showUnencryptedHint = isset($_REQUEST['nocrypt']) && $_REQUEST['nocrypt'] == 1;
$view->setVar('showUnencryptedHint', $showUnencryptedHint);

$hint = $geoCacheDesc->getHint();

if(!$showUnencryptedHint){
    $hint = str_rot13_html($geoCacheDesc->getHint());

    //replace { and } to prevent replacing at view template processing!
    $hint = mb_ereg_replace('{', '&#0123;', $hint);
    $hint = mb_ereg_replace('}', '&#0125;', $hint);
}

$view->setVar('hintEncrypted', $geoCacheDesc->getHint());
$view->setVar('hintDecrypted', $hint);



/*
$includeDeletedLogs = false;
if ($usr && !$HideDeleted && $usr['admin'] != 1) {
    $includeDeletedLogs = true;
}
if($usr['admin'] == 1){
    $includeDeletedLogs = true;
}
tpl_set_var('includeDeletedLogs', $includeDeletedLogs ? 1 : 0);
 */

if (isset($_REQUEST['logbook']) && $_REQUEST['logbook'] == 'no') {
    tpl_set_var('hidelogbook_start', '<!--');
    tpl_set_var('hidelogbook_end', '-->');
} else {
    tpl_set_var('hidelogbook_start', '');
    tpl_set_var('hidelogbook_end', '');
}






// action functions
$edit_action = "";
$log_action = "";
$watch_action = "";
$ignore_action = "";
$print_action = "";
$is_watched = "";
$watch_label = "";
$is_ignored = "";
$ignore_label = "";
$ignore_icon = "";

//sql request only if we want show 'watch' button for user
if($show_watch) {
    //is this cache watched by this user?

    if (!$geocache->isWatchedBy($loggedUser->getUserId())) {
        $watch_action = mb_ereg_replace('{cacheid}', urlencode($cache_id), $function_watch);
        $is_watched = 'watchcache.php?cacheid=' . $cache_id . '&amp;target=viewcache.php%3Fcacheid=' . $cache_id;
        $watch_label = tr('watch');
    } else {
        $watch_action = mb_ereg_replace('{cacheid}', urlencode($cache_id), $function_watch_not);
        $is_watched = 'removewatch.php?cacheid=' . $cache_id . '&amp;target=viewcache.php%3Fcacheid=' . $cache_id;
        $watch_label = tr('watch_not');
    }
}

//sql request only if we want show 'ignore' button for user
if($show_ignore) {
    //is this cache ignored by this user?

    if(!$geocache->isIgnoredBy($loggedUser->getUserId())){

        $ignore_action = mb_ereg_replace('{cacheid}', urlencode($cache_id), $function_ignore);
        $is_ignored = "addignore.php?cacheid=" . $cache_id . "&amp;target=viewcache.php%3Fcacheid%3D" . $cache_id;
        $ignore_label = tr('ignore');
        $ignore_icon = 'images/actions/ignore';
    } else {
        $ignore_action = mb_ereg_replace('{cacheid}', urlencode($cache_id), $function_ignore_not);
        $is_ignored = "removeignore.php?cacheid=" . $cache_id . "&amp;target=viewcache.php%3Fcacheid%3D" . $cache_id;
        $ignore_label = tr('ignore_not');
        $ignore_icon = 'images/actions/ignore';
    }
}

if ($usr !== false) {
    //user logged in => he can log
    $log_action = mb_ereg_replace('{cacheid}', urlencode($cache_id), $function_log);


    $printListLabel = PrintList::IsOnTheList($geocache->getCacheId()) ?
        tr('remove_from_list'): tr('add_to_list');


    $cache_menu = array(
        'title' => tr('cache_menu'),
        'menustring' => tr('cache_menu'),
        'siteid' => 'viewcache_menu',
        'navicolor' => '#E8DDE4',
        'visible' => false,
        'filename' => 'viewcache.php',
        'submenu' => array(
            array(
                'title' => tr('new_log_entry'),
                'menustring' => tr('new_log_entry'),
                'visible' => true,
                'filename' => 'log.php?cacheid=' . $cache_id,
                'newwindow' => false,
                'siteid' => 'new_log',
                'icon' => 'images/actions/new-entry'
            ),
            array(
                'title' => $watch_label,
                'menustring' => $watch_label,
                'visible' => $show_watch,
                'filename' => $is_watched,
                'newwindow' => false,
                'siteid' => 'observe_cache',
                'icon' => 'images/actions/watch'
            ),
            array(
                'title' => tr('report_problem'),
                'menustring' => tr('report_problem'),
                'visible' => true,
                'filename' => 'reportcache.php?cacheid=' . $cache_id,
                'newwindow' => false,
                'siteid' => 'report_cache',
                'icon' => 'images/actions/report-problem'
            ),
            array(
                'title' => tr('print'),
                'menustring' => tr('print'),
                'visible' => true,
                'filename' => 'printcache.php?cacheid=' . $cache_id,
                'newwindow' => false,
                'siteid' => 'print_cache',
                'icon' => 'images/actions/print'
            ),
            array(
                'title' => $printListLabel,
                'menustring' => $printListLabel,
                'visible' => true,
                'filename' => PrintList::AddOrRemoveCacheUrl($geocache->getCacheId()),
                'newwindow' => false,
                'siteid' => 'print_list_cache',
                'icon' => PrintList::IsOnTheList($geocache->getCacheId()) ?
                            'images/actions/list-remove' : 'images/actions/list-add'
            ),
            array(
                'title' => $ignore_label,
                'menustring' => $ignore_label,
                'visible' => $show_ignore,
                'filename' => $is_ignored,
                'newwindow' => false,
                'siteid' => 'ignored_cache',
                'icon' => $ignore_icon
            ),
            array(
                'title' => tr('edit'),
                'menustring' => tr('edit'),
                'visible' => $show_edit,
                'filename' => 'editcache.php?cacheid=' . $cache_id,
                'newwindow' => false,
                'siteid' => 'edit_cache',
                'icon' => 'images/actions/edit'
            )
        )
    );
    $report_action = "<li><a href=\"reportcache.php?cacheid=$cache_id\">" . tr('report_problem') . "</a></li>";
} else {
    $cache_menu = array(
        'title' => tr('cache_menu'),
        'menustring' => tr('cache_menu'),
        'siteid' => 'viewcache_menu',
        'navicolor' => '#E8DDE4',
        'visible' => false,
        'filename' => 'viewcache.php',
        'submenu' => array(),
    );
}








//make the template and send it out


// geoPath badge
$geoPathSectionDisplay = false;

if ($powerTrailModuleSwitchOn && $cache_id != null) {
    $geoPathsList = [];
    foreach (powerTrailBase::checkForPowerTrailByCache($cache_id) as $pt) {
        $geoPath = new stdClass();
        $geoPath->id = $pt['id'];
        $geoPath->name = $pt['name'];
        if ($pt['image'] == ''){
            $geoPath->img = 'tpl/stdstyle/images/blue/powerTrailGenericLogo.png';
        }else{
            $geoPath->img = $pt['image'];
        }
        $geoPathsList[] = $geoPath;
        $geoPathSectionDisplay = true;
    }
    $view->setVar('geoPathsList', $geoPathsList);
}
$view->setVar('geoPathSectionDisplay', $geoPathSectionDisplay);



$view->setVar('linkargs', $linkargs);

$view->setVar('viewcache_js', "tpl/stdstyle/js/viewcache." . filemtime($rootpath . 'tpl/stdstyle/js/viewcache.js') . ".js");



tpl_BuildTemplate();

function onTheList($theArray, $item)
{
    for ($i = 0; $i < count($theArray); $i++) {
        if ($theArray[$i] == $item)
            return $i;
    }
    return -1;
}
