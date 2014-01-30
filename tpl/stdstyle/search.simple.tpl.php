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
        <tr>
            <td>&nbsp;</td>
            <td>
                <input id="orderRatingFirst" type="checkbox" name="orderRatingFirst" class="checkbox" value="1" onclick="javascript:sync_options(this)" {orderRatingFirst_checked} />
                <label for="orderRatingFirst">Skrzynki z rekomendacjami są pokazywane jako pierwsze</label>
            </td>
        </tr>

   Unicode Reminder ??

     simple filter template for XHTML search form

 ****************************************************************************/
?>
<script type="text/javascript">
<!--
var mnAttributesShowCat2 = 1;
var maAttributes = new Array({attributes_jsarray});

function loadRegionsSelector(){
    console.log($('#country').val());
    if(typeof $('#country').val() == 'undefined' || $('#country').val() == ''){
        $('#region1').prop('disabled', true);
        return false;
    }
    $('#region1').hide();
    $('#region1').prop('disabled', false);
    $('#regionAjaxLoader').show();
    request = $.ajax({
        url: "ajaxGetRegionsByCountryCode.php",
        type: "post",
        data:{countryCode: $('#country').val(), selectedRegion: '{region}', searchForm: 1 },
    });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR){
        $('#region1').html(response);
        console.log(response);
    });

    request.always(function () {
        $('#regionAjaxLoader').hide();
        $('#region1').fadeIn(1000);
    });
}

function _sbn_click() {
    if (document.searchbyname.cachename.value == "") {
        alert("{{alert_search_by_cachename}}");
        return false;
    } else if (check_recommendations() == false) {
        return false;
    }
    return true;
}

function _sbd_click() {
    if (isNaN(document.searchbydistance.lon_h.value) || isNaN(document.searchbydistance.lon_min.value)) {
        alert("{{alert_search_by_dist_01}}");
        return false;
    } else if (isNaN(document.searchbydistance.lat_h.value) || isNaN(document.searchbydistance.lat_min.value)) {
        alert("{{alert_search_by_dist_02}}");
        return false;
    } else if (isNaN(document.searchbydistance.distance.value)) {
        alert("{{alert_search_by_dist_03}}");
        return false;
    } else if (document.searchbydistance.distance.value <= 0 || document.searchbydistance.distance.value > 9999) {
        alert("{{alert_search_by_dist_04}}");
        return false;
    } else if (check_recommendations() == false) {
        return false;
    }
    return true;
}

function _sbort_click() {
    if (document.searchbyort.ort.value == "") {
        alert("{{alert_search_by_ort_01}}");
        return false;
    } else if (document.searchbyort.ort.value.length < 3) {
        alert("{{alert_search_by_ort_02}}");
        return false;
    } else if (isNaN(document.searchbyort.distance.value)) {
        alert("{{alert_search_by_ort_03}}");
        return false;
    } else if (document.searchbyort.distance.value <= 0 || document.searchbyort.distance.value > 9999) {
        alert("{{alert_search_by_ort_04}}");
        return false;
    } else if (check_recommendations() == false) {
        return false;
    }
    return true;
}

function _sbft_click() {
    if (document.searchbyfulltext.fulltext.value == "") {
        alert("{{alert_search_by_text_01}}");
        return false;
    } else if (document.searchbyfulltext.fulltext.value.length < 3) {
        alert("{{alert_search_by_text_02}}");
        return false;
    } else if ((document.searchbyfulltext.ft_name.checked == false) &&  (document.searchbyfulltext.ft_desc.checked == false) &&
        (document.searchbyfulltext.ft_logs.checked == false) && (document.searchbyfulltext.ft_pictures.checked == false)) {
        alert("{{alert_search_by_text_03}}");
        return false;
    } else if (check_recommendations() == false) {
        return false;
    }
    return true;
}

function _sbo_click() {
    if (document.searchbyowner.owner.value == "") {
        alert("{{alert_search_by_owner}}");
        return false;
    } else if (check_recommendations() == false) {
        return false;
    }
    return true;
}

function _sbf_click() {
    if (document.searchbyfinder.finder.value == "") {
        alert("{{alert_search_by_finder}}");
        return false;
    } else if (check_recommendations() == false) {
        return false;
    }
    return true;
}

function check_recommendations(){
    if (document.optionsform.cache_rec[1].checked == true) {
        if (isNaN(document.optionsform.cache_min_rec.value)) {
            alert("Minimalna ilość rekomendacji musi być cyfrą!");
            return false;
        } else if (document.optionsform.cache_min_rec.value <= 0 || document.optionsform.cache_min_rec.value > 999) {
            alert("Dozwolona wartość minimalnej ilości rekomendacji musi być z zakresu: 0 - 999");
            return false;
        }
    }
    return true;
}

function sync_options(element)

{
    var sortby = "";
    if (document.optionsform.sort[0].checked == true)
        sortby = "byname";
    else if (document.optionsform.sort[1].checked == true)
        sortby = "bydistance";
    else if (document.optionsform.sort[2].checked == true)
        sortby = "bycreated";

    var recommendations = 0;
    if (document.optionsform.cache_rec[0].checked == true) {
        document.optionsform.cache_min_rec.disabled = 'disabled';
        recommendations = 0
    }
    else if (document.optionsform.cache_rec[1].checked == true) {
        document.optionsform.cache_min_rec.disabled = false;
        recommendations = document.optionsform.cache_min_rec.value;
    }

    var tmpattrib = "";
    for (i = 0; i < maAttributes.length; i++)
        if (maAttributes[i][1] == 1)
            tmpattrib = '' + tmpattrib + maAttributes[i][0] + ';';
    if(tmpattrib.length > 0)
        tmpattrib = tmpattrib.substr(0, tmpattrib.length-1);

    var tmpattrib_not = "";
    for (i = 0; i < maAttributes.length; i++)
        if (maAttributes[i][1] == 2)
            tmpattrib_not =  '' + tmpattrib_not + maAttributes[i][0] + ';';
    if(tmpattrib_not.length > 0)
        tmpattrib_not = tmpattrib_not.substr(0, tmpattrib_not.length-1);

    var formnames = new Array("searchbyname","searchbydistance","searchbyort","searchbyfulltext","searchbyowner", "searchbyfinder");
    var gpxLogLimit = $('#gpxLogLimit').val();



    for (var a=0; a < formnames.length; a++) {

        if(document.optionsform.region.value == 0) document.optionsform.region.value = '';

        document.forms[formnames[a]].sort.value = sortby;
        document.forms[formnames[a]].f_inactive.value = document.optionsform.f_inactive.checked ? 1 : 0;
        document.forms[formnames[a]].f_ignored.value = document.optionsform.f_ignored.checked ? 1 : 0;
        document.forms[formnames[a]].f_userfound.value = document.optionsform.f_userfound.checked ? 1 : 0;
        document.forms[formnames[a]].f_userowner.value = document.optionsform.f_userowner.checked ? 1 : 0;
        document.forms[formnames[a]].f_watched.value = document.optionsform.f_watched.checked ? 1 : 0;
        //document.forms[formnames[a]].f_geokret.value = document.optionsform.f_geokret.checked ? 1 : 0;

        if (document.optionsform.country.value != "" && document.optionsform.country.value != "PL" ) {
            document.forms[formnames[a]].country.value = document.optionsform.country.value;
            document.optionsform.region.value = "";
            document.forms[formnames[a]].region.value = "";
           // document.optionsform.region.disabled = false;
        }else {
        document.forms[formnames[a]].country.value = "PL";
        //document.optionsform.region.disabled = false;
        document.forms[formnames[a]].region.value = document.optionsform.region.value;}

        document.forms[formnames[a]].cachetype.value = getCachetypeFilter();

        document.forms[formnames[a]].cachesize_2.value = document.optionsform.cachesize_2.checked ? 1 : 0;
        document.forms[formnames[a]].cachesize_3.value = document.optionsform.cachesize_3.checked ? 1 : 0;
        document.forms[formnames[a]].cachesize_4.value = document.optionsform.cachesize_4.checked ? 1 : 0;
        document.forms[formnames[a]].cachesize_5.value = document.optionsform.cachesize_5.checked ? 1 : 0;
        document.forms[formnames[a]].cachesize_6.value = document.optionsform.cachesize_6.checked ? 1 : 0;
        document.forms[formnames[a]].cachesize_7.value = document.optionsform.cachesize_7.checked ? 1 : 0;
        document.forms[formnames[a]].cachevote_1.value = document.optionsform.cachevote_1.value;
        document.forms[formnames[a]].cachevote_2.value = document.optionsform.cachevote_2.value;
        document.forms[formnames[a]].cachenovote.value = document.optionsform.cachenovote.checked ? 1 : 0;
        document.forms[formnames[a]].cachedifficulty_1.value = document.optionsform.cachedifficulty_1.value;
        document.forms[formnames[a]].cachedifficulty_2.value = document.optionsform.cachedifficulty_2.value;
        document.forms[formnames[a]].cacheterrain_1.value = document.optionsform.cacheterrain_1.value;
        document.forms[formnames[a]].cacheterrain_2.value = document.optionsform.cacheterrain_2.value;
        document.forms[formnames[a]].cache_attribs.value = tmpattrib;
        document.forms[formnames[a]].cache_attribs_not.value = tmpattrib_not;
        document.forms[formnames[a]].cacherating.value = recommendations;
        document.forms[formnames[a]].gpxLogLimit.value = gpxLogLimit;
    }
}

function getCachetypeFilter(){
    var cachetype_filter = '';
    cachetype_filter = cachetype_filter.concat(document.getElementById('cachetype_1').style.visibility == 'hidden' ? 0 : 1);
    cachetype_filter = cachetype_filter.concat(document.getElementById('cachetype_2').style.visibility == 'hidden' ? 0 : 1);
    cachetype_filter = cachetype_filter.concat(document.getElementById('cachetype_3').style.visibility == 'hidden' ? 0 : 1);
    cachetype_filter = cachetype_filter.concat(document.getElementById('cachetype_4').style.visibility == 'hidden' ? 0 : 1);
    cachetype_filter = cachetype_filter.concat(document.getElementById('cachetype_5').style.visibility == 'hidden' ? 0 : 1);
    cachetype_filter = cachetype_filter.concat(document.getElementById('cachetype_6').style.visibility == 'hidden' ? 0 : 1);
    cachetype_filter = cachetype_filter.concat(document.getElementById('cachetype_7').style.visibility == 'hidden' ? 0 : 1);
    cachetype_filter = cachetype_filter.concat(document.getElementById('cachetype_8').style.visibility == 'hidden' ? 0 : 1);
    cachetype_filter = cachetype_filter.concat(document.getElementById('cachetype_9').style.visibility == 'hidden' ? 0 : 1);
    cachetype_filter = cachetype_filter.concat(document.getElementById('cachetype_10').style.visibility == 'hidden' ? 0 : 1);
    return cachetype_filter;
}


var oldPositions = new Array();
function switchCacheType(image_name) {

    if(document.getElementById(image_name).style.visibility == 'hidden') {
        document.getElementById(image_name + "_bw").style.visibility = 'hidden';
        oldPositions[image_name + "_bw"] = document.getElementById(image_name + "_bw").style.position;
        document.getElementById(image_name + "_bw").style.position = 'absolute';
        document.getElementById(image_name).style.visibility = '';
        if(oldPositions[image_name])
            document.getElementById(image_name).style.position = oldPositions[image_name];
        else
            document.getElementById(image_name).style.position = 'relative';;
    }
    else {
        document.getElementById(image_name).style.visibility = 'hidden';
        oldPositions[image_name] = document.getElementById(image_name).style.position;
        document.getElementById(image_name).style.position = 'absolute';
        document.getElementById(image_name + "_bw").style.visibility = '';
        if(oldPositions[image_name + "_bw"])
            document.getElementById(image_name + "_bw").style.position = oldPositions[image_name + "_bw"];
        else
            document.getElementById(image_name + "_bw").style.position = 'relative';
    }

    sync_options(null);
}

function switchAttribute(id)
{
    var attrImg = document.getElementById("attrimg" + id);
    var nArrayIndex = 0;

    for (nArrayIndex = 0; nArrayIndex < maAttributes.length; nArrayIndex++)
    {
        if (maAttributes[nArrayIndex][0] == id)
            break;
    }

    if (maAttributes[nArrayIndex][1] == 0)
    {
        attrImg.src = maAttributes[nArrayIndex][3];
        maAttributes[nArrayIndex][1] = 1;
    }
    else if (maAttributes[nArrayIndex][1] == 1)
    {
        attrImg.src = maAttributes[nArrayIndex][4];
        maAttributes[nArrayIndex][1] = 2;
    }
    else if (maAttributes[nArrayIndex][1] == 2)
    {
        attrImg.src = maAttributes[nArrayIndex][5];
        maAttributes[nArrayIndex][1] = 0;
    }

    sync_options(null);
}

function hideAttributesCat2()
{
    mnAttributesShowCat2 = 0;
    document.getElementById('attributesCat2').style.display = "none";
}

function showAttributesCat2()
{
    mnAttributesShowCat2 = 1;
    document.getElementById('attributesCat2').style.display = "block";
}

function switchCat2()
{
    if (mnAttributesShowCat2 != 0)
        hideAttributesCat2();
    else
        showAttributesCat2();
}

function insertLocation(lat, lon)
{
    var latNS = 'N', lonEW = 'E';

    if (lat < 0) {
        lat = -lat;
        latNS = 'S';
    }
    if (lon < 0) {
        lon = -lon;
        lonEW = 'W';
    }

    var lat_h = lat | 0;
    var lon_h = lon | 0;
    var lat_min = ((lat - lat_h)*60);
    var lon_min = ((lon - lon_h)*60);

    document.searchbydistance.latNS.value = latNS;
    document.searchbydistance.lat_h.value = lat_h;
    document.searchbydistance.lat_min.value = lat_min.toFixed(3);
    document.searchbydistance.lonEW.value = lonEW;
    document.searchbydistance.lon_h.value = lon_h;
    document.searchbydistance.lon_min.value = lon_min.toFixed(3);
}

function showGeoCoder()
{
    var geocoder = window.open('geocoder.php','geocoder','width=650,height=500');
}



//-->
</script>

<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="Szukanie skrzynek" title="Szukanie skrzynek" align="middle"/><img src="tpl/stdstyle/images/blue/search3.png" class="icon32" alt="Szukanie skrzynek" title="Szukanie skrzynek" align="middle"/>&nbsp;{{search_cache}}</div>
<div class="searchdiv">

<p class="content-title-noshade-size3">{{search_options}}</p>

<form name="optionsform" style="display:inline;" action="search.php" method="post">
    <table class="table">
        <colgroup>
            <col width="200"/>
            <col/>
        </colgroup>
        <tr><td class="buffer" colspan="3"><span id="scriptwarning" class="errormsg">Obsługa JavaScript są wyłączone przez co możesz mieć wiele funkcji obsługi tego serwisu niedostepne.</span></td></tr>
        <tr>
            <td class="content-title-noshade">{{omit_caches}}:</td>
            <td class="content-title-noshade" colspan="2">
                <input type="checkbox" name="f_inactive" value="1" id="l_inactive" class="checkbox" onclick="javascript:sync_options(this)" {f_inactive_checked} /> <label for="l_inactive">{{not_active}}</label>
                <input type="checkbox" name="f_ignored" value="1" id="l_ignored" class="checkbox" onclick="javascript:sync_options(this)" {f_ignored_disabled} /> <label for="l_ignored">{{ignored}}</label>
                <input type="checkbox" name="f_userfound" value="1" id="l_userfound" class="checkbox" onclick="javascript:sync_options(this)" {f_userfound_disabled} /> <label for="l_userfound">{{founds}}</label>&nbsp;&nbsp;
                <input type="checkbox" name="f_userowner" value="1" id="l_userowner" class="checkbox" onclick="javascript:sync_options(this)" {f_userowner_disabled} /> <label for="l_userowner">{{of_owner}}</label>&nbsp;&nbsp;
                <input type="checkbox" name="f_watched" value="1" id="l_watched" class="checkbox" onclick="javascript:sync_options(this)" {f_watched_disabled} /> <label for="l_watched">{{observed}}</label>
                <!--<input type="checkbox" name="f_geokret" value="1" id="l_geokret" class="checkbox" onclick="javascript:sync_options(this)" {f_geokret_checked} /> <label for="l_geokret">{{geokret}}</label>-->
            </td>
        </tr>
        <tr><td class="buffer" colspan="3"></td></tr>
        <tr>
            <td class="content-title-noshade">{{cache_type}}:</td>
            <td class="content-title-noshade">
                {cachetype_options}
            </td>
        </tr>
        <tr><td class="buffer" colspan="3"></td></tr>
        <tr>
            <td valign="top" class="content-title-noshade">{{cache_size}}:</td>
            <td class="content-title-noshade">
                {cachesize_options}
            </td>
        </tr>
        <tr><td class="buffer" colspan="3"></td></tr>
        <tr>
            <td valign="middle" class="content-title-noshade">{{cache_attributes}}:</td>
            <td class="content-title-noshade">
                <div style="width:500px;">{cache_attrib_list}</div>
                <div id="attributesCat2">{cache_attribCat2_list}</div>
            </td>
        </tr>
        <tr><td class="buffer" colspan="3"></td></tr>
        <tr>
            <td valign="top" class="content-title-noshade">{{task_difficulty}}:</td>
            <td class="content-title-noshade">
                {{from}} <select name="cachedifficulty_1" class="input40" onchange="javascript:sync_options(this)">
                    <option value="1" selected="selected">1</option>
                    <option value="1.5">1.5</option>
                    <option value="2">2</option>
                    <option value="2.5">2.5</option>
                    <option value="3">3</option>
                    <option value="3.5">3.5</option>
                    <option value="4">4</option>
                    <option value="4.5">4.5</option>
                    <option value="5">5</option>
                </select>
                {{to}} <select name="cachedifficulty_2" class="input40" onchange="javascript:sync_options(this)">
                    <option value="1">1</option>
                    <option value="1.5">1.5</option>
                    <option value="2">2</option>
                    <option value="2.5">2.5</option>
                    <option value="3">3</option>
                    <option value="3.5">3.5</option>
                    <option value="4">4</option>
                    <option value="4.5">4.5</option>
                    <option value="5" selected="selected">5</option>
                </select>
            </td>
        </tr>
        <tr><td class="buffer" colspan="3"></td></tr>
        <tr>
            <td valign="top" class="content-title-noshade">{{terrain_difficulty}}:</td>
            <td class="content-title-noshade">
                {{from}} <select name="cacheterrain_1" class="input40" onchange="javascript:sync_options(this)">
                    <option value="1" selected="selected">1</option>
                    <option value="1.5">1.5</option>
                    <option value="2">2</option>
                    <option value="2.5">2.5</option>
                    <option value="3">3</option>
                    <option value="3.5">3.5</option>
                    <option value="4">4</option>
                    <option value="4.5">4.5</option>
                    <option value="5">5</option>
                </select>
                {{to}} <select name="cacheterrain_2" class="input40" onchange="javascript:sync_options(this)">
                    <option value="1">1</option>
                    <option value="1.5">1.5</option>
                    <option value="2">2</option>
                    <option value="2.5">2.5</option>
                    <option value="3">3</option>
                    <option value="3.5">3.5</option>
                    <option value="4">4</option>
                    <option value="4.5">4.5</option>
                    <option value="5" selected="selected">5</option>
                </select>
            </td>
        </tr>
        <tr><td class="buffer" colspan="3"></td></tr>
        <tr>
            <td valign="top" class="content-title-noshade">{{score}}:</td>
            <td class="content-title-noshade">
                {{from}} <select name="cachevote_1" onchange="javascript:sync_options(this)">
                    <option value="-3">{{rating_poor}}</option>
                    <option value="0.5">{{rating_mediocre}}</option>
                    <option value="1.2">{{rating_avarage}}</option>
                    <option value="2">{{rating_good}}</option>
                    <option value="2.5">{{rating_excellent}}</option>
                </select>
                {{to}} <select name="cachevote_2" onchange="javascript:sync_options(this)">
                    <option value="0.499">{{rating_poor}}</option>
                    <option value="1.199">{{rating_mediocre}}</option>
                    <option value="1.999">{{rating_avarage}}</option>
                    <option value="2.499">{{rating_good}}</option>
                    <option value="3.000" selected="selected">{{rating_excellent}}</option>
                </select>
                <input type="checkbox" name="cachenovote" value="1" id="l_cachenovote" class="checkbox" onclick="javascript:sync_options(this)" checked="checked"/><label for="l_cachenovote">{{with_hidden_score}}</label>
            </td>
        </tr>
        <tr><td class="buffer" colspan="3"></td></tr>
        <tr>
            <td class="content-title-noshade">{{search_recommendations}}:</td>
            <td class="content-title-noshade" colspan="2">
                <input type="radio" name="cache_rec" value="0" tabindex="0" id="l_all_caches" class="radio" onclick="javascript:sync_options(this)" {all_caches_checked} /> <label for="l_all_caches">{{search_all_caches}}</label>&nbsp;
                <input type="radio" name="cache_rec" value="1" tabindex="1" id="l_recommended_caches" class="radio" onclick="javascript:sync_options(this)" {recommended_caches_checked} /> <label for="l_recommended_caches">{{search_recommended_caches}}</label>&nbsp;
                <input type="text" name="cache_min_rec" value="{cache_min_rec}" maxlength="3" class="input50" onchange="javascript:sync_options(this)" {min_rec_caches_disabled} />
            </td>
        </tr>
        <tr><td class="buffer" colspan="3"></td></tr>
        <tr>
            <td class="content-title-noshade">{{country_label}}:</td>
            <td class="content-title-noshade">
                <select name="country" id="country" class="input200" onchange="sync_options(this); loadRegionsSelector();">
                    {countryoptions}
                </select>
                <script>loadRegionsSelector();</script>
            </td>
        </tr>
                </tr>
        <tr><td class="buffer" colspan="3"></td></tr>
        <tr>
        <tr>
            <td valign="top" class="content-title-noshade">{{regions_only_for}}:</td>
            <td class="content-title-noshade">
                <img id="regionAjaxLoader" style="display: none" src="tpl/stdstyle/js/jquery_1.9.2_ocTheme/ptPreloader.gif" />
                <select name="region" id="region1" class="input200" onchange="javascript:sync_options(this)">
                    {regionoptions}
                    </select>
            </tr>
        <tr><td class="buffer" colspan="3"></td></tr>
        <tr>
            <td class="content-title-noshade">{{sort_by}}:</td>
            <td colspan="2" class="content-title-noshade">
                <input type="radio" name="sort" value="byname" tabindex="0" id="l_sortbyname" class="radio" onclick="javascript:sync_options(this)" {byname_checked}/> <label for="l_sortbyname">{{cache_name}}</label>&nbsp;
                <input type="radio" name="sort" value="bydistance" tabindex="1" id="l_sortbydistance" class="radio" onclick="javascript:sync_options(this)" {bydistance_checked}/> <label for="l_sortbydistance">{{distance_label}}</label>&nbsp;
                <input type="radio" name="sort" value="bycreated" tabindex="1" id="l_sortbycreated" class="radio" onclick="javascript:sync_options(this)" {bycreated_checked}/> <label for="l_sortbycreated">{{date_created_label}}</label>

            </td>
        </tr>
        <tr><td class="buffer" colspan="3"></td></tr>

        <tr>
            <td class="content-title-noshade">
                {{pt225}}:
                <br/> <span style="font-size: 7px;">({{pt226}})</span>
            </td>
            <td colspan="2" class="content-title-noshade">
                <input id="gpxLogLimit" name="gpxLogLimit" style="border: none;" type="range" autocomplete="on" onchange="$('#gpxLogLimitCurrent').html(this.value); javascript:sync_options(this);" oninput="$('#gpxLogLimitCurrent').html(this.value); javascript:sync_options(this);" min="5" value="{gpxLogLimitUserChoice}" max="100" step="5" /> <span id="gpxLogLimitCurrent" style="font-size: 10px; font-weight: bold">{gpxLogLimitUserChoice}</span>
            </td>
        </tr>
        <tr><td class="buffer" colspan="3"></td></tr>

    </table>
</form>

<script language="javascript" type="text/javascript">
<!--
    document.getElementById("scriptwarning").firstChild.nodeValue = "";

    // erweiterte attribute ausblenden, falls kein erweitertes attribute selektiert
    var i = 0;
    var bHide = true;
    for (i = 0; i < maAttributes.length; i++)
    {
        if (maAttributes[i][1] != 0 && maAttributes[i][6] > 1)
        {
            bHide = false;
            break;
        }
    }

    if (bHide == true)
        hideAttributesCat2();
//-->
</script>


<form action="search.php" onsubmit="javascript:return(_sbn_click());" method="{formmethod}" enctype="application/x-www-form-urlencoded" name="searchbyname" dir="ltr" style="display:inline;">
    <input type="hidden" name="searchto" value="searchbyname" />
    <input type="hidden" name="showresult" value="1" />
    <input type="hidden" name="expert" value="0" />
    <input type="hidden" name="output" value="HTML" />

    <input type="hidden" name="sort" value="{hidopt_sort}" />
    <input type="hidden" name="f_inactive" value="{hidopt_inactive}" />
    <input type="hidden" name="f_ignored" value="{hidopt_ignored}" />
    <input type="hidden" name="f_userfound" value="{hidopt_userfound}" />
    <input type="hidden" name="f_userowner" value="{hidopt_userowner}" />
    <input type="hidden" name="f_watched" value="{hidopt_watched}" />
    <input type="hidden" name="f_geokret" value="{hidopt_geokret}" />
    <input type="hidden" name="country" value="{country}" />
    <input type="hidden" name="region" value="{region}" />
    <input type="hidden" name="cachetype" value="{cachetype}" />
    <input type="hidden" name="cache_attribs" value="{hidopt_attribs}" />
    <input type="hidden" name="cache_attribs_not" value="{hidopt_attribs_not}" />

    <input type="hidden" name="cachesize_1" value="{cachesize_1}" />
    <input type="hidden" name="cachesize_2" value="{cachesize_2}" />
    <input type="hidden" name="cachesize_3" value="{cachesize_3}" />
    <input type="hidden" name="cachesize_4" value="{cachesize_4}" />
    <input type="hidden" name="cachesize_5" value="{cachesize_5}" />
    <input type="hidden" name="cachesize_6" value="{cachesize_6}" />
    <input type="hidden" name="cachesize_7" value="{cachesize_7}" />
    <input type="hidden" name="cachevote_1" value="{cachevote_1}" />
    <input type="hidden" name="cachevote_2" value="{cachevote_2}" />
    <input type="hidden" name="cachenovote" value="{cachenovote}" />
    <input type="hidden" name="cachedifficulty_1" value="{cachedifficulty_1}" />
    <input type="hidden" name="cachedifficulty_2" value="{cachedifficulty_2}" />
    <input type="hidden" name="cacheterrain_1" value="{cacheterrain_1}" />
    <input type="hidden" name="cacheterrain_2" value="{cacheterrain_2}" />
    <input type="hidden" name="cacherating" value="{cacherating}" />
    <input type="hidden" name="cachename" value="%"  />
    <input type="hidden" name="gpxLogLimit" value="{gpxLogLimitUserChoice}" />


    <table class="table">
        <colgroup>
            <col width="200"/>
            <col width="220"/>
            <col/>
        </colgroup>
        <tr><td class="buffer"></td></tr>
        <tr>
            <td><button type="submit" value="{{search}}" style="font-size:12px;width:140px"><b>{{search}}</b></button></td>
            <td class="content-title-noshade"><input type="checkbox" name="showonmap" id="showonmap1" /><label for="showonmap1">{{show_on_map}}</label></td>
        </tr>
        <tr><td class="buffer" colspan="2"></td></tr>
    </table>
</div>
<div class="searchdiv">
    <table class="table">
        <colgroup>
            <col width="200"/>
            <col width="220"/>
            <col/>
        </colgroup>
        <tr>
            <td colspan="3"><p class="content-title-noshade-size1">{{search_by_name}}</p></td>
        </tr>
        <tr><td class="buffer" colspan="3"></td></tr>
        <tr>
            <td class="content-title-noshade">{{cache_name}}:</td>
            <td><input type="text" name="cachename" value="{cachename}" class="input200" /></td>
            <td><input type="submit" value="{{search}}" class="formbuttons" /></td>
            <td class="content-title-noshade"><input type="checkbox" name="showonmap" id="showonmap1" /><label for="showonmap1">{{show_on_map}}</label></td>
        </tr>
        <tr><td class="buffer" colspan="3"></td></tr>
    </table>
</form>
</div> <div class="searchdiv">
<form action="search.php" onsubmit="javascript:return(_sbd_click());" method="{formmethod}" enctype="application/x-www-form-urlencoded" name="searchbydistance" dir="ltr" style="display:inline;">
    <input type="hidden" name="searchto" value="searchbydistance" />
    <input type="hidden" name="showresult" value="1" />
    <input type="hidden" name="expert" value="0" />
    <input type="hidden" name="output" value="HTML" />

    <input type="hidden" name="sort" value="{hidopt_sort}" />
    <input type="hidden" name="f_inactive" value="{hidopt_inactive}" />
    <input type="hidden" name="f_ignored" value="{hidopt_ignored}" />
    <input type="hidden" name="f_userfound" value="{hidopt_userfound}" />
    <input type="hidden" name="f_userowner" value="{hidopt_userowner}" />
    <input type="hidden" name="f_watched" value="{hidopt_watched}" />
    <input type="hidden" name="f_geokret" value="{hidopt_geokret}" />
    <input type="hidden" name="country" value="{country}" />
    <input type="hidden" name="region" value="{region}" />
    <input type="hidden" name="cachetype" value="{cachetype}" />
    <input type="hidden" name="cache_attribs" value="{hidopt_attribs}" />
    <input type="hidden" name="cache_attribs_not" value="{hidopt_attribs_not}" />

    <input type="hidden" name="cachesize_1" value="{cachesize_1}" />
    <input type="hidden" name="cachesize_2" value="{cachesize_2}" />
    <input type="hidden" name="cachesize_3" value="{cachesize_3}" />
    <input type="hidden" name="cachesize_4" value="{cachesize_4}" />
    <input type="hidden" name="cachesize_5" value="{cachesize_5}" />
    <input type="hidden" name="cachesize_6" value="{cachesize_6}" />
    <input type="hidden" name="cachesize_7" value="{cachesize_7}" />
    <input type="hidden" name="cachevote_1" value="{cachevote_1}" />
    <input type="hidden" name="cachevote_2" value="{cachevote_2}" />
    <input type="hidden" name="cachenovote" value="{cachenovote}" />
    <input type="hidden" name="cachedifficulty_1" value="{cachedifficulty_1}" />
    <input type="hidden" name="cachedifficulty_2" value="{cachedifficulty_2}" />
    <input type="hidden" name="cacheterrain_1" value="{cacheterrain_1}" />
    <input type="hidden" name="cacheterrain_2" value="{cacheterrain_2}" />
    <input type="hidden" name="cacherating" value="{cacherating}" />
    <input type="hidden" name="gpxLogLimit" value="{gpxLogLimitUserChoice}" />

    <table class="table">
        <colgroup>
            <col width="200"/>
            <col width="220"/>
            <col/>
        </colgroup>
        <tr>
            <td colspan="3"><p class="content-title-noshade-size1">{{search_by_distance}}</p></td>
        </tr>
        <tr><td class="buffer" colspan="3"></td></tr>
        <tr>
            <td valign="top" class="content-title-noshade">{{coordinates}}:</td>
            <td colspan="2" valign="top">
                <select name="latNS" class="input40">
                    <option value="N" {latN_sel}>N</option>
                    <option value="S" {latS_sel}>S</option>
                </select>&nbsp;
                <input type="text" name="lat_h" maxlength="2" value="{lat_h}" class="input30" />&nbsp;°&nbsp;
                <input type="text" name="lat_min" maxlength="6" value="{lat_min}" class="input40" />&nbsp;'&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;<img src="tpl/stdstyle/images/free_icons/information.png" alt="" title="info" />&nbsp;&nbsp;<b><a href="javascript:void(0)" onclick="showGeoCoder()">{{where_i_am}}</a></b>
                <br/>
                <select name="lonEW" class="input40">
                    <option value="E" {lonE_sel}>E</option>
                    <option value="W" {lonW_sel}>W</option>
                </select>&nbsp;
                <input type="text" name="lon_h" maxlength="3" value="{lon_h}" class="input30" />&nbsp;°&nbsp;
                <input type="text" name="lon_min" maxlength="6" value="{lon_min}" class="input40" />&nbsp;'&nbsp;
            </td>
        </tr>
        <tr>
            <td class="content-title-noshade">{{max_distance}}:</td>
            <td class="content-title-noshade">
                <input type="text" name="distance" value="{distance}" maxlength="4" class="input50" />&nbsp;
                <select name="unit" class="input100">
                    <option value="km" {sel_km}>{{kilometer}}</option>
                    <option value="sm" {sel_sm}>{{mile}}</option>
                    <option value="nm" {sel_nm}>{{nautical_mile}}</option>
                </select>
            </td>
            <td class="content-title-noshade"><input type="submit" value="{{search}}" class="formbuttons" /></td>
            <td class="content-title-noshade"><input type="checkbox" name="showonmap" id="showonmap2" /><label for="showonmap2">{{show_on_map}}</label></td>
        </tr>
        <tr><td class="buffer" colspan="3"></td></tr>
    </table>
</form>
</div> <div class="searchdiv">
<form action="search.php" onsubmit="javascript:return(_sbort_click());" method="{formmethod}" enctype="application/x-www-form-urlencoded" name="searchbyort" dir="ltr" style="display:inline;">
    <input type="hidden" name="searchto" value="searchbyort" />
    <input type="hidden" name="showresult" value="1" />
    <input type="hidden" name="expert" value="0" />
    <input type="hidden" name="output" value="HTML" />

    <input type="hidden" name="sort" value="{hidopt_sort}" />
    <input type="hidden" name="f_inactive" value="{hidopt_inactive}" />
    <input type="hidden" name="f_ignored" value="{hidopt_ignored}" />
    <input type="hidden" name="f_userfound" value="{hidopt_userfound}" />
    <input type="hidden" name="f_userowner" value="{hidopt_userowner}" />
    <input type="hidden" name="f_watched" value="{hidopt_watched}" />
    <input type="hidden" name="f_geokret" value="{hidopt_geokret}" />
    <input type="hidden" name="country" value="{country}" />
    <input type="hidden" name="region" value="{region}" />
    <input type="hidden" name="cachetype" value="{cachetype}" />
    <input type="hidden" name="cache_attribs" value="{hidopt_attribs}" />
    <input type="hidden" name="cache_attribs_not" value="{hidopt_attribs_not}" />

    <input type="hidden" name="cachesize_1" value="{cachesize_1}" />
    <input type="hidden" name="cachesize_2" value="{cachesize_2}" />
    <input type="hidden" name="cachesize_3" value="{cachesize_3}" />
    <input type="hidden" name="cachesize_4" value="{cachesize_4}" />
    <input type="hidden" name="cachesize_5" value="{cachesize_5}" />
    <input type="hidden" name="cachesize_6" value="{cachesize_6}" />
    <input type="hidden" name="cachesize_7" value="{cachesize_7}" />
    <input type="hidden" name="cachevote_1" value="{cachevote_1}" />
    <input type="hidden" name="cachevote_2" value="{cachevote_2}" />
    <input type="hidden" name="cachenovote" value="{cachenovote}" />
    <input type="hidden" name="cachedifficulty_1" value="{cachedifficulty_1}" />
    <input type="hidden" name="cachedifficulty_2" value="{cachedifficulty_2}" />
    <input type="hidden" name="cacheterrain_1" value="{cacheterrain_1}" />
    <input type="hidden" name="cacheterrain_2" value="{cacheterrain_2}" />
    <input type="hidden" name="cacherating" value="{cacherating}" />
    <input type="hidden" name="gpxLogLimit" value="{gpxLogLimitUserChoice}" />

    <table class="table">
    <colgroup>
        <col width="200"/>
        <col width="220"/>
        <col/>
    </colgroup>
    <tr>
        <td colspan="3"><p class="content-title-noshade-size1">{{search_by_city}}</p></td>
    </tr>
    <tr><td class="buffer" colspan="3"></td></tr>
    {ortserror}
    </table>

    <table class="table">
        <colgroup>
            <col width="200"/>
            <col width="220"/>
            <col/>
        </colgroup>
        <tr>
            <td class="content-title-noshade">{{city_name}}:</td>
            <td class="content-title-noshade" colspan="2" valign="top"><input type="text" name="ort" value="{ort}" class="input200" /></td>
        </tr>
        <tr>
            <td class="content-title-noshade">{{max_distance}}:</td>
            <td class="content-title-noshade">
                <input type="text" name="distance" value="{distance}" maxlength="4" class="input50" />&nbsp;
                <select name="unit" class="input100">
                    <option value="km" {sel_km}>{{kilometer}}</option>
                    <option value="sm" {sel_sm}>{{mile}}</option>
                    <option value="nm" {sel_nm}>{{nautical_mile}}</option>
                </select>
            </td>
            <td class="content-title-noshade"><input type="submit" value="{{search}}" class="formbuttons" /></td>
            <td class="content-title-noshade"><input type="checkbox" name="showonmap" id="showonmap3" /><label for="showonmap3">{{show_on_map}}</label></td>
        </tr>
        <tr><td class="buffer" colspan="3"></td></tr>
    </table>
</form>
</div> <div class="searchdiv">
<form action="search.php" onsubmit="javascript:return(_sbft_click());" method="{formmethod}" enctype="application/x-www-form-urlencoded" name="searchbyfulltext" dir="ltr" style="display:inline;">
    <input type="hidden" name="searchto" value="searchbyfulltext" />
    <input type="hidden" name="showresult" value="1" />
    <input type="hidden" name="expert" value="0" />
    <input type="hidden" name="output" value="HTML" />

    <input type="hidden" name="sort" value="{hidopt_sort}" />
    <input type="hidden" name="f_inactive" value="{hidopt_inactive}" />
    <input type="hidden" name="f_ignored" value="{hidopt_ignored}" />
    <input type="hidden" name="f_userfound" value="{hidopt_userfound}" />
    <input type="hidden" name="f_userowner" value="{hidopt_userowner}" />
    <input type="hidden" name="f_watched" value="{hidopt_watched}" />
    <input type="hidden" name="f_geokret" value="{hidopt_geokret}" />
    <input type="hidden" name="country" value="{country}" />
    <input type="hidden" name="region" value="{region}" />
    <input type="hidden" name="cachetype" value="{cachetype}" />
    <input type="hidden" name="cache_attribs" value="{hidopt_attribs}" />
    <input type="hidden" name="cache_attribs_not" value="{hidopt_attribs_not}" />

    <input type="hidden" name="cachesize_1" value="{cachesize_1}" />
    <input type="hidden" name="cachesize_2" value="{cachesize_2}" />
    <input type="hidden" name="cachesize_3" value="{cachesize_3}" />
    <input type="hidden" name="cachesize_4" value="{cachesize_4}" />
    <input type="hidden" name="cachesize_5" value="{cachesize_5}" />
    <input type="hidden" name="cachesize_6" value="{cachesize_6}" />
    <input type="hidden" name="cachesize_7" value="{cachesize_7}" />
    <input type="hidden" name="cachevote_1" value="{cachevote_1}" />
    <input type="hidden" name="cachevote_2" value="{cachevote_2}" />
    <input type="hidden" name="cachenovote" value="{cachenovote}" />
    <input type="hidden" name="cachedifficulty_1" value="{cachedifficulty_1}" />
    <input type="hidden" name="cachedifficulty_2" value="{cachedifficulty_2}" />
    <input type="hidden" name="cacheterrain_1" value="{cacheterrain_1}" />
    <input type="hidden" name="cacheterrain_2" value="{cacheterrain_2}" />
    <input type="hidden" name="cacherating" value="{cacherating}" />
    <input type="hidden" name="gpxLogLimit" value="{gpxLogLimitUserChoice}" />

    <table class="table">
        <colgroup>
            <col width="200"/>
            <col width="220"/>
            <col/>
        </colgroup>
        <tr>
            <td colspan="3"><p class="content-title-noshade-size1">{{search_text}}</p></td>
        </tr>
        <tr><td class="buffer" colspan="3"></td></tr>
        {fulltexterror}
        <tr>
            <td class="content-title-noshade">{{text}}:</td>
            <td class="content-title-noshade"><input type="text" name="fulltext" value="{fulltext}" class="input200" /></td>
            <td class="content-title-noshade"><input type="submit" value="{{search}}" class="formbuttons" /></td>
            <td class="content-title-noshade"><input type="checkbox" name="showonmap" id="showonmap4" /><label for="showonmap4">{{show_on_map}}</label></td>
        </tr>
        <tr>
            <td></td>
            <td colspan="2" align="center" class="content-title-noshade">
                                <input type="checkbox" name="ft_name" id="ft_name" class="checkbox" value="1" {ft_name_checked} /> <label for="ft_name">{{cache_name}}</label>
                        <input type="checkbox" name="ft_desc" id="ft_desc" class="checkbox" value="1" {ft_desc_checked} /> <label for="ft_desc">{{descriptions}}</label>
                        <input type="checkbox" name="ft_logs" id="ft_logs" class="checkbox" value="1" {ft_logs_checked} /> <label for="ft_logs">{{logs_label}}</label>
                        <input type="checkbox" name="ft_pictures" id="ft_pictures" class="checkbox" value="1" {ft_pictures_checked} /> <label for="ft_pictures">{{pictures}}</label>

            </td>
        </tr>
        <tr><td class="buffer" colspan="3"></td></tr>
    </table>
</form>
</div> <div class="searchdiv">
<form action="search.php" onsubmit="javascript:return(_sbo_click());" method="{formmethod}" enctype="application/x-www-form-urlencoded" name="searchbyowner" dir="ltr" style="display:inline;">
    <input type="hidden" name="searchto" value="searchbyowner" />
    <input type="hidden" name="showresult" value="1" />
    <input type="hidden" name="expert" value="0" />
    <input type="hidden" name="output" value="HTML" />

    <input type="hidden" name="sort" value="{hidopt_sort}" />
    <input type="hidden" name="f_inactive" value="{hidopt_inactive}" />
    <input type="hidden" name="f_ignored" value="{hidopt_ignored}" />
    <input type="hidden" name="f_userfound" value="{hidopt_userfound}" />
    <input type="hidden" name="f_userowner" value="{hidopt_userowner}" />
    <input type="hidden" name="f_watched" value="{hidopt_watched}" />
    <input type="hidden" name="f_geokret" value="{hidopt_geokret}" />
    <input type="hidden" name="country" value="{country}" />
    <input type="hidden" name="region" value="{region}" />
    <input type="hidden" name="cachetype" value="{cachetype}" />
    <input type="hidden" name="cache_attribs" value="{hidopt_attribs}" />
    <input type="hidden" name="cache_attribs_not" value="{hidopt_attribs_not}" />

    <input type="hidden" name="cachesize_1" value="{cachesize_1}" />
    <input type="hidden" name="cachesize_2" value="{cachesize_2}" />
    <input type="hidden" name="cachesize_3" value="{cachesize_3}" />
    <input type="hidden" name="cachesize_4" value="{cachesize_4}" />
    <input type="hidden" name="cachesize_5" value="{cachesize_5}" />
    <input type="hidden" name="cachesize_6" value="{cachesize_6}" />
    <input type="hidden" name="cachesize_7" value="{cachesize_7}" />
    <input type="hidden" name="cachevote_1" value="{cachevote_1}" />
    <input type="hidden" name="cachevote_2" value="{cachevote_2}" />
    <input type="hidden" name="cachenovote" value="{cachenovote}" />
    <input type="hidden" name="cachedifficulty_1" value="{cachedifficulty_1}" />
    <input type="hidden" name="cachedifficulty_2" value="{cachedifficulty_2}" />
    <input type="hidden" name="cacheterrain_1" value="{cacheterrain_1}" />
    <input type="hidden" name="cacheterrain_2" value="{cacheterrain_2}" />
    <input type="hidden" name="cacherating" value="{cacherating}" />
    <input type="hidden" name="gpxLogLimit" value="{gpxLogLimitUserChoice}" />

    <table class="table">
        <colgroup>
            <col width="200"/>
            <col width="220"/>
            <col/>
        </colgroup>
        <tr>
            <td colspan="3"><p class="content-title-noshade-size1">{{search_by_owner}}</p></td>
        </tr>
        <tr><td class="buffer" colspan="3"></td></tr>
        <tr>
            <td class="content-title-noshade">{{owner_label}}:</td>
            <td class="content-title-noshade"><input type="text" name="owner" value="{owner}" maxlength="40" class="input200" /></td>
            <td class="content-title-noshade"><input type="submit" value="{{search}}" class="formbuttons" /></td>
            <td class="content-title-noshade"><input type="checkbox" name="showonmap" id="showonmap5" /><label for="showonmap5">{{show_on_map}}</label></td>
        </tr>
        <tr><td class="buffer" colspan="3"></td></tr>
    </table>
</form>
</div><div class="searchdiv">
<form action="search.php" onsubmit="javascript:return(_sbf_click());" method="{formmethod}" enctype="application/x-www-form-urlencoded" name="searchbyfinder" dir="ltr" style="display:inline;">
    <input type="hidden" name="searchto" value="searchbyfinder" />
    <input type="hidden" name="showresult" value="1" />
    <input type="hidden" name="expert" value="0" />
    <input type="hidden" name="output" value="HTML" />

    <input type="hidden" name="sort" value="{hidopt_sort}" />
    <input type="hidden" name="f_inactive" value="{hidopt_inactive}" />
    <input type="hidden" name="f_ignored" value="{hidopt_ignored}" />
    <input type="hidden" name="f_userfound" value="{hidopt_userfound}" />
    <input type="hidden" name="f_userowner" value="{hidopt_userowner}" />
    <input type="hidden" name="f_watched" value="{hidopt_watched}" />
    <input type="hidden" name="f_geokret" value="{hidopt_geokret}" />
    <input type="hidden" name="country" value="{country}" />
    <input type="hidden" name="region" value="{region}" />
    <input type="hidden" name="cachetype" value="{cachetype}" />
    <input type="hidden" name="cache_attribs" value="{hidopt_attribs}" />
    <input type="hidden" name="cache_attribs_not" value="{hidopt_attribs_not}" />

    <input type="hidden" name="cachesize_1" value="{cachesize_1}" />
    <input type="hidden" name="cachesize_2" value="{cachesize_2}" />
    <input type="hidden" name="cachesize_3" value="{cachesize_3}" />
    <input type="hidden" name="cachesize_4" value="{cachesize_4}" />
    <input type="hidden" name="cachesize_5" value="{cachesize_5}" />
    <input type="hidden" name="cachesize_6" value="{cachesize_6}" />
    <input type="hidden" name="cachesize_7" value="{cachesize_7}" />
    <input type="hidden" name="cachevote_1" value="{cachevote_1}" />
    <input type="hidden" name="cachevote_2" value="{cachevote_2}" />
    <input type="hidden" name="cachenovote" value="{cachenovote}" />
    <input type="hidden" name="cachedifficulty_1" value="{cachedifficulty_1}" />
    <input type="hidden" name="cachedifficulty_2" value="{cachedifficulty_2}" />
    <input type="hidden" name="cacheterrain_1" value="{cacheterrain_1}" />
    <input type="hidden" name="cacheterrain_2" value="{cacheterrain_2}" />
    <input type="hidden" name="cacherating" value="{cacherating}" />
    <input type="hidden" name="gpxLogLimit" value="{gpxLogLimitUserChoice}" />

    <table class="table">
        <colgroup>
            <col width="200"/>
            <col width="220"/>
            <col/>
        </colgroup>
        <tr>
            <td colspan="3"><p class="content-title-noshade-size1">{{search_by_finder}}</p></td>
        </tr>
        <tr><td class="buffer" colspan="3"></td></tr>
        <tr>
            <td class="content-title-noshade">{{finder_label}}:</td>
            <td class="content-title-noshade"><input type="text" name="finder" value="{finder}" maxlength="40" class="input200" /></td>
            <td class="content-title-noshade"><input type="submit" value="{{search}}" class="formbuttons" /></td>
            <td class="content-title-noshade"><input type="checkbox" name="showonmap" id="showonmap6" /><label for="showonmap6">{{show_on_map}}</label></td>
        </tr>
        <tr><td class="buffer" colspan="3"></td></tr>
    </table>
</form>
</div>
