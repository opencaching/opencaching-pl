<?php

use src\Models\GeoCache\GeoCache;

/**
 * This is column with GeoKret icon if cache contains at least one GeoKret.
 *
 * @param GeoCache $cache
 */

return function (GeoCache $cache) {

    $statusTitle = tr($cache->getCacheTypeTranslationKey());
    $statusTitle .= ', ' . tr($cache->getStatusTranslationKey());

    if (!empty($cache->getGeokretsHosted())) {
        ?>
        <img src="/images/gk.png" class="icon16" alt="<?= tr('geokret') ?>" title="<?= tr('geokret') ?>">
        <?php
    }
};
