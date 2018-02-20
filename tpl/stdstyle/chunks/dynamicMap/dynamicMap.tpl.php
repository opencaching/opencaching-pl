<?php

use Utils\Uri\Uri;
use Utils\View\View;
use lib\Objects\ChunkModels\DynamicMap\DynamicMapModel;
use lib\Objects\OcConfig\OcDynamicMapConfig;

/**
 * This chunk displays dynamic map with different kinds of markers.
 * Markers should be passed by $mapModel.
 */
return function (DynamicMapModel $mapModel, $canvasId){

  // load chunk CSS
  View::callChunkInline('loadCssByJs',
    Uri::getLinkWithModificationTime(
        '/tpl/stdstyle/chunks/dynamicMap/dynamicMap.css'));

?>
<!-- load info-window templates -->
<?php foreach($mapModel->getInfoWindowTemplates() as $tplId => $infoWinScr) { ?>
  <script type="text/x-handlebars-template" id="<?=$tplId?>Tpl" >
    <?php include(__DIR__.$infoWinScr); ?>
  </script>
<?php } //foreach-InfoWindowTemplates ?>

<script src="/tpl/stdstyle/chunks/dynamicMap/dynamicMapCommons.js"></script>
<script src="/lib/js/handlebarsjs/handlebars.min-v4.0.11.js"></script>

<script>

// global object containing dynamic all map properties
var dynamicMapParams_<?=$canvasId?> = {};

{ //ECMA6-block-scoping

    $(document).ready(function(){
      initializeMap(dynamicMapParams_<?=$canvasId?>);
    });

    function initializeMap(params){

      // init global map object
      params.map = new google.maps.Map(
          document.getElementById('<?=$canvasId?>'), {}
      );

      // access to map object because of WMSImageMapTypeOptions
      window.getGoogleMapObject = function(){ return params.map; }

      // initialize list of mapTypeIds (used to display control etc.)
      params.ocMapTypesIds = [];

      //first add native maps from Google
      for (var type in google.maps.MapTypeId) {
        params.ocMapTypesIds.push( google.maps.MapTypeId[type] );
      }

      //then add custom OC maps
      var mapItems = <?=OcDynamicMapConfig::getJsMapItems()?>;
      for (var mapType in mapItems){
          params.map.mapTypes.set(mapType, mapItems[mapType]()); //add this OC map
          params.ocMapTypesIds.push(mapType);
      }

      var map_options = {
          mapTypeId: '<?=$mapModel->getMapTypeName()?>',
          center: new google.maps.LatLng(<?=$mapModel->getCoords()->getLatitude()?>,<?=$mapModel->getCoords()->getLongitude()?>),
          zoom: <?=$mapModel->getZoom()?>,

          disableDefaultUI: true,   // by default disable all controls and show:
          scaleControl: true,       // show scale on the bottom of map
          zoomControl: true,        // +/- constrols
          mapTypeControl: true,     // list of the maps
          streetViewControl: true,  // streetview guy
          rotateControl: true,      // this is visible only on huge zoom
          keyboardShortcuts: true,  // for example key '+' = zoom+
          clickableIcons: false,    // POI on the map doesn't open balons on clicks
          gestureHandling: 'greedy',// disable ctrl+ zooming

          draggableCursor: 'crosshair',
          draggingCursor: 'pointer',

          tilt: 0,                  // disable auto-switch to tilted view

          mapTypeControlOptions: {
              mapTypeIds: params.ocMapTypesIds,
              position: google.maps.ControlPosition.TOP_RIGHT,
              style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
          }
      };

      // set map options
      params.map.setOptions( map_options );

      // register map change custom callback
      google.maps.event.addListener(params.map, "maptypeid_changed", function() {
        // called when user switch the map
        refreshMapCopyright(params.map);
        controlGoogleContent(params.map);
      });

      // register infowindow close at map click
      google.maps.event.addListener(params.map, 'click', function(event){
        // hide current info-window
        if(params.lastOpenedInfoWindow){
          params.lastOpenedInfoWindow.close();
        }
      });

      // load markers from given map model
      loadMarkers(params);

      // ...and again when the map is fully loaded
      google.maps.event.addListenerOnce(params.map, 'idle', function(){
        // called only on the first time the map is loaded
        refreshMapCopyright(params.map);
        controlGoogleContent(params.map);
      });

    } // initializeMap


    function loadMarkers(params){

      var bounds = new google.maps.LatLngBounds();

      // add markers
      params.markerMgr = {};
      <?php foreach ($mapModel->getMarkersData() as $markerClass => $markersData) { ?>

        params.markerMgr.<?=$markerClass?> =
            <?php View::callChunkInline($mapModel->getMarkersMrgs($markerClass), $markersData);?>;

        for(markerData of params.markerMgr.<?=$markerClass?>.data) {

          var marker = params.markerMgr.<?=$markerClass?>.markerFactory(markerData);
          marker.setMap(params.map);
          marker.ocData = markerData;

          marker.addListener('click', function() {

            if(params.lastOpenedInfoWindow){
              params.lastOpenedInfoWindow.close();
            }
            var infoWindow = params.markerMgr.<?=$markerClass?>.infoWindowFactory(this.ocData);
            params.lastOpenedInfoWindow = infoWindow;
            infoWindow.open(params.map, this);

          });

          // find bbox which contains all markers
          bounds.extend(marker.getPosition());

        }

      <?php } //foreach-markersMgr ?>

      if(!bounds.isEmpty()){ // only if bound are present (there are markers)
        // register event which zoom-out map if there is only one marker or markers are very closed
        google.maps.event.addListenerOnce(params.map, 'bounds_changed', function(event) {
          if (this.getZoom() > 12) {
            this.setZoom(12);
          }
        });

        // resize map to see all markers
        params.map.fitBounds(bounds);
      }

    }

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

    <?=OcDynamicMapConfig::getWMSImageMapTypeOptions()?>
}
</script>

<?php
};
//end of chunk - nothing should be after this line
