<?php

use src\Controllers\CacheController;

?>
<div class="content2-container">

    <div class="content2-pagetitle">
        <?= tr('newest_caches') ?>
        <a href="/rss/newcaches.xml">
            <img src="/images/misc/rss.svg" class="icon16" alt="RSS icon">
        </a>
        <a href="<?= \src\Utils\Uri\SimpleRouter::getLink(CacheController::class, 'newForeignCaches') ?>" class="btn btn-sm btn-default float-right">
            <?= tr('abroad_caches') ?>
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