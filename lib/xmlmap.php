<?php
use lib\Objects\GeoCache\GeoCache;

$rootpath = "../";
require_once ($rootpath . 'lib/common.inc.php');
require_once ($rootpath . 'okapi/facade.php');

$obj = new tmp_Xmlmap();

/**
 * TODO: This should be moved to future mapController and I'm going to do it,
 * so the name and structure is temporary.
 *
 * This code:
 *  - found the cache located in requested bbox based on search params
 *  - returns this cache description in HTML to display in "mapInfo-baloon" or url of the full cache desc.
 *
 * It can handle params (from url - GET method):
 * - rspFormat - format of the response - possible values:
 *    - html - html data to display in the mapInfo baloon
 *    - url  - only url of the cache (if found); This value is use by default.
 * - searchdata - the previous search result to looking for a cache in
 * - latmin, latmax, lonmin, lonmax - bbox cords
 * - user_id - to identify the user for which the search params are intrepretated
 * - ...seach_params... - map search params
 *
 * If there is no cache found based on params listed above the empty result is returned.
 *
 * Example of the use case url:
 * /lib/xmlmap.php?
 * rspFormat=html
 * &latmin=54.3845561894001&lonmin=18.529129028320312&latmax=54.387754965527115&lonmax=18.534622192382812
 * &userid=8428
 * &h_u=false&h_t=false&h_m=false&h_v=false&h_w=false&h_e=false&h_q=false&h_o=false&h_owncache=false&h_ignored=true&h_own=true
 * &h_found=true&h_noattempt=false&h_nogeokret=false&h_avail=false&h_temp_unavail=true&h_arch=true&be_ftf=false
 * &powertrail_only=false&min_score=-3&max_score=3&h_noscore=true&
 *
 */
class tmp_Xmlmap
{

    private $search_params = array();
    private $user_id;
    private $rspFormat;
    private $screenWidth;

    /**
     * create the mapinfo-baloon content object
     * and start request processing
     */
    public function __construct()
    {

        /*
         * There are two "modes" (see the mapper_okapi for details):
         * - without "searchdata" - the normal version.
         * - with "searchdata" - ONLY "searchdata" is taken into account. All other
         * parameters are ignored.
         */
        $searchData = $this->getSearchData();
        if (! is_null($searchData)) {

            if (! $this->loadSearchData($searchData)) {
                die();
            }
        } else {
            if (! $this->parseUrlSearchParams()) {
                // parseUrlSearchParams returns with error or contradictory set of caches
                // there is nothing more to do here
                die();
            }
        }

        $this->getCommonParams();

        //cache attributes we need from OKAPI...
        $fields = 'code|name|location|type|size2|' .
                  'recommendations|rating_votes|rating|' .
                  'willattends|status|owner|founds|' .
                  'notfounds|internal_id|date_hidden';


        $params = array();
        $params['search_method'] = 'services/caches/search/bbox';
        $params['search_params'] = json_encode($this->search_params);
        $params['retr_method'] = 'services/caches/geocaches';
        $params['retr_params'] = '{"fields":"' . $fields . '"}';
        $params['wrap'] = 'false';

        switch ($this->rspFormat) {
            case 'html':
                $this->htmlFormat($params);
                return;

            case 'url':
            default:
                $this->getUrlOnly($params);
                return;
        }
    }

    /**
     * Find and parse the searchdata key in URL
     * @return search data key or null if there is no searchdata param
     */
    private function getSearchData()
    {
        if (isset($_GET['searchdata']) && preg_match('/^[a-f0-9]{6,32}/', $_GET['searchdata']))
            return $_GET['searchdata'];
        else
            return null;
    }

    /**
     * Find and parse the rest of the params from URL
     */
    private function getCommonParams()
    {

        if( isset($_GET['rspFormat']) ) //rspFormat can be not set
            $this->rspFormat = $_GET['rspFormat'];

        if( isset($_GET['screenW']) )
            $this->screenWidth = (int) $_GET['screenW'];
        else
            $this->screenWidth = 1000;

        $this->user_id = $_GET['userid'];

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

    /**
     * Parse map filter params and convert it to okapi search params
     * @return boolean - true on success or false if the search params are
     */
    private function parseUrlSearchParams()
    {

        // h_ignored - convert to OKAPI's "exclude_ignored".
        if ( isset($_GET['h_ignored']) && $_GET['h_ignored'] == "true")
            $this->search_params['exclude_ignored'] = "true";

        // h_avail, h_temp_unavail, h_arch ("hide available" etc.) - convert to
        // OKAPI's "status" filter.
        $tmp = array();
        if (isset($_GET['h_avail']) && $_GET['h_avail'] != "true")
            $tmp[] = "Available";
        if (isset($_GET['h_temp_unavail']) && $_GET['h_temp_unavail'] != "true")
            $tmp[] = "Temporarily unavailable";
        if (isset($_GET['h_arch']) && $_GET['h_arch'] != "true")
            $tmp[] = "Archived";

        $this->search_params['status'] = implode("|", $tmp);
        if (count($tmp) == 0)
            return false; //search params are contradictory

        // min_score, max_score - convert to OKAPI's "rating" filter. This code
        // is weird, because values passed to min_score/max_score are weird...
        $t = floatval($_GET['min_score']);
        $min_rating = ($t < 0) ? "1" : (($t < 1) ? "2" : (($t < 1.5) ? "3" : (($t < 2.2) ? "4" : "5")));
        $t = floatval($_GET['max_score']);
        $max_rating = ($t < 0.7) ? "1" : (($t < 1.3) ? "2" : (($t < 2.2) ? "3" : (($t < 2.7) ? "4" : "5")));
        $this->search_params['rating'] = $min_rating . "-" . $max_rating;
        unset($t, $min_rating, $max_rating);

        // h_noscore - convert to OKAPI's "rating" parameter.
        if (isset($_GET['h_noscore']) && $_GET['h_noscore'] == "true")
            $this->search_params['rating'] = $this->search_params['rating'] . "|X";

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
        if (isset($_GET['h_nogeokret']) && $_GET['h_nogeokret'] == 'true')
            $this->search_params['with_trackables_only'] = "true";

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

        foreach ($mapping as $letter => $type)
            if (isset($_GET['h_' . $letter]) && ($_GET['h_' . $letter] == "true"))
                $types_to_hide[] = $type;

        if (count($types_to_hide) > 0)
            $this->search_params['type'] = "-" . implode("|", $types_to_hide);
        unset($types_to_hide, $mapping, $letter, $type);

        // h_own (hide user's own caches) - convert to OKAPI's "exclude_my_own" parameter.
        if (isset($_GET['h_own']) && $_GET['h_own'] == "true")
            $this->search_params['exclude_my_own'] = "true";

        // h_found, h_noattempt - convert to OKAPI's "found_status" parameter.
        $h_found = (isset($_GET['h_found']) && $_GET['h_found'] == "true");
        $h_noattempt = (isset($_GET['h_noattempt']) && $_GET['h_noattempt'] == "true");
        if ($h_found && (! $h_noattempt))
            $this->search_params['found_status'] = "notfound_only";
        elseif ((! $h_found) && $h_noattempt)
            $this->search_params['found_status'] = "found_only";
        elseif ((! $h_found) && (! $h_noattempt))
            $this->search_params['found_status'] = "either";
        else
            return false; //search params are contradictory

        return true;
    }

    private function loadSearchData($searchData)
    {
        \okapi\OkapiErrorHandler::reenable();

        // We need to transform OC's "searchdata" into OKAPI's "search set".
        // First, we need to determine if we ALREADY did that.
        // Note, that this is not exactly thread-efficient. Multiple threads may
        // do this transformation in the same time. However, this is done only once
        // for each searchdata, so we will ignore it.

        $cache_key = "OC_searchdata_" . $searchData;
        $set_id = \okapi\Cache::get($cache_key);
        if ($set_id === null) {
            // Read the searchdata file into a temporary table.

            $filepath = \okapi\Settings::get('VAR_DIR') . "/searchdata/" . $searchData;
            \okapi\Db::execute("
            create temporary table temp_" . $searchData . " (
                cache_id integer primary key
            ) engine=memory
        ");
            if (file_exists($filepath)) {
                \okapi\Db::execute("
                        load data local infile '$filepath'
                        into table temp_" . $searchData . "
                fields terminated by ' '
                lines terminated by '\\n'
                (cache_id)
            ");
            }

            // Tell OKAPI to import the table into its own internal structures.
            // Cache it for two hours.

            $set_info = \okapi\Facade::import_search_set("temp_" . $searchData, 7200, 7200);
            $set_id = $set_info['set_id'];
            \okapi\Cache::set($cache_key, $set_id, 7200);
        }
        $this->search_params['set_and'] = $set_id;
        $this->search_params['status'] = "Available|Temporarily unavailable|Archived";

        \okapi\OkapiErrorHandler::disable();
        return true;
    }

    /**
     * Call OKAPI, parse response and display the results
     * @param array $params - params of the cache to search and display
     */
    private function htmlFormat(array $params)
    {
        $ocConfig = \lib\Objects\OcConfig\OcConfig::instance();
        //call OKAPI
        $okapi_resp = \okapi\Facade::service_call('services/caches/shortcuts/search_and_retrieve', $this->user_id, $params);

        if (! is_a($okapi_resp, "ArrayObject")) { // strange OKAPI return !?
            error_log(__METHOD__.": ERROR: strange OKAPI response - not an ArrayObject");
            exit(0);
        }

        \okapi\OkapiErrorHandler::disable();

        if ($okapi_resp->count() == 0) {
            // no caches found
            exit(0);
        }

        // get the first object from the list
        $arrayCopy = $okapi_resp->getArrayCopy();
        $geoCache = new \lib\Objects\GeoCache\GeoCache(array(
            'okapiRow' => array_pop($arrayCopy)
        ));

        //generate the results
        if( $this->screenWidth < 400 ){
            tpl_set_tplname('map/map_cacheinfo_small');
        }else{
            tpl_set_tplname('map/map_cacheinfo');
        }

        tpl_set_var('cache_lat', $geoCache->getCoordinates()->getLatitude());
        tpl_set_var('cache_lon', $geoCache->getCoordinates()->getLongitude());
        tpl_set_var('cache_name', $geoCache->getCacheName());
        tpl_set_var('cache_icon', $geoCache->getCacheIcon());

        $is_event = ($geoCache->getCacheType() == $geoCache::TYPE_EVENT ? '1' : '0'); // be aware: booleans not working here
        tpl_set_var('is_event', $is_event, false);

        $is_scored = ($geoCache->getRatingId() != 0 && $geoCache->getRatingVotes() > 2) ? '1' : '0';
        tpl_set_var('is_scored', $is_scored, false);
        tpl_set_var('rating_desc', tr($geoCache->getRatingDesc()));

        $is_recommended = ($geoCache->getRecommendations() > 0 ? '1' : '0');
        tpl_set_var('is_recommended', $is_recommended, false);
        tpl_set_var('cache_recommendations', $geoCache->getRecommendations(), false);

        tpl_set_var('cache_code', $geoCache->getWaypointId());
        tpl_set_var('cache_founds', $geoCache->getFounds());
        tpl_set_var('cache_not_founds', $geoCache->getNotFounds());
        tpl_set_var('cache_rating_votes', $geoCache->getRatingVotes());
        tpl_set_var('cache_willattends', $geoCache->getWillattends());

        tpl_set_var('cache_url', $geoCache->getCacheUrl());

        tpl_set_var('user_name', $geoCache->getOwner()->getUserName());
        tpl_set_var('user_profile', $geoCache->getOwner()->getProfileUrl());
        tpl_set_var('start_date', $geoCache->getDatePlaced()->format($ocConfig->getDateFormat()));

        tpl_set_var('cache_size_desc', tr($geoCache->getSizeDesc()));

        $is_powertrail_part = ($geoCache->isPowerTrailPart() ? '1' : '0');
        tpl_set_var('is_powertrail_part', $is_powertrail_part, false);

        if ($geoCache->isPowerTrailPart()) {
            tpl_set_var('pt_name', $geoCache->getPowerTrail()->getName());
            tpl_set_var('pt_image', $geoCache->getPowerTrail()->getImage());
            tpl_set_var('pt_icon', $geoCache->getPowerTrail()->getFootIcon());
            tpl_set_var('pt_url', $geoCache->getPowerTrail()->getPowerTrailUrl());
        }

        $is_titled =( $geoCache->isTitled()? 'true' : 'false' );
        tpl_set_var('is_titled', $is_titled, false);
        //tpl_set_var('is_titled', $geoCache->isTitled(), false);


        // make the template and send it out
        tpl_BuildTemplate(false, false, true);
    }

    /**
     * Call OKAPI to return the URL of the cache based on given params
     * @param array $params - search params for the okapi call
     */
    private function getUrlOnly(array $params)
    {
        //we want only cache details page URL from OKAPI
        $params['retr_params'] = '{"fields":"url"}';

        //call OKAPI - OKAPI displays the results
        \okapi\Facade::service_display('services/caches/shortcuts/search_and_retrieve', $this->user_id, $params);
    }
}


