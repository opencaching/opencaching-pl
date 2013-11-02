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
<div id="mapka" style="width:100%; height:600pt"></div>
</center>
</div>

<script type="text/javascript">
var hmapa = null;
var currentinfowindow = null;

function AddMarker(wspolrzedne, icon, cache_icon, wp, cache_name, log_id, log_icon, user_id, user_name, log_date, log_text)
//function AddMarker(wspolrzedne, icon )
{
  var marker = new google.maps.Marker({
      position: wspolrzedne,
      map: hmapa,
      icon: icon
     });

	var infowindow = new google.maps.InfoWindow({
		 
	    content: '<table><tr><td><img src=\"tpl/stdstyle/images/' + cache_icon + '\" border=\"0\" alt=\"\" title=\"geocache\"/><b>&nbsp;<a class=\"links\" href=\"viewcache.php?wp=' + wp + '\">' + wp + ': ' + cache_name + '</a></td></tr><tr><td><a class=\"links\" href=\"viewlogs.php?logid=' + log_id + '\"><img src=\"tpl/stdstyle/images/' + log_icon + '\" border=\"0\" alt=\"\" /></a> <span style = \"links\"> {{mywatches_map_01}} </span> <a class=\"links\" href=\"viewprofile.php?userid=' + user_id + '\">' + user_name + '</a> <span style = \"links\">{{mywatches_map_02}}: ' + log_date + '</span><hr><span style = \"font-size: 8pt\">'+log_text+'</span></td></tr></table>'
	    	
	});
	
	google.maps.event.addListener(marker, "click", function() {
		if (currentinfowindow !== null) {
			currentinfowindow.close();
		}
		infowindow.open (hmapa, marker);
		currentinfowindow = infowindow;
	});
	
}


function initialize() 
{
	var mapDiv = document.getElementById('mapka');
	
	var mapOptions = {
	    zoom: 10,
	    center:  new google.maps.LatLng({latitude}, {longitude}),
	    
	    mapTypeId: google.maps.MapTypeId.ROADMAP,

	    disableDefaultUI: true,
	    streetViewControl: false,
	    overviewMapControl: false,
	    panControl: false,
	    
	    
	    
	    zoomControl: true,        
	    zoomControlOptions: {
	        style: google.maps.ZoomControlStyle.SMALL
	      },

	      mapTypeControl: true,
	      mapTypeControlOptions: {
	        style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
	      },    
	  };

	  hmapa = new google.maps.Map(mapDiv, mapOptions);

	  {markers}
}



google.maps.event.addDomListener(window, 'load', initialize);

</script>


