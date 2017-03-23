<?php

namespace Controllers;

use lib\Objects\GeoCache\GeoCache;
use lib\Objects\OcConfig\OcConfig;
use lib\Objects\GeoCache\Waypoint;
use Utils\Gis\Gis;
use Utils\Log\CacheAccessLog;
use lib\Objects\GeoCache\GeoCacheDesc;
use lib\Objects\GeoCache\OpenChecker;
use lib\Objects\Coordinates\Coordinates;
use lib\Objects\GeoCache\PrintList;
use Controllers\ViewCacheController;
use Utils\Uri\Uri;

class ViewCacheController extends BaseController
{
    /** @var GeoCache $geocache */
    private $geocache = null;
    private $cache_id;

    private $userModifiedCacheCoords = null;


    public function __construct()
    {
        parent::__construct();
        $this->geocache = $this->loadGeocache();
    }

    public function index()
    {

        /* check if there is cache to display */
        if( $this->geocache == null ||
            (
                ( $this->geocache->getStatus() == GeoCache::STATUS_NOTYETAVAILABLE ||
                    $this->geocache->getStatus() == GeoCache::STATUS_BLOCKED
                ) &&
                (
                    $this->loggedUser == null ||
                    ( $this->loggedUser->getUserId() != $this->geocache->getOwnerId() && !$this->loggedUser->isAdmin() )
                )
            ) ||
            (
                $this->geocache->getStatus() == GeoCache::STATUS_WAITAPPROVERS &&
                (
                    $this->loggedUser == null ||
                    (
                        !$this->loggedUser->isAdmin() && !$this->loggedUser->isGuide() &&
                        $this->loggedUser->getUserId() != $this->geocache->getOwnerId()
                    )
                )
            )
        ){
            // there is no cache to display...
            tpl_set_tplname('viewcache/viewcache_error');
            tpl_BuildTemplate();
            exit(0);
        }


        //set here the template to process
        if (isset($_REQUEST['print']) && $_REQUEST['print'] == 'y'){
            tpl_set_tplname('viewcache/viewcache_print');
        }else{
            tpl_set_tplname('viewcache/viewcache');
        }
        set_tpl_subtitle(htmlspecialchars($this->geocache->getCacheName()) . ' - ');

        $this->geocache->incCacheVisits($this->loggedUser, $_SERVER["REMOTE_ADDR"]);

        // detailed cache access logging
        global $enable_cache_access_logs;
        if (@$enable_cache_access_logs) {
            $userId = $this->loggedUser ? $this->loggedUser->getUserId() : null;
            CacheAccessLog::logBrowserCacheAccess($this->geocache->getCacheId(), $userId, 'view_cache');
        }

        PrintList::HandleRequest($this->geocache->getCacheId());


        $this->cache_id = $this->geocache->getCacheId(); //TODO: refactor to $geocache...

        tpl_set_var('cacheid', $this->cache_id);

        $this->view->setVar('geoCache', $this->geocache);
        $this->view->setVar('isUserAuthorized', is_object($this->loggedUser) );
        $this->view->setVar('isAdminAuthorized', $this->loggedUser && $this->loggedUser->isAdmin() );
        $this->view->setVar('displayPrePublicationAccessInfo', $this->loggedUser && $this->loggedUser->isAdmin() );

        $this->view->setVar('ownerId', $this->geocache->getOwner()->getUserId());
        $this->view->setVar('ownerName', htmlspecialchars($this->geocache->getOwner()->getUserName()));

        global $hide_coords;
        $this->view->setVar('alwaysShowCoords', !$hide_coords);
        $this->view->setVar('cachename', htmlspecialchars($this->geocache->getCacheName()));

        $this->view->setVar('diffTitle', tr('task_difficulty').': '.$this->geocache->getDifficulty()/2) . ' ' .tr('out_of') . ' ' . '5.0';
        $this->view->setVar('terrainTitle', tr('terrain_difficulty').': '.$this->geocache->getTerrain()/2) . ' ' .tr('out_of') . ' ' . '5.0';
        $this->view->setVar('cacheMainIcon',$this->geocache->getCacheIcon($this->loggedUser));

        tpl_set_var('altitude', $this->geocache->getAltitudeObj()->getAltitude());

        $this->view->setVar('cacheHiddenDate', $this->geocache->getDatePlaced()->format($this->ocConfig->getDateFormat()));
        $this->view->setVar('cacheCreationDate', $this->geocache->getDateCreated()->format($this->ocConfig->getDateFormat()));
        $this->view->setVar('cacheLastModifiedDate', $this->geocache->getLastModificationDate()->format($this->ocConfig->getDateFormat()));

        tpl_set_var('total_number_of_logs', $this->geocache->getFounds() + $this->geocache->getNotFounds() + $this->geocache->getNotesCount());

        if($this->geocache->isAdopted()){
            $this->view->setVar('founderId', $this->geocache->getFounder()->getUserId());
            $this->view->setVar('founderName', htmlspecialchars($this->geocache->getFounder()->getUserName()));
        }

        $this->view->setVar('hideLogbook',isset($_REQUEST['logbook']) && $_REQUEST['logbook'] == 'no');
        $this->view->setVar('viewcache_js', Uri::getLinkWithModificationTime('tpl/stdstyle/viewcache/viewcache.js'));
        $this->view->setVar('viewcache_css', Uri::getLinkWithModificationTime('tpl/stdstyle/viewcache/viewcache.css'));


        $this->processUserCoordsModification();
        $this->processDistanceToCache();
        $this->processCacheRating();
        $this->processDetailedCoords();
        $this->processExternalMaps();
        $this->processOtherSites();
        $this->processTitled();
        $this->processUserNote();
        $this->processDesc();
        $this->processOpenChecker();
        $this->processWayPoints();
        $this->processOcTeamComments();
        $this->processPics();
        $this->processHint();
        $this->processLogs();
        $this->processUserMenu();
        $this->processGeoPaths();
        tpl_BuildTemplate();

    }

    private function loadGeocache()
    {
        if (isset($_REQUEST['cacheid'])) {
            return GeoCache::fromCacheIdFactory( $_REQUEST['cacheid'] );

        } elseif (isset($_REQUEST['uuid'])) {
            return GeoCache::fromUUIDFactory( $_REQUEST['uuid'] );

        } elseif (isset($_REQUEST['wp'])) {
            return GeoCache::fromWayPointFactory( $_REQUEST['wp'] );
        }
        return null;
    }

    private function processOcTeamComments()
    {

        if($this->loggedUser && $this->loggedUser->isAdmin()){

            if ( isset($_POST['rr_comment']) && !empty($_POST['rr_comment']) ) {

                // add OC Team comment
                GeoCacheDesc::UpdateAdminComment( $this->geocache, $_POST['rr_comment'], $this->loggedUser);
                $this->view->redirect(Uri::getCurrentUri());

            }elseif ( isset($_GET['rmAdminComment']) && isset($_GET['cacheid'])) {

                // remove OC Team comment
                GeoCacheDesc::RemoveAdminComment($this->geocache);
                $this->view->redirect(Uri::removeParam('rmAdminComment'));

            }
        }

    }

    private function processGeoPaths()
    {
        global $powerTrailModuleSwitchOn;

        // geoPath badge
        $geoPathSectionDisplay = false;

        if ($powerTrailModuleSwitchOn && $this->cache_id != null) {
            $geoPathsList = [];
            foreach (\powerTrailBase::checkForPowerTrailByCache($this->cache_id) as $pt) {
                $geoPath = new \stdClass();
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
            $this->view->setVar('geoPathsList', $geoPathsList);
        }
        $this->view->setVar('geoPathSectionDisplay', $geoPathSectionDisplay);

    }

    private function processUserMenu()
    {
        if ($this->loggedUser) {

            if ($this->geocache->getOwnerId() == $this->loggedUser->getUserId()) {
                $show_edit = true;
                $show_ignore = false;
                $show_watch = false;
                $showReportProblemButton = false;
            } else {
                $show_edit = $this->loggedUser->isAdmin();
                $show_ignore = true;
                $show_watch = true;
                $showReportProblemButton = true;
            }

            $this->view->setVar('showEditButton',$show_edit);
            $this->view->setVar('showWatchButton',$show_watch);
            $this->view->setVar('showIgnoreButton',$show_ignore);
            $this->view->setVar('showReportProblemButton',$showReportProblemButton);


            if($show_watch) {
                //is this cache watched by this user?

                if (!$this->geocache->isWatchedBy($this->loggedUser->getUserId())) {
                    $this->view->setVar('watchLink','watchcache.php?cacheid=' . $this->cache_id . '&amp;target=' . Uri::getCurrentUri(true));
                    $this->view->setVar('watchLabel',tr('watch'));
                } else {
                    $this->view->setVar('watchLink','removewatch.php?cacheid=' . $this->cache_id . '&amp;target=' . Uri::getCurrentUri(true));
                    $this->view->setVar('watchLabel',tr('watch_not'));
                }
            }

            if($show_ignore) {
                //is this cache ignored by this user?

                if(!$this->geocache->isIgnoredBy($this->loggedUser->getUserId())){

                    $this->view->setVar('ignoreLink',"addignore.php?cacheid=" . $this->cache_id . "&amp;target=" . Uri::getCurrentUri(true));
                    $this->view->setVar('ignoreLabel',tr('ignore'));
                } else {
                    $this->view->setVar('ignoreLink',"removeignore.php?cacheid=" . $this->cache_id . "&amp;target=" . Uri::getCurrentUri(true));
                    $this->view->setVar('ignoreLabel',tr('ignore_not'));
                }
            }

            $this->view->setVar('printListLabel',PrintList::IsOnTheList($this->geocache->getCacheId()) ?
                tr('remove_from_list'): tr('add_to_list'));

            $this->view->setVar('printListLink',PrintList::AddOrRemoveCacheUrl($this->geocache->getCacheId()));
            $this->view->setVar('printListIcon',PrintList::IsOnTheList($this->geocache->getCacheId()) ?
                        'images/actions/list-remove-16.png' : 'images/actions/list-add-16.png');

        }
    }

    private function processhint()
    {
        $showUnencryptedHint = isset($_REQUEST['nocrypt']) && $_REQUEST['nocrypt'] == 1;
        $this->view->setVar('showUnencryptedHint', $showUnencryptedHint);

        $hint = $this->geoCacheDesc->getHint();

        if(!$showUnencryptedHint){
            $hint = str_rot13_html($this->geoCacheDesc->getHint());

            //replace { and } to prevent replacing at view template processing!
            $hint = mb_ereg_replace('{', '&#0123;', $hint);
            $hint = mb_ereg_replace('}', '&#0125;', $hint);
        }

        $this->view->setVar('hintEncrypted', $this->geoCacheDesc->getHint());
        $this->view->setVar('hintDecrypted', $hint);
    }

    private function processPics()
    {
        global $hide_coords;

        $picturesToDisplay = null;
        if ($this->geocache->getPicturesCount() != 0 &&
            !( isset($_REQUEST['print']) && isset($_REQUEST['pictures']) && $_REQUEST['pictures'] == 'no' )) {

                //there are any pictures to display

                $hideSpoilersForAnonims = !$this->loggedUser && $hide_coords;
                $showOnlySpoilers = isset($_REQUEST['spoiler_only']) && $_REQUEST['spoiler_only'] == 1;
                $unHideSpoilersThumbs = $this->loggedUser && isset($_REQUEST['print']) &&  $_REQUEST['print'] = 'big' || $_REQUEST['print'] = 'small';

                $picturesToDisplay = $this->geocache->getPicturesList($showOnlySpoilers, $hideSpoilersForAnonims, $unHideSpoilersThumbs);

                $this->view->setVar('displayBigPictures',
                    isset($_REQUEST['print']) && $_REQUEST['print']=='y' && isset($_REQUEST['pictures']) && $_REQUEST['pictures'] == 'big');
            }
            $this->view->setVar('picturesToDisplay', $picturesToDisplay);
    }

    private function processWayPoints()
    {
        $waypointsList = Waypoint::GetWaypointsForCacheId($this->geocache);
        $this->view->setVar('waypointsList', $waypointsList);
        $this->view->setVar('cacheWithStages',
            $this->geocache->getCacheType() == GeoCache::TYPE_OTHERTYPE ||
            $this->geocache->getCacheType() == GeoCache::TYPE_QUIZ ||
            $this->geocache->getCacheType() == GeoCache::TYPE_MULTICACHE );
    }

    private function processOpenChecker()
    {
        if( OpenChecker::isEnabledInConfig() && $this->geocache->isOpenCheckerApplicable() ){
            $openChecker = OpenChecker::ForCacheIdFactory($this->geocache->getCacheId());
        }else{
            $openChecker = null;
        }
        $this->view->setVar('openChecker', $openChecker);
    }

    private function processDesc()
    {
        global $lang;

        // determine description language
        $availableDescLangs = mb_split(',', $this->geocache->getDescLanguagesList());

        // check if user requests other lang of cache desc...
        if ( isset($_REQUEST['desclang']) && (array_search($_REQUEST['desclang'], $availableDescLangs) !== false)) {
            $descLang = $_REQUEST['desclang'];

        } elseif ( array_search( mb_strtoupper($lang) , $availableDescLangs) ) { // or try current lang
            $descLang = mb_strtoupper($lang);

        } else { // use first available otherwise
            $descLang = $availableDescLangs[0];
        }

        $availableDescLangsLinks = [];
        foreach ($availableDescLangs as $l){
            $availableDescLangsLinks[mb_strtoupper($l)] = Uri::setOrReplaceParamValue('desclang', mb_strtoupper($l));
        }

        $this->view->setVar('usedDescLang', $descLang); // lang of presented description
        $this->view->setVar('availableDescLangs', $availableDescLangs);
        $this->view->setVar('availableDescLangsLinks', $availableDescLangsLinks);

        $this->geoCacheDesc = $this->geocache->getCacheDescription($descLang);
        $this->view->setVar('geoCacheDesc', $this->geoCacheDesc);

    }

    private function processLogs()
    {
        if(isset($_REQUEST['showdel'])){
            $_SESSION['showdel'] = $_REQUEST['showdel'];
        }

        $displayDeletedLogs = true;
        if( $this->loggedUser && $this->loggedUser->isAdmin() || !$this->geocache->hasDeletedLog() ){
            $showDeletedLogsDisplayLink = false; //admin always see deleted logs

        }else{
            $showDeletedLogsDisplayLink = true;

            if ( isset($_SESSION['showdel']) && $_SESSION['showdel'] == 'y'){
                //hide-link
                $deletedLogsDisplayLink = Uri::addAnchorName('log_start', Uri::setOrReplaceParamValue('showdel', 'n'));
                $deletedLogsDisplayText = tr('vc_HideDeletions');
            }else{
                //show link
                $deletedLogsDisplayLink = Uri::addAnchorName('log_start', Uri::setOrReplaceParamValue('showdel', 'y'));
                $deletedLogsDisplayText = tr('vc_ShowDeletions');

                $displayDeletedLogs = false;
            }

            $this->view->setVar('deletedLogsDisplayLink',$deletedLogsDisplayLink);
            $this->view->setVar('deletedLogsDisplayText',$deletedLogsDisplayText);
        }

        $logEnteriesCount = intval($this->geocache->getLogEntriesCount($displayDeletedLogs));

        $this->view->setVar('showDeletedLogsDisplayLink', $showDeletedLogsDisplayLink);
        $this->view->setVar('displayAllLogsLink', 0 < $logEnteriesCount );
        $this->view->setVar('logEnteriesCount', $logEnteriesCount);
        $this->view->setVar('displayDeletedLogs', $displayDeletedLogs);

    }

    private function processUserNote()
    {
        if($this->loggedUser){

            $userNoteText = '';
            if(isset($_POST['saveUserNote'])){

                $userNoteText = $_POST['userNoteText'];



                if(!empty($userNoteText)){
                    $this->geocache->saveUserNote($this->loggedUser->getUserId(), $userNoteText);


                } else {
                    // empty update = delete note
                    $this->geocache->deleteUserNote($this->loggedUser->getUserId());
                }
            }elseif(isset($_POST['removeUserNote'])){
                $this->geocache->deleteUserNote($this->loggedUser->getUserId());

            }else{
                $userNoteText = $this->geocache->getUserNote($this->loggedUser->getUserId());
            }

            $this->view->setVar('userNoteText', $userNoteText);
        }
    }


    private function processUserCoordsModification()
    {
        /** @var Coordinates $userModifiedCacheCoords */
        $this->userModifiedCacheCoords = null;
        if ($this->loggedUser && (
            $this->geocache->getCacheType() == GeoCache::TYPE_OTHERTYPE ||
            $this->geocache->getCacheType() == GeoCache::TYPE_QUIZ ||
            $this->geocache->getCacheType() == GeoCache::TYPE_MULTICACHE )) {

                $this->view->setVar('cacheCoordsModificationAllowed',true);

                // insert/edit modified coordinates
                if (isset($_POST['userModifiedCoordsSubmited']) &&
                    isset($_POST['userCoordsFinalLatitude']) &&
                    isset($_POST['userCoordsFinalLongitude']) ) {

                        $this->userModifiedCacheCoords = Coordinates::FromCoordsFactory($_POST['userCoordsFinalLatitude'], $_POST['userCoordsFinalLongitude']);
                        if($this->userModifiedCacheCoords){
                            $this->geocache->saveUserCoordinates($this->userModifiedCacheCoords, $this->loggedUser->getUserId());
                        }else{
                            //TODO: improper coords!?
                        }

                    }elseif ( isset($_POST['deleteUserModifiedCoords']) ){
                        // user requested to delete user-modified-ccords
                        $this->geocache->deleteUserCoordinates($this->loggedUser->getUserId());

                    }else{ //there are no new userCoords for this cache - check if user set something before
                        $this->userModifiedCacheCoords = $this->geocache->getUserCoordinates($this->loggedUser->getUserId());
                    }


            }else{
                $this->view->setVar('cacheCoordsModificationAllowed', false);
            }

            $this->view->setVar('userModifiedCacheCoords', $this->userModifiedCacheCoords);

    }

    private function processDistanceToCache()
    {
        if ( $this->loggedUser && $this->loggedUser->getHomeCoordinates()->areCordsReasonable() ) {

            $this->view->setVar('distanceToCache', sprintf("%.2f", Gis::distanceBetween($this->loggedUser->getHomeCoordinates(), $this->geocache->getCoordinates())));
            $this->view->setVar('displayDistanceToCache', true);

        } else {
            $this->view->setVar('displayDistanceToCache', false);
        }
    }

    private function processCacheRating()
    {
        if ($this->geocache->getRatingVotes() < 3) {
            // DO NOT show cache's score
            $score = tr('not_available');
            $scoreColor = "#000000";
        } else {
            switch($this->geocache->getScoreAsRatingNum()){
                case 1: $scoreColor = "#DD0000"; break;
                case 2: $scoreColor = "#F06464"; break;
                case 3: $scoreColor = "#DD7700"; break;
                case 4: $scoreColor = "#77CC00"; break;
                case 5: $scoreColor = "#00DD00"; break;
            }
            $score = $this->geocache->getScoreNameTranslation();
        }

        $this->view->setVar('scoreColor', $scoreColor);
        $this->view->setVar('score', $score);
    }

    private function processTitled()
    {
        global $titled_cache_period_prefix; //from config
        $this->view->setVar('titledDesc',tr($titled_cache_period_prefix.'_titled_cache'));
    }

    private function processDetailedCoords()
    {
        global $hide_coords;

        list($lat_dir, $lat_h, $lat_min) = help_latToArray($this->geocache->getCoordinates()->getLatitude());
        list($lon_dir, $lon_h, $lon_min) = help_lonToArray($this->geocache->getCoordinates()->getLongitude());


        if ($this->loggedUser || !$hide_coords) {

            if ($this->geocache->getCoordinates()->getLongitude() < 0) {
                $longNC = $this->geocache->getCoordinates()->getLongitude() * (-1);
                tpl_set_var('longitudeNC', $longNC);
            } else {
                tpl_set_var('longitudeNC', $this->geocache->getCoordinates()->getLongitude());
            }

            tpl_set_var('longitude', $this->geocache->getCoordinates()->getLongitude());
            tpl_set_var('latitude', $this->geocache->getCoordinates()->getLatitude());
            tpl_set_var('lon_h', $lon_h);
            tpl_set_var('lon_min', $lon_min);
            tpl_set_var('lonEW', $lon_dir);
            tpl_set_var('lat_h', $lat_h);
            tpl_set_var('lat_min', $lat_min);
            tpl_set_var('latNS', $lat_dir);
        }
    }

    private function processExternalMaps()
    {
        global $config;

        $externalMaps = [];
        if(!$this->userModifiedCacheCoords){
            $lat = $this->geocache->getCoordinates()->getLatitude();
            $lon = $this->geocache->getCoordinates()->getLongitude();
        }else{
            $lat = $this->userModifiedCacheCoords->getLatitude();
            $lon = $this->userModifiedCacheCoords->getLongitude();
        }
        foreach($config['maps']['external'] as $key => $value){
            if ( $value == 1 ) {
                $externalMaps[$key] = sprintf($config['maps']['external'][$key.'_URL'],
                    $lat, $lon,
                    $this->geocache->getCacheId(), $this->geocache->getWaypointId(),
                    urlencode($this->geocache->getCacheName()) );
            }
        }
        $this->view->setVar('externalMaps', $externalMaps);

        $zoom = $config['maps']['cache_page_map']['zoom'];
        $mapType = $config['maps']['cache_page_map']['layer'];

        $this->view->setVar('mapImgLink', "lib/staticmap.php?center=$lat,$lon&amp;zoom=$zoom&amp;size=170x170&amp;maptype=$mapType&amp;markers=$lat,$lon,mark-small-blue");

        $this->view->setVar('loginToSeeMapMsg', mb_ereg_replace("{target}", urlencode("viewcache.php?cacheid=".$this->geocache->getCacheId()), tr('map_msg')));

    }

    private function processOtherSites()
    {
        global $config;

        if ($this->loggedUser &&
		($this->loggedUser->getUserId() == $this->geocache->getOwnerId() ||
		$this->loggedUser->isAdmin() ||
		$this->loggedUser->isGuide() ||
		$this->loggedUser->getFoundGeocachesCount() >= $config['otherSites_minfinds'])) {
            $this->view->setVar('otherSitesListing', $this->geocache->getFullOtherWaypointsList() );
            $this->view->setVar('searchAtOtherSites', true);
        }else{
            $this->view->setVar('otherSitesListing', []);
            $this->view->setVar('searchAtOtherSites', false);

        }
    }

}
