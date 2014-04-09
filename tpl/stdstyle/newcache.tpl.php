<?php

/***************************************************************************
    *
    *   This program is free software; you can redistribute it and/or modify
    *   it under the terms of the GNU General Public License as published by
    *   the Free Software Foundation; either version 2 of the License, or
    *   (at your option) any later version.
    *
    ***************************************************************************/

/****************************************************************************

   Unicode Reminder ?? ąść


 ****************************************************************************/
?>
<script type="text/javascript" src="lib/tinymce4/tinymce.min.js"></script>
<input type="hidden" value="arrrgh" id="qwertyuiop">
<script src="tpl/stdstyle/js/jquery-2.0.3.js"></script>
<link rel="stylesheet" href="tpl/stdstyle/js/jquery_1.9.2_ocTheme/themes/cupertino/jquery.ui.all.css">
<script src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ui/minified/jquery-ui.min.js"></script>
<script src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ui/jquery.datepick-{language4js}.js"></script>
<script src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ui/timepicker.js"></script>


<script type="text/javascript">
tinymce.init({
   selector: "#desc",
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
</script>

<script type="text/javascript">
<!--

$(function() {
    $( "#waypointsToChose" ).dialog({
        position: ['center',150],
        autoOpen: false,
        width: 500,
        modal: true,
        show: {effect: 'bounce', duration: 350, /* SPECIF ARGUMENT */ times: 3},
        hide: "explode",
        buttons:
        {
            {{newCacheWpClose}}: function()
            {
                $(this).dialog( "close" );
            }
        }
    });
});

function hiddenDatePickerChange(identifier){
    var dateTimeStr = $('#'+identifier+'DatePicker').val();
    var dateArr = dateTimeStr.split("-");
    $("#"+identifier+"_year").val(dateArr[0]);
    $("#"+identifier+"_month").val(dateArr[1]);
    $("#"+identifier+"_day").val(dateArr[2]);
}

$(function() {
    $('#scriptwarning').hide();
    chkcountry2();

    $.datepicker.setDefaults($.datepicker.regional['pl']);
    $('#hiddenDatePicker, #activateDatePicker').datepicker({
        dateFormat: 'yy-mm-dd',
        regional: '{language4js}'
    }).val();
});

function checkRegion(){
    // console.log($('#lat_min').val().length);
    if( $('#lat_h').val().length > 0 &&
        $('#lon_h').val().length > 0 &&
        $('#lat_min').val().length > 3 &&
        $('#lon_min').val().length > 3) {

        var latmin = parseFloat($('#lat_min').val());
        var lonmin = parseFloat($('#lon_min').val());

        var lat = parseFloat($('#lat_h').val()) +  latmin / 60;
        if($('#latNS').val() == 'S') lat = -lat;

        var lon = parseFloat($('#lon_h').val()) + lonmin / 60;
        if ($('#lonEW').val() == 'W') lon = -lon;

        request = $.ajax({
        url: "ajaxRetreiveRegionByCoordinates.php",
        type: "post",
        data:{lat: lat, lon: lon },
    });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR){
        if(response == 'false') {
            return false;
        }
        obj = JSON.parse(response);
        if($('#country').val() == obj['code1']) {
            $('#region1').val(obj['code3']);
        } else {
            $('#country').val(obj['code1']);
            chkcountry2();
            $(function() {
                setTimeout(function() {
                $('#region1').val(obj['code3']);
            }, 2000);
    });
        }
    });

    request.always(function () {
    });

    // alert(lat+' / '+lon);
    }
}


var maAttributes = new Array({jsattributes_array});

function startUpload(){
    $('#f1_upload_form').hide();
    $('#ajaxLoaderLogo').show();
    return true;
}

function stopUpload(success){
    $('#ajaxLoaderLogo').hide();
    $('#f1_upload_form').show();
    $('#wptInfo').show();
    $(function() {
        setTimeout(function() {
            $('#wptInfo').fadeOut(1000);
        }, 5000);
    });

    var gpx = jQuery.parseJSON(success);

    var waypointsCount = count(gpx);

    //console.log(waypointsCount);
    //console.log(gpx);

    if(waypointsCount==1){
        fillFormInputs(gpx[0])
    }
    if(waypointsCount>1){
        $('#gpxWaypointObject').val(success);
        var i=0;
        var costam = '{{newCacheWpDesc}}<br/><br/>';
        gpx.forEach(function(wayPoint) {
            costam += '<a href="javascript:void(0);" onclick="updateFromWaypoint('+i+')"><b>'
            +wayPoint.name+'</b> - '+wayPoint.coords_latNS+wayPoint.coords_lat_h+'°'+wayPoint.coords_lat_min+' / '
            +wayPoint.coords_lonEW+wayPoint.coords_lon_h+'°'+wayPoint.coords_lon_min+'<br />';
            i++;
        });
        $('#waypointsToChose').html(costam);
        $('#waypointsToChose').dialog('open');
        $(".ui-dialog-titlebar-close").hide();
    }
    return true;
}

function updateFromWaypoint(waypointId){
    var gpxWaypointObject = $('#gpxWaypointObject').val();
    var gpx = jQuery.parseJSON(gpxWaypointObject);
    fillFormInputs(gpx[waypointId]);
    $('#waypointsToChose').dialog("close");
    $('#wptInfo').show();
    $(function() {
        setTimeout(function() {
            $('#wptInfo').fadeOut(1000);
        }, 5000);
    });
}

function fillFormInputs(gpx){
    var CacheHidedate = gpx.time.substring(0,10);
    $("#lat_h").val(gpx.coords_lat_h);
    $("#lon_h").val(gpx.coords_lon_h);
    $("#lat_min").val(gpx.coords_lat_min);
    $("#lon_min").val(gpx.coords_lon_min);
    $("#name").val(gpx.name);
    tinyMCE.activeEditor.setContent(gpx.desc);
    $("#desc").val(gpx.desc);
    $("#hiddenDatePicker").val(CacheHidedate);
    checkRegion();
}

function chkregion() {
    if ($('#region').val() == "0") {
        alert("Proszę wybrać region");
        return false;
    }
    return true;
}


function chkcountry2(){
    $('#region1').hide();
    $('#regionAjaxLoader').show();
    request = $.ajax({
        url: "ajaxGetRegionsByCountryCode.php",
        type: "post",
        data:{countryCode: $('#country').val(), selectedRegion: '{sel_region}' },
    });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR){
        $('#region1').html(response);
        //console.log(response);
    });

    request.always(function () {
        $('#regionAjaxLoader').hide();
        $('#region1').fadeIn(1000);
    });
}

function _chkVirtual ()
{
chkiconcache();
    // disable password for traditional cache
    if($('#cacheType').val() == "2")
    {
        $('#log_pw').attr('disabled', true);
    }
    else
    {
        $('#log_pw').removeAttr('disabled');

    }

  if ($('#cacheType').val() == "4" || $('#cacheType').val() == "5" || $('#cacheType').val() == "6" )
    {

        // if( document.newcacheform.size.options[ $('#size option').length - 1].value != "7" && document.newcacheform.size.options[document.newcacheform.size.options.length - 2].value != "7")
        if (!($("#size option[value='7']").length > 0))
        {
            var o = new Option("{{size_07}}", "7");
            $(o).html("{{size_07}}");
            $("#size").append(o);
        }
        $('#size').val(7);
        $('#size').attr('disabled', true);
  }
  else
  {
    $('#size').attr('disabled', false);
    $("#size option[value='7']").remove();
  }
  return false;
}

function rebuildCacheAttr()
{
    var i = 0;
    var sAttr = '';

    for (i = 0; i < maAttributes.length; i++)
    {
        if (maAttributes[i][1] == 1)
        {
            if (sAttr != '') sAttr += ';';
            sAttr = sAttr + maAttributes[i][0];

            document.getElementById('attr' + maAttributes[i][0]).src = maAttributes[i][3];
        }
        else
            document.getElementById('attr' + maAttributes[i][0]).src = maAttributes[i][2];

        document.getElementById('cache_attribs').value = sAttr;

    }
}

function chkcountry()
{

if (document.newcacheform.country.value !='PL')
{
document.forms['newcacheform'].country.value = document.newcacheform.country.value;

// var as = $('#qwertyuiop').val();
// alert(as);
$('#region0').hide();
$('#region1').hide();
$('#region2').hide();
$('#region3').hide();
$('#region1').val(0);

document.forms['newcacheform'].region.value = '0';
document.newcacheform.region.disable = true;
 } else {
$('#region0').show();
$('#region1').show();
$('#region2').show();
$('#region3').show();
document.forms['newcacheform'].country.value = 'PL';
//document.newcacheform.region.options[document.newcacheform.region.options.length] = new Option('--- Select name of region ---', '0')
document.newcacheform.region.disable = false;
document.forms['newcacheform'].region.value = document.newcacheform.region.value;}
}

function chkiconcache()
    {
    var mode = $('#cacheType').val(); // document.newcacheform.type.value;
    var iconarray = new Array();
        iconarray['-1'] = 'arrow_left.png';
        iconarray['1'] = 'unknown.png';
        iconarray['2'] = 'traditional.png';
        iconarray['3'] = 'multi.png';
        iconarray['4'] = 'virtual.png';
        iconarray['5'] = 'webcam.png';
        iconarray['6'] = 'event.png';
        iconarray['7'] = 'quiz.png';
        iconarray['8'] = 'moving.png';
        iconarray['10'] = 'owncache.png';
    var image_cache = "/tpl/stdstyle/images/cache/" + iconarray[mode];
    $('#actionicons').attr('src', image_cache);
}

function toggleAttr(id)
{ // same func in newcache.tpl.php and editcache.tpl.php
    var i = 0;
    var answ =''; var bike_id=''; var walk_id=''; var boat_id='';
    if (id ==85 || id ==84 || id == 86)
    { //toggle contradictory attribs
        for (i = 0; i < maAttributes.length; i++) //finding id of bike and walk_only attributes
        {
            if (maAttributes[i][0]==84)  {walk_id = i;};
            if (maAttributes[i][0]==85)  {bike_id = i;};
            if (maAttributes[i][0]==86)  {boat_id = i;};
            if ((bike_id!='') && (walk_id!='') && (boat_id!='')) {break;};
        };

        if ((id == 84) && (maAttributes[walk_id][1] == 0) && ((maAttributes[bike_id][1] == 1) || (maAttributes[boat_id][1] == 1))) {
            //request confirmation if bike or boat is set and attemting to set Walk_only
            answ = confirm('{{ec_bike_set_msg}}');
            if (answ == false) { return false;  };
            maAttributes[bike_id][1] =0;
            maAttributes[boat_id][1] =0;
        };
        if ((id == 85) && (maAttributes[bike_id][1] == 0) && ((maAttributes[walk_id][1] == 1) || (maAttributes[boat_id][1] == 1))) {
            //request confirmation if Walk or boat_only is set and attemting to set Bike
            answ = confirm('{{ec_walk_set_msg}}');
            if (answ == false) { return false;  };
            maAttributes[walk_id][1] =0;
            maAttributes[boat_id][1] =0;
        };
        if ((id == 86) && (maAttributes[boat_id][1] == 0) && ((maAttributes[walk_id][1] == 1) || (maAttributes[bike_id][1] == 1))) {
            //request confirmation if bike or boat_only is set and attemting to set Boat
            answ = confirm('{{ec_boat_set_msg}}');
            if (answ == false) { return false;  };
            maAttributes[bike_id][1] =0;
            maAttributes[walk_id][1] =0;
        };

    //alert(id);
    };
    for (i = 0; i < maAttributes.length; i++)
    {
        if (maAttributes[i][0] == id)
        {

            if (maAttributes[i][1] == 0)
                maAttributes[i][1] = 1;
            else
                maAttributes[i][1] = 0;

            rebuildCacheAttr();
            break;
        }
    }
}


function count(mixed_var, mode) {
  // discuss at: http://phpjs.org/functions/count/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // input by: Waldo Malqui Silva
  // input by: merabi
  // bugfixed by: Soren Hansen
  // bugfixed by: Olivier Louvignes (http://mg-crea.com/)
  // improved by: Brett Zamir (http://brett-zamir.me)
  // example 1: count([[0,0],[0,-4]], 'COUNT_RECURSIVE');
  // returns 1: 6
  // example 2: count({'one' : [1,2,3,4,5]}, 'COUNT_RECURSIVE');
  // returns 2: 6
  var key, cnt = 0;
  if (mixed_var === null || typeof mixed_var === 'undefined') {
    return 0;
  } else if (mixed_var.constructor !== Array && mixed_var.constructor !== Object) {
    return 1;
  }
  if (mode === 'COUNT_RECURSIVE') {
    mode = 1;
  }
  if (mode != 1) {
    mode = 0;
  }
  for (key in mixed_var) {
    if (mixed_var.hasOwnProperty(key)) {
      cnt++;
      if (mode == 1 && mixed_var[key] && (mixed_var[key].constructor === Array || mixed_var[key].constructor ===
        Object)) {
        cnt += this.count(mixed_var[key], 1);
      }
    }
  }
  return cnt;
}


//-->
</script>
<script type="text/javascript"><!--
function nearbycache()
{
        var latNS = document.forms['newcacheform'].latNS.value;
        var lat_h = document.forms['newcacheform'].lat_h.value;
        var lat_min = document.forms['newcacheform'].lat_min.value;
        var lonEW = document.forms['newcacheform'].lonEW.value;
        var lon_h = document.forms['newcacheform'].lon_h.value;
        var lon_min = document.forms['newcacheform'].lon_min.value;
                if (document.newcacheform.lat_h.value == "0" && document.newcacheform.lon_h.value == "0" ) {
        alert("{{input_coord}}");
            } else {
    window.open('/search.php?searchto=searchbydistance&showresult=1&expert=0&output=HTML&sort=bydistance&f_userowner=0&f_userfound=0&f_inactive=0&distance=0.3&unit=km&latNS=' + latNS + '&lat_h=' + lat_h + '&lat_min=' + lat_min + '&lonEW=' + lonEW + '&lon_h=' + lon_h + '&lon_min=' + lon_min);
    }
    return false;
}
function extractregion()
{
        var latNS = document.forms['newcacheform'].latNS.value;
        var lat_h = document.forms['newcacheform'].lat_h.value;
        var lat_min = document.forms['newcacheform'].lat_min.value;
        var lat ;
        lat=(lat_h*1)+(lat_min/60);
        if (latNS=="S") lat=-lat;
        var lonEW = document.forms['newcacheform'].lonEW.value;
        var lon_h = document.forms['newcacheform'].lon_h.value;
        var lon_min = document.forms['newcacheform'].lon_min.value;
        var lon ;
        lon=(lon_h*1)+(lon_min/60);
            if (lonEW=="W") lon=-lon;
        if (document.newcacheform.lat_h.value == "0" && document.newcacheform.lon_h.value == "0" ) {
        alert("{{input_coord}}");
            } else {
    window.open('/region.php?lat=' + lat + '&lon=' + lon+ '&popup=y','Region','width=300,height=250');
    }
    return false;
}

//--></script>
<script type="text/javascript"><!--
function nearbycachemapOC()
{
        var lat_h = document.forms['newcacheform'].lat_h.value;
        var latNS = document.forms['newcacheform'].latNS.value;
        var lat_min = document.forms['newcacheform'].lat_min.value;
        var lat ;
        lat=(lat_h*1)+(lat_min/60);
        if (latNS=="S") lat=-lat;
        var lon_h = document.forms['newcacheform'].lon_h.value;
        var lonEW = document.forms['newcacheform'].lonEW.value;
        var lon_min = document.forms['newcacheform'].lon_min.value;
        var lon ;
        lon=(lon_h*1)+(lon_min/60);
            if (lonEW=="W") lon=-lon;
                if (document.newcacheform.lat_h.value == "0" && document.newcacheform.lon_h.value == "0" ) {
        alert("{{input_coord}}");
            } else {
        window.open('/cachemap3.php?circle=1&inputZoom=17&lat=' + lat + '&lon=' + lon);}
    return false;
}//--></script>


<style>
    #oldIE {
    color: red;
    border: solid 1px;
    border-color: red;
    padding: 10px;
    width:90%;
    }

    #hiddenDatePicker, #activateDatePicker{
        width: 75px;
    }

</style>
<!--[if IE 6 ]> <div id="oldIE">{{pt129}}</div><br/><br/> <![endif]-->
<!--[if IE 7 ]> <div id="oldIE">{{pt129}}</div><br/><br/> <![endif]-->
<!--[if IE 8 ]> <div id="oldIE">{{pt129}}</div><br/><br/> <![endif]-->


<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="" title="{{new_cache}}" align="middle"/>&nbsp;{{new_cache}}</div>
    {general_message}
    <div class="buffer"></div>
    <div class="content2-container bg-blue02" >
            <p class="content-title-noshade-size1"><img src="tpl/stdstyle/images/blue/basic2.png" class="icon32" alt=""/>&nbsp;{{basic_information}}</p>
    </div>

    <div class="buffer"></div>
    <div class="notice">
        {{first_cache}}.
    </div>
    {approvement_note}
    <div class="buffer"></div>
    <table class="table" border="0">
    <colgroup>
        <col width="180"/>
        <col/>
    </colgroup>
    <tr>
        <td>
            <p class="content-title-noshade">{{newcache_import_wpt}}</p>
        </td>
        <td>
        <div id="wptInfo" style="display: none; color: #006600; font-weight: bold;">{{newcache_import_wpt_ok}}</div>
        <form action="newcacheAjaxWaypointUploader.php" method="post" enctype="multipart/form-data" target="upload_target" onsubmit="startUpload();" >
            <p id="f1_upload_form"><br/>
             <input name="myfile" type="file" size="30" />
            <input type="submit" value="{{newcache_upload}}"/>
            </p>
            <iframe id="upload_target" name="upload_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>
        </form>
        <img style="display: none" id="ajaxLoaderLogo" src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ptPreloader.gif" />
        </td>
    </tr>
    <tr><td>&nbsp;</td>
        <td><div class="notice" style="width:500px;height:60px;">{{newcache_import_wpt_help}}</div>
        </td>
    </tr>

<form action="newcache.php" method="post" enctype="application/x-www-form-urlencoded" name="newcacheform" dir="ltr" onsubmit="javascript: return chkregion()">
<input type="hidden" name="show_all_countries" value="{show_all_countries}"/>
<input type="hidden" name="show_all_langs" value="{show_all_langs}"/>
<input type="hidden" name="version2" value="1"/>
<input type="hidden" name="beginner" value="0"/>
<input type="hidden" name="newcache_info" value="0"/>
<input type="hidden" id="cache_attribs" name="cache_attribs" value="{cache_attribs}" />
<input id="descMode" type="hidden" name="descMode" value="1" />

    <tr>
        <td><p class="content-title-noshade">{{name_label}}:</p></td>
        <td><input type="text" name="name" id="name" value="{name}" maxlength="60" class="input400"/>{name_message}</td>
    </tr>
    <tr><td class="buffer" colspan="2"></td></tr>
    <tr>
        <td><p class="content-title-noshade">{{cache_type}}:</p></td>
        <td>
            <select name="type" id="cacheType" class="input200" onchange="return _chkVirtual()">
                {typeoptions}
            </select>&nbsp;&nbsp;<img id="actionicons" name="actionicon" src="" align="top" alt="">{type_message}
        </td>
    </tr>
        <tr><td>&nbsp;</td>
        <td><div class="notice" style="width:500px;height:44px;">{{read_info_about_cache_types}}</div>
        </td></tr>
    <tr>
        <td><p class="content-title-noshade">{{cache_size}}:</p></td>
        <td>
            <select name="size" id="size" class="input200" onchange="return _chkVirtual()" {is_disabled_size}>
                {sizeoptions}
            </select>{size_message}
        </td>
    </tr>
    <tr><td colspan="2"><div class="buffer"></div></td></tr>
    <tr>
        <td valign="top"><p class="content-title-noshade">{{coordinates}}:</p></td>
        <td class="content-title-noshade">
        <fieldset style="border: 1px solid black; width: 80%; height: 32%; background-color: #FAFBDF;">
            <legend>&nbsp; <strong>WGS-84</strong> &nbsp;</legend>&nbsp;&nbsp;&nbsp;
            <select name="latNS" id="latNS" class="input40">
                <option value="N"{latNsel}>N</option>
                <option value="S"{latSsel}>S</option>
            </select>
            &nbsp;<input type="text" id="lat_h"  name="lat_h" maxlength="2" value="{lat_h}" class="input30" onchange="checkRegion()"/>
            &deg;&nbsp;<input type="text" id="lat_min" name="lat_min" maxlength="6" value="{lat_min}" class="input50" onkeyup="this.value=this.value.replace( /,/g,'.' );" onchange="checkRegion()" />&nbsp;'&nbsp;
            <button onclick="return nearbycachemapOC()">{{check_nearby_caches_map}}</button>
            {lat_message}<br />
            &nbsp;&nbsp;&nbsp;
            <select name="lonEW" id="lonEW" class="input40">
                <option value="W"{lonWsel}>W</option>
                <option value="E"{lonEsel}>E</option>
            </select>
            &nbsp;<input type="text" id="lon_h" name="lon_h" maxlength="3" value="{lon_h}" class="input30" onchange="checkRegion()" />
            &deg;&nbsp;<input type="text" id="lon_min" name="lon_min" maxlength="6" value="{lon_min}" class="input50" onkeyup="this.value=this.value.replace( /,/g,'.' );" onchange="checkRegion()" />&nbsp;'&nbsp;
            <button onclick="return nearbycache()">{{check_nearby_caches}}</button><br />
             {lon_message}</fieldset>
        </td>
    </tr>
    <tr><td>&nbsp;</td>
        <td><div class="notice" style="width:500px;height:44px;">{{check_nearby_caches_info}}</div>
        </td></tr>
    <tr>
        <td><p class="content-title-noshade">{{country_label}}:</p></td>
        <td>
            <select name="country"  id="country" class="input200" onchange="javascript:chkcountry2()">
                {countryoptions}
            </select>
            {show_all_countries_submit}
        </td>
    </tr>
    <tr><td colspan="2"><div class="buffer"></div></td></tr>
    <tr>
        <td><p id="region0" class="content-title-noshade">{{regiononly}}:</p></td>
        <td>
            <!-- <select name="region" id="region1" class="input200" onchange="javascript:chkcountry()" > --></select>
            <select name="region" id="region1" class="input200" >

            </select>&nbsp;&nbsp;<img src="tpl/stdstyle/images/free_icons/help.png" class="icon16" id="region2" alt=""/>&nbsp;<button id="region3" onclick="return extractregion()">{{region_from_coord}}</button>
            <img id="regionAjaxLoader" style="display: none" src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ptPreloader.gif" />
            {region_message}
        </td>
    </tr>
    <tr><td colspan="2"><div class="buffer"></div></td></tr>
    <tr><td><p class="content-title-noshade">{{difficulty_level}}:</p></td>
        <td>
            {{task_difficulty}}:
            <select name="difficulty" class="input60">
                {difficulty_options}
            </select>&nbsp;&nbsp;
            {{terrain_difficulty}}:
            <select name="terrain" class="input60">
                {terrain_options}
            </select>{diff_message}
        </td>
    </tr>
        <tr>
        <td>&nbsp;</td>
        <td><div class="notice" style="width:500px;height:44px;">{{difficulty_problem}} <a href="rating-c.php" target="_BLANK">{{rating_system}}</a>.</div>
        </td>
    </tr>
    <tr><td><p class="content-title-noshade">{{additional_information}} ({{optional}}):</p></td>
        <td>
                {{time}}:
                <input type="text" name="search_time" maxlength="10" value="{search_time}" class="input30" /> h
                &nbsp;&nbsp;
                {{length}}:
                <input type="text" name="way_length" maxlength="10" value="{way_length}" class="input30" /> km &nbsp; {effort_message}
            </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td><div class="notice" style="width:500px;height:44px">{{time_distance_hint}}</div><div class="buffer"></div></td>
    </tr>
    <tr>
        <td><p class="content-title-noshade">{{waypoint}} ({{optional}}):</p></td>
        <td>
            Geocaching.com: &nbsp;&nbsp;<input type="text" name="wp_gc" value="{wp_gc}" maxlength="7" class="input50"/>
            Navicache.com: <input type="text" name="wp_nc" value="{wp_nc}" maxlength="6" class="input50"/><br/>
            OpenCaching.com: <input type="text" name="wp_tc" value="{wp_tc}" maxlength="7" class="input50"/>
            GPSGames.org: <input type="text" name="wp_ge" value="{wp_ge}" maxlength="6" class="input50"/>

        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td><div class="notice" style="width:500px;height:44px;">{{waypoint_other_portal_info}}</div><div class="buffer"></div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="content2-container bg-blue02">
            <p class="content-title-noshade-size1"><img src="tpl/stdstyle/images/blue/attributes.png" class="icon32" alt=""/>&nbsp;{{cache_attributes}}</p>
        </div>
        </td>
    </tr>
    <tr><td class="buffer" colspan="2"></td></tr>
    <tr>
        <td colspan="2">{cache_attrib_list}</td>
    </tr>
    <tr><td colspan="2"><div class="buffer"></div></td></tr>
    <tr>
        <td colspan="2"><div class="notice" style="width:500px;min-height:24px;height:auto;white-space: nowrap;">{{attributes_edit_hint}} {{attributes_desc_hint}}</div></td></tr>
    <tr>
        <td colspan="2"><div class="content2-container bg-blue02">
            <p class="content-title-noshade-size1"><img src="tpl/stdstyle/images/blue/describe.png" class="icon32" alt=""/>&nbsp;{{descriptions}}</p>
            </div>
            </td>
    </tr>
    <tr><td colspan="2"><div class="buffer"></div></td></tr>
    <tr>
        <td><p class="content-title-noshade">{{language}}:</p></td>
        <td>
            <select name="desc_lang" class="input200">
                {langoptions}
            </select>
            {show_all_langs_submit}
        </td>
    </tr>
    <tr><td colspan="2"><div class="notice" style="width:500px;height:44px;">{{other_languages_desc}}</div></td></tr>
    <tr>
        <td><p class="content-title-noshade">{{short_desc_label}}:</p></td>
        <td><input type="text" name="short_desc" maxlength="120" value="{short_desc}" class="input400"/></td>
    </tr>
    <tr>
        <td colspan="2">
            <br />
                <p class="content-title-noshade">{{full_description}}:</p>
            <br/>
            {desc_message}
        </td>
    </tr>

    <tr>
        <td colspan="2">
            <span id="scriptwarning" class="errormsg">{{no_javascript}}</span>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <textarea id="desc" name="desc" class="descMCE">{desc}</textarea>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <div class="notice" style="width:500px;min-height:24px;height:auto;"><b><i>{{mandatory_field}}.</i></b> {{full_desc_long_text}} {{description_hint}} {{html_usage}} <a href="articles.php?page=htmltags" target="_BLANK">{{available_html}}</a></div>
        </td>
    </tr>
    <tr><td colspan="2"><div class="buffer"></div></td></tr>
    <tr>
        <td colspan="2"><div class="notice" style="width:500px;min-height:24px;height:auto;">{{additional_enc_info}}</div></td>
    </tr>
    <tr>
        <td colspan="2">
            <textarea style="display: none" name="hintaa" id="hintaa"></textarea>
            <textarea name="hints" id="hints" style="width: 500px; height: 80px;" >{hints}</textarea><br /><br />
        </td>
    </tr>
    <tr>
        <td colspan="2"><div class="content2-container bg-blue02">

            <p class="content-title-noshade-size1"><img src="tpl/stdstyle/images/blue/crypt.png" class="icon32" alt=""/>
                {{other}}
            </p>
            </div>
        </td>
    </tr>
    <tr><td colspan="2"><div class="buffer"></div></td></tr>
    <tr><td colspan="2"><div class="notice" style="width:500px;height:24px;">{{add_photo_newcache}}</div></td></tr>
    <tr>
        <td colspan="2">
        <fieldset style="border: 1px solid black; width: 80%; height: 32%; background-color: #FFFFFF;">
            <legend>&nbsp; <strong>{{date_hidden_label}}</strong> &nbsp;</legend>
            <input type="text" id="hiddenDatePicker" id="hiddenDatePicker" value="{hidden_year}-{hidden_month}-{hidden_day}" onchange="hiddenDatePickerChange('hidden');"/>
            <input type="hidden" name="hidden_year"  id="hidden_year"  maxlength="4" value="{hidden_year}"/>
            <input type="hidden" name="hidden_month" id="hidden_month" maxlength="2" value="{hidden_month}"/>
            <input type="hidden" name="hidden_day"   id="hidden_day"   maxlength="2" value="{hidden_day}"/>
            {hidden_since_message}
        </fieldset>
        </td>
    </tr>
    <tr><td colspan="2"><div class="notice buffer" style="width:500px;height:24px;">{{event_hidden_hint}}</div></td></tr>
    {hide_publish_start}
    <tr>
        <td colspan="2">
        <fieldset style="border: 1px solid black; width: 80%; height: 32%; background-color: #FFFFFF;">
            <legend>&nbsp; <strong>{{submit_new_cache}}</strong> &nbsp;</legend>
            <input type="radio" class="radio" name="publish" id="publish_now" value="now" {publish_now_checked}/>&nbsp;<label for="publish_now">{{publish_now}}</label><br />
            <input type="radio" class="radio" name="publish" id="publish_later" value="later" {publish_later_checked}/>&nbsp;<label for="publish_later">{{publish_date}}:</label>
            <input type="text" id="activateDatePicker" id="activateDatePicker" value="{activate_year}-{activate_month}-{activate_day}" onchange="hiddenDatePickerChange('activate');"/>
            <input class="input40" type="hidden" name="activate_year"  id="activate_year"  maxlength="4" value="{activate_year}"/>
            <input class="input20" type="hidden" name="activate_month" id="activate_month" maxlength="2" value="{activate_month}"/>
            <input class="input20" type="hidden" name="activate_day"   id="activate_day"   maxlength="2" value="{activate_day}"/>&nbsp;
            <select name="activate_hour" class="input40">{activation_hours}
            </select>&nbsp;{{hour}}&nbsp;{activate_on_message}<br />
            <input type="radio" class="radio" name="publish" id="publish_notnow" value="notnow" {publish_notnow_checked}/>&nbsp;<label for="publish_notnow">{{dont_publish_yet}}</label>
        </fieldset>
        </td>
    </tr>
    {hide_publish_end}
    <tr>

        <td colspan="2"><br />
        <fieldset style="border: 1px solid black; width: 80%; height: 32%; background-color: #FFFFFF;">
        <legend>&nbsp; <strong>{{log_password}}</strong> &nbsp;</legend>
        <input class="input100" type="text" name="log_pw" id="log_pw" value="{log_pw}" maxlength="20"/> ({{no_password_label}})
        </fieldset>
        </td>
    </tr>
    <tr><td colspan="2"><div class="notice buffer" style="width:500px;height:24px;">{{please_read}}</div></td></tr>
    <tr><td colspan="2"><div class="errormsg"><br />{{creating_cache}}<br /><br /></div></td></tr>
    <tr>
        <td colspan="2">
        <button type="submit" name="submitform" value="{submit}" style="font-size:14px;width:160px"><b>{submit}</b></button>

        <br /><br /></td>
    </tr>
</table>
</form>

<input type="hidden" value="" id="gpxWaypointObject">
<div id="waypointsToChose" title="{{newCacheWpTitle}}"></div>



