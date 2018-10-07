
<div id="mainMap" class="mapCanvasFullScreen"></div>

<div style="display:none">

    <div id="mainMapControls" class="ol-control">
      <!--
        // search temporary disabled
      <input id="searchControlInput" type="text" size="10">
      <input id="searchControlButton" value="<?=tr('search')?>" type="button">
       -->
      <img id="fullscreenToggle" src="/images/icons/embeded.svg"
           title="<?=tr('map_disableFullscreen')?>" alt="<?=tr('map_disableFullscreen')?>">

      <img id="refreshButton" src="/images/icons/refresh.svg"
           title="<?=tr('map_refresh')?>" alt="<?=tr('map_refresh')?>">

      <img id="filtersToggle" src="/images/icons/marker.svg"
           title="<?=tr('map_toggleFilters')?>" alt="<?=tr('map_toggleFilters')?>">
    </div>

    <div id="mapFilters" class="ol-control mapFiltersFullScreen">
      <?=$view->callSubTpl("/mainMap/mainMapFilters")?>
    </div>
</div>

<!-- map-chunk start -->
  <?php $view->callChunk('dynamicMap/dynamicMap', $view->mapModel, "mainMap");?>
<!-- map-chunk end -->

<script id="mainMapPopupTpl" type="text/x-handlebars-template">
  <?=$view->callSubTpl("/mainMap/mainMapPopup")?>
</script>

<script>
$(function() {
  mainMapEntryPoint(<?=$view->mapParams?>);
});
</script>

