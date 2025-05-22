<?php

namespace src\Controllers;

use powerTrailBase;
use src\Libs\CalendarButtons\CalendarButtonAssets;
use src\Libs\CalendarButtons\CalendarButtonFactory;
use src\Models\Coordinates\Coordinates;
use src\Models\GeoCache\GeoCache;
use src\Models\GeoCache\GeoCacheDesc;
use src\Models\GeoCache\GeoCacheLog;
use src\Models\GeoCache\OpenChecker;
use src\Models\GeoCache\PrintList;
use src\Models\GeoCache\Waypoint;
use src\Models\OcConfig\OcConfig;
use src\Utils\Gis\Gis;
use src\Utils\I18n\I18n;
use src\Utils\Log\CacheAccessLog;
use src\Utils\Map\StaticMap;
use src\Utils\Text\Formatter;
use src\Utils\Text\Rot13;
use src\Utils\Text\UserInputFilter;
use src\Utils\Uri\SimpleRouter;
use src\Utils\Uri\Uri;
use stdClass;
use src\Libs\CalendarButtons\SingleEventButton;
use okapi\Settings;

class ViewCacheController extends BaseController
{
    /** @var GeoCache */
    private $geocache = null;

    private $cache_id;

    /** @var Coordinates|null */
    private $userModifiedCacheCoords = null;

    private $infoMsg = null;

    private $errorMsg = null;

    /** @var GeoCacheDesc */
    private $geoCacheDesc;

    public function isCallableFromRouter(string $actionName): bool
    {
        // all public methods can be called by router
        return true;
    }

    public function ocTeamCommentForm($cacheId)
    {
        $this->redirectNotLoggedUsers();

        if (! $this->loggedUser->hasOcTeamRole()) {
            $this->displayCommonErrorPageAndExit("You're not an OcTeam member!");
        }

        $cache = GeoCache::fromCacheIdFactory($cacheId);

        if (! $cache) {
            $this->displayCommonErrorPageAndExit('No such geocache?!');
        }

        $this->view->setVar('cacheName', $cache->getCacheName());
        $this->view->setVar('cacheUrl', $cache->getCacheUrl());
        $this->view->setVar('cacheId', $cache->getCacheId());

        $this->view->setTemplate('viewcache/add_octeam_comment');
        $this->view->buildView();
    }

    public function saveOcTeamComments($cacheId)
    {
        $this->redirectNotLoggedUsers();

        if (! $this->loggedUser->hasOcTeamRole()) {
            $this->displayCommonErrorPageAndExit("You're not an OcTeam member!");
        }

        $cache = GeoCache::fromCacheIdFactory($cacheId);

        if (! $cache) {
            $this->displayCommonErrorPageAndExit('No such geocache?!');
        }

        if (isset($_POST['ocTeamComment']) && ! empty($_POST['ocTeamComment'])) {
            // add OC Team comment
            GeoCacheDesc::UpdateAdminComment(
                $cache,
                UserInputFilter::purifyHtmlString($_POST['ocTeamComment']),
                $this->loggedUser
            );

            $this->view->redirect($cache->getCacheUrl());
        }
    }

    public function rmOcTeamComments($cacheId)
    {
        $this->redirectNotLoggedUsers();

        if (! $this->loggedUser->hasOcTeamRole()) {
            $this->displayCommonErrorPageAndExit("You're not an OcTeam member!");
        }

        $cache = GeoCache::fromCacheIdFactory($cacheId);

        if (! $cache) {
            $this->displayCommonErrorPageAndExit('No such geocache?!');
        }

        // remove OC Team comment
        GeoCacheDesc::RemoveAdminComment($cache);
        $this->view->redirect($cache->getCacheUrl());
    }

    public function index()
    {
        $this->geocache = $this->loadGeocache();

        // check if there is cache to display
        if ($this->geocache == null
            || (
                (
                    $this->geocache->getStatus() == GeoCache::STATUS_NOTYETAVAILABLE
                    || $this->geocache->getStatus() == GeoCache::STATUS_BLOCKED
                )
                && (
                    $this->loggedUser == null
                    || ($this->loggedUser->getUserId() != $this->geocache->getOwnerId() && ! $this->loggedUser->hasOcTeamRole())
                )
            )
            || (
                $this->geocache->getStatus() == GeoCache::STATUS_WAITAPPROVERS
                && (
                    $this->loggedUser == null
                    || (
                        ! $this->loggedUser->hasOcTeamRole() && ! $this->loggedUser->isGuide()
                        && $this->loggedUser->getUserId() != $this->geocache->getOwnerId()
                    )
                )
            )
        ) {
            // there is no cache to display...
            $this->view->setTemplate('viewcache/viewcache_error');
            $this->view->buildView();

            exit(0);
        }

        if ($this->geocache->getCacheType() == GeoCache::TYPE_EVENT) {
            $this->view->addLocalCss(CalendarButtonAssets::getCss());
            $this->view->addLocalJs(CalendarButtonAssets::getJs());
        }

        //set here the template to process
        if (isset($_REQUEST['print']) && $_REQUEST['print'] == 'y') {
            $this->view->setTemplate('viewcache/viewcache_print');
        } else {
            $this->view->setTemplate('viewcache/viewcache');
            $this->view->loadJQuery();
            $this->view->loadJQueryUI();
            $this->view->loadFancyBox();

            if (isset($_REQUEST['infomsg'])) {
                $this->infoMsg = $_REQUEST['infomsg'];
            }

            if (isset($_REQUEST['errormsg'])) {
                $this->errorMsg = $_REQUEST['errormsg'];
            }
            $this->view->setVar('infoMsg', $this->infoMsg);
            $this->view->setVar('errorMsg', $this->errorMsg);
        }
        $this->view->setSubtitle(htmlspecialchars($this->geocache->getCacheName()) . ' - ');

        $this->geocache->incCacheVisits($this->loggedUser, $_SERVER['REMOTE_ADDR']);

        // detailed cache access logging
        CacheAccessLog::logBrowserCacheAccess($this->geocache, $this->loggedUser, CacheAccessLog::EVENT_VIEW_CACHE);

        PrintList::HandleRequest($this->geocache->getCacheId());

        $this->cache_id = $this->geocache->getCacheId(); //TODO: refactor to $geocache...

        tpl_set_var('cacheid', $this->cache_id);

        $this->view->setVar('geoCache', $this->geocache);
        $this->view->setVar('isUserAuthorized', is_object($this->loggedUser));
        $this->view->setVar('isAdminAuthorized', $this->loggedUser && $this->loggedUser->hasOcTeamRole());
        $this->view->setVar('displayPrePublicationAccessInfo', $this->loggedUser && $this->loggedUser->hasOcTeamRole());

        $this->view->setVar('ownerId', $this->geocache->getOwner()->getUserId());
        $this->view->setVar('ownerName', htmlspecialchars($this->geocache->getOwner()->getUserName()));

        $this->view->setVar('alwaysShowCoords', ! OcConfig::coordsHiddenForNonLogged());
        $this->view->setVar('cachename', htmlspecialchars($this->geocache->getCacheName()));

        $this->view->setVar('diffTitle', tr('task_difficulty') . ': ' . $this->geocache->getDifficulty() / 2 . ' ' . tr('out_of') . ' ' . '5.0');
        $this->view->setVar('terrainTitle', tr('terrain_difficulty') . ': ' . $this->geocache->getTerrain() / 2 . ' ' . tr('out_of') . ' ' . '5.0');
        $this->view->setVar('cacheMainIcon', $this->geocache->getCacheIcon($this->loggedUser));

        tpl_set_var('altitude', $this->geocache->getAltitude());

        $this->view->setVar(
            'cacheHiddenDate',
            Formatter::date($this->geocache->getDatePlaced())
        );

        if ($this->geocache->getCacheType() == GeoCache::TYPE_EVENT) {

            $descLang = $this->getDescLang();

            $descriptionObject = $this->geocache->getCacheDescription($descLang);

            if (is_null($descriptionObject)) {
                $descriptionObject = GeoCacheDesc::getEmptyDesc($this->geocache);
            }

            $description = $descriptionObject->getDescToDisplay();

            $singleEventButton = CalendarButtonFactory::createButton('single_event', [
                'name' => $this->geocache->getCacheName(),
                'description' => htmlspecialchars(strip_tags($description)),
                'startDate' => Formatter::formatDateTime($this->geocache->getDatePlaced(), "Y-m-d"),
                'timeZone' => date_default_timezone_get(),
                'location' => $this->geocache->getCoordinates()->getLatitude() . "," . $this->geocache->getCoordinates()->getLongitude(),
                'language' => I18n::getCurrentLang(),
                'label' => tr('add_to_calendar')
            ]);

            $this->view->setVar('addEventToCalendarButton', $singleEventButton->render());
        }

        $this->view->setVar(
            'cacheCreationDate',
            Formatter::date($this->geocache->getDateCreated())
        );
        $this->view->setVar(
            'cacheLastModifiedDate',
            Formatter::date($this->geocache->getLastModificationDate())
        );
        $this->view->setVar(
            'cachePublishedDate',
            empty($this->geocache->getDatePublished())
                ? ''
                : Formatter::date($this->geocache->getDatePublished())
        );

        tpl_set_var('total_number_of_logs', $this->geocache->getFounds() + $this->geocache->getNotFounds() + $this->geocache->getNotesCount());

        if ($this->geocache->isAdopted()) {
            $this->view->setVar('founderName', htmlspecialchars($this->geocache->getFounder()->getUserName()));
        }

        $this->view->setVar('hideLogbook', isset($_REQUEST['logbook']) && $_REQUEST['logbook'] == 'no');
        $this->view->setVar('viewcache_js', Uri::getLinkWithModificationTime('/views/viewcache/viewcache.js'));

        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/views/viewcache/viewcache.css'));

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
        $this->processPics();
        $this->processHint();
        $this->processLogs();
        $this->processUserMenu();
        $this->processGeoPaths();

        $this->processMeritBadgePopUp(); //pop-up on new badge level achievement

        $this->view->buildView();
    }

    /**
     * This function returns the map tile with marker on given coords - used only for viewcache
     * @param $lat
     * @param $lon
     */
    public function getStaticMapImage($lat, $lon)
    {
        global $config;

        $coords = Coordinates::FromCoordsFactory($lat, $lon);

        if (! $coords) {
            $this->displayCommonErrorPageAndExit('Wrong coords?');
        }

        $zoom = $config['maps']['cache_page_map']['zoom'];
        $mapType = $config['maps']['cache_page_map']['layer'];
        $size = [170, 170];

        StaticMap::displayMapWithMarkerAtCenter($coords, $zoom, $size, $mapType);
    }

    private function processMeritBadgePopUp()
    {
        if (! $this->isUserLogged()) {
            // there is no logged user
            $this->view->setVar('badgesPopupHtml', '');
            $this->view->setVar('badgesPopUp', false);

            return;
        }

        if (! isset($_REQUEST['badgesPopupFor']) || empty($_REQUEST['badgesPopupFor'])) {
            $this->view->setVar('badgesPopUp', false);

            return;
        }

        $this->view->setVar('badgesPopUp', true);

        $ctrlMeritBadge = new MeritBadgeController();

        $this->view->setVar(
            'badgesPopupHtml',
            $ctrlMeritBadge->prepareHtmlChangeLevelMeritBadges(explode(',', $_REQUEST['badgesPopupFor']), $this->loggedUser->getUserId())
        );
    }

    private function loadGeocache()
    {
        if (isset($_REQUEST['cacheid'])) {
            return GeoCache::fromCacheIdFactory($_REQUEST['cacheid']);
        }

        if (isset($_REQUEST['uuid'])) {
            return GeoCache::fromUUIDFactory($_REQUEST['uuid']);
        }

        if (isset($_REQUEST['wp'])) {
            return GeoCache::fromWayPointFactory($_REQUEST['wp']);
        }
    }

    private function processGeoPaths()
    {
        // geoPath badge
        $geoPathSectionDisplay = false;

        if (OcConfig::areGeopathsSupported() && $this->cache_id != null) {
            $geoPathsList = [];

            foreach (powerTrailBase::checkForPowerTrailByCache($this->cache_id) as $pt) {
                $geoPath = new stdClass();
                $geoPath->id = $pt['id'];
                $geoPath->name = $pt['name'];

                if ($pt['image'] == '') {
                    $geoPath->img = '/images/blue/powerTrailGenericLogo.png';
                } else {
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
            $showReportProblemButton = true; // "show-report" is always present

            if ($this->geocache->getOwnerId() == $this->loggedUser->getUserId()) {
                $show_edit = true;
                $show_ignore = false;
                $show_watch = false;
            } else {
                $show_edit = $this->loggedUser->hasOcTeamRole();

                if ($this->geocache->getStatus() == GeoCache::STATUS_WAITAPPROVERS) {
                    // skip watching/ignoring for non-published caches
                    $show_ignore = false;
                    $show_watch = false;
                } else {
                    $show_ignore = true;
                    $show_watch = true;
                }
            }

            $this->view->setVar('showEditButton', $show_edit);
            $this->view->setVar('showWatchButton', $show_watch);
            $this->view->setVar('showIgnoreButton', $show_ignore);
            $this->view->setVar('showReportProblemButton', $showReportProblemButton);

            if ($show_watch) {
                //is this cache watched by this user?
                if ($this->geocache->isWatchedBy($this->loggedUser->getUserId())) {
                    $this->view->setVar('watched', true);
                } else {
                    $this->view->setVar('watched', false);
                }
            }

            if ($show_ignore) {
                //is this cache watched by this user?
                if ($this->geocache->isIgnoredByUser($this->loggedUser)) {
                    $this->view->setVar('ignored', true);
                } else {
                    $this->view->setVar('ignored', false);
                }
            }

            $this->view->setVar('printListLabel', PrintList::IsOnTheList($this->geocache->getCacheId())
                ? tr('remove_from_list') : tr('add_to_list'));

            $this->view->setVar('printListLink', PrintList::AddOrRemoveCacheUrl($this->geocache->getCacheId()));
            $this->view->setVar('printListIcon', PrintList::IsOnTheList($this->geocache->getCacheId())
                ? 'images/actions/list-remove-16.png' : 'images/actions/list-add-16.png');
        }
    }

    private function processhint()
    {
        $showUnencryptedHint = isset($_REQUEST['nocrypt']) && $_REQUEST['nocrypt'] == 1;
        $this->view->setVar('showUnencryptedHint', $showUnencryptedHint);

        $hint = $this->geoCacheDesc->getHint();

        if (! $showUnencryptedHint) {
            $hint = Rot13::withoutHtml($this->geoCacheDesc->getHint());

            //replace { and } to prevent replacing at view template processing!
            $hint = mb_ereg_replace('{', '&#0123;', $hint);
            $hint = mb_ereg_replace('}', '&#0125;', $hint);
        }

        $this->view->setVar('hintEncrypted', $this->geoCacheDesc->getHint());
        $this->view->setVar('hintDecrypted', $hint);
    }

    private function processPics()
    {
        $picturesToDisplay = null;

        if ($this->geocache->getPicturesCount() != 0
            && ! (isset($_REQUEST['print'], $_REQUEST['pictures']) && $_REQUEST['pictures'] == 'no')) {
            //there are any pictures to display

            $hideSpoilersForAnonims = ! $this->loggedUser && OcConfig::coordsHiddenForNonLogged();
            $showOnlySpoilers = isset($_REQUEST['spoiler_only']) && $_REQUEST['spoiler_only'] == 1;
            $unHideSpoilersThumbs = $this->loggedUser && isset($_REQUEST['print']) && $_REQUEST['print'] = 'big' || $_REQUEST['print'] = 'small';

            $picturesToDisplay = $this->geocache->getPicturesList($showOnlySpoilers, $hideSpoilersForAnonims, $unHideSpoilersThumbs);

            $this->view->setVar(
                'displayBigPictures',
                isset($_REQUEST['print']) && $_REQUEST['print'] == 'y' && isset($_REQUEST['pictures']) && $_REQUEST['pictures'] == 'big'
            );
        }
        $this->view->setVar('picturesToDisplay', $picturesToDisplay);
    }

    private function processWayPoints()
    {
        $waypointsList = Waypoint::GetWaypointsForCacheId($this->geocache);
        $this->view->setVar('waypointsList', $waypointsList);
        $this->view->setVar(
            'cacheWithStages',
            $this->geocache->getCacheType() == GeoCache::TYPE_OTHERTYPE
            || $this->geocache->getCacheType() == GeoCache::TYPE_QUIZ
            || $this->geocache->getCacheType() == GeoCache::TYPE_MULTICACHE
        );
    }

    private function processOpenChecker()
    {
        if (OpenChecker::isEnabledInConfig() && $this->geocache->isOpenCheckerApplicable()) {
            $openChecker = OpenChecker::ForCacheIdFactory($this->geocache->getCacheId());
        } else {
            $openChecker = null;
        }
        $this->view->setVar('openChecker', $openChecker);
    }

    private function getAvailableDescLangs()
    {
        return mb_split(',', $this->geocache->getDescLanguagesList());
    }

    private function getDescLang(){

        $availableDescLangs = $this->getAvailableDescLangs();

        // check if user requests other lang of cache desc...
        if (isset($_REQUEST['desclang']) && (array_search($_REQUEST['desclang'], $availableDescLangs) !== false)) {
            $descLang = $_REQUEST['desclang'];
        } elseif (array_search(mb_strtoupper(I18n::getCurrentLang()), $availableDescLangs)) { // or try current lang
            $descLang = mb_strtoupper(I18n::getCurrentLang());
        } else { // use first available otherwise
            $descLang = $availableDescLangs[0];
        }

        return $descLang;
    }
    private function processDesc()
    {
        // determine description language
        $availableDescLangs = $this->getAvailableDescLangs();
        $descLang = $this->getDescLang();

        $availableDescLangsLinks = [];

        foreach ($availableDescLangs as $l) {
            $availableDescLangsLinks[mb_strtoupper($l)] = Uri::setOrReplaceParamValue('desclang', mb_strtoupper($l));
        }

        $this->view->setVar('usedDescLang', $descLang); // lang of presented description
        $this->view->setVar('availableDescLangs', $availableDescLangs);
        $this->view->setVar('availableDescLangsLinks', $availableDescLangsLinks);

        $this->geoCacheDesc = $this->geocache->getCacheDescription($descLang);

        if (is_null($this->geoCacheDesc)) {
            $this->geoCacheDesc = GeoCacheDesc::getEmptyDesc($this->geocache);
        }

        $this->view->setVar('geoCacheDesc', $this->geoCacheDesc);
    }

    private function processLogs()
    {
        if (isset($_REQUEST['showdel'])) {
            $_SESSION['showdel'] = $_REQUEST['showdel'];
        }

        $displayDeletedLogs = true;

        if ($this->loggedUser && $this->loggedUser->hasOcTeamRole() || ! $this->geocache->hasDeletedLog()) {
            $showDeletedLogsDisplayLink = false; //admin always see deleted logs
        } else {
            $showDeletedLogsDisplayLink = true;

            if (isset($_SESSION['showdel']) && $_SESSION['showdel'] == 'y') {
                //hide-link
                $deletedLogsDisplayLink = Uri::addAnchorName('log_start', Uri::setOrReplaceParamValue('showdel', 'n'));
                $deletedLogsDisplayText = tr('vc_HideDeletions');
            } else {
                //show link
                $deletedLogsDisplayLink = Uri::addAnchorName('log_start', Uri::setOrReplaceParamValue('showdel', 'y'));
                $deletedLogsDisplayText = tr('vc_ShowDeletions');

                $displayDeletedLogs = false;
            }

            $this->view->setVar('deletedLogsDisplayLink', $deletedLogsDisplayLink);
            $this->view->setVar('deletedLogsDisplayText', $deletedLogsDisplayText);
        }

        $logEntriesCount = intval($this->geocache->getLogEntriesCount($displayDeletedLogs));

        $this->view->setVar('showDeletedLogsDisplayLink', $showDeletedLogsDisplayLink);
        $this->view->setVar('displayAllLogsLink', 0 < $logEntriesCount);
        $this->view->setVar('logEntriesCount', $logEntriesCount);
        $this->view->setVar('displayDeletedLogs', $displayDeletedLogs);

        $logfilterConfig = $this->ocConfig->getLogfilterConfig();
        $this->view->setVar(
            'enableLogsFiltering',
            ! empty($logfilterConfig['enable_logs_filtering'])
        );

        if (
            $this->loggedUser
            && ! empty($logfilterConfig['show_activities_tooltip'])
        ) {
            $this->view->setVar('showActivitiesTooltip', true);

            // check if user has any activities based on cache type
            if ($this->geocache->getCacheType() == GeoCache::TYPE_EVENT) {
                $activityTypes = [
                    GeoCacheLog::LOGTYPE_ATTENDED,
                ];
            } else {
                $activityTypes = [
                    GeoCacheLog::LOGTYPE_FOUNDIT,
                    GeoCacheLog::LOGTYPE_DIDNOTFIND,
                ];
            }
            $userActivityLogsCount = $this->geocache->getLogsCountByType(
                $this->loggedUser,
                $activityTypes
            );

            if (sizeof($userActivityLogsCount) > 0) {
                // retrieve user activites
                $this->view->setVar(
                    'userActivityLogs',
                    (new GeoCacheLog())->getCacheLogsForUser(
                        $this->geocache->getCacheId(),
                        $this->loggedUser->getUserId(),
                        $activityTypes
                    )
                );
            }
        } else {
            $this->view->setVar('showActivitiesTooltip', false);
        }
    }

    private function processUserNote()
    {
        if ($this->loggedUser) {
            $userNoteText = '';

            if (isset($_POST['saveUserNote'])) {
                $userNoteText = $_POST['userNoteText'];

                if (! empty($userNoteText)) {
                    $this->geocache->saveUserNote($this->loggedUser->getUserId(), $userNoteText);
                } else {
                    // empty update = delete note
                    $this->geocache->deleteUserNote($this->loggedUser->getUserId());
                }
            } elseif (isset($_POST['removeUserNote'])) {
                $this->geocache->deleteUserNote($this->loggedUser->getUserId());
            } else {
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
            $this->geocache->getCacheType() == GeoCache::TYPE_OTHERTYPE
                || $this->geocache->getCacheType() == GeoCache::TYPE_QUIZ
                || $this->geocache->getCacheType() == GeoCache::TYPE_MULTICACHE
        )) {
            $this->view->setVar('cacheCoordsModificationAllowed', true);

            // insert/edit modified coordinates
            if (isset($_POST['userModifiedCoordsSubmited'], $_POST['userCoordsFinalLatitude'], $_POST['userCoordsFinalLongitude'])
                ) {
                $this->userModifiedCacheCoords = Coordinates::FromCoordsFactory($_POST['userCoordsFinalLatitude'], $_POST['userCoordsFinalLongitude']);

                if ($this->userModifiedCacheCoords) {
                    $this->geocache->saveUserCoordinates($this->userModifiedCacheCoords, $this->loggedUser->getUserId());
                }
                //TODO: improper coords!?
            } elseif (isset($_POST['deleteUserModifiedCoords'])) {
                // user requested to delete user-modified-ccords
                $this->geocache->deleteUserCoordinates($this->loggedUser->getUserId());
            } else { //there are no new userCoords for this cache - check if user set something before
                $this->userModifiedCacheCoords = $this->geocache->getUserCoordinates($this->loggedUser->getUserId());
            }
        } else {
            $this->view->setVar('cacheCoordsModificationAllowed', false);
        }

        $this->view->setVar('userModifiedCacheCoords', $this->userModifiedCacheCoords);
    }

    private function processDistanceToCache()
    {
        if ($this->loggedUser && $this->loggedUser->getHomeCoordinates()->areCordsReasonable()) {
            $this->view->setVar('distanceToCache', sprintf('%.2f', Gis::distanceBetween($this->loggedUser->getHomeCoordinates(), $this->geocache->getCoordinates())));
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
            $scoreColor = '#000000';
        } else {
            switch ($this->geocache->getScoreAsRatingNum()) {
                case 1:
                    $scoreColor = '#DD0000';
                    break;
                case 2:
                    $scoreColor = '#F06464';
                    break;
                case 3:
                    $scoreColor = '#5A5A5A';
                    break;
                case 4:
                    $scoreColor = '#77CC00';
                    break;
                case 5:
                    $scoreColor = '#00DD00';
                    break;
            }
            $score = $this->geocache->getScoreNameTranslation();
        }

        $this->view->setVar('scoreColor', $scoreColor);
        $this->view->setVar('score', $score);
    }

    private function processTitled()
    {
        if (OcConfig::getTitledCachePeriod() != 'none') {
            $this->view->setVar('titledDesc', tr(OcConfig::getTitledCachePeriod() . '_titled_cache'));
        } else {
            $this->view->setVar('titledDesc', '-');
        }
    }

    private function processDetailedCoords()
    {
        [$lat_dir, $lat_h, $lat_min] = $this->geocache->getCoordinates()->getLatitudeParts(Coordinates::COORDINATES_FORMAT_DEG_MIN);
        [$lon_dir, $lon_h, $lon_min] = $this->geocache->getCoordinates()->getLongitudeParts(Coordinates::COORDINATES_FORMAT_DEG_MIN);

        if ($this->loggedUser || ! OcConfig::coordsHiddenForNonLogged()) {
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
        if (! $this->userModifiedCacheCoords) {
            $lat = $this->geocache->getCoordinates()->getLatitude();
            $lon = $this->geocache->getCoordinates()->getLongitude();
        } else {
            $lat = $this->userModifiedCacheCoords->getLatitude();
            $lon = $this->userModifiedCacheCoords->getLongitude();
        }

        // read external maps urls
        $externalMaps = [];

        foreach (OcConfig::getMapExternalUrls() as $name => $url) {
            if ($name == "Flopp's Map") {
                $name = tr('flopps_map');
            }
            $externalMaps[$name] = sprintf(
                $url,
                $lat,
                $lon,
                $this->geocache->getCacheId(),
                $this->geocache->getWaypointId(),
                urlencode($this->geocache->getCacheName())
            );
        }
        $this->view->setVar('externalMaps', $externalMaps);

        if ($lat && $lon) {
            $this->view->setVar('mapImgLink', SimpleRouter::getLink(self::class, 'getStaticMapImage', [$lat, $lon]));
        } else {
            // if coordinates of the cache are not present - strange - maybe the very new cache
            $this->view->setVar('mapImgLink', null);
        }
        $this->view->setVar(
            'loginToSeeMapMsg',
            mb_ereg_replace('{target}', urlencode('viewcache.php?cacheid=' . $this->geocache->getCacheId()), tr('map_msg'))
        );
    }

    private function processOtherSites()
    {
        global $config;

        if ($this->loggedUser
            && ($this->loggedUser->getUserId() == $this->geocache->getOwnerId()
                || $this->loggedUser->hasOcTeamRole()
                || $this->loggedUser->isGuide()
                || $this->loggedUser->getFoundGeocachesCount() >= $config['otherSites_minfinds'])) {
            $this->view->setVar('otherSitesListing', $this->geocache->getFullOtherWaypointsList());
            $this->view->setVar('searchAtOtherSites', true);
        } else {
            $this->view->setVar('otherSitesListing', []);
            $this->view->setVar('searchAtOtherSites', false);
        }
    }

    /**
     * Displays event attenders page for cache with waypoint $cacheWp
     *
     * @param string $cacheWp
     */
    public function eventAttenders($cacheWp)
    {
        $cache = GeoCache::fromWayPointFactory($cacheWp);

        if (is_null($cache) || ! $cache->isEvent()) {
            $this->view->redirect('/');

            exit();
        }
        $this->view->setVar('cache', $cache);
        $this->view->setVar('willattenders', $cache->getAttenders(GeoCacheLog::LOGTYPE_WILLATTENDED));
        $this->view->setVar('attenders', $cache->getAttenders(GeoCacheLog::LOGTYPE_ATTENDED));
        $this->view->setTemplate('viewcache/event_attenders');
        $this->view->buildInMiniTpl();
    }
}
