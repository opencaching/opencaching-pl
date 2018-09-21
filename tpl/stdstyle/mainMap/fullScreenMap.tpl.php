<div id="mapCanvasFullScreen"></div>

<div style="display:none">

    <div id="mainMapControls" class="ol-control">
      <!--
        // search temporary disabled
      <input id="searchControlInput" type="text" size="10">
      <input id="searchControlButton" value="<?=tr('search')?>" type="button">
       -->
      <img id="fullscreenToggle" src="/images/fullscreen-off.png"
           title="<?=tr('disable_fullscreen')?>" alt="<?=tr('disable_fullscreen')?>">

      <img id="refreshButton" src="/images/refresh.png"
           title="<?=tr('refresh_map')?>" alt="<?=tr('refresh_map')?>">

      <img id="filtersToggle" src="/okapi/static/tilemap/legend_other.png"
           title="<?=tr('toggle_filters')?>" alt="<?=tr('toggle_filters')?>">
    </div>

    <div id="mapFilters" class="ol-control mapFiltersFullScreen">
      <?=$view->callSubTpl("/mainMap/mainMapFilters")?>
    </div>
</div>

<!-- map-chunk start -->
  <?php $view->callChunk('dynamicMap/dynamicMap', $view->mapModel, "mapCanvasFullScreen");?>
<!-- map-chunk end -->

<script id="mainMapPopupTpl" type="text/x-handlebars-template">
  <?=$view->callSubTpl("/mainMap/mainMapPopup")?>
</script>

<script>
var params = {
  mapId: "mapCanvasFullScreen",
  isFullScreenMap: true,
  userId: "<?=$view->mapUserId?>",
  openPopupAtCenter: <?=isset($view->openPopup)?"true":"false"?>,
  userName: "<?=$view->mapUserName?>",
  searchdata: "<?=isset($view->searchData)?$view->searchData:"null"?>",
  cacheSetId: <?=isset($view->cacheSet)?$view->cacheSet->getId():"null"?>,
  initUserPrefs: <?=$view->savedUserPrefs?>,
};

$(function() {
  mainMapEntryPoint(params);
});

</script>

