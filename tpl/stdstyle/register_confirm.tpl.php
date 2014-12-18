<?php
/* * *************************************************************************
  ./tpl/stdstyle/register_confirm.tpl.php
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

  register confirmation page

  template replacement(s):
  email
  username
  country

 * ************************************************************************** */
?>
<table class="content">
    <colgroup>
        <col width="150">
        <col>
    </colgroup>
    <tr>
        <td class="content2-pagetitle" colspan="2">
            <img src="tpl/stdstyle/images/blue/profile.png" border="0" align="middle" width="32" height="32" alt="" title="" align="middle" /><font size="4"> <b>{{register_msg5}}</b></font>
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>

    <tr>
        <td colspan="2" class="help">
            <div class="notice">
                {{register_msg6}}
            </div>
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>

    <tr>
        <td><img src="tpl/stdstyle/images/free_icons/user.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{user}}:</td>
        <td>{username}</td>
    </tr>

    <tr>
        <td><img src="tpl/stdstyle/images/free_icons/email.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{email_address}}:</td>
        <td><b>{email}</b></td>
    </tr>

    <tr>
        <td><img src="tpl/stdstyle/images/free_icons/world.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{country_label}}:</td>
        <td>{country}</td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>

</table>
