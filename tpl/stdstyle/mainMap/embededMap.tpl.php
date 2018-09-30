
<div class="content2-container">

    <div style="display:none">

        <div id="mainMapControls" class="ol-control">
          <!--
            // search temporary disabled
          <input id="searchControlInput" type="text" size="10">
          <input id="searchControlButton" value="<?=tr('search')?>" type="button">
           -->
          <img id="fullscreenToggle" src="/images/icons/fullscreen.svg"
               title="<?=tr('map_disableFullscreen')?>" alt="<?=tr('map_disableFullscreen')?>">

          <img id="refreshButton" src="/images/icons/refresh.svg"
               title="<?=tr('map_refresh')?>" alt="<?=tr('map_refresh')?>">
        </div>
    </div>
    <div id="mapCanvasEmbeded"></div>


    <div id="mapFilters" class="mapFiltersEmbeded">
      <?=$view->callSubTpl("/mainMap/mainMapFilters")?>
    </div>
</div>



<!-- map-chunk start -->
  <?php $view->callChunk('dynamicMap/dynamicMap', $view->mapModel, "mapCanvasEmbeded");?>
<!-- map-chunk end -->

<script id="mainMapPopupTpl" type="text/x-handlebars-template">
  <?=$view->callSubTpl("/mainMap/mainMapPopup")?>
</script>

<script>
var params = {
    mapId: "mapCanvasEmbeded",
    isFullScreenMap: false,
    openPopupAtCenter: <?=isset($view->openPopup)?"true":"false"?>,
    circle150m: <?=isset($view->circle150m)?"true":"false"?>,
    userId: "<?=$view->mapUserId?>",
    userName: "<?=$view->mapUserName?>",
    searchdata: "<?=isset($view->searchData)?$view->searchData:"null"?>",
    cacheSetId: <?=isset($view->cacheSetId)?$view->cacheSetId:"null"?>,
    initUserPrefs: <?=$view->savedUserPrefs?>,
  };

  $(function() {
    mainMapEntryPoint(params);
  });

</script>