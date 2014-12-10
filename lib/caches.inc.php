<?php

/* * *************************************************************************
  ./lib/caches.inc.php
  --------------------
  begin                : June 24 2004
  copyright            : (C) 2004 The OpenCaching Group
  forum contact at     : http://www.opencaching.com/phpBB2

 * ************************************************************************* */

/* * *************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 * ************************************************************************* */

/* * **************************************************************************

  Unicode Reminder ??

  functions and variables for cache-submission related things

 * ************************************************************************** */

// Sachestatus-ID selected by default
$default_cachestatus_id = 1;

$log_types = array();
$cache_types = array();
$wp_types = array();
$cache_status = array();
$cache_size = array();

// Sachesize-ID selected by default
$default_logtype_id = 1;

// new: get *_types from database
if (!isset($cachetype))
    $cachetype = '';

$log_types = get_log_types_from_database();
$cache_types = get_cache_types_from_database();
$wp_types = get_wp_types_from_database($cachetype);
$cache_status = get_cache_status_from_database();
$cache_size = get_cache_size_from_database();

function get_log_types_from_database()
{
    global $dblink;
    $log_types = array();

    $resp = sql("SELECT * FROM log_types ORDER BY id ASC");
    while ($row = sql_fetch_assoc($resp)) {
        $log_types[] = $row;
    }
    return $log_types;
}

function get_cache_types_from_database()
{
    global $dblink;
    $cache_types = array();

    $resp = sql("SELECT * FROM cache_type ORDER BY sort ASC");
    while ($row = sql_fetch_assoc($resp)) {
        $cache_types[] = $row;
    }
    return $cache_types;
}

function get_wp_types_from_database($cachetype)
{
    global $dblink;
    $wp_types = array();
//  $wp_types[] = array('id' => '-1', 'pl' =>'Proszę wybrać typ', 'en' => 'Select one');
    if ($cachetype == '2' || $cachetype == '4' || $cachetype == '5' || $cachetype == '6' || $cachetype == '9') {
        $param = "id=-1 OR id=4 OR id=5";
    } else {
        $param = "id=-1 OR id=1 OR id=2 OR id=3 OR id=4 OR id=5";
    }
    $resp = sql("SELECT * FROM waypoint_type WHERE $param ORDER BY id ASC");
    while ($row = sql_fetch_assoc($resp)) {
        $wp_types[] = $row;
    }
    return $wp_types;
}

function get_cache_status_from_database()
{
    global $dblink;
    $cache_status = array();

    $resp = sql("SELECT * FROM cache_status ORDER BY id ASC");
    while ($row = sql_fetch_assoc($resp)) {
        $cache_status[] = $row;
    }
    return $cache_status;
}

function get_cache_size_from_database()
{
    global $dblink;
    $cache_size = array();

    $resp = sql("SELECT * FROM cache_size ORDER BY id ASC");
    while ($row = sql_fetch_assoc($resp)) {
        $cache_size[] = $row;
    }
    return $cache_size;
}

function log_type_from_id($id, $lang)
{
    global $log_types;
    if (checkField('log_types', $lang))
        $lang_db = $lang;
    else
        $lang_db = "en";

    foreach ($log_types AS $type) {
        if ($type['id'] == $id) {
            return $type[$lang];
        }
    }
}

function cache_type_from_id($id, $lang)
{
    global $cache_types;
    if (checkField('cache_type', $lang))
        $lang_db = $lang;
    else
        $lang_db = "en";

    foreach ($cache_types AS $cache_type) {
        if ($cache_type['id'] == $id) {
            return $cache_type[$lang_db];
        }
    }
}

function cache_size_from_id($id, $lang)
{
    global $cache_size;
    if (checkField('cache_size', $lang))
        $lang_db = $lang;
    else
        $lang_db = "en";

    foreach ($cache_size AS $size) {
        if ($size['id'] == $id) {
            return $size[$lang_db];
        }
    }
}

function cache_status_from_id($id, $lang)
{
    global $cache_status;
    if (checkField('cache_status', $lang))
        $lang_db = $lang;
    else
        $lang_db = "en";

    foreach ($cache_status AS $status) {
        if ($status['id'] == $id) {
            return $status[$lang_db];
        }
    }
}

?>
