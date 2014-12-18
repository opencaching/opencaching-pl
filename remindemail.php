<?php

/* * *************************************************************************
  ./remindemail.php
  -------------------
  begin                : Wed September 21, 2005
  copyright            : (C) 2005 The OpenCaching Group
  forum contact at     : http://www.opencaching.com/phpBB2

 * ************************************************************************* */

/* * *************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 * ************************************************************************* */

/* * **************************************************************************

  Unicode Reminder ăĄă˘

  remind the user which email address he used for the opencaching-registration

 * ************************************************************************** */

//prepare the templates and include all neccessary
global $octeamEmailsSignature, $absolute_server_URI;
require_once('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    //set here the template to process
    $tplname = 'remindemail';

    //load language specific variables
    require_once($stylepath . '/' . $tplname . '.inc.php');

    tpl_set_var('username', '');
    tpl_set_var('username_message', '');
    tpl_set_var('message', '');

    if (isset($_REQUEST['submit'])) {
        $username = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';

        $rs = sql("SELECT `user_id`, `email`, `username` FROM `user` WHERE `username`='&1'", $username);
        if (mysql_num_rows($rs) == 0) {
            tpl_set_var('username', htmlspecialchars($username, ENT_COMPAT, 'UTF-8'));
            tpl_set_var('username_message', $wrong_username);
        } else {
            $r = sql_fetch_array($rs);

            $email_content = read_file($stylepath . '/email/remindemail.email');

            $email_content = mb_ereg_replace('{server}', $absolute_server_URI, $email_content);
            $email_content = mb_ereg_replace('{ForgottenEmail_07}', tr('ForgottenEmail_07'), $email_content);
            $email_content = mb_ereg_replace('{ForgottenEmail_08}', tr('ForgottenEmail_08'), $email_content);
            $email_content = mb_ereg_replace('{ForgottenEmail_09}', tr('ForgottenEmail_09'), $email_content);
            $email_content = mb_ereg_replace('{ForgottenEmail_10}', tr('ForgottenEmail_10'), $email_content);
            $email_content = mb_ereg_replace('{ForgottenEmail_11}', tr('ForgottenEmail_11'), $email_content);
            $email_content = mb_ereg_replace('{user}', $r['username'], $email_content);
            $email_content = mb_ereg_replace('{date}', strftime($datetimeformat), $email_content);
            $email_content = mb_ereg_replace('{email}', $r['email'], $email_content);
            $email_content = mb_ereg_replace('{octeamEmailsSignature}', $octeamEmailsSignature, $email_content);

            // ok, mail verschicken
            mb_send_mail($r['email'], $mail_subject, $email_content, $emailheaders);

            // logentry($module, $eventid, $userid, $objectid1, $objectid2, $logtext, $details)
            logentry('remindemail', 3, $r['user_id'], 0, 0, 'Remind-E-Mail-Adress an ' . sql_escape($r['username']) . ' / ' . sql_escape($r['email']), array());

            tpl_set_var('username', htmlspecialchars($username, ENT_COMPAT, 'UTF-8'));
            tpl_set_var('message', $mail_send);
        }
    }
}

//make the template and send it out
tpl_BuildTemplate();
?>
