<?php

use Utils\Database\XDb;
use Utils\Log\Log;
//prepare the templates and include all neccessary
global $octeamEmailsSignature, $absolute_server_URI;
require_once('./lib/common.inc.php');


    //set here the template to process
    $tplname = 'remindemail';

    //load language specific variables
    require_once($stylepath . '/' . $tplname . '.inc.php');

    tpl_set_var('username', '');
    tpl_set_var('username_message', '');
    tpl_set_var('message', '');

    if (isset($_REQUEST['submit'])) {
        $username = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';

        $rs = XDb::xSql("SELECT `user_id`, `email`, `username` FROM `user` WHERE `username`= ? LIMIT 1", $username);

        if( !$r = XDb::xFetchArray($rs) ){
            tpl_set_var('username', htmlspecialchars($username, ENT_COMPAT, 'UTF-8'));
            tpl_set_var('username_message', $wrong_username);
        } else {

            $email_content = file_get_contents($stylepath . '/email/remindemail.email');

            $email_content = mb_ereg_replace('{server}', $absolute_server_URI, $email_content);
            $email_content = mb_ereg_replace('{ForgottenEmail_07}', tr('ForgottenEmail_07'), $email_content);
            $email_content = mb_ereg_replace('{ForgottenEmail_08}', tr('ForgottenEmail_08'), $email_content);
            $email_content = mb_ereg_replace('{ForgottenEmail_09}', tr('ForgottenEmail_09'), $email_content);
            $email_content = mb_ereg_replace('{ForgottenEmail_10}', tr('ForgottenEmail_10'), $email_content);
            $email_content = mb_ereg_replace('{ForgottenEmail_11}', tr('ForgottenEmail_11'), $email_content);
            $email_content = mb_ereg_replace('{user}', $r['username'], $email_content);
            $email_content = mb_ereg_replace('{date}', strftime(
                $GLOBALS['config']['datetimeformat']), $email_content);
            $email_content = mb_ereg_replace('{email}', $r['email'], $email_content);
            $email_content = mb_ereg_replace('{octeamEmailsSignature}', $octeamEmailsSignature, $email_content);

            global $emailaddr;

            $emailheaders = "Content-Type: text/plain; charset=utf-8\r\n";
            $emailheaders .= "Content-Transfer-Encoding: 8bit\r\n";
            $emailheaders .= 'From: "' . $emailaddr . '" <' . $emailaddr . '>';

            mb_send_mail($r['email'], $mail_subject, $email_content, $emailheaders);

            Log::logentry('remindemail', 3, $r['user_id'], 0, 0, 'Remind-E-Mail-Adress an ' . $r['username'] . ' / ' . $r['email'] , array());

            tpl_set_var('username', htmlspecialchars($username, ENT_COMPAT, 'UTF-8'));
            tpl_set_var('message', $mail_send);
        }
    }


//make the template and send it out
tpl_BuildTemplate();

