<?php

?>
<form action="remindemail.php" method="post" enctype="application/x-www-form-urlencoded" dir="ltr" style="display: inline;">
    <table class="content">
        <colgroup>
            <col width="150">
            <col>
        </colgroup>
        <tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/profile.png" class="icon32" alt="" title="Neuer Cache" align="middle" /><font size="4">  <b>{{ForgottenEmail_01}}</b></font></td></tr>
        <tr><td class="spacer" colspan="2"></td></tr>
        <tr>
            <td class="help" colspan="2">
                <img src="tpl/stdstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="Hinweis" title="Hinweis" align="middle" />

            </td>
        </tr>
        <tr><td class="spacer" colspan="2"></td></tr>

        <tr>
            <td>{{ForgottenEmail_02}}:</td>
            <td>
                <input name="username" type="text" value="{username}" maglength="60" class="input200"  /> {username_message}
            </td>
        </tr>
        <tr><td class="spacer" colspan="2"></td></tr>
        <tr><td class="spacer" colspan="2"></td></tr>
        <tr>
            <td width="150px">&nbsp;</td>
            <td>
                <input type="submit" name="submit" value="{{ForgottenEmail_03}}" class="formbuttons" />
            </td>
        </tr>
    </table>
</form>
{message}
