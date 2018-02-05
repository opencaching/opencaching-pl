
<script>
    function checkForm()
    {
        if (document.newpic_form.title.value == "")
        {
            alert("{{newpicjs01}}!");
            return false;
        }

        if (document.newpic_form.file.value == "")
        {
            alert("{{newpicjs02}}!");
            return false;
        }

        return true;
    }
</script>

<form action="newpic.php" method="post" enctype="multipart/form-data" name="newpic_form" dir="ltr" onsubmit="return checkForm();">
    <input type="hidden" name="objectid" value="{objectid}" />
    <input type="hidden" name="type" value="{type}" />
    <input type="hidden" name="def_seq" value="{def_seq}" />
    <table class="content">
        <colgroup>
            <col width="100">
            <col>
        </colgroup>
        <tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/picture.png" class="icon32" alt="" title="Neuw Cache" align="middle" /> <b>{pictypedesc} &nbsp;<a href="/viewcache.php?cacheid={cacheid}">{cachename}</a></b></td></tr>
        <tr><td class="spacer" colspan="2"><br /><font color="red"><b>{{cache_picture_intro}}</b></font></td></tr>

        <tr  class="form-group-sm">
            <td valign="top">{{title_picture}}:</td>
            <td><input class="form-control input200" name="title" type="text" value="{title}" size="43" /> {errnotitledesc}</td>
        </tr>

        <tr  class="form-group-sm">
            <td valign="top">{{file_name}}:</td>
            <td>
                <div class="form-inline">
                <?php $view->callChunk('fileUpload','file', 'image/*'); ?>
                </div>
                {errnopicgivendesc}
            </td>
        </tr>
        <tr><td class="spacer" colspan="2"></td></tr>

        <tr>
            <td align="right"><input class="checkbox" type="checkbox" name="spoiler" value="1"{spoilerchecked}/></td>
            <td>{{spoiler_info}}<td>
        </tr>
        {begin_cacheonly}
        <tr>
            <td align="right"><input class="checkbox" type="checkbox" name="notdisplay" value="1"{notdisplaychecked}/></td>
            <td>{{dont_show}}</td>
        </tr>
        {end_cacheonly}

        <tr><td class="spacer" colspan="2"></td></tr>
        <tr>
            <td class="help" colspan="2">
                <img src="tpl/stdstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="Uwaga" title="Uwaga" />
                {{pictures_intro}}
            </td>
        </tr>
        <tr><td class="spacer" colspan="2"></td></tr>

        <tr>
            <td class="header-small" colspan="2">
                <button type="submit" name="submit" value="submit" class="btn btn-primary">{submit}</button>
            </td>
        </tr>
    </table>
</form>
