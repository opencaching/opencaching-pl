<?php

?>
<table class="content">
    <colgroup>
        <col width="100">
        <col>
    </colgroup>
    <tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/recommendation.png" class="icon32" alt="Cache-Rekomendacja" title="Cache-Rekomendacja" align="middle" /> <b>{{recommended_geocaches}}</b></td></tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr><td class="header-small">Użytkownik rekomenduje skrzynke &quot;<a href="viewcache.php?cacheid={cacheid}">{cachename}</a>&quot;, oraz natępujące skrzynki rekomendował:</td></tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr>
        <td colspan="2">
            <table class="null" border="0" cellspacing="0">
                <tr>
                    <td class="header-small" width="50px">{{number_recommendations}}</td>
                    <td class="header-small" width="10px">&nbsp;</td>
                    <td class="header-small">{{name}}</td>
                </tr>
                {{recommendations}}
            </table>
        </td>
    </tr>

</table>
