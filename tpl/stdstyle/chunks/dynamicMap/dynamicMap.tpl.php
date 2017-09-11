<?php

use Utils\Uri\Uri;
use lib\Objects\OcConfig\OcDynamicMapConfig;
use lib\Objects\ChunkModels\DynamicMap\AbstractDynamicMapBase;


return function (AbstractDynamicMapBase $mapModel, $canvasId){

    $chunkCSS = Uri::getLinkWithModificationTime(
        '/tpl/stdstyle/chunks/dynamicMap/dynamicMap.css');

    $lat = $mapModel->getCoords()->getLatitude();
    $lot = $mapModel->getCoords()->getLongitude();

?>

<script type='text/javascript'>
    var linkElement = document.createElement("link");
    linkElement.rel = "stylesheet";
    linkElement.href = "<?=$chunkCSS?>";
    linkElement.type = "text/css";
    document.head.appendChild(linkElement);
</script>

<script>

var globalMapParams = {};

//on-load function
$(function() {
  initializeMap(globalMapParams);
});

<?=OcDynamicMapConfig::getWMSImageMapTypeOptions()?>

function initializeMap(){

  params = {};

  // init global map object
  params.__map = new google.maps.Map(
      document.getElementById('<?=$canvasId?>'), {}
  );

  // access to map object because of WMSImageMapTypeOptions
  window.getGoogleMapObject = function(){ return params.__map; }

  // initialize list of mapTypeIds (used to display control etc.)
  params.__ocMapTypesIds = [];

  //first add native maps from Google
  for (var type in google.maps.MapTypeId) {
    params.__ocMapTypesIds.push( google.maps.MapTypeId[type] );
  }

  //then add custom OC maps
  var mapItems = <?=OcDynamicMapConfig::getJsMapItems()?>;
  for (var mapType in mapItems){
      params.__map.mapTypes.set(mapType, mapItems[mapType]()); //add this OC map
      params.__ocMapTypesIds.push(mapType);
  }

  var map_options = {
      mapTypeId: '<?=$mapModel->getMapTypeName()?>',
      center: new google.maps.LatLng(<?=$lat?>, <?=$lot?>),
      zoom: <?=$mapModel->getZoom()?>,

      disableDefaultUI: true, // by default disable all controls and show:
      scaleControl: true,     // show scale on the bottom of map
      zoomControl: true,      // +/- constrols
      mapTypeControl: true,   // list of the maps
      streetViewControl: true,// streetview guy
      rotateControl:true,     // this is visible only on huge zoom
      keyboardShortcuts: true,// for example key '+' = zoom+
      clickableIcons: false,  // POI on the map doesn't open balons on clicks

      draggableCursor: 'crosshair',
      draggingCursor: 'pointer',

      mapTypeControlOptions: {
          mapTypeIds: params.__ocMapTypesIds,
          position: google.maps.ControlPosition.TOP_RIGHT,
          style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
      }
  };

  //set map options
  params.__map.setOptions( map_options );

  //register map change custom callback
  google.maps.event.addListener(params.__map, "maptypeid_changed", function() {

      //called when user switch the map
      refreshMapCopyright(params.__map);
      controlGoogleContent(params.__map);
  });

  loadMarkers(params.__map);


  // ...and again when the map is fully loaded
  google.maps.event.addListenerOnce(params.__map, 'idle', function(){

    // called only on the first time the map is loaded
    refreshMapCopyright(params.__map);
    controlGoogleContent(params.__map);

  });
} // initializeMap


function refreshMapCopyright(map){

  var copyrightTexts = <?=OcDynamicMapConfig::getJsAttributionMap()?>;

  var copyrightText = copyrightTexts[map.getMapTypeId()];
  var element = $('#map_copyright');

  if(copyrightText){ // we have copyright text for this custom map

    if(!element.length){
        element = $('<div id="map_copyright"></div>');

        //add this div to map
        map.controls[google.maps.ControlPosition.BOTTOM_RIGHT].push(element[0]);
    }
    element.html(copyrightText);
  }else{ // this is native google map or there is no attribution text for it
    element.hide();
  }
}

<?=file_get_contents(__DIR__ . '/dynamicMapCommons.js');?>

var DynamicMapMarkersObject = <?=$mapModel->getJsDynamicMapMarkerObject()?>

var lastOpenedInfoWindow = null;

function loadMarkers(map){

  var bounds = new google.maps.LatLngBounds();

  DynamicMapMarkersObject.data.forEach(function(m) {

    var marker = DynamicMapMarkersObject.markerFactory(m);
    marker.setMap(map);

    marker.addListener('click', function() {

      if(lastOpenedInfoWindow){
        lastOpenedInfoWindow.close();
      }

      var infowindow = DynamicMapMarkersObject.infoWindowFactory(m);

      lastOpenedInfoWindow = infowindow;
      infowindow.open(map, marker);

    });
		// find bbox which contains all markers
    bounds.extend(marker.getPosition());

  });

	// resize map to see all markers
  map.fitBounds(bounds);

}

</script>


<?php
};
//end of chunk - nothing should be after this line

