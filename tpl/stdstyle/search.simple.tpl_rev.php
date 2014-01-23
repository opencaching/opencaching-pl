<?php
/***************************************************************************
                                                  ./tpl/stdstyle/search.simple.tpl.php
                                                            -------------------
        begin                : July 25 2004
        copyright            : (C) 2004 The OpenCaching Group
        forum contact at     : http://www.opencaching.com/phpBB2

    ***************************************************************************/

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

   Unicode Reminder メモ

     simple filter template for XHTML search form

 ****************************************************************************/
?>
<script type="text/javascript">
<!--
var mnAttributesShowCat2 = 1;
var maAttributes = new Array({attributes_jsarray});

function _sbn_click()
{
    if (document.searchbyname.cachename.value == "")
    {
        alert("Proszę w polu nazwa wprowadzić wartość!");
        return false;
    }
    return true;
}

function _sbft_click()
{
    if (document.searchbyfulltext.fulltext.value == "")
    {
        alert("Proszę w polu tekst wprowadzić wartość!");
        return false;
    }

    if ((document.searchbyfulltext.ft_name.checked == false) &&
       (document.searchbyfulltext.ft_desc.checked == false) &&
       (document.searchbyfulltext.ft_logs.checked == false) &&
       (document.searchbyfulltext.ft_pictures.checked == false))
    {
        alert("Musisz zaznaczyć choć jedno pole do poszukiwań !");
        return false;
    }

    return true;
}

function _sbd_click()
{
    if (isNaN(document.searchbydistance.lon_h.value) || isNaN(document.searchbydistance.lon_min.value))
    {
        alert("Stopnie długości geograficznej muszą być cyfra!\nFormat: hh° mm.mmm");
        return false;
    }
    else if (isNaN(document.searchbydistance.lat_h.value) || isNaN(document.searchbydistance.lat_min.value))
    {
        alert("Stopnie szerkości geograficznej muszą być cyfrą!\nFormat: hh° mm.mmm");
        return false;
    }
    else if (isNaN(document.searchbydistance.distance.value))
    {
        alert("max. Odległość musi być cyfrą!");
        return false;
    }
    else if (document.searchbydistance.distance.value <= 0 || document.searchbydistance.distance.value > 9999)
    {
        alert("Dozwolona max wartość odległości musi być z zakresu: 0 - 9999");
        return false;
    }
    return true;
}

function _sbplz_click()
{
    if (document.searchbyplz.plz.value == "")
    {
        alert("Proszę w polu kod pocztowy wprowadzić wartość!");
        return false;
    }
    return true;
}

function _sbort_click()
{
    if (document.searchbyort.ort.value == "")
    {
        alert("Proszę w polu nazwa miejsca wprowadzić wartość!");
        return false;
    }
    return true;
}

function _sbo_click()
{
    if (document.searchbyowner.owner.value == "")
    {
        alert("Proszę w polu właściciel wprowadzić wartość!");
        return false;
    }
    return true;
}

function _sbf_click()
{
    if (document.searchbyfinder.finder.value == "")
    {
        alert("Proszę w polu Szkający wprowadzić wartość!");
        return false;
    }
    return true;
}

function sync_options(element)
{
    var formnames = new Array();
    formnames[0] = "searchbyname";
    formnames[1] = "searchbydistance";
    formnames[2] = "searchbyowner";
    formnames[3] = "searchbyfinder";
    formnames[4] = "searchbyplz";
    formnames[5] = "searchbyort";
    formnames[6] = "searchbyfulltext";

    var sortby = "";
    if (document.optionsform.sort[0].checked == true)
        sortby = "byname";
    else if (document.optionsform.sort[1].checked == true)
        sortby = "bydistance";
    else if (document.optionsform.sort[2].checked == true)
        sortby = "bycreated";

    var tmpattrib = "";
    for (i = 0; i < maAttributes.length; i++)
        if (maAttributes[i][1] == 1)
            tmpattrib = tmpattrib + maAttributes[i][0] + ';';
    if(tmpattrib.length > 0)
        tmpattrib = tmpattrib.substr(0, tmpattrib.length-1);

    var tmpattrib_not = "";
    for (i = 0; i < maAttributes.length; i++)
        if (maAttributes[i][1] == 2)
            tmpattrib_not = tmpattrib_not + maAttributes[i][0] + ';';
    if(tmpattrib_not.length > 0)
        tmpattrib_not = tmpattrib_not.substr(0, tmpattrib_not.length-1);

    for (var i in formnames)
    {
        document.forms[formnames[i]].sort.value = sortby;
        document.forms[formnames[i]].f_userowner.value = document.optionsform.f_userowner.checked ? 1 : 0;
        document.forms[formnames[i]].f_userfound.value = document.optionsform.f_userfound.checked ? 1 : 0;
        document.forms[formnames[i]].f_inactive.value = document.optionsform.f_inactive.checked ? 1 : 0;
        document.forms[formnames[i]].f_ignored.value = document.optionsform.f_ignored.checked ? 1 : 0;
                document.forms[formnames[i]].f_watched.value = document.optionsform.f_watched.checked ? 1 : 0;
        document.forms[formnames[i]].country.value = document.optionsform.country.value;
        document.forms[formnames[i]].cachetype.value = document.optionsform.cachetype.value;
        document.forms[formnames[i]].cache_attribs.value = tmpattrib;
        document.forms[formnames[i]].cache_attribs_not.value = tmpattrib_not;
    }
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
//-->
</script>

<form name="optionsform" style="display:inline;">
    <table class="content">
        <colgroup>
            <col width="200">
            <col>
        </colgroup>
        <tr><td class="header" colspan="3"><img src="tpl/stdstyle/images/cache/traditional.gif" class="icon32" alt="Szukanie skrzynek" title="Szukanie skrzynek" align="middle" /><font size="4">  <b>Szukanie skrzynek</b></font></td></tr>
        <tr><td class="spacer" colspan="3"><span id="scriptwarning" class="errormsg">Obsługa JavaScript są wyłączone przez co możesz mieć wiele funkcji obsługi tego serwisu niedostepne.</span></td></tr>
        <tr>
            <td>Wynik szukania sortuj wg:</td>
            <td colspan="2">
                <input type="radio" name="sort" value="byname" index="0" id="l_sortbyname" class="radio" onclick="javascript:sync_options(this)" {byname_checked}> <label for="l_sortbyname">Nazwa skrzynki</label>&nbsp;
                <input type="radio" name="sort" value="bydistance" index="1" id="l_sortbydistance" class="radio" onclick="javascript:sync_options(this)" {bydistance_checked}> <label for="l_sortbydistance">Odległości</label>&nbsp;
                <input type="radio" name="sort" value="bycreated" index="1" id="l_sortbycreated" class="radio" onclick="javascript:sync_options(this)" {bycreated_checked}> <label for="l_sortbycreated">Wg daty utworzenia</label>

            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td class="help" colspan="2"><img src="tpl/stdstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="Uwagi" title="Uwagi" align="middle" />Odległość użyteczna wtedy gdy dane oraz wspołrzędne własne są wprowadzone.
            </td>
        </tr>
        <tr>
            <td>Ominąć skrzynki:</td>
            <td colspan="2">
                <input type="checkbox" name="f_userowner" value="1" id="l_userowner" class="checkbox" onclick="javascript:sync_options(this)" {f_userowner_disabled} /> <label for="l_userowner">Właściciela</label>&nbsp;&nbsp;
                <input type="checkbox" name="f_userfound" value="1" id="l_userfound" class="checkbox" onclick="javascript:sync_options(this)" {f_userfound_disabled} /> <label for="l_userfound">Znalezione</label>&nbsp;&nbsp;
                <input type="checkbox" name="f_inactive" value="1" id="l_inactive" class="checkbox" onclick="javascript:sync_options(this)" {f_inactive_checked} > <label for="l_inactive">Nieaktywne</label>
                <input type="checkbox" name="f_ignored" value="1" id="l_ignored" class="checkbox" onclick="javascript:sync_options(this)" {f_ignored_disabled} > <label for="l_ignored">Ignorowane</label>
                                <input type="checkbox" name="f_watched" value="1" id="l_watched" class="checkbox" onclick="javascript:sync_options(this)" {f_watched_disabled} > <label for="l_ignored">Obserwowane</label>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td class="help" colspan="2"><img src="tpl/stdstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="Uwagi" title="Uwagi" align="middle" />Użyteczne jeśli dane wprowadzone.
            </td>
        </tr>
        <tr>
            <td>Rodzaj skrzynki:</td>
            <td>
                <select name="cachetype" class="input200" onChange="javascript:sync_options(this)">
                    {cachetype_options}
                </select>
            </td>
        </tr>
        <tr>
            <td>Kraj:</td>
            <td>
                <select name="country" class="input200" onChange="javascript:sync_options(this)">
                    {countryoptions}
                </select>
            </td>
        </tr>
        <tr><td class="spacer" colspan="3"></td></tr>
        <tr>
            <td valign="top">Atrybut skrzynki:</td>
            <td>
                <div>{cache_attrib_list}</div>
                <div id="attributesCat2">{cache_attribCat2_list}</div>
            </td>
        </tr>
    </table>
</form>

<script language="javascript">
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
    <input type="hidden" name="f_userowner" value="{hidopt_userowner}" />
    <input type="hidden" name="f_userfound" value="{hidopt_userfound}" />
    <input type="hidden" name="f_inactive" value="{hidopt_inactive}" />
    <input type="hidden" name="f_ignored" value="{hidopt_ignored}" />
        <input type="hidden" name="f_watched" value="{hidopt_watched}" />
    <input type="hidden" name="country" value="{country}" />
    <input type="hidden" name="cachetype" value="{cachetype}" />
    <input type="hidden" name="cache_attribs" value="{hidopt_attribs}" />
    <input type="hidden" name="cache_attribs_not" value="{hidopt_attribs_not}" />

    <table class="content">
        <colgroup>
            <col width="200">
            <col width="220">
            <col>
        </colgroup>
        <tr>
            <td class="header-small" colspan="3">Szukaj wg nazwy skrzynki</td>
        </tr>
        <tr><td class="spacer" colspan="3"></td></tr>
        <tr>
            <td>Nazwa:</td>
            <td><input type="text" name="cachename" value="{cachename}" class="input200" /></td>
            <td><input type="submit" value="Szukaj" class="formbuttons" /></td>
        </tr>
        <tr><td class="spacer" colspan="3"></td></tr>
    </table>
</form>

<form action="search.php" onsubmit="javascript:return(_sbd_click());" method="{formmethod}" enctype="application/x-www-form-urlencoded" name="searchbydistance" dir="ltr" style="display:inline;">
    <input type="hidden" name="searchto" value="searchbydistance" />
    <input type="hidden" name="showresult" value="1" />
    <input type="hidden" name="expert" value="0" />
    <input type="hidden" name="output" value="HTML" />

    <input type="hidden" name="sort" value="{hidopt_sort}" />
    <input type="hidden" name="f_userowner" value="{hidopt_userowner}" />
    <input type="hidden" name="f_userfound" value="{hidopt_userfound}" />
    <input type="hidden" name="f_inactive" value="{hidopt_inactive}" />
    <input type="hidden" name="f_ignored" value="{hidopt_ignored}" />
        <input type="hidden" name="f_watched" value="{hidopt_watched}" />
        <input type="hidden" name="country" value="{country}" />
    <input type="hidden" name="cachetype" value="{cachetype}" />
    <input type="hidden" name="cache_attribs" value="{hidopt_attribs}" />
    <input type="hidden" name="cache_attribs_not" value="{hidopt_attribs_not}" />

    <table class="content">
        <colgroup>
            <col width="200">
            <col width="220">
            <col>
        </colgroup>
        <tr>
            <td class="header-small" colspan="3">Szukaj wg odległości</td>
        </tr>
        <tr><td class="spacer" colspan="3"></td></tr>
        <tr>
            <td valign="top">Współrzędne:</td>
            <td colspan="2" valign="top">
                <select name="latNS" class="input40">
                    <option value="N" {latN_sel}>N</option>
                    <option value="S" {latS_sel}>S</option>
                </select>&nbsp;
                <input type="text" name="lat_h" maxlength="2" value="{lat_h}" class="input30" />&nbsp;°&nbsp;
                <input type="text" name="lat_min" maxlength="6" value="{lat_min}" class="input40" />&nbsp;'&nbsp;
                <br />
                <select name="lonEW" class="input40">
                    <option value="E" {lonE_sel}>E</option>
                    <option value="W" {lonW_sel}>W</option>
                </select>&nbsp;
                <input type="text" name="lon_h" maxlength="3" value="{lon_h}" class="input30" />&nbsp;°&nbsp;
                <input type="text" name="lon_min" maxlength="6" value="{lon_min}" class="input40" />&nbsp;'&nbsp;
            </td>
        </tr>
        <tr>
            <td>Maksymalna odległość:</td>
            <td>
                <input type="text" name="distance" value="{distance}" maxlength="4" class="input50" />&nbsp;
                <select name="unit" class="input100">
                    <option value="km" {sel_km}>Kilometer</option>
                    <option value="sm" {sel_sm}>Mila</option>
                    <option value="nm" {sel_nm}>Mila morska</option>
                </select>
            </td>
            <td><input type="submit" value="Szukaj" class="formbuttons" /></td>
        </tr>
        <tr><td class="spacer" colspan="3"></td></tr>
    </table>
</form>

<table class="content">
    <colgroup>
        <col width="200">
        <col width="220">
        <col>
    </colgroup>
    <tr>
        <td class="header-small" colspan="3">Szukaj wg nazwy miejsowosci</td>
    </tr>
    <tr><td class="spacer" colspan="3"></td></tr>
    {ortserror}
</table>

<form action="search.php" onsubmit="javascript:return(_sbort_click());" method="{formmethod}" enctype="application/x-www-form-urlencoded" name="searchbyort" dir="ltr" style="display:inline;">
    <input type="hidden" name="searchto" value="searchbyort" />
    <input type="hidden" name="showresult" value="1" />
    <input type="hidden" name="expert" value="0" />
    <input type="hidden" name="output" value="HTML" />

    <input type="hidden" name="sort" value="{hidopt_sort}" />
    <input type="hidden" name="f_userowner" value="{hidopt_userowner}" />
    <input type="hidden" name="f_userfound" value="{hidopt_userfound}" />
    <input type="hidden" name="f_inactive" value="{hidopt_inactive}" />
    <input type="hidden" name="f_ignored" value="{hidopt_ignored}" />
        <input type="hidden" name="f_watched" value="{hidopt_watched}" />
    <input type="hidden" name="country" value="{country}" />
    <input type="hidden" name="cachetype" value="{cachetype}" />
    <input type="hidden" name="cache_attribs" value="{hidopt_attribs}" />
    <input type="hidden" name="cache_attribs_not" value="{hidopt_attribs_not}" />

    <table class="content">
        <colgroup>
            <col width="200">
            <col width="220">
            <col>
        </colgroup>
        <tr>
            <td>Nazwa miasta:</td>
            <td><input type="text" name="ort" value="{ort}" class="input200" /></td>
            <td><input type="submit" value="Szukaj" class="formbuttons" /></td>
        </tr>
    </table>
</form>

<table class="content">
    <colgroup>
        <col width="200">
        <col width="220">
        <col>
    </colgroup>
    <tr><td class="spacer" colspan="3"></td></tr>
</table>

<form action="search.php" onsubmit="javascript:return(_sbft_click());" method="{formmethod}" enctype="application/x-www-form-urlencoded" name="searchbyfulltext" dir="ltr" style="display:inline;">
    <input type="hidden" name="searchto" value="searchbyfulltext" />
    <input type="hidden" name="showresult" value="1" />
    <input type="hidden" name="expert" value="0" />
    <input type="hidden" name="output" value="HTML" />

    <input type="hidden" name="sort" value="{hidopt_sort}" />
    <input type="hidden" name="f_userowner" value="{hidopt_userowner}" />
    <input type="hidden" name="f_userfound" value="{hidopt_userfound}" />
    <input type="hidden" name="f_inactive" value="{hidopt_inactive}" />
    <input type="hidden" name="f_ignored" value="{hidopt_ignored}" />
        <input type="hidden" name="f_watched" value="{hidopt_watched}" />
    <input type="hidden" name="country" value="{country}" />
    <input type="hidden" name="cachetype" value="{cachetype}" />
    <input type="hidden" name="cache_attribs" value="{hidopt_attribs}" />
    <input type="hidden" name="cache_attribs_not" value="{hidopt_attribs_not}" />

    <table class="content">
        <colgroup>
            <col width="200">
            <col width="220">
            <col>
        </colgroup>
        <tr>
            <td class="header-small" colspan="3">Szukaj Tekstu</td>
        </tr>
        <tr><td class="spacer" colspan="3"></td></tr>
        {fulltexterror}
        <tr>
            <td>Tekst:</td>
            <td><input type="text" name="fulltext" value="{fulltext}" class="input200" /></td>
            <td><input type="submit" value="Szukaj" class="formbuttons" /></td>
        </tr>
        <tr>
            <td></td>
            <td colspan="2">
                <table width="250px">
                    <tr>
                        <td><input type="checkbox" name="ft_name" id="ft_name" class="checkbox" value="1" {ft_name_checked} /> <label for="ft_name">Nazwa</label></td>
                        <td><input type="checkbox" name="ft_desc" id="ft_desc" class="checkbox" value="1" {ft_desc_checked} /> <label for="ft_desc">Opis</label></td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="ft_logs" id="ft_logs" class="checkbox" value="1" {ft_logs_checked} /> <label for="ft_logs">Logi</label></td>
                        <td><input type="checkbox" name="ft_pictures" id="ft_pictures" class="checkbox" value="1" {ft_pictures_checked} /> <label for="ft_pictures">Obrazki</label></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr><td class="spacer" colspan="3"></td></tr>
    </table>
</form>

<form action="search.php" onsubmit="javascript:return(_sbo_click());" method="{formmethod}" enctype="application/x-www-form-urlencoded" name="searchbyowner" dir="ltr" style="display:inline;">
    <input type="hidden" name="searchto" value="searchbyowner" />
    <input type="hidden" name="showresult" value="1" />
    <input type="hidden" name="expert" value="0" />
    <input type="hidden" name="output" value="HTML" />

    <input type="hidden" name="sort" value="{hidopt_sort}" />
    <input type="hidden" name="f_userowner" value="{hidopt_userowner}" />
    <input type="hidden" name="f_userfound" value="{hidopt_userfound}" />
    <input type="hidden" name="f_inactive" value="{hidopt_inactive}" />
    <input type="hidden" name="f_ignored" value="{hidopt_ignored}" />
        <input type="hidden" name="f_watched" value="{hidopt_watched}" />
    <input type="hidden" name="country" value="{country}" />
    <input type="hidden" name="cachetype" value="{cachetype}" />
    <input type="hidden" name="cache_attribs" value="{hidopt_attribs}" />
    <input type="hidden" name="cache_attribs_not" value="{hidopt_attribs_not}" />

    <table class="content">
        <colgroup>
            <col width="200">
            <col width="220">
            <col>
        </colgroup>
        <tr>
            <td class="header-small" colspan="3">Szukaj wg właściciela skrzynki</td>
        </tr>
        <tr><td class="spacer" colspan="3"></td></tr>
        <tr>
            <td>Właściciel:</td>
            <td><input type="text" name="owner" value="{owner}" maxlength="40" class="input200" /></td>
            <td><input type="submit" value="Szukaj" class="formbuttons" /></td>
        </tr>
        <tr><td class="spacer" colspan="3"></td></tr>
    </table>
</form>

<form action="search.php" onsubmit="javascript:return(_sbf_click());" method="{formmethod}" enctype="application/x-www-form-urlencoded" name="searchbyfinder" dir="ltr" style="display:inline;">
    <input type="hidden" name="searchto" value="searchbyfinder" />
    <input type="hidden" name="showresult" value="1" />
    <input type="hidden" name="expert" value="0" />
    <input type="hidden" name="output" value="HTML" />

    <input type="hidden" name="sort" value="{hidopt_sort}" />
    <input type="hidden" name="f_userowner" value="{hidopt_userowner}" />
    <input type="hidden" name="f_userfound" value="{hidopt_userfound}" />
    <input type="hidden" name="f_inactive" value="{hidopt_inactive}" />
    <input type="hidden" name="f_ignored" value="{hidopt_ignored}" />
        <input type="hidden" name="f_watched" value="{hidopt_watched}" />
    <input type="hidden" name="country" value="{country}" />
    <input type="hidden" name="cachetype" value="{cachetype}" />
    <input type="hidden" name="cache_attribs" value="{hidopt_attribs}" />
    <input type="hidden" name="cache_attribs_not" value="{hidopt_attribs_not}" />

    <table class="content">
        <colgroup>
            <col width="200">
            <col width="220">
            <col>
        </colgroup>
        <tr>
            <td class="header-small" colspan="3">Szukaj wg znalazcy</td>
        </tr>
        <tr><td class="spacer" colspan="3"></td></tr>
        <tr>
            <td>Znalazca:</td>
            <td><input type="text" name="finder" value="{finder}" maxlength="40" class="input200" /></td>
            <td><input type="submit" value="Szukaj" class="formbuttons" /></td>
        </tr>
        <tr><td class="spacer" colspan="3"></td></tr>
    </table>
</form>
