<div class="content2-container">

  <div class="content2-pagetitle"><?=tr('admCs_degradedCs')?></div>

<?php if(!$view->noCsToArchive) { ?>

  <div id="mapCanvas"></div>

  <!-- map-chunk start -->
  <?php $view->callChunk('dynamicMap/dynamicMap', $view->mapModel, "mapCanvas");?>
  <!-- map-chunk end -->

  <div id="cacheSetList">
      <!-- listOfCaches-chunk start -->
      <?php $view->callChunk('listOfCaches/listOfCaches', $view->listOfCssToArchiveModel);?>
      <!-- listOfCaches-chunk end -->
  </div>

<?php } else { // if-noCsToArchive ?>

  <h3><?=tr('admCs_emptyList')?></h3>

<?php } // if-noCsToArchive ?>

</div>
