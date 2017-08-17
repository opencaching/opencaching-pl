<?php

use lib\Objects\PowerTrail\PowerTrail;

return function (array $data){

    $geopathcIconSrc = PowerTrail::GetPowerTrailIconsByType($data['ptType']);
?>
    <a href="powerTrail.php?ptAction=showSerie&amp;ptrail=<?=$data['ptId']?>">
        <img src="<?=$geopathcIconSrc?>" class="icon16" target="_blank"
             alt="" title="<?=$data['ptName']?>" />
    </a>
<?php
};

