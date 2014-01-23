<?php
/***************************************************************************
    *
    *   This program is free software; you can redistribute it and/or modify
    *   it under the terms of the GNU General Public License as published by
    *   the Free Software Foundation; either version 2 of the License, or
    *   (at your option) any later version.
    *
    *  UTF-8 ąść
    ***************************************************************************/
?>
<script type="text/javascript">
<!--
    function checkForm()
    {
        if(document.newpic_form.title.value == "")
        {
            alert("Proszę nadać nazwę obrazkowi!");
            return false;
        }

        if(document.newpic_form.file.value == "")
        {
            alert("Proszę podać źródło obrazka!");
            return false;
        }

        return true;
    }
    //-->
</script>
<form action="newpic-gps.php" method="post" enctype="multipart/form-data" name="newpic_form" dir="ltr" onsubmit="return checkForm();">
<input type="hidden" name="objectid" value="{objectid}" />
<input type="hidden" name="type" value="{type}" />
<table class="content">
    <colgroup>
        <col width="100">
        <col>
    </colgroup>
    <tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/picture.png" class="icon32" alt="" title="Neuw Cache" align="middle" /> <b>{pictypedesc} <a href="/viewcache.php?cacheid={cacheid}">{cachename}</a></b></td></tr>
    <tr><td class="spacer" colspan="2"><br /><font color="red"><b>Umieszczaj tylko istotne obrazki związane z skrzynką. Nie rób fotoalbumu okolicy.</b></font></td></tr>

    <tr>
        <td valign="top">Tytuł:</td>
        <td><input class="input200" name="title" type="text" value="{title}" size="43" /> {errnotitledesc}</td>
    </tr>

    <tr>
        <td valign="top">Nazwa pliku:</td>
        <td><input class="input200" name="file" type="file" maxlength="{maxpicsize}" /> {errnopicgivendesc}</td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>

    <tr>
        <td align="right"><input class="checkbox" type="checkbox" name="spoiler" value="1"{spoilerchecked}/></td>
        <td>Nie pokazuj miniatury (SPOILER) - użyj tej opcji jeśli wgrywasz zdjęcie na którym pokazane jest miejsce ukrycia skrzynki.
        Dopiero jak ktoś kliknie na SPOILER pokaże mu się wgrany obrazek. <td>
    </tr>
    {begin_cacheonly}
    <tr>
        <td align="right"><input class="checkbox" type="checkbox" name="notdisplay" value="1"{notdisplaychecked}/></td>
        <td>Tego zdjęcia nie pokazuj</td>
    </tr>
    {end_cacheonly}

    <tr><td class="spacer" colspan="2"></td></tr>
    <tr>
        <td class="help" colspan="2"><img src="tpl/stdstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="Uwaga" title="Uwaga" /> Nastpujące formaty są akceptowane: BMP, GIF, PNG i JPG - dla zdjęć rekomendowany format JPG .<br />Maksymalna wielkość pliku dozwolona 100 KB. Zalecane wymiary obrazków: 480 Pixel szerokość i 360 Pixel wysokość. <br />Po potwierdzeniu następna strona, która się pojawi może potrwać chwilę.</td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>

    <tr>
        <td class="header-small" colspan="2">
            <input type="submit" name="submit" value="{submit}" style="width:120px"/>
        </td>
    </tr>
  </table>
</form>
