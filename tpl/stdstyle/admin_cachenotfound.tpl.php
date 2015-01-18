<?php
global $usr;
?>
<!--<form name="optionsform" style="display:inline;">-->
<form name="optionsform" style="display:inline;" action='admin_cachenotfound.php' method="GET">
    <table class="content" border="0" cellspacing="0" cellpadding="0">
        <tr><td class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt=""  /><font size="4">  <b>{{cache_notfound}}</b></font></td></tr>
        <tr><td class="spacer"></td></tr>
        <tr>
            <td>
                <input type="checkbox" name="show_reported" value="1" id="l_show_reported" class="checkbox" {show_reported} /> <label for="l_show_reported">{{adm_cachenotfound_01}}</label><br />
                <input type="checkbox" name="show_duplicated" value="1" id="l_show_duplicated" class="checkbox" {show_duplicated} /> <label for="l_show_duplicated">{{adm_cachenotfound_02}}</label><br />
                <input type="submit" value={{filter}} class="formbuttons" />
            </td>
        </tr>
        <tr>
            <td style="padding-left: 0px; padding-right: 0px;">
                <table border="0" cellspacing="0" cellpadding="0" class="null">
                    <tr>
                        <td width="18" height="13" bgcolor="#E6E6E6">#</td>
                        <td width="200" height="13" bgcolor="#E6E6E6"><b>{{name_label}}</b></td>
                        <td width="60" height="13" bgcolor="#E6E6E6"><b>{{adm_cachenotfound_03}}</b></td>
                        <td width="60" height="13" bgcolor="#E6E6E6"><b>{{adm_cachenotfound_04}}</b></td>
                    </tr>
                    <!--a-->{results}<!--z-->
                </table>
            </td>
        </tr>
    </table>
</form>
