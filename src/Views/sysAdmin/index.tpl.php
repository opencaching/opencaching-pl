<?php

use src\Utils\Uri\SimpleRouter;

?>
<h1>Sysadmin panels</h1>
<div>
    <ul>
        <li><h3><a href="<?= SimpleRouter::getLink('sys', 'apc') ?>">APC</a></h3></li>
        <li><h3><a href="<?= SimpleRouter::getLink('sys', 'phpinfo') ?>">PHP-INFO</a></h3></li>
        <li><h3><a href="<?= SimpleRouter::getLink('Cron.CronAdmin') ?>"><?=tr('mnu_cronJobs')?></a></h3></li>
        <li><h3><a href="<?= SimpleRouter::getLink('Admin.DbUpdate') ?>"><?=tr('mnu_dbUpdate')?></a></h3></li>
        <li><h3><a href="<?= SimpleRouter::getLink('sys', 'fixCachesLocation') ?>">LocationFix</a></h3></li>

    </ul>
</div>
