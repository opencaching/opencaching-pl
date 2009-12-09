<?php
/***************************************************************************
												  ./tpl/stdstyle/coordinates.inc.php
															-------------------
		begin                : Mon July 2 2004
		copyright            : (C) 2004 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

	***************************************************************************/

/***************************************************************************
	*
	*   This program is free software; you can redistribute it and/or modify
	*   it under the terms of the GNU General Public License as published by
	*   the Free Software Foundation; either version 2 of the License, or
	*   (at your option) any later version.
	*
	***************************************************************************/

/****************************************************************************

   Unicode Reminder メモ

	 set template specific variables
<script type="text/javascript" src="http://developer.garmin.com/web/communicator-api/garmin/device/GarminDeviceDisplay.js"> </script>');

 ****************************************************************************/

tpl_set_var('htmlheaders', '<link rel="stylesheet" href="tpl/stdstyle/css/communicator.css" type="text/css" media="screen" />
<script type="text/javascript" language="javascript" src="tpl/stdstyle/js/garmin/prototype.js"></script>
<script type="text/javascript" src="tpl/stdstyle/js/garmin/device/GarminDeviceDisplay.js"> </script>');

tpl_set_var('bodyMod', ' onload="load()" onunload="GUnload()"');

?>
