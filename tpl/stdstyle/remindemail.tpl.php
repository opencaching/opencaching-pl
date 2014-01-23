<?php
/***************************************************************************
                                            ./tpl/stdstyle/remindemail.tpl.php
                                                            -------------------
        begin                : Wed September 21, 2005
        copyright            : (C) 2005 The OpenCaching Group
        forum contact at     : http://www.opencaching.com/phpBB2

    ***************************************************************************/

/***************************************************************************
    *
    *   This program is free software; you can redistribute it and/or modify
    *   it under the terms of the GNU General Public License as published by
    *   the Free Software Foundation; either version 2 of the License, or
    *   (at your option) any later version.
    *
    ***************************************************************************/

/****************************************************************************

   Unicode Reminder メモ

     template replacement(s):

 ****************************************************************************/
?>
<form action="remindemail.php" method="post" enctype="application/x-www-form-urlencoded" dir="ltr" style="display: inline;">
<table class="content">
    <colgroup>
        <col width="150">
        <col>
    </colgroup>
    <tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/profile.png" class="icon32" alt="" title="Neuer Cache" align="middle" /><font size="4">  <b>E-Mail-Adresse vergessen</b></font></td></tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr>
        <td class="help" colspan="2">
            <img src="tpl/stdstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="Hinweis" title="Hinweis" align="middle" />

        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>

    <tr>
        <td>Username:</td>
        <td>
            <input name="username" type="text" value="{username}" maglength="60" class="input200"  /> {username_message}
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr>
        <td width="150px">&nbsp;</td>
        <td>
        <input type="submit" name="submit" value="Bestätigen" class="formbuttons" />
        </td>
    </tr>
</table>
</form>
{message}
