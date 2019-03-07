<?php
namespace src\Controllers;

use okapi\Facade;
use src\Models\Coordinates\GeoCode;
use src\Models\GeoCache\GeoCache;
use src\Models\OcConfig\OcConfig;
use src\Models\User\UserPreferences\MainMapSettings;
use src\Models\User\UserPreferences\UserPreferences;
use src\Utils\Text\Formatter;
use src\Utils\Debug\Debug;

/**
 * This class provides:
 * - data of cache under coords based on OKAPI (for map-click handler)
 * - OKAPI map tiles service
 */

class MainMapAjaxController extends BaseController
{
    const RATING_REGEX = '/^[1-4]-[1-5]|X$/';
    const SEARCHDATA_REGEX = '/^[a-f0-9]{6,32}$/';
    const GEOPATH_ID_REGEX = '/^[0-9]+(\|[0-9]+)*$/';
    const BBOX_REGEX = '/^(-?\d+\.?\d*\|){3}-?\d+\.?\d*$/';

    private $searchParams = [];

    public function isCallableFromRouter($actionName)
    {
        return true;
    }

    public function __construct(){
        parent::__construct();
    }

    public function index()
    {
        $this->ajaxErrorResponse("No index!", 404);
    }

    public function getPopupData($bboxStr, $userUuid)
    {
        // map is only for logged users
        $this->checkUserLoggedAjax();

        if(!preg_match(self::BBOX_REGEX, $bboxStr)){
            $this->ajaxErrorResponse('Incorrect bbox!', 500);
            exit;
        }

        $cache = $this->getCache($userUuid, $bboxStr);

        if(is_null($cache)){
            $this->ajaxJsonResponse(null);
        }

        $resp = new \stdClass();
        $resp->url = $cache->getCacheUrl();

        $resp->cacheName = $cache->getCacheName();
        $resp->cacheCode = $cache->getGeocacheWaypointId();
        $resp->cacheIcon = $cache->getCacheIcon();
        $resp->cacheUrl = $cache->getCacheUrl();
        $resp->cacheSizeDesc = tr($cache->getSizeTranslationKey());

        $resp->coords = new \stdClass();
        $resp->coords->lat = $cache->getCoordinates()->getLatitude();
        $resp->coords->lon = $cache->getCoordinates()->getLongitude();

        $resp->ratingDesc =
            $cache->getRatingVotes() < 3
            ? tr('not_available')
            : $cache->getRatingDesc();

        if($cache->isEvent()){
            $resp->isEvent = true;
            $resp->eventStartDate = Formatter::date($cache->getDatePlaced());
        }else{
            $resp->isEvent = false;
        }

        $resp->ownerProfileUrl = $cache->getOwner()->getProfileUrl();
        $resp->ownerName = $cache->getOwner()->getUserName();

        $resp->cacheFounds = $cache->getFounds();
        $resp->cacheNotFounds = $cache->getNotFounds();

        $resp->cacheRatingVotes = $cache->getRatingVotes();
        $resp->cacheRecosNumber = $cache->getRecommendations();
        if( $cache->isTitled() ) {
            global $titled_cache_period_prefix; //TODO: move it to the ocConfig
            $resp->titledDesc = tr($titled_cache_period_prefix.'_titled_cache');
        }

        if ($cache->isPowerTrailPart()) {
            $resp->powerTrailName = $cache->getPowerTrail()->getName();
            $resp->powerTrailIcon = $cache->getPowerTrail()->getFootIcon();
            $resp->powerTrailUrl = $cache->getPowerTrail()->getPowerTrailUrl();
        }

        $this->ajaxJsonResponse($resp);
    }

    public function getMapTile($x, $y, $zoom, $userUuid)
    {
        $this->checkUserLoggedAjax();

        if( $zoom > 21){ // OKAPI mapper max zoom
            //TODO
            die(); // zoom is too large
        }

        $this->searchParams['x'] = $x;    // x-index of tile
        $this->searchParams['y'] = $y;    // y-index of tile
        $this->searchParams['z'] = $zoom; // zoom of the tile

        if ($searchData = $this->getSearchDataParam()) {
            // searchData = set of caches - the rest of caches are excluded
            $this->loadSearchData($searchData);
        }

        $this->parseUrlSearchParams($userUuid); // parse filter params

        // Get OKAPI's response and display it. Add proper Cache-Control headers.
        Facade::service_display(
            'services/caches/map/tile',
            // Do NOT pass any other user's ID here! See OKAPI issue #496.
            $this->loggedUser->getUserId(),
            $this->searchParams
        );
    }

    public function getPlaceLocalization($place) {
        try {
            $placesDetails = GeoCode::fromOpenRouteService($place);
            $this->ajaxJsonResponse($placesDetails);
        } catch (\Exception $e) {
            $this->ajaxErrorResponse($e->getMessage(), 500);
        }
    }

    public function saveMapSettingsAjax()
    {

        $this->checkUserLoggedAjax();

        if (!isset($_POST['userMapSettings'])) {
            $this->ajaxErrorResponse('no filtersData var in JSON', 400);
        }

        $json = $_POST['userMapSettings'];

        if (UserPreferences::savePreferencesJson(MainMapSettings::KEY, $json)) {
            $this->ajaxSuccessResponse("Data saved");
        } else {
            $this->ajaxErrorResponse("Can't save a data", 500);
        }
    }

    private function getCache($forUserUuid, $bboxStr)
    {

        if ($searchData = $this->getSearchDataParam()) {
            // searchData = set of caches - the rest of caches are excluded
            $this->loadSearchData($searchData);
        }

        // load the rest of params
        $this->parseUrlSearchParams($forUserUuid);

        $this->searchParams['bbox'] = $bboxStr;
        $this->searchParams['limit'] = 1;


        // we need only id from OKAPI...
        $fields = 'code';

        $params = array();
        $params['search_method'] = 'services/caches/search/bbox';
        $params['search_params'] = json_encode($this->searchParams);
        $params['retr_method'] = 'services/caches/geocaches';
        $params['retr_params'] = '{"fields":"' . $fields . '"}';
        $params['wrap'] = 'false';


        // call OKAPI
        /** @var \ArrayObject */
        $okapiResp = Facade::service_call(
            'services/caches/shortcuts/search_and_retrieve',
            null,   // Do NOT pass a user ID here! See OKAPI issue #496.
            $params
        );

        if (! is_a($okapiResp, "ArrayObject")) { // strange OKAPI return !?
            Debug::errorLog("Strange OKAPI response - not an ArrayObject!");
            $this->ajaxErrorResponse('Internal error', 500);
            exit;
        }

        $iterator = $okapiResp->getIterator();

        if(!$iterator->valid()){
            // no caches found - just return empty result
            return null;
        }

        return GeoCache::fromWayPointFactory($iterator->key());
    }

    private function loadSearchData($searchData)
    {
        $filepath = OcConfig::getDynFilesPath() . "/searchdata/" . $searchData;

        $set_id = Facade::import_search_set_file($searchData, $filepath);

        $this->searchParams['set_and'] = $set_id;
        $this->searchParams['status'] = "Available|Temporarily unavailable|Archived";
    }

    /**
     * Find and parse the searchdata key in URL
     * @return string search data key or null if there is no searchdata param
     */
    private function getSearchDataParam()
    {
        if ( isset($_GET['searchdata']) &&
            preg_match(self::SEARCHDATA_REGEX, $_GET['searchdata'])){

            return $_GET['searchdata'];

        } else {
            return null;
        }
    }

    /**
     * Parse map filter params and convert it to okapi search params
     */
    private function parseUrlSearchParams($userUuid)
    {
        $this->searchParams['view_user_uuid'] = $userUuid;

        // exIgnored - convert to OKAPI's "exclude_ignored".
        //
        // We use an undocumented internal search parameter here,
        // that was implemented only for this purpose.
        // See https://github.com/opencaching/okapi/issues/496.

        if (isset($_GET['exIgnored'])){
            $this->searchParams['not_ignored_by'] = $userUuid;
        }

        // exMyOwn (hide user's own caches) - convert to OKAPI's "exclude_my_own" parameter.
        if (isset($_GET['exMyOwn'])) {
            $this->searchParams['owner_uuid'] = "-".$userUuid;
        }

        // filter out found or not yet found caches
        if ( isset($_GET['exFound'])) {

            if ( isset($_GET['exNoYetFound'])) {
                // exclude found && notAttendYet = empty set of caches
                $this->ajaxErrorResponse("Search params are contradictory", 400);
            } else {
                $this->searchParams['not_found_by'] = $userUuid;
            }

        } else if ( isset($_GET['exNoYetFound'])) {
            $this->searchParams['found_by'] = $userUuid;
        }

        // exNoGeokret - Convert to OKAPI's "with_trackables_only" parameter.
        if ( isset($_GET['exNoGeokret']) ) {
            $this->searchParams['with_trackables_only'] = "true";
        }

        // OKAPI's "status" filter.
        $status = ['Available']; // available is always present
        if ( !isset($_GET['exTempUnavail']) ) {
            $status[] = "Temporarily unavailable";
        }
        if ( !isset($_GET['exArchived']) ) {
            $status[] = "Archived";
        }
        $this->searchParams['status'] = implode("|", $status);

        // exNoGeokret - Convert to OKAPI's "with_trackables_only" parameter.
        if ( isset($_GET['exWithoutRecommendation']) ) {
            $this->searchParams['min_rcmds'] = "1";
        }

        // ftfHunter (hunt for FTFs) - convert to OKAPI's "ftf_hunter" parameter.
        if ( isset($_GET['ftfHunter']) ) {
            $this->searchParams['ftf_hunter'] = "true";

            // BTW, if we override "status" parameter, then we should also override
            // "rating" (all ftfs have "null" for rating). I don't do that though, to
            // stay 100% compatible with the previous implementation.
        }

        // powertrailOnly (hunt for powerTrails) - convert to OKAPI's "powertrail_only" parameter.
        if ( isset($_GET['powertrailOnly']) ) {
            $this->searchParams['powertrail_only'] = "true";
        }

        // min_score - convert to OKAPI's "rating" filter
        if ( isset($_GET['rating']) && preg_match(self::RATING_REGEX, $_GET['rating']) ){
            $this->searchParams['rating'] = $_GET['rating'];
        }


        // min_score - convert to OKAPI's "rating" filter
        if ( isset($_GET['size2']) ){
            //'none', 'nano', 'micro', 'small', 'regular', 'large', 'xlarge', 'other'.
            switch($_GET['size2']){
                case "nano":
                    $this->searchParams['size2'] = 'micro|small|regular|large|xlarge|other';
                    break;
                case "micro":
                    $this->searchParams['size2'] = 'small|regular|large|xlarge|other';
                    break;
                case "small":
                    $this->searchParams['size2'] = 'regular|large|xlarge|other';
                    break;
                case "regular":
                    $this->searchParams['size2'] = 'large|xlarge|other';
                    break;
            }
        }

        // powertrail_ids (only caches from powerTrails with id) - convert to OKAPI's "powertrail_ids" param.
        if ( isset($_GET['csId']) &&
             preg_match(self::GEOPATH_ID_REGEX, $_GET['csId']) ) {

                $this->searchParams['powertrail_ids'] = $_GET['csId'];
        }


        // exclusion of types - convert to OKAPI's "type" filter
        $typesToExclude = array();
        $types = ["Other","Traditional","Multi","Virtual","Webcam","Event","Quiz","Moving","Own"];

        foreach ($types as $type) {
            if( isset($_GET['exType'.$type]) ){
                $typesToExclude[] = $type;
            }
        }
        if ( !empty($typesToExclude) ) {
            $this->searchParams['type'] = "-" . implode("|", $typesToExclude);
        }
    }

}
