<?php
namespace Controllers;

use okapi\Facade;
use lib\Objects\GeoCache\GeoCache;
use lib\Objects\User\UserPreferences\MainMapSettings;
use lib\Objects\User\UserPreferences\UserPreferences;

/**
 * This class provides:
 * - data of cache under coords based on OKAPI (for map-click handler)
 * - OKAPI map tiles service
 */

class MainMapAjaxController extends BaseController
{

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

    public function getPopupData($bboxStr, $userId)
    {
        // map is only for logged users
        $this->checkUserLoggedAjax();

        $cache = $this->getCache($userId, $bboxStr);

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
            $resp->eventStartDate = $cache->getDatePlaced()->format(
                $this->ocConfig->getDateFormat());
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

    public function getMapTile($x, $y, $zoom, $userId)
    {
        if( $zoom > 21){ // OKAPI mapper max zoom
            //TODO
            die(); // zoom is too large
        }

        $this->searchParams['x'] = $x;    // x-index of tile
        $this->searchParams['y'] = $y;    // y-index of tile
        $this->searchParams['z'] = $zoom; // zoom of the tile

        /*
         * There are two "modes" (see the mapper_okapi for details):
         * - without "searchdata" - the normal version.
         * - with "searchdata" - ONLY "searchdata" is taken into account (All other
         * parameters are ignored).
         */
        if ($searchData = $this->getSearchDataParam()) {
            $this->loadSearchData($searchData); // load searchdata
        } else {
            $this->parseUrlSearchParams(); // parse filter params
        }

        # Get OKAPI's response and display it. Add proper Cache-Control headers.
        Facade::service_display('services/caches/map/tile', $userId, $this->searchParams);
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

    private function getCache($forUserId, $bboxStr)
    {
        /*
         * There are two "modes" (see the mapper_okapi for details):
         * - without "searchdata" - the normal version.
         * - with "searchdata" - ONLY "searchdata" is taken into account (All other
         * parameters are ignored).
         */
        if ($searchData = $this->getSearchDataParam()) {
            $this->loadSearchData($searchData);
        } else {
            $this->parseUrlSearchParams();
        }


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


        //call OKAPI
        /** @var \ArrayObject */
        $okapiResp = Facade::service_call(
            'services/caches/shortcuts/search_and_retrieve',
            $forUserId, $params);

        Facade::disable_error_handling(); // disable OKAPI error handler after Facade usage

        if (! is_a($okapiResp, "ArrayObject")) { // strange OKAPI return !?
            error_log(__METHOD__.": ERROR: strange OKAPI response - not an ArrayObject!");
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
        Facade::reenable_error_handling();

        // We need to transform OC's "searchdata" into OKAPI's "search set".
        // First, we need to determine if we ALREADY did that.
        // Note, that this is not exactly thread-efficient. Multiple threads may
        // do this transformation in the same time. However, this is done only once
        // for each searchdata, so we will ignore it.

        $cache_key = "OC_searchdata_" . $searchData;
        $set_id = Facade::cache_get($cache_key);
        if ($set_id === null) {
            // Read the searchdata file into a temporary table.

            $filepath = \okapi\Settings::get('VAR_DIR') . "/searchdata/" . $searchData;


            if (file_exists($filepath)) {

                \okapi\core\Db::execute("
                    create temporary table temp_" . $searchData . " (
                    cache_id integer primary key
                    ) engine=memory
                ");

                \okapi\core\Db::execute("
                        load data local infile '$filepath'
                        into table temp_" . $searchData . "
                        fields terminated by ' '
                        lines terminated by '\\n'
                        (cache_id)
                ");
            } else {
                //TODO: no such file!
            }

            // Tell OKAPI to import the table into its own internal structures.
            // Cache it for two hours.

            $set_info = Facade::import_search_set("temp_" . $searchData, 7200, 7200);
            $set_id = $set_info['set_id'];
            Facade::cache_set($cache_key, $set_id, 7200);
        }
        $this->searchParams['set_and'] = $set_id;
        $this->searchParams['status'] = "Available|Temporarily unavailable|Archived";

        Facade::disable_error_handling();
        return true;
    }

    /**
     * Find and parse the searchdata key in URL
     * @return string search data key or null if there is no searchdata param
     */
    private function getSearchDataParam()
    {
        if ( isset($_GET['searchdata']) &&
            preg_match('/^[a-f0-9]{6,32}/', $_GET['searchdata'])){

            return $_GET['searchdata'];

        } else {
            return null;
        }
    }

    /**
     * Parse map filter params and convert it to okapi search params
     */
    private function parseUrlSearchParams()
    {

        // exIgnored - convert to OKAPI's "exclude_ignored".
        if (isset($_GET['exIgnored'])){
            $this->searchParams['ignored_status'] = "notignored_only";
        }

        // exMyOwn (hide user's own caches) - convert to OKAPI's "exclude_my_own" parameter.
        if (isset($_GET['exMyOwn'])) {
            $this->searchParams['exclude_my_own'] = "true";
        }

        // filter out found or not yet found caches
        if ( isset($_GET['exFound'])) {

            if ( isset($_GET['exNoYetFound'])) {
                // exclude found && notAttendYet = empty set of caches
                $this->ajaxErrorResponse("Search params are contradictory", 400);
            } else {
                $this->searchParams['found_status'] = "notfound_only";
            }

        } else if ( isset($_GET['exNoYetFound'])) {
            $this->searchParams['found_status'] = "found_only";
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
        if ( isset($_GET['rating']) && preg_match('/^[1-4]-[1-5]|X$/', $_GET['rating']) ){
            $this->searchParams['rating'] = $_GET['rating'];
        }

        // powertrail_ids (only caches from powerTrails with id) - convert to OKAPI's "powertrail_ids" param.
        if ( isset($_GET['csId']) &&
             preg_match('/^[0-9]+(\|[0-9]+)*$/', $_GET['csId']) ) {

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
