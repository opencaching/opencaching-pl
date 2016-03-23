<?php

use Utils\Database\XDb;
function onTheList($theArray, $item)
{
    for ($i = 0; $i < count($theArray); $i++) {
        if ($theArray[$i] == $item)
            return $i;
    }
    return -1;
}

function getDBFilter($userid)
{
    $filter = "11111111110111110134100000000000"; // default filter
    $query = XDb::xSql("SELECT * from map_settings_v2 WHERE `user_id`= ? ",$userid);
    while ($row = XDb::xFetchArray($query)) {
        $filter = '';
        foreach ($row as $k => $v) {
            if ($k != 'user_id')
                $filter .= $v;
        }
    }
    return $filter;
}

function setDBFilter($userid, $filter)
{
    $q="REPLACE INTO map_settings_v2
        SET
        user_id = ?, unknown = ?, traditional = ?,
        multicache = ?, virtual = ?, webcam = ?,
        event = ?, quiz = ?, mobile = ?, math = ?,
        drivein = ?, ignored = ?, own = ?,
        found = ?, notyetfound = ?, geokret = ?,
        showsign = ?, active = ?, notactive = ?,
        maptype = ?, cachelimit = ?, cachesort = ?";

    XDb::xSql(
        $q, $userid,$filter[0],$filter[1],$filter[2],
        $filter[3],$filter[4],$filter[5],
        $filter[6],$filter[7],$filter[8],$filter[9],
        $filter[10],$filter[11],$filter[12],
        $filter[13],$filter[14],$filter[15],$filter[16],
        $filter[17],$filter[18],$filter[19],$filter[20]);
}

function makeDBFilter()
{
    $f = "";
    if (isset($_POST['u']))
        $f .= "0";
    else
        $f .= "1";
    if (isset($_POST['t']))
        $f .= "0";
    else
        $f .= "1";
    if (isset($_POST['m']))
        $f .= "0";
    else
        $f .= "1";
    if (isset($_POST['v']))
        $f .= "0";
    else
        $f .= "1";
    if (isset($_POST['w']))
        $f .= "0";
    else
        $f .= "1";
    if (isset($_POST['e']))
        $f .= "0";
    else
        $f .= "1";
    if (isset($_POST['q']))
        $f .= "0";
    else
        $f .= "1";
    if (isset($_POST['o']))
        $f .= "0";
    else
        $f .= "1";
    if (isset($_POST['c']))
        $f .= "0";
    else
        $f .= "1";
    if (isset($_POST['d']))
        $f .= "0";
    else
        $f .= "1";
    if (isset($_POST['I']))
        $f .= "0";
    else
        $f .= "1";
    if (isset($_POST['W']))
        $f .= "0";
    else
        $f .= "1";
    if (isset($_POST['Z']))
        $f .= "0";
    else
        $f .= "1";
    if (isset($_POST['A']))
        $f .= "0";
    else
        $f .= "1";
    if (isset($_POST['N']))
        $f .= "0";
    else
        $f .= "1";
    if (isset($_POST['C']))
        $f .= "1";
    else
        $f .= "0";
    if (isset($_POST['T']))
        $f .= "0";
    else
        $f .= "1";
    if (isset($_POST['Y']))
        $f .= "0";
    else
        $f .= "1";
    if (isset($_POST['maptype'])) {
        $f .= $_POST['maptype'];
    } else {
        $f .= "3";
    }
    if (isset($_POST['cachelimit'])) {
        $f .= (($_POST['cachelimit'] / 50) - 1);
    } else {
        $f .= "4";
    }
    if (isset($_POST['cachesort'])) {
        $f .= $_POST['cachesort'];
    } else {
        $f .= "1";
    }
    return $f;
//  ifutmvweqocdIWZANCT
}

require_once('./lib/common.inc.php');
$tplname = 'cachemap2';
tpl_set_var('bodyMod', ' onload="load()" onunload="GUnload()"');
//tpl_set_var('BodyMod', ' onload="load()" onunload="GUnload()"');
global $usr;
global $get_userid;
global $filter;
global $caches_list;
global $language;
global $lang;

$userid = '';

if (isset($_REQUEST['userid'])) {
    $get_userid = strip_tags($_REQUEST['userid']);
} else {
    $get_userid = '';
}

//user logged in?
if ($usr == false) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);
} else {

    if ($get_userid == '')
        $userid = $usr['userid'];
    else
        $userid = $get_userid;
    $rs = XDb::xSql("SELECT `latitude`, `longitude`, `username`
        FROM `user` WHERE `user_id`= ? ", $userid);

    $record = XDb::xFetchArray($rs);
    if ((isset($_REQUEST['lat']) && isset($_REQUEST['lon']) && $_REQUEST['lat'] != "" && $_REQUEST['lon'] != "") && ($_REQUEST['lat'] != 0 && $_REQUEST['lon'] != 0)) {
        $coordsXY = $_REQUEST['lat'] . "," . $_REQUEST['lon'];
        $coordsX = $_REQUEST['lat'];
        if ($_REQUEST['inputZoom'] != "")
            tpl_set_var('zoom', $_REQUEST['inputZoom']);
        else
            tpl_set_var('zoom', 11);
    }
    else {
        $coordsXY = "$record[latitude],$record[longitude]";
        $coordsX = "$record[latitude]";
        if ($coordsX == "" || $coordsX == 0) {
            $coordsXY = "52.5,19.2";
            tpl_set_var('zoom', 6);
        } else
            tpl_set_var('zoom', 11);
    }

    if (isset($_REQUEST['print_list']) && $_REQUEST['print_list'] == 'y') {
        // add cache to print (do not duplicate items)
        if (count($_SESSION['print_list']) == 0)
            $_SESSION['print_list'] = array();
        if (onTheList($_SESSION['print_list'], $_REQUEST['cacheid']) == -1)
            array_push($_SESSION['print_list'], $_REQUEST['cacheid']);
    }
    if (isset($_REQUEST['print_list']) && $_REQUEST['print_list'] == 'n') {
        // remove cache from print list
        while (onTheList($_SESSION['print_list'], $_REQUEST['cacheid']) != -1)
            unset($_SESSION['print_list'][onTheList($_SESSION['print_list'], $_REQUEST['cacheid'])]);
        $_SESSION['print_list'] = array_values($_SESSION['print_list']);
    }

    tpl_set_var('coords', $coordsXY);
    tpl_set_var('username', $record['username']);

    $filter = getDBFilter($usr['userid']);

    if (isset($_POST['submit'])) {
        $makeFilterResult = makeDBFilter();
        setDBFilter($usr['userid'], $makeFilterResult);
        $filter = $makeFilterResult;
    }

    /* SET YOUR MAP CODE HERE */
    tpl_set_var('cachemap_header', '<script src="http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=' . $googlemap_key . '" type="text/javascript"></script>
    <script language="JavaScript1.2" type="text/javascript">
    <!--
        window.onbeforeunload = saveMapType;
        function saveMapType(){
            var ajaxRequest;  // The variable that makes Ajax possible!
            try{
                // Opera 8.0+, Firefox, Safari
                ajaxRequest = new XMLHttpRequest();
            } catch (e){
                // Internet Explorer Browsers
                try{
                    ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
                } catch (e) {
                    try{
                        ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
                    } catch (e){
                        // Something went wrong
                        alert("Your browser broke!");
                        return false;
                    }
                }
            }

            var mapid;
            switch (map.getCurrentMapType()) {
                case G_NORMAL_MAP:
                    mapid = "0";
                break;
                case G_SATELLITE_MAP:
                    mapid = "1";
                break;
                case G_HYBRID_MAP:
                    mapid = "2";
                break;
                case G_PHYSICAL_MAP:
                    mapid = "3";
                break;
                default:
                    mapid = "0";
            }

            var climit = ((document.getElementById("cachelimit").value / 50) - 1);
            var csort = document.getElementById("cachesort").value;

            var queryString = "?map_v=2&maptype=" + mapid + "&cachelimit=" + climit + "&cachesort=" + csort;
            ajaxRequest.open("GET", "cachemapsettings.php" + queryString, false);
            ajaxRequest.send(null);

        }
    //-->
    </script>');
    tpl_BuildTemplate();
}
?>
