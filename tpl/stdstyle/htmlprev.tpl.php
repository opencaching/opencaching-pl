<?php
/* * *************************************************************************
  ./tpl/stdstyle/htmlprev.tpl.php
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
            Du willst in deiner Cachebeschreibung einen Teil des Textes <b>fett</b> oder <i>kursiv</i> machen,
            hast jedoch keine Ahnung von HTML?<br />
            <p>Kein Problem, mit dieser Anleitung wird das zum Kinderspiel!</p>
            <p><b>Schritt 1:</b> Als erstes musst du in das folgende Textfeld deinen Text eingeben und
                auf Weiter klicken.
            <form action="htmlprev.php" name="text2html" method="post" enctype="application/x-www-form-urlencoded">
                <table>
                    <tr>
                        <td colspan="2">
                            <textarea name="thetext" class="logs">{thetext}</textarea>
                        </td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td alignment="left" width="50%">
                        </td>
                        <td align="right" width="50%">
                            <input type="submit" name="toStep2" value="Weiter" class="formbuttons">
                        </td>
                    </tr>
                </table>
            </form>
            </p>
        </td>
    </tr>
</table>
