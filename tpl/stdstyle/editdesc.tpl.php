<?php

?>
<script type="text/javascript" src="lib/tinymce4/tinymce.min.js"></script>
<script src="tpl/stdstyle/js/jquery-2.0.3.min.js"></script>
<script type="text/javascript">
    tinymce.init({
        selector: "#desc",
        menubar: false,
        toolbar_items_size: "small",
        browser_spellcheck: true,
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

<form name="descform" action="editdesc.php" method="post" enctype="application/x-www-form-urlencoded" id="editdesc_form" dir="ltr">
    <input type="hidden" name="post" value="1"/>
    <input type="hidden" name="descid" value="{descid}"/>
    <input type="hidden" name="show_all_langs_value" value="{show_all_langs_value}"/>
    <div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/describe.png" class="icon32" alt="">&nbsp;{{edit_cache_description}} <a href="viewcache.php?cacheid={cacheid}">{cachename}</a></div>
    <table class="table">
        <tr class="form-group-sm">
            <td class="content-title-noshade">{{language}}:</td>
            <td class="options">
                <select name="desclang" class="form-control input120">
                    {desclangs}
                </select>{show_all_langs_submit}
            </td>
        </tr>
        <tr class="form-group-sm">
            <td class="content-title-noshade">{{short_description}}:</td>
            <td class="options"><input type="text" name="short_desc" maxlength="120" value="{short_desc}" class="form-control input400"/></td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="notice">{{short_desc_long_text}}</div>
            </td>
        </tr>
    </table>
    <div class="buffer"></div>
    <div class="content-title-noshade-size12">{{full_description}}:{desc_err}</div>
    <div class="buffer"></div>
    <p id="scriptwarning" class="errormsg">{{javascript_edit_info}}</p>
    <div class="content2-container"><textarea id="desc" name="desc" class="desc">{desc}</textarea></div>
    <div class="notice">{{html_usage}} <a href="articles.php?page=htmltags" target="_blank">{{available_html}}</a></div>
    <div class="notice">{{geocaching_com_foto_info}}</div>
    <div class="buffer"></div>
    <div class="content-title-noshade-size12">{{hint_info}}:</div>
    <div class="buffer"></div>
    <div class="content2-container"><textarea name="hints" class="hint">{hints}</textarea></div>
    <div class="notice">{{hint_long_text}}</div>
    <div class="notice">{{hint_instructions}}</div>
    <div class="buffer"></div>
    <div>
        <input type="reset" name="reset" value="{{reset}}" class="btn btn-default"/>&nbsp;&nbsp;
        <input type="submit" name="submitform" value="{{submit}}" class="btn btn-primary"/>
    </div>
</form>
