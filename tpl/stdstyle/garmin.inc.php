<?php

/* * *************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 * ************************************************************************* */


tpl_set_var('htmlheaders', '<link rel="stylesheet" href="tpl/stdstyle/css/communicator.css" type="text/css" media="screen" />
<script type="text/javascript" language="javascript" src="tpl/stdstyle/js/garmin/prototype.js"></script>
<script type="text/javascript" src="tpl/stdstyle/js/garmin/device/GarminDeviceDisplay.js"> </script>');

tpl_set_var('bodyMod', ' onload="load()" onunload="GUnload()"');
?>
