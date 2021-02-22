
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

      <img id="searchToggle" src="/images/icons/search.svg"
           title="<?=tr('map_search')?>" alt="<?=tr('map_search')?>">
    </div>

    <div id="mapFilters" class="ol-control mapFiltersFullScreen">
      <?=$view->callSubTpl("/mainMap/mainMapFilters")?>
    </div>

    <div id="mainMapSearch" class="ol-control">
        <input id="searchInput" type="text" placeholder="<?=tr('map_searchPlacePlaceholder')?>">
        <span id="searchTrigger" class="searchTrigger disabled btn btn-sm btn-primary"><?=tr('map_searchTrigger')?></span>
        <div>© openrouteservice.org by HeiGIT<br>Map data © OpenStreetMap contributors</div>
        <div id="searchResults" class="searchResults"></div>
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
