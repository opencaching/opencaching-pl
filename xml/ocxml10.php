<?php

/* * *************************************************************************
  ./xml/ocxml10.php
  -------------------
  begin                : August 27, 2005
  copyright            : (C) 2005 The OpenCaching Group
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

  This is the XML-interface for Opencaching.de

  Available parameters to call this script:

  modifiedsince  ... all selected objects will be reported that were modified
  or created after modifiedsince
  Format: yyyymmddHHMMSS

  caches         ... set this parameter to 1 if you want to get caches
  descs          ... set this parameter to 1 if you want to get cache-descriptions
  logs           ... set this parameter to 1 if you want to get logs
  users          ... set this parameter to 1 if you want to get useraccounts
  pictures       ... set this parameter to 1 if you want to get pictures
  removedobjects ... set this parameter to 1 if you want to get removed/deleted-objects

  ocxmltag       ... set this parameter to 0 if you dont want to get the oc10xml-tag
  to get this working, you have to set doctype and xmldecl to 0, too
  doctype        ... set this parameter to 0 if you dont want to get the doctype-tag
  xmldecl        ... set this parameter to 0 if you dont want to get the xmldecl-tag

  Example: http://www.opencaching.de/xml/ocxml10.php?modifiedsince=20050905000000&caches=1

  For a full list of available attribute values or tag-contents, see:
  http://www.opencaching.de/download/ocdb/ocdb-empty.sql

  Future changes of this interface may be:
  - new tags
  - new tag-attributes
  - new paramters to call this script
  - minor bugfixes

  If this changes would break compatibility to running applications, a new
  version will be released (ocxml11.php or ocxml20.php) and the old interface
  will keeped online as long as the database-backend is compatible.

  If you have trouble with this interface, try to contact:
  - via http://www.opencaching.com/phpBB2
  - or via mailto:xml@opencaching.de

  A note to application developers:

  Implement your application to use the modifiedsince-parameter for incremental updates.
  This safes you, your users and us traffic and will help to keep opencaching.de online.
  If modifiedsince doesn't work as expected, contact us instead of disabling it!

  Terms of Usage:

  You can use this XML-interface as you want, as long as your usage doesn't confict with
  any law and doesn't violate the Terms of Usage from opencaching.de

  If you aren't sure if your planned usage fits within the opencaching.de
  Terms of Usage, try to contact via mailto:contact@opencaching.de

  Abuse has to be reported to mailto:contact@opencaching.de

 * ************************************************************************** */

$rootpath = '../';
require('../lib/common.inc.php');

/* begin with some constants */
$t1 = "\t";
$t2 = "\t\t";
$t3 = "\t\t\t";
$t4 = "\t\t\t\t";
$t5 = "\t\t\t\t\t";
$t6 = "\t\t\t\t\t\t";

$sDateshort = 'Y-m-d';
$sDateformat = 'Y-m-d H:i:s';

/* end with some constants */

/* begin db connect */
if ($error == true) {
    echo 'Unable to connect to database';
    exit;
}
/* end db connect */

/* begin now a few dynamically loaded constants */

$logtypes = array();
$rs = sql('SELECT `id`, `pl` FROM log_types');
for ($i = 0; $i < mysql_num_rows($rs); $i++) {
    $r = this_sql_fetch_array($rs);
    $logtypes[$r['id']] = $r['pl'];
}
mysql_free_result($rs);

$cachetypes = array();
$rs = sql('SELECT `id`, `short`, `pl` FROM cache_type');
for ($i = 0; $i < mysql_num_rows($rs); $i++) {
    $r = this_sql_fetch_array($rs);
    $cachetypes[$r['id']]['pl'] = $r['pl'];
    $cachetypes[$r['id']]['short'] = $r['short'];
}
mysql_free_result($rs);

$cachestatus = array();
$rs = sql('SELECT `id`, `pl` FROM cache_status');
for ($i = 0; $i < mysql_num_rows($rs); $i++) {
    $r = this_sql_fetch_array($rs);
    $cachestatus[$r['id']]['pl'] = $r['pl'];
}
mysql_free_result($rs);

$counties = array();
$rs = sql('SELECT `short`, `pl` FROM countries');
for ($i = 0; $i < mysql_num_rows($rs); $i++) {
    $r = this_sql_fetch_array($rs);
    $counties[$r['short']]['pl'] = $r['pl'];
}
mysql_free_result($rs);

$cachesizes = array();
$rs = sql('SELECT `id`, `pl` FROM cache_size');
for ($i = 0; $i < mysql_num_rows($rs); $i++) {
    $r = this_sql_fetch_array($rs);
    $cachesizes[$r['id']]['pl'] = $r['pl'];
}
mysql_free_result($rs);

$languages = array();
$rs = sql('SELECT `short`, `pl` FROM languages');
for ($i = 0; $i < mysql_num_rows($rs); $i++) {
    $r = this_sql_fetch_array($rs);
    $languages[$r['short']]['pl'] = $r['pl'];
}
mysql_free_result($rs);

$objecttypes['1'] = 'cachelog';
$objecttypes['2'] = 'cache';
$objecttypes['3'] = 'cachedesc';
$objecttypes['4'] = 'user';
$objecttypes['6'] = 'picture';

/* end now a few dynamically loaded constants */

/* begin parameter reading */

// fitler parameters
$dModifiedsince = isset($_REQUEST['modifiedsince']) ? $_REQUEST['modifiedsince'] : '0';

// selections
$bCaches = isset($_REQUEST['caches']) ? $_REQUEST['caches'] : '0';
$bDescs = isset($_REQUEST['descs']) ? $_REQUEST['descs'] : '0';
$bLogs = isset($_REQUEST['logs']) ? $_REQUEST['logs'] : '0';
$bUsers = isset($_REQUEST['users']) ? $_REQUEST['users'] : '0';
$bPictures = isset($_REQUEST['pictures']) ? $_REQUEST['pictures'] : '0';
$bRemovedObjects = isset($_REQUEST['removedobjects']) ? $_REQUEST['removedobjects'] : '0';

// xml options
$bOcXmlTag = isset($_REQUEST['ocxmltag']) ? $_REQUEST['ocxmltag'] : '1';
$bDocType = isset($_REQUEST['doctype']) ? $_REQUEST['doctype'] : '1';
$bXmlDecl = isset($_REQUEST['xmldecl']) ? $_REQUEST['xmldecl'] : '1';

// dependencies
$nPrimary = isset($_REQUEST['primary']) ? $_REQUEST['primary'] : '0';

// validation and parsing
if (strlen($dModifiedsince) != 14) {
    echo 'Invalid modifiedsince value (wrong length)';
    exit;
}

// convert to time
$nYear = substr($dModifiedsince, 0, 4);
$nMonth = substr($dModifiedsince, 4, 2);
$nDay = substr($dModifiedsince, 6, 2);
$nHour = substr($dModifiedsince, 8, 2);
$nMinute = substr($dModifiedsince, 10, 2);
$nSecond = substr($dModifiedsince, 12, 2);

if ((!is_numeric($nYear)) && (!is_numeric($nMonth)) && (!is_numeric($nDay)) && (!is_numeric($nHour)) && (!is_numeric($nMinute)) && (!is_numeric($nSecond))) {
    echo 'Invalid modifiedsince value (non-numeric content)';
    exit;
}

if (($nYear < 1970) || ($nYear > 2100) || ($nMonth < 1) || ($nMonth > 12) || ($nDay < 1) || ($nDay > 31) || ($nHour < 0) || ($nHour > 23) || ($nMinute < 0) || ($nMinute > 59) || ($nSecond < 0) || ($nSecond > 59)) {
    echo 'Invalid modifiedsince value (value out of range)';
    exit;
}
$sModifiedSince = date('Y-m-d H:i:s', mktime($nHour, $nMinute, $nSecond, $nMonth, $nDay, $nYear));

if ((($bCaches != '0') && ($bCaches != '1')) ||
        (($bDescs != '0') && ($bDescs != '1')) ||
        (($bLogs != '0') && ($bLogs != '1')) ||
        (($bUsers != '0') && ($bUsers != '1')) ||
        (($bPictures != '0') && ($bPictures != '1')) ||
        (($bRemovedObjects != '0') && ($bRemovedObjects != '1'))) {
    echo 'Invalid selection value';
    exit;
}

if (($nPrimary != '0') &&
        ($nPrimary != '1') &&
        ($nPrimary != '2') &&
        ($nPrimary != '3') &&
        ($nPrimary != '4') &&
        ($nPrimary != '6') &&
        ($nPrimary != '7')) {
    echo 'Invalid dependency value';
    exit;
}

if ((($bOcXmlTag != '0') && ($bOcXmlTag != '1')) ||
        (($bDocType != '0') && ($bDocType != '1')) ||
        (($bXmlDecl != '0') && ($bXmlDecl != '1'))) {
    echo 'Invalid xml options value';
    exit;
}

// doctype but no ocxml?
if (($bDocType == '1') && ($bOcXmlTag == '0')) {
    echo 'doctype yes but no for ocxml-tag? Are you sure that you know what you are doing?';
    exit;
}

// xmldecl but no ocxml?
if (($bXmlDecl == '1') && ($bOcXmlTag == '0')) {
    echo 'xmldecl yes but no for ocxml-tag? Are you sure that you know what you are doing?';
    exit;
}

/* end parameter reading */

/* begin object selection */

$naCachelogs = array();
$naCaches = array();
$naDescs = array();
$naUsers = array();
$naPictures = array();
$naRemovedObjects = array();

if ((($nPrimary == 0) && ($bLogs == 1)) || ($nPrimary == 1)) {
    // Cachelogs
    $rs = sql('SELECT `cache_logs`.`id` FROM `cache_logs` INNER JOIN `caches` ON `caches`.`cache_id`=`cache_logs`.`cache_id` WHERE `caches`.`status`!=4 && `caches`.`status`!=5 AND `caches`.`status`!=6 AND `cache_logs`.`deleted`=0 AND `cache_logs`.`last_modified`>= \'' . $sModifiedSince . '\'');
    for ($i = 0; $i < mysql_num_rows($rs); $i++) {
        $r = this_sql_fetch_array($rs);
        $naCachelogs[$r['id']] = $r['id'];
    }
    mysql_free_result($rs);
}

if ((($nPrimary == 0) && ($bCaches == 1)) || ($nPrimary == 2)) {
    // Caches
    $rs = sql('SELECT `cache_id` `id` FROM `caches` WHERE `last_modified`>= \'' . $sModifiedSince . '\' AND `status`!=4 AND `status`!=5 AND `status`!=6');
    for ($i = 0; $i < mysql_num_rows($rs); $i++) {
        $r = this_sql_fetch_array($rs);
        $naCaches[$r['id']] = $r['id'];
    }
    mysql_free_result($rs);
}

if ((($nPrimary == 0) && ($bDescs == 1)) || ($nPrimary == 3)) {
    // Cachesdesc
    $rs = sql('SELECT `id` FROM `cache_desc` INNER JOIN `caches` ON `caches`.`cache_id`=`cache_desc`.`cache_id` WHERE `cache_desc`.`last_modified`>= \'' . $sModifiedSince . '\' AND `caches`.`status`!=4 AND `caches`.`status`!=5 AND `caches`.`status`!=6');
    for ($i = 0; $i < mysql_num_rows($rs); $i++) {
        $r = this_sql_fetch_array($rs);
        $naDescs[$r['id']] = $r['id'];
    }
    mysql_free_result($rs);
}

if ((($nPrimary == 0) && ($bUsers == 1)) || ($nPrimary == 4)) {
    // Users
    $rs = sql('SELECT `user_id` `id` FROM `user` WHERE `last_modified`>= \'' . $sModifiedSince . '\'');
    for ($i = 0; $i < mysql_num_rows($rs); $i++) {
        $r = this_sql_fetch_array($rs);
        $naUsers[$r['id']] = $r['id'];
    }
    mysql_free_result($rs);
}

if ((($nPrimary == 0) && ($bPictures == 1)) || ($nPrimary == 6)) {
    // Users
    $rs = sql('SELECT `pictures`.`id` FROM `pictures` INNER JOIN
                                               `caches` ON `pictures`.`object_type`=2 AND
                                                 `pictures`.`object_id`=`caches`.`cache_id`
                                           WHERE `pictures`.`last_modified` >= \'' . $sModifiedSince . '\' AND
                                                                                     `caches`.`status`!=4 AND
                                                 `caches`.`status`!=5 AND
                                                                                     `caches`.`status`!=6
                     UNION DISTINCT
                     SELECT `pictures`.`id` FROM `pictures` INNER JOIN
                                                 `cache_logs` ON `pictures`.`object_type`=1 AND
                                                 `pictures`.`object_id`=`cache_logs`.`id` INNER JOIN
                                                 `caches` ON `cache_logs`.`cache_id`=`caches`.`cache_id`
                                           WHERE `pictures`.`last_modified` >= \'' . $sModifiedSince . '\' AND
                                                 `caches`.`status`!=4 AND
                                                 `caches`.`status`!=5 AND
                                                                                     `caches`.`status`!=6 AND
                                                                                     `cache_logs`.`deleted`=0
                                                                                     ');



    for ($i = 0; $i < mysql_num_rows($rs); $i++) {
        $r = this_sql_fetch_array($rs);
        $naPictures[$r['id']] = $r['id'];
    }
    mysql_free_result($rs);
}

if ((($nPrimary == 0) && ($bRemovedObjects == 1)) || ($nPrimary == 7)) {
    // Users
    $rs = sql('SELECT `id` FROM `removed_objects` WHERE `removed_date`>=\'' . $sModifiedSince . '\'');
    for ($i = 0; $i < mysql_num_rows($rs); $i++) {
        $r = this_sql_fetch_array($rs);
        $naRemovedObjects[$r['id']] = $r['id'];
    }
    mysql_free_result($rs);
}

/* end object selection */

// Abh�ngigkeiten suchen
// TODO ...

/* begin output */
// ausgeben
header("Content-type: application/xml");

if ($bXmlDecl == '1')
    echo '<?xml version="1.0" encoding="iso-8859-2" standalone="no" ?>' . "\n";
if ($bDocType == '1')
    echo '<!DOCTYPE oc10xml PUBLIC "-//Opencaching Network//DTD OCXml V 1.0//EN" "http://www.opencaching.pl/xml/ocxml10.dtd">' . "\n";
if ($bOcXmlTag == '1')
    echo '<oc10xml version="1.0" date="' . date($sDateformat) . '" since="' . $sModifiedSince . '">' . "\n";

// Logs
if (count($naCachelogs) > 0) {
    $sIds = '';
    foreach ($naCachelogs AS $id)
        $sIds .= ', ' . $id;
    $sIds = substr($sIds, 2);

    $rs = sql('SELECT `id`, `cache_id`, `user_id`, `type`, `date`, `text`, `date_created`, `last_modified`, `UUID` FROM cache_logs WHERE `cache_logs`.`deleted`=0 AND id IN (' . $sIds . ')');
    for ($i = 0; $i < mysql_num_rows($rs); $i++) {
        $r = this_sql_fetch_array($rs);

        echo $t1 . '<cachelog>' . "\n";
        echo $t2 . '<id id="' . $r['id'] . '">' . $r['UUID'] . '</id>' . "\n";
        echo $t2 . '<cacheid id="' . $r['cache_id'] . '">' . cache_id2uuid($r['cache_id']) . '</cacheid>' . "\n";
        echo $t2 . '<userid id="' . $r['user_id'] . '">' . user_id2uuid($r['user_id']) . '</userid>' . "\n";
        echo $t2 . '<type id="' . $r['type'] . '">' . htmlentities_iso88592($logtypes[$r['type']]) . '</type>' . "\n";
        echo $t2 . '<date>' . date($sDateshort, strtotime($r['date'])) . '</date>' . "\n";
        echo $t2 . '<text>' . htmlentities_iso88592($r['text']) . '</text>' . "\n";
        echo $t2 . '<datecreated>' . date($sDateformat, strtotime($r['date_created'])) . '</datecreated>' . "\n";
        echo $t2 . '<lastmodified>' . date($sDateformat, strtotime($r['last_modified'])) . '</lastmodified>' . "\n";
        echo $t1 . '</cachelog>' . "\n";
    }
    mysql_free_result($rs);
}

// Caches
if (count($naCaches) > 0) {
    $sIds = '';
    foreach ($naCaches AS $id)
        $sIds .= ', ' . $id;
    $sIds = substr($sIds, 2);

    $rs = sql('SELECT `cache_id` id, `user_id`, `name`, `longitude`, `latitude`, `date_created`, `type`, `status`, `country`, `date_hidden`, `desc_languages`, `size`, `difficulty`, `terrain`, `uuid`, `last_modified`, `wp_oc`, `wp_gc`, `wp_nc` FROM `caches` WHERE `cache_id` IN (' . $sIds . ')');
    for ($i = 0; $i < mysql_num_rows($rs); $i++) {
        $r = this_sql_fetch_array($rs);

        echo $t1 . '<cache>' . "\n";
        echo $t2 . '<id id="' . $r['id'] . '">' . $r['uuid'] . '</id>' . "\n";
        echo $t2 . '<userid id="' . $r['user_id'] . '">' . user_id2uuid($r['user_id']) . '</userid>' . "\n";
        echo $t2 . '<username>' . htmlentities_iso88592(user_id2uuid2($r['user_id'])) . '</username>' . "\n";
        echo $t2 . '<name>' . htmlentities_iso88592(filterevilchars($r['name'])) . '</name>' . "\n";
        echo $t2 . '<longitude>' . sprintf('%01.5f', $r['longitude']) . '</longitude>' . "\n";
        echo $t2 . '<latitude>' . sprintf('%01.5f', $r['latitude']) . '</latitude>' . "\n";
        echo $t2 . '<type id="' . $r['type'] . '" short="' . $cachetypes[$r['type']]['short'] . '">' . htmlentities_iso88592($cachetypes[$r['type']]['pl']) . '</type>' . "\n";
        echo $t2 . '<status id="' . $r['status'] . '">' . htmlentities_iso88592($cachestatus[$r['status']]['pl']) . '</status>' . "\n";
        echo $t2 . '<country id="' . $r['country'] . '">' . htmlentities_iso88592($counties[$r['country']]['pl']) . '</country>' . "\n";
        echo $t2 . '<size id="' . $r['size'] . '">' . htmlentities_iso88592($cachesizes[$r['size']]['pl']) . '</size>' . "\n";
        echo $t2 . '<desclanguages>' . htmlentities($r['desc_languages']) . '</desclanguages>' . "\n";
        echo $t2 . '<difficulty>' . $r['difficulty'] . '</difficulty>' . "\n";
        echo $t2 . '<terrain>' . $r['terrain'] . '</terrain>' . "\n";
        echo $t2 . '<waypoints ocpl="' . addslashes(filterevilchars($r['wp_oc'])) . '" gccom="' . addslashes(filterevilchars($r['wp_gc'])) . '" gpsgames="' . addslashes(filterevilchars($r['wp_nc'])) . '"></waypoints>' . "\n";
        echo $t2 . '<datehidden>' . date($sDateshort, strtotime($r['date_hidden'])) . '</datehidden>' . "\n";
        echo $t2 . '<datecreated>' . date($sDateformat, strtotime($r['date_created'])) . '</datecreated>' . "\n";
        echo $t2 . '<lastmodified>' . date($sDateformat, strtotime($r['last_modified'])) . '</lastmodified>' . "\n";
        echo $t1 . '</cache>' . "\n";
    }
    mysql_free_result($rs);
}

// Cachedesc
if (count($naDescs) > 0) {
    $sIds = '';
    foreach ($naDescs AS $id)
        $sIds .= ', ' . $id;
    $sIds = substr($sIds, 2);

    $rs = sql('SELECT `id`, `cache_id`, `language`, `desc`, `desc_html`, `hint`, `short_desc`, `last_modified`, `uuid` FROM `cache_desc` WHERE `id` IN (' . $sIds . ')');
    for ($i = 0; $i < mysql_num_rows($rs); $i++) {
        $r = this_sql_fetch_array($rs);

        $sHTML = ($r['desc_html'] == '1') ? '1' : '0';
//          $new_desc = filterevilchars(htmlentities_iso88592($r['desc']));
        echo $t1 . '<cachedesc>' . "\n";
        echo $t2 . '<id id="' . $r['id'] . '">' . $r['uuid'] . '</id>' . "\n";
        echo $t2 . '<cacheid id="' . $r['cache_id'] . '">' . cache_id2uuid($r['cache_id']) . '</cacheid>' . "\n";
        echo $t2 . '<language id="' . $r['language'] . '">' . htmlentities($languages[$r['language']]['pl']) . '</language>' . "\n";
        echo $t2 . '<shortdesc>' . htmlentities_iso88592($r['short_desc']) . '</shortdesc>' . "\n";
        echo $t2 . '<desc html="' . $sHTML . '">' . htmlentities_iso88592(filterevilchars($r['desc'])) . '</desc>' . "\n";
        echo $t2 . '<hint>' . $r['hint'] . '</hint>' . "\n";
        echo $t2 . '<lastmodified>' . date($sDateformat, strtotime($r['last_modified'])) . '</lastmodified>' . "\n";
        echo $t1 . '</cachedesc>' . "\n";
    }
    mysql_free_result($rs);
}

// User
if (count($naUsers) > 0) {
    $sIds = '';
    foreach ($naUsers AS $id)
        $sIds .= ', ' . $id;
    $sIds = substr($sIds, 2);

    $rs = sql('SELECT `user_id`, `username`, `pmr_flag`, `date_created`, `uuid`, `last_modified` FROM `user` WHERE `user_id` IN (' . $sIds . ')');
    for ($i = 0; $i < mysql_num_rows($rs); $i++) {
        $r = this_sql_fetch_array($rs);

        $sPMR = ($r['pmr_flag'] == '1') ? '1' : '0';

        echo $t1 . '<user>' . "\n";
        echo $t2 . '<id id="' . $r['user_id'] . '">' . $r['uuid'] . '</id>' . "\n";
        echo $t2 . '<username>' . htmlentities_iso88592(filterevilchars($r['username'])) . '</username>' . "\n";
        echo $t2 . '<pmr>' . $sPMR . '</pmr>' . "\n";
        echo $t2 . '<datecreated>' . date($sDateformat, strtotime($r['date_created'])) . '</datecreated>' . "\n";
        echo $t2 . '<lastmodified>' . date($sDateformat, strtotime($r['last_modified'])) . '</lastmodified>' . "\n";
        echo $t1 . '</user>' . "\n";
    }
    mysql_free_result($rs);
}

// Pictures
if (count($naPictures) > 0) {
    $sIds = '';
    foreach ($naPictures AS $id)
        $sIds .= ', ' . $id;
    $sIds = substr($sIds, 2);

    $rs = sql('SELECT `id`, `url`, `title`, `description`, `desc_html`, `object_id`, `object_type`, `date_created`, `uuid`, `last_modified` FROM `pictures` WHERE `id` IN (' . $sIds . ')');
    for ($i = 0; $i < mysql_num_rows($rs); $i++) {
        $r = this_sql_fetch_array($rs);

        $sHTML = ($r['desc_html'] == '1') ? '1' : '0';

        echo $t1 . '<picture>' . "\n";
        echo $t2 . '<id id="' . $r['id'] . '">' . $r['uuid'] . '</id>' . "\n";
        echo $t2 . '<url>' . htmlentities(filterevilchars($r['url'])) . '</url>' . "\n";
        echo $t2 . '<title>' . htmlentities_iso88592(filterevilchars($r['title'])) . '</title>' . "\n";
        echo $t2 . '<object id="' . $r['object_id'] . '" type="' . $r['object_type'] . '" typename="' . $objecttypes[$r['object_type']] . '">' . object_id2uuid($r['object_id'], $r['object_type']) . '</object>' . "\n";
        echo $t2 . '<datecreated>' . date($sDateformat, strtotime($r['date_created'])) . '</datecreated>' . "\n";
        echo $t2 . '<lastmodified>' . date($sDateformat, strtotime($r['last_modified'])) . '</lastmodified>' . "\n";
        echo $t1 . '</picture>' . "\n";
    }
    mysql_free_result($rs);
}

// removed_objects
if (count($naRemovedObjects) > 0) {
    $sIds = '';
    foreach ($naRemovedObjects AS $id)
        $sIds .= ', ' . $id;
    $sIds = substr($sIds, 2);

    $rs = sql('SELECT `id`, `localid`, `uuid`, `type`, `removed_date` FROM `removed_objects` WHERE `id` IN (' . $sIds . ')');
    for ($i = 0; $i < mysql_num_rows($rs); $i++) {
        $r = this_sql_fetch_array($rs);

        echo $t1 . '<removedobject>' . "\n";
        echo $t2 . '<id id="' . $r['id'] . '" />' . "\n";
        echo $t2 . '<object id="' . $r['localid'] . '" type="' . $r['type'] . '" typename="' . $objecttypes[$r['type']] . '">' . $r['uuid'] . '</object>' . "\n";
        echo $t2 . '<removeddate>' . date($sDateformat, strtotime($r['removed_date'])) . '</removeddate>' . "\n";
        echo $t1 . '</removedobject>' . "\n";
    }
    mysql_free_result($rs);
}

if ($bOcXmlTag == '1')
    echo '</oc10xml>' . "\n";

/* end output */

/* begin some useful functions */

function filterevilchars($str)
{
    $evilchars = array(173 => 173, 167 => 167, 160 => 160, 157 => 157, 144 => 144, 143 => 143,
        141 => 141, 129 => 129, 127 => 127, 34 => 34, 31 => 31, 30 => 30,
        29 => 29, 28 => 28, 27 => 27, 26 => 26, 25 => 25, 24 => 24,
        23 => 23, 22 => 22, 21 => 21, 20 => 20, 19 => 19, 18 => 18,
        17 => 17, 16 => 16, 15 => 15, 14 => 14, 12 => 12, 11 => 11,
        9 => 9, 8 => 8, 7 => 7, 6 => 6, 5 => 5, 4 => 4, 3 => 3,
        2 => 2, 1 => 1, 0 => 0);

    foreach ($evilchars AS $ascii)
        $str = str_replace(chr($ascii), '', $str);

    return $str;
}

function object_id2uuid($objectid, $objecttype)
{
    if ($objecttype == '1')
        return log_id2uuid($objectid);
    elseif ($objecttype == '2')
        return cache_id2uuid($objectid);
    elseif ($objecttype == '4')
        return user_id2uuid($objectid);
}

function cache_id2uuid($id)
{
    global $dblink;

    $rs = sql('SELECT uuid FROM caches WHERE cache_id=' . sql_escape($id));
    $r = this_sql_fetch_array($rs);
    mysql_free_result($rs);
    return $r['uuid'];
}

function log_id2uuid($id)
{
    global $dblink;

    $rs = sql('SELECT uuid FROM cache_logs WHERE `cache_logs`.`deleted`=0 AND id=' . sql_escape($id));
    $r = this_sql_fetch_array($rs);
    mysql_free_result($rs);
    return $r['uuid'];
}

function user_id2uuid($id)
{
    global $dblink;

    $rs = sql('SELECT uuid FROM user WHERE user_id=' . sql_escape($id));
    $r = this_sql_fetch_array($rs);
    mysql_free_result($rs);
    return $r['uuid'];
}

function user_id2uuid2($id)
{
    global $dblink;

    $rs = sql('SELECT username FROM user WHERE user_id=' . sql_escape($id));
    $r = this_sql_fetch_array($rs);
    mysql_free_result($rs);
    return $r['username'];
}

function this_sql_fetch_array($rs)
{
    $r = mysql_fetch_assoc($rs);
    foreach ($r AS $k => $v) {
        if ($v != null) {
            $conv = @iconv("UTF-8", "ISO-8859-2", $v);
            if ($conv != false)
                $r[$k] = $conv;
            else
                $r[$k] = '--- Charset conversion error ---';
        }
    }
    return $r;
}

function htmlentities_iso88592($string = '')
{
    $pl_iso = array('&ecirc;', '&oacute;', '&plusmn;', '&para;',
        '&sup3;', '&iquest;', '&frac14;', '&aelig;', '&ntilde;', '&Ecirc;',
        '&Oacute;', '&iexcl;', '&brvbar;', '&pound;', '&not;', '&macr;',
        '&AElig;', '&Ntilde;', '&deg;', '&auml;', '&Auml;', '&uuml;', '&Uuml;', '&ouml;', '&Ouml;', '&szlig;', '&acute;');
    $entitles = get_html_translation_table(HTML_ENTITIES);
    $entitles = array_diff($entitles, $pl_iso);
    return strtr($string, $entitles);
}

/*
  $pl_iso = array( '&ecirc;' , '&oacute;' , '&plusmn;' , '&para;' ,
  '&sup3;' , '&iquest;' , '&frac14;' , '&aelig;' , '&ntilde;' , '&Ecirc;' ,
  '&Oacute;' , '&iexcl;' , '&brvbar;' , '&pound;' , '&not;' , '&macr;' ,
  '&AElig;' , '&Ntilde;' );

  function htmlentities_iso88592($r)
  {
  $pl_iso = array('�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�');
  $entitles = get_html_translation_table(HTML_ENTITIES);
  $entitles = array_diff($entitles, $pl_iso);
  return strtr($string, $entitles);
  }
 */
?>
