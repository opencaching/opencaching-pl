<?php

?>

<table class="content" border="0">
    <tr><td class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="" title="{{new_cache}}" align="middle" /><font size="4"><b>{{mc_beginn_00}}</b></font></td></tr>
    <tr><td class="spacer"></td></tr>
</table>
<br/>
<div class="callout callout-info">
    <div class="callout-title">{{nc01}},</div>
        {{nc02}}
        <ul class="callout-highlight">
        <li>{{nc03}}</li>
        <li>{{nc04}}</li>
        <li>{{nc05}}</li>
        <li>{{nc06}}</li>
        <li>{{nc07}}</li>
        <li>{{nc08}} <a class="links" href="/cacheguides.php">{{nc09}} <img src="tpl/stdstyle/images/misc/linkicon.png" alt="link"></a>, {{nc10}}?</li>
        <li>{{nc11}} <a class="links" href="{wiki_link_rules}">{{nc12}} <img src="tpl/stdstyle/images/misc/linkicon.png" alt="link"></a>?</li>
        </ul>
        {{nc13}} <a class="links" href="{wiki_link_placingCache}">{{nc14}} <img src="tpl/stdstyle/images/misc/linkicon.png" alt="link"></a>
        {{nc15}} <a class="links" href="{wiki_link_cachingCode}">{{nc16}}<img src="tpl/stdstyle/images/misc/linkicon.png" alt="link"></a> {{nc17}}.
        <br/><br/>{{nc18}}<br/><b>{{nc19}}</b>
</div>
<br/><br/>
<form action="newcache.php" method="post" enctype="application/x-www-form-urlencoded" name="newcacheform" dir="ltr"><input type="hidden" name="newcache_info" value="0"/>
    <center><button class="btn btn-primary" type ="submit">{{nc20}}</button>
    </center>
</form>
