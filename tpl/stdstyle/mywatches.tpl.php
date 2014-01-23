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

   Unicode Reminder ăĄă˘

     wachtes of this user

 ****************************************************************************/
?>
<script type="text/javascript" src="lib/js/wz_tooltip.js"></script>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/search1.png" class="icon32" alt="{title_text}" title="{title_text}" align="middle" />&nbsp;{title_text}</div>
<div class="searchdiv">
<table class="table" border="0" cellspacing="0">
    <colgroup>
        <col width="500px"/>
        <col width="1x"/>
        <col width="40px"/>
        <col width="1x"/>
        <col width="40px" />
    </colgroup>

    <tr>
        <td><p class="content-title-noshade">{{geocache}}</p></td>
        <td>&nbsp;</td>
        <td nowrap="nowrap" class="content-title-noshade">{{last_log_entries}}</td>
        <td>&nbsp;</td>
        <td nowrap="nowrap" class="content-title-noshade" style = "text-align: center" >{{delete}}</td>
    </tr>
    {watches}
    {print_delete_all_watches}
    {export_all_watches}
</table>
</div>
