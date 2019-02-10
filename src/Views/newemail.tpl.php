<?php

?>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/email.png" class="icon32" alt="E-Mail-Adres" title="E-Mail-Adres" align="middle" />&nbsp;{{em00}}</div>
<div class="searchdiv">
    {message}
    <div class="notice">
        {{em01}}.
    </div>

    <form action="newemail.php" method="post" enctype="application/x-www-form-urlencoded" name="forgot_pw_form" dir="ltr" style="display: inline;">
        <table class="table">
            <colgroup>
                <col width="150px">
                <col>
            </colgroup>
            <tr class="form-group-sm">
                <td class="content-title-noshade">{{em02}}:</td>
                <td>
                    <input name="newemail" maxlength="60" type="text" value="{new_email}" class="form-control input200" /> {email_message}
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <input type="submit" name="submit_getcode" value="{getcode}" class="btn btn-primary btn-sm" />
                </td>
            </tr>
        </table>
        <div class="notice">{{em03}}.</div>
        <table class="table">
            <colgroup>
                <col width="150px">
                <col>
            </colgroup>
            <tr class="form-group-sm">
                <td class="content-title-noshade">{{em04}}:</td>
                <td>
                    <input name="code" maxlength="60" type="text" value="" class="form-control input100" />{code_message}
                </td>
            </tr>
        </table>
        <div class="buffer"></div>
        <input type="reset" name="reset" value="{reset}" class="btn btn-default" />&nbsp;&nbsp;
        <input type="submit" name="submit_changeemail" value="{change_email}" class="btn btn-primary" />
    </form>
</div>
