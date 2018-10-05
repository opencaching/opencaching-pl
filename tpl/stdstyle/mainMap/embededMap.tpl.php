<script>
  //On touch-screen devices use full-screen map by default
  //Check for touch device below should be kept in sync with analogous check in lib/cachemap3.js
  if (('ontouchstart' in window) || (navigator.msMaxTouchPoints > 0)){
      //check cookie to allow user to come back to non-full screen mode
      if( document.cookie.indexOf("forceFullScreenMap=off") == -1){
          //touch device + cookie not set => redirect to full screen map
          window.location = '/MainMap/fullscreen'+window.location.search;
      }
  }
</script>

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
    searchData: <?=isset($view->searchData)?'"'.$view->searchData.'"':"null"?>,
    cacheSetId: <?=isset($view->cacheSetId)?$view->cacheSetId:"null"?>,
    initUserPrefs: <?=$view->savedUserPrefs?>,
  };

  $(function() {
    mainMapEntryPoint(params);
  });

</script>
