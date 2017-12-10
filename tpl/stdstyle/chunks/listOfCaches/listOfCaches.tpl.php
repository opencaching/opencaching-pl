<?php

use lib\Objects\ChunkModels\ListOfCaches\ListOfCachesModel;
use Utils\Uri\Uri;
use lib\Objects\ChunkModels\ListOfCaches\AbstractColumn;
use Utils\View\View;

/**
 * This is generic list-of-caches. This list needs properly initialized model.
 * Example of initialization can be found in model class (ListOfCachesModel).
 *
 */
return function (ListOfCachesModel $listModel){

    $chunkCSS = Uri::getLinkWithModificationTime(
        '/tpl/stdstyle/chunks/listOfCaches/listOfCaches.css');

?>

<script type='text/javascript'>
    var linkElement = document.createElement("link");
    linkElement.rel = "stylesheet";
    linkElement.href = "<?=$chunkCSS?>";
    linkElement.type = "text/css";
    document.head.appendChild(linkElement);
</script>

<?php //becacus some of columns uses tooltip() //TODO: remove this tooltips ?>
<script type="text/javascript" src="/lib/js/wz_tooltip.js"></script>

<table class="listOfCaches bs-table table-striped">

    <?php if($listModel->isHeaderEnabled()) { ?>
        <thead>
        <?php foreach ($listModel->getColumns() as /** @var AbstractColumn */ $column){ ?>
          <th class="<?=$column->getCssClass()?>">
              <?=$column->getHeader()?>
          </th>
        <?php } //foreach header ?>
        </thead>
    <?php } //if-display-header ?>

    <tbody>
    <?php foreach ($listModel->getRows() as $row){ ?>
        <tr>
          <?php foreach ($listModel->getColumns() as /** @var AbstractColumn */ $column){ ?>
            <td class="<?=$column->getCssClass()?>">
              <?=$column->callColumnChunk($row)?>
            </td>
          <?php } //foreach column ?>
        </tr>
    <?php } //foreach row?>
    </tbody>
</table>

<?=$listModel->callPaginationChunk()?>

<?php

};
