<div class="content2-container">

  <div class="content2-pagetitle"><?=tr('admCs_degradedCs')?></div>

  <div id="mapCanvas"></div>

  <!-- map-chunk start -->
  <?php $view->callChunk('dynamicMap/dynamicMap', $view->mapModel, "mapCanvas");?>
  <!-- map-chunk end -->

  <div id="cacheSetList">
      <!-- listOfCaches-chunk start -->
      <?php $view->callChunk('listOfCaches/listOfCaches', $view->listOfCssToArchiveModel);?>
      <!-- listOfCaches-chunk end -->
  </div>
</div>
