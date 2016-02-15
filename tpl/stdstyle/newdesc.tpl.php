<?php

?>
<script type="text/javascript" src="lib/tinymce4/tinymce.min.js"></script>
<script src="tpl/stdstyle/js/jquery-2.0.3.min.js"></script>
<script type="text/javascript">
    tinymce.init({
        selector: "#desc",
        width: 600,
        height: 350,
        menubar: false,
        toolbar_items_size: 'small',
        language: "{language4js}",
        gecko_spellcheck: true,
        relative_urls: false,
        remove_script_host: false,
        entity_encoding: "raw",
        toolbar1: "newdocument | styleselect formatselect fontselect fontsizeselect",
        toolbar2: "cut copy paste | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image code | preview ",
        toolbar3: "bold italic underline strikethrough |  alignleft aligncenter alignright alignjustify | hr | subscript superscript | charmap emoticons | forecolor backcolor | nonbreaking ",
        plugins: [
            "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
            "table directionality emoticons template textcolor paste textcolor"
        ],
    });

    $(function () {
        $('#scriptwarning').hide();
    });

</script>



<form action="newdesc.php" method="post" enctype="application/x-www-form-urlencoded" name="newdescform" dir="ltr">
    <input type="hidden" name="cacheid" value="{cacheid}"/>
    <input type="hidden" name="show_all_langs" value="{show_all_langs}"/>
    <table class="content">
        <colgroup>
            <col width="100">
            <col>
        </colgroup>
        <tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/describe.png" class="icon32" alt="" title="New Cache" align="middle" /> <b>{{make_new_description}} <a href="viewcache.php?cacheid={cacheid}">{name}</a></b></td></tr>
        <tr><td class="spacer" colspan="2"></td></tr>
        <tr>
            <td>{{language}}:</td>
            <td>
                <select name="desc_lang">
                    {langoptions}
                </select>
                {show_all_langs_submit} {lang_message}
            </td>
        </tr>
        <tr><td class="spacer" colspan="2"></td></tr>

        <tr>
            <td>{{short_desc_label}}:</td>
            <td><input type="text" name="short_desc" maxlength="120" value="{short_desc}" class="input400" /></td>
        </tr>
        <tr><td class="spacer" colspan="2"></td></tr>
        <tr>
            <td colspan="2">{{full_description}}:</td>
        </tr>

        <tr>
            <td colspan="2">
                <span id="scriptwarning" class="errormsg">{{no_javascript}}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <textarea id="desc" name="desc" cols="80" rows="15">{desc}</textarea>
            </td>
        </tr>
        <tr><td class="spacer" colspan="2"></td></tr>
        <tr>
            <td class="help" colspan="2">
                <img src="tpl/stdstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="Uwagi" title="Uwagi" /> {{html_edit_info}}.<br />
                <img src="tpl/stdstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="Uwagi" title="Uwagi" />
                {{geocaching_com_foto_info}}
            </td>
        </tr>
        <tr><td class="spacer" colspan="2"></td></tr>

        <tr>
            <td colspan="2">{{extra_coded_info}}:</td>
        </tr>
        <tr>
            <td colspan="2">
                <textarea name="hints" class="hint mceNoEditor">{hints}</textarea>
            </td>
        </tr>
        <tr><td class="spacer" colspan="2"></td></tr>
        <tr><td class="spacer" colspan="2"></td></tr>

        <tr>
            <td class="header-small" colspan="2">
                <input type="reset" name="reset" value="{{reset}}" class="formbuttons" />&nbsp;&nbsp;
                <input type="submit" name="submitform" value="{submit}" class="formbuttons" />
            </td>
        </tr>
    </table>
</form>

