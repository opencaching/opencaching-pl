<div class="content2-container">

    <div class="content2-pagetitle">
        <?= tr($view->pageTitle) ?>
    </div>

    <?php if ($view->cachesCount > 0) {
        $view->callChunk('listOfCaches/listOfCaches', $view->listCacheModel);
    } else { //$view->cachesCount == 0 ?>
        <div class="spacer"></div>
        <p><?= tr('list_of_caches_is_empty') ?></p>
    <?php } //$view->cachesCount == 0 ?>

</div>