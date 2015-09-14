<?php

?>
<table class="content">
    <tr><td class="header"><img src="tpl/stdstyle/images/misc/32x32-tools.png" class="icon32" alt="HTML-Vorschau" title="HTML-Vorschau" align="middle" /><font size="4">  <b>HTML Vorschau</b></font></td></tr>
    <tr><td class="spacer"></td></tr>
    <tr>
        <td>
            <b>Schritt 3:</b> So sieht dein HTML Code in einem Browser aus.<br />
            ---<br />
            {thecode}
            <br />---<br />
            <p>Hier nochmals der HTML Code, den du nun abspeichern kannst oder direkt als Beschreibung eingeben.
                <br />---<br />
                {thehtmlcode}
                <br />---</p>
            <form action="htmlprev.php" name="text2html" method="post" enctype="application/x-www-form-urlencoded">
                <input type="hidden" name="thetext" value="{thetext}" />
                <input type="hidden" name="thehtml" value="{orghtml}" />
                <input type="submit" name="backStep2" value="Zurück" class="formbuttons"/>
            </form>
            </p>
        </td>
    </tr>
</table>
