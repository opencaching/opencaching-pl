<?php

use src\Models\GeoCache\GeoCache;

/**
 * GeoPath icon for the cache.
 *
 * @param GeoCache $cache
 */

return function (GeoCache $cache) {

    if ($cache->isPowerTrailPart(false)) {
        ?>
        <a href="<?= $cache->getPowerTrail()->getPowerTrailUrl() ?>" target="_blank">
            <img src="<?= $cache->getPowerTrail()->getFootIcon() ?>" class="icon16"
                 alt="" title="<?= htmlentities($cache->getPowerTrail()->getName()) ?>">
        </a>
        <?php
    }

};