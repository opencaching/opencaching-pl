<?php

?>

<script type="text/javascript" src="lib/js/wz_tooltip.js"></script>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="" />&nbsp;{{mycache_note}} </div>



<div class="searchdiv">
    <table class="table" border="0" cellspacing="0">
        <colgroup>
            <col width="10px"/>
            <col width="490px"/>
            <col width="1x"/>
            <col width="40px"/>
            <col width="1x"/>
            <col width="40px" />
        </colgroup>

        <tr>
            <td><p class="content-title-noshade"></p></td>
            <td><p class="content-title-noshade">{{geocache}}</p></td>
            <td>&nbsp;</td>
            <td nowrap="nowrap" ><p class="content-title-noshade">{{coordsmod_info_01}}</p></td>
            <td nowrap="nowrap" class="content-title-noshade">{{last_log_entries}}</td>
            <td>&nbsp;</td>
            <td nowrap="nowrap" class="content-title-noshade" style = "text-align: center" >{{delete}}</td>
        </tr>
        {notes_content}
    </table>
</div>


<div class="notice noprint">{{mycache_notes}} <a href="{wiki_link_cacheNotes}" target="_blank">{{here}}</a></div>




