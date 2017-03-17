<?php

?>
<script type="text/javascript" src="lib/js/wz_tooltip.js"></script>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="" title="" align="middle"/>&nbsp;{{my_caches_status}}: <font color="black">{cache_stat}</font></div>


<div>
    <div class="btn-group btn-group-justified">
            <a class="btn btn-default btn-sm" href="mycaches.php?status=1">{{active}} ({activeN})</a>
            <a class="btn btn-default btn-sm" href="mycaches.php?status=2">{{temp_unavailable}} ({unavailableN})</a>
            <a class="btn btn-default btn-sm" href="mycaches.php?status=3">{{archived}} ({archivedN})</a>
            <a class="btn btn-default btn-sm" href="mycaches.php?status=5">{{not_published}} ({notpublishedN})</a>
            <a class="btn btn-default btn-sm" href="mycaches.php?status=4">{{for_approval}} ({approvalN})</a>
            <a class="btn btn-default btn-sm" href="mycaches.php?status=6">{{blocked}} ({blockedN})</a>
    </div>
</div>
    
<p>&nbsp;</p>
<div class="searchdiv">
    <table border="0" cellspacing="2" cellpadding="1" style="margin-left: 10px; line-height: 1.4em; font-size: 13px;" width="95%">
        <tr>
            <td colspan="2"><a class="links" href="mycaches.php?col=1{my_cache_sort}">{{date_hidden_label}}</a></td>
            <td></td>
            <td><a class="links" href="mycaches.php?col=2{my_cache_sort}">{{geocache}}</a></td>
            <td align="middle"><a class="links" href="mycaches.php?col=3{my_cache_sort}" alt="{{mc_by_founds}}" title="{{mc_by_founds}}"><img src="tpl/stdstyle/images/log/16x16-found.png"></a></td>
            <td align="middle"><a class="links" href="mycaches.php?col=4{my_cache_sort}" alt="{{mc_by_reco}}" title="{{mc_by_reco}}"><img src="images/rating-star.png"></a></td>
            <td align="middle"><a class="links" href="mycaches.php?col=6{my_cache_sort}" alt="{{mc_by_gk}}" title="{{mc_by_gk}}"><img src="images/gk.png"></a></td>
            <td align="middle"><a class="links" href="mycaches.php?col=7{my_cache_sort}" alt="{{mc_by_visits}}" title="{{mc_by_visits}}"><img src="tpl/stdstyle/images/free_icons/vcard.png"></a></td>
            <td><a class="links" href="mycaches.php?col=5{my_cache_sort}">{col5_header}</a></td>
            <td><strong>{{latest_logs}}</strong></td>
        </tr>
        <tr><td colspan="10"><hr></hr></td></tr>
        {file_content}
        <tr><td colspan="10"><hr></hr></td></tr>
    </table>
</div>
<p>
    {pages}
</p>

