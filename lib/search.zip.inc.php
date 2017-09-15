<?php

use okapi\Facade;

function call_okapi($usr, $waypoints, $lang, $file_base_name, $zip_part)
{
    $okapi_params = array('cache_codes' => $waypoints, 'langpref' => $lang,
        'location_source' => 'alt_wpt:user-coords', 'location_change_prefix' => '(F)');
    // TODO: limit log entries per geocache?
    if (isset($_GET['format']))
        $okapi_params['caches_format'] = $_GET['format'];
    $okapi_response = Facade::service_call('services/caches/formatters/garmin', $usr['userid'], $okapi_params);
    // Modifying OKAPI's default HTTP Response headers.
    $okapi_response->content_disposition = 'attachment; filename=' . $file_base_name . (($zip_part != 0) ? '-' . $zip_part : '') . '.zip';
    return $okapi_response;
}

function generate_link_content($queryid, $file_base_name, $zip_part)
{
    if (isset($_GET['format']))
        $format = '&format=' . $_GET['format'];
    else
        $format = '';
    $zipname = 'ocpl' . $queryid . '.zip?startat=0&count=max&zippart=' . $zip_part . $format . (isset($_GET['okapidebug']) ? '&okapidebug' : '');
    $link_content = '<li><a class="links" href="' . $zipname . '" title="Garmin ZIP file (part ' . $zip_part . ')">' . $file_base_name . '-' . $zip_part . '.zip</a></li>';
    return $link_content;
}

// reflect okapi limit of allowed geocache codes per invocation
function get_max_caches_per_call()
{
    if (isset($_REQUEST['okapidebug']))
        return 500;
    else
        return 50;
}

function get_pagination_template()
{
    if (isset($_GET['format'])) {
        switch ($_GET['format']) {
            case 'gpx': return 'garminzip';
            default: return 'garminzip-' . $_GET['format'];
        }
    }
    return 'garminzip';
}

function get_pagination_page_title()
{
    switch (get_pagination_template()) {
        case 'garminzip': return tr('GarminZip_01') . ': Garmin ZIP';
        case 'garminzip-ggz': return tr('GarminZip_01') . ': Garmin ' . tr('format_ggz_pict');
    }
}

// all the logic is done here
include 'search.okapi.inc.php';

