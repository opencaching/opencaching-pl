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


  /* begin configuration */

require('../lib/settings.inc.php');

/*
  if you dont want to include the settings.inc.php, these values have to be specified
  Note that pconnect isn't supported here

  $dbserver = '127.0.0.1';
  $dbname = 'ocde';
  $dbusername = 'ocde';
  $dbpasswd = 'ocde';
 */

/* end configuration */

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
$bFail = false;
$dblink = @mysql_connect($dbserver, $dbusername, $dbpasswd);
if ($dblink != false) {
    //database connection established ... set the used database
    if (@mysql_select_db($dbname, $dblink) == false) {
        $bFail = true;
        mysql_close($dblink);
        $dblink = false;
    }
} else
    $bFail = true;

if ($bFail == true) {
    echo 'Unable to connect to database';
    exit;
}
/* end db connect */
mysql_query("SET NAMES 'latin2'");

/* begin now a few dynamically loaded constants */


$cachetypes = array();
$rs = mysql_query('SELECT `id`, `short`, `pl` FROM cache_type', $dblink);
for ($i = 0; $i < mysql_num_rows($rs); $i++) {
    $r = mysql_fetch_array($rs);
    $cachetypes[$r['id']]['pl'] = $r['pl'];
    $cachetypes[$r['id']]['short'] = $r['short'];
}
mysql_free_result($rs);

$cachestatus = array();
$rs = mysql_query('SELECT `id`, `pl` FROM cache_status', $dblink);
for ($i = 0; $i < mysql_num_rows($rs); $i++) {
    $r = mysql_fetch_array($rs);
    $cachestatus[$r['id']]['pl'] = $r['pl'];
}
mysql_free_result($rs);

$counties = array();
$rs = mysql_query('SELECT `short`, `pl` FROM countries', $dblink);
for ($i = 0; $i < mysql_num_rows($rs); $i++) {
    $r = mysql_fetch_array($rs);
    $counties[$r['short']]['pl'] = $r['pl'];
}
mysql_free_result($rs);

$cachesizes = array();
$rs = mysql_query('SELECT `id`, `pl` FROM cache_size', $dblink);
for ($i = 0; $i < mysql_num_rows($rs); $i++) {
    $r = mysql_fetch_array($rs);
    $cachesizes[$r['id']]['pl'] = $r['pl'];
}
mysql_free_result($rs);

$username = array();
$rs = mysql_query('SELECT `user_id`, `username` FROM user', $dblink);
for ($i = 0; $i < mysql_num_rows($rs); $i++) {
    $r = mysql_fetch_array($rs);
    $username[$r['user_id']] = $r['username'];
}
mysql_free_result($rs);

$languages = array();
$rs = mysql_query('SELECT `short`, `pl` FROM languages', $dblink);
for ($i = 0; $i < mysql_num_rows($rs); $i++) {
    $r = mysql_fetch_array($rs);
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

$naCaches = array();
$naDescs = array();
$naUsers = array();

if ((($nPrimary == 0) && ($bCaches == 1)) || ($nPrimary == 2)) {
    // Caches
    $rs = mysql_query('SELECT cache_id id FROM caches WHERE last_modified >= \'' . $sModifiedSince . '\'', $dblink);
    for ($i = 0; $i < mysql_num_rows($rs); $i++) {
        $r = mysql_fetch_array($rs);
        $naCaches[$r['id']] = $r['id'];
    }
    mysql_free_result($rs);
}

if ((($nPrimary == 0) && ($bDescs == 1)) || ($nPrimary == 3)) {
    // Cachesdesc
    mysql_query("SET NAMES 'latin2'");
    $rs = mysql_query('SELECT id FROM cache_desc WHERE last_modified >= \'' . $sModifiedSince . '\'', $dblink);
    for ($i = 0; $i < mysql_num_rows($rs); $i++) {
        $r = mysql_fetch_array($rs);
        $naDescs[$r['id']] = $r['id'];
    }
    mysql_free_result($rs);
}

if ((($nPrimary == 0) && ($bUsers == 1)) || ($nPrimary == 4)) {
    // Users
    $rs = mysql_query('SELECT user_id id FROM user WHERE last_modified >= \'' . $sModifiedSince . '\'', $dblink);
    for ($i = 0; $i < mysql_num_rows($rs); $i++) {
        $r = mysql_fetch_array($rs);
        $naUsers[$r['id']] = $r['id'];
    }
    mysql_free_result($rs);
}


/* end object selection */


/* begin output */
// ausgeben
header("Content-type: application/xml");

if ($bXmlDecl == '1')
    echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
//  if ($bDocType == '1') echo '<!DOCTYPE oc10xml PUBLIC "-//Opencaching Network//DTD OCXml V 1.0//EN" "http://www.opencaching.pl/xml/ocxml10.dtd">' . "\n";
if ($bOcXmlTag == '1')
    echo '<rss version="2.0" xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" xmlns:ymaps="http://api.maps.yahoo.com/Maps/V2/AnnotatedMaps.xsd"><channel>
     <title>Polish Caches OC.pl</title><link><![CDATA[http://www.opencaching.pl]]></link><description>OC.pl Caches</description>' . "\n";
echo '<ymaps:Groups>
<group>
<Title>Unknown Type Cache</Title>
<Id>Nieznana</Id>
<BaseIcon width="16" height="16"><![CDATA[http://opencaching.pl/tpl/stdstyle/images/cache/16x16-unknown.png]]></BaseIcon>
</group>

<group>
<Title>Traditional Cache</Title>
<Id>Tradycyjna</Id>
<BaseIcon width="16" height="16"><![CDATA[http://opencaching.pl/tpl/stdstyle/images/cache/16x16-traditional.png]]></BaseIcon>
</group>

<Group>
<Title>Multi Cache</Title>
<Id>Multicache</Id>
<BaseIcon width="16" height="16"><![CDATA[http://opencaching.pl/tpl/stdstyle/images/cache/16x16-multi.png]]></BaseIcon>
</Group>

<group>
<Title>Virtual Cache</Title>
<Id>Virtual</Id>
<BaseIcon width="16" height="16"><![CDATA[http://opencaching.pl/tpl/stdstyle/images/cache/16x16-virtual.png]]></BaseIcon>
</group>

<group>
<Title>WebCam Cache</Title>
<Id>Webcam</Id>
<BaseIcon width="16" height="16"><![CDATA[http://opencaching.pl/tpl/stdstyle/images/cache/16x16-webcam.png]]></BaseIcon>
</group>
<group>

<Title>Event Cache</Title>
<Id>Wydarzenie</Id>
<BaseIcon width="16" height="16"><![CDATA[http://opencaching.pl/tpl/stdstyle/images/cache/16x16-event.png]]></BaseIcon>
</group>

<group>
<Title>Mystery/Quiz Cache</Title>
<Id>Quiz</Id>
<BaseIcon width="16" height="16"><![CDATA[http://opencaching.pl/tpl/stdstyle/images/cache/16x16-quiz.png]]></BaseIcon>
</group>

<group>
<Title>Mobile Cache</Title>
<Id>Mobilna</Id>
<BaseIcon width="16" height="16"><![CDATA[http://opencaching.pl/tpl/stdstyle/images/cache/16x16-moving.gif]]></BaseIcon>
</group>



</ymaps:Groups>';


// Caches
if (count($naCaches) > 0) {
    $sIds = '';
    foreach ($naCaches AS $id)
        $sIds .= ', ' . $id;
    $sIds = substr($sIds, 2);

    $rs = mysql_query('SELECT `cache_id` id, `user_id`, `name`, `longitude`, `latitude`, `date_created`, `type`, `status`, `country`, `date_hidden`, `desc_languages`, `size`, `difficulty`, `terrain`, `uuid`, `last_modified`, `wp_gc`, `wp_nc`, `wp_oc` FROM `caches` WHERE `country`!="AT" AND `status`=1 AND `cache_id` IN (' . $sIds . ')', $dblink);
    for ($i = 0; $i < mysql_num_rows($rs); $i++) {
        $r = mysql_fetch_array($rs);

        switch ($r['type']) {
            case 1:
                $icon = 'Nietypowa';
                break;
            case 2:
                $icon = 'Tradycyjna';
                break;
            case 3:
                $icon = 'Multicache';
                break;
            case 4:
                $icon = 'Virtual';
                break;
            case 5:
                $icon = 'Webcam';
                break;
            case 6:
                $icon = 'Wydarzenie';
                break;
            case 7:
                $icon = 'Quiz';
                break;
            /*      case 8:
              $icon = 'Matenatyczna';
              break;
             */
            case 9:
                $icon = 'Mobilna';
                break;
            /*      case 10:
              $icon = 'Drive-in';
              break;
             */
        }
        $iso2utf8tr = array(
            "\261" => "\xc4\x85", /* a */
            "\346" => "\xc4\x87", /* c */
            "\352" => "\xc4\x99", /* e */
            "\263" => "\xc5\x82", /* l */
            "\361" => "\xc5\x84", /* n */
            "\363" => "\xc3\xb3", /* o- */
            "\266" => "\xc5\x9b", /* s */
            "\274" => "\xc5\xbc", /* z- */
            "\277" => "\xc5\xba", /* z */
            "\241" => "\xc4\x84", /* A */
            "\306" => "\xc4\x86", /* C */
            "\312" => "\xc4\x98", /* E */
            "\243" => "\xc5\x81", /* L */
            "\321" => "\xc5\x83", /* N */
            "\323" => "\xc3\x93", /* O */
            "\246" => "\xc5\x9a", /* S */
            "\254" => "\xc5\xbb", /* Z */
            "\257" => "\xc5\xb9" /* Z */
        );

        $iso_string = $r['name'];
        $utf8 = strtr($iso_string, $iso2utf8tr);
// ' by ' . $username[$r['user_id']] .
        echo $t1 . '<item>' . "\n";
        echo $t2 . '<title>' . $r['wp_oc'] . ' - ' . $utf8 . '</title>' . "\n";
        echo $t2 . '<link><![CDATA[http://www.opencaching.pl/viewcache.php?cacheid=' . $r['id'] . ']]></link>' . "\n";
        echo $t2 . '<description>' . 'Zalozona przez: ' . $username[$r['user_id']] . ' .::. Rodzaj: ' . $icon . ' .::. Zadanie: ' . sprintf('%01.1f', $r['difficulty'] / 2) . ' .::. Teren: ' . sprintf('%01.1f', $r['terrain'] / 2) . '</description>' . "\n";
        echo $t2 . '<geo:lat>' . sprintf('%01.5f', $r['latitude']) . '</geo:lat>' . "\n";
        echo $t2 . '<geo:long>' . sprintf('%01.5f', $r['longitude']) . '</geo:long>' . "\n";
//          echo $t2 . '<ymaps:Address>' . 'Wielkosc: ' . $r['size'] . '</ymaps:Address>' . "\n";
//          echo $t2 . '<ymaps:CityState>' . 'Zadanie: ' . $r['difficulty'] . '</ymaps:CityState>' . "\n";
//          echo $t2 . '<ymaps:PhoneNumber>' . 'Teren: ' . $r['terrain'] . '</ymaps:PhoneNumber>' . "\n";
        echo $t2 . '<ymaps:GroupID>' . $icon . '</ymaps:GroupID>' . "\n";
        echo $t1 . '</item>' . "\n";
    }
    mysql_free_result($rs);
}


if ($bOcXmlTag == '1')
    echo '</channel></rss>' . "\n";

/* end output */

/* begin some useful functions */

function filterevilchars($str)
{
    $evilchars = array(173 => 173, 160 => 160, 157 => 157, 144 => 144, 143 => 143,
        141 => 141, 129 => 129, 127 => 127, 31 => 31, 30 => 30,
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

    $rs = mysql_query('SELECT uuid FROM caches WHERE cache_id=' . addslashes($id));
    $r = mysql_fetch_array($rs);
    mysql_free_result($rs);
    return $r['uuid'];
}

function log_id2uuid($id)
{
    global $dblink;

    $rs = mysql_query('SELECT uuid FROM cache_logs WHERE `cache_logs`.`deleted`=0 AND id=' . addslashes($id));
    $r = mysql_fetch_array($rs);
    mysql_free_result($rs);
    return $r['uuid'];
}

function user_id2uuid($id)
{
    global $dblink;

    $rs = mysql_query('SELECT uuid FROM user WHERE user_id=' . addslashes($id));
    $r = mysql_fetch_array($rs);
    mysql_free_result($rs);
    return $r['uuid'];
}

function htmlentities_iso88592($r)
{
    $pl_iso = array('ę', 'ó', '±', '¶', 'ł', 'ż', 'Ľ', 'ć', 'ń', 'Ę', 'Ó', 'ˇ', '¦', 'Ł', 'Ż', '¬', 'Ć', 'Ń');
    $entitles = get_html_translation_table(HTML_ENTITIES);
    $entitles = array_diff($entitles, $pl_iso);
    return strtr($string, $entitles);
}

/* end some useful functions */
?>
