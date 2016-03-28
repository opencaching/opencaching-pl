<?php

use Utils\Database\XDb;

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

if ($usr['admin']) {
    $_SESSION['submitted'] = false;
    $cachename = XDb::xMultiVariableQueryValue(
        "SELECT name FROM caches WHERE cache_id= :1 ",
        0, $_REQUEST['cacheid']);

    tpl_set_var('cachename', $cachename);
    tpl_set_var('cacheid', $_REQUEST['cacheid']);
    $tplname = 'add_rr_comment';
    tpl_BuildTemplate();
}
