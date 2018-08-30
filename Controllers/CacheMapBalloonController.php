<?php
namespace Controllers;

use Controllers\BaseController;
use okapi\Facade;
use lib\Objects\GeoCache\GeoCache;

/**
 * This class provides data for map-click handlers
 */

class CacheMapBalloonController extends BaseController
{
    public function isCallableFromRouter($actionName)
    {
        return true;
    }

    public function __construct(){
        parent::__construct();

        // map is only for logged users
        $this->checkUserLoggedAjax();
    }

    public function index()
    {
        $this->ajaxErrorResponse("No index!", 404);
    }

    public function json($urlOnly=null)
    {
        $cache = $this->getCache();

        if(is_null($cache)){
            $this->ajaxJsonResponse(null);
        }

        $resp = new \stdClass();
        $resp->url = $cache->getCacheUrl();

        if (!$urlOnly) {
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
        }

        $this->ajaxJsonResponse($resp);
    }

    private function getCache()
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

        $this->getCommonParams();

        // we need only id from OKAPI...
        $fields = 'code';

        $params = array();
        $params['search_method'] = 'services/caches/search/bbox';
        $params['search_params'] = json_encode($this->search_params);
        $params['retr_method'] = 'services/caches/geocaches';
        $params['retr_params'] = '{"fields":"' . $fields . '"}';
        $params['wrap'] = 'false';


        //call OKAPI
        /** @var \ArrayObject */
        $okapiResp = Facade::service_call(
            'services/caches/shortcuts/search_and_retrieve',
            $this->user_id, $params);

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
        $this->search_params['set_and'] = $set_id;
        $this->search_params['status'] = "Available|Temporarily unavailable|Archived";

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

        // h_ignored - convert to OKAPI's "exclude_ignored".
        if ( isset($_GET['h_ignored']) && $_GET['h_ignored'] == "true"){
            $this->search_params['exclude_ignored'] = "true";
        }

        // h_avail, h_temp_unavail, h_arch ("hide available" etc.) - convert to
        // OKAPI's "status" filter.
        $tmp = array();
        if (isset($_GET['h_avail']) && $_GET['h_avail'] != "true") {
            $tmp[] = "Available";
        }

        if (isset($_GET['h_temp_unavail']) && $_GET['h_temp_unavail'] != "true") {
            $tmp[] = "Temporarily unavailable";
        }

        if (isset($_GET['h_arch']) && $_GET['h_arch'] != "true") {
            $tmp[] = "Archived";
        }

        $this->search_params['status'] = implode("|", $tmp);

        if (count($tmp) == 0) {
            $this->ajaxErrorResponse("Search params are contradictory", 400);
        }

        // min_score, max_score - convert to OKAPI's "rating" filter. This code
        // is weird, because values passed to min_score/max_score are weird...
        $t = floatval($_GET['min_score']);
        $min_rating = ($t < 0) ? "1" : (($t < 1) ? "2" : (($t < 1.5) ? "3" : (($t < 2.2) ? "4" : "5")));
        $t = floatval($_GET['max_score']);
        $max_rating = ($t < 0.7) ? "1" : (($t < 1.3) ? "2" : (($t < 2.2) ? "3" : (($t < 2.7) ? "4" : "5")));
        $this->search_params['rating'] = $min_rating . "-" . $max_rating;
        unset($t, $min_rating, $max_rating);

        // h_noscore - convert to OKAPI's "rating" parameter.
        if (!isset($_GET['h_noscore']) || $_GET['h_noscore'] == "true") {
            $this->search_params['rating'] = $this->search_params['rating'] . "|X";
        }

        // be_ftf (hunt for FTFs) - convert to OKAPI's "ftf_hunter" parameter.
        if (isset($_GET['be_ftf']) && $_GET['be_ftf'] == "true") {
            $this->search_params['ftf_hunter'] = "true";

            // Also, override previously set "status" filter. This behavior is
            // compatible with what previous mapper scripts did.

            $this->search_params['status'] = "Available";

            // BTW, if we override "status" parameter, then we should also override
            // "rating" (all ftfs have "null" for rating). I don't do that though, to
            // stay 100% compatible with the previous implementation.
        }

        // powertrail_only (hunt for powerTrails) - convert to OKAPI's "powertrail_only" parameter.
        if (isset($_GET['powertrail_only']) && $_GET['powertrail_only'] == "true") {
            $this->search_params['powertrail_only'] = "true";
        }

        // powertrail_ids (only caches from powerTrails with id) - convert to OKAPI's "powertrail_ids" param.
        if ( isset($_GET['powertrail_ids']) &&
            preg_match('/^[0-9]+(\|[0-9]+)*$/', $_GET['powertrail_ids']) ) {
                $this->search_params['powertrail_ids'] = $_GET['powertrail_ids'];
        }

        // h_nogeokret - Convert to OKAPI's "with_trackables_only" parameter.
        if (isset($_GET['h_nogeokret']) && $_GET['h_nogeokret'] == 'true') {
            $this->search_params['with_trackables_only'] = "true";
        }

        // h_?, where ? is a single letter - hide a specific cache type.
        // Convert to OKAPI's "type" parameter.
        $types_to_hide = array();
        $mapping = array(
            'u' => "Other",
            't' => "Traditional",
            'm' => "Multi",
            'v' => "Virtual",
            'w' => "Webcam",
            'e' => "Event",
            'q' => "Quiz",
            'o' => "Moving",
            'owncache' => "Own"
        );

        // Note: Some are missing!
        foreach ($mapping as $letter => $type) {
            if (isset($_GET['h_' . $letter]) && ($_GET['h_' . $letter] == "true")) {
                $types_to_hide[] = $type;
            }
        }

        if (count($types_to_hide) > 0) {
            $this->search_params['type'] = "-" . implode("|", $types_to_hide);
        }

        unset($types_to_hide, $mapping, $letter, $type);

        // h_own (hide user's own caches) - convert to OKAPI's "exclude_my_own" parameter.
        if (isset($_GET['h_own']) && $_GET['h_own'] == "true") {
            $this->search_params['exclude_my_own'] = "true";
        }

        // h_found, h_noattempt - convert to OKAPI's "found_status" parameter.
        $h_found = (isset($_GET['h_found']) && $_GET['h_found'] == "true");
        $h_noattempt = (isset($_GET['h_noattempt']) && $_GET['h_noattempt'] == "true");
        if ($h_found && (! $h_noattempt)) {
            $this->search_params['found_status'] = "notfound_only";
        } elseif ((! $h_found) && $h_noattempt) {
            $this->search_params['found_status'] = "found_only";
        } elseif ((! $h_found) && (! $h_noattempt)) {
            $this->search_params['found_status'] = "either";
        } else {
            $this->ajaxErrorResponse("Search params are contradictory", 400);
        }
    } // input params are OK

    /**
     * Find and parse the rest of the params from URL
     */
    private function getCommonParams()
    {

        if( isset($_GET['screenW']) ){
            $this->screenWidth = (int) $_GET['screenW'];
        } else {
            $this->screenWidth = 1000;
        }

        if( isset($_GET['userid'])){
            $this->user_id = $_GET['userid'];
        }else{
            // userid is obligatory!
            $this->ajaxErrorResponse("UserId is obligatory!", 400);
            exit; // to be sure
        }

        $latmin = $_GET['latmin'];
        $lonmin = $_GET['lonmin'];
        $latmax = $_GET['latmax'];
        $lonmax = $_GET['lonmax'];

        if (($latmin == $latmax) && ($lonmin == $lonmax)) {
            // Special case for showing marker for specific cache - just single coordinate provided
            // Use small buffer to handle this case
            $latmin -= 0.00001;
            $lonmin -= 0.00001;
            $latmax += 0.00001;
            $lonmax += 0.00001;
        }

        $bbox[] = $latmin;
        $bbox[] = $lonmin;
        $bbox[] = $latmax;
        $bbox[] = $lonmax;

        $this->search_params['bbox'] = implode('|', $bbox);
        $this->search_params['limit'] = 1;
    }


}
