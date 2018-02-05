<?php

use Utils\Database\XDb;
require_once('./lib/common.inc.php');

$view->callChunk('tinyMCE', false);
?>
<script>
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
            ;
        }
        ;
    }
    ;

    function do_reset() {
        if (!confirm("{Do_reset_logform}"))
        {
            return false;
        } else {
            var frm = document.getElementById("logform");

            frm.reset();
            //window.location.reload();
            document.getElementById('logtype').onchange();

            handle_score_note();
            var GKBox = document.getElementById("toggleGeoKretyDIV");
            GKBox.style.display = "none";
            return true;
        }
        ;

    }
    ;

    function onSubmitHandler()
    {

        handle_score_note();
        if (document.getElementById('logtext').value.length == 0) {

        }
        var length;
        if (tinyMCE && tinyMCE.get('logtext')) {
            length = tinyMCE.get('logtext').getContent().length;
        }
        else {
            length = document.getElementById('logtext').value.length;
        }
        if (length == 0) {
            if (!confirm("{{empty_entry_confirm}}"))
                return false;
        }

        var rates = document.getElementsByName('r');
        var rate_value = -15;
        for (var i = 0; i < rates.length; i++) {

            if (rates[i].checked) {
                rate_value = rates[i].value;
            }
            ;
        }
        ;
        //alert(rate_value);

        if ((document.getElementById('logtype').value == 1) && ((rate_value == -10) || (rate_value == -15)))
        {
            if (!confirm("{{empty_mark}}"))
                return false;
        }


        //document.getElementById(obj).disabled = true;
        //document.logform.submitform.disabled = true;
        setTimeout('document.logform.submitform.disabled=true', 1);

        return true;
    }

    function _chkFound() {

<?php

$founds = XDb::xMultiVariableQueryValue(
    "SELECT count(cache_id)
    FROM cache_logs
    WHERE `deleted`=0
        AND cache_id = :1
        AND user_id = :2
        AND type='1'",
    0, $_REQUEST['cacheid'], $usr['userid'] );

?>


        if (document.logform.logtype.value == "1" || (<?php echo $founds; ?> > 0 && document.logform.logtype.value == "3") || document.logform.logtype.value == "7") {
            document.logform.r.disabled = false;
            //document.logform.rating.disabled = false;
        }
        else
        {
            document.logform.r.disabled = false;
            //document.logform.rating.disabled = true;
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
            if (document.logform.logtype.value == "1" || document.logform.logtype.value == "7")
                vis.display = 'table-row';
            else
                vis.display = 'none';
        }
        else
            vis.display = val;

        //if( vis.display==''&&elem.offsetWidth!=undefined&&elem.offsetHeight!=undefined)
        //      vis.display=(elem.offsetWidth!=0&&elem.offsetHeight!=0)?'block':'none';
        //vis.display = (vis.display==''||vis.display=='block')?'none':'block';
    }
    function chkMoved()
    {
        var mode = document.logform.logtype.value;
        var iconarray = new Array();
        iconarray['-2'] = 'Arrow-Right.png';
        iconarray['1'] = '16x16-found.png';
        iconarray['2'] = '16x16-dnf.png';
        iconarray['3'] = '16x16-note.png';
        iconarray['4'] = '16x16-moved.png';
        iconarray['5'] = '16x16-need-maintenance.png';
        iconarray['6'] = '16x16-made-maintenance.png';
        iconarray['7'] = '16x16-go.png';
        iconarray['8'] = '16x16-wattend.png';
        iconarray['9'] = '16x16-trash.png';
        iconarray['10'] = '16x16-published.png';
        iconarray['11'] = '16x16-temporary.png';
        iconarray['12'] = '16x16-octeam.png';
        var image_log = "/tpl/stdstyle/images/log/" + iconarray[mode];
        document.getElementById('actionicon').src = image_log;

        var el;
        el = 'coord_table';
        mvd1 = "wybor_NS";
        mvd2 = "wsp_NS_st";
        mvd3 = "wsp_NS_min";
        mvd4 = "wybor_WE";
        mvd5 = "wsp_WE_st";
        mvd6 = "wsp_WE_min";

        if (document.logform.logtype.value == "4")
        {
            document.getElementById(el).style.display = 'table-row';
            document.getElementById(mvd1).disabled = false;
            document.getElementById(mvd2).disabled = false;
            document.getElementById(mvd3).disabled = false;
            document.getElementById(mvd4).disabled = false;
            document.getElementById(mvd5).disabled = false;
            document.getElementById(mvd6).disabled = false;
        }
        else
        {
            document.getElementById(el).style.display = 'none';
            document.getElementById(mvd1).disabled = true;
            document.getElementById(mvd2).disabled = true;
            document.getElementById(mvd3).disabled = true;
            document.getElementById(mvd4).disabled = true;
            document.getElementById(mvd5).disabled = true;
            document.getElementById(mvd6).disabled = true;
        }
    }

    function formDefault(theInput) {
        if (theInput.value == '') {
            theInput.value = theInput.defaultValue;
        }
    }

    function GkActionMoved(kret)
    {
        var mode = document.logform.GeoKretSelector1.value;
        gk = "GKtxt" + kret;
        sel = "GeoKretSelector" + kret;

        // if (document.logform.GeoKretSelector1.value != -1)
        if (document.getElementById(sel).value == -1)
        {
            document.getElementById(gk).style.display = 'none';
        }
        else
        {
            document.getElementById(gk).style.display = 'inline';
        }
    }

    function toggleGeoKrety() {
        var GKBox = document.getElementById("toggleGeoKretyDIV");

        if (GKBox.style.display == "block")
        {
            GKBox.style.display = "none";
        }
        else
        {
            GKBox.style.display = "block";
        }
    }
</script>


<script>
    $(function () {
        $('#scriptwarning').hide();
    });
</script>

<form action="log.php" method="post" enctype="application/x-www-form-urlencoded" name="logform" id="logform" dir="ltr" onsubmit="return onSubmitHandler()" >
    <input type="hidden" name="cacheid" value="{cacheid}"/>
    <input type="hidden" name="version2" value="1"/>

    <div class="content2-pagetitle">
        <img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="">&nbsp;{{post_new_log}} <a href="viewcache.php?cacheid={cacheid}">{cachename}</a>
    </div>
    <div class="buffer"></div>
    <div class="notice">{{empty_entry_notice}}</div>
    <div class="notice">{{report_problem_notice}} <a class="links" href="report.php?action=add&cacheid={cacheid}">{{report_problem}}</a></div>

    <table class="table">
        <tr class="form-group-sm">
            <td class="content-title-noshade">
                <img src="tpl/stdstyle/images/free_icons/page_go.png" class="icon16" alt="">&nbsp;{{type_of_log}}:
            </td>
            <td class="options">
                <select class="form-control input200" name="logtype" id="logtype" onChange="toogleLayer('ocena');">
                    {logtypeoptions}
                </select>&nbsp;&nbsp;<img id="actionicon" src="/tpl/stdstyle/images/log/Arrow-Right.png" alt="" style="vertical-align:top">
                &nbsp;{log_message}
            </td>
        </tr>
        <tr class="form-group-sm">
            <td class="content-title-noshade">
                <img src="tpl/stdstyle/images/free_icons/date.png" class="icon16" alt="">&nbsp;{{date_logged}}:
            </td>
            <td class="options">
                <img src="tpl/stdstyle/images/free_icons/date_previous.png" alt ="{{lc_Day_before}}" title="{{lc_Day_before}}" onclick="subs_days(1);"/>
                <input class="form-control input30" type="text" id="logday" name="logday" maxlength="2" value="{logday}"/>.
                <input class="form-control input30" type="text" id="logmonth" name="logmonth" maxlength="2" value="{logmonth}"/>.
                <input class="form-control input50" type="text" id="logyear" name="logyear" maxlength="4" value="{logyear}"/>
                <img src="tpl/stdstyle/images/free_icons/date_next.png" alt ="{{lc_Day_after}}" title="{{lc_Day_after}}" onclick="subs_days(-1);"/>
                &nbsp;&nbsp;<img src="tpl/stdstyle/images/free_icons/clock.png" class="icon16" alt="">&nbsp;{{time}}:
                <input class="form-control input30" type="text" name="loghour" maxlength="2" value="{loghour}"/> HH (0-23)
                <input class="form-control input30" type="text" name="logmin" maxlength="2" value="{logmin}"/> MM (0-59)
                <br>{date_message}
            </td>
        </tr>
            {rating_message}
        <tr class="form-group-sm" id="ocena" style="display:{display};">
            <td class="content-title-noshade" style="vertical-align:top">
                <img src="tpl/stdstyle/images/free_icons/star.png" class="icon16" alt="">&nbsp;{score_header}
            </td>
            <td class="options">
                {score}<br>
                <span class="notice" id="no_score">{score_note_innitial}</span>
            </td>
        </tr>
        <tr class="form-group-sm" id="coord_table" style="display:none;">
            <td class="content-title-noshade" style="vertical-align:top">
                <img src="tpl/stdstyle/images/log/16x16-moved.png" class="icon16" alt="">&nbsp;{{new_coords}}:
            </td>
            <td class="options">
                <select name="wybor_NS"   id="wybor_NS"   disabled="disabled" class="form-control input50"><option selected="selected">N</option><option>S</option></select>
                <input type="text"        id="wsp_NS_st"  name="wsp_NS_st"  size="2" maxlength="2" disabled="disabled" value="{wsp_NS_st}" class="form-control input40" placeholder="dd">&nbsp;°
                <input type="text"        id="wsp_NS_min" name="wsp_NS_min" size="6" maxlength="6" disabled="disabled" value="{wsp_NS_min}" class="form-control input70" placeholder="mm.mmm" onkeyup="this.value = this.value.replace(/,/g, '.');"/>&nbsp;'
                <span class="errormsg">{lat_message}</span>
                <br>
                <select name="wybor_WE"  id="wybor_WE"   disabled="disabled" class="form-control input50"><option selected="selected">E</option><option>W</option></select>
                <input type="text"       id="wsp_WE_st"  name="wsp_WE_st"  size="2" value="{wsp_WE_st}"  maxlength="3" disabled="disabled" class="form-control input40" placeholder="dd" />&nbsp;°
                <input type="text"       id="wsp_WE_min" name="wsp_WE_min" size="6" value="{wsp_WE_min}" maxlength="6" disabled="disabled" class="form-control input70" placeholder="mm.mmm" onkeyup="this.value = this.value.replace(/,/g, '.');" />&nbsp;'
                <span class="errormsg">{lon_message}</span>
                <br>
                <span class="errormsg">{coords_not_ok}</span>
            </td>
        </tr>
    </table>
    <div class="buffer"></div>
    <div class="content2-container">
        <img src="tpl/stdstyle/images/description/22x22-geokret.png" alt=""> <a onclick="javascript:toggleGeoKrety();" class="links" href="#"><span class="content-title-noshade-size12">{{GKApi06}}</span></a>
        <div id="toggleGeoKretyDIV" style="display: none">
            <div style="display: {GeoKretyApiNotConfigured};">
                <span class="errormsg"><br>{{GKApi07}}</span><br><br>
                {{GKApi08}}<br>
                1. {{GKApi09}} (<a href="https://geokrety.org/mypage.php" class="links" target="_blank">{{GKApi04}}</a>)<br>
                2. {{GKApi10}} (<a href="myprofile.php?action=change" class="links" target="_blank">{{GKApi04}}</a>)<br>
            </div>
            <div style="display: {GeoKretyApiConfigured}">
                <p><br>{{GKApi05}}</p>
                {GeoKretApiSelector}
                <p><br>{{GKApi18}}</p>
                {GeoKretApiSelector2}
            </div>
            <br><div class="notice">{{GKApi19}} {{GKApi27}}</div>
        </div>
        <div class="buffer"></div>
        <p id="scriptwarning" class="errormsg">{{javascript_edit_info}}</p>
        <img src="tpl/stdstyle/images/free_icons/page_edit.png" class="icon16" alt="">&nbsp;<span class="content-title-noshade-size12">{{comments_log}}:</span>
        <div class="buffer"></div>
        <textarea name="logtext" id="logtext" class="cachelog tinymce">{logtext}</textarea>
        {log_pw_field}
        <div class="buffer"></div>
        <a href="#" class="btn btn-default" onclick="return do_reset()">{log_reset_button}</a>
        <a href="#" class="btn btn-primary" onclick="event.preventDefault();
                $(this).closest('form').submit()">{{submit_log_entry}}</a>
        <input type="hidden" name="submitform" value="{{submit}}">
    </div>
</form>
<script>
    handle_score_note();
    function highlight_score_labels() {
        var score_rates = document.getElementsByName('r');
        for (var i = 0; i < score_rates.length; i++)
        {
            if (score_rates[i].value != -15) //do not do for hidden default value
            {
                var thisLabel = document.getElementById('score_lbl_' + i);
                var score_txt = thisLabel.innerHTML;
                score_txt = score_txt.replace('<u>', '');
                score_txt = score_txt.replace('</u>', '');
                if (score_rates[i].checked) {
                    score_txt = '<u>' + score_txt + '</u>';
                }
                ;
                thisLabel.innerHTML = score_txt;
            }
            ;
        }
    }

    function clear_no_score() {
        document.getElementById('no_score').innerHTML = "{score_note_thanks}";
        highlight_score_labels();

    }

    function encor_no_score() {
        highlight_score_labels();
        document.getElementById('no_score').innerHTML = "{score_note_encorage}";
    }

    function handle_score_note() {
        var score_rates = document.getElementsByName('r');
        for (var i = 0; i < score_rates.length; i++)
        {
            if (score_rates[i].checked)
            {
                //alert(i);
                if (score_rates[i].value == -10)
                {
                    encor_no_score();
                    return;
                } else {
                    clear_no_score();
                    return;
                }
            }

        }
        document.getElementById('no_score').innerHTML = "{score_note_innitial}";
        highlight_score_labels();
    }
</script>
