<?php

/* * *************************************************************************
  ./search.php
  -------------------
  begin                : July 25 2004
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

  Unicode Reminder ăĄă˘

  compute querystrings from mozilla/firefox-searchplugin and redirect to search.php

 * ************************************************************************** */

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');
require($stylepath . '/searchplugin.inc.php');
$ocWP = strtolower($GLOBALS['oc_waypoint']);
// initialize
$keyword_name = 'name';
$keyword_finder = 'finder';
$keyword_owner = 'owner';
$keyword_town = 'town';
$keyword_zipcode = 'place';
$keyword_cacheid = 'id';
$keyword_wp = 'wp';

$searchurl = 'search.php';

# get parameter from URL
$userinput = isset($_REQUEST['userinput']) ? $_REQUEST['userinput'] : '';
//  $wp = isset($_REQUEST['wp']) ? $_REQUEST['wp'] : '';
$sourceid = isset($_REQUEST['sourceid']) ? $_REQUEST['sourceid'] : 0;

if (($sourceid == 'waypoint-search') && ($userinput != '')) {
    $sourceid = 'mozilla-search';
    $userinput = 'wp:' . $userinput;
//      $wp = 'wp:'.$wp;
}

if (($sourceid == 'mozilla-search') && ($userinput != '')) {
    $params = mb_split(':', $userinput);
    if ($params !== false) {
        if (count($params) == 1) {
            $searchto = 'name';
            $searchfor = urlencode($params[0]);
        } else {
            $searchto = $params[0];
            array_splice($params, 0, 1);
            $searchfor = urlencode(implode(':', $params));
        }
        unset($params);

        // for zipcode/town-search: if logged in, sort by distance
        if ($usr == false) {
            $order = 'byname';
        } else {
            $order = 'bydistance';
        }

        $targeturl = 'search.php?showresult=1&expert=0&output=HTML&f_userowner=0&f_userfound=0';
        switch ($searchto) {
            case $keyword_name:
                $targeturl .= '&sort=byname&searchbyname=1&f_inactive=1&cachename=' . $searchfor;
                break;
            case $keyword_finder:
                $targeturl .= '&sort=byname&searchbyfinder=1&f_inactive=0&finder=' . $searchfor;
                break;
            case $keyword_owner:
                $targeturl .= '&sort=byname&searchbyowner=1&f_inactive=0&owner=' . $searchfor;
                break;
            case $keyword_town:
                $targeturl .= '&searchbyort=1&f_inactive=1&ort=' . $searchfor . '&sort=' . $order;
                break;
            case $keyword_zipcode:
                $targeturl .= '&searchbyplz=1&f_inactive=1&plz=' . $searchfor . '&sort=' . $order;
                break;
            case $keyword_cacheid:
                $targeturl .= '&sort=byname&searchbycacheid=1&f_inactive=1&cacheid=' . $searchfor;
                break;
            case $keyword_wp:
                $targeturl = 'index.php';
                $searchfor = mb_trim($searchfor);
                $target = mb_strtolower(mb_substr($searchfor, 0, 2));
                if (mb_substr($target, 0, 1) == 'n') {
                    $target = 'nc';
                }
                if (mb_ereg_match('([a-f0-9]){4,4}$', mb_strtolower($searchfor))) {
                    $target = $ocWP;
                    $searchfor = $target . '' . $searchfor;
                }
                if ((($target == 'oc') || ($target == $ocWP) || ($target == 'nc') || ($target == 'gc')) &&
                        mb_ereg_match('((' . $ocWP . '|oc)([a-z0-9]){4,4}|gc([a-z0-9]){4,5}|n([a-f0-9]){5,5})$', mb_strtolower($searchfor))) {
                    // get cache_id from DB
                    if ($target == $ocWP) {
                        $target = 'oc';
                    }
                    $rs = sql("SELECT `cache_id`, `latitude`, `longitude` FROM `caches` WHERE `wp_" . sql_escape($target) . "`='&1'", $searchfor);
                    $count = mysql_num_rows($rs);
                    if ($count == 1) {
                        $record = sql_fetch_array($rs);
                        if (isset($_POST['namapie']))
                            $targeturl = 'cachemap2.php?lat=' . $record['latitude'] . '&lon=' . $record['longitude'] . '&cacheid=' . $record['cache_id'];
                        else
                            $targeturl = 'viewcache.php?cacheid=' . $record['cache_id'];
                        unset($record);
                    }
                    else if ($count == 0) {
                        $tplname = 'searchplugin';
                        tpl_set_var('error_msg', mb_ereg_replace('{wp}', $searchfor, $errmsg_no_cache_found));
                        tpl_BuildTemplate();
                        exit;
                    } else if ($count > 1) {
                        $tplname = 'searchplugin';
                        tpl_set_var('error_msg', mb_ereg_replace('{wp}', $searchfor, $errmsg_many_caches_found));
                        tpl_BuildTemplate();
                        exit;
                    }
                    mysql_free_result($rs);
                    unset($count);
                } else {
                    // wrong waypoint format
                    $tplname = 'searchplugin';
                    tpl_set_var('error_msg', $errmsg_unknown_format);
                    tpl_BuildTemplate();
                    exit;
                }
                break;
        }
    }
}
tpl_redirect($targeturl);
?>
