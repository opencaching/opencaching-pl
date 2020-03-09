<?php

use src\Models\PowerTrail\PowerTrail;

/**
 * GeoPath icon for the cache.
 * $date needs to contain:
 * - ptId - id of the GeoPath
 * - ptType - type of the GeoPath
 * - ptName - name of the GeoPath
 *
 * @param array $data
 */

return function (array $data) {

    if (empty($data)) {
        // seems that no GeoPath data is present here
        return;
    }

    $geoPathIconSrc = PowerTrail::GetPowerTrailIconsByType($data['ptType']);
    ?>
    <a href="/powerTrail.php?ptAction=showSerie&amp;ptrail=<?= $data['ptId'] ?>" target="_blank">
        <img src="<?= $geoPathIconSrc ?>" class="icon16"
             alt="" title="<?= $data['ptName'] ?>">
    </a>
    <?php
};