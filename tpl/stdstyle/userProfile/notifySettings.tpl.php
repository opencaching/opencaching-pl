<?php

use Utils\Uri\SimpleRouter;
use lib\Objects\Notify\Notify;

?>

<div class="content2-pagetitle">
  <?=tr('settings_notifications')?>
</div>

<div class="content2-container">
  <div class="callout callout-info"><?=tr('notify_settings_intro')?></div>

  <div class="content-title-noshade-size1"><?=tr('notify_settings_freq')?></div>

  <?=tr('notify_settings_freq2')?>
  <select id="intervalSelect" name="watchmail_mode" onChange="intervalChanged()" class="form-control input200">
    <option value="<?=Notify::SEND_NOTIFICATION_HOURLY?>">
      <?=tr('notify_settings_hourlyMode');?>
    </option>
    <option value="<?=Notify::SEND_NOTIFICATION_DAILY?>">
      <?=tr('notify_settings_dailyMode');?>
    </option>
    <option value="<?=Notify::SEND_NOTIFICATION_WEEKLY?>">
      <?=tr('notify_settings_weeklyMode');?>
    </option>
  </select>

  <span id="watch_day_selector">
    <?=tr('notify_settings_everyWeekday')?>
    <select id="weekdaySelect" name="watchmail_day" onChange="notifySettingsChange()" class="form-control input150">
      <option value="1"><?=tr('notify_settings_monday')?></option>
      <option value="2"><?=tr('notify_settings_tuesday')?></option>
      <option value="3"><?=tr('notify_settings_wednesday')?></option>
      <option value="4"><?=tr('notify_settings_thursday')?></option>
      <option value="5"><?=tr('notify_settings_friday')?></option>
      <option value="6"><?=tr('notify_settings_saturday')?></option>
      <option value="7"><?=tr('notify_settings_sunday')?></option>
    </select>
  </span>

  <span id="watch_hour_selector">
    <?=tr('notify_settings_atHour')?>
    <select id="hourSelect" name="watchmail_hour" onChange="notifySettingsChange()" class="form-control input100">
      <?php for($hour=0; $hour<24; $hour++) { ?>
        <option value="<?=$hour?>"><?=sprintf('%02d:00', $hour)?></option>
      <?php } //for ?>
    </select>
  </span>

  <div class="buffer"></div>
  <div class="content-title-noshade-size1"><?=tr('notify_settings_caches')?></div>
  <img src="/tpl/stdstyle/images/misc/on.svg" alt="<?=tr('yes')?>" title="<?=tr('yes')?>" class="notify-switch-img<?php if (! $view->notifyCaches) {?> no-display <?php }?>" id="notifyCachesOn" onclick="notifyCachesChange(0)">
  <img src="/tpl/stdstyle/images/misc/off.svg" alt="<?=tr('no')?>" title="<?=tr('no')?>" class="notify-switch-img<?php if ($view->notifyCaches) {?> no-display <?php }?>" id="notifyCachesOff" onclick="notifyCachesChange(1)">
  <?=tr('notify_settings_cachesonoff')?>
  <div class="notify-add-nbh<?php if (! $view->notifyCaches) {?> no-display <?php }?>" id="notify-add-nbh">
    <?php if (! empty($view->neighbourhoods)) {?>
      <?=tr('notify_settings_addnbh')?>:<br>
      <?php foreach ($view->neighbourhoods as $nbh) { ?>
        <img src="/tpl/stdstyle/images/misc/on.svg" alt="<?=tr('yes')?>" title="<?=tr('yes')?>" class="notify-switch-img<?php if (! $nbh->getNotify()) {?> no-display <?php }?>" id="notifyNbhOn-<?=$nbh->getSeq()?>" onclick="notifyNbhChange(<?=$nbh->getSeq()?>, 0)">
        <img src="/tpl/stdstyle/images/misc/off.svg" alt="<?=tr('no')?>" title="<?=tr('no')?>" class="notify-switch-img<?php if ($nbh->getNotify()) {?> no-display <?php }?>" id="notifyNbhOff-<?=$nbh->getSeq()?>" onclick="notifyNbhChange(<?=$nbh->getSeq()?>, 1)">
        <?=$nbh->getName()?><br>
      <?php } // end foreach ?>
    <?php } // end if ?>
  </div>

  <div class="buffer"></div>
  <div class="content-title-noshade-size1"><?=tr('notify_settings_logs')?></div>
  <img src="/tpl/stdstyle/images/misc/on.svg" alt="<?=tr('yes')?>" title="<?=tr('yes')?>" class="notify-switch-img<?php if (! $view->notifyLogs) {?> no-display <?php }?>" id="notifyLogsOn" onclick="notifyLogsChange(0)">
  <img src="/tpl/stdstyle/images/misc/off.svg" alt="<?=tr('no')?>" title="<?=tr('no')?>" class="notify-switch-img<?php if ($view->notifyLogs) {?> no-display <?php }?>" id="notifyLogsOff" onclick="notifyLogsChange(1)">
  <?=tr('notify_settings_logsonoff')?>

  <div class="callout callout-warning<?php if ($view->notifyLogs) {?> no-display <?php }?>" id="notifyLogsWarning"><?=tr('notify_settings_watchinfo')?></div>

  <div class="buffer"></div>
  <div class="notice"><?=tr('autosave')?></div>

  <div class="align-center">
    <a href="<?=SimpleRouter::getLink('MyNeighbourhood','config')?>" class="btn btn-default btn-md"><?=tr('my_neighborhood')?> (<?=tr('config')?>)</a>
    <a href="/mywatches.php" class="btn btn-default btn-md"><?=tr('usrWatch_title')?></a>
  </div>
</div>

<script>
function notifyCachesChange(state) {
	if (state == 1) {
		$.ajax({
			url : "<?=SimpleRouter::getLink('UserProfile', 'ajaxSetNotifyCaches', 1)?>",
			type : "get",
		});
		$("#notifyCachesOff").hide();
		$("#notifyCachesOn").show();
		$("#notify-add-nbh").show();
	} else {
		$.ajax({
			url : "<?=SimpleRouter::getLink('UserProfile', 'ajaxSetNotifyCaches', 0)?>",
			type : "get",
		});
		$("#notifyCachesOn").hide();
		$("#notifyCachesOff").show();
		$("#notify-add-nbh").hide();
	}
}

function notifyLogsChange(state) {
	if (state == 1) {
		$.ajax({
			url : "<?=SimpleRouter::getLink('UserProfile', 'ajaxSetNotifyLogs', 1)?>",
			type : "get",
		});
		$("#notifyLogsOff").hide();
		$("#notifyLogsOn").show();
		$("#notifyLogsWarning").hide();
	} else {
		$.ajax({
			url : "<?=SimpleRouter::getLink('UserProfile', 'ajaxSetNotifyLogs', 0)?>",
			type : "get",
		});
		$("#notifyLogsOn").hide();
		$("#notifyLogsOff").show();
		$("#notifyLogsWarning").show();
	}
}

function notifyNbhChange(nbh, state) {
	$.ajax({
		url : "<?=SimpleRouter::getLink('UserProfile', 'ajaxSetNeighbourhoodNotify')?>",
		type : "post",
		data : {
			nbh : nbh,
			state : state,
		}
	});
	if (state == 1) {
		$("#notifyNbhOff-"+nbh).hide();
		$("#notifyNbhOn-"+nbh).show();
	} else {
		$("#notifyNbhOn-"+nbh).hide();
		$("#notifyNbhOff-"+nbh).show();
	}
}

function intervalChanged(update = true) {
	if (update) {
		notifySettingsChange();
	}
	switch( $( "#intervalSelect" ).val() ){
		case '<?=Notify::SEND_NOTIFICATION_HOURLY?>':
			$("#watch_hour_selector").hide();
			$("#watch_day_selector").hide();
			break;
		case '<?=Notify::SEND_NOTIFICATION_DAILY?>':
			$("#watch_hour_selector").show();
			$("#watch_day_selector").hide();
			break;
		case '<?=Notify::SEND_NOTIFICATION_WEEKLY?>':
			$("#watch_hour_selector").show();
			$("#watch_day_selector").show();
			break;
		default:
	}
}

function notifySettingsChange() {
	$.ajax({
		url : "<?=SimpleRouter::getLink('UserProfile', 'ajaxSetNotifySettings')?>",
		type : "post",
		data : {
			watchmail_mode : $("#intervalSelect").val(),
			watchmail_day : $("#weekdaySelect").val(),
			watchmail_hour: $("#hourSelect").val()
		}
	});

}

$(function() {
	$("#intervalSelect").val("<?=$view->intervalSelected?>");
	$("#weekdaySelect").val("<?=$view->weekDaySelected?>");
	$("#hourSelect").val("<?=$view->hourSelected?>");
	intervalChanged(false);
});
</script>
