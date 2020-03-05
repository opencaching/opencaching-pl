<?php

use src\Models\ChunkModels\ListOfCaches\AbstractColumn;
use src\Models\ChunkModels\ListOfCaches\ListOfCachesModel;
use src\Utils\Uri\Uri;

/**
 * This is generic list-of-caches. This list needs properly initialized model.
 * Example of initialization can be found in model class (ListOfCachesModel).
 * @param ListOfCachesModel $listModel
 */
return function (ListOfCachesModel $listModel) {

    if ($listModel->getId() == 1) {
        $chunkCSS = Uri::getLinkWithModificationTime(
            '/views/chunks/listOfCaches/listOfCaches.css'); ?>
        <script>
            var linkElement = document.createElement("link");
            linkElement.rel = "stylesheet";
            linkElement.href = "<?=$chunkCSS?>";
            linkElement.type = "text/css";
            document.head.appendChild(linkElement);
        </script>

        <script src="/js/wz_tooltip.js"></script>
        <?php
    }

    ?>
    <table class="listOfCaches bs-table table-striped">

        <?php if ($listModel->isHeaderEnabled()) { ?>
            <thead>
            <tr>
                <?php foreach ($listModel->getColumns() as /** @var AbstractColumn */ $column) { ?>
                    <th class="<?= $column->getCssClass() ?> <?= $column->getAdditionalClass() ?>">
                        <?= $column->getHeader() ?>
                    </th>
                <?php } //foreach header ?>
            </tr>
            </thead>
        <?php } //if-display-header ?>

        <tbody>
        <?php if (!empty($listModel->getRows())) { ?>
            <?php foreach ($listModel->getRows() as $row) { ?>
                <tr>
                    <?php foreach ($listModel->getColumns() as /** @var AbstractColumn */ $column) { ?>
                        <td class="<?= $column->getCssClass() ?> <?= $column->getAdditionalClass() ?>">
                            <?= $column->callColumnChunk($row) ?>
                        </td>
                    <?php } //foreach column ?>
                </tr>
            <?php } //foreach row?>
        <?php } else { // empty rows list ?>
            <tr>
                <td colspan="<?= count($listModel->getColumns()) ?>" class="center">
                    <?= $listModel->getEmptyListMessage() ?>.
                </td>
            </tr>
        <?php } // empty rows list ?>
        </tbody>
    </table>

    <?= $listModel->callPaginationChunk() ?>

    <?php

};