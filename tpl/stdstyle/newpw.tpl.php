<?php

?>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/password.png" class="icon32" alt="" title="{{change_password_msg1}}" align="middle" />&nbsp;{{change_password_msg1}}</div>
<div class="searchdiv">
    {message}
    <p class="content-title-noshade-size2">{{step1}}</p>
    <div class="notice">
        {{new_password_msg1}}
    </div>
    <form action="newpw.php" method="post" enctype="application/x-www-form-urlencoded" name="newpw_form" dir="ltr" style="display: inline;">
        <input type="hidden" name="action" value="forgot_password" />
        <table class="table">
            <colgroup>
                <col width="150px">
                <col>
            </colgroup>
            <tr>
                <td><p class="content-title-noshade">{{email_address}}</p></td>
                <td>
                    <input name="email" type="text" value="{email}" maglength="60" class="input200"  /> {email_message}
                </td>
            </tr>
            <tr>
                <td width="150px">&nbsp;</td>
                <td>
                    <input type="submit" name="submit_getcode" value="{getcode}" class="formbuttons" />
                </td>
            </tr>
        </table>
    </form>
    <div class="buffer"></div>
    <p class="content-title-noshade-size2">{{step2}}</p>
    <div class="notice" style="height:44px;">
        {{new_password_msg2}}
    </div>
    <form action="newpw.php" method="post" enctype="application/x-www-form-urlencoded" name="newpw_form" dir="ltr" style="display: inline;">
        <input type="hidden" name="action" value="forgot_password" />
        <table class="table">
            <colgroup>
                <col width="150px">
                <col>
            </colgroup>
            <tr>
                <td><p class="content-title-noshade">{{email_address}}</p></td>
                <td>
                    <input name="email" type="text" value="{email}" maglength="60" class="input200"  /> {email_message}
                </td>
            </tr>
            <tr>
                <td><p class="content-title-noshade">{{security_code}}:</p></td>
                <td>
                    <input name="code" type="text" value="{code}" maglength="60" class="input200" />
                    {code_message}
                </td>
            </tr>
            <tr>
                <td><p class="content-title-noshade">{{new_password}}:</p></td>
                <td>
                    <input name="password" type="password" value="" maxlength="60" class="input120" /> {pw_message}
                </td>
            </tr>
            <tr>
                <td><p class="content-title-noshade">{{password_confirm}}:</p></td>
                <td>
                    <input name="rp_pass" type="password" value="" maxlength="60" class="input120" />
                </td>
            </tr>
            <tr>
                <td class="header-small" colspan="2">
                    <div class="buffer"></div>
                    <input type="reset" name="cancel" value="{reset}" class="formbuttons" />&nbsp;&nbsp;
                    <input type="submit" name="submit_changepw" value="{changepw}" class="formbuttons" />
                </td>
            </tr>
        </table>
    </form>
</div>
