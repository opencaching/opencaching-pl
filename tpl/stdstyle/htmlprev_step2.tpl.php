<?php
/***************************************************************************
                                            ./tpl/stdstyle/htmlprev_step2.tpl.php
                                                            -------------------
        begin                : October 1 2004
        copyright            : (C) 2004 The OpenCaching Group
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

     HTML generation and preview

 ****************************************************************************/
?>
<table class="content">
    <tr><td class="header"><img src="tpl/stdstyle/images/misc/32x32-tools.png" class="icon32" alt="HTML-Vorschau" title="HTML-Vorschau" align="middle" /><font size="4">  <b>HTML Vorschau</b></font></td></tr>
    <tr><td class="spacer"></td></tr>
    <tr>
    <td>
        <b>Schritt 2:</b> Hier siehst du den HTML Code deines Textes. Wenn du ein Wort oder Satz <b>fett</b> schreiben möchtest,
        füge vor den Satz ein &lt;b&gt; und hinter den Satz ein &lt;/b&gt;.
            <form action="htmlprev.php" name="text2html" method="post" enctype="application/x-www-form-urlencoded">
                <input type="hidden" name="thetext" value="{thetext}"/>
                <table>
                    <tr>
                        <td colspan="2">
                            <textarea name="thehtml" class="logs">{thehtml}</textarea>
                        </td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td alignment="left" width="50%">
                            <input type="submit" name="backStep1" value="Zurück" class="formbuttons"/>
                        </td>
                        <td align="right" width="50%">
                            <input type="submit" name="toStep3" value="Vorschau" class="formbuttons"/>
                        </td>
                    </tr>
                </table>
            </form>
    </td>
    </tr>
</table>
