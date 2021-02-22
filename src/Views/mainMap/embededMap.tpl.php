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
        <!--
            invisible container with map controls prepared to be applied later to the map
        -->

        <div id="mainMapControls" class="ol-control">
          <!--
            // search temporary disabled
          <input id="searchControlInput" type="text" size="10">
          <input id="searchControlButton" value="<?=tr('search')?>" type="button">
           -->
          <img id="fullscreenToggle" src="/images/icons/fullscreen.svg"
               title="<?=tr('fullscreen')?>" alt="<?=tr('fullscreen')?>">

          <img id="refreshButton" src="/images/icons/refresh.svg"
               title="<?=tr('map_refresh')?>" alt="<?=tr('map_refresh')?>">

          <img id="searchToggle" src="/images/icons/search.svg"
               title="<?=tr('map_search')?>" alt="<?=tr('map_search')?>">

        </div>

        <div id="mainMapSearch" class="ol-control">
            <input id="searchInput" type="text" placeholder="<?=tr('map_searchPlacePlaceholder')?>">
            <span id="searchTrigger" class="searchTrigger disabled btn btn-sm btn-primary"><?=tr('map_searchTrigger')?></span>
            <div>© openrouteservice.org by HeiGIT<br>Map data © OpenStreetMap contributors</div>
            <div id="searchResults" class="searchResults"></div>
        </div>
    </div>

    <div id="mainMap" class="mapCanvasEmbeded"></div>


    <div id="mapFilters" class="mapFiltersEmbeded">
      <?=$view->callSubTpl("/mainMap/mainMapFilters")?>
    </div>
</div>

<!-- map-chunk start -->
  <?php $view->callChunk('dynamicMap/dynamicMap', $view->mapModel, "mainMap");?>
<!-- map-chunk end -->

<script id="mainMapPopupTpl" type="text/x-handlebars-template">
  <?=$view->callSubTpl("/mainMap/mainMapPopup")?>
</script>

<script id="mainMapSearchResultTpl" type="text/x-handlebars-template">
  <?=$view->callSubTpl("/mainMap/mainMapSearchResult")?>
</script>

<script>
  $(function() {
    mainMapEntryPoint(<?=$view->mapParams?>);
  });
</script>

<script>
    var tr = {
        'map_error': '<?=tr('map_error')?>',
        'map_searchEmpty': '<?=tr('map_searchEmpty')?>',
    };
</script>
