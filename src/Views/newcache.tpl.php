<?php

use src\Controllers\GpxLoadApiController;
use src\Controllers\MainMapController;
use src\Models\GeoCache\GeoCacheCommons;
use src\Models\OcConfig\OcConfig;
use src\Utils\Uri\SimpleRouter;

$view->callChunk('tinyMCE');
?>

<script>

    $(function() {
        $("#waypointsToChose").dialog({
            position: { my: "top+150", at: "top", of: window },
            autoOpen: false,
            width: $(window).width() > 800 ? 800 : $(window).width() * 0.9,
            modal: true,
            show: {effect: 'bounce', duration: 350, /* SPECIF ARGUMENT */ times: 3},
            hide: "explode",
            buttons: {
              '<?= tr('newCacheWpClose'); ?>': function() {
                $(this).dialog("close");
              }
            }
        });

        $("#gpxFormatInfo").dialog({
            position: { my: "top", at: "top", of: window },
            autoOpen: false,
            width: 800,
            minHeight: 800,
            modal: true,
            hide: "explode",
            show: "fade",
            title: "<?= tr('gpx_info_title'); ?>",
            buttons: {
              '<?= tr('newCacheWpClose'); ?>': function() {
                $(this).dialog("close");
              }
            }
        });
        $("#showGpxFormatInfo").click(function(evt) {
            $('#gpxFormatInfo').dialog("option", "height", window.innerHeight);
            $('#gpxFormatInfo').dialog('open');
            $(".ui-dialog-titlebar-close").hide();
        });

        $("#gpxUpload").click(function(evt) {
            var fd = new FormData();
            var files = $('#myfile')[0].files;
            // Check file selected or not
            if (files.length > 0 ) {
                fd.append('myfile',files[0]);
                startUpload();
                $.ajax({
                    url: '<?= SimpleRouter::getLink(GpxLoadApiController::class, 'newCacheGpxLoad'); ?>',
                    type: 'post',
                    data: fd,
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        stopUpload(response);
                    }
               });
            }
        });
    });

    // data picker init
    $(function() {
      updateRegionsList();
      $.datepicker.setDefaults($.datepicker.regional['pl']);
      $('#hiddenDatePicker, #activateDatePicker').datepicker (
        $.datepicker.regional["{language4js}"]
      ).datepicker("option", "dateFormat", "yy-mm-dd").val();
    });

    function hiddenDatePickerChange(identifier){
        var dateTimeStr = $('#' + identifier + 'DatePicker').val();
        var dateArr = dateTimeStr.split("-");
        $("#" + identifier + "_year").val(dateArr[0]);
        $("#" + identifier + "_month").val(dateArr[1]);
        $("#" + identifier + "_day").val(dateArr[2]);
    }

    function selectPublishLater(){
        $("#publish_later").prop("checked", true);
    }

    function checkRegion(){
        console.log('checkRegion');

        if ($('#lat_h').val().length == 0 ||
            $('#lon_h').val().length == 0 ) {
          return;
        }

        var latmin = parseFloat($('#lat_min').val());
        if(isNaN(latmin)) {
          latmin = 0;
        }

        var lonmin = parseFloat($('#lon_min').val());
        if(isNaN(lonmin)) {
          lonmin = 0;
        }

        var lat = parseFloat($('#lat_h').val()) + latmin / 60;
        if ($('#latNS').val() == 'S') {
           lat = - lat;
        }

        var lon = parseFloat($('#lon_h').val()) + lonmin / 60;
        if ($('#lonEW').val() == 'W') {
          lon = - lon;
        }

        request = $.ajax({
          url: "/location/getRegionsByLocation/"+lat+'/'+lon,
          type: "get",
        });

        // callback handler that will be called on success
        request.done(function (response, textStatus, jqXHR){

          locationData = response.locationTable;
          if ( !locationData['code1'] ) {
            // unknown country
            return;
          }

          if ($('#country').val() == locationData['code1']) {
            // same country, update region
            $('#region1').val( locationData['code3'] );
          } else {
              // country changed
              $('#country').val( locationData['code1'] );
              updateRegionsList(locationData['code3']);
          }
        });

        request.always(function () { });
    }

    var maAttributes = new Array({jsattributes_array});

    function startUpload(){
      $('#ajaxLoaderLogo').show();
      return true;
    }

    var waypointsToChoose = [];

    function stopUpload(response){
        $('#ajaxLoaderLogo').hide();
        $('#wptInfo').html(response['status']['msg']);
        $('#wptInfo').removeClass('errormsg successmsg');
        if (response['status']['code'] == 0) {
            $('#wptInfo').addClass('successmsg');
        } else {
            $('#wptInfo').addClass('errormsg');
        }
        $('#wptInfo').show();
        $(function() {
            setTimeout(
                function() {
                    $('#wptInfo').fadeOut(1000);
                },
                5000
            );
        });
        if (
            response['status']['code'] == 0
            && typeof response['data'] != 'undefined'
        ) {
            var wpts = response['data'];
            var waypointsCount = count(wpts);

            if (waypointsCount == 1) {
                fillFormInputs(wpts[0]);
            } else if (waypointsCount > 1) {
                waypointsToChoose = wpts;
                var i = 0;
                var ct = '<?= tr('newCacheWpDesc'); ?><br/><br/>';
                wpts.forEach(function(wpt) {
                    ct += '<a href="javascript:void(0);" onclick="updateFromWaypoint(' + i + ')"><b>'
                        + wpt.name + '</b> - ' + wpt.coords_latNS + wpt.coords_lat_h + '°' + wpt.coords_lat_min + ' / '
                        + wpt.coords_lonEW + wpt.coords_lon_h + '°' + wpt.coords_lon_min + '<br />';
                    i++;
                });
                $('#waypointsToChose').html(ct);
                $('#waypointsToChose').dialog('open');
                $(".ui-dialog-titlebar-close").hide();
            }
        }

        return true;
    }

    function updateFromWaypoint(index){
        fillFormInputs(waypointsToChoose[index]);
        waypointsToChoose = [];
        $('#waypointsToChose').dialog("close");
        $('#wptInfo').show();
        $(function() {
            setTimeout(function() {
            $('#wptInfo').fadeOut(1000);
            }, 5000);
        });
    }

    function fillFormInputs(wpt){
        var cacheHideDate = wpt.time.substring(0, 10);
        $("#lat_h").val(wpt.coords_lat_h);
        $("#lon_h").val(wpt.coords_lon_h);
        $("#lat_min").val(wpt.coords_lat_min);
        $("#lon_min").val(wpt.coords_lon_min);
        $("#name").val(wpt.name);
        if (wpt["type"] > 0) {
            $("#cacheType option[value=" + wpt["type"] + "]").attr(
                "selected", "selected"
            );
        }
        if (wpt["size"] > 0) {
            $("#size option[value=" + wpt["size"] + "]").attr(
                "selected", "selected"
            );
        }
        if (wpt["difficulty"] > 0) {
            $("select[name=difficulty] option[value=" + (wpt["difficulty"] * 2) + "]").attr(
                "selected", "selected"
            );
        }
        if (wpt["terrain"] > 0) {
            $("select[name=terrain] option[value=" + (wpt["terrain"] * 2) + "]").attr(
                "selected", "selected"
            );
        }
        if (wpt["trip_time"] > 0) {
            trip_time =
                Math.trunc(wpt["trip_time"]) +
                ':' +
                Math.round((wpt["trip_time"] - Math.trunc(wpt["trip_time"])) * 60);
            $("input[name=search_time]").val(trip_time);
        }
        if (wpt["trip_distance"] > 0) {
            $("input[name=way_length]").val(wpt["trip_distance"]);
        }
        $("#hints").val(wpt["hint"]);
        $("input[name=short_desc]").val(wpt["short_desc"]);
        tinyMCE.activeEditor.setContent(wpt.desc);
        $("#desc").val(wpt.desc);
        $("#hiddenDatePicker").val(cacheHideDate);
        if ("wp_gc" in wpt && wpt["wp_gc"] !== undefined) {
            $("input[name=wp_gc]").val(wpt["wp_gc"]);
        }
        checkRegion();
    }

    function chkregion() {
        if ($('#region').val() == "0") {
          alert('<?= tr('newcache_pleaseSelectRegion'); ?>');
          return false;
        }
        return true;
    }

    function updateRegionsList(regionToSelect) {

      if (typeof(regionToSelect)==='undefined') {
        regionToSelect = null;
      }

        $('#region1').hide();
        $('#regionAjaxLoader').show();

        request = $.ajax({
            url: "/Location/getRegionsByCountryCodeAjax/"+$('#country').val(),
            type: "get",
        });

        // callback handler that will be called on success
        request.done(function (response, textStatus, jqXHR){
            var select = $('#region1');
            select.empty();
            if(response.regions.length == 0){
              select.append('<option value="-1" selected="selected">- ? -</option>');
              select.prop('disabled', 'disabled'); // disabled select is not submited!
              $('#hiddenRegion').removeAttr('disabled');
            } else {
                $('#hiddenRegion').prop('disabled', 'disabled');
                select.removeAttr('disabled');
                select.append('<option value="0" selected="selected"><?= tr('search01'); ?></option>');
                response.regions.forEach(function(element) {
                  if ( element.code == '{sel_region}') {
                    select.append('<option selected="selected" value="'+element.code+'">'+element.name+'</option>')
                  } else {
                    select.append('<option value="'+element.code+'">'+element.name+'</option>')
                  }
                });
            }
        });

        request.always(function () {
            $('#regionAjaxLoader').hide();
                $('#region1').fadeIn(1000);

                if(regionToSelect) {
                  $('#region1').val( regionToSelect );
                }
            });
    }

    function _chkVirtual () {
      chkiconcache();
      // disable password for traditional cache
      if ($('#cacheType').val() == "2") {
        $('#log_pw').attr('disabled', true);
      } else {
        $('#log_pw').removeAttr('disabled');
      }

      if ($('#cacheType').val() == "4" || $('#cacheType').val() == "5" || $('#cacheType').val() == "6") {
        // if( document.newcacheform.size.options[ $('#size option').length - 1].value != "7" && document.newcacheform.size.options[document.newcacheform.size.options.length - 2].value != "7")
        if (!($("#size option[value='7']").length > 0)) {
          var o = new Option("<?= tr('cacheSize_none'); ?>", "7");
          $(o).html("<?= tr('cacheSize_none'); ?>");
          $("#size").append(o);
        }

        $('#size').val(7);
        $('#size').attr('disabled', true);
      } else {
        $('#size').attr('disabled', false);
        $("#size option[value='7']").remove();
      }

      return false;
    }

    function rebuildCacheAttr() {
      var i = 0;
      var sAttr = '';
      for (i = 0; i < maAttributes.length; i++) {
        if (maAttributes[i][1] == 1) {
          if (sAttr != '') sAttr += ';';
          sAttr = sAttr + maAttributes[i][0];
          document.getElementById('attr' + maAttributes[i][0]).src = maAttributes[i][3];
        } else {
            document.getElementById('attr' + maAttributes[i][0]).src = maAttributes[i][2];
        }
        document.getElementById('cache_attribs').value = sAttr;
      }
    }

    function chkcountry() {
      if (document.newcacheform.country.value != 'PL') {
        document.forms['newcacheform'].country.value = document.newcacheform.country.value;
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
        document.forms['newcacheform'].region.value = document.newcacheform.region.value;
      }
    }

    function chkiconcache() {
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
        var image_cache = "/images/cache/" + iconarray[mode];
        $('#actionicons').attr('src', image_cache);
    }

    function toggleAttr(id) { // same func in newcache.tpl.php and editcache.tpl.php
      var i = 0;
        //            var answ = ''; var bike_id = ''; var walk_id = ''; var boat_id = '';
        //            if (id == 85 || id == 84 || id == 86)
        //    { //toggle contradictory attribs
        //    for (i = 0; i < maAttributes.length; i++) //finding id of bike and walk_only attributes
        //    {
        //    if (maAttributes[i][0] == 84)  {walk_id = i; };
        //            if (maAttributes[i][0] == 85)  {bike_id = i; };
        //            if (maAttributes[i][0] == 86)  {boat_id = i; };
        //            if ((bike_id != '') && (walk_id != '') && (boat_id != '')) {break; };
        //    };
        //            if ((id == 84) && (maAttributes[walk_id][1] == 0) && ((maAttributes[bike_id][1] == 1) || (maAttributes[boat_id][1] == 1))) {
        //    //request confirmation if bike or boat is set and attemting to set Walk_only
        //    answ = confirm('{{ec_bike_set_msg}}');
        //            if (answ == false) { return false; };
        //            maAttributes[bike_id][1] = 0;
        //            maAttributes[boat_id][1] = 0;
        //    };
        //            if ((id == 85) && (maAttributes[bike_id][1] == 0) && ((maAttributes[walk_id][1] == 1) || (maAttributes[boat_id][1] == 1))) {
        //    //request confirmation if Walk or boat_only is set and attemting to set Bike
        //    answ = confirm('{{ec_walk_set_msg}}');
        //            if (answ == false) { return false; };
        //            maAttributes[walk_id][1] = 0;
        //            maAttributes[boat_id][1] = 0;
        //    };
        //            if ((id == 86) && (maAttributes[boat_id][1] == 0) && ((maAttributes[walk_id][1] == 1) || (maAttributes[bike_id][1] == 1))) {
        //    //request confirmation if bike or boat_only is set and attemting to set Boat
        //    answ = confirm('{{ec_boat_set_msg}}');
        //            if (answ == false) { return false; };
        //            maAttributes[bike_id][1] = 0;
        //            maAttributes[walk_id][1] = 0;
        //    };
        //            //alert(id);
        //    };
        for (i = 0; i < maAttributes.length; i++) {
          if (maAttributes[i][0] == id) {
            if (maAttributes[i][1] == 0) {
                maAttributes[i][1] = 1;
            } else {
                maAttributes[i][1] = 0;
            }
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

    document.addEventListener("DOMContentLoaded", function () {
        const input = document.getElementById("myfile");
        if (input) {
            input.addEventListener("change", function () {
                if (input.files && input.files.length > 0) {
                    const uploadButton = document.getElementById("gpxUpload");
                    if (uploadButton) {
                        uploadButton.click();
                    }
                }
            });
        }
    });

</script>
<script>
    function nearbycache()
    {
    var latNS = document.forms['newcacheform'].latNS.value;
            var lat_h = document.forms['newcacheform'].lat_h.value;
            var lat_min = document.forms['newcacheform'].lat_min.value;
            var lonEW = document.forms['newcacheform'].lonEW.value;
            var lon_h = document.forms['newcacheform'].lon_h.value;
            var lon_min = document.forms['newcacheform'].lon_min.value;
            if (document.newcacheform.lat_h.value == "0" && document.newcacheform.lon_h.value == "0") {
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
            var lat;
            lat = (lat_h * 1) + (lat_min / 60);
            if (latNS == "S") lat = - lat;
            var lonEW = document.forms['newcacheform'].lonEW.value;
            var lon_h = document.forms['newcacheform'].lon_h.value;
            var lon_min = document.forms['newcacheform'].lon_min.value;
            var lon;
            lon = (lon_h * 1) + (lon_min / 60);
            if (lonEW == "W") lon = - lon;
            if (document.newcacheform.lat_h.value == "0" && document.newcacheform.lon_h.value == "0") {
    alert("{{input_coord}}");
    } else {
    window.open('/region.php?lat=' + lat + '&lon=' + lon + '&popup=y', 'Region', 'width=300,height=250');
    }
    return false;
    }

</script>
<script>
    function nearbycachemapOC()
    {
    var lat_h = document.forms['newcacheform'].lat_h.value;
            var latNS = document.forms['newcacheform'].latNS.value;
            var lat_min = document.forms['newcacheform'].lat_min.value;
            var lat;
            lat = (lat_h * 1) + (lat_min / 60);
            if (latNS == "S") lat = - lat;
            var lon_h = document.forms['newcacheform'].lon_h.value;
            var lonEW = document.forms['newcacheform'].lonEW.value;
            var lon_min = document.forms['newcacheform'].lon_min.value;
            var lon;
            lon = (lon_h * 1) + (lon_min / 60);
            if (lonEW == "W") lon = - lon;
            if (document.newcacheform.lat_h.value == "0" && document.newcacheform.lon_h.value == "0") {
    alert("{{input_coord}}");
    } else {
    window.open('<?= SimpleRouter::getLink(MainMapController::class, 'fullscreen'); ?>?circle&lat='+lat+'&lon='+lon); }
    return false;
    }
</script>
<script>
$(document).ready(function(){
    $('input').keyup(function(){
        if($(this).val().length==$(this).attr("maxlength")){
            $(this).next('[type="text"]').focus();
        }
    });
});
</script>
<style>
    #hiddenDatePicker, #activateDatePicker{
        width: 75px;
    }

</style>

<div class="content2-pagetitle"><img src="/images/blue/cache.png" class="icon32" alt="" title="{{new_cache}}">&nbsp;{{new_cache}}</div>
{general_message}
<div class="buffer"></div>
<div class="content2-container bg-blue02" >
    <p class="content-title-noshade-size1"><img src="/images/blue/basic2.png" class="icon32" alt=""/>&nbsp;{{basic_information}}</p>
</div>
<div class="notice">
    {{first_cache}}.
</div>
{approvement_note}
<table class="table">
    <tr class="form-group-sm">
        <td style="width: 180px">
            <p><br/></p>
            <p class="content-title-noshade">{{newcache_import_wpt}}</p>
        </td>
        <td>
            <div id="wptInfoCont">
              <span id="wptInfo" style="display: none;"></span>
            </div>
            <div class="form-inline">
                <?php $view->callChunk('fileUpload', 'myfile', '.gpx'); ?>
                <input id="gpxUpload" class="btn btn-primary btn-sm btn-upload" type="button" value="<?= tr('newcache_upload'); ?>"/>
                <img style="display: none" id="ajaxLoaderLogo" src="images/misc/ptPreloader.gif" alt="">
            </div>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>
            <div class="notice">{{newcache_import_wpt_help}}</div>
            <div class="notice">{{newcache_import_wpt_format_details_prompt}} <a id="showGpxFormatInfo" href="javascript:;">{{newcache_import_wpt_format_details_prompt_link}}</a></div>
        </td>
    </tr>

    <form action="newcache.php" method="post" enctype="application/x-www-form-urlencoded" name="newcacheform" dir="ltr" onsubmit="javascript: return chkregion()">
        <input type="hidden" name="show_all_countries" value="{show_all_countries}"/>
        <input type="hidden" name="show_all_langs" value="{show_all_langs}"/>
        <input type="hidden" name="version2" value="1"/>
        <input type="hidden" name="beginner" value="0"/>
        <input type="hidden" name="newcache_info" value="0"/>
        <input type="hidden" id="cache_attribs" name="cache_attribs" value="{cache_attribs}" />

        <tr class="form-group-sm">
            <td><p class="content-title-noshade">{{name_label}}:</p></td>
            <td><input type="text" name="name" id="name" value="{name}" maxlength="60" class="form-control input400"/>{name_message}</td>
        </tr>
        <tr><td class="buffer" colspan="2"></td></tr>
        <tr class="form-group-sm">
            <td><p class="content-title-noshade">{{cache_type}}:</p></td>
            <td>
                <select name="type" id="cacheType" class="form-control input200" onchange="_chkVirtual()">
                    {typeoptions}
                </select>&nbsp;&nbsp;<img id="actionicons" src="" align="top" alt="">{type_message}
            </td>
        </tr>
        <tr><td>&nbsp;</td>
            <td><div class="notice">{{read_info_about_cache_types}}</div>
            </td></tr>
        <tr class="form-group-sm">
            <td><p class="content-title-noshade">{{cache_size}}:</p></td>
            <td>
                <select name="size" id="size" class="form-control input200" onchange="_chkVirtual()" {is_disabled_size}>
                    {sizeoptions}
                </select>{size_message}
            </td>
        </tr>
        <tr><td colspan="2"><div class="buffer"></div></td></tr>
        <tr>
            <td valign="top"><p class="content-title-noshade">{{coordinates}}:</p></td>
            <td class="content-title-noshade">
                <fieldset style="border: 1px solid black; width: 90%; height: 32%; background-color: #FAFBDF;" class="form-group-sm">
                    <legend>&nbsp; <strong>WGS-84</strong> &nbsp;</legend>
                    <select name="latNS" id="latNS" class="form-control input50" onchange="checkRegion()">
                        <option value="N"{latNsel}>N</option>
                        <option value="S"{latSsel}>S</option>
                    </select>
                    &nbsp;
                  <input
                      type="number"
                      id="lat_h"
                      name="lat_h"
                      maxlength="2"
                      class="form-control input45"
                      onchange="checkRegion()"
                      placeholder="0"
                      value="{lat_h}"
                      min="0"
                      max="90"
                      required
                  />&deg;&nbsp;
                  <input
                      type="text"
                      id="lat_min"
                      name="lat_min"
                      maxlength="6"
                      class="form-control input50"
                      onkeyup="this.value = this.value.replace(/,/g, '.');"
                      onchange="checkRegion()"
                      placeholder="00.000"
                      value="{lat_min}"
                      pattern="\d{1,2}.\d{1,3}"
                      required
                  />&nbsp;'&nbsp;
                    <button class="btn btn-default btn-sm" onclick="nearbycachemapOC()">{{check_nearby_caches_map}}</button>
                    {lat_message}<br />
                    <select name="lonEW" id="lonEW" class="form-control input50" onchange="checkRegion()">
                        <option value="W"{lonWsel}>W</option>
                        <option value="E"{lonEsel}>E</option>
                    </select>
                    &nbsp;
                  <input
                      type="number"
                      id="lon_h"
                      name="lon_h"
                      maxlength="3"
                      class="form-control input45"
                      onchange="checkRegion()"
                      placeholder="0"
                      value="{lon_h}"
                      min="0"
                      max="180"
                      required
                  />&deg;&nbsp;
                  <input
                      type="text"
                      id="lon_min"
                      name="lon_min"
                      maxlength="6"
                      class="form-control input50"
                      onkeyup="this.value = this.value.replace(/,/g, '.');"
                      onchange="checkRegion()"
                      placeholder="00.000"
                      value="{lon_min}"
                      pattern="\d{1,2}.\d{1,3}"
                      required
                  />&nbsp;'&nbsp;
                    <button class="btn btn-default btn-sm" onclick="nearbycache()">{{check_nearby_caches}}</button><br />
                    {lon_message}</fieldset>
            </td>
        </tr>
        <tr><td>&nbsp;</td>
            <td><div class="notice">{{check_nearby_caches_info}}</div>
            </td></tr>
        <tr class="form-group-sm">
            <td><p class="content-title-noshade">{{country_label}}:</p></td>
            <td>
                <select name="country"  id="country" class="form-control input200" onchange="updateRegionsList()">
                    {countryoptions}
                </select>
                {show_all_countries_submit}
            </td>
        </tr>

        <tr class="form-group-sm">
            <td><p id="region0" class="content-title-noshade">{{regiononly}}:</p></td>
            <td>
                <input type="hidden" name="region" disabled="disabled" id="hiddenRegion" value="-1" />
                <select name="region" id="region1" class="form-control input200" >
                </select>&nbsp;<button class="btn btn-default btn-sm" id="region3" onclick="extractregion()">{{region_from_coord}}</button>
                <img id="regionAjaxLoader" style="display: none" src="images/misc/ptPreloader.gif" alt="">
                {region_message}
            </td>
        </tr>
        <tr><td colspan="2"><div class="buffer"></div></td></tr>
        <tr class="form-group-sm"><td><p class="content-title-noshade">{{difficulty_level}}:</p></td>
            <td>
                {{task_difficulty}}:
                <select name="difficulty" class="form-control input100">
                    {difficulty_options}
                </select>&nbsp;&nbsp;
                {{terrain_difficulty}}:
                <select name="terrain" class="form-control input100">
                    {terrain_options}
                </select>{diff_message}
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><div class="notice">{{difficulty_problem}} <a href="/Cache/difficultyForm" target="_BLANK">{{rating_system}}</a></div>
            </td>
        </tr>
        <tr class="form-group-sm"><td><p class="content-title-noshade">{{additional_information}} ({{optional}}):</p></td>
            <td>
                {{time}}:
                <input type="text" name="search_time" maxlength="10" value="{search_time}" class="form-control input50" /> h
                &nbsp;&nbsp;
                {{length}}:
                <input type="text" name="way_length" maxlength="10" value="{way_length}" class="form-control input30" /> km &nbsp; {effort_message}
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><div class="notice">{{time_distance_hint}}</div><div class="buffer"></div></td>
        </tr>
        <tr class="form-group-sm">
            <td><p class="content-title-noshade">{{foreign_waypoint}} ({{optional}}):</p></td>
            <td>
                <table class="table compact-horizontal">
                    <tr>
                        <td>Geocaching.com:</td>
                        <td><input type="text" name="wp_gc" value="{wp_gc}" maxlength="7" size="7" class="form-control input70 uppercase" onChange="yes_change();"/>&nbsp; &nbsp;</td>
                        <td>Navicache.com:&nbsp;</td>
                        <td><input type="text" name="wp_nc" value="{wp_nc}" maxlength="6" size="6" class="form-control input70 uppercase" onChange="yes_change();"/></td>
                    </tr>
                    <tr>
                        <td colspan="2">{wp_gc_message}</td>
                        <td colspan="2">{wp_nc_message}</td>
                    </tr>
                    <tr>
                        <td>Terracaching.com:&nbsp;</td>
                        <td><input type="text" name="wp_tc" value="{wp_tc}" maxlength="7" size="7" class="form-control input70 uppercase" onChange="yes_change();"/>&nbsp; &nbsp;</td>
                        <td>GPSGames.org:</td>
                        <td><input type="text" name="wp_ge" value="{wp_ge}" maxlength="6" size="6" class="form-control input70 uppercase" onChange="yes_change();"/></td>
                    </tr>
                    <tr>
                        <td colspan="2">{wp_tc_message}</td>
                        <td colspan="2">{wp_ge_message}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><div class="notice">{{foreign_waypoint_info}}</div><div class="buffer"></div></td>
        </tr>
        <tr>
            <td colspan="2"><div class="content2-container bg-blue02">
                    <p class="content-title-noshade-size1"><img src="/images/blue/attributes.png" class="icon32" alt=""/>&nbsp;{{cache_attributes}}</p>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">{cache_attrib_list}</td>
        </tr>
        <tr>
            <td colspan="2"><div class="notice">{{attributes_edit_hint}} {{attributes_desc_hint}}</div></td></tr>
        <tr>
            <td colspan="2"><div class="content2-container bg-blue02">
                    <p class="content-title-noshade-size1"><img src="/images/blue/describe.png" class="icon32" alt=""/>&nbsp;{{descriptions}}</p>
                </div>
            </td>
        </tr>
        <tr class="form-group-sm">
            <td><p class="content-title-noshade">{{language}}:</p></td>
            <td>
                <select name="desc_lang" class="form-control input200">
                    {langoptions}
                </select>
                {show_all_langs_submit}
            </td>
        </tr>
        <tr><td colspan="2"><div class="notice">{{other_languages_desc}}</div></td></tr>
        <tr class="form-group-sm" >
            <td><p class="content-title-noshade">{{short_description}}:</p></td>
            <td><input type="text" name="short_desc" maxlength="120" value="{short_desc}" class="form-control input400"/></td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="notice">{{short_desc_long_text}}</div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <p class="content-title-noshade">{{full_description}}:</p>
                {desc_message}
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="content2-container"><textarea id="desc" name="desc" class="desc tinymce">{desc}</textarea></div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="notice"><b><i>{{mandatory_field}}.</i></b> {{full_desc_long_text}}</div>
                <div class="notice">{{html_usage}} <a href="articles.php?page=htmltags" target="_blank">{{available_html}}</a></div>
                <div class="notice">{{geocaching_com_foto_info}}</div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <p class="content-title-noshade">{{hint_info}}:</p>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="content2-container"><textarea name="hints" class="hint" id="hints">{hints}</textarea></div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="notice">{{hint_long_text}}</div>
                <div class="notice">{{hint_instructions}}</div>
            </td>
        </tr>

        <?php if (OcConfig::isReactivationRulesEnabled()) { ?>
          <tr><td colspan="2">
            <fieldset class="form-group-sm reactivationRules">
              <legend class="content-title-noshade"><?= tr('editDesc_reactivRulesLabel'); ?></legend>
              <p>
                <?= tr('editDesc_reactivRulesDesc'); ?>
                <div class="notice buffer"><?= tr('editDesc_reactivRulesMoreInfo'); ?></div>
              </p>

            <?php
            $reactivRuleChecked = false;
            $firstRuleId = false;

            foreach (OcConfig::getReactivationRulesPredefinedOpts() as $key => $opt) { ?>
            <?php
                $optTxt = tr($opt);
                $reactivRuleChecked = $reactivRuleChecked || $optTxt == $view->reactivRulesRadio; ?>
                <input type="radio" id="reactivRules<?= $key; ?>" name="reactivRules" value="<?= $optTxt; ?>"
                    <?= (! $firstRuleId ? ' required oninvalid="this.setCustomValidity(\'' . tr('editDesc_invalidRactivRule') . '\')" oninput="this.setCustomValidity(\'\')"' : ' oninput="document.getElementById(\'' . $firstRuleId . '\').setCustomValidity(\'\')"'); ?>
                    <?= ($optTxt == $view->reactivRulesRadio) ? 'checked' : ''; ?>>
                <label for="reactivRules<?= $key; ?>"><?= $optTxt; ?></label>
                <br/>
            <?php
                if (! $firstRuleId) {
                    $firstRuleId = 'reactivRules' . $key;
                }
            } // foreach - OcConfig::getReactivationRulesPredefinedOpts()?>

                <input type="radio" id="reactivRulesCustom" name="reactivRules" value="Custom rulset"
                    <?= (! $firstRuleId ? ' required oninvalid="this.setCustomValidity(\'' . tr('editDesc_invalidRactivRule') . '\')" oninput="this.setCustomValidity(\'\')"' : ' oninput="document.getElementById(\'' . $firstRuleId . '\').setCustomValidity(\'\')"'); ?>
                    <?= (! $reactivRuleChecked && ! empty($view->reactivRulesCustom)) ? 'checked' : ''; ?>>
              <label for="reactivRulesCustom"><?= tr('editDesc_reactivRuleCustomDefinition'); ?>:</label>

              <textarea placeholder="<?= tr('editDesc_reactivRuleCustomDefinition'); ?>" id="reactivRulesCustom"
                        class="customReactivation" name="reactivRulesCustom"
                        maxlength="1000"><?= $view->reactivRulesCustom; ?></textarea>
            </fieldset>
          </td></tr>
        <?php } // if-OcConfig::isReactivationRulesEnabled()?>

        <tr>
            <td colspan="2"><div class="content2-container bg-blue02">
                    <p class="content-title-noshade-size1"><img src="/images/blue/crypt.png" class="icon32" alt=""/>
                        {{other}}
                    </p>
                </div>
            </td>
        </tr>
        <tr><td colspan="2"><div class="notice">{{add_photo_newcache}}</div></td></tr>
        <tr class="form-group-sm">
            <td colspan="2">
                <fieldset style="border: 1px solid black; width: 80%; height: 32%; background-color: #FFFFFF;">
                    <legend>&nbsp; <strong>{{date_hidden_label}}</strong> &nbsp;</legend>
                    <input type="text" class="form-control" id="hiddenDatePicker" value="{hidden_year}-{hidden_month}-{hidden_day}" onchange="hiddenDatePickerChange('hidden');"/>
                    <input type="hidden" name="hidden_year"  id="hidden_year" value="{hidden_year}"/>
                    <input type="hidden" name="hidden_month" id="hidden_month" value="{hidden_month}"/>
                    <input type="hidden" name="hidden_day"   id="hidden_day" value="{hidden_day}"/>
                    {hidden_since_message}
                </fieldset>
            </td>
        </tr>
        <tr><td colspan="2"><div class="notice buffer">{{event_hidden_hint}}</div></td></tr>
        {hide_publish_start}
        <tr class="form-group-sm">
            <td colspan="2">
                <fieldset style="border: 1px solid black; width: 80%; height: 32%; background-color: #FFFFFF;">
                    <legend>&nbsp; <strong>{{submit_new_cache}}</strong> &nbsp;</legend>
                    <input type="radio" class="radio" name="publish" id="publish_now" value="now" {publish_now_checked}/>&nbsp;<label for="publish_now">{{publish_now}}</label><br />
                    <input type="radio" class="radio" name="publish" id="publish_later" value="later" {publish_later_checked}/>&nbsp;<label for="publish_later">{{publish_date}}:</label>
                    <input type="text" class="form-control" id="activateDatePicker" value="{activate_year}-{activate_month}-{activate_day}" onchange="hiddenDatePickerChange('activate'); selectPublishLater();"/>
                    <input class="input40" type="hidden" name="activate_year"  id="activate_year"  value="{activate_year}"/>
                    <input class="input20" type="hidden" name="activate_month" id="activate_month" value="{activate_month}"/>
                    <input class="input20" type="hidden" name="activate_day"   id="activate_day"   value="{activate_day}"/>&nbsp;
                    <select name="activate_hour" class="form-control input70">{activation_hours}
                    </select>&nbsp;–&nbsp;{activate_on_message}<br />
                    <input type="radio" class="radio" name="publish" id="publish_notnow" value="notnow" {publish_notnow_checked}/>&nbsp;<label for="publish_notnow">{{dont_publish_yet}}</label>
                </fieldset>
            </td>
        </tr>
        {hide_publish_end}
        <tr class="form-group-sm">
            <td colspan="2">
                <fieldset style="border: 1px solid black; width: 80%; height: 32%; background-color: #FFFFFF;">
                    <legend>&nbsp; <strong>{{log_password}}</strong> &nbsp;</legend>
                    <input class="form-control input100" type="text" name="log_pw" id="log_pw" value="{log_pw}" maxlength="20"/> ({{no_password_label}})
                </fieldset>
            </td>
        </tr>
        <tr><td colspan="2"><div class="notice buffer">{{please_read}}</div></td></tr>
        <tr><td colspan="2"><div class="errormsg">{{creating_cache}}</div></td></tr>
        <tr>
            <td colspan="2"><div class="buffer"></div>
                <button type="submit" name="submitform" value="<?= tr('new_cache2'); ?>" class="btn btn-primary"><?= tr('new_cache2'); ?></button>
            </td>
        </tr>
</form>
</table>
<div class="buffer"></div>
<input type="hidden" value="" id="gpxWaypointObject">
<div id="waypointsToChose" title="{{newCacheWpTitle}}"></div>
<div id="gpxFormatInfo" class="hidden" style="overflow: auto;">
<p style="text-align: justify;">
<?= tr('gpx_info_intro'); ?>:
<ul>
<li><a href="http://www.topografix.com/GPX/1/0/gpx.xsd" title="Topografix 1.0">Topografix 1.0</a>,
<?= tr('gpx_info_in_namespace'); ?> <code>http://www.topografix.com/GPX/1/0</code>.</li>
<li><a href="http://www.topografix.com/GPX/1/0/gpx.xsd" title="Topografix 1.1">Topografix 1.1</a>,
<?= tr('gpx_info_in_namespace'); ?> <code>http://www.topografix.com/GPX/1/1</code>.</li>
</ul>
</p>
<br/>
<p style="text-align: justify;">
<?= tr('gpx_info_p1'); ?>:
</p>
<table class="bs-table" style="width: 95%;">
<colgroup>
<col style="width: 25%;"/>
<col/>
</colgroup>
<tr>
<td><code>lat</code></td><td><?= tr('gpx_info_lat'); ?></td>
</tr>
<tr>
<td><code>lon</code></td><td><?= tr('gpx_info_lon'); ?></td>
</tr>
</table>
<p style="text-align: justify;">
<?= tr('gpx_info_p2'); ?>:
</p>
<table class="bs-table" style="width: 95%;">
<colgroup>
<col style="width: 25%;"/>
<col/>
<tr>
<td><code>name</code></td><td><?= tr('name_label'); ?></td>
</tr>
<tr>
<td><code>time</code></td><td><?= tr('gpx_info_time_hidden_date_only'); ?></td>
</tr>
<tr>
<td><code>desc</code></td><td><?= tr('full_description'); ?></td>
</tr>
</table>
<p style="text-align: justify;">
<?= tr('gpx_info_p3'); ?>
</p>
<hr style="height: 1px; margin: 10px;"/>
<p style="text-align: justify;">
<?= tr('gpx_info_p4'); ?>:
</p>
<table class="bs-table" style="width: 95%;">
<colgroup>
<col style="width: 25%;"/>
<col/>
<tr>
<td><code>type</code></td><td><?= tr('cache_type'); ?></td>
</tr>
<tr>
<td><code>container</code></td><td><?= tr('cache_size'); ?></td>
</tr>
<tr>
<td><code>difficulty</code></td><td><?= tr('task_difficulty'); ?></td>
</tr>
<tr>
<td><code>terrain</code></td><td><?= tr('terrain_difficulty'); ?></td>
</tr>
<tr>
<td><code>encoded_hints</code></td><td><?= tr('hint_info'); ?></td>
</tr>
<tr>
<td><code>long_description</code></td><td><?= tr('full_description'); ?></td>
</tr>
<tr>
<td><code>short_description</code></td><td><?= tr('short_description'); ?></td>
</tr>
</table>
<p style="text-align: justify;">
<?= tr('gpx_info_p5'); ?>
</p>
<p style="text-align: justify;">
<?= tr('gpx_info_p6'); ?>:
</p>
<table class="bs-table" style="width: 95%;">
<colgroup>
<col style="width: 25%;"/>
<col/>
<tr>
<td><code>traditional cache</code></td><td><?= tr(GeoCacheCommons::TYPE_TRADITIONAL_TR_KEY); ?></td>
</tr>
<tr>
<td><code>multi-cache</code></td><td><?= tr(GeoCacheCommons::TYPE_MULTICACHE_TR_KEY); ?></td>
</tr>
<tr>
<td><code>unknown cache</code></td><td><?= tr(GeoCacheCommons::TYPE_QUIZ_TR_KEY); ?></td>
</tr>
<tr>
<td><code>virtual cache</code></td><td><?= tr(GeoCacheCommons::TYPE_VIRTUAL_TR_KEY); ?></td>
</tr>
<tr>
<td><code>webcam cache</code></td><td><?= tr(GeoCacheCommons::TYPE_WEBCAM_TR_KEY); ?></td>
</tr>
<tr>
<td><code>letterbox hybrid</code></td><td><?= tr(GeoCacheCommons::TYPE_OTHERTYPE_TR_KEY); ?></td>
</tr>
<tr>
<td><code>earthcache</code></td><td><?= tr(GeoCacheCommons::TYPE_OTHERTYPE_TR_KEY); ?></td>
</tr>
<tr>
<td><code>wherigo cache</code></td><td><?= tr(GeoCacheCommons::TYPE_OTHERTYPE_TR_KEY); ?></td>
</tr>
<tr>
<td><code>event cache</code></td><td><?= tr(GeoCacheCommons::TYPE_EVENT_TR_KEY); ?></td>
</tr>
<tr>
<td><code>cache in trash out event</code></td><td><?= tr(GeoCacheCommons::TYPE_EVENT_TR_KEY); ?></td>
</tr>
<tr>
<td><code>mega-event cache</code></td><td><?= tr(GeoCacheCommons::TYPE_EVENT_TR_KEY); ?></td>
</tr>
<tr>
<td><code>giga-event cache</code></td><td><?= tr(GeoCacheCommons::TYPE_EVENT_TR_KEY); ?></td>
</tr>
</table>
<br/>
<p style="text-align: justify;">
<?= tr('gpx_info_p7'); ?>:
</p>
<table class="bs-table" style="width: 95%;">
<colgroup>
<col style="width: 25%;"/>
<col/>
<tr>
<td><code>micro</code></td><td><?= tr(GeoCacheCommons::SIZE_MICRO_TR_KEY); ?></td>
</tr>
<tr>
<td><code>small</code></td><td><?= tr(GeoCacheCommons::SIZE_SMALL_TR_KEY); ?></td>
</tr>
<tr>
<td><code>regular</code></td><td><?= tr(GeoCacheCommons::SIZE_REGULAR_TR_KEY); ?></td>
</tr>
<tr>
<td><code>large</code></td><td><?= tr(GeoCacheCommons::SIZE_LARGE_TR_KEY); ?></td>
</tr>
<tr>
<td><code>other</code></td><td><?= tr(GeoCacheCommons::SIZE_OTHER_TR_KEY); ?></td>
</tr>
<tr>
<td><code>virtual</code></td><td><?= tr(GeoCacheCommons::SIZE_NONE_TR_KEY); ?></td>
</tr>
</table>
<hr style="height: 1px; margin: 10px;"/>
<p style="text-align: justify;">
<?= tr('gpx_info_p8'); ?>:
</p>
<table class="bs-table" style="width: 95%;">
<colgroup>
<col style="width: 25%;"/>
<col/>
<tr>
<td><code>type</code></td><td><?= tr('cache_type'); ?></td>
</tr>
<tr>
<td><code>size</code></td><td><?= tr('cache_size'); ?></td>
</tr>
<tr>
<td><code>trip_time</code></td><td><?= tr('gpx_info_trip_time'); ?></td>
</tr>
<tr>
<td><code>trip_distance</code></td><td><?= tr('gpx_info_trip_distance'); ?></td>
</tr>
</table>
<br/>
<p style="text-align: justify;">
<?= tr('gpx_info_p9'); ?>:
</p>
<table class="bs-table" style="width: 95%;">
<colgroup>
<col style="width: 25%;"/>
<col/>
<tr>
<td><code>traditional cache</code></td><td><?= tr(GeoCacheCommons::TYPE_TRADITIONAL_TR_KEY); ?></td>
</tr>
<tr>
<td><code>multi-cache</code></td><td><?= tr(GeoCacheCommons::TYPE_MULTICACHE_TR_KEY); ?></td>
</tr>
<tr>
<td><code>quiz cache</code></td><td><?= tr(GeoCacheCommons::TYPE_QUIZ_TR_KEY); ?></td>
</tr>
<tr>
<td><code>moving cache</code></td><td><?= tr(GeoCacheCommons::TYPE_MOVING_TR_KEY); ?></td>
</tr>
<tr>
<td><code>virtual cache</code></td><td><?= tr(GeoCacheCommons::TYPE_VIRTUAL_TR_KEY); ?></td>
</tr>
<tr>
<td><code>webcam cache</code></td><td><?= tr(GeoCacheCommons::TYPE_WEBCAM_TR_KEY); ?></td>
</tr>
<tr>
<td><code>podcast cache</code></td><td><?= tr(GeoCacheCommons::TYPE_GEOPATHFINAL_TR_KEY); ?></td>
</tr>
<tr>
<td><code>event cache</code></td><td><?= tr(GeoCacheCommons::TYPE_EVENT_TR_KEY); ?></td>
</tr>
<tr>
<td><code>own cache</code></td><td><?= tr(GeoCacheCommons::TYPE_OWNCACHE_TR_KEY); ?></td>
</tr>
<tr>
<td><code>other cache</code></td><td><?= tr(GeoCacheCommons::TYPE_OTHERTYPE_TR_KEY); ?></td>
</tr>
</table>
<br/>
<p style="text-align: justify;">
<?= tr('gpx_info_p10'); ?>:
</p>
<table class="bs-table" style="width: 95%;">
<colgroup>
<col style="width: 25%;"/>
<col/>
<tr>
<td><code>nano</code></td><td><?= tr(GeoCacheCommons::SIZE_NANO_TR_KEY); ?></td>
</tr>
<tr>
<td><code>micro</code></td><td><?= tr(GeoCacheCommons::SIZE_MICRO_TR_KEY); ?></td>
</tr>
<tr>
<td><code>small</code></td><td><?= tr(GeoCacheCommons::SIZE_SMALL_TR_KEY); ?></td>
</tr>
<tr>
<td><code>regular</code></td><td><?= tr(GeoCacheCommons::SIZE_REGULAR_TR_KEY); ?></td>
</tr>
<tr>
<td><code>large</code></td><td><?= tr(GeoCacheCommons::SIZE_LARGE_TR_KEY); ?></td>
</tr>
<tr>
<td><code>very large</code></td><td><?= tr(GeoCacheCommons::SIZE_XLARGE_TR_KEY); ?></td>
</tr>
<tr>
<td><code>other</code></td><td><?= tr(GeoCacheCommons::SIZE_OTHER_TR_KEY); ?></td>
</tr>
<tr>
<td><code>no container</code></td><td><?= tr(GeoCacheCommons::SIZE_NONE_TR_KEY); ?></td>
</tr>
</table>
<hr style="height: 1px; margin: 10px;"/>
<p style="text-align: justify;">
<?= tr('gpx_info_p11'); ?>
</div>
