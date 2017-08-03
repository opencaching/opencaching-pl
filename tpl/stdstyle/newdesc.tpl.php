<?php
$view->callChunk('tinyMCE');
?>
<form action="newdesc.php" method="post" enctype="application/x-www-form-urlencoded" name="newdesc_form" dir="ltr">
    <input type="hidden" name="cacheid" value="{cacheid}"/>
    <input type="hidden" name="show_all_langs" value="{show_all_langs}"/>
    <div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/describe.png" class="icon32" alt="">&nbsp;{{make_new_description}} <a href="viewcache.php?cacheid={cacheid}">{cachename}</a></div>
    <table class="table">
        <tr class="form-group-sm">
            <td class="content-title-noshade">{{language}}:</td>
            <td class="options">
                <select name="desc_lang" class="form-control input120">
                    {langoptions}
                </select>
                {show_all_langs_submit} {lang_message}
            </td>
        </tr>
        <tr  class="form-group-sm">
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
    <div class="content2-container"><textarea id="desc" name="desc" class="tinymce desc">{desc}</textarea></div>
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
<script type="text/javascript">
  document.getElementById('scriptwarning').style.display = 'none';
</script>