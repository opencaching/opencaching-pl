<?php

/***************************************************************************
    *
    *   This program is free software; you can redistribute it and/or modify
    *   it under the terms of the GNU General Public License as published by
    *   the Free Software Foundation; either version 2 of the License, or
    *   (at your option) any later version.
    *
    *  UTF8 remaider śąłó
    ***************************************************************************/


require_once('./lib/common.inc.php');

?>
<link href="tpl/stdstyle/css/confirmCancelButtons.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
<!--
function subs_days (days_number) {
 //alert('ok');
 var d_day = document.getElementById('logday').value ;
 var d_mn = document.getElementById('logmonth').value-1;
 var d_yr = document.getElementById('logyear').value ;
 var d = new Date (d_yr, d_mn , d_day - days_number , 0, 0,0);


 //alert(d);
 if (isNaN(d)==false)
    {

        var d_now = +new Date;
        if (d <=d_now )
        {
         document.getElementById('logday').value = d.getDate();
         document.getElementById('logmonth').value = d.getMonth()+1;
         document.getElementById('logyear').value = d.getFullYear();
        }   ;
    };
};

function do_reset() {
        if(!confirm("{Do_reset_logform}"))
        {
            return false;
        } else {
            var frm = document.getElementById("logform");

            frm.reset();
            //window.location.reload();
            document.getElementById( 'logtype' ).onchange();

            handle_score_note();
            var GKBox = document.getElementById("toggleGeoKretyDIV");
            GKBox.style.display = "none";
            return true;
        };

};

function onSubmitHandler()
{

    handle_score_note();
    if(document.getElementById('logtext').value.length == 0) {

    }
    var length;
    if(tinyMCE && tinyMCE.get('logtext')) {
        length = tinyMCE.get('logtext').getContent().length;
    }
    else  {
        length = document.getElementById('logtext').value.length;
    }
    if(length == 0) {
        if(!confirm("{{empty_entry_confirm}}"))
            return false;
    }

    var rates = document.getElementsByName('r');
    var rate_value =-15;
    for (var i = 0; i < rates.length; i++) {

              if (rates[i].checked) {
               rate_value =  rates[i].value;
                };
          };
    //alert(rate_value);

    if ((document.getElementById( 'logtype' ).value == 1) && ((rate_value == -10) || (rate_value  == -15)))
    {
        if(!confirm("{{empty_mark}}"))
            return false;
    }


    //document.getElementById(obj).disabled = true;
    //document.logform.submitform.disabled = true;
    setTimeout('document.logform.submitform.disabled=true',1);

    return true;
}

function insertSmiley(parSmiley) {
  var myText = document.logform.logtext;
  myText.focus();
  /* fuer IE */
  if(typeof document.selection != 'undefined') {
    var range = document.selection.createRange();
    var selText = range.text;
    range.text = parSmiley + selText;
  }
  /* fuer Firefox/Mozilla-Browser */
  else if(typeof myText.selectionStart != 'undefined')
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

function _chkFound () {

<?php
        $sql = "SELECT count(cache_id) FROM cache_logs WHERE `deleted`=0 AND cache_id = '".sql_escape($_REQUEST['cacheid'])."' AND user_id = '".sql_escape($usr['userid'])."' AND type='1'";
        $tmp = sql($sql);
        $founds = mysql_result($tmp,0);
?>


  if (document.logform.logtype.value == "1" || (<?php echo $founds;?>>0 && document.logform.logtype.value == "3") || document.logform.logtype.value == "7") {
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


function toogleLayer( whichLayer, val )
{
        chkMoved();
        var elem, vis;
        _chkFound();
        if( document. getElementById )
                elem = document.getElementById(whichLayer);
        else if( document.all )
                elem = document.all[whichLayer];
        else if( document.layers )
                elem = document.layers[whichLayer];
        vis = elem.style;

        if(val != '')
        {
        if (document.logform.logtype.value == "1" || document.logform.logtype.value == "7")
                vis.display = 'block';
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
                        document.logform.actionicon.src = image_log;

         var el;
      el='coord_table';
      mvd1="wybor_NS";
      mvd2="wsp_NS_st";
      mvd3="wsp_NS_min";
      mvd4="wybor_WE";
      mvd5="wsp_WE_st";
      mvd6="wsp_WE_min";

      if (document.logform.logtype.value == "4")
       {
        document.getElementById(el).style.display='block';
        document.getElementById(mvd1).disabled=false;
        document.getElementById(mvd2).disabled=false;
        document.getElementById(mvd3).disabled=false;
        document.getElementById(mvd4).disabled=false;
        document.getElementById(mvd5).disabled=false;
        document.getElementById(mvd6).disabled=false;
       }
      else
      {
       document.getElementById(el).style.display='none';
       document.getElementById(mvd1).disabled=true;
       document.getElementById(mvd2).disabled=true;
       document.getElementById(mvd3).disabled=true;
       document.getElementById(mvd4).disabled=true;
       document.getElementById(mvd5).disabled=true;
       document.getElementById(mvd6).disabled=true;
      }
}
//-->

function formDefault(theInput) {
if (theInput.value =='') {
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
        document.getElementById(gk).style.display='none';
       }
      else
      {
       document.getElementById(gk).style.display='inline';
      }
}

function toggleGeoKrety() {
    var GKBox = document.getElementById("toggleGeoKretyDIV");

    if(GKBox.style.display == "block")
     {
        GKBox.style.display = "none";
     }
    else
     {
        GKBox.style.display = "block";
     }
}




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
    language : "{language4js}",
    gecko_spellcheck : true,
    relative_urls : false,
    remove_script_host: false,
    entity_encoding : "raw",
    toolbar1: "newdocument | styleselect formatselect fontselect fontsizeselect",
    toolbar2: "cut copy paste | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image code | preview ",
    toolbar3: "bold italic underline strikethrough |  alignleft aligncenter alignright alignjustify | hr | subscript superscript | charmap emoticons | forecolor backcolor | nonbreaking ",

     plugins: [
        "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
        "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
        "table directionality emoticons template textcolor paste textcolor"
     ],
 });

$(function() {
    $('#scriptwarning').hide();

    // $.datepicker.setDefaults($.datepicker.regional['pl']);
    // $('#hiddenDatePicker, #activateDatePicker').datepicker({
        // dateFormat: 'yy-mm-dd',
        // regional: '{language4js}'
    // }).val();
});

</script>

<form action="log.php" method="post" enctype="application/x-www-form-urlencoded" name="logform" id="logform" dir="ltr" onsubmit="return onSubmitHandler()" >
<input type="hidden" name="cacheid" value="{cacheid}"/>
<input type="hidden" name="version2" value="1"/>
<input id="descMode" type="hidden" name="descMode" value="3" />

<table class="content">
    <tr>
        <td class="content2-pagetitle" colspan="2">
            <img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="" title="Cache" align="middle" />
            <b>{{post_new_log}} <a href="viewcache.php?cacheid={cacheid}">{cachename}</a></b>
        </td>
    </tr>
        <tr>
            <br /><span id="scriptwarning" class="errormsg">{{pt129}}.</span><br />
        </td>
    </tr>
</table>
<div class="searchdiv">
<table class="content" style="font-size: 12px; line-height: 1.6em;">
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr>
        <td colspan="2"><div class="notice" style="width: 500px;">{{report_problem_notice}} <img src="/tpl/stdstyle/images/blue/arrow.png" alt="" title=""/>&nbsp; <a class="links" href="reportcache.php?cacheid={cacheid}">{{report_problem}}</a> </div></td>
    </tr>
        <tr>
                    <td colspan="2"><div class="notice" style="width:500px;height:44px">{{empty_entry_notice}}</div></td>
                    </tr>
    <tr>
        <td width="180px"><img src="tpl/stdstyle/images/free_icons/page_go.png" class="icon16" alt="" title="" align="middle" />&nbsp;<strong>{{type_of_log}}:</strong></td>
        <td>
            <select onLoad="javascript:toogleLayer('ocena');" name="logtype" id="logtype" onChange="toogleLayer('ocena');">
                {logtypeoptions}
            </select>&nbsp;&nbsp;<img name='actionicon' src='' align="top" alt="">
            <br />{log_message}
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>


    <tr>
        <td width="180px"><img src="tpl/stdstyle/images/free_icons/date.png" class="icon16" alt="" title="" align="middle" />&nbsp;<strong>{{date_logged}}:</strong></td>
        <td>
            <img src="tpl/stdstyle/images/free_icons/date_previous.png" alt ="{{lc_Day_before}}" title="{{lc_Day_before}}" onclick="subs_days(1);"/>
            <input class="input20" type="text" id="logday" name="logday" maxlength="2" value="{logday}"/>.
            <input class="input20" type="text" id="logmonth" name="logmonth" maxlength="2" value="{logmonth}"/>.
            <input class="input40" type="text" id="logyear" name="logyear" maxlength="4" value="{logyear}"/>
            <img src="tpl/stdstyle/images/free_icons/date_next.png" alt ="{{lc_Day_after}}" title="{{lc_Day_after}}" onclick="subs_days(-1);"/>

              &nbsp;&nbsp;<img src="tpl/stdstyle/images/free_icons/clock.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{time}} :  <input class="input20" type="text" name="loghour" maxlength="2" value="{loghour}"/> HH (0-23)
            <input class="input20" type="text" name="logmin" maxlength="2" value="{logmin}"/> MM (0-60)
            <br />{date_message}
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    {rating_message}
    <tr><td class="spacer" colspan="2"></td></tr>
</table>

<div class="content" id="ocena" style="display:{display};">
<table class="content" style="font-size: 12px; line-height: 1.6em;" border="0">
    <tr>
        <td width="180px" valign="top"><img src="tpl/stdstyle/images/free_icons/star.png" class="icon16" alt="" title="" align="middle" />&nbsp;<b>{score_header}</b></td>
        <td width="*">{score}<br/>&nbsp;<span class="notice" style="width:500px;height:44px" id="no_score" name="no_score"  >{score_note_innitial}</span></td>
    </tr>
</table>
<br />
<!-- GeoKretyApi (by Łza) -->
<div id="GeoKretyApi">
 <p style="font-size: 12px;"><img src="tpl/stdstyle/images/description/22x22-geokret.png"> <a href="javascript:toggleGeoKrety();" class=links href="#">{{GKApi06}}</a>
 <div id="toggleGeoKretyDIV" style="display: none">

   <div style="display: {GeoKretyApiNotConfigured}; width: 500px; color: red; font-size: 12px;">
     {{GKApi07}}{{GKApi08}}<br /><br />
     1. {{GKApi09}} <a href="http://geokrety.org/mypage.php">{{GKApi04}}</a><br />
     2. {{GKApi10}} <a href="myprofile.php?action=change">{{GKApi04}}</a>  <br />
   </div>

   <div style="display: {GeoKretyApiConfigured}">
    <p style="color: darkgreen">{{GKApi05}}</p>
    {GeoKretApiSelector}
    <hr align ="left" style="width: 380px; color: darkgreen; align: left;"/>
    <p style="color: darkgreen">{{GKApi18}}</p>
    {GeoKretApiSelector2}
   </div>
 </div>
</div>
<!-- end of GeoKretyApi (by Łza) -->

</div>

<!-- [Łza] logowanie keszynek mobilnych: nowe współrzędne  -->
<div class="content" id="coord_table" style="display:none;">
<table class="content" style="font-size: 12px; line-height: 1.6em;">
    <tr>
        <td></td>
        <td><span style="color: red; font-weight:bold;">{coords_not_ok}</span></td>
    </tr>
    <tr>
        <td width="180px">
          <img src="tpl/stdstyle/images/log/16x16-moved.png" class="icon16" alt="" title="" align="middle" /><b>&nbsp;{{nowe_wsp}}</b>

        </td>
        <td>
         <select name="wybor_NS"   id="wybor_NS"   disabled="disabled"><option selected="selected">N</option><option>S</option></select>
         <input type="text"        id="wsp_NS_st"  name="wsp_NS_st"  size="2" maxlength="2" disabled="disabled" value="{wsp_NS_st}"/>
         <input type="text"        id="wsp_NS_min" name="wsp_NS_min" size="6" maxlength="6" disabled="disabled" value="{wsp_NS_min}" onkeyup="this.value=this.value.replace( /,/g,'.' );"/>
         <span style="color: red; font-weight:bold;">{lat_message}</span>
         <br />
         <select name="wybor_WE"  id="wybor_WE"   disabled="disabled"><option selected="selected">E</option><option>W</option></select>
         <input type="text"       id="wsp_WE_st"  name="wsp_WE_st"  size="2" value="{wsp_WE_st}"  maxlength="2" disabled="disabled" />
         <input type="text"       id="wsp_WE_min" name="wsp_WE_min" size="6" value="{wsp_WE_min}" maxlength="6" disabled="disabled" onkeyup="this.value=this.value.replace( /,/g,'.' );" />
         <span style="color: red; font-weight:bold;">{lon_message}</span>
        </td>
        <td width="*">      <br/></td>
    </tr>
</table>
</div>



<table class="content" style="font-size: 12px; line-height: 1.6em;">
    <tr>
        <td colspan="2"><br /><img src="tpl/stdstyle/images/free_icons/page_edit.png" class="icon16" alt="" title="" align="middle" />&nbsp;<strong>{{comments_log}}:</strong><br /></td>
    </tr>
    <tr>
        <td>
            <textarea name="logtext" id="logtext">{logtext}</textarea>
    </td>
    </tr>
    <tr>
        <td colspan="2">
            {smilies}
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>

        {log_pw_field}

    <tr><td class="spacer" colspan="2"></td></tr>
    {listed_start}
    <tr>
        <td colspan="2" width="600px"><strong><img src="tpl/stdstyle/images/free_icons/world_go.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{listed_other}}:&nbsp;{listed_on}</strong>
        </td>
    </tr>

    <tr><td class="spacer" colspan="2"></td></tr>
    {listed_end}
    <tr>
        <td class="header-small" colspan="2" align="center">
            <a href="#" class="blueConfirmButton" onclick="return do_reset()">{log_reset_button}</a>
            <a href="#" class="blueConfirmButton" onclick="event.preventDefault();$(this).closest('form').submit()">{{submit_log_entry}}</a>
            <input type="hidden" name="submitform" value="{{submit}}" style="width:120px"/>
        </td>

    </tr>
    <?php if (strpos($_SERVER['HTTP_USER_AGENT'], "Android") !== false) { ?>
        <tr>
            <td colspan="2">
                <div style='background: url(/images/android_notice.png); background-repeat: no-repeat; padding-left: 70px; padding-top: 10px; max-width: 400px; margin: 7px 0 20px 0'>
                    <p style='font-size: 18px; font-weight: bold'>Ułatwienia dla Androida</p>
                    <p>Na <a href='http://forum.opencaching.pl/viewforum.php?f=6'>naszym forum</a> znajdziesz
                    informacje o wielu aplikacjach ułatwiających keszowanie z Androidem. Niektóre z nich są
                    płatne, ale jest też wiele bardzo przydatnych, darmowych aplikacji. Warto
                    <a href='http://forum.opencaching.pl/viewforum.php?f=6'>sprawdzić</a>!</p>
                </div>
            </td>
        </tr>
    <?php } ?>
</table>
</div>
</form>
<script language="javascript" type="text/javascript">
<!--
handle_score_note();
function highlight_score_labels () {
  var score_rates = document.getElementsByName('r');
  for(var i = 0; i < score_rates.length; i++)
  {
        if (score_rates[i].value!=-15) //do not do for hidden default value
        {
            var thisLabel = document.getElementById('score_lbl_'+i) ;
            var score_txt = thisLabel.innerHTML;
            score_txt = score_txt.replace('<u>','');
            score_txt = score_txt.replace('</u>','');
            if(score_rates[i].checked) {score_txt= '<u>'+score_txt+'</u>';};
            thisLabel.innerHTML = score_txt;
        };
  }
}

function clear_no_score () {
document.getElementById('no_score').innerHTML="{score_note_thanks}";
highlight_score_labels();

}

function encor_no_score () {
document.getElementById('no_score').innerHTML="{score_note_encorage}";
highlight_score_labels();

}

function handle_score_note () {
 var score_rates = document.getElementsByName('r');
  for(var i = 0; i < score_rates.length; i++)
  {
    if (score_rates[i].checked)
        {
            //alert(i);
            if (score_rates[i].value == -10)
            {
                encor_no_score ();
                return;
            } else {
                 clear_no_score ();
                 return;
            }
        }

  }
  document.getElementById('no_score').innerHTML="{score_note_innitial}";
  highlight_score_labels();
}


//-->
</script>
