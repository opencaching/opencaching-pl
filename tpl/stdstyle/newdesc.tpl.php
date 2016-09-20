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



<form action="newdesc.php" method="post" enctype="application/x-www-form-urlencoded" name="newdesc_form" dir="ltr">
    <input type="hidden" name="cacheid" value="{cacheid}"/>
    <input type="hidden" name="show_all_langs" value="{show_all_langs}"/>
    <div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/describe.png" class="icon32" alt="" align="middle" />&nbsp;{{make_new_description}} <a href="viewcache.php?cacheid={cacheid}">{cachename}</a></div>
    <table class="table">
        <colgroup>
            <col width="100">
            <col>
        </colgroup>
        <tr  class="form-group-sm">
            <td class="content-title-noshade">{{language}}:</td>
            <td>
                <select name="desc_lang" class="form-control input120">
                    {langoptions}
                </select>
                {show_all_langs_submit} {lang_message}
            </td>
        </tr>
        <tr><td class="buffer" colspan="2"></td></tr>

        <tr  class="form-group-sm">
            <td class="content-title-noshade">{{short_description}}:</td>
            <td><input type="text" name="short_desc" maxlength="120" value="{short_desc}" class="form-control input400"/></td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="notice" style="width:500px;min-height:24px;height:auto;">{{short_desc_long_text}}</div>
            </td>
        </tr>
    </table>
    <div class="buffer"></div>
    <div>
        <p class="content-title-noshade-size1">{{full_description}}:{desc_err}</p>
    </div>
    <div class="buffer"></div>
    <p id="scriptwarning" class="errormsg">{{javascript_edit_info}}</p>
    <p><textarea id="desc" name="desc" class="descMCE" cols="80" rows="15">{desc}</textarea></p>
    <div class="buffer"></div>
    <div class="notice">{{html_usage}} <a href="articles.php?page=htmltags" target="_blank">{{available_html}}</a></div>
    <div class="notice">{{geocaching_com_foto_info}}</div>
    <div class="buffer"></div>
    <div><p class="content-title-noshade-size1">{{hint_info}}:</p></div>
    <div class="buffer"></div>
    <div><textarea name="hints" class="hint mceNoEditor" cols="80" rows="3">{hints}</textarea></div>
    <div class="notice">{{hint_long_text}}</div>
    <div class="notice">{{hint_instructions}}</div>
    <div class="buffer"></div>
    <div>
        <input type="reset" name="reset" value="{{reset}}" class="btn btn-default"/>&nbsp;&nbsp;
        <input type="submit" name="submitform" value="{{submit}}" class="btn btn-primary"/>
    </div>
    <div class="buffer"></div>
</form>

