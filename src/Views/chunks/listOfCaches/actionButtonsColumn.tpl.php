<?php

/**
 * This is column with clickable action buttons.
 * There can be many buttons definition.
 * Every button is a table like:
 *
 * DataRowExtractor should return array with columns:
 * [
 *   'btnClasses' =>  css classes to add to button
 *   'btnText' => text of the button
 *   'onClick' => onclick action - for example function name
 *   'title' => title value for title html param of the button
 * ],
 * [...], ...
 */

return function (array $data){

    if(empty($data)){
        // if there is no data - skip this data
        return;
    }

?>
  <?php foreach ($data as $btnData) {
            if( empty($btnData['btnText']) || empty ($btnData['onClick']) ) {
                continue;
            }
  ?>
    <a class='btn btn-sm <?=$btnData['btnClasses']?>' onclick="<?=$btnData['onClick']?>" title="<?=$btnData['title']?>">
      <?=$btnData['btnText']?>
    </a>
  <?php } //foreach ?>
<?php
};
