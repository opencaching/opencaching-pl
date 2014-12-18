<?php
/* * *************************************************************************
  ./tpl/stdstyle/newpw.tpl.php
  -------------------
  begin                : Mon June 14 2004
  copyright            : (C) 2004 The OpenCaching Group
  forum contact at     : http://www.opencaching.com/phpBB2

 * ************************************************************************* */

/* * *************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 * ************************************************************************* */

/* * **************************************************************************

  Unicode Reminder ăĄă˘

  login page

  template replacement(s):

  message         message to display the user
  email           entered email
  code     entered code
  email_message   messages relating to the email
  code_message    messages relating to the code
  pw_message      messages relating to the new password
  changepw        submit string
  getcode         submit string

 * ************************************************************************** */
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