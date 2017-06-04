<?php

// logbook generator...

require_once('./lib/common.inc.php');

//user logged in?
if ($usr == false) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);
} else {
    $tplname = 'logbook';
}

tpl_BuildTemplate();

