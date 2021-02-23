<?php

use src\Models\ApplicationContainer;

require_once (__DIR__.'/lib/common.inc.php');

$no_tpl_build = false;
if (!ApplicationContainer::GetAuthorizedUser()) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);
} else {
    $tplname = 'log_cache_multi_send';
}

if ($no_tpl_build == false) {
    //make the template and send it out
    tpl_BuildTemplate();
}
