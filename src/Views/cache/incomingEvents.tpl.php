<div class="content2-container">

    <div class="content2-pagetitle">
        <?= tr('incomming_events') ?>
    </div>
    <?php
    foreach ($view->listCacheModelArray as $location => $listCacheModel) {
        ?>
        <h1><?= $location ?></h1>
        <?php
        $view->callChunk('listOfCaches/listOfCaches', $listCacheModel);
    }
    ?>

</div>