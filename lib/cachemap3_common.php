<?php
/*
 * 
 * This is common code for mapv3
 *  this is used by: 
 *    -- /cachemap3.php
 *    -- /cachemap-full.php
 */


function onTheList($theArray, $item)
{
    for ($i = 0; $i < count($theArray); $i++) {
        if ($theArray[$i] == $item)
            return $i;
    }
    return -1;
}


function getDBFilter($user_id)
{

    global $MIN_SCORE, $MAX_SCORE, $powerTrailModuleSwitchOn; //defined in settings.inc/php

    $filter = array("h_u" => 1,
        "h_t" => 1,
        "h_m" => 1,
        "h_v" => 1,
        "h_w" => 1,
        "h_e" => 1,
        "h_q" => 1,
        "h_o" => 1,
        "h_owncache" => 1,
        "h_ignored" => 0,
        "h_own" => 1,
        "h_found" => 1,
        "h_noattempt" => 1,
        "h_nogeokret" => 1,
        "h_avail" => 0,
        "h_temp_unavail" => 1,
        "map_type" => 1,
        "h_arch" => 0,
        "be_ftf" => 0,
        "powertrail_only" => 0,
        "min_score" => $MIN_SCORE,
        "max_score" => $MAX_SCORE,
        "h_noscore" => 1
    ); // default filter
    $query = mysql_query("SELECT * from map_settings WHERE `user_id`=$user_id LIMIT 1");
    while ($row = mysql_fetch_assoc($query)) {
        $filter["h_u"] = $row['unknown'];
        $filter["h_t"] = $row['traditional'];
        $filter["h_m"] = $row['multicache'];
        $filter["h_v"] = $row['virtual'];
        $filter["h_w"] = $row['webcam'];
        $filter["h_e"] = $row['event'];
        $filter["h_q"] = $row['quiz'];
        $filter["h_o"] = $row['mobile'];
        $filter["h_owncache"] = $row['owncache'];
        $filter["h_ignored"] = $row['ignored'];
        $filter["h_own"] = $row['own'];
        $filter["h_found"] = $row['found'];
        $filter["h_noattempt"] = $row['notyetfound'];
        $filter["h_nogeokret"] = $row['geokret'];
        $filter["h_avail"] = $row['active'];
        $filter["h_temp_unavail"] = $row['notactive'];
        $filter["map_type"] = $row['maptype'];
        $filter["h_arch"] = $row['archived'];
        $filter["be_ftf"] = $row['be_ftf'];

        global $powerTrailModuleSwitchOn;
        if($powerTrailModuleSwitchOn){
            $filter["powertrail_only"] = $row['powertrail_only'];
        }

        $filter["min_score"] = $row['min_score'];
        $filter["max_score"] = $row['max_score'];
        $filter["h_noscore"] = $row['noscore'];
    }

    return $filter;
}
