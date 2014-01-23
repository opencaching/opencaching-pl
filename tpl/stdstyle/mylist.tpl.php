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
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/colected.png" class="icon32" alt="" title="New Log entry" align="middle"/>&nbsp;{title_text}</div>


<div class="searchdiv">
            <table class="null" border="0" cellspacing="0">
                <colgroup>
                    <col>
                    <col width="10">
                    <col width="130">
                    <col width="90">
                </colgroup>
                <tr>
                    <td class="header-small">{{cache_label}}</td>
                    <td class="header-small">&nbsp;</td>
                    <td class="header-small" nowrap="nowrap">{{last_found}}</td>
                    <td class="header-small" nowrap="nowrap">&nbsp;</td>
                </tr>
                {list}
                {print_delete_list}
            </table>
</br><br/>
                {export_list}

</div>
