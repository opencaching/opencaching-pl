<?php

use src\Models\PowerTrail\PowerTrail;

/**
 * Geopatch icon for the cache.
 * $date needs to contain:
 * - ptId - id of the powertrails
 * - ptType - type of the powertrail
 * - ptName - name of the powertrails
*/

return function (array $data){

    if(empty($data)){
        // seems that no geopath data is present here
        return;
    }

    $geopathcIconSrc = PowerTrail::GetPowerTrailIconsByType($data['ptType']);
?>
    <a href="/powerTrail.php?ptAction=showSerie&amp;ptrail=<?=$data['ptId']?>">
        <img src="<?=$geopathcIconSrc?>" class="icon16" target="_blank"
             alt="" title="<?=$data['ptName']?>" />
    </a>
<?php
};
