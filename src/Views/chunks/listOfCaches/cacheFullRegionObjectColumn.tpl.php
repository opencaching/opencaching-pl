<?php

use src\Models\GeoCache\GeoCache;

/**
 * This is column which displays cache country & region.
 * $cache arg has to contain GeoCache object
 *
 * @param GeoCache $cache
 *
 */

return function (GeoCache $cache) {
    echo $cache->getCacheLocationObj()->getLocationDesc(' &gt; ');
};