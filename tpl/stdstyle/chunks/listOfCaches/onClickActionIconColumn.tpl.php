<?php

return function (array $data){

?>
    <img src="<?=$data['icon']?>" onclick="<?=$data['onClick']?>"
         class="icon16" alt="<?=$data['title']?>" title="<?=$data['title']?>">
<?php
};

