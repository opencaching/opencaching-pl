<?php

use Utils\Database\XDb;
/* begin configuration */

$rootpath = '../';
require($rootpath . 'lib/common.inc.php');
require($rootpath . 'lib/export.inc.php');
require($rootpath . 'lib/calculation.inc.php');

if ($error == true) {
    echo 'Unable to connect to database';
    exit;
}

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

/* begin parameter reading */
global $bXmlCData;
global $sCharset;

// xml options
$bOcXmlTag = isset($_REQUEST['ocxmltag']) ? $_REQUEST['ocxmltag'] : '1';
$bDocType = isset($_REQUEST['doctype']) ? $_REQUEST['doctype'] : '1';
$bXmlDecl = isset($_REQUEST['xmldecl']) ? $_REQUEST['xmldecl'] : '1';
$sCharset = isset($_REQUEST['charset']) ? mb_strtolower($_REQUEST['charset']) : 'utf-8';
$bXmlCData = isset($_REQUEST['cdata']) ? $_REQUEST['cdata'] : '1';
$bAttrlist = isset($_REQUEST['attrlist']) ? $_REQUEST['attrlist'] : '0';

if ((($bOcXmlTag != '0') && ($bOcXmlTag != '1')) ||
        (($bDocType != '0') && ($bDocType != '1')) ||
        (($bXmlCData != '0') && ($bXmlCData != '1')) ||
        (($bAttrlist != '0') && ($bAttrlist != '1')) ||
        (($bXmlDecl != '0') && ($bXmlDecl != '1'))) {
    echo 'Invalid xml options value';
    exit;
}

if (($sCharset != 'iso-8859-2') && ($sCharset != 'utf-8')) {
    echo 'Invalid charset';
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

$ziptype = isset($_REQUEST['zip']) ? $_REQUEST['zip'] : 'zip';
if (($ziptype != '0') && ($ziptype != 'zip') && ($ziptype != 'gzip') && ($ziptype != 'bzip2')) {
    echo 'invalid zip type';
    exit;
}

// clean up ... 245h nach the last call  bylo 86400
$cleanerdate = date($sDateformat, time() - 10800);
$rs = XDb::xSql("SELECT `id` FROM `xmlsession` WHERE `last_use`< ? AND `cleaned`=0", $cleanerdate);

while ($r = XDb::xFetchArray($rs)) {
    // delete xmlsession_data
    XDb::xSql('DELETE FROM `xmlsession_data` WHERE `session_id`= ? ', $r['id']);

    // delete files
    $path = $zip_basedir . 'ocxml11/' . $r['id'];
    if (is_dir($path))
        unlinkrecursiv($path);

    // save cleaned
    XDb::xSql('UPDATE `xmlsession` SET `cleaned`=1 WHERE `id`= ? ', $r['id']);
}

if (isset($_REQUEST['sessionid'])) {
    $sessionid = $_REQUEST['sessionid'];
    $filenr = isset($_REQUEST['file']) ? $_REQUEST['file'] : '1';

    if (!mb_ereg_match('^[0-9]{1,11}', $sessionid))
        die('sessionid invalid');

    if (!mb_ereg_match('^[0-9]{1,11}', $filenr))
        die('filenr invalid');

    outputXmlSessionFile($sessionid, $filenr, $bOcXmlTag, $bDocType, $bXmlDecl, $ziptype);
}
else {
    // filter parameters
    $dModifiedsince = isset($_REQUEST['modifiedsince']) ? $_REQUEST['modifiedsince'] : '0';

    // selections
    $bCache = isset($_REQUEST['cache']) ? $_REQUEST['cache'] : '0';
    $bCachedesc = isset($_REQUEST['cachedesc']) ? $_REQUEST['cachedesc'] : '0';
    $bCachelog = isset($_REQUEST['cachelog']) ? $_REQUEST['cachelog'] : '0';
    $bUser = isset($_REQUEST['user']) ? $_REQUEST['user'] : '0';
    $bPicture = isset($_REQUEST['picture']) ? $_REQUEST['picture'] : '0';
    $bRemovedObject = isset($_REQUEST['removedobject']) ? $_REQUEST['removedobject'] : '0';
    $bPictureFromCachelog = isset($_REQUEST['picturefromcachelog']) ? $_REQUEST['picturefromcachelog'] : '0';

    // validation and parsing
    if (mb_strlen($dModifiedsince) != 14) {
        echo 'Invalid modifiedsince value (wrong length)';
        exit;
    }

    // convert to time
    $nYear = mb_substr($dModifiedsince, 0, 4);
    $nMonth = mb_substr($dModifiedsince, 4, 2);
    $nDay = mb_substr($dModifiedsince, 6, 2);
    $nHour = mb_substr($dModifiedsince, 8, 2);
    $nMinute = mb_substr($dModifiedsince, 10, 2);
    $nSecond = mb_substr($dModifiedsince, 12, 2);

    if ((!is_numeric($nYear)) && (!is_numeric($nMonth)) && (!is_numeric($nDay)) && (!is_numeric($nHour)) && (!is_numeric($nMinute)) && (!is_numeric($nSecond))) {
        echo 'Invalid modifiedsince value (non-numeric content)';
        exit;
    }

    if (($nYear < 1970) || ($nYear > 2100) || ($nMonth < 1) || ($nMonth > 12) || ($nDay < 1) || ($nDay > 31) || ($nHour < 0) || ($nHour > 23) || ($nMinute < 0) || ($nMinute > 59) || ($nSecond < 0) || ($nSecond > 59)) {
        echo 'Invalid modifiedsince value (value out of range)';
        exit;
    }
    $sModifiedSince = date('Y-m-d H:i:s', mktime($nHour, $nMinute, $nSecond, $nMonth, $nDay, $nYear));

    if ((($bCache != '0') && ($bCache != '1')) ||
            (($bCachedesc != '0') && ($bCachedesc != '1')) ||
            (($bCachelog != '0') && ($bCachelog != '1')) ||
            (($bUser != '0') && ($bUser != '1')) ||
            (($bPicture != '0') && ($bPicture != '1')) ||
            (($bRemovedObject != '0') && ($bRemovedObject != '1'))) {
        echo 'Invalid selection value';
        exit;
    }

    // selection options
    if (isset($_REQUEST['country'])) {
        $country = $_REQUEST['country'];

        if ( 1 != XDb::xMultiVariableQueryValue(
                'SELECT COUNT(*) FROM `countries` WHERE `short`= :1', 0, $country) ){
            die('Unknown country');
        }

        $selection['type'] = 1;
        $selection['country'] = $country;
    }
    else if (isset($_REQUEST['lat']) || isset($_REQUEST['lon']) || isset($_REQUEST['distance'])) {
        if (!(isset($_REQUEST['lat']) && isset($_REQUEST['lon']) && isset($_REQUEST['distance'])))
            die('lat, lon, distance: you have to specify all paramters');

        $lat = $_REQUEST['lat'];
        $lon = $_REQUEST['lon'];
        $distance = $_REQUEST['distance'];

        if (!is_numeric($lat))
            die('lat is no number');
        if (!is_numeric($lon))
            die('lon is no number');
        if (!is_numeric($distance))
            die('distance is no number');

        if (($lat < -180) || ($lat > 180))
            die('lat out of range');
        if (($lon < -180) || ($lon > 180))
            die('lon out of range');
        if (($distance < 0) || ($distance > 250))
            die('distance out of range [0, 250]');

        $selection['type'] = 2;
        $selection['lat'] = $lat;
        $selection['lon'] = $lon;
        $selection['distance'] = $distance;
    }
    else if (isset($_REQUEST['cacheid']) || isset($_REQUEST['wp']) || isset($_REQUEST['uuid'])) {

        $selection['type'] = 3;
        if (isset($_REQUEST['wp'])) {
            $wpl = $_REQUEST['wp'];
            $selection['cacheid'] = XDb::xMultiVariableQueryValue(
                "SELECT `cache_id` FROM `caches` WHERE `wp_oc`= :1 ", 0, $wpl);

        } else if (isset($_REQUEST['uuid'])) {
            $selection['cacheid'] = XDb::xMultiVariableQueryValue(
                "SELECT `cache_id` FROM `caches` WHERE `uuid`= :1", 0, $_REQUEST['uuid']);
        } else {
            $selection['cacheid'] = $_REQUEST['cacheid'] + 0;
        }
    } else
        $selection['type'] = 0;

    if ($selection['type'] != 0)
        if ($bUser == 1)
            die('selection used, user has to be 0');

    // session-management verwenden?
    $usesession = isset($_REQUEST['session']) ? $_REQUEST['session'] : 1;
    if (($usesession != 0) && ($usesession != 1))
        die('session-value invalid');

    $sessionid = startXmlSession($sModifiedSince, $bCache, $bCachedesc, $bCachelog, $bUser, $bPicture, $bRemovedObject, $bPictureFromCachelog, $selection);

    if ($usesession == 1) {
        $rs = XDb::xSql(
            'SELECT `users`, `caches`, `cachedescs`, `cachelogs`, `pictures`, `removedobjects`
            FROM `xmlsession` WHERE id= ?', $sessionid);
        $recordcount = XDb::xFetchArray($rs);
        XDb::xFreeResults($rs);

        if ($sCharset == 'iso-8859-2')
            header('Content-Type: application/xml; charset=ISO-8859-1');
        else if ($sCharset == 'utf-8')
            header('Content-Type: application/xml; charset=UTF-8');

        $xmloutput = '';
        if ($bXmlDecl == '1') {
            if ($sCharset == 'iso-8859-2')
                $xmloutput .= '<?xml version="1.0" encoding="iso-8859-1" standalone="yes" ?>' . "\n";
            else if ($sCharset == 'utf-8')
                $xmloutput .= '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>' . "\n";
        }
        if ($bOcXmlTag == '1')
            $xmloutput .= '<ocxmlsession>' . "\n";
        $xmloutput .= '  <sessionid>' . $sessionid . '</sessionid>' . "\n";
        $xmloutput .= '  <records user="' . $recordcount['users'] .
                '" cache="' . $recordcount['caches'] .
                '" cachedesc="' . $recordcount['cachedescs'] .
                '" cachelog="' . $recordcount['cachelogs'] .
                '" picture="' . $recordcount['pictures'] .
                '" removeobject="' . $recordcount['removedobjects'] . '" />' . "\n";
        if ($bOcXmlTag == '1')
            $xmloutput .= '</ocxmlsession>';

        if ($sCharset == 'iso-8859-2')
            echo iconv('UTF-8', 'ISO-8859-2', $xmloutput);
        else if ($sCharset == 'utf-8')
            echo $xmloutput;

        exit;
    }
    else {
        // return all records
        XDb::xSql('CREATE TEMPORARY TABLE `tmpxml_users` (`id` int(11), PRIMARY KEY (`id`)) SELECT `object_id` `id` FROM `xmlsession_data` WHERE `session_id`= ? AND `object_type`=4', $sessionid);
        XDb::xSql('CREATE TEMPORARY TABLE `tmpxml_caches` (`id` int(11), PRIMARY KEY (`id`)) SELECT `object_id` `id` FROM `xmlsession_data` WHERE `session_id`= ? AND `object_type`=2', $sessionid);
        XDb::xSql('CREATE TEMPORARY TABLE `tmpxml_cachedescs` (`id` int(11), PRIMARY KEY (`id`)) SELECT `object_id` `id` FROM `xmlsession_data` WHERE `session_id`= ? AND `object_type`=3', $sessionid);
        XDb::xSql('CREATE TEMPORARY TABLE `tmpxml_cachelogs` (`id` int(11), PRIMARY KEY (`id`)) SELECT `object_id` `id` FROM `xmlsession_data` WHERE `session_id`= ? AND `object_type`=1', $sessionid);
        XDb::xSql('CREATE TEMPORARY TABLE `tmpxml_pictures` (`id` int(11), PRIMARY KEY (`id`)) SELECT `object_id` `id` FROM `xmlsession_data` WHERE `session_id`= ? AND `object_type`=6', $sessionid);
        XDb::xSql('CREATE TEMPORARY TABLE `tmpxml_removedobjects` (`id` int(11), PRIMARY KEY (`id`)) SELECT `object_id` `id` FROM `xmlsession_data` WHERE `session_id`= ? AND `object_type`=7', $sessionid);

        outputXmlFile($sessionid, 0, $bXmlDecl, $bOcXmlTag, $bDocType, $ziptype);
    }
}

exit;

/* end parameter reading */

function outputXmlFile($sessionid, $filenr, $bXmlDecl, $bOcXmlTag, $bDocType, $ziptype)
{
    global $zip_basedir, $zip_wwwdir, $sDateformat, $sDateshort, $t1, $t2, $t3, $safemode_zip, $safemode_zip, $sCharset, $bAttrlist, $absolute_server_URI;
    // transfer all records from tmpxml_*

    if (!mb_ereg_match('^[0-9]{1,11}', $sessionid))
        die('sessionid invalid');

    if (!mb_ereg_match('^[0-9]{1,11}', $filenr))
        die('filenr invalid');

    /* begin now a few dynamically loaded constants */
    $logtypes = array();
    $rs = XDb::xSql('SELECT `id`, `pl` FROM log_types');
    while( $r = XDb::xFetchArray($rs) ){
        $logtypes[$r['id']] = $r['pl'];
    }
    XDb::xFreeResults($rs);

    $cachetypes = array();
    $rs = XDb::xSql('SELECT `id`, `short`, `pl` FROM cache_type');
    while( $r = XDb::xFetchArray($rs) ){
        $cachetypes[$r['id']]['pl'] = $r['pl'];
        $cachetypes[$r['id']]['short'] = $r['short'];
    }
    XDb::xFreeResults($rs);

    $cachestatus = array();
    $rs = XDb::xSql('SELECT `id`, `pl` FROM cache_status');
    while( $r = XDb::xFetchArray($rs) ){
        $cachestatus[$r['id']]['pl'] = $r['pl'];
    }
    XDb::xFreeResults($rs);

    $counties = array();
    $rs = XDb::xSql('SELECT `short`, `pl` FROM countries');
    while( $r = XDb::xFetchArray($rs) ){
        $counties[$r['short']]['pl'] = $r['pl'];
    }
    XDb::xFreeResults($rs);

    $cachesizes = array();
    $rs = XDb::xSql('SELECT `id`, `pl` FROM cache_size');
    while ( $r = XDb::xFetchArray($rs) ){
        $cachesizes[$r['id']]['pl'] = $r['pl'];
    }
    XDb::xFreeResults($rs);

    $languages = array();
    $rs = XDb::xSql('SELECT `short`, `pl` FROM languages');
    while( $r = XDb::xFetchArray($rs) ){
        $languages[$r['short']]['pl'] = $r['pl'];
    }
    XDb::xFreeResults($rs);

    $objecttypes['4'] = 'user';
    $objecttypes['2'] = 'cache';
    $objecttypes['3'] = 'cachedesc';
    $objecttypes['1'] = 'cachelog';
    $objecttypes['6'] = 'picture';

    /* end now a few dynamically loaded constants */

    // create temporary file
    if (!is_dir($zip_basedir . 'ocxml11/' . $sessionid))
        mkdir($zip_basedir . 'ocxml11/' . $sessionid);

    $fileid = 1;
    while (file_exists($zip_basedir . 'ocxml11/' . $sessionid . '/' . $sessionid . '-' . $filenr . '-' . $fileid . '.xml'))
        $fileid++;

    $xmlfilename = $zip_basedir . 'ocxml11/' . $sessionid . '/' . $sessionid . '-' . $filenr . '-' . $fileid . '.xml';

    $f = fopen($xmlfilename, 'w');

    if ($bXmlDecl == '1') {
        if ($sCharset == 'iso-8859-2')
            fwrite($f, '<?xml version="1.0" encoding="iso-8859-2" standalone="no" ?>' . "\n");
        else if ($sCharset == 'utf-8')
            fwrite($f, '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>' . "\n");
    }
    if ($bDocType == '1')
        fwrite($f, '<!DOCTYPE oc11xml PUBLIC "-//Opencaching Network//DTD OCXml V 1.1//EN" "http://www.opencaching.pl/xml/ocxml11.dtd">' . "\n");
    if ($bOcXmlTag == '1') {
        $rs = XDb::xSql(
            'SELECT `date_created`, `modified_since` FROM `xmlsession`
            WHERE `id`= ? LIMIT 1', $sessionid);
        $r = XDb::xFetchArray($rs);
        fwrite($f, '<oc11xml version="1.1" date="' . date($sDateformat, strtotime($r['date_created'])) . '" since="' . date($sDateformat, strtotime($r['modified_since'])) . '">' . "\n");
        XDb::xFreeResults($rs);
    }
    if ($bAttrlist == '1') {
        $rs = XDb::xSql(
            "SELECT `id`, `text_long`, `icon_large`, `icon_no`, `icon_undef`
            FROM `cache_attrib` WHERE `language`='pl'");
        fwrite($f, $t1 . '<attrlist>' . "\n");
        while ($r = XDb::xFetchArray($rs)) {
            fwrite($f, $t2 . '<attr id="' . $r['id'] . '" icon_large="' . xmlentities2($absolute_server_URI . $r['icon_large']) . '" icon_no="' . xmlentities2($absolute_server_URI . $r['icon_no']) . '" icon_undef="' . xmlentities2($absolute_server_URI . $r['icon_undef']) . '">' . xmlcdata($r['text_long']) . '</attr>' . "\n");
        }
        fwrite($f, $t1 . '</attrlist>' . "\n");
        XDb::xFreeResults($rs);
    }
    $rs = XDb::xSql(
        'SELECT `user`.`user_id` `id`, `user`.`node` `node`, `user`.`uuid` `uuid`, `user`.`username` `username`,
                `user`.`date_created` `date_created`, `user`.`last_modified` `last_modified`
        FROM `tmpxml_users`, `user`
        WHERE `tmpxml_users`.`id`=`user`.`user_id`');

    while ($r = XDb::xFetchArray($rs)) {
        fwrite($f, $t1 . '<user>' . "\n");

        fwrite($f, $t2 . '<id id="' . $r['id'] . '" node="' . $r['node'] . '">' . $r['uuid'] . '</id>' . "\n");
        fwrite($f, $t2 . '<username>' . xmlcdata($r['username']) . '</username>' . "\n");
        fwrite($f, $t2 . '<pmr>0</pmr>' . "\n");
        fwrite($f, $t2 . '<datecreated>' . date($sDateformat, strtotime($r['date_created'])) . '</datecreated>' . "\n");
        fwrite($f, $t2 . '<lastmodified>' . date($sDateformat, strtotime($r['last_modified'])) . '</lastmodified>' . "\n");

        fwrite($f, $t1 . '</user>' . "\n");
    }
    XDb::xFreeResults($rs);

    $rs = XDb::xSql(
                'SELECT `caches`.`cache_id` `id`, `caches`.`uuid` `uuid`, `caches`.`user_id` `user_id`,
                      `user`.`uuid` `useruuid`, `user`.`username` `username`, `caches`.`name` `name`,
                      `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, `caches`.`type` `type`,
                      `caches`.`country` `country`, `caches`.`size` `size`, `caches`.`desc_languages` `desclanguages`,
                      `caches`.`difficulty` `difficulty`, `caches`.`terrain` `terrain`, `caches`.`way_length` `way_length`,
                      `caches`.`search_time` `search_time`, `caches`.`wp_gc` `wp_gc`, `caches`.`wp_nc` `wp_nc`,
                      `caches`.`wp_oc` `wp_oc`, `caches`.`date_hidden` `date_hidden`, `caches`.`date_created` `date_created`,
                      `caches`.`last_modified` `last_modified`, `caches`.`status` `status`, `caches`.`node` `node`
                FROM `tmpxml_caches`, `caches`, `user`
                WHERE `tmpxml_caches`.`id`=`caches`.`cache_id`
                    AND `caches`.`user_id`=`user`.`user_id`');
    while ($r = XDb::xFetchArray($rs)) {
        fwrite($f, $t1 . '<cache>' . "\n");
        fwrite($f, $t2 . '<id id="' . $r['id'] . '" node="' . $r['node'] . '">' . $r['uuid'] . '</id>' . "\n");
        fwrite($f, $t2 . '<userid id="' . $r['user_id'] . '" uuid="' . $r['useruuid'] . '">' . xmlcdata($r['username']) . '</userid>' . "\n");
        fwrite($f, $t2 . '<name>' . xmlcdata($r['name']) . '</name>' . "\n");
        fwrite($f, $t2 . '<longitude>' . sprintf('%01.5f', $r['longitude']) . '</longitude>' . "\n");
        fwrite($f, $t2 . '<latitude>' . sprintf('%01.5f', $r['latitude']) . '</latitude>' . "\n");
        fwrite($f, $t2 . '<type id="' . $r['type'] . '" short="' . xmlentities2($cachetypes[$r['type']]['short']) . '">' . xmlcdata($cachetypes[$r['type']]['pl']) . '</type>' . "\n");
        fwrite($f, $t2 . '<status id="' . $r['status'] . '">' . xmlcdata($cachestatus[$r['status']]['pl']) . '</status>' . "\n");
        fwrite($f, $t2 . '<country id="' . $r['country'] . '">' . xmlcdata($counties[$r['country']]['pl']) . '</country>' . "\n");
        fwrite($f, $t2 . '<size id="' . $r['size'] . '">' . xmlcdata($cachesizes[$r['size']]['pl']) . '</size>' . "\n");
        fwrite($f, $t2 . '<desclanguages>' . $r['desclanguages'] . '</desclanguages>' . "\n");
        fwrite($f, $t2 . '<difficulty>' . sprintf('%01.1f', $r['difficulty'] / 2) . '</difficulty>' . "\n");
        fwrite($f, $t2 . '<terrain>' . sprintf('%01.1f', $r['terrain'] / 2) . '</terrain>' . "\n");
        fwrite($f, $t2 . '<rating waylength="' . $r['way_length'] . '" needtime="' . $r['search_time'] . '" />' . "\n");
        fwrite($f, $t2 . '<waypoints gccom="' . xmlentities2($r['wp_gc']) . '" gpsgames="' . xmlentities2($r['wp_nc']) . '" oc="' . xmlentities2($r['wp_oc']) . '" />' . "\n");
        fwrite($f, $t2 . '<datehidden>' . date($sDateformat, strtotime($r['date_hidden'])) . '</datehidden>' . "\n");
        fwrite($f, $t2 . '<datecreated>' . date($sDateformat, strtotime($r['date_created'])) . '</datecreated>' . "\n");
        fwrite($f, $t2 . '<lastmodified>' . date($sDateformat, strtotime($r['last_modified'])) . '</lastmodified>' . "\n");

        $rsAttributes = XDb::xSql(
            "SELECT `cache_attrib`.`id`, `cache_attrib`.`text_long`
            FROM `caches_attributes`
                INNER JOIN `cache_attrib` ON `caches_attributes`.`attrib_id`=`cache_attrib`.`id`
            WHERE `language`='pl' AND `caches_attributes`.`cache_id`= ? ", $r['id']);

        fwrite($f, $t2 . '<attributes>' . "\n");
        while ($rAttribute = XDb::xFetchArray($rsAttributes)) {
            fwrite($f, $t3 . '<attribute id="' . ($rAttribute['id'] + 0) . '">' . xmlcdata($rAttribute['text_long']) . '</attribute>' . "\n");
        }
        fwrite($f, $t2 . '</attributes>' . "\n");
        XDb::xFreeResults($rsAttributes);

        $rswaypoints = XDb::xSql(
            "SELECT `wp_id`, `type`, `longitude`, `latitude`,  `desc`, `status`, `stage`, waypoint_type.pl wp_type, waypoint_type.icon wp_icon
            FROM `waypoints`
                INNER JOIN waypoint_type ON (waypoints.type = waypoint_type.id)
            WHERE `cache_id`= ?
                AND (`status`='1' OR `status`='2')
            ORDER BY `stage`,`wp_id`", $r['id']);

        fwrite($f, $t2 . '<wpts>' . "\n");
        while ($rwpt = XDb::xFetchArray($rswaypoints)) {
            if ($rwpt['status'] == 1) {
                fwrite($f, $t3 . '<wpt lat="' . sprintf('%01.5f', $rwpt['latitude']) . '" lon="' . sprintf('%01.5f', $rwpt['longitude']) . '">' . "\n");
            } else {
                fwrite($f, $t3 . '<wpt lat="" lon="">' . "\n");
            }
            fwrite($f, $t3 . '<wptype> ' . $rwpt['wp_type'] . ' </wptype>' . "\n");
            fwrite($f, $t3 . '<stage> Etap: ' . ($rwpt['stage'] + 0) . '</stage>' . "\n");
            $rwpt['desc'] = mb_ereg_replace('<br />', '', $rwpt['desc']);
            $rwpt['desc'] = html_entity_decode($rwpt['desc'], ENT_COMPAT, 'UTF-8');
            fwrite($f, $t3 . '<desc> ' . xmlcdata($rwpt['desc']) . ' </desc>' . "\n");
            fwrite($f, $t3 . '</wpt>' . "\n");
        }
        fwrite($f, $t2 . '</wpts>' . "\n");
        XDb::xFreeResults($rswaypoints);

        fwrite($f, $t1 . '</cache>' . "\n");
    }
    XDb::xFreeResults($rs);

    $rs = XDb::xSql(
             'SELECT `cache_desc`.`id` `id`, `cache_desc`.`uuid` `uuid`, `cache_desc`.`cache_id` `cache_id`,
                      `cache_desc`.`language` `language`, `cache_desc`.`short_desc` `short_desc`,
                      `cache_desc`.`desc` `desc`, `cache_desc`.`desc_html` `desc_html`, `cache_desc`.`hint` `hint`,
                      `cache_desc`.`last_modified` `last_modified`, `caches`.`uuid` `cacheuuid`, `cache_desc`.`node` `node`
             FROM `tmpxml_cachedescs`, `cache_desc`, `caches`
             WHERE `tmpxml_cachedescs`.`id`=`cache_desc`.`id` AND `caches`.`cache_id`=`cache_desc`.`cache_id`');

    while ($r = XDb::xFetchArray($rs)) {
        fwrite($f, $t1 . '<cachedesc>' . "\n");

        fwrite($f, $t2 . '<id id="' . $r['id'] . '" node="' . $r['node'] . '">' . $r['uuid'] . '</id>' . "\n");
        fwrite($f, $t2 . '<cacheid id="' . $r['cache_id'] . '">' . $r['cacheuuid'] . '</cacheid>' . "\n");

        fwrite($f, $t2 . '<language id="' . $r['language'] . '">' . xmlcdata($languages[$r['language']]['pl']) . '</language>' . "\n");
        fwrite($f, $t2 . '<shortdesc>' . xmlcdata($r['short_desc']) . '</shortdesc>' . "\n");

        if ($r['desc_html'] == 0) {
            $r['desc'] = mb_ereg_replace('<br />', '', $r['desc']);
            $r['desc'] = html_entity_decode($r['desc'], ENT_COMPAT, 'UTF-8');
        }

        fwrite($f, $t2 . '<desc html="' . (($r['desc_html'] == 1) ? '1' : '0') . '">' . xmlcdata($r['desc']) . '</desc>' . "\n");

        $r['hint'] = mb_ereg_replace('<br />', '', $r['hint']);
        $r['hint'] = html_entity_decode($r['hint'], ENT_COMPAT, 'UTF-8');

        fwrite($f, $t2 . '<hint>' . xmlcdata($r['hint']) . '</hint>' . "\n");
        fwrite($f, $t2 . '<lastmodified>' . date($sDateformat, strtotime($r['last_modified'])) . '</lastmodified>' . "\n");

        fwrite($f, $t1 . '</cachedesc>' . "\n");
    }
    XDb::xFreeResults($rs);

    $rs = XDb::xSql(
        'SELECT `cache_logs`.`id` `id`, `cache_logs`.`cache_id` `cache_id`, `cache_logs`.`user_id` `user_id`,
                `cache_logs`.`type` `type`, `cache_logs`.`date` `date`, `cache_logs`.`text` `text`,
                `cache_logs`.`date_created` `date_created`, `cache_logs`.`last_modified` `last_modified`,
                `cache_logs`.`uuid` `uuid`, `user`.`username` `username`, `caches`.`uuid` `cacheuuid`,
                `user`.`uuid` `useruuid`, `cache_logs`.`node` `node`,
                IF(NOT ISNULL(`cache_rating`.`cache_id`) AND `cache_logs`.`type`=1, 1, 0) AS `recommended`
        FROM `cache_logs`
            INNER JOIN `tmpxml_cachelogs` ON `cache_logs`.`id`=`tmpxml_cachelogs`.`id`
            INNER JOIN `user` ON `cache_logs`.`user_id`=`user`.`user_id`
            INNER JOIN `caches` ON `caches`.`cache_id`=`cache_logs`.`cache_id`
            LEFT JOIN `cache_rating` ON `cache_logs`.`cache_id`=`cache_rating`.`cache_id`
                AND `cache_logs`.`user_id`=`cache_rating`.`user_id` AND `cache_logs`.`deleted`=0
                    ');
    while ($r = XDb::xFetchArray($rs)) {
        $r['text'] = mb_ereg_replace('<br />', '', $r['text']);
        $r['text'] = mb_ereg_replace('/&amp;#(38|60|62);/', '&#$1;', $r['text']);  // decode OKAPI logs
        $r['text'] = html_entity_decode($r['text'], ENT_COMPAT, 'UTF-8');

        fwrite($f, $t1 . '<cachelog>' . "\n");
        fwrite($f, $t2 . '<id id="' . $r['id'] . '" node="' . $r['node'] . '">' . $r['uuid'] . '</id>' . "\n");
        fwrite($f, $t2 . '<cacheid id="' . $r['cache_id'] . '">' . $r['cacheuuid'] . '</cacheid>' . "\n");
        fwrite($f, $t2 . '<userid id="' . $r['user_id'] . '" uuid="' . $r['useruuid'] . '">' . xmlcdata($r['username']) . '</userid>' . "\n");
        fwrite($f, $t2 . '<logtype id="' . $r['type'] . '" recommended="' . $r['recommended'] . '">' . xmlcdata($logtypes[$r['type']]) . '</logtype>' . "\n");
        fwrite($f, $t2 . '<date>' . date($sDateshort, strtotime($r['date'])) . '</date>' . "\n");
        fwrite($f, $t2 . '<text>' . xmlcdata($r['text']) . '</text>' . "\n");
        fwrite($f, $t2 . '<datecreated>' . date($sDateformat, strtotime($r['date_created'])) . '</datecreated>' . "\n");
        fwrite($f, $t2 . '<lastmodified>' . date($sDateformat, strtotime($r['last_modified'])) . '</lastmodified>' . "\n");
        fwrite($f, $t1 . '</cachelog>' . "\n");
    }
    XDb::xFreeResults($rs);

    $rs = XDb::xSql(
        'SELECT `pictures`.`id` `id`, `pictures`.`url` `url`, `pictures`.`title` `title`,
                `pictures`.`object_id` `object_id`, `pictures`.`object_type` `object_type`,
                `pictures`.`date_created` `date_created`, `pictures`.`uuid` `uuid`,
                `pictures`.`last_modified` `last_modified`, `pictures`.`display` `display`,
                `pictures`.`spoiler` `spoiler`, `pictures`.`node` `node`
        FROM `tmpxml_pictures`, `pictures`
        WHERE `tmpxml_pictures`.`id`=`pictures`.id');

    while ($r = XDb::xFetchArray($rs)) {
        fwrite($f, $t1 . '<picture>' . "\n");
        fwrite($f, $t2 . '<id id="' . $r['id'] . '" node="' . $r['node'] . '">' . $r['uuid'] . '</id>' . "\n");
        fwrite($f, $t2 . '<url>' . xmlcdata($r['url']) . '</url>' . "\n");
        fwrite($f, $t2 . '<title>' . xmlcdata($r['title']) . '</title>' . "\n");
        fwrite($f, $t2 . '<object id="' . $r['object_id'] . '" type="' . $r['object_type'] . '" typename="' . xmlentities2($objecttypes[$r['object_type']]) . '">' . object_id2uuid($r['object_id'], $r['object_type']) . '</object>' . "\n");
        fwrite($f, $t2 . '<attributes spoiler="' . $r['spoiler'] . '" display="' . $r['display'] . '" />' . "\n");
        fwrite($f, $t2 . '<datecreated>' . date($sDateformat, strtotime($r['date_created'])) . '</datecreated>' . "\n");
        fwrite($f, $t2 . '<lastmodified>' . date($sDateformat, strtotime($r['last_modified'])) . '</lastmodified>' . "\n");
        fwrite($f, $t1 . '</picture>' . "\n");
    }
    XDb::xFreeResults($rs);

    $rs = XDb::xSql(
        'SELECT `removed_objects`.`id` `id`, `removed_objects`.`localid` `localid`, `removed_objects`.`uuid` `uuid`,
                `removed_objects`.`type` `type`, `removed_objects`.`removed_date` `removed_date`, `removed_objects`.`node` `node`
        FROM `tmpxml_removedobjects`, `removed_objects`
        WHERE `removed_objects`.`id`=`tmpxml_removedobjects`.`id`');

    while ($r = XDb::xFetchArray($rs)) {
        fwrite($f, $t1 . '<removedobject>' . "\n");
        fwrite($f, $t2 . '<id id="' . $r['id'] . '" node="' . $r['node'] . '" />' . "\n");
        fwrite($f, $t2 . '<object id="' . $r['localid'] . '" type="' . $r['type'] . '" typename="' . xmlentities2($objecttypes[$r['type']]) . '">' . $r['uuid'] . '</object>' . "\n");
        fwrite($f, $t2 . '<removeddate>' . date($sDateformat, strtotime($r['removed_date'])) . '</removeddate>' . "\n");
        fwrite($f, $t1 . '</removedobject>' . "\n");
    }
    XDb::xFreeResults($rs);

    if ($bOcXmlTag == '1')
        fwrite($f, '</oc11xml>' . "\n");

    fclose($f);

    $rel_xmlfile = 'ocxml11/' . $sessionid . '/' . $sessionid . '-' . $filenr . '-' . $fileid . '.xml';
    $rel_zipfile = 'ocxml11/' . $sessionid . '/' . $sessionid . '-' . $filenr . '-' . $fileid;

    // zip and redirect url
    if ($ziptype == '0') {
        tpl_redirect($zip_wwwdir . 'ocxml11/' . $sessionid . '/' . $sessionid . '-' . $filenr . '-' . $fileid . '.xml');
        exit;
    } else if ($ziptype == 'zip')
        $rel_zipfile .= '.zip';
    else if ($ziptype == 'bzip2')
        $rel_zipfile .= '.bz2';
    else if ($ziptype == 'gzip')
        $rel_zipfile .= '.gz';
    else
        die('unknown zip type');

    $call = $safemode_zip . ' --type=' . escapeshellcmd($ziptype) . ' --src=' . escapeshellcmd($rel_xmlfile) . ' --dst=' . escapeshellcmd($rel_zipfile);
    system($call);

    if (!file_exists($zip_basedir . $rel_zipfile))
        die('all ok, but zip failed - internal server error');

    tpl_redirect($zip_wwwdir . $rel_zipfile);

    exit;
}

function startXmlSession($sModifiedSince, $bCache, $bCachedesc, $bCachelog, $bUser, $bPicture, $bRemovedObject, $bPictureFromCachelog, $selection)
{
    global $rootpath;

    // create session
    XDb::xSql(
        'INSERT INTO `xmlsession` (`last_use`, `modified_since`, `date_created`)
        VALUES (NOW(), ?, NOW())', date('Y-m-d H:i:s', strtotime($sModifiedSince)));
    $sessionid = XDb::xLastInsertId();

    $recordcount['caches'] = 0;
    $recordcount['cachedescs'] = 0;
    $recordcount['cachelogs'] = 0;
    $recordcount['users'] = 0;
    $recordcount['pictures'] = 0;
    $recordcount['removedobjects'] = 0;

    if ($selection['type'] == 0) {
        // without selection
        if ($bCache == 1) {
            $stmt = XDb::xSql(
                "INSERT INTO xmlsession_data (`session_id`, `object_type`, `object_id`)
                SELECT $sessionid, 2, `cache_id` FROM `caches`
                WHERE `last_modified` >= ? AND `status`!=5 AND `status`!=6 AND `status`!=4",
                $sModifiedSince);

            $recordcount['caches'] = XDb::xNumRows($stmt);
        }

        if ($bCachedesc == 1) {
            $stmt = XDb::xSql(
                "INSERT INTO `xmlsession_data` (`session_id`, `object_type`, `object_id`)
                 SELECT $sessionid, 3, `cache_desc`.`id`
                 FROM `cache_desc` INNER JOIN `caches` ON `cache_desc`.`cache_id`=`caches`.`cache_id`
                 WHERE `cache_desc`.`last_modified` >= ? AND `caches`.`status`!=5
                    AND `status`!=6 AND `status`!=4",
                 $sModifiedSince);

            $recordcount['cachedescs'] = XDb::xNumRows($stmt);
        }

        if ($bCachelog == 1) {
            $stmt = XDb::xSql(
                "INSERT INTO `xmlsession_data` (`session_id`, `object_type`, `object_id`)
                 SELECT $sessionid, 1, `cache_logs`.`id`
                 FROM `cache_logs` INNER JOIN `caches` ON `cache_logs`.`cache_id`=`caches`.`cache_id`
                 WHERE `cache_logs`.`last_modified` >= ? AND `caches`.`status`!=5
                    AND `status`!=6 AND `status`!=4 AND `cache_logs`.`deleted`=0",
                 $sModifiedSince);

            $recordcount['cachelogs'] = XDb::xNumRows($stmt);
        }

        if ($bUser == 1) {
            $stmt = XDb::xSql(
                "INSERT INTO `xmlsession_data` (`session_id`, `object_type`, `object_id`)
                 SELECT $sessionid, 4, `user_id` FROM `user` WHERE `last_modified` >= ? ",
                $sModifiedSince);

            $recordcount['users'] = XDb::xNumRows($stmt);
        }

        if ($bPicture == 1) {
            $stmt = XDb::xSql(
                "INSERT INTO `xmlsession_data` (`session_id`, `object_type`, `object_id`)
                 SELECT $sessionid, 6, `pictures`.`id`
                 FROM `pictures`
                    INNER JOIN `caches` ON `pictures`.`object_type`=2
                        AND `pictures`.`object_id`=`caches`.`cache_id`
                 WHERE `pictures`.`last_modified` >= ?
                    AND `caches`.`status`!=5 AND `status`!=6 AND `status`!=4
                 UNION DISTINCT
                 SELECT $sessionid, 6, `pictures`.`id`
                 FROM `pictures`
                    INNER JOIN `cache_logs` ON `pictures`.`object_type`=1
                        AND `pictures`.`object_id`=`cache_logs`.`id`
                    INNER JOIN `caches` ON `cache_logs`.`cache_id`=`caches`.`cache_id`
                 WHERE `pictures`.`last_modified` >= ?
                    AND `caches`.`status`!=5 AND `status`!=6 AND `caches`.`status`!=4
                    AND `cache_logs`.`deleted`=0",
                $sModifiedSince, $sModifiedSince);

            $recordcount['pictures'] = XDb::xNumRows($stmt);
        }

        if ($bRemovedObject == 1) {
            $stmt = XDb::xSql(
                "INSERT INTO `xmlsession_data` (`session_id`, `object_type`, `object_id`)
                 SELECT $sessionid, 7, `id` FROM `removed_objects`
                 WHERE `removed_date` >= ? ", $sModifiedSince);

            $recordcount['removedobjects'] = XDb::xNumRows($stmt);
        }
    } else {
        $qWhere = '';
        $qHaving = '';

        if ($selection['type'] == 1) {
            XDb::xSql(
                "CREATE TEMPORARY TABLE `tmpxmlSesssionCaches` (`cache_id` int(11), PRIMARY KEY (`cache_id`)) ENGINE=MEMORY
                 SELECT DISTINCT `cache_countries`.`cache_id`
                 FROM `caches`, `cache_countries`
                 WHERE `caches`.`cache_id`=`cache_countries`.`cache_id` AND `cache_countries`.`country`= ?
                    AND `caches`.`status`!=5 AND `status`!=6 AND `status`!=4",
                $selection['country']);

        } else if ($selection['type'] == 2) {
            require_once($rootpath . 'lib/search.inc.php');

            XDb::xSql(
                'CREATE TEMPORARY TABLE `tmpxmlSesssionCaches` (`cache_id` int(11), `distance` double, KEY (`cache_id`)) ENGINE=MEMORY
                 SELECT `cache_coordinates`.`cache_id`,'.
                        getSqlDistanceFormula($selection['lon'], $selection['lat'], $selection['distance'], 'cache_coordinates') . ' `distance`
                 FROM `caches`, `cache_coordinates`
                 WHERE `cache_coordinates`.`cache_id`=`caches`.`cache_id` AND `caches`.`status`!=5
                     AND `status`!=6 AND `status`!=4
                     AND `cache_coordinates`.`latitude` > ' . getMinLat($selection['lon'], $selection['lat'], $selection['distance']) . '
                     AND `cache_coordinates`.`latitude` < ' . getMaxLat($selection['lon'], $selection['lat'], $selection['distance']) . '
                     AND `cache_coordinates`.`longitude` >' . getMinLon($selection['lon'], $selection['lat'], $selection['distance']) . '
                     AND `cache_coordinates`.`longitude` < ' . getMaxLon($selection['lon'], $selection['lat'], $selection['distance']) . '
                 HAVING `distance` < ' . ($selection['distance'] + 0) );

        } else if ($selection['type'] == 3) {
            XDb::xSql(
                "CREATE TEMPORARY TABLE `tmpxmlSesssionCaches` (`cache_id` int(11), PRIMARY KEY (`cache_id`)) ENGINE=MEMORY
                 SELECT ". $selection['cacheid'] ." AS cache_id" );
        }

        if ($bCache == 1) {
            $stmt = XDb::xSql(
                "INSERT INTO `xmlsession_data` (`session_id`, `object_type`, `object_id`)
                 SELECT DISTINCT $sessionid, 2, `tmpxmlSesssionCaches`.`cache_id`
                 FROM `tmpxmlSesssionCaches`, `caches`
                 WHERE `tmpxmlSesssionCaches`.`cache_id`=`caches`.`cache_id`
                     AND `caches`.`last_modified` >= ? ", $sModifiedSince);

            $recordcount['caches'] = XDb::xNumRows($stmt);
        }

        if ($bCachedesc == 1) {
            $stmt = XDb::xSql(
                "INSERT INTO `xmlsession_data` (`session_id`, `object_type`, `object_id`)
                 SELECT DISTINCT $sessionid, 3, `cache_desc`.`id`
                 FROM `cache_desc`, `tmpxmlSesssionCaches`
                 WHERE `cache_desc`.`cache_id`=`tmpxmlSesssionCaches`.`cache_id`
                     AND `cache_desc`.`last_modified` >= ? ", $sModifiedSince);

            $recordcount['cachedescs'] = XDb::xNumRows($stmt);
        }

        if ($bCachelog == 1) {
            $stmt = XDb::xSql(
                "INSERT INTO `xmlsession_data` (`session_id`, `object_type`, `object_id`)
                 SELECT DISTINCT $sessionid, 1, `cache_logs`.`id`
                 FROM `cache_logs`, `tmpxmlSesssionCaches`
                 WHERE `cache_logs`.`deleted`=0
                     AND `cache_logs`.`cache_id`=`tmpxmlSesssionCaches`.`cache_id`
                     AND `cache_logs`.`last_modified` >= ? ", $sModifiedSince);

            $recordcount['cachelogs'] = XDb::xNumRows($stmt);
        }

        if ($bPicture == 1) {
            // cachebilder
            $stmt = XDb::xSql(
                "INSERT INTO `xmlsession_data` (`session_id`, `object_type`, `object_id`)
                 SELECT DISTINCT $sessionid, 6, `pictures`.`id`
                 FROM `pictures`, `tmpxmlSesssionCaches`
                 WHERE `pictures`.`object_id`=`tmpxmlSesssionCaches`.`cache_id`
                     AND `pictures`.`object_type`=2
                     AND `pictures`.`last_modified` >= ? ", $sModifiedSince);

            $recordcount['pictures'] = XDb::xNumRows($stmt);

            // bilder von logs
            if ($bPictureFromCachelog == 1) {
                $stmt = XDb::xSql(
                    "INSERT INTO `xmlsession_data` (`session_id`, `object_type`, `object_id`)
                     SELECT DISTINCT $sessionid, 6, `pictures`.id
                     FROM `pictures` , `cache_logs`, `tmpxmlSesssionCaches`
                     WHERE `tmpxmlSesssionCaches`.`cache_id`=`cache_logs`.`cache_id`
                         AND `cache_logs`.`deleted`=0
                         AND `pictures`.`object_type`=1
                         AND `pictures`.`object_id`=`cache_logs`.`id`
                         AND `pictures`.`last_modified` >= ? ", $sModifiedSince);

                $recordcount['pictures'] += XDb::xNumRows($stmt);
            }
        }

        if ($bRemovedObject == 1) {
            $stmt = XDb::xSql(
                "INSERT INTO `xmlsession_data` (`session_id`, `object_type`, `object_id`)
                 SELECT DISTINCT $sessionid, 7, `id`
                 FROM `removed_objects`
                 WHERE `removed_date` >= ? ", $sModifiedSince);

            $recordcount['removedobjects'] = XDb::xNumRows($stmt);
        }
    }

    XDb::xSql(
        'UPDATE `xmlsession` SET `caches`= ?, `cachedescs`= ?, `cachelogs`= ?, `users`= ?, `pictures`= ?, `removedobjects`= ?
        WHERE `id`= ? LIMIT 1',
        $recordcount['caches'], $recordcount['cachedescs'], $recordcount['cachelogs'], $recordcount['users'],
        $recordcount['pictures'], $recordcount['removedobjects'], $sessionid);

    return $sessionid;
}

function outputXmlSessionFile($sessionid, $filenr, $bOcXmlTag, $bDocType, $bXmlDecl, $ziptype)
{
    XDb::xSql('UPDATE xmlsession SET last_use=NOW() WHERE id= ? ', $sessionid);

    /* begin calculate which records to transfer */

    $rs = XDb::xSql(
        'SELECT `users`, `caches`, `cachedescs`, `cachelogs`, `pictures`, `removedobjects`
        FROM `xmlsession` WHERE `id`= ? AND `cleaned`=0', $sessionid);

    if ( ! $rRecordsCount = XDb::xFetchArray($rs) )
        die('invalid sessionid');

    XDb::xFreeResults($rs);

    $startat = ($filenr - 1) * 500;
    if (($startat < 0) || ($startat > $rRecordsCount['users'] + $rRecordsCount['caches'] + $rRecordsCount['cachedescs'] + $rRecordsCount['cachelogs'] + $rRecordsCount['pictures'] + $rRecordsCount['removedobjects'] - 1))
        die('filenr out of range');

    $recordnr[0] = 0;
    $recordnr[1] = $rRecordsCount['users'];
    $recordnr[2] = $recordnr[1] + $rRecordsCount['caches'];
    $recordnr[3] = $recordnr[2] + $rRecordsCount['cachedescs'];
    $recordnr[4] = $recordnr[3] + $rRecordsCount['cachelogs'];
    $recordnr[5] = $recordnr[4] + $rRecordsCount['pictures'];
    $recordnr[6] = $recordnr[5] + $rRecordsCount['removedobjects'];

    if ($recordnr[6] > $startat + 500)
        $endat = $startat + 500;
    else
        $endat = $recordnr[6] - $startat;

    for ($i = 0; $i < 6; $i++) {
        if (($startat >= $recordnr[$i]) && ($startat + 500 < $recordnr[$i + 1])) {
            if ($recordnr[$i + 1] - $startat > 500)
                $limits[$i] = array('start' => $startat - $recordnr[$i], 'count' => 500);
            else
                $limits[$i] = array('start' => $startat - $recordnr[$i], 'count' => $recordnr[$i + 1] - $startat);

            //$limits[$i] = array('start' => 'a', 'count' => 'a');
        }
        else if (($startat >= $recordnr[$i]) && ($startat < $recordnr[$i + 1])) {
            $limits[$i] = array('start' => $startat - $recordnr[$i], 'count' => $recordnr[$i + 1] - $startat);
            //$limits[$i] = array('start' => 'b', 'count' => 'b');
        } else if (($startat + 500 >= $recordnr[$i]) && ($startat + 500 < $recordnr[$i + 1])) {
            if ($startat + 500 < $recordnr[$i + 1])
                $limits[$i] = array('start' => 0, 'count' => 500 - $recordnr[$i] + $startat);
            else
                $limits[$i] = array('start' => 0, 'count' => $recordnr[$i + 1] - $recordnr[$i]);

            if ($limits[$i]['count'] < 0)
                $limits[$i]['count'] = 0;

            //$limits[$i] = array('start' => 'c', 'count' => 'c');
        }
        else if (($startat < $recordnr[$i]) && ($startat + 500 >= $recordnr[$i + 1])) {
            $limits[$i] = array('start' => 0, 'count' => $recordnr[$i + 1] - $recordnr[$i]);
        } else
            $limits[$i] = array('start' => '0', 'count' => '0');

    }
    /* end calculate which records to transfer */

    XDb::xSql(
        'CREATE TEMPORARY TABLE `tmpxml_users` (`id` int(11), PRIMARY KEY (`id`)) SELECT `object_id` `id`
        FROM `xmlsession_data` WHERE `session_id`= ? AND `object_type`=4 LIMIT ' . $limits[0]['start'] . ',' . $limits[0]['count'],
        $sessionid);

    XDb::xSql(
        'CREATE TEMPORARY TABLE `tmpxml_caches` (`id` int(11), PRIMARY KEY (`id`)) SELECT `object_id` `id`
        FROM `xmlsession_data` WHERE `session_id`= ? AND `object_type`=2 LIMIT ' . $limits[1]['start'] . ',' . $limits[1]['count'],
        $sessionid);

    XDb::xSql(
        'CREATE TEMPORARY TABLE `tmpxml_cachedescs` (`id` int(11), PRIMARY KEY (`id`)) SELECT `object_id` `id`
        FROM `xmlsession_data` WHERE `session_id`= ? AND `object_type`=3 LIMIT ' . $limits[2]['start'] . ',' . $limits[2]['count'],
        $sessionid);

    XDb::xSql(
        'CREATE TEMPORARY TABLE `tmpxml_cachelogs` (`id` int(11), PRIMARY KEY (`id`)) SELECT `object_id` `id`
        FROM `xmlsession_data` WHERE `session_id`= ? AND `object_type`=1 LIMIT ' . $limits[3]['start'] . ',' . $limits[3]['count'],
        $sessionid );

    XDb::xSql(
        'CREATE TEMPORARY TABLE `tmpxml_pictures` (`id` int(11), PRIMARY KEY (`id`)) SELECT `object_id` `id`
        FROM `xmlsession_data` WHERE `session_id`= ? AND `object_type`=6 LIMIT ' . $limits[4]['start'] . ',' . $limits[4]['count'],
        $sessionid );

    XDb::xSql(
        'CREATE TEMPORARY TABLE `tmpxml_removedobjects` (`id` int(11), PRIMARY KEY (`id`)) SELECT `object_id` `id`
        FROM `xmlsession_data` WHERE `session_id`= ? AND `object_type`=7 LIMIT ' . $limits[5]['start'] . ',' . $limits[5]['count'],
        $sessionid );

    outputXmlFile($sessionid, $filenr, $bXmlDecl, $bOcXmlTag, $bDocType, $ziptype);
}

/* begin some useful functions */

function xmlcdata($str)
{
    global $bXmlCData;

    if ($bXmlCData == '1') {
        $str = mb_ereg_replace(']]>', ']] >', $str);
        $str = output_convert($str);
        return '<![CDATA[' . filterevilchars($str) . ']]>';
    } else
        return xmlentities2($str);
}

function xmlentities2($str)
{
    $from[0] = '&'; $to[0] = '&amp;';
    $from[1] = '<'; $to[1] = '&lt;';
    $from[2] = '>'; $to[2] = '&gt;';
    $from[3] = '"'; $to[3] = '&quot;';
    $from[4] = '\''; $to[4] = '&apos;';

    for ($i = 0; $i <= 4; $i++)
        $str = mb_ereg_replace($from[$i], $to[$i], $str);

    $str = output_convert($str);
    return filterevilchars($str);
}

function filterevilchars($str)
{
    global $sCharset;

    // the same for for ISO-8859-1 and UTF-8
    $str = mb_ereg_replace('[\x{00}-\x{09}\x{0B}\x{0C}\x{0E}-\x{1F}]*', '', $str);

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
    return XDb::xMultiVariableQueryValue(
        "SELECT `uuid` FROM `caches` WHERE `cache_id`= :1 LIMIT 1", '', $id);
}

function log_id2uuid($id)
{
    return XDb::xMultiVariableQueryValue(
        "SELECT `uuid` FROM `cache_logs`
        WHERE `cache_logs`.`deleted`=0 AND `id`= :1 LIMIT 1", '', $id);
}

function user_id2uuid($id)
{
    return XDb::xMultiVariableQueryValue(
        "SELECT `uuid` FROM `user` WHERE `user_id`= :1 LIMIT 1", '', $id);
}

/* end some useful functions */

function unlinkrecursiv($path)
{
    if (mb_substr($path, -1) != '/')
        $path .= '/';

    $notunlinked = 0;

    if(!is_dir($path)){
        return true;
    }

    $hDir = @opendir($path);
    if($hDir===false){
        return true;
    }
    while (false !== ($file = readdir($hDir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($path . $file)) {
                if (unlinkrecursiv($path . $file . '/') == false)
                    $notunlinked++;
            }
            else {
                if ((mb_substr($file, -4) == '.zip') ||
                        (mb_substr($file, -3) == '.gz') ||
                        (mb_substr($file, -4) == '.bz2') ||
                        (mb_substr($file, -4) == '.xml'))
                    unlink($path . $file);
                else
                    $notunlinked++;
            }
        }
    }
    closedir($hDir);

    if ($notunlinked == 0) {
        if(is_dir($path)){
            rmdir($path);
        }
        return true;
    } else
        return false;
}

function output_convert($str)
{
    global $sCharset;

    if ($sCharset == 'iso-8859-2') {
        if ($str != null) {
            $str = @iconv('UTF-8', 'ISO-8859-2', $str);
            if ($str == false)
                $str = '--- charset conversion error ---';
        }
        return $str;
    }
    else if ($sCharset == 'utf-8')
        return $str;
}

?>
