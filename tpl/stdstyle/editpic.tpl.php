<?php

?>
<script type="text/javascript">
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
    }
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

            <tr class="form-group-sm">
                <td valign="top">{{title_picture}}:</td>
                <td><input class="form-control input200" name="title" type="text" value="{title}" size="43" /> {errnotitledesc}</td>
            </tr>

            <tr>
                <td valign="top">{{file_name}}:</td>
                <td>
                    <div class="form-inline">
                    <?php $view->callChunk('fileUpload','file', 'image/*', $view->maxPicSize ); ?>
                    </div>
                </td>
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
                    <button type="submit" name="submit" value="submit" class="btn btn-primary">{{submit}}</button>
                </td>
            </tr>
        </table>
</form>
</div>
