
<hr/>
  <h3>Map with caches and cacheSets</h3>
  <div id="mapCanvas"></div>
  <!-- map-chunk start -->
  <?php $view->callChunk('dynamicMap/dynamicMap', $view->mapModel, "mapCanvas");?>
  <!-- map-chunk end -->

<hr/>
  <h3>Just empty map</h3>
  <div id="emptyMapCanvas"></div>
  <!-- map-chunk start -->
  <?php $view->callChunk('dynamicMap/dynamicMap', $view->emptyMap, "emptyMapCanvas");?>
  <!-- map-chunk end -->

<hr/>