<?php

?>
<script type="text/javascript" src="lib/tinymce4/tinymce.min.js"></script>
<script src="tpl/stdstyle/js/jquery-2.0.3.js"></script>
<script type="text/javascript">
    tinymce.init({
        selector: "#desc",
        width: 600,
        height: 350,
        menubar: false,
        toolbar_items_size: 'small',
        gecko_spellcheck: true,
        relative_urls: false,
        remove_script_host: false,
        entity_encoding: "raw",
        language: "{language4js}",
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


<form name="descform" action="editdesc.php" method="post" enctype="application/x-www-form-urlencoded" id="editcache_form" dir="ltr">
    <input type="hidden" name="post" value="1"/>
    <input type="hidden" name="descid" value="{descid}"/>
    <input type="hidden" name="show_all_langs_value" value="{show_all_langs_value}"/>
    <div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/describe.png" class="icon32" alt="" />&nbsp;{{edit_cache_description}} <a href="viewcache.php?cacheid={cacheid}">{cachename}</a></div>
    <table class="table">
        <colgroup>
            <col width="100"/>
            <col/>
        </colgroup>
        <tr>
            <td class="content-title-noshade">{{language}}:</td>
            <td>
                <select name="desclang">
                    {desclangs}
                </select>{show_all_langs_submit}
            </td>
        </tr>
        <tr><td class="buffer" colspan="2"></td></tr>
        <tr>
            <td class="content-title-noshade">{{short_description}}:</td>
            <td><input type="text" name="short_desc" maxlength="120" value="{short_desc}" class="input400"/></td>
        </tr>
        <tr><td class="buffer" colspan="2"></td></tr>
    </table>
    <div class="buffer"></div>
    <div>
        <p class="content-title-noshade-size1">{{full_description}}:{desc_err}</p>
    </div>
    <p id="scriptwarning" class="errormsg">{{javascript_edit_info}}</p>
    <p><textarea id="desc" name="desc" class="descMCE" cols="80" rows="15">{desc}</textarea></p>
    <div class="buffer"></div>
    <div class="notice">{{html_edit_info}}</div>
    <div class="notice">{{geocaching_com_foto_info}}</div>
    <div class="buffer"></div>
    <div><p class="content-title-noshade-size1">{{extra_coded_info}}:</p></div>
    <div class="buffer"></div>
    <div><textarea name="hints" class="mceNoEditor" cols="80" rows="15">{hints}</textarea></div>
    <div class="buffer"></div>
    <div>
        <input type="reset" name="reset" value="{{reset}}" class="formbuttons"/>&nbsp;&nbsp;
        <input type="submit" name="submitform" value="{{submit}}" class="formbuttons"/>
    </div>
    <div class="buffer"></div>
</form>

