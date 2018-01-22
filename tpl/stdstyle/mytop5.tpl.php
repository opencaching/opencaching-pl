<?php

?>
<div class="content2-pagetitle">
    <img src="tpl/stdstyle/images/blue/recommendation.png" class="icon32" alt="{{my_recommendations}}" title="{{my_recommendations}}" align="middle"/>
    &nbsp;{{my_recommendations}}
</div>
{msg_delete}

<div class="searchdiv">
    <table class = "table">
        <colgroup>
            <col width="10px"/>
            <col width="500px"/>
            <col width="1px"/>
            <col width="140px"/>
            <col width="1px"/>
            <col width="60px"/>
        </colgroup>
        <tr>
            <td class="content-title-noshade"></td>
            <td class="content-title-noshade">{{geocache}}</td>
            <td class="content-title-noshade">&nbsp</td>
            <td class="content-title-noshade">{{created_by}}</td>
            <td class="content-title-noshade"></td>
            <td class="content-title-noshade" style = "text-align: center" >{{delete}}</td>

        </tr>
        {top5}
    </table>
</div>
