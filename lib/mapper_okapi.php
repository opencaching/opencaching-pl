<?php

# This is a wrapper for OKAPI's "services/tilemap/tile" method. If takes
# request parameters in the "legacy" mapper.php/mapper.fcgi format, converts
# them to OKAPI's parameters, then executes OKAPI's tile-serving method.
#
# All mapper_* scripts take the same set of parameters (more or less).
# This way, they can be interchangable.

$rootpath = "../";

# require_once($rootpath.'lib/common.inc.php');
require_once($rootpath.'okapi/facade.php');

# mapper.php/mapper.fcgi used to take the following parameters:
#
# [Still supported by mapper_okapi.php]
# x, y, z, userid, h_ignored, h_temp_unavail, h_arch, h_avail, be_ftf,
# min_score, max_score, h_nogeokret, cache types 1..8 "h_*": h_u,t,m,v,w,e,q,o,
# h_own, h_found, h_noattempt (->not_found_by), h_noscore.
#
# [Currently not supported by mapper_okapi.php]
# sc, signes, searchdata, h_pl, h_de, mapid, waypoints.

$params = array();
$force_result_empty = false;

# x, y, z - these don't need any conversion.

$params['x'] = $_GET['x'];
$params['y'] = $_GET['y'];
$params['z'] = $_GET['z'];

# userid - we will simulate an OAuth call in the name of this user.

$user_id = $_GET['userid'];

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
	
# be_ftf (hunt for FTFs) - convert to OKAPI's "max_founds" filter.

if ($_GET['be_ftf'] == "true")
{
	$params['max_founds'] = "0";
	
	# Also, override previously set "status" filter. This behavior is
	# compatible with what previous mapper scripts did.
	
	$params['status'] = "Available";
}

# min_score, max_score - convert to OKAPI's "rating" filter. This code
# is weird, because values passed to min_score/max_score are weird...

$t = floatval($_GET['min_score']);
$min_rating = ($t < 0) ? "1" : (($t < 1) ? "2" : (($t < 1.5) ? "3" : (($t < 2.2) ? "4" : "5")));
$t = floatval($_GET['max_score']);
$max_rating = ($t < 0.7) ? "1" : (($t < 1.3) ? "2" : (($t < 2.2) ? "3" : (($t < 2.7) ? "4" : "5")));
$params['rating'] = $min_rating."-".$max_rating;
unset($t, $min_rating, $max_rating);

# h_nogeokret - Support was temporarily withdrawn (issue 147 - WONTFIX).

// WONTFIX

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
	if ($_GET['h_'.$letter] == "true")
		$types_to_hide[] = $type;
if (count($types_to_hide) > 0)
	$params['type'] = "-".implode("|", $types_to_hide);
unset($types_to_hide, $mapping, $letter, $type);

# h_own (hide user's own caches) - convert to OKAPI's "exclude_my_own" parameter.

if ($_GET['h_own'] == "true")
	$params['exclude_my_own'] = "true";

# h_found, h_noattempt - convert to OKAPI's "found_status" parameter.

$h_found = ($_GET['h_found'] == "true");
$h_noattempt = ($_GET['h_noattempt'] == "true");
if ($h_found && (!$h_noattempt))
	$params['found_status'] = "found_only";
elseif ((!$h_found) && $h_noattempt)
	$params['found_status'] = "notfound_only";
elseif ((!$h_found) && (!$h_noattempt))
	$params['found_status'] = "either";
else
	$force_result_empty = true;

# h_noscore - WRTODO (issue 148).

if ($_GET['h_noscore'] == "true")
	$params['rating'] = $params['rating']."|X";

#
# We have all the parameters. Note, that some mapper-compatible parameter sets
# always render empty results. We will just exit, without producing any image
# what so ever (WRTODO: output a predefined empty gif?)
#

if ($force_result_empty)
	die();

# Get OKAPI's response and display it.

$okapi_response = \okapi\Facade::service_call('services/caches/map/tile', $user_id, $params);
$okapi_response->display();

