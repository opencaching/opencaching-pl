<?php

require('./lib/common.inc.php');

if ($error == false) {

    $NEED_FIND_LIMIT = 10;
    tpl_set_var('number_finds_caches', '4');
    $tplname = 'newcache_beginner';
}
tpl_BuildTemplate();
?>
