<?php
/***************************************************************************
                                            ./tpl/stdstyle/viewquery.tpl.php
                                                            -------------------
        begin                : November 4 2005
        copyright            : (C) 2005 The OpenCaching Group
        forum contact at     : http://www.opencaching.com/phpBB2

   Unicode Reminder ??

    ***************************************************************************/
?>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/colected.png" class="icon32" alt="" />&nbsp;{{collected_queries}}</div>
<div class="searchdiv">
<table class="table" width="740">
    <colgroup>
        <col width="250"/>
        <col width="150"/>
        <col width="40"/>
    </colgroup>
    <tr>
        <td class="content-title-noshade-size2">{{name_label}}</td>
        <td class="content-title-noshade-size2" >{{download}}</td>
        <td class="content-title-noshade-size2" style="text-align: center" >{{delete}}</td>
    </tr>
        {queries}
</table>
</div>
<div class="notice">{{accept_terms_of_use}}</div>
