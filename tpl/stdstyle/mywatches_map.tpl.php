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


<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/search1.png" class="icon32" alt="{title_text}" title="{title_text}" align="middle" />&nbsp;{title_text}</div>
<div class="searchdiv">
<center>
<div id="mapka" style="width:100%; height:500pt"></div>
</center>

</div>

<script type="text/javascript">
var hmapa = null;
var currentinfowindow = null;
var weatherLayer = null;

function SwitchWheather()
{
	if ( weatherLayer.getMap() == null )
		{ weatherLayer.setMap( hmapa ); }
	else
		{ weatherLayer.setMap( null );}	
}


function HomeControl(controlDiv, map) {

	  // Set CSS styles for the DIV containing the control
	  // Setting padding to 5 px will offset the control
	  // from the edge of the map
	  controlDiv.style.padding = '5px';

	  // Set CSS for the control border
	  var controlUI = document.createElement('div');
	  controlUI.style.backgroundColor = 'white';
	  controlUI.style.borderStyle = 'solid';
	  controlUI.style.borderWidth = '1px';
	  controlUI.style.cursor = 'pointer';
	  controlUI.style.textAlign = 'center';
	  controlUI.title = '';
	  controlDiv.appendChild(controlUI);

	  // Set CSS for the control interior
	  var controlText = document.createElement('div');
	  controlText.style.fontFamily = 'Arial,sans-serif';
	  controlText.style.fontSize = '11px';
	  controlText.style.paddingLeft = '5px';
	  controlText.style.paddingRight = '5px';
	  controlText.innerHTML = '<b>{{Wheather}}</b>';
	  controlUI.appendChild(controlText);

	  // Setup the click event listeners: simply set the map to
	  // Chicago
	  google.maps.event.addDomListener(controlUI, 'click', function() {
		  SwitchWheather();
	  });

	}

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
	    scaleControl: true,	    
	    streetViewControl: true,
	    overviewMapControl: true,
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


	  var homeControlDiv = document.createElement('div');
	  var homeControl = new HomeControl(homeControlDiv, hmapa);

	  homeControlDiv.index = 1;
	  hmapa.controls[google.maps.ControlPosition.TOP_LEFT].push(homeControlDiv);
	  
	  

	  weatherLayer = new google.maps.weather.WeatherLayer({
		    temperatureUnits: google.maps.weather.TemperatureUnit.CELSIUS });
	  
		  weatherLayer.setMap(hmapa);
	  
	  
	  {markers}
}



google.maps.event.addDomListener(window, 'load', initialize);

</script>


