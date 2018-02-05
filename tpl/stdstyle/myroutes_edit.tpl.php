<?php
use Utils\Database\XDb;
global $rootpath;
require_once('./lib/common.inc.php');
require_once('./lib/class.polylineEncoder.php');
$route_id = $_REQUEST['routeid'];

$rscp = XDb::xSql("SELECT `lat` ,`lon`
                    FROM `route_points`
                    WHERE `route_id`= ? ", $route_id);
$p = array();
$points = array();
for ($i = 0; false != ($record = XDb::xFetchArray($rscp)); $i++) {

    $y = $record['lon'];
    $x = $record['lat'];

    $p[0] = $x;
    $p[1] = $y;
    $points[$i] = $p;
}

$encoder = new PolylineEncoder();
$polyline = $encoder->encode($points);
?>
<script src="/lib/jsts/attache.array.min.js"></script>
<script src="/lib/jsts/javascript.util.js"></script>
<script src="/lib/jsts/jsts.0.13.2.js"></script>
<script src="/lib/js/myroutes_map.<?= date("YmdHis", filemtime($rootpath . 'lib/js/myroutes_map.js')) ?>.js"></script>
<script>
//<![CDATA[

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
        return true;
    }

    window.onload = function () {
        load(document.myroute_form.radius.value, "<?= $polyline->points ?>");
    };

//]]>
</script>


<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/route.png" class="icon32" alt="" />&nbsp;{{edit_route}}: <span style="color: black;font-size:13px;">{routes_name}</span></div>

<form action="myroutes_edit.php" method="post" enctype="multipart/form-data" name="myroute_form" dir="ltr" onsubmit="return checkForm();">
    <input type="hidden" name="routeid" value="{routeid}" />
    <div class="searchdiv">
        <table class="content">
            <tr class="form-group-sm">
                <td valign='top' width='25%'><span style="font-weight:bold;">{{route_name}}:</span></td>
                <td width='75%'><input type='text' name='name' size='50' class='form-control input300' value='{name}' /></td>
            </tr>
            <tr>
                <td valign='top' width='25%'><span style="font-weight:bold;">{{route_desc}}:</span></td>
                <td width='75%'><textarea name='desc' cols='80' rows='3' >{desc}</textarea></td>
            </tr>
            <tr class="form-group-sm">
                <td valign='top' width='25%'><span style="font-weight:bold;">{{route_radius}} (km):</span></td>
                <td width='75%'><input type='text' name='radius' size='5' class='form-control input50' value='{radius}' />&nbsp;&nbsp;<span class="notice">{{radius_info}}</span></td>
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
                    <button type="submit" name="submit" value="submit" class="btn btn-primary">{{save}}</button>
                    <br /><br /></td>
            </tr>
        </table>
    </div>
</form>

<div class="searchdiv">
    <center>
        <div id="map" style="width:100%; height:500px"></div>
        <br/><span style="font-weight:bold;">{{marked_grey_search_area}}</span>
    </center>
</div>
