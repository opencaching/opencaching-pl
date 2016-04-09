<?php

use Utils\Database\XDb;

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    //set here the template to process
    $tplname = 'activation_status';

    $user = isset($_REQUEST['user']) ? $_REQUEST['user'] : '';
    $code = isset($_REQUEST['code']) ? $_REQUEST['code'] : '';

    tpl_set_var('error_message', '');
    tpl_set_var('success_message', '');
    tpl_set_var('login_url', '');

    if (isset($code) && isset($user)) {
        //TO DO: maybe validate uuid here..
        $rs = XDb::xSql("SELECT `user_id` `id`, `activation_code` `code`, `email`, `username`
                    FROM `user` WHERE `uuid`= ? ", $user);
        if ($r = XDb::xFetchArray($rs)) {
            if ($r['code'] != '') {
                if (($r['code'] == $code) && ($code != '')) {
                    XDb::xFreeResults($rs);

                    // ok, we can activate this account
                    XDb::xSql("UPDATE `user` SET `is_active_flag`=1, `activation_code`='' WHERE `user_id`= ? ", $r['id']);
                    tpl_set_var('success_message', tr('activation_success'));
                    tpl_set_var('login_url', '<a href="login.php">'.tr('goto_login').'</a><br />');
                    lib\Controllers\EmailController::sendPostActivationMail($r['username'], $r['email']);
                } else {
                    tpl_set_var('error_message', tr('activation_error1'));
                }
            } else {
                tpl_set_var('error_message', tr('activation_error2'));
            }
        } else {
            tpl_set_var('error_message', tr('activation_error1'));
        }
        XDb::xFreeResults($rs);
    } else {
        tpl_set_var('error_message', tr('activation_error1'));
    }
}

//make the template and send it out
tpl_BuildTemplate();
?>
