<?php

# This is a wrapper for OKAPI's "services/tilemap/tile" method. If takes
# request parameters in the "legacy" mapper.php/mapper.fcgi format, converts
# them to OKAPI's parameters, then executes OKAPI's tile-serving method.
#
# All mapper_* scripts take the same set of parameters (more or less).
# This way, they can be interchangable.
#
# Please note, that this file is NOT part of the official API. It may
# stop working at any time.

$rootpath = "../";

require_once($rootpath . 'okapi/facade.php');

# The code below may produce notices, so we will disable OKAPI's default
# error handler.

\okapi\OkapiErrorHandler::disable();

# mapper.php/mapper.fcgi used to take the following parameters:
#
# [mapper.fcgi params supported by mapper_okapi.php]
# x, y, z, userid, h_ignored, h_temp_unavail, h_arch, h_avail, be_ftf,
# min_score, max_score, h_nogeokret, cache types 1..8 "h_*": h_u,t,m,v,w,e,q,o,
# h_own, h_found, h_noattempt (->not_found_by), h_noscore, searchdata.
#
# [mapper.fcgi params NOT supported by mapper_okapi.php]
# sc, signes, h_pl, h_de, mapid, waypoints.

$params = array();
$force_result_empty = false;

# x, y, z - these don't need any conversion.

$params['x'] = $_GET['x'];
$params['y'] = $_GET['y'];
$params['z'] = $_GET['z'];

# userid - we will simulate an OAuth call in the name of this user.
#
# There seems to be a privacy issue here (e.g. user X can see which caches are
# ignored by user Y), but this worked this way for years (even before I wrote
# this page), so I guess users don't think so.

$user_id = $_GET['userid'];

# There are two "modes" the legacy-compatible mapper operates:
# 1. Without "searchdata" - the normal version.
# 2. With "searchdata" - ONLY "searchdata" is taken into account. All other
#    parameters are ignored.

$searchdata = (isset($_GET['searchdata']) && preg_match('/^[a-f0-9]{6,32}/', $_GET['searchdata'])) ? $_GET['searchdata'] : null;

if ($searchdata) {  # Mode 2 - with "searchdata".
    \okapi\OkapiErrorHandler::reenable();

    # We need to transform OC's "searchdata" into OKAPI's "search set".
    # First, we need to determine if we ALREADY did that.
    # Note, that this is not exactly thread-efficient. Multiple threads may
    # do this transformation in the same time. However, this is done only once
    # for each searchdata, so we will ignore it.

    $cache_key = "OC_searchdata_" . $searchdata;
    $set_id = \okapi\Cache::get($cache_key);
    if ($set_id === null) {
        # Read the searchdata file into a temporary table.

        $filepath = \okapi\Settings::get('VAR_DIR') . "/searchdata/" . $searchdata;
        \okapi\Db::execute("
            create temporary table temp_" . $searchdata . " (
                cache_id integer primary key
            ) engine=memory
        ");
        if (file_exists($filepath)) {
            \okapi\Db::execute("
                load data local infile '$filepath'
                into table temp_" . $searchdata . "
                fields terminated by ' '
                lines terminated by '\\n'
                (cache_id)
            ");
        }

        # Tell OKAPI to import the table into its own internal structures.
        # Cache it for two hours.

        $set_info = \okapi\Facade::import_search_set("temp_" . $searchdata, 7200, 7200);
        $set_id = $set_info['set_id'];
        \okapi\Cache::set($cache_key, $set_id, 7200);
    }
    $params['set_and'] = $set_id;
    $params['status'] = "Available|Temporarily unavailable|Archived";

    \okapi\OkapiErrorHandler::disable();
} else {  # Mode 1 - without "searchdata".
    # h_ignored - convert to OKAPI's "exclude_ignored".
    if ($_GET['h_ignored'] == "true")
        $params['exclude_ignored'] = "true";

    # h_avail, h_temp_unavail, h_arch ("hide available" etc.) - convert to
    # OKAPI's "status" filter.

    $tmp = array();
    if ($_GET['h_avail'] != "true")
        $tmp[] = "Available";
    if ($_GET['h_temp_unavail'] != "true")
        $tmp[] = "Temporarily unavailable";
    if ($_GET['h_arch'] != "true")
        $tmp[] = "Archived";
    $params['status'] = implode("|", $tmp);
    if (count($tmp) == 0)
        $force_result_empty = true;

    # min_score, max_score - convert to OKAPI's "rating" filter. This code
    # is weird, because values passed to min_score/max_score are weird...

    $t = floatval($_GET['min_score']);
    $min_rating = ($t < 0) ? "1" : (($t < 1) ? "2" : (($t < 1.5) ? "3" : (($t < 2.2) ? "4" : "5")));
    $t = floatval($_GET['max_score']);
    $max_rating = ($t < 0.7) ? "1" : (($t < 1.3) ? "2" : (($t < 2.2) ? "3" : (($t < 2.7) ? "4" : "5")));
    $params['rating'] = $min_rating . "-" . $max_rating;
    unset($t, $min_rating, $max_rating);

    # h_noscore - convert to OKAPI's "rating" parameter.

    if ($_GET['h_noscore'] == "true")
        $params['rating'] = $params['rating'] . "|X";

    # be_ftf (hunt for FTFs) - convert to OKAPI's "ftf_hunter" parameter.

    if ($_GET['be_ftf'] == "true") {
        $params['ftf_hunter'] = "true";

        # Also, override previously set "status" filter. This behavior is
        # compatible with what previous mapper scripts did.

        $params['status'] = "Available";

        # BTW, if we override "status" parameter, then we should also override
        # "rating" (all ftfs have "null" for rating). I don't do that though, to
        # stay 100% compatible with the previous implementation.
    }


    # powertrail_only (hunt for powerTrails) - convert to OKAPI's "powertrail_only" parameter.
    if (isset($_GET['powertrail_only']) && $_GET['powertrail_only'] == "true") {
        $params['powertrail_only'] = "true";
    }

    # powertrail_ids (only caches from powerTrails with id) - convert to OKAPI's "powertrail_ids" param.
    if ( isset($_GET['powertrail_ids']) &&
        preg_match('/^[0-9]+(\|[0-9]+)*$/', $_GET['powertrail_ids']) ) {
        $params['powertrail_ids'] = $_GET['powertrail_ids'];
    }

    # h_nogeokret - Convert to OKAPI's "with_trackables_only" parameter.

    if ($_GET['h_nogeokret'] == 'true')
        $params['with_trackables_only'] = "true";

    # h_?, where ? is a single letter - hide a specific cache type.
    # Convert to OKAPI's "type" parameter.

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
        'owncache' => "Own",
            # Note: Some are missing!
    );
    foreach ($mapping as $letter => $type)
        if (isset($_GET['h_' . $letter]) && ($_GET['h_' . $letter] == "true"))
            $types_to_hide[] = $type;
    if (count($types_to_hide) > 0)
        $params['type'] = "-" . implode("|", $types_to_hide);
    unset($types_to_hide, $mapping, $letter, $type);

    # h_own (hide user's own caches) - convert to OKAPI's "exclude_my_own" parameter.

    if ($_GET['h_own'] == "true")
        $params['exclude_my_own'] = "true";

    # h_found, h_noattempt - convert to OKAPI's "found_status" parameter.

    $h_found = ($_GET['h_found'] == "true");
    $h_noattempt = ($_GET['h_noattempt'] == "true");
    if ($h_found && (!$h_noattempt))
        $params['found_status'] = "notfound_only";
    elseif ((!$h_found) && $h_noattempt)
        $params['found_status'] = "found_only";
    elseif ((!$h_found) && (!$h_noattempt))
        $params['found_status'] = "either";
    else
        $force_result_empty = true;
}

#
# We have all the parameters. Note, that some mapper-compatible parameter sets
# always render empty results. We will just exit, without producing any image
# whatsoever.
#

if ($force_result_empty)
    die();
if (!$user_id)
    die();

# End of "buggy" code. Re-enable OKAPI's error handler.

\okapi\OkapiErrorHandler::reenable();

# Get OKAPI's response and display it. Add proper Cache-Control headers.

\okapi\Facade::service_display('services/caches/map/tile', $user_id, $params);
