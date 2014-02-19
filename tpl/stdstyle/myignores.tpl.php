<?php

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

     wachtes of this user

 ****************************************************************************/
?>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/colected.png" class="icon32" alt="{title_text}" title="{title_text}" align="middle" />&nbsp;{title_text}</div>
<div class="searchdiv">
<table class="table">
    <colgroup>
        <col width="650px"/>
        <col width="1x"/>
        <col width="40px"/>
    </colgroup>
    <tr>
            <td class="content-title-noshade">Geocache</td>
            <td>&nbsp;</td>
            <td style="text-align: center" class="content-title-noshade">{{off_ignore}}</td>
    </tr>
    {ignores_caches}
</table>
{no_ignores}
</div>

