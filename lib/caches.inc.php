<?php

use Utils\Database\XDb;

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
    $log_types = array();

    $resp = XDb::xSql("SELECT * FROM log_types ORDER BY id ASC");
    while ($row = XDb::xFetchArray($resp)) {
        $log_types[] = $row;
    }
    return $log_types;
}

function get_cache_types_from_database()
{
    $cache_types = array();

    $resp = XDb::xSql("SELECT * FROM cache_type ORDER BY sort ASC");
    while ($row = XDb::xFetchArray($resp)) {
        $cache_types[] = $row;
    }
    return $cache_types;
}

function get_wp_types_from_database($cachetype)
{
    $wp_types = array();
    if ($cachetype == '2' || $cachetype == '4' || $cachetype == '5' || $cachetype == '6' || $cachetype == '9') {
        $param = "id=-1 OR id=4 OR id=5 OR id=6";
    } else {
        $param = "id=-1 OR id=1 OR id=2 OR id=3 OR id=4 OR id=5 OR id=6";
    }
    $resp = XDb::xSql("SELECT * FROM waypoint_type WHERE $param ORDER BY id ASC");
    while ($row = XDb::xFetchArray($resp)) {
        $wp_types[] = $row;
    }
    return $wp_types;
}

function get_cache_status_from_database()
{
    $cache_status = array();

    $resp = XDb::xSql("SELECT * FROM cache_status ORDER BY id ASC");
    while ($row = XDb::xFetchArray($resp)) {
        $cache_status[] = $row;
    }
    return $cache_status;
}

function get_cache_size_from_database()
{
    $cache_size = array();

    $resp = XDb::xSql("SELECT * FROM cache_size ORDER BY id ASC");
    while ($row = XDb::xFetchArray($resp)) {
        $cache_size[] = $row;
    }
    return $cache_size;
}

function log_type_from_id($id, $lang)
{
    global $log_types;
    if (Xdb::xContainsColumn('log_types', $lang))
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
    if (Xdb::xContainsColumn('cache_type', $lang))
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
    if (Xdb::xContainsColumn('cache_size', $lang))
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
    if (XDb::xContainsColumn('cache_status', $lang))
        $lang_db = $lang;
    else
        $lang_db = "en";

    foreach ($cache_status AS $status) {
        if ($status['id'] == $id) {
            return $status[$lang_db];
        }
    }
}


