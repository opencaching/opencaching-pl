<?php

?>
<script type="text/javascript">
    function checkForm()
    {

        if (document.myroute_form.name.value == "")
        {
            alert("{{route_name_info}}");
            return false;
        }
        if (document.myroute_form.radius.value < 0.5 || document.myroute_form.radius.value > 10)
        {
            alert("{{radius_info}}");
            return false;
        }
        if (document.myroute_form.file.value == "")
        {
            alert("{{file_name_info}}");
            return false;
        }

        return true;
    }
</script>


<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/route.png" class="icon32" alt="" />&nbsp;{{add_new_route}}</div>

<form action="myroutes_add.php" method="post" enctype="multipart/form-data" name="myroute_form" dir="ltr" >
    <div class="searchdiv">
        <table class="content">
            <tr class="form-group-sm">
                <td valign='top' width='25%'><span style="font-weight:bold;">{{route_name}}:</span></td>
                <td width='75%'><input type='text' name='name' size='50' class='form-control input300' value=''></td>
            </tr>
            <tr>
                <td valign='top' width='25%'><span style="font-weight:bold;">{{route_desc}}:</span></td>
                <td width='75%'><textarea name='desc' cols='80' rows='3'></textarea></td>
            </tr>
            <tr class="form-group-sm">
                <td valign='top' width='25%'><span style="font-weight:bold;">{{route_radius}} (km):</span></td>
                <td width='75%'><input type='text' name='radius' size='5' class='form-control input50' value=''>&nbsp;&nbsp;<span class="notice">{{radius_info}}</span></td>
            </tr>

            <tr class="form-group-sm">
                <td valign="top"><span style="font-weight:bold;">{{file_name}} KML:</span></td>
                <td>

                    <div class="form-inline">
                    <?php $view->callChunk('fileUpload','file', '.kml','51200'); ?>
                    </div>
                </td>
            </tr>
            <tr><td class="buffer" colspan="2"></td></tr>
            <tr>
                <td valign="top" align="left" colspan="2">
                    <button type="submit" name="back" value="back" class="btn btn-default">{{cancel}}</button>&nbsp;&nbsp;
                    <button type="submit" name="submitform" value="submit" onclick="return checkForm();" class="btn btn-primary">{{add_new_route}}</button>
                    <br /><br /></td>
            </tr>

        </table>
</form>
</div>
