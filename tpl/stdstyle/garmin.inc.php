<?php

tpl_set_var('htmlheaders', '<link rel="stylesheet" href="tpl/stdstyle/css/communicator.css" type="text/css" media="screen" />
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

?>
