<?php

use src\Models\GeoCache\GeoCache;
use src\Models\GeoCache\GeoCacheLog;
use src\Utils\Text\Formatter;

/**
 * This is column with last log-icon and log-text for GeoCache
 *
 * @param GeoCache $cache
 */

return function (GeoCache $cache) {

    $log = $cache->getLastLog();

    if (empty($log)) {
        echo tr('usrWatch_noLogs');
    } else {
        $recommended = ($log->getType() == GeoCacheLog::LOGTYPE_FOUNDIT && $log->isRecommendedByUser($log->getUser()))
        ?>
        <a href="<?= $log->getLogUrl() ?>" target="_blank" class="lightTipped links">
            <img src="<?= $log->getLogIcon() ?>" class="icon16" alt="<?= tr($log->getTypeTranslationKey()) ?>"
                 title="<?= tr($log->getTypeTranslationKey()) ?>">
            <?= Formatter::date($log->getDate()) ?>
        </a>
        <div class="lightTip">
            <b><?= htmlentities($log->getUser()->getUserName()) ?> (<?= tr($log->getTypeTranslationKey()) ?>):</b>
            <br>
            <?php if ($recommended) { ?><img src="/images/rating-star.png" alt=""><?php } ?>
            <?= htmlentities(Formatter::truncateText(strip_tags($log->getText()), 160)) ?>
        </div>

        <?php
    }
};