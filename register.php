<?php
use Utils\Database\XDb;
use lib\Objects\User\User;
use Utils\Email\Email;
use Utils\Email\EmailSender;
use Utils\Generators\Uuid;

// prepare the templates and include all neccessary
if (! isset($rootpath))
    $rootpath = '';

require_once ('./lib/common.inc.php');

// set here the template to process
$tplname = 'register';

// set to defaults
tpl_set_var('tos_message', '');
tpl_set_var('email_message', '');
tpl_set_var('email', '');
tpl_set_var('username', '');
tpl_set_var('username_message', '');
tpl_set_var('password_message', '');

if (isset($_POST['submit'])) {
    // form load setting
    $username = $_POST['username'];
    $password = $_POST['password1'];
    $password2 = $_POST['password2'];
    $email = $_POST['email'];
    $tos = isset($_POST['TOS']) ? ($_POST['TOS'] == 'ON') : false;

    // try to register
    // validate the entered data
    $email_not_ok = ! Email::isValidEmail($email);
    $username_not_ok = mb_ereg_match(User::REGEX_USERNAME, $username) ? false : true;
    if ($username_not_ok == false) {
        // username should not be formatted like an email-address
        $username_not_ok = Email::isValidEmail($username);
    }
    $password_not_ok = mb_ereg_match(User::REGEX_PASSWORD, $password) ? false : true;
    $password_diffs = ($password != $password2);

    // check if email is in the database
    $rs = XDb::xSql("SELECT `username` FROM `user` WHERE `email`= ?  LIMIT 1", $email);
    if (XDb::xFetchArray($rs) > 0) {
        $email_exists = true;
    } else {
        $email_exists = false;
    }

    // check if username is in the database
    $rs = XDb::xSql("SELECT `username` FROM `user` WHERE `username`= ? LIMIT 1", $username);
    if (XDb::xFetchArray($rs) > 0) {
        $username_exists = true;
    } else {
        $username_exists = false;
    }

    $all_ok = false;
    if ((! $email_not_ok) && (! $username_not_ok) && (! $password_not_ok) && (! $password_diffs) && (! $email_exists)) {
        if ($username_exists == false) {
            if ($tos == true) {
                $all_ok = true;
            }
        }
    }

    if ($all_ok) {
        // send email
        // generate random password
        $activationcode = mb_strtoupper(mb_substr(md5(uniqid('')), 0, 13));

        $uuid = Uuid::create();
        if (strtotime("2008-11-01 00:00:00") <= strtotime(date("Y-m-d h:i:s")))
            $rules_conf_req = 1;
        else
            $rules_conf_req = 0;
        // insert the user
        XDb::xSql("INSERT INTO `user` ( `username`, `password`, `email`, `latitude`,
                                      `longitude`, `last_modified`, `is_active_flag`,
                                      `date_created`,
                                      `uuid`, `activation_code`, `node`, `rules_confirmed` )
                VALUES (?, ?, ?, NULL, NULL, NOW(), '0', NOW(), ?, ?, ?, ?)",
            $username, hash('sha512', md5($password)), $email, $uuid, $activationcode, $oc_nodeid, $rules_conf_req);

        EmailSender::sendActivationMessage(__DIR__ . '/tpl/stdstyle/email/user_activation.email.html', $username, $activationcode, $email, $uuid);

        // display confirmationpage
        $tplname = 'register_confirm';
    } else {
        // set error strings
        if ($email_not_ok)
            tpl_set_var('email_message', '<span class="errormsg">' . tr('error_email_not_ok') . '</span>');
        if ($username_not_ok)
            tpl_set_var('username_message', '<span class="errormsg">' . tr('error_username_not_ok') . '</span>');
        if ($email_exists)
            tpl_set_var('email_message', '<span class="errormsg">' . tr('error_email_exists') . '</span>');
        if ($username_exists)
            tpl_set_var('username_message', '<span class="errormsg">' . tr('error_username_exists') . '</span>');
        if ($password_not_ok)
            tpl_set_var('password_message', '<span class="errormsg">' . tr('error_password_not_ok') . '</span>');
        else if ($password_diffs)
            tpl_set_var('password_message', '<span class="errormsg">' . tr('error_password_diffs') . '</span>');
        if ($tos == false)
            tpl_set_var('tos_message', '<br><span class="errormsg">' . tr('error_tos') . '</span>');
    }
} else {
    // set to defaults
    $username = '';
    $email = '';
}

tpl_set_var('email', htmlspecialchars($email, ENT_COMPAT, 'UTF-8'));
tpl_set_var('username', htmlspecialchars($username, ENT_COMPAT, 'UTF-8'));

// make the template and send it out
tpl_BuildTemplate();