<?php

namespace src\Controllers;

use src\Models\CacheSet\CacheSet;
use src\Models\ChunkModels\DynamicMap\DynamicMapModel;
use src\Models\Coordinates\Coordinates;
use src\Models\Coordinates\GeoCode;
use src\Models\GeoCache\GeoCache;
use src\Models\User\User;
use src\Models\User\UserPreferences\MainMapSettings;
use src\Models\User\UserPreferences\UserPreferences;
use src\Utils\Uri\Uri;

/**
 * Modes of mainMap init:
 *
 * - Default map: [no-params]
 *     start at user home coords - no more params - just logged user
 *
 * For all modes below filters state is not saved in DB!
 *
 * - Map with popup opened: [?openPopup&lat=<Y>&lon=<X>]
 *     start at given coords + try to open popup for given point (there should be cache found)
 *
 * - Map for geopath: [?cs=<csId>]
 *     map start at the coords of the center of given geopath - only this geopath caches are presented
 *
 * - Map for searchData [?searchdata=<hash>&bbox=minx|miny|,maxx|maxy]
 *     map used for presenting OC searches - searchdata is a hash of predefined
 *     set of caches stored in file + bbox is an extent at which map shoudl start
 *
 * - Map for given center coords + zoom: [?zoom=<zoom>&lat=<Y>&lon=<X>
 *     map starts at given center with given zoom
 *
 * - Map of another user: [?userid=<userId>]
 *     map presents the view from the point of given user perspective
 *
 * - Map with 150m circle: [?circle&lat=<Y>&lon=<X>]
 *     map is centered at given coords + displays movable circle with radius 75m
 */
class MainMapController extends BaseController
{
    private $mapJsParams;

    public function __construct($requestedAction)
    {
        parent::__construct();

        // map is only for logged users
        $this->redirectNotLoggedUsers();

        $this->mapJsParams = new MainMapJsParams();
    }

    public function isCallableFromRouter(string $actionName): bool
    {
        return true;
    }

    public function index()
    {
        $this->fullScreen();
    }

    /**
     * Display fullscreen map
     */
    public function fullScreen()
    {
        $this->view->setTemplate('mainMap/fullScreenMap');

        $this->mapJsParams->isFullScreenMap = true;

        $this->mapCommonInit();

        $this->view->setVar('mapParams', $this->getMapJsParamsJson());
        $this->view->buildInMiniTpl();
    }

    public function embeded()
    {
        $this->view->setTemplate('mainMap/embededMap');

        $this->mapJsParams->isFullScreenMap = false;

        $this->mapCommonInit();

        $this->view->setVar('mapParams', $this->getMapJsParamsJson());
        $this->view->buildView();
    }

    private function mapCommonInit()
    {
        $this->view->addHeaderChunk('openLayers5');
        $this->view->loadJQuery();

        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime(
                '/views/mainMap/mainMap.css'
            )
        );

        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime(
                '/views/mainMap/mainMapPopup.css'
            )
        );

        $this->view->addHeaderChunk('handlebarsJs');

        $this->view->addLocalJs(
            Uri::getLinkWithModificationTime('/views/mainMap/mainMap.js')
        );

        // find user for this map display
        $user = null;

        if (isset($_GET['userid'])) {
            $user = User::fromUserIdFactory($_GET['userid']);
        }

        if (! $user) {
            $user = $this->loggedUser;
        }

        $this->mapJsParams->userId = $user->getUserId();
        $this->mapJsParams->userUuid = $user->getUserUuid();
        $this->mapJsParams->username = $user->getUserName();

        $this->mapJsParams->isSearchEnabled = GeoCode::isGeocodeServiceAvailable();
        $mapModel = new DynamicMapModel();

        // load previously saved map settings
        $savedUserPrefs = UserPreferences::getUserPrefsByKey(MainMapSettings::KEY);
        $this->mapJsParams->initUserPrefs = $savedUserPrefs->getValues();
        $mapModel->setInitLayerName($this->mapJsParams->initUserPrefs['map']);

        // set map center based on requested coords&zoom
        if (isset($_GET['zoom'], $_GET['lat'], $_GET['lon'])) {
            // coords + zoom

            $mapCenter = Coordinates::FromCoordsFactory(
                floatval($_GET['lat']),
                floatval($_GET['lon'])
            );

            if (! $mapCenter) {
                $mapModel->setInfoMessage(tr('map_incorectMapParams'));
            } else {
                $zoom = intval($_GET['zoom']);
            }

            // set map center based on requested coords and open popup at center (used to show geocache)
        } elseif (isset($_GET['openPopup'], $_GET['lat'], $_GET['lon'])) {
            // opened popup
            $this->mapJsParams->openPopupAtCenter = true;

            $mapCenter = Coordinates::FromCoordsFactory(
                floatval($_GET['lat']),
                floatval($_GET['lon'])
            );

            if (! $mapCenter) {
                $mapModel->setInfoMessage(tr('map_incorectMapParams'));
            } else {
                $zoom = 14;
            }
        } elseif (isset($_GET['circle'], $_GET['lat'], $_GET['lon'])) {
            // 150m-circle at coords

            $mapCenter = Coordinates::FromCoordsFactory(
                floatval($_GET['lat']),
                floatval($_GET['lon'])
            );

            if (! $mapCenter) {
                $mapModel->setInfoMessage(tr('map_incorectMapParams'));
            } else {
                $zoom = 17;
                $this->mapJsParams->circle150m = true;
                $this->mapJsParams->initUserPrefs = null;  // clear filters
                $this->mapJsParams->dontSaveFilters = true;

                $mapModel->setInfoMessage(tr('map_circle150mMode'));
            }
        } elseif (isset($_GET['searchdata'], $_GET['bbox'])) {
            // searchData + bbox mode
            if (! preg_match(MainMapAjaxController::SEARCHDATA_REGEX, $_GET['searchdata'])
               || ! preg_match(MainMapAjaxController::BBOX_REGEX, $_GET['bbox'])) {
                // searchData error!
                $mapModel->setInfoMessage(tr('map_incorectMapParams'));
            } else {
                $this->mapJsParams->searchData = $_GET['searchdata'];
                $this->mapJsParams->initUserPrefs = null;  // clear filters
                $this->mapJsParams->dontSaveFilters = true;
                $mapModel->setInfoMessage(tr('map_searchResultsMode'));

                [$swLon, $swLat, $neLon, $neLat] = explode('|', $_GET['bbox']);

                $swCoord = Coordinates::FromCoordsFactory($swLat, $swLon);
                $neCoord = Coordinates::FromCoordsFactory($neLat, $neLon);

                $mapModel->setStartExtent($swCoord, $neCoord);
            }
        } elseif (isset($_GET['cs'])) {
            // only given geopath
            $geoPath = CacheSet::fromCacheSetIdFactory($_GET['cs']);

            $this->view->setVar('cacheSet', $geoPath);

            if (! $geoPath) {
                $mapModel->setInfoMessage(tr('map_incorectMapParams'));
            } else {
                $this->mapJsParams->cacheSetId = $geoPath->getId();
                $this->mapJsParams->initUserPrefs = null;  // clear filters
                $this->mapJsParams->dontSaveFilters = true;

                $mapCenter = $geoPath->getCoordinates();
                $zoom = 14;
            }
        } else {
            // default mode: map at user home coods
            $mapCenter = $user->getHomeCoordinates();
            $zoom = 11; //default zoom for user-home coords
        }

        if (isset($mapCenter) && is_object($mapCenter) && $mapCenter->areCordsReasonable()) {
            $mapModel->setCoords($mapCenter);
            $mapModel->setZoom($zoom);
        }

        $this->view->setVar('mapModel', $mapModel);
        $this->view->setVar('nanoFilterEnabled', GeoCache::nanoIsInUse());
    }

    private function getMapJsParamsJson()
    {
        return json_encode($this->mapJsParams, JSON_PRETTY_PRINT);
    }
}

/**
 * MainMap params used in JS
 */
class MainMapJsParams
{
    public $mapId = 'mainMap';          // id of the map div (in tpl)

    public $isFullScreenMap = false;    //

    public $userId = null;              // userId of user from map context

    public $userUuid = null;            // userUuid of user from map context

    public $username = '';              // username of the user from map context

    // modes:
    public $circle150m = false;         // display 150m circle at teh center

    public $openPopupAtCenter = false;  // preopen popup at center of the map

    public $searchData = null;          // uuid of searchdata to display on map

    public $cacheSetId = null;          // id of geopath to display on map

    public $initUserPrefs = null;       // user preferences object

    public $dontSaveFilters = false;    // skip saving user filter setting at server

    public $isSearchEnabled = false;    // condition to show search controls
}
