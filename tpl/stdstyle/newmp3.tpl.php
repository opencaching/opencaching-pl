<?php
/* * *************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *  UTF-8 ąść
 * ************************************************************************* */
?>
<script type="text/javascript">
<!--
    function checkForm()
    {
        if (document.newmp3_form.title.value == "")
        {
            alert("{{newmp3_12}}");
            return false;
        }

        if (document.newmp3_form.file.value == "")
        {
            alert("{{newmp3_13}}");
            return false;
        }

        return true;
    }
    //-->
</script>
<form action="newmp3.php" method="post" enctype="multipart/form-data" name="newmp3_form" dir="ltr" onsubmit="return checkForm();">
    <input type="hidden" name="objectid" value="{objectid}" />
    <input type="hidden" name="type" value="{type}" />
    <input type="hidden" name="def_seq_m" value="{def_seq_m}" />
    <div class="searchdiv">
        <table class="content">
            <colgroup>
                <col width="100">
                <col>
            </colgroup>
            <tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/podcache-mp3.png" class="icon32" alt="" title="Cache" align="middle" /> <b>{mp3typedesc}: </b><font size="2"><a href="/viewcache.php?cacheid={cacheid}">{cachename}</a></font></td></tr>

            <tr><td class="spacer" colspan="2"><br /><br /></td></tr>
            <tr>
                <td valign="top">{{newmp3_14}}:</td>
                <td><input class="input200" name="title" type="text" value="{title}" size="43" /> {errnotitledesc}</td>
            </tr>

            <tr>
                <td valign="top">{{newmp3_15}}:</td>
                <td><input class="input200" name="file" type="file" maxlength="{maxmp3size}" /> {errnomp3givendesc}</td>
            </tr>
            <tr><td class="spacer" colspan="2"></td></tr>

            {begin_cacheonly}
            <tr>
                <td align="right"><input class="checkbox" type="checkbox" name="notdisplay" value="1"{notdisplaychecked}/></td>
                <td>{{newmp3_16}}</td>
            </tr>
            {end_cacheonly}

            <tr><td class="spacer" colspan="2"></td></tr>
            <tr>
                <td class="help" colspan="2"><img src="tpl/stdstyle/images/misc/16x16-info.png" border="0" alt="Uwaga" title="{{newmp3_17}}" /> {{newmp3_18}}</td>
            </tr>
            <tr><td class="spacer" colspan="2"></td></tr>

            <tr>
                <td class="header-small" colspan="2">
                    <input type="submit" name="submit" value="{submit}" style="width:120px"/>
                </td>
            </tr>
        </table>
</form>
</div>
