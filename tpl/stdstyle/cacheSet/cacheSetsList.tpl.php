

<div class="content2-pagetitle">{{gp_mainTitile}}</div>

<div class="content2-container">

    <div id="mapCanvas"></div>

    <!-- map-chunk start -->
    <?php $view->callChunk('dynamicMap/dynamicMap', $view->mapModel, "mapCanvas");?>
    <!-- map-chunk end -->

    <hr/>

    <!-- listOfCaches-chunk start -->
    <?php $view->callChunk('listOfCaches/listOfCaches', $view->listCacheModel);?>
    <!-- listOfCaches-chunk end -->

</div>
