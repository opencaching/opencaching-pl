<?php

use Utils\Uri\Uri;
use Utils\View\View;
use lib\Objects\ChunkModels\DynamicMap\DynamicMapModel;

/**
 * This chunk displays dynamic map with different kinds of markers.
 * Markers should be passed by $mapModel.
 *
 * OpenLayers chunk should be loaded to header by:
 *  $this->view->addHeaderChunk('openLayers5');
 *
 */
return function (DynamicMapModel $mapModel, $canvasId){

  // load chunk CSS
  View::callChunkInline('loadCssByJs',
    Uri::getLinkWithModificationTime('/tpl/stdstyle/chunks/dynamicMap/dynamicMap.css'));

  View::callChunkInline('handlebarsJs');

?>

<script src="<?=Uri::getLinkWithModificationTime(
    "/tpl/stdstyle/chunks/dynamicMap/dynamicMapCommons.js")?>"></script>

<!-- load markers popup templates -->
<?php foreach($mapModel->getMarkerTypes() as $markerType) { ?>
  <script type="text/x-handlebars-template" class="<?=$markerType?>" >
    <?php include(__DIR__.'/markers/'.$markerType.'Popup.tpl.php'); ?>
  </script>
<?php } //foreach-popupTemplates ?>

<script>
//global object containing all dynamic map properties
var dynamicMapParams_<?=$canvasId?> = {
  prefix: "<?=$canvasId?>",
  targetDiv: "<?=$canvasId?>",
  centerOn: <?=$mapModel->getCoords()->getAsOpenLayersFormat()?>,
  mapStartZoom: <?=$mapModel->getZoom()?>,
  forceMapZoom: <?=$mapModel->isZoomForced()?'true':'false'?>,
  startExtent: <?=$mapModel->getStartExtentJson()?>,
  mapLayersConfig: getMapLayersConfig(), // loaded in header by openlayers5 chunk
  selectedLayerKey: "<?=$mapModel->getSelectedLayerName()?>",
  infoMessage: "<?=$mapModel->getInfoMessage()?>",
  markerData: <?=$mapModel->getMarkersDataJson()?>,
  markerMgr: {
    <?php foreach($mapModel->getMarkerTypes() as $markerType) { ?>
      <?=$markerType?>: <?php include(__DIR__.'/markers/'.$markerType.'Mgr.tpl.php'); ?>,
    <?php } //foreach $markerTypes ?>
  },
  compiledPopupTpls: []
};

$(document).ready(function(){
  // initialize dynamicMap
  dynamicMapEntryPoint(dynamicMapParams_<?=$canvasId?>);
});

</script>

<?php
};
//end of chunk - nothing should be after this line
