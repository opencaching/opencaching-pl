<?php
/***************************************************************************
 *
*   This program is free software; you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation; either version 2 of the License, or
*   (at your option) any later version.
*
***************************************************************************/

/****************************************************************************

Unicode Reminder ăĄă˘

watch_map of this user

****************************************************************************/
?>



<div class="searchdiv">
<center>
<div id="map0" style="width:100%; height:100%"></div>
</center>
</div>

<script type="text/javascript">
var hmapa = null;
var currentinfowindow = null;


function initialize() 
{
	hmapa = new google.maps.Map(
			document.getElementById("map0"),
			{
				center: new google.maps.LatLng(54, 18 ),
				zoom: 10,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			}
		);

}

window.onload = function() {
	initialize();
};
</script>


