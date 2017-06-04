<?php

use Utils\Database\XDb;

$_SESSION['called_from_confirm'] = 1;

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

// check if user has already confirmed
$rs = XDb::xSql("SELECT `rules_confirmed` FROM `user` WHERE `user_id` = ? ", $usr['userid']);
if ($r = XDb::xFetchArray($rs)) {
    if ($r['rules_confirmed'] == 0 && (strtotime("2008-11-01 00:00:00") <= strtotime(date("Y-m-d h:i:s")))) {
        // acceptance neccessary!
        // set here the template to process
        $tplname = 'confirm';

        $accepted = isset($_REQUEST['accepted']) ? 1 : 0;
        tpl_set_var('message_start', '');
        tpl_set_var('message', '<b>Aby korzystać z serwisu, należy zapoznać się z regulaminem i zaakceptować go.</b>');
        tpl_set_var('message_end', '');

        if (isset($_REQUEST['submit'])) {
            if ($accepted) {
                XDb::xSql("UPDATE `user` SET `rules_confirmed` = 1 WHERE `user_id` = ? ", $usr['userid']);
                header("Location: index.php");
            }
        }

        //make the template and send it out
        tpl_BuildTemplate();
    } else
        header("Location: index.php");
}

