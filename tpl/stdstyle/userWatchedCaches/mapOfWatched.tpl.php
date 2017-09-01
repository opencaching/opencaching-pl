<div class="content2-pagetitle">
  <?=tr('usrWatch_mapTitle')?>
</div>

<div id="mapContainer">
    <div id="mapCanvas"></div>
</div>


<?php $view->callChunk('dynamicMap/dynamicMap', $view->mapModel, "mapCanvas");?>

