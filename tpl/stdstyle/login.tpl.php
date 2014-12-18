<?php
/* * *************************************************************************
  ./tpl/stdstyle/login.tpl.php
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

  username      default username
  message       message to display the user
  target        page to display after login

 * ************************************************************************** */
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
        <tr>
            <td class="content-title-noshade">{{username_label}}:</td>
            <td><input name="email" maxlength="80" type="text" value="{username}" class="input150" /></td>
        </tr>
        <tr>
            <td class="content-title-noshade">{{password}}:</td>
            <td><input name="password" maxlength="60" type="password" value="" class="input150" /></td>
        </tr>
    </table>
    <input type="reset" name="reset" value="{{reset}}" class="formbuttons" />&nbsp;&nbsp;
    <input type="submit" name="LogMeIn" value="{{login}}" class="formbuttons" />
</form>
<p class="content-title-noshade">{{not_registered}}<br />

    {{forgotten_your_password}}<br />

    {{forGottenEmailAddress}}</p>
