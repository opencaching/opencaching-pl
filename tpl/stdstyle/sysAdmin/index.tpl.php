<?php
use Utils\Uri\SimpleRouter;
?>
<h1>Sysadmin panels</h1>
<div>
  <ul>
    <li><h3><a href="<?=SimpleRouter::getLink('sys','apc')?>">APC</a></h3></li>
    <li><h3><a href="<?=SimpleRouter::getLink('sys','phpinfo')?>">PHP-INFO</a></h3></li>
  </ul>
</div>