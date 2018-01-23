<?php

use lib\Objects\GeoCache\GeoCacheLogCommons;
use Utils\Text\Formatter;

/**
	This is column with log-icon and log-text.
  It needs vars in $data:
	- logId - id of the log
    - logType - type of the log
    - logText - text of the log
    - logUserName - name of the author
    - logDate - date of the log
*/

return function (array $data){

    if( !isset($data['logId']) || is_null($data['logId']) ){
        // there is no log data - exit;
        $nolog = true;
    }else{
        $nolog = false;
        $logIcon = GeoCacheLogCommons::GetIconForType($data['logType']);
        $logUrl = "/viewlogs.php?logid=${data['logId']}";
        $userName = $data['logUserName'];
        $logText = GeoCacheLogCommons::cleanLogTextForToolTip($data['logText']);
        $logDate = Formatter::date($data['logDate']);
        $logTypeName = GeoCacheLogCommons::cleanLogTextForToolTip(
            tr(GeoCacheLogCommons::typeTranslationKey($data['logType'])));
    }
?>
  <?php if(!$nolog) { ?>

      <a href="<?=$logUrl?>" target="_blank" class="lightTipped">

        <img src="<?=$logIcon?>" class="icon16" alt="LogIcon" title="LogIcon" />
        <?=$logDate?>
      </a>
      <div class="lightTip">
        <b><?=$userName?> (<?=$logTypeName?>):</b>
        <br/><?=$logText?>
      </div>

  <?php } else { // $nolog ?>
      <?=tr('usrWatch_noLogs')?>
  <?php } ?>
<?php
};

