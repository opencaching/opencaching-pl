<?php

?>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/stat1.png" border="0" align="middle" width="32" height="32" alt=""  />&nbsp;{{choose_statpic}}</div>
<p class="content-title-noshade-size2">{{statpic_previews}}:</p>
<div class="buffer"></div>
<table class="table">
    <form name="change" action="change_statpic.php" method="post" enctype="application/x-www-form-urlencoded"  style="display: inline;">

        <colgroup>
            <col width="200">
            <col>
        </colgroup>
        <tr>
            <td class="content-title-noshade">{{user_statpic_text}}:</td>
            <td>
                <input type="text" name="statpic_text" maxlength="30" value="{statpic_text}" class="input200"/>
                {{statpic_text_message}}
            </td>
        </tr>
        <tr><td class="buffer" colspan="2"></td></tr>
        {available_logos}
        <tr><td class="buffer" colspan="2"></td></tr>
        <tr>
            <td colspan="2">
                <input type="reset" name="reset" value="{{reset}}" class="btn btn-default"/>&nbsp;&nbsp;
                <input type="submit" name="submit" value="{{change}}" class="btn btn-primary"/>
            </td>
        </tr>
    </form>
</table>
