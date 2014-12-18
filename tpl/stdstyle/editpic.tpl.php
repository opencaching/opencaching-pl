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
        if (document.editpic_form.title.value == "")
        {
            alert("Proszę nadać nazwę obrazkowi!");
            return false;
        }

        if (document.editpic_form.file.value == "")
        {
            /*alert("Proszę podać źródło obrazka!");
             return false;*/
        }

        return true;
    }//-->
</script>

<form action="editpic.php" method="post" enctype="multipart/form-data" name="editpic_form" dir="ltr" onsubmit="return checkForm();">
    <input type="hidden" name="uuid" value="{uuid}" />
    <div class="searchdiv">
        <table class="content">
            <colgroup>
                <col width="100">
                <col>
            </colgroup>
            <tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/picture.png" class="icon32" alt="" title="edit picture" align="middle" /> <b>{pictypedesc} </b>&nbsp;<a href="/viewcache.php?cacheid={cacheid}">{cachename}</a></td></tr>
            <tr><td class="spacer" colspan="2"><br /></td></tr>

            <tr>
                <td valign="top">{{title_picture}}:</td>
                <td><input class="input200" name="title" type="text" value="{title}" size="43" /> {errnotitledesc}</td>
            </tr>

            <tr>
                <td valign="top">{{file_name}}:</td>
                <td><input class="input200" name="file" type="file" accept="image/jpeg,image/gif,image/png" maxlength="{maxpicsize}" /></td>
            </tr>
            <tr><td class="spacer" colspan="2"></td></tr>

            <tr>
                <td align="right"><input class="checkbox" type="checkbox" name="spoiler" value="1" {spoilerchecked}></td>
                <td>{{spoiler_info}}</td>
            </tr>
            {begin_cacheonly}
            <tr>
                <td align="right"><input class="checkbox" type="checkbox" name="notdisplay" value="1" {notdisplaychecked}></td>
                <td>{{dont_show}}</td>
            </tr>
            {end_cacheonly}

            <tr><td class="spacer" colspan="2"></td></tr>

            <tr>
                <td class="header-small" colspan="2">
                    <button type="submit" name="submit" value="submit" style="font-size:12px;width:120px"><b>{{submit}}</b></button>
                </td>
            </tr>
        </table>
</form>
</div>
