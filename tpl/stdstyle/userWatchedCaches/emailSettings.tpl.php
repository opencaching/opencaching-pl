<?php

use lib\Objects\Notify\Notify;
use Utils\Uri\SimpleRouter;

?>

<?=$view->callChunk('infoBar', null, $view->infoMsg, $view->errorMsg)?>

<div class="content2-pagetitle">
  <?=tr('usrWatch_emailSettingsTitle')?>
</div>


<div>
    <form id="emailSettingsForm" action="mywatches.php?action=updateEmailSettings" method="post">
      <div class="settingsDiv">

        <label class="content-title-noshade">
            <?=tr('usrWatch_sendNotifications')?>
            <select id="intervalSelect" name="watchmail_mode" onChange="intervalChanged()"
                    class="">
                <option value="<?=Notify::SEND_NOTIFICATION_HOURLY?>">
                  <?=tr('usrWatch_hourlyMode');?>
                </option>
                <option value="<?=Notify::SEND_NOTIFICATION_DAILY?>">
                  <?=tr('usrWatch_dailyMode');?>
                </option>
                <option value="<?=Notify::SEND_NOTIFICATION_WEEKLY?>">
                  <?=tr('usrWatch_weeklyMode');?>
                </option>
            </select>
        </label>

        <label id="watch_day_selector" class="content-title-noshade">
            <?=tr('usrWatch_everyWeekday')?>
            <select id="weekdaySelect" name="watchmail_day" class="">
                <option value="1"><?=tr('usrWatch_monday')?></option>
                <option value="2"><?=tr('usrWatch_tuesday')?></option>
                <option value="3"><?=tr('usrWatch_wednesday')?></option>
                <option value="4"><?=tr('usrWatch_thursday')?></option>
                <option value="5"><?=tr('usrWatch_friday')?></option>
                <option value="6"><?=tr('usrWatch_saturday')?></option>
                <option value="7"><?=tr('usrWatch_sunday')?></option>
            </select>
        </label>

        <label id="watch_hour_selector" class="content-title-noshade">
            <?=tr('usrWatch_atHour')?>
            <select id="hourSelect" name="watchmail_hour" class="">
                <?php for($hour=0;$hour<24;$hour++) { ?>
                  <option value="<?=$hour?>"><?=sprintf('%02d:00', $hour)?></option>
                <?php } //for ?>
            </select>
        </label>
      </div>
      <div class="submitDiv">
        <input type="submit" class="btn btn-primary btn-md" value="{{store}}">
        <a href="<?=SimpleRouter::getLink('MyNeighbourhood','config')?>" class="btn btn-default btn-md"><?=tr('my_neighborhood')?> (<?=tr('config')?>)</a>
      </div>
    </form>
</div>

<script>
function intervalChanged(){

  switch( $( "#intervalSelect" ).val() ){
    case '<?=Notify::SEND_NOTIFICATION_HOURLY?>':
      console.log('A');
      $("#watch_hour_selector").hide();
      $("#watch_day_selector").hide();

      break;
    case '<?=Notify::SEND_NOTIFICATION_DAILY?>':
      console.log('B');
      $("#watch_hour_selector").show();
      $("#watch_day_selector").hide();
      break;
    case '<?=Notify::SEND_NOTIFICATION_WEEKLY?>':
      console.log('C');
      $("#watch_hour_selector").show();
      $("#watch_day_selector").show();
      break;
    default:
      console.log("D");
  }
}

$(function() {
  $("#intervalSelect").val("<?=$view->intervalSelected?>");
  $("#weekdaySelect").val("<?=$view->weekDaySelected?>");
  $("#hourSelect").val("<?=$view->hourSelected?>");
  intervalChanged();
});

</script>

