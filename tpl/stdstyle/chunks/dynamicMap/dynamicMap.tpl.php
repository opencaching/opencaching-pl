<?php

use lib\Objects\ChunkModels\DynamicMap\DynamicMapModel;
use Utils\Uri\Uri;
use lib\Objects\OcConfig\OcDynamicMapConfig;


return function (DynamicMapModel $mapModel, $canvasId){

    $chunkCSS = Uri::getLinkWithModificationTime(
        '/tpl/stdstyle/chunks/dynamicMap/dynamicMap.css');


?>

<script type='text/javascript'>
    var linkElement = document.createElement("link");
    linkElement.rel = "stylesheet";
    linkElement.href = "<?=$chunkCSS?>";
    linkElement.type = "text/css";
    document.head.appendChild(linkElement);
</script>

<script

<script>

var globalMapParams = {
    mapCanvasId: '<?=$canvasId?>'
};

//on-Load function
$(function() {
  initializeMap(globalMapParams);
});

function initializeMap(params){

  // init global map object
  params.__map = new google.maps.Map(
      document.getElementById(params.mapCanvasId), {}
  );

  // access to map object because of WMSImageMapTypeOptions
  window.getGlobalMapObject = function(){ return params.__map; }

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




}


</script>


<?php
};
//end of chunk - nothing should be after this line

