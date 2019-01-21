<?php

use Utils\Database\XDb;
use Utils\I18n\I18n;

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



function cache_type_from_id($id)
{
    $lang_db = I18n::getLangForDbTranslations('cache_type');

    foreach (get_cache_types_from_database() AS $cache_type) {
        if ($cache_type['id'] == $id) {
            return $cache_type[$lang_db];
        }
    }
}




