<?php

use src\Controllers\CacheController;

?>
<div class="content2-container">

    <div class="content2-pagetitle">
        <?= tr('abroad_caches') ?>
        <a href="<?= \src\Utils\Uri\SimpleRouter::getLink(CacheController::class, 'newCaches') ?>"
           class="btn btn-sm btn-default float-right">
            <?= tr('newest_caches') ?>
        </a>
    </div>
    <?php
    foreach ($view->listCacheModelArray as $country => $listCacheModel) {
        ?>
        <h1><?= tr($country) ?></h1>
        <?php
        $view->callChunk('listOfCaches/listOfCaches', $listCacheModel);
    }
    ?>

</div>