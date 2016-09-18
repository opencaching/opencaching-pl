<?php

?>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/profile.png" class="icon32" alt="" title="Login" align="middle"/>&nbsp;{{login}}</div>
{message_start}
{message}
{message_end}
<form action="login.php" method="post" enctype="application/x-www-form-urlencoded" name="login_form" dir="ltr" style="display: inline;">
    <input type="hidden" name="target" value="{target}" />
    <input type="hidden" name="action" value="login" />
    <table class="table">
        <colgroup>
            <col width="150" />
            <col />
        </colgroup>
        <tr class="form-group-sm">
            <td class="content-title-noshade">{{user_or_email}}:</td>
            <td><input name="email" maxlength="80" type="text" value="{username}" class="form-control input150" /></td>
        </tr>
        <tr class="form-group-sm">
            <td class="content-title-noshade">{{password}}:</td>
            <td><input name="password" maxlength="60" type="password" value="" class="form-control input150" /></td>
        </tr>
    </table>
    <input type="reset" name="reset" value="{{reset}}" class="btn btn-default" />&nbsp;&nbsp;
    <input type="submit" name="LogMeIn" value="{{login}}" class="btn btn-primary" />
</form>
<p class="content-title-noshade">{{not_registered}}<br />

    {{forgotten_your_password}}<br />

    {{forGottenEmailAddress}}</p>
