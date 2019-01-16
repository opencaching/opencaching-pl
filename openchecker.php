<?php

/**
 *  openchecker.php
 *  ------------------------------------------------------------------------------------------------
 *  Puzzle Final Cache coordinate checker
 *  ------------------------------------------------------------------------------------------------
 *  @author: Andrzej 'Łza' Woźniak [wloczynutka@gmail.com]
 *
 *
 *
 *  ================================================================================================
 *  TODO:
 *  1.) validation data from post (should be numeric, but this is not a must - if data is not numeric
 *      we just get result that puzzle solution is incorrect)
 *  2.) convert section 2 to OOP.
 *  3.) after succesfull check, provide (.gpx) download with final stage coords, downloadable for
 *      GPS devices.
 *  4.) store checks in database rather then session (logging out resets your attempts count)
 *  5.) rename database tables and fields according to https://github.com/opencaching/opencaching-pl/issues/649
 *  6.) remove / rewrite LIMIT 0,1000 ?
 *  ================================================================================================
 */

use Utils\Database\XDb;
use lib\Objects\GeoCache\GeoCache;
use lib\Objects\GeoCache\Waypoint;
use Utils\View\View;

// variables required by opencaching.pl
global $usr;

//prepare the templates and include all neccessary
require_once (__DIR__.'/lib/common.inc.php');
require_once (__DIR__.'/modules/openchecker/pagination_class.php');
require_once (__DIR__.'/modules/openchecker/openchecker_classes.php');

if ($usr == false) {
    // not logged in, go to login page
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);
    exit;
}

if ( $config['module']['openchecker']['enabled'] == false ) {
    tpl_redirect('index.php');
    exit;
}

/** @var View */
$view->setTemplate('openchecker');
$view->loadJQuery();


$OpenCheckerSetup = New OpenCheckerSetup();
tpl_set_var('openchecker_script', $OpenCheckerSetup->scriptname);

tpl_set_var("section_5_start", '<!--');
tpl_set_var("section_5_stop", '-->');

$OpenChecker = New OpenCheckerCore();

/*
 * if isset $_POST['guessedCoordsFinalLatitude'] means that user entered coords to check.
 * - Checking for bruteforce (and prevent if detected)
 * - Checking if coordinates are correct, then display result
 */
if (isset($_POST['guessedCoordsFinalLatitude'])) {
    $OpenChecker->BruteForceCheck($OpenCheckerSetup);
    $OpenChecker->CoordsComparing($OpenCheckerSetup);
}

$OpenChecker->DisplayAllOpenCheckerCaches($OpenCheckerSetup);

// section 2 (display cache data and coordinate entry form)

// TODO: rewrite code using OOP and database queries with PDO

$rs = XDb::xSql("SELECT `caches`.`name`,
        `caches`.`cache_id`,
        `caches`.`type`,
        `caches`.`user_id`,
        `cache_type`.`icon_large`,
        `user`.`username`
        FROM   `caches`, `user`, `cache_type`
        WHERE  `caches`.`user_id` = `user`.`user_id`
        AND    `caches`.`type` = `cache_type`.`id`
        AND    `caches`.`wp_oc` = ? ", $OpenChecker->cache_wp);

// prepare sections
tpl_set_var("section_1_start", '<!--');
tpl_set_var("section_1_stop", '-->');
tpl_set_var("section_2_start", '');
tpl_set_var("section_2_stop", '');
tpl_set_var("section_3_start", '<!--');
tpl_set_var("section_3_stop", '-->');
tpl_set_var("section_4_start", '<!--');
tpl_set_var("section_4_stop", '-->');

if (!$record = Xdb::xFetchArray($rs)) {
    tpl_set_var("openchecker_wrong_cache", tr('openchecker_wrong_cache'));
    tpl_set_var("section_2_start", '<!--');
    tpl_set_var("section_2_stop", '-->');
    tpl_set_var("section_5_start", '');
    tpl_set_var("section_5_stop", '');
    $OpenChecker->Finalize();
}

$cache_id = $record['cache_id'];

tpl_set_var("wp_oc", $OpenChecker->cache_wp);
tpl_set_var("cache_icon", '<img src="tpl/stdstyle/images/' . $record['icon_large'] . '" />');
tpl_set_var("cacheid", $record['cache_id']);
tpl_set_var("user_name", htmlspecialchars($record['username']));
tpl_set_var("cachename", htmlspecialchars($record['name']));
tpl_set_var("user_id", $record['user_id']);

Xdb::xFreeResults($rs);

$wp_rs = XDb::xSql("SELECT `waypoints`.`wp_id`,
        `waypoints`.`type`,
        `waypoints`.`longitude`,
        `waypoints`.`latitude`,
        `waypoints`.`status`,
        `waypoints`.`type`,
        `waypoints`.`opensprawdzacz`
        FROM `waypoints`
        WHERE `cache_id`= ? AND `type` = " . Waypoint::TYPE_FINAL, $cache_id);

$wp_record = XDb::xFetchArray($wp_rs);
if (($wp_record['type'] == Waypoint::TYPE_FINAL) && ($wp_record['opensprawdzacz'] == 1)) {

    $view->setVar('displayOpencheckerForm', true);

    tpl_set_var("openchecker_not_enabled", '');
} else {
    tpl_set_var("openchecker_not_enabled", tr('openchecker_not_enabled'));

    $view->setVar('displayOpencheckerForm', false);
}

// assemble template and display HTML page
$OpenChecker->Finalize();
