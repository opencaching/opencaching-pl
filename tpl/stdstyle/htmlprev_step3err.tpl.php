<?php
/* * *************************************************************************
  ./tpl/stdstyle/htmlprev_step3.tpl.php
  -------------------
  begin                : October 1 2004
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

  Unicode Reminder メモ

  HTML generation and preview

 * ************************************************************************** */
?>
<table class="content">
    <tr><td class="header"><img src="tpl/stdstyle/images/misc/32x32-tools.png" class="icon32" alt="HTML-Vorschau" title="HTML-Vorschau" align="middle" /><font size="4">  <b>HTML Vorschau</b></font></td></tr>
    <tr><td class="spacer"></td></tr>
    <tr>
        <td>
            Fehler: Der HTML Code ist nicht gültig.<br /><br />
            Details:<br />
            <p style="margin-top:0px;margin-left:15px;margin-right:20px;background-color:#e5e5e5;border:1px solid black;text-align:left;padding:3px 8px 3px 8px;">
                {errmsg}
            </p>
            <form action="htmlprev.php" name="text2html" method="post" enctype="application/x-www-form-urlencoded">
                <input type="hidden" name="thetext" value="{thetext}" />
                <input type="hidden" name="thehtml" value="{thehtml}" />
                <input type="submit" name="backStep2" value="Zurück" class="formbuttons"/>
            </form>
        </td>
    </tr>
</table>
