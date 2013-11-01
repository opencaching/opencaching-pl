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
<div id="mapka-canvas" style="width:100%; height:100%"></div>
</center>
</div>

<script type="text/javascript">
var map = null;
var currentinfowindow = null;

function AddMarker(wspolrzedne, icon, cache_icon, wp, cache_name, log_id, log_icon, user_id, user_name, log_date, log_text)
//function AddMarker(wspolrzedne, icon )
{
    /*var marker = new google.maps.Marker({
        position: wspolrzedne,
        map: map,
        icon: icon
       });*/

	/*var infowindow = new google.maps.InfoWindow({
		 
	    content: '<table><tr><td><img src=\"tpl/stdstyle/images/' + cache_icon + '\" border=\"0\" alt=\"\" title=\"geocache\"/><b>&nbsp;<a class=\"links\" href=\"viewcache.php?wp=' + wp + '\">' + wp + ': ' + cache_name + '</a></td></tr><tr><td><a class=\"links\" href=\"viewlogs.php?logid=' + log_id + '\"><img src=\"tpl/stdstyle/images/' + log_icon + '\" border=\"0\" alt=\"\" /></a> <span style = \"links\"> przez </span> <a class=\"links\" href=\"viewprofile.php?userid=' + user_id + '\">' + user_name + '</a> <span style = \"links\">dnia: ' + log_date + '</span><hr><span style = \"font-size: 8pt\">'+log_text+'</span></td></tr></table>'
	    	
	});*/

	//'<img title="Wink" src="lib/tinymce/plugins/emotions/img/smiley-wink.gif" border="0" alt="Wink" />' 
	
	/*google.maps.event.addListener(marker, "click", function() {
		if (currentinfowindow !== null) {
			currentinfowindow.close();
		}
		infowindow.open (map, marker);
		currentinfowindow = infowindow;
	});*/
	

				 
		    
		    
    //marker.setTitle("ala m");
    
}

function initialize() {

	mapDiv = document.getElementById("mapka-canvas");
	
	var mapOptions = {
	    zoom: 10,
	    center:  new google.maps.LatLng(54.00,18.00 ),
	    mapTypeId: google.maps.MapTypeId.ROADMAP
	  };

	    
  /*var mapDiv = document.getElementById('map-canvas');
  var mapOptions = {
    zoom: 10,
    center:  new google.maps.LatLng({latitude}, {longitude}),
    //center:  new google.maps.LatLng(54.00, 18.00),
    //center: new google.maps.LatLng(-33, 151),
    
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
  }*/
  
  map = new google.maps.Map(mapDiv, mapOptions);

  //document.write( "TEST" );
  
  //{1markers}
}

google.maps.event.addDomListener(window, 'load', initialize);
</script>


