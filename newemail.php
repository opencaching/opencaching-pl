<?php
/***************************************************************************
                                                                ./newpw.php
                                                            -------------------
        begin                : Mon June 14 2004
        copyright            : (C) 2004 The OpenCaching Group
        forum contact at     : http://www.opencaching.com/phpBB2

    ***************************************************************************/

/***************************************************************************
    *
    *   This program is free software; you can redistribute it and/or modify
    *   it under the terms of the GNU General Public License as published by
    *   the Free Software Foundation; either version 2 of the License, or
    *   (at your option) any later version.
    *
    ***************************************************************************/

/****************************************************************************

   Unicode Reminder メモ

     change the users email

     used template(s): newpw

 ****************************************************************************/

    //prepare the templates and include all neccessary
    global $octeamEmailsSignature, $absolute_server_URI;
    require_once('./lib/common.inc.php');

    //Preprocessing
    if ($error == false)
    {
        //user logged in?
        if ($usr == false)
        {
            $target = urlencode(tpl_get_current_page());
            tpl_redirect('login.php?target='.$target);
        }
        else
        {
            //set here the template to process
            $tplname = 'newemail';

            //load language specific variables
            require_once($stylepath . '/' . $tplname . '.inc.php');

            tpl_set_var('new_email', '');
            tpl_set_var('message', '');
            tpl_set_var('email_message', '');
            tpl_set_var('code_message', '');
            tpl_set_var('change_email', $change_email);
            tpl_set_var('reset', $reset);
            tpl_set_var('getcode', $get_code);

            if (isset($_POST['submit_getcode']) || isset($_POST['submit_changeemail']))
            {
                $new_email = $_POST['newemail'];

                tpl_set_var('new_email', htmlspecialchars($new_email, ENT_COMPAT, 'UTF-8'));

                //validate the email
                $email_exists = false;
                $new_email_not_ok = false;

                if (!is_valid_email_address($new_email))
                {
                    $new_email_not_ok = true;
                    tpl_set_var('email_message', $error_email_not_ok);
                }
                else
                {
                    //prüfen, ob email schon in der Datenbank vorhanden
                    $rs = sql("SELECT `username` FROM `user` WHERE `email`='&1'", $new_email);
                    if (mysql_num_rows($rs) > 0)
                    {
                        $email_exists = true;
                        tpl_set_var('email_message', $error_email_exists);
                    }
                }

                if ((!$email_exists) && (!$new_email_not_ok))
                {
                    if (isset($_POST['submit_getcode']))
                    {
                        //send the secure code via email and store the new email in the database
                        $secure_code = uniqid('');

                        //code in DB eintragen
                        sql("UPDATE `user` SET `new_email_date`='&1', `new_email_code`='&2', `new_email`='&3' WHERE `user_id`='&4'", time(), $secure_code, $new_email, $usr['userid']);

                        $email_content = read_file($stylepath . '/email/newemail.email');
                    $email_content = mb_ereg_replace('{server}', $absolute_server_URI, $email_content);
                    $email_content = mb_ereg_replace('{newEmailAddr_01}', tr('newEmailAddr_01'), $email_content);
                    $email_content = mb_ereg_replace('{newEmailAddr_02}', tr('newEmailAddr_02'), $email_content);
                    $email_content = mb_ereg_replace('{newEmailAddr_03}', tr('newEmailAddr_03'), $email_content);
                    $email_content = mb_ereg_replace('{newEmailAddr_04}', tr('newEmailAddr_04'), $email_content);
                    $email_content = mb_ereg_replace('{newEmailAddr_05}', tr('newEmailAddr_05'), $email_content);
                    $email_content = mb_ereg_replace('{newEmailAddr_06}', tr('newEmailAddr_06'), $email_content);
                    $email_content = mb_ereg_replace('{newEmailAddr_07}', tr('newEmailAddr_07'), $email_content);
                        $email_content = mb_ereg_replace('{user}', $usr['username'], $email_content);
                        $email_content = mb_ereg_replace('{date}', strftime($datetimeformat), $email_content);
                        $email_content = mb_ereg_replace('{code}', $secure_code, $email_content);
                    $email_content = mb_ereg_replace('{octeamEmailsSignature}', $octeamEmailsSignature, $email_content);
                        //email versenden
                        mb_send_mail($new_email, $email_subject, $email_content, $emailheaders);

                        tpl_set_var('message', $email_send);
                    }
                    else if (isset($_POST['submit_changeemail']))
                    {
                        $secure_code = $_POST['code'];

                        $rs = sql("SELECT `new_email_code`, `new_email`, `new_email_date` FROM `user` WHERE `user_id`='&1'", $usr['userid']);
                        $record = sql_fetch_array($rs);
                        $new_email = $record['new_email'];
                        $new_email_code = $record['new_email_code'];
                        $new_email_date = $record['new_email_date'];

                        if ($new_email == '')
                        {
                            //no new email was entered
                            tpl_set_var('message', $error_no_new_email);
                        }
                        else if ($new_email_code != $secure_code)
                        {
                            //wrong code
                            tpl_set_var('code_message', $error_wrong_code);
                        }
                        else if (time() - $new_email_date > 259200)
                        {
                            //code timed out
                            tpl_set_var('code_message', $error_code_timed_out);
                        }
                        else
                        {
                            //check if email exists
                            $rs = sql("SELECT `username` FROM `user` WHERE `email`='&1'", $new_email);
                            if (mysql_num_rows($rs) > 0)
                            {
                                tpl_set_var('message', $error_email_exists);
                            }
                            else
                            {
                                //neue EMail eintragen
                                sql("UPDATE `user` SET `new_email_date`=NULL, `new_email_code`=NULL, `new_email`=NULL, `email`='&1', `last_modified`=NOW() WHERE `user_id`='&2'", $new_email, $usr['userid']);

                                //try to change the email
                                tpl_set_var('message', $email_changed);
                            }
                        }
                    }
                }
            }
        }
    }

    //make the template and send it out
    tpl_BuildTemplate();
?>
