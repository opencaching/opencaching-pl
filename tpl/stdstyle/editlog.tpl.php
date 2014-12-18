<?php
/* * *************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *  UTF-8 ąść
 * ************************************************************************* */
?>
<link href="tpl/stdstyle/css/confirmCancelButtons.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
<!--

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

    function insertSmiley(parSmiley) {
        var myText = document.editlog.logtext;
        myText.focus();
        /* fuer IE */
        if (typeof document.selection != 'undefined') {
            var range = document.selection.createRange();
            var selText = range.text;
            range.text = parSmiley + selText;
        }
        /* fuer Firefox/Mozilla-Browser */
        else if (typeof myText.selectionStart != 'undefined')
        {
            var start = myText.selectionStart;
            var end = myText.selectionEnd;
            var selText = myText.value.substring(start, end);
            myText.value = myText.value.substr(0, start) + parSmiley + selText + myText.value.substr(end);
            /* Cursorposition hinter Smiley setzen */
            myText.selectionStart = start + parSmiley.length;
            myText.selectionEnd = start + parSmiley.length;
        }
        /* fuer die anderen Browser */
        else
        {
            alert(navigator.appName + ': Setting smilies is not supported');
        }
    }

    function _chkFound() {
<?php
$sql = "SELECT count(cache_id) FROM cache_logs WHERE deleted=0 AND cache_id = (SELECT cache_id FROM cache_logs WHERE deleted=0 AND id = '" . sql_escape(intval($_REQUEST['logid'])) . "') AND user_id = '" . sql_escape($usr['userid']) . "' AND type='1'";
$founds = mysql_result(mysql_query($sql), 0);
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
        document.editlog.actionicon.src = image_log;
//         var el;
//  el='coord_table';
//  if (document.editlog.logtype.value == "4")
//  {document.getElementById(el).style.display='block';
//    } else {document.getElementById(el).style.display='none';}
    }

//-->
</script>

<script type="text/javascript" src="lib/tinymce4/tinymce.min.js"></script>
<script src="tpl/stdstyle/js/jquery-2.0.3.min.js"></script>
<script type="text/javascript">
    tinymce.init({
        selector: "textarea",
        width: 600,
        height: 350,
        menubar: false,
        toolbar_items_size: 'small',
        language: "{language4js}",
        gecko_spellcheck: true,
        relative_urls: false,
        remove_script_host: false,
        entity_encoding: "raw",
        toolbar1: "newdocument | styleselect formatselect fontselect fontsizeselect",
        toolbar2: "cut copy paste | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image code | preview ",
        toolbar3: "bold italic underline strikethrough |  alignleft aligncenter alignright alignjustify | hr | subscript superscript | charmap emoticons | forecolor backcolor | nonbreaking ",
        plugins: [
            "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
            "table directionality emoticons template textcolor paste textcolor"
        ],
    });

    $(function () {
        $('#scriptwarning').hide();

        // $.datepicker.setDefaults($.datepicker.regional['pl']);
        // $('#hiddenDatePicker, #activateDatePicker').datepicker({
        // dateFormat: 'yy-mm-dd',
        // regional: '{language4js}'
        // }).val();
    });

</script>


<form action="editlog.php" method="post" enctype="application/x-www-form-urlencoded" name="editlog" id="editlog" dir="ltr">
    <input type="hidden" name="logid" value="{logid}"/>
    <input type="hidden" name="version2" value="1"/>
    <input id="descMode" type="hidden" name="descMode" value="{descMode}" />
    <table class="content">
        <tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="" title="edit log Cache" align="middle" /> <b>{{edit_logentry}} <a href="viewcache.php?cacheid={cacheid}">{cachename}</a></b></td></tr>

        <tr>
            <td colspan="2">
                <br /><span id="scriptwarning" class="errormsg">{{pt129}}.</span><br />
            </td>
        </tr>

    </table>
    <div class="searchdiv">
        <table class="content" style="font-size: 12px; line-height: 1.6em;">
            <tr><td class="spacer" colspan="2"></td></tr>
            <tr>
                <td width="180px"><img src="tpl/stdstyle/images/free_icons/page_go.png" class="icon16" alt="" title="" align="middle" />&nbsp;<strong>{{type_of_log}}:</strong></td>
                <td align="left">
                    <!--<select name="logtype" onChange="return _chkFound()">-->
                    <select onload="javascript:toogleLayer('ocena');" name="logtype" onchange="javascript:toogleLayer('ocena');">
                        {logtypeoptions}
                    </select>&nbsp;&nbsp;<img name='actionicon' src='' align="top" alt="">
                </td>
            </tr>
            <tr><td class="spacer" colspan="2"></td></tr>

            <tr>
                <td width="180px"><img src="tpl/stdstyle/images/free_icons/date.png" class="icon16" alt="" title="" align="middle" />&nbsp;<strong>{{date_logged}}:</td>
                <td align="left">
                    <img src="tpl/stdstyle/images/free_icons/date_previous.png" alt ="{{lc_Day_before}}" title="{{lc_Day_before}}" onclick="subs_days(1);"/>
                    <input class="input20" type="text" id="logday"  name="logday" maxlength="2" value="{logday}"/>.
                    <input class="input20" type="text" id="logmonth" name="logmonth" maxlength="2" value="{logmonth}"/>.
                    <input class="input40" type="text" id="logyear" name="logyear" maxlength="4" value="{logyear}"/>
                    <img src="tpl/stdstyle/images/free_icons/date_next.png" alt ="{{lc_Day_after}}" title="{{lc_Day_after}}" onclick="subs_days(-1);"/>
                    &nbsp;&nbsp;
                    <img src="tpl/stdstyle/images/free_icons/clock.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{time}} :  <input class="input20" type="text" name="loghour" maxlength="2" value="{loghour}"/> HH (0-23)
                    <input class="input20" type="text" name="logmin" maxlength="2" value="{logmin}"/> MM (0-60)
                    <br />{date_message}
                </td>
            </tr>
            <tr><td class="spacer" colspan="2"></td></tr>
            {rating_message}
        </table>
        <table class="content" style="font-size: 12px; line-height: 1.6em;">
            <tr>
                <td colspan="2"><br /><img src="tpl/stdstyle/images/free_icons/page_edit.png" class="icon16" alt="" title="" align="middle" />&nbsp;<strong>{{comments_log}}:</td>
            </tr>
            <tr>
                <td>
                    <textarea name="logtext" id="logtext">{logtext}</textarea>
                </td>
            </tr>
            <tr>
                <td id="smilies" colspan="2" style="display: {smiliesdisplay}">
                    {smilies}
                </td>
            </tr>
            <tr><td class="spacer" colspan="2"></td></tr>

            {log_pw_field}

            <tr><td class="spacer" colspan="2"></td></tr>
            <tr>
                <td class="header-small" colspan="2" align="center">
                    <input class="blueConfirmButton" type="reset" name="reset" value="{{reset}}" style="width:120px"/>&nbsp;&nbsp;
                    <a href="#" class="blueConfirmButton" onclick="event.preventDefault();
                            $(this).closest('form').submit()">{{submit}}</a>
                    <input type="hidden" name="submitform" value="{{submit}}" style="width:120px"/>
                </td>
            </tr>
        </table>
    </div>
</form>

