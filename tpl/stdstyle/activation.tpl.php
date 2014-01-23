<form action="activation.php" method="post" enctype="application/x-www-form-urlencoded" style="display: inline;">
<input type="hidden" name="submit" value="1" />
<table class="content">
    <colgroup>
        <col width="150">
        <col>
    </colgroup>
    <tr>
        <td class="content2-pagetitle" colspan="2">
            <img src="tpl/stdstyle/images/blue/profile.png" border="0" align="middle" width="32" height="32" alt=""  /><font size="4"> <b>{{account_activation}}</b></font>
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>

    {message_start}<tr><td colspan="2" class="message">{message}</td></tr><tr><td class="spacer" colspan="2"></td></tr>{message_end}

    <tr>
        <td colspan="2" class="help">
            <img src="tpl/stdstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="Uwaga" title="Uwaga" align="middle" />
            {{finish_registration_hint}}
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr>
        <td>{{email_address}}:</td>
        <td><input type="text" name="email" maxlength="60" value="{email}" class="input200" /> {email_message}</td>
    </tr>
    <tr>
        <td>{{activation_code}}:</td>
        <td><input type="text" name="code" maxlength="20" value="{code}" class="input200" /></td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr><td class="spacer" colspan="2"></td></tr>

    <tr>
        <td class="header-small" colspan="2">
            <input type="reset" name="reset" value="{{reset}}" class="formbuttons" />&nbsp;&nbsp;
            <input type="submit" name="submit" value="{{confirm}}" class="formbuttons" />
        </td>
    </tr>
    </table>
</form>
