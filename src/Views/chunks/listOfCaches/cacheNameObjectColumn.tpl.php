<?php

use src\Models\GeoCache\GeoCache;

/**
 * This is column which displays cache name.
 * $date arg has to contain GeoCache object
 *
 * @param GeoCache $cache
 */

return function (GeoCache $cache) {

    ?>
    <a href="<?= $cache->getCacheUrl() ?>" target=”_blank” class="links">
        <?= htmlentities($cache->getCacheName()) ?>
    </a>
    <?php
};