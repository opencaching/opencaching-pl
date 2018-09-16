<div class="content2-container">

  <div class="content2-pagetitle">
    <?=tr('lastLogMap_pageTitle')?>
  </div>

  <div id="mapCanvas"></div>

</div>

<!-- map-chunk start -->
  <?php $view->callChunk('dynamicMap/dynamicMap', $view->mapModel, "mapCanvas");?>
<!-- map-chunk end -->
