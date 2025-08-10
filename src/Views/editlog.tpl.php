<?php

use src\Utils\Database\XDb;
use src\Models\ApplicationContainer;

$view->callChunk('tinyMCE', false);
?>
<script>

    function subs_days(days_number) {
      var dateStr = document.getElementById('logDatePicker').value;
        var dateArr = dateStr.split('-');
        if (dateArr.length !== 3) return;
        var d_yr = parseInt(dateArr[0], 10);
        var d_mn = parseInt(dateArr[1], 10) - 1;
        var d_day = parseInt(dateArr[2], 10);
        var d = new Date(d_yr, d_mn, d_day - days_number, 0, 0, 0);
        if (!isNaN(d.getTime())) {
            var d_now = new Date();
            if (d <= d_now) {
                var mm = (d.getMonth() + 1).toString().padStart(2, '0');
                var dd = d.getDate().toString().padStart(2, '0');
                var yyyy = d.getFullYear();
                var newDateStr = yyyy + '-' + mm + '-' + dd;
                document.getElementById('logDatePicker').value = newDateStr;
                if ($('#logDatePicker').data('datepicker')) {
                    $('#logDatePicker').datepicker('setDate', newDateStr);
                }
                logDatePickerChange();
            }
        }
    }

    function _chkFound() {
<?php

$loggedUser = ApplicationContainer::GetAuthorizedUser();
$founds = XDb::xMultiVariableQueryValue(
    "SELECT count(cache_id) FROM cache_logs
    WHERE deleted=0
        AND cache_id =
            (
                SELECT cache_id FROM cache_logs
                WHERE deleted=0 AND id = :1
            )
        AND user_id = :2 AND type='1'",
    0, $_REQUEST['logid'], $loggedUser->getUserId());

?>
        if (document.editlog.logtype.value == "1" || (<?php echo $founds; ?> > 0 && document.editlog.logtype.value == "3") || document.editlog.logtype.value == "7") {
            document.editlog.rating.disabled = false;
        }
        else
        {
            document.editlog.rating.disabled = true;
        }
        return false;
    }

    function toogleLayer(whichLayer, val)
    {
        chkMoved();
        var elem, vis;
        _chkFound();
        if (document.getElementById)
            elem = document.getElementById(whichLayer);
        else if (document.all)
            elem = document.all[whichLayer];
        else if (document.layers)
            elem = document.layers[whichLayer];
        vis = elem.style;

        if (val != '')
        {
            if (document.editlog.logtype.value == "1" || document.editlog.logtype.value == "7")
                vis.display = 'block';
            else
                vis.display = 'none';
        }
        else
            vis.display = val;
    }
    function chkMoved()
    {
        var mode = document.editlog.logtype.value;
        var iconarray = new Array();
        iconarray['1'] = '16x16-found.png';
        iconarray['2'] = '16x16-dnf.png';
        iconarray['3'] = '16x16-note.png';
        iconarray['4'] = '16x16-moved.png';
        iconarray['5'] = '16x16-need-maintenance.png';
        iconarray['6'] = '16x16-need-maintenance.png';
        iconarray['7'] = '16x16-go.png';
        iconarray['8'] = '16x16-wattend.png';
        iconarray['9'] = '16x16-trash.png';
        iconarray['10'] = '16x16-published.png';
        iconarray['11'] = '16x16-temporary.png';
        iconarray['12'] = '16x16-octeam.png';
        var image_log = "/images/log/" + iconarray[mode];
        document.getElementById('actionicon').src = image_log;
    }

    $(function() {
        $.datepicker.setDefaults($.datepicker.regional['{language4js}']);
        $('#logDatePicker').datepicker({
            dateFormat: 'yy-mm-dd',
            regional: '{language4js}',
            maxDate: 0
        });
        $('#logTimePicker').timepicker({
            hourText: '{{timePicker_hourText}}',
            minuteText: '{{timePicker_minuteText}}',
            timeSeparator: ':',
            nowButtonText: '{{timePicker_nowButtonText}}',
            showNowButton: true,
            closeButtonText: '{{timePicker_closeButtonText}}',
            showCloseButton: true,
            deselectButtonText: '{{timePicker_deselectButtonText}}',
            showDeselectButton: true,
            showPeriodLabels: false
        });
    });

    function logDatePickerChange(){
        var dateTimeStr = $('#logDatePicker').val();
        var dateArr = dateTimeStr.split("-");
        if(dateArr.length === 3) {
            $("#logyear").val(dateArr[0]);
            $("#logmonth").val(dateArr[1]);
            $("#logday").val(dateArr[2]);
        }
    }

    function logTimePickerChange(){
        var timeStr = $('#logTimePicker').val();
        if (timeStr) {
            var timeArr = timeStr.split(":");
            if(timeArr.length === 2) {
                $("#loghour").val(timeArr[0]);
                $("#logmin").val(timeArr[1]);
            }
        }
    }

</script>


<form action="editlog.php" method="post" enctype="application/x-www-form-urlencoded" name="editlog" id="editlog" dir="ltr">
    <input type="hidden" name="logid" value="{logid}"/>
    <input type="hidden" name="version2" value="1"/>
    <div class="content2-pagetitle">
        <img src="/images/blue/logs.png" class="icon32" alt="">&nbsp;{{edit_logentry}} <a href="viewcache.php?cacheid={cacheid}">{cachename}</a>
    </div>
    <div class="buffer"></div>

    <table class="table logformTable">
        <tr class="form-group-sm">
            <td class="content-title-noshade">
                <img src="images/free_icons/page_go.png" class="icon16" alt="">&nbsp;{{type_of_log}}:
            </td>
            <td class="options">
                <select onload="javascript:toogleLayer('ocena');" name="logtype" class="form-control input200" onchange="javascript:toogleLayer('ocena');">
                    {logtypeoptions}
                </select>&nbsp;&nbsp;<img id="actionicon" src="/images/log/Arrow-Right.png" alt="" style="vertical-align:top">
            </td>
        </tr>
        <tr class="form-group-sm">
            <td class="content-title-noshade">
                <img src="images/free_icons/date.png" class="icon16" alt="">&nbsp;{{date_logged}}:
            </td>
            <td class="options">
                <img src="images/free_icons/date_previous.png" alt ="{{lc_Day_before}}" title="{{lc_Day_before}}" onclick="subs_days(1);"/>
                <input type="text" class="form-control input100" id="logDatePicker" value="{logyear}-{logmonth}-{logday}" onchange="logDatePickerChange();" />
                <input type="hidden" name="logyear"  id="logyear" value="{logyear}"/>
                <input type="hidden" name="logmonth" id="logmonth" value="{logmonth}"/>
                <input type="hidden" name="logday"   id="logday" value="{logday}"/>
                <img src="images/free_icons/date_next.png" alt ="{{lc_Day_after}}" title="{{lc_Day_after}}" onclick="subs_days(-1);"/>
                &nbsp;&nbsp;<img src="images/free_icons/clock.png" class="icon16" alt="">&nbsp;{{time}}:
                <input type="text" class="form-control input70" id="logTimePicker" value="{loghour}:{logmin}" onchange="logTimePickerChange();" />
                <input type="hidden" name="loghour" id="loghour" value="{loghour}"/>
                <input type="hidden" name="logmin"  id="logmin" value="{logmin}"/>
                <br>{date_message}
            </td>
        </tr>
            {rating_message}
    </table>
    <div class="content2-container">
        <div class="buffer"></div>
        <img src="images/free_icons/page_edit.png" class="icon16" alt="">&nbsp;<span class="content-title-noshade-size12">{{comments_log}}:</span>
        <div class="buffer"></div>
        <textarea name="logtext" id="logtext" class="cachelog tinymce">{logtext}</textarea>
        {log_pw_field}
        <div class="buffer"></div>
        <a href="#" class="btn btn-primary" onclick="event.preventDefault();
            $(this).closest('form').submit()">{{submit}}</a>&nbsp;&nbsp;
        <input class="btn btn-default" type="reset" name="reset" value="{{reset}}">
        <input type="hidden" name="submitform" value="{{submit}}">
    </div>
</form>
