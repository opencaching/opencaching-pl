<?php

/**
 * This is column with clickable icon.
 * $data needs to contain:
 * - icon - src of the icon
 * - onClick - onclick action - for example function name
 * - title - title value for title html param of the icon
 */

return function (array $data){

    // exit if there is no icon given
    if(is_null($data['icon'])){
        return '';
    }

?>
    <img src="<?=$data['icon']?>" onclick="<?=$data['onClick']?>"
         class="icon16" alt="<?=$data['title']?>" title="<?=$data['title']?>">
<?php
};

