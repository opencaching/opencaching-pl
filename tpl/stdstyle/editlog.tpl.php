<?php

use Utils\Database\XDb;

$view->callChunk('tinyMCE', false);
?>
<link href="tpl/stdstyle/css/confirmCancelButtons.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">

    function subs_days(days_number) {
        //alert('ok');
        var d_day = document.getElementById('logday').value;
        var d_mn = document.getElementById('logmonth').value - 1;
        var d_yr = document.getElementById('logyear').value;
        var d = new Date(d_yr, d_mn, d_day - days_number, 0, 0, 0);


        //alert(d);
        if (isNaN(d) == false)
        {
            var d_now = +new Date;
            if (d <= d_now)
            {
                document.getElementById('logday').value = d.getDate();
                document.getElementById('logmonth').value = d.getMonth() + 1;
                document.getElementById('logyear').value = d.getFullYear();
            }
        }
    }

    function _chkFound() {
<?php

$founds = XDb::xMultiVariableQueryValue(
    "SELECT count(cache_id) FROM cache_logs
    WHERE deleted=0
        AND cache_id =
            (
                SELECT cache_id FROM cache_logs
                WHERE deleted=0 AND id = :1
            )
        AND user_id = :2 AND type='1'",
    0, $_REQUEST['logid'], $usr['userid']);

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

        //if( vis.display==''&&elem.offsetWidth!=undefined&&elem.offsetHeight!=undefined)
        //  vis.display=(elem.offsetWidth!=0&&elem.offsetHeight!=0)?'block':'none';
        //vis.display = (vis.display==''||vis.display=='block')?'none':'block';
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
        var image_log = "/tpl/stdstyle/images/log/" + iconarray[mode];
        document.getElementById('actionicon').src = image_log;
//         var el;
//  el='coord_table';
//  if (document.editlog.logtype.value == "4")
//  {document.getElementById(el).style.display='block';
//    } else {document.getElementById(el).style.display='none';}
    }

</script>

<script>
    $(function () {
        $('#scriptwarning').hide();
    });
</script>

<form action="editlog.php" method="post" enctype="application/x-www-form-urlencoded" name="editlog" id="editlog" dir="ltr">
    <input type="hidden" name="logid" value="{logid}"/>
    <input type="hidden" name="version2" value="1"/>
    <div class="content2-pagetitle">
        <img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="">&nbsp;{{edit_logentry}} <a href="viewcache.php?cacheid={cacheid}">{cachename}</a>
    </div>
    <div class="buffer"></div>

    <table class="table">
        <tr class="form-group-sm">
            <td class="content-title-noshade">
                <img src="tpl/stdstyle/images/free_icons/page_go.png" class="icon16" alt="">&nbsp;{{type_of_log}}:
            </td>
            <td class="options">
                <select onload="javascript:toogleLayer('ocena');" name="logtype" class="form-control input200" onchange="javascript:toogleLayer('ocena');">
                    {logtypeoptions}
                </select>&nbsp;&nbsp;<img id="actionicon" src="/tpl/stdstyle/images/log/Arrow-Right.png" alt="" style="vertical-align:top">
            </td>
        </tr>
        <tr class="form-group-sm">
            <td class="content-title-noshade">
                <img src="tpl/stdstyle/images/free_icons/date.png" class="icon16" alt="">&nbsp;{{date_logged}}:
            </td>
            <td class="options">
                <img src="tpl/stdstyle/images/free_icons/date_previous.png" alt ="{{lc_Day_before}}" title="{{lc_Day_before}}" onclick="subs_days(1);"/>
                <input class="form-control input30" type="text" id="logday"  name="logday" maxlength="2" value="{logday}"/>.
                <input class="form-control input30" type="text" id="logmonth" name="logmonth" maxlength="2" value="{logmonth}"/>.
                <input class="form-control input50" type="text" id="logyear" name="logyear" maxlength="4" value="{logyear}"/>
                <img src="tpl/stdstyle/images/free_icons/date_next.png" alt ="{{lc_Day_after}}" title="{{lc_Day_after}}" onclick="subs_days(-1);"/>
                &nbsp;&nbsp;
                <img src="tpl/stdstyle/images/free_icons/clock.png" class="icon16" alt="">&nbsp;{{time}}:  <input class="form-control input30" type="text" name="loghour" maxlength="2" value="{loghour}"/> HH (0-23)
                <input class="form-control input30" type="text" name="logmin" maxlength="2" value="{logmin}"> MM (0-59)
                <br>{date_message}
            </td>
        </tr>
            {rating_message}
        </table>
        <div class="content2-container">
            <div class="buffer"></div>
            <p id="scriptwarning" class="errormsg">{{javascript_edit_info}}</p>
            <img src="tpl/stdstyle/images/free_icons/page_edit.png" class="icon16" alt="">&nbsp;<span class="content-title-noshade-size12">{{comments_log}}:</span>
            <div class="buffer"></div>
            <textarea name="logtext" id="logtext" class="cachelog tinymce">{logtext}</textarea>
            {log_pw_field}
            <div class="buffer"></div>
            <input class="btn btn-default" type="reset" name="reset" value="{{reset}}">&nbsp;&nbsp;
            <a href="#" class="btn btn-primary" onclick="event.preventDefault();
                $(this).closest('form').submit()">{{submit}}</a>
            <input type="hidden" name="submitform" value="{{submit}}">
        </div>
</form>
