<?php

/**
	This is columnt with clickable icon.
  $data needs to contain:
		- icon - src of the icon
		- onClick - onclick action - for example function name
		- title - title value for title html param of the icon
*/

return function (array $data){

?>
    <img src="<?=$data['icon']?>" onclick="<?=$data['onClick']?>"
         class="icon16" alt="<?=$data['title']?>" title="<?=$data['title']?>">
<?php
};

