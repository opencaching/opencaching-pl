<?php

?>

<div class="content2-pagetitle">
  <img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="">
  {{mc_beginn_00}}
</div>
<div class="callout callout-info">
  <div class="callout-title">{{nc01}},</div>
  {{nc02}}
  <ul class="callout-highlight">
    <li>{{nc03}}</li>
    <li>{{nc04}}</li>
    <li>{{nc05}}</li>
    <li>{{nc06}}</li>
    <li>{{nc07}}</li>
    <li>{{nc11}} <a class="links" href="{wiki_link_rules}">{{nc12}} <img src="tpl/stdstyle/images/misc/linkicon.png" alt="link"></a>?</li>
  </ul>
  {{nc13}} <a class="links" href="{wiki_link_placingCache}">{{nc14}} <img src="tpl/stdstyle/images/misc/linkicon.png" alt="link"></a>
  {{nc15}} <a class="links" href="{wiki_link_cachingCode}">{{nc16}} <img src="tpl/stdstyle/images/misc/linkicon.png" alt="link"></a> {{nc17}}.<br>
  {{nc08}} <a class="links" href="/cacheguides.php">{{nc09}} <img src="tpl/stdstyle/images/misc/linkicon.png" alt="link"></a>, {{nc10}}.
  <div class="buffer"></div>
  {{nc18}}<br><b>{{nc19}}</b>
</div>
<div class="align-center">
  <form action="newcache.php" method="post" enctype="application/x-www-form-urlencoded" name="newcacheform" dir="ltr">
    <input type="hidden" name="newcache_info" value="0"/>
    <button class="btn btn-primary" type="submit">{{nc20}}</button>
  </form>
</div>