<?php
/***************************************************************************
    *
    *   This program is free software; you can redistribute it and/or modify
    *   it under the terms of the GNU General Public License as published by
    *   the Free Software Foundation; either version 2 of the License, or
    *   (at your option) any later version.
    *
    *  UTF-8 ąść
    ***************************************************************************/
?>

<script src="lib/js/gmap.<?= date("YmdHis", filemtime($rootpath . 'lib/js/gmap.js')) ?>.js" type="text/javascript"></script>
<script type="text/javascript">
//<![CDATA[
function checkForm()
{
    if(document.myroute_form.name.value == "")
    {
        alert("{{route_name_info}}");
        return false;
    }

    if(document.myroute_form.radius.value < 0.5 ||document.myroute_form.radius.value > 10 )
    {
        alert("{{radius_info}}");
        return false;
    }
    if(document.myroute_form.distance.value =="" )
    {
        alert("{{Trasa nie została wyznaczona}}");
        return false;
    } else {
    var len=document.myroute_form.distance.value;
    var lv=len.split("&nbsp;");
    var l=lv[0].split(",");
    var v = parseFloat(l[0]);
    if (v >200) {alert("Wybrana trasa jest za długa ("+v+" km). Maksymalna długość wyznaczonej trasy powinna być do 200 km."); return false;}
    }

    return true;
}
//]]>
</script>


<style type="text/css">

#mapDiv {
    width: 500px;
    height: 500px;
    margin: 5px;
}

#directions_info {
    padding: 3px;
    font: normal 12px courier new;
    text-align: left;
    color: navy;
    overflow: auto;
    height: 435px;
    width: 210px;
}

#directions_info,#buttonContainer {
    border: 1px solid #c8c8c8;
    margin: 5px;
    background: #eff4f8;
}

#loadingMessage {
    position: absolute;
    width: 200px;
    text-align: center;
    padding: 10px;
    border: 5px solid #290B8B;
    background: #3F06FA;
    color: #eeeeee;
    font: bold 20px verdana;
    z-index: 1;
    left: 0px;
    top: 0px;
    opacity: 0.7;
}

/* -------------- Simplify ----------------- */
.button,.buttonB {
    background: #A6A8CC;
    color: navy;
    border: 2px solid navy;
    text-align: center;
    vertical-align: middle;
    font: normal 10px verdana;
    padding: 2px;
    cursor: pointer;
    margin: 3px;
}

.buttonB {
    border: 2px solid rgb(219, 230, 241);
    background-color: #7fa2ca;
    font-weight: bold;
    color: #FFFFFF;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
}

.button:hover,.buttonB:hover {
    color: #000000;
    font-weight: bold;
    border: 2px solid #7fa2ca;
    background: #dde7f1;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
}

#driveVia {
    border: 1px solid gray;
    font: normal 10px verdana;
}

</style>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/route.png" class="icon32" alt="" />&nbsp;{{setup_new_route}}</div>
<div class="searchdiv">

<div class="searchdiv">
<table class="content">
<tr>
<td align="right"><span style="font-weight:bold;">{{route_01}}&nbsp;</span></td>
<td><input type="text" size="25" id="driveFrom" name="from" value=""/></td>
<td rowspan="2"><span style="font-weight:bold;">Via: </span></td>
<td rowspan="2"><textarea name="via" id="driveVia" rows="2" cols="22"></textarea></td>
<td rowspan="2" align="right">&nbsp;&nbsp;<button name="submit" type="submit" value="Go" onclick="getDirections()">{{setup_new_route}}</button></td>
</tr>
<tr>
<td align="right"><span style="font-weight:bold;">{{route_02}}&nbsp;</span></td>
<td align="right"><input type="text" size="25" id="driveTo" name="to" value="" /></td>
</tr>
</table>
</div>

<br/>
<table cellspacing="0" cellpadding="0" id="outerTable">
    <tr>
        <td width="200" valign="top">
            <div id="buttonContainer">
                <div class="buttonB" onclick="removeResults()">{{route_03}}</div>
                <div class="buttonB" onclick="resetMap()">{{route_04}}</div>
            </div>
            <div id="directions_info"></div>
        </td>
        <td valign="top">
            <div id="mapDiv"></div>
        </td>
    </tr>
</table>
<script>
    window.onload = load;
</script>
<div id="loadingMessage" style="display:none;">{{route_05}}</div>
<br/>

<div class="searchdiv">

<form action="myroutes_add_map2.php" method="post" enctype="multipart/form-data" name="myroute_form" dir="ltr" onsubmit="return checkForm();">
<input type="hidden" name="distance" value="" />
<input type="hidden" name="route_points" value="" />
<table class="content">
    <tr>
<td valign='top' width='25%'><span style="font-weight:bold;">{{route_name}}:</span></td>
<td width='75%'><input type='text' name='name' size='50' value=''></td>
</tr>
<tr>
<td valign='top' width='25%'><span style="font-weight:bold;">{{route_desc}}:</span></td>
<td width='75%'><textarea name='desc' cols='80' rows='3'></textarea></td>
</tr>
<tr>
<td valign='top' width='25%'><span style="font-weight:bold;">{{route_radius}} (km):</span></td>
<td width='75%'><input type='text' name='radius' size='5' value=''>&nbsp;&nbsp;<span class="notice">{{radius_info}}</span></td>
</tr>
<tr>
<td valign="top" align="left" colspan="2"><br /><br />
    <button type="submit" name="submitform" value="submit"  style="font-size:12px;width:160px"><b>{{save_route}}</b></button>
<br /></td>
    </tr>
</table><br/>
</form>
</div>
</div>
