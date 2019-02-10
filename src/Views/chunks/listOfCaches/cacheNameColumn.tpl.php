<?php

use src\Models\GeoCache\GeoCacheCommons;

/**
 * This is column which displays cache name.
 * $date arg has to contain:
 * - cacheWp - OC waypoint, for example: OP1234
 * - cacheName - name of the cache
 * - isStatusAware  - whether to adjust style depending in cache status
 *
*/

return function (array $data) {

    // exit if there is no icon given
    $statusAwareClass = "";
    if (isset($data['isStatusAware']) && $data['isStatusAware'] && $data['cacheStatus'] == 3) {
        $statusAwareClass = 'column_cacheName_archived';
    }

?>
    <a href="<?=GeoCacheCommons::GetCacheUrlByWp($data['cacheWp'])?>" target=”_blank” class="<?=$statusAwareClass?>">
      <?=$data['cacheName']?>
    </a>
<?php
};
