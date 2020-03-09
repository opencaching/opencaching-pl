<?php

use src\Models\ChunkModels\ListOfCaches\Column_CacheLog;
use src\Models\GeoCache\GeoCacheLog;
use src\Models\GeoCache\GeoCacheLogCommons;
use src\Utils\Text\Formatter;

/**
 * This is column with log-icon and log-text.
 * It needs vars in $data:
 * - logId - id of the log
 * - logType - type of the log
 * - logText - text of the log
 * - logUserName - name of the author
 * - logDate - date of the log
 * - recommended - if not null means cache is recommended by user (shows only if log-type=FOUND)
 *
 * @param array $data
 * @param Column_CacheLog|null $model
 */

return function (array $data, Column_CacheLog $model = null) {

    if (!isset($data['logId']) || is_null($data['logId'])) {
        // there is no log data - exit;
        $nolog = true;
    } else {
        $nolog = false;
        $showFullLog = ($model && $model->isFullLogTextPresented()) ? true : false;

        $logIcon = GeoCacheLogCommons::GetIconForType($data['logType']);
        $logUrl = "/viewlogs.php?logid=${data['logId']}";
        $userName = $data['logUserName'];
        $logText = GeoCacheLogCommons::cleanLogTextForToolTip($data['logText']);
        $logDate = Formatter::date($data['logDate']);
        $logTypeName = GeoCacheLogCommons::cleanLogTextForToolTip(
            tr(GeoCacheLogCommons::typeTranslationKey($data['logType'])));
        $recommended = (isset($data['recommended']) && $data['logType'] == GeoCacheLog::LOGTYPE_FOUNDIT);

        $logTextEllipsisNecessary = $showFullLog && mb_strlen($logText) > 150; // elipsis text longer than...

    }
    ?>
    <?php if (!$nolog) { ?>

        <?php if ($showFullLog) { ?>

            <div>
                <a href="<?= $logUrl ?>" target="_blank" class="links">
                    <img src="<?= $logIcon ?>" class="icon16" alt="<?= $logTypeName ?>" title="<?= $logTypeName ?>">
                </a>
                <?= $logDate ?>,

                <?php if (isset($userName)) { ?>
                    <b><?= $userName ?></b>
                <?php } ?>

                <?php if ($recommended) { ?><img src="/images/rating-star.png" alt=""><?php } ?>
            </div>

            <div>
                <?php if ($logTextEllipsisNecessary) { ?>
                    <span class="lightTipped"><?= Formatter::truncateText($logText, 120) ?></span>
                    <div class="lightTip"><?= $logText ?></div>
                <?php } else { // !if-$logTextEllipsisNecessary ?>
                    <div><?= $logText ?></div>
                <?php } // if-$logTextEllipsisNecessary ?>
            </div>

        <?php } else { //if !showFullLog ?>

            <a href="<?= $logUrl ?>" target="_blank" class="lightTipped links">
                <img src="<?= $logIcon ?>" class="icon16" alt="<?= $logTypeName ?>" title="<?= $logTypeName ?>"/>
                <?= $logDate ?>
            </a>

            <div class="lightTip">
                <b><?= $userName ?> (<?= $logTypeName ?>):</b>
                <br>
                <?php if ($recommended) { ?><img src="/images/rating-star.png" alt=""><?php } ?>
                <?= $logText ?>
            </div>

        <?php } //if-showFullLog ?>

    <?php } else { // $nolog ?>
        <?= tr('usrWatch_noLogs') ?>
    <?php } ?>
    <?php
};