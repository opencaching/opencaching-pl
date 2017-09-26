<?php

//prepare the templates and include all neccessary
function convert($str)
{
    $str = mb_ereg_replace('ę', 'e', $str);
    $str = mb_ereg_replace('ó', 'o', $str);
    $str = mb_ereg_replace('ą', 'a', $str);
    $str = mb_ereg_replace('ś', 's', $str);
    $str = mb_ereg_replace('ł', 'l', $str);
    $str = mb_ereg_replace('ż', 'z', $str);
    $str = mb_ereg_replace('ź', 'z', $str);
    $str = mb_ereg_replace('ć', 'c', $str);
    $str = mb_ereg_replace('ń', 'n', $str);
    $str = mb_ereg_replace('Ę', 'E', $str);
    $str = mb_ereg_replace('Ó', 'O', $str);
    $str = mb_ereg_replace('Ą', 'A', $str);
    $str = mb_ereg_replace('Ś', 'S', $str);
    $str = mb_ereg_replace('Ł', 'L', $str);
    $str = mb_ereg_replace('Ż', 'Z', $str);
    $str = mb_ereg_replace('Ź', 'Z', $str);
    $str = mb_ereg_replace('Ć', 'C', $str);
    $str = mb_ereg_replace('Ń', 'N', $str);

    $str = mb_ereg_replace('ä', 'a', $str);
    $str = mb_ereg_replace('Ä', 'A', $str);

// romanian characters
    $str = mb_ereg_replace('ă', 'a', $str);
    $str = mb_ereg_replace('î', 'i', $str);
    $str = mb_ereg_replace('ş', 's', $str);
    $str = mb_ereg_replace('ţ', 't', $str);
    $str = mb_ereg_replace('â', 'a', $str);
    $str = mb_ereg_replace('Ă', 'A', $str);
    $str = mb_ereg_replace('Î', 'I', $str);
    $str = mb_ereg_replace('Ş', 'S', $str);
    $str = mb_ereg_replace('Ţ', 'T', $str);
    $str = mb_ereg_replace('Â', 'A', $str);
// romanian new keyboard
    $str = mb_ereg_replace('ș', 's', $str);
    $str = mb_ereg_replace('ț', 't', $str);
    $str = mb_ereg_replace('Ș', 'S', $str);
    $str = mb_ereg_replace('Ț', 'T', $str);
// hungarian characters
    $str = mb_ereg_replace('é', 'e', $str);
    $str = mb_ereg_replace('á', 'a', $str);
    $str = mb_ereg_replace('ö', 'o', $str);
    $str = mb_ereg_replace('ő', 'o', $str);
    $str = mb_ereg_replace('ü', 'u', $str);
    $str = mb_ereg_replace('ű', 'u', $str);
    $str = mb_ereg_replace('ó', 'o', $str);
    $str = mb_ereg_replace('ú', 'u', $str);
    $str = mb_ereg_replace('É', 'E', $str);
    $str = mb_ereg_replace('Á', 'A', $str);
    $str = mb_ereg_replace('Ö', 'O', $str);
    $str = mb_ereg_replace('Ő', 'O', $str);
    $str = mb_ereg_replace('Ü', 'U', $str);
    $str = mb_ereg_replace('Ű', 'U', $str);
    $str = mb_ereg_replace('Ó', 'O', $str);
    $str = mb_ereg_replace('Ú', 'U', $str);

    return $str;
}

require_once('./lib/common.inc.php');

$tplname = 'garmin';

tpl_set_var('htmlheaders', '<link rel="stylesheet" href="tpl/stdstyle/css/garmin.css" type="text/css" media="screen" />
<script type="text/javascript" src="tpl/stdstyle/js/garmin/prototype.js"></script>
<script type="text/javascript" src="tpl/stdstyle/js/garmin/device/GarminDeviceDisplay.js"> </script>');

tpl_set_var('bodyMod', ' onload="load()"');

$garminKeyStr = '';
if (isset($config['garmin-key'])){
    foreach($config['garmin-key'] as $k => $v){
        $garminKeyStr .= '"'.$k.'", "'.$v.'", ';
    }
    $garminKeyStr = rtrim($garminKeyStr, ', ');
}
tpl_set_var('garminKeyStr', $garminKeyStr);

global $hide_coords;
if ($usr == false && $hide_coords) {
    tpl_errorMsg($tplname, tr('login_message_09'));
    exit;
}

$lat = isset($_REQUEST['lat']) ? $_REQUEST['lat'] : '';
$long = isset($_REQUEST['long']) ? $_REQUEST['long'] : '';
$wp = isset($_REQUEST['wp']) ? $_REQUEST['wp'] : '';
$name = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';

$str = convert($name);

tpl_set_var('lat', $lat);
tpl_set_var('long', $long);
tpl_set_var('wp_oc', $wp);
tpl_set_var('cachename', $str);



//make the template and send it out
tpl_BuildTemplate();

