<?php
/* * *************************************************************************
  ./tpl/stdstyle/newemail.tpl.php
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

  Unicode Reminder ??

  template replacement(s):

  message
  email_message
  code_message
  new_email

 * ************************************************************************** */
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
            <tr>
                <td class="content-title-noshade">{{em02}}:</td>
                <td>
                    <input name="newemail" maxlength="60" type="text" value="{new_email}" class="input200" /> {email_message}
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <input type="submit" name="submit_getcode" value="{getcode}" class="formbuttons" />
                </td>
            </tr>
        </table>
        <div class="notice">{{em03}}.</div>
        <table class="table">
            <colgroup>
                <col width="150px">
                <col>
            </colgroup>
            <tr>
                <td class="content-title-noshade">{{em04}}:</td>
                <td>
                    <input name="code" maxlength="60" type="text" value="" class="input100" />{code_message}
                </td>
            </tr>
        </table>
        <div class="buffer"></div>
        <input type="reset" name="reset" value="{{reset}}" class="formbuttons" />&nbsp;&nbsp;
        <input type="submit" name="submit_changeemail" value="{change_email}" class="formbuttons" />
    </form>
</div>
