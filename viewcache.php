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

//prepare the templates and include all neccessary
if (!isset($rootpath)){
    global $rootpath;
}
require_once('./lib/common.inc.php');
require_once('lib/cache_icon.inc.php');

global $caches_list, $usr, $hide_coords, $cache_menu, $octeam_email, $site_name, $absolute_server_URI, $octeamEmailsSignature;
global $dynbasepath, $powerTrailModuleSwitchOn, $titled_cache_period_prefix;
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

    tpl_set_var('hidesearchdownloadsection_start', '');
    tpl_set_var('hidesearchdownloadsection_end', '');
    tpl_set_var('uType', $loggedUser->isAdmin());
} else {


    tpl_set_var('hidesearchdownloadsection_start', '<!--');
    tpl_set_var('hidesearchdownloadsection_end', '-->');
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


if ($loggedUser && (
    $geocache->getCacheType() == GeoCache::TYPE_OTHERTYPE ||
    $geocache->getCacheType() == GeoCache::TYPE_QUIZ ||
    $geocache->getCacheType() == GeoCache::TYPE_MULTICACHE )) {

    $view->setVar('cacheCoordsModificationAllowed',true);
    $userCoordinates = null;

    // insert/edit modified coordinates
    if (isset($_POST['modCoords']) &&
        isset($_POST['userCoordsFinalLatitude']) &&
        isset($_POST['userCoordsFinalLongitude']) ) {

            $userCoordinates = Coordinates::FromCoordsFactory($_POST['userCoordsFinalLatitude'], $_POST['userCoordsFinalLongitude']);
            if($userCoordinates){
                $geocache->saveUserCoordinates($userCoordinates, $loggedUser->getUserId());
            }else{
                //TODO: improper coords!?
            }

    }elseif ( isset($_POST['resetCoords']) ){
        // user requested to delete user-modified-ccords
        $geocache->deleteUserCoordinates($loggedUser->getUserId());

    }else{ //there are no new userCoords for this cache - check if user set something before
        $userCoordinates = $geocache->getUserCoordinates($loggedUser->getUserId());
    }

    $view->setVar('userModifiedCoords', $userCoordinates);

}else{
    $view->setVar('cacheCoordsModificationAllowed', false);
    $view->setVar('userModifiedCoords', null);
}






if (isset($_REQUEST['print_list']) && $_REQUEST['print_list'] == 'y') {
    // add cache to print (do not duplicate items)
    if ( !isset($_SESSION['print_list']) || count($_SESSION['print_list']) == 0){
        $_SESSION['print_list'] = array();
    }

    if (onTheList($_SESSION['print_list'], $cache_id) == -1)
        array_push($_SESSION['print_list'], $cache_id);
}

if (isset($_REQUEST['print_list']) && $_REQUEST['print_list'] == 'n') {
    // remove cache from print list
    while (onTheList($_SESSION['print_list'], $cache_id) != -1)
        unset($_SESSION['print_list'][onTheList($_SESSION['print_list'], $cache_id)]);
    $_SESSION['print_list'] = array_values($_SESSION['print_list']);
}

$owner_id = $geocache->getOwner()->getUserId();
tpl_set_var('owner_id', $owner_id);




if ( $loggedUser && $loggedUser->getHomeCoordinates()->areCordsReasonable() ) {

    $view->setVar('distanceToCache', sprintf("%.2f", Gis::distanceBetween($loggedUser->getHomeCoordinates(), $geocache->getCoordinates())));
    $view->setVar('displayDistanceToCache', true);

} else {
    $view->setVar('displayDistanceToCache', false);
}



// check if there is geokret in this cache
$thatquery = "SELECT gk_item.id, name, distancetravelled as distance
    FROM gk_item INNER JOIN gk_item_waypoint ON (gk_item.id = gk_item_waypoint.id)
    WHERE gk_item_waypoint.wp = :v1 AND stateid<>1 AND stateid<>4 AND stateid <>5 AND typeid<>2 AND missing=0";
$params['v1']['value'] = (string) $geocache->getGeocacheWaypointId();
$params['v1']['data_type'] = 'string';
$s = $dbc->paramQuery($thatquery, $params);
unset($params); //clear to avoid overlaping on next paramQuery (if any))
$geokrety_all_count = $dbc->rowCount($s);
if ($geokrety_all_count == 0) {
    // no geokrets in this cache
    tpl_set_var('geokrety_begin', '<!--');
    tpl_set_var('geokrety_end', '-->');
    tpl_set_var('geokrety_content', '');
} else {
    // geokret is present in this cache
    $geokrety_content = '';
    $geokrety_all = $dbc->dbResultFetchAll($s);

    for ($i = 0; $i < $geokrety_all_count; $i++) {
        $geokret = $geokrety_all[$i];
        $geokrety_content .= "<img src=\"/images/geokret.gif\" alt=\"\">&nbsp;<a href='https://geokrety.org/konkret.php?id=" . $geokret['id'] . "'>" . $geokret['name'] . "</a> - " . tr('total_distance') . ": " . $geokret['distance'] . " km<br>";
    }
    tpl_set_var('geokrety_begin', '');
    tpl_set_var('geokrety_end', '');
    tpl_set_var('geokrety_content', $geokrety_content);
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


// NPA - nature protection areas
$npac = "0";
$npa_content = '';
if(count($geocache->getNatureRegions()) > 0){
    $npa_content .="<table width=\"90%\" border=\"0\" style=\"border-collapse: collapse; font-weight: bold;font-size: 14px; line-height: 1.6em\"><tr>
    <td align=\"center\" valign=\"middle\"><b>" . tr('npa_info') . " <font color=\"green\"></font></b>:<br></td><td align=\"center\" valign=\"middle\">&nbsp;</td></tr>";
    $npac = "1";
    foreach ($geocache->getNatureRegions() as $key => $npa) {
        $npa_content .= "<tr><td align=\"center\" valign=\"middle\"><font color=\"blue\"><a target=\"_blank\" href=\"http://" . $npa['npalink'] . "\">" . $npa['npaname'] . "</a></font><br>";
        $npa_content .="</td><td align=\"center\" valign=\"middle\"><img src=\"tpl/stdstyle/images/pnk/" . $npa['npalogo'] . "\"></td></tr>";
    }
    $npa_content .="</table>";
}

// Natura 2000

if (count($geocache->getNatura2000Sites()) > 0) {
    $npa_content .="<table width=\"90%\" border=\"0\" style=\"border-collapse: collapse; font-weight: bold;font-size: 14px; line-height: 1.6em\"><tr>
    <td width=90% align=\"center\" valign=\"middle\"><b>" . tr('npa_info') . " <font color=\"green\">NATURA 2000</font></b>:<br>";
    $npac = "1";
    foreach ($geocache->getNatura2000Sites() as $npa) {
        $npa_item = $config['nature2000link'];
        $npa_item = mb_ereg_replace('{linkid}', $npa['linkid'], $npa_item);
        $npa_item = mb_ereg_replace('{sitename}', $npa['npaSitename'], $npa_item);
        $npa_item = mb_ereg_replace('{sitecode}', $npa['npaSitecode'], $npa_item);
        $npa_content .= $npa_item . '<br>';
    }
    $npa_content .="</td><td align=\"center\" valign=\"middle\"><img src=\"tpl/stdstyle/images/misc/natura2000.png\"></td>
        </tr></table>";
}

if ($npac == "0") {

    tpl_set_var('hidenpa_start', '<!--');
    tpl_set_var('hidenpa_end', '-->');
    tpl_set_var('npa_content', '');
} else {

    tpl_set_var('hidenpa_start', '');
    tpl_set_var('hidenpa_end', '');
    tpl_set_var('npa_content', $npa_content);
}

$icons = $geocache->dictionary->getCacheTypeIcons();

//cache data
list($iconname) = getCacheIcon($usr['userid'], $geocache->getCacheId(), $geocache->getStatus(), $geocache->getOwner()->getUserId(), $icons[$geocache->getCacheType()]['icon']);
list($lat_dir, $lat_h, $lat_min) = help_latToArray($geocache->getCoordinates()->getLatitude());
list($lon_dir, $lon_h, $lon_min) = help_lonToArray($geocache->getCoordinates()->getLongitude());

$tpl_subtitle = htmlspecialchars($geocache->getCacheName(), ENT_COMPAT, 'UTF-8') . ' - ';




$view->setVar('loginToSeeMapMsg', mb_ereg_replace("{target}", urlencode("viewcache.php?cacheid=".$geocache->getCacheId()), tr('map_msg')));

$lat = $geocache->getCoordinates()->getLatitude();
$lon = $geocache->getCoordinates()->getLongitude();
$zoom = $config['maps']['cache_page_map']['zoom'];
$mapType = $config['maps']['cache_page_map']['source'];
$view->setVar('mapImgLink', "lib/staticmap.php?center=$lat,$lon&amp;zoom=$zoom&amp;size=170x170&amp;maptype=$mapType&amp;markers=$lat,$lon,mark-small-blue");




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
$lat = $geocache->getCoordinates()->getLatitude();
$lon = $geocache->getCoordinates()->getLongitude();
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






$view->setVar('cacheHiddenDate', $geocache->getDatePlaced()->format($ocConfig->getDateFormat()));
$view->setVar('cacheCreationDate', $geocache->getDateCreated()->format($ocConfig->getDateFormat()));
$view->setVar('cacheLastModifiedDate', $geocache->getLastModificationDate()->format($ocConfig->getDateFormat()));



if ($loggedUser && $loggedUser->getFoundGeocachesCount() >= $config['otherSites_minfinds']) {
    $view->setVar('otherSitesListing', $geocache->getFullOtherWaypointsList() );
}else{
    $view->setVar('otherSitesListing', []);
}





tpl_set_var('difficulty_icon_diff', icon_difficulty("diff", $geocache->getDifficulty()));
tpl_set_var('difficulty_icon_terr', icon_difficulty("terr", $geocache->getTerrain()));


tpl_set_var('total_number_of_logs', htmlspecialchars($geocache->getFounds() + $geocache->getNotFounds() + $geocache->getNotesCount(), ENT_COMPAT, 'UTF-8'));

// Personal cache notes
//user logged in?
if ($usr == true) {
    $s = $dbc->multiVariableQuery("SELECT `cache_notes`.`note_id` `note_id`,`cache_notes`.`date` `date`, `cache_notes`.`desc` `desc`, `cache_notes`.`desc_html` `desc_html` FROM `cache_notes` WHERE `cache_notes` .`user_id`=:1 AND `cache_notes`.`cache_id`=:2", $usr['userid'], $cache_id);
    $cacheNotesRowCount = $dbc->rowCount($s);

    if ($cacheNotesRowCount > 0) {
        $notes_record = $dbc->dbResultFetchOneRowOnly($s);
    }

    tpl_set_var('note_content', "");
    tpl_set_var('CacheNoteE', '-->');
    tpl_set_var('CacheNoteS', '<!--');
    tpl_set_var('EditCacheNoteE', '');
    tpl_set_var('EditCacheNoteS', '');

    //edit user note...
    if (isset($_POST['edit'])) {
        tpl_set_var('CacheNoteE', '-->');
        tpl_set_var('CacheNoteS', '<!--');
        tpl_set_var('EditCacheNoteE', '');
        tpl_set_var('EditCacheNoteS', '');

        if ($cacheNotesRowCount > 0) {
            $note = $notes_record['desc'];
            tpl_set_var('noteid', $notes_record['note_id']);
        } else {
            $note = "";
        }
        tpl_set_var('note_content', $note);
    }

    //remove the user note from the cache
    if (isset($_POST['remove'])) {

        if ($cacheNotesRowCount > 0) {
            $note_id = $notes_record['note_id'];
            //remove
            XDb::xSql(
                "DELETE FROM `cache_notes` WHERE `note_id`= ? and user_id= ? ", $note_id, $usr['userid']);
            //display cache-page
            tpl_redirect('viewcache.php?cacheid=' . urlencode($cache_id));
            exit;
        }
    }

    //save current value of the user note
    if (isset($_POST['save'])) {

        $cnote = $_POST['note_content'];
        $cn = strlen($cnote);

        if ($cacheNotesRowCount != 0) {
            $note_id = $notes_record['note_id'];
            $dbc->multiVariableQuery("UPDATE `cache_notes` SET `date`=NOW(),`desc`=:1, `desc_html`=:2 WHERE `note_id`=:3", $cnote, '0', $note_id);
        }

        if ($cacheNotesRowCount == 0 && $cn != 0) {
            $dbc->multiVariableQuery("INSERT INTO `cache_notes` ( `note_id`, `cache_id`, `user_id`, `date`, `desc_html`, `desc`) VALUES ('', :1, :2, NOW(), :3, :4)", $cache_id, $usr['userid'], '0', $cnote);
        }

        //display cache-page
        tpl_redirect('viewcache.php?cacheid=' . urlencode($cache_id) . '#cache_note2');
        exit;
    }


    if ($cacheNotesRowCount != 0 && (!isset($_POST['edit']) || !isset($_REQUEST['edit']))) {
        tpl_set_var('CacheNoteE', '');
        tpl_set_var('CacheNoteS', '');
        tpl_set_var('EditCacheNoteE', '-->');
        tpl_set_var('EditCacheNoteS', '<!--');


        $note_desc = $notes_record['desc'];

        if ($notes_record['desc_html'] == '0'){
            $note_desc = htmlspecialchars($note_desc, ENT_COMPAT, 'UTF-8');
        } else {
            require_once($rootpath . 'lib/class.inputfilter.php');
            $myFilter = new InputFilter($allowedtags, $allowedattr, 0, 0, 1);
            $note_desc = $myFilter->process($note_desc);
        }
        tpl_set_var('notes_content', nl2br($note_desc));
    }
} else {
    tpl_set_var('note_content', "");
    tpl_set_var('CacheNoteE', '-->');
    tpl_set_var('CacheNoteS', '<!--');
    tpl_set_var('EditCacheNoteE', '-->');
    tpl_set_var('EditCacheNoteS', '<!--');
}
// end personal cache note
tpl_set_var('ignorer_count', $geocache->getIgnoringUsersCount());
tpl_set_var('notes_icon', $notes_icon);
tpl_set_var('save_icon', $save_icon);
tpl_set_var('search_icon', $search_icon);




if (($usr['admin'] == 1)) {
    $showhidedel_link = ""; //no need to hide/show deletion for COG (they always see deletions)
} else {
    $del_count = $dbc->multiVariableQueryValue('SELECT count(*) number FROM `cache_logs` WHERE `deleted`=1 and `cache_id`=:1', 0, $geocache->getCacheId());
    if ($del_count == 0) {
        $showhidedel_link = ""; //don't show link if no deletion '
    } else {
        if (isset($_SESSION['showdel']) && $_SESSION['showdel'] == 'y') {
            $showhidedel_link = $hide_del_link;
        } else {
            $showhidedel_link = $show_del_link;
        }
        $showhidedel_link = str_replace('{thispage}', 'viewcache.php', $showhidedel_link); //$show_del_link is defined in viecache.inc.php - for both viewlogs and viewcashes .php
    }
}

tpl_set_var('showhidedel_link', mb_ereg_replace('{cacheid}', htmlspecialchars(urlencode($cache_id), ENT_COMPAT, 'UTF-8'), $showhidedel_link));
tpl_set_var('new_log_entry_link', mb_ereg_replace('{cacheid}', htmlspecialchars(urlencode($cache_id), ENT_COMPAT, 'UTF-8'), $new_log_entry_link));






$HideDeleted = true;
if(isset($_SESSION['showdel']) && $_SESSION['showdel'] == 'y'){
   $HideDeleted = false;
}

//now include also those deleted due to displaying this type of record for all unless hide_deletions is on
if (($usr['admin'] == 1) || ($HideDeleted == false)) {
    $query_hide_del = "";  //include deleted
} else {
    $query_hide_del = "`deleted`=0 AND"; //exclude deleted
}

$number_logs = $dbc->multiVariableQueryValue(
    "SELECT count(*) number FROM `cache_logs` WHERE " . $query_hide_del . " `cache_id`=:1 "
    , 0, $geocache->getCacheId());

tpl_set_var('logEnteriesCount', $number_logs);

if ($number_logs > $logs_to_display) {
    tpl_set_var('viewlogs_last', mb_ereg_replace('{cacheid_urlencode}', htmlspecialchars(urlencode($cache_id), ENT_COMPAT, 'UTF-8'), $viewlogs_last));
    tpl_set_var('viewlogs', mb_ereg_replace('{cacheid_urlencode}', htmlspecialchars(urlencode($cache_id), ENT_COMPAT, 'UTF-8'), $viewlogs));

    $viewlogs_from = $dbc->multiVariableQueryValue(
        "SELECT id FROM cache_logs
        WHERE " . $query_hide_del . " cache_id=:1
        ORDER BY date DESC, id
        LIMIT ".XDb::xEscape($logs_to_display),
        -1, $cache_id );

    tpl_set_var('viewlogs_from', $viewlogs_from);
} else {
    tpl_set_var('viewlogs_last', '');
    tpl_set_var('viewlogs', '');
    tpl_set_var('viewlogs_from', '');
}

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






// show mp3 files for PodCache
if ($geocache->getMp3count() > 0) {
    if (isset($_REQUEST['mp3_files']) && $_REQUEST['mp3_files'] == 'no'){
        tpl_set_var('mp3_files', "");
    }else{
        tpl_set_var('mp3_files', viewcache_getmp3table($cache_id, $geocache->getMp3count()));
    }
    tpl_set_var('hidemp3_start', '');
    tpl_set_var('hidemp3_end', '');
}
else {
    tpl_set_var('mp3_files', '<br>');
    tpl_set_var('hidemp3_start', '<!--');
    tpl_set_var('hidemp3_end', '-->');
}


// show pictures
if ($geocache->getPicturesCount() == 0 || (isset($_REQUEST['print']) && isset($_REQUEST['pictures']) && $_REQUEST['pictures'] == 'no')) {
    tpl_set_var('pictures', '<br>');
    tpl_set_var('hidepictures_start', '<!--');
    tpl_set_var('hidepictures_end', '-->');
} else {
    if (isset($_REQUEST['spoiler_only']) && $_REQUEST['spoiler_only'] == 1){
        $spoiler_only = true;
    } else {
        $spoiler_only = false;
    }

    if ($usr == false && $hide_coords) {
        $disable_spoiler_view = true; //hide any kind of spoiler if usr not logged in
    } else {
        $disable_spoiler_view = false;
    }

    if (isset($_REQUEST['pictures']) && $_REQUEST['pictures'] == 'big'){
        tpl_set_var('pictures', viewcache_getfullsizedpicturestable($cache_id, true, $spoiler_only, $cache_record['picturescount'], $disable_spoiler_view));
    } elseif (isset($_REQUEST['pictures']) && $_REQUEST['pictures'] == 'small'){
        tpl_set_var('pictures', viewcache_getpicturestable($cache_id, true, true, $spoiler_only, true, $cache_record['picturescount'], $disable_spoiler_view));
    } elseif (isset($_REQUEST['pictures']) && $_REQUEST['pictures'] == 'no'){
        tpl_set_var('pictures', "");
    } else {
        tpl_set_var('pictures', viewcache_getpicturestable($cache_id, true, true, false, false, $geocache->getPicturesCount(), $disable_spoiler_view));
    }
    tpl_set_var('hidepictures_start', '');
    tpl_set_var('hidepictures_end', '');
}





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





//check number of pictures in logs
$rspiclogs = $dbc->multiVariableQueryValue(
    "SELECT COUNT(*) FROM `pictures`,`cache_logs`
    WHERE `pictures`.`object_id`=`cache_logs`.`id`
        AND `pictures`.`object_type`=1 AND `cache_logs`.`cache_id`= :1", 0, $cache_id);

if ($rspiclogs != 0) {
    tpl_set_var('gallery', $gallery_icon . '&nbsp;' . $rspiclogs . 'x&nbsp;' . mb_ereg_replace('{cacheid}', htmlspecialchars(urlencode($cache_id), ENT_COMPAT, 'UTF-8'), $gallery_link));
} else {
    tpl_set_var('gallery', '');
}

$includeDeletedLogs = false;
if ($usr && !$HideDeleted && $usr['admin'] != 1) {
    $includeDeletedLogs = true;
}
if($usr['admin'] == 1){
    $includeDeletedLogs = true;
}
tpl_set_var('includeDeletedLogs', $includeDeletedLogs ? 1 : 0);


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
    $s = $dbc->multiVariableQuery(
        "SELECT * FROM `cache_watches` WHERE `cache_id`=:1 AND `user_id`=:2 LIMIT 1",
        $cache_id, $usr['userid']);

    if ($dbc->rowCount($s) == 0) {
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
    $s = $dbc->multiVariableQuery("SELECT `cache_id` FROM `cache_ignore` WHERE `cache_id`=:1 AND `user_id`=:2 LIMIT 1",
        $cache_id, $usr['userid']);

    if ($dbc->rowCount($s) == 0) {
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
    $printt = tr('print');
    $addToPrintList = tr('add_to_list');
    $removeFromPrintList = tr('remove_from_list');

    if (isset($_SESSION['print_list'])) {
        $sesPrintList = $_SESSION['print_list'];
    } else {
        $sesPrintList = array();
    }

    if (onTheList($sesPrintList, $cache_id) == -1) {
        $print_list = "viewcache.php?cacheid=$cache_id&amp;print_list=y";
        $print_list_label = $addToPrintList;
        $print_list_icon = 'images/actions/list-add';
    } else {
        $print_list = "viewcache.php?cacheid=$cache_id&amp;print_list=n";
        $print_list_label = $removeFromPrintList;
        $print_list_icon = 'images/actions/list-remove';
    }


    $cache_menu = array(
        'title' => tr('cache_menu'),
        'menustring' => tr('cache_menu'),
        'siteid' => 'cachelisting',
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
                'title' => $print_list_label,
                'menustring' => $print_list_label,
                'visible' => true,
                'filename' => $print_list,
                'newwindow' => false,
                'siteid' => 'print_list_cache',
                'icon' => $print_list_icon
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
        'siteid' => 'cachelisting',
        'navicolor' => '#E8DDE4',
        'visible' => false,
        'filename' => 'viewcache.php',
        'submenu' => array(),
    );
}

tpl_set_var('log', $log_action);
tpl_set_var('watch', $watch_action);
tpl_set_var('report', isset($report_action) ? $report_action : '');
tpl_set_var('ignore', $ignore_action);
tpl_set_var('edit', $edit_action);
tpl_set_var('print', $print_action);
tpl_set_var('print_list', isset($print_list) ? $print_list : '');






//make the template and send it out

tpl_set_var('bodyMod', '');

// pass to tmplate if user is logged (hide other geocaching portals links)
if ($usr == false || $usr['userFounds'] < 99)
    $userLogged = 'none';
else
    $userLogged = 'block';

tpl_set_var('userLogged', $userLogged);



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


tpl_set_var('viewcache_js', "tpl/stdstyle/js/viewcache." . filemtime($rootpath . 'tpl/stdstyle/js/viewcache.js') . ".js");

tpl_BuildTemplate();

function onTheList($theArray, $item)
{
    for ($i = 0; $i < count($theArray); $i++) {
        if ($theArray[$i] == $item)
            return $i;
    }
    return -1;
}
