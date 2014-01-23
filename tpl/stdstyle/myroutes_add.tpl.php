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
<script type="text/javascript">
<!--
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
        if(document.myroute_form.file.value == "")
        {
            alert("{{file_name_info}}");
            return false;
        }

        return true;
    }
    //-->
</script>


<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/route.png" class="icon32" alt="" />&nbsp;{{add_new_route}}</div>

<form action="myroutes_add.php" method="post" enctype="multipart/form-data" name="myroute_form" dir="ltr" >
<input type="hidden" name="MAX_FILE_SIZE" value="51200" />
<div class="searchdiv">
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
        <td valign="top"><span style="font-weight:bold;">{{file_name}} KML:</span></td>
        <td><input class="input200" name="file" type="file" /> </td>
    </tr>
<tr><td class="buffer" colspan="2"></td></tr>
    <tr>
        <td valign="top" align="left" colspan="2">
            <button type="submit" name="back" value="back" style="font-size:12px;width:160px"><b>{{cancel}}</b></button>&nbsp;&nbsp;
            <button type="submit" name="submitform" value="submit" onclick="return checkForm();" style="font-size:12px;width:160px"><b>{{add_new_route}}</b></button>
        <br /><br /></td>
    </tr>

  </table>
</form>
</div>
