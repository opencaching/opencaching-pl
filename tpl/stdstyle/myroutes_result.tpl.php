<?php

?>
<script type="text/javascript" src="lib/js/wz_tooltip.js"></script>
<script language="javascript" type="text/javascript">
<!--

    function check_logs() {
        if (document.myroute_form.cache_log[1].checked == true) {
            if (isNaN(document.myroute_form.nrlogs.value)) {
                alert("Minimalna ilość logów musi być cyfrą!");
                return false;
            } else if (document.myroute_form.nrlogs.value <= 0 || document.myroute_form.nrlogs.value > 999) {
                alert("Dozwolona wartość minimalnej ilości logów musi być z zakresu: 0 - 999");
                return false;
            }
        }
        return true;
    }
    function sync_options(element)
    {
        var nlogs = 0;
        if (document.forms['myroute_form'].cache_log[0].checked == true) {
            document.forms['myroute_form'].nrlogs.disabled = 'disabled';
            nlogs = 0;
        }
        else if (document.forms['myroute_form'].cache_log[1].checked == true) {
            document.forms['myroute_form'].nrlogs.disabled = false;
            nlogs = document.forms['myroute_form'].nrlogs.value;
        }
        document.forms['myroute_form'].logs.value = nlogs;
    }
//-->
</script>

<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/route.png" class="icon32" alt="" />&nbsp;{{caches_along_route}} ({number_caches}): <span style="color: black;font-size:13px;">{routes_name} ({{radius}} {distance} km)</span></div>
<div class="searchdiv">
    <form action="myroutes_search.php" method="post" enctype="multipart/form-data" name="myroute_form" dir="ltr">
        <input type="hidden" name="routeid" value="{routeid}"/>
        <input type="hidden" name="distance" value="{distance}"/>
        <input type="hidden" name="logs" value=""/>

        <table border="0" cellspacing="2" cellpadding="1" style="margin-left: 10px; line-height: 1.4em; font-size: 13px;" width="95%">
            <tr>
                <td ><strong>{{date_hidden_label}}</strong></td>
                <td style="width: 22px;"><img src="images/rating-star.png" border="0" alt="Recomended" title="Recomended"/></td>
                <td style="width: 22px;">&nbsp;</td>
                <td ><strong>Geocache</strong></td>
                <td><strong>{{owner}}</strong>&nbsp;&nbsp;&nbsp;</td>
                <td colspan="3"><strong>{{latest_logs}}</strong></td>
            </tr>
            <tr>
                <td colspan="8"><hr></hr></td>
            </tr>
            {file_content}
            <tr>
                <td colspan="8"><hr></hr></td>
            </tr>
        </table>
</div>
<br/>
{list_empty_start}
<div class="searchdiv">
    <table border="0" cellspacing="2" cellpadding="1" style="margin-left: 10px; line-height: 1.4em; font-size: 13px;" width="95%">
        <tr>
            <td class="content-title-noshade" style="font-size:14px;">{{logs_cache_gpx}}:</td></tr>
        <tr>
            <td class="content-title-noshade" style="font-size:12px;" colspan="2">
                <input type="radio" name="cache_log" value="0" tabindex="0" id="l_all_logs_caches" class="radio" onclick="javascript:sync_options(this)" {all_logs_caches} /> <label for="l_all_logs_caches">{{show_all_log_entries}}</label>&nbsp;
                <input type="radio" name="cache_log" value="1" tabindex="1" id="l_minl_caches" class="radio" onclick="javascript:sync_options(this)" {min_logs_caches} /> <label for="l_minl_caches">{{min_logs_cache}}</label>&nbsp;
                <input type="text" name="nrlogs" value="{nrlogs}" maxlength="3" class="input50" onchange="javascript:sync_options(this)" {min_logs_caches_disabled}/>
            </td>
        </tr>
    </table>
</div>
{list_empty_end}
<br/>
<button type="submit" name="back" value="back" style="font-size:12px;width:160px"><b>{{back}}</b></button>&nbsp;&nbsp;
{list_empty_start}
<button type="submit" name="submit_gpx" value="submit_gpx" style="font-size:12px;width:160px"><b>{{save_gpx}}</b></button>
<button type="submit" name="submit_gpx_with_photos" value="submit_gpx_with_photos" style="font-size:12px;width:160px"><b>{{save_gpx_with_photos}}</b></button>
{list_empty_end}
<br/><br/><br/>
</form>
