<?php

use lib\Objects\ChunkModels\ListOfCaches\ListOfCachesModel;

/**
 * This is generic list-of-caches. This list needs properly initialized model.
 * Example of initialization can be found in model class (ListOfCachesModel).
 *
 */
return function (ListOfCachesModel $listModel){
?>
<table>
    <thead>

    </thead>
    <tbody>
    <?php foreach ($listModel->getRows() as $row){ ?>
        <tr>
            <?php foreach ($listModel->getColumns() as $column){ ?>
                <td>
                  <?=$column->callColumnChunk($row)?>
                </td>
            <?php } //foreach column ?>
        </tr>
    <?php } //foreach row?>
    </tbody>
</table>
<?php
};
