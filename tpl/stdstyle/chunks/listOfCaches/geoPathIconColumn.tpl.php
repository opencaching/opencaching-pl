<?php

use lib\Objects\PowerTrail\PowerTrail;

return function (array $data){

?>

<a href="powerTrail.php?ptAction=showSerie&amp;ptrail=<?=$data['ptId']?>">
    <img src="<?=PowerTrail::GetPowerTrailIconsByType($data['ptType'])?>"
         class="icon16" alt="" title="<?=$data['ptName']?>" />
</a>

<?php
};

