<?php

use Utils\Database\XDb;
use lib\Objects\User\User;
use Utils\Database\OcDb;
use Utils\Email\Email;
use Utils\Email\EmailSender;
use Utils\Generators\Uuid;


//prepare the templates and include all neccessary
if (!isset($rootpath))
    $rootpath = '';
require_once('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    //set here the template to process
    $tplname = 'register';

    //load language specific variables
    require_once($stylepath . '/' . $tplname . '.inc.php');

    //set to defaults
    tpl_set_var('register', $register);
    tpl_set_var('reset', tr('reset'));
    tpl_set_var('tos_message', '');
    tpl_set_var('all_countries_submit', '');
    tpl_set_var('countries_list', '');
    tpl_set_var('email_message', '');
    tpl_set_var('email', '');
    tpl_set_var('username', '');
    tpl_set_var('username_message', '');
    tpl_set_var('password_message', '');
    tpl_set_var('show_all_countries', 0);

    $db = OcDb::instance();
    if (isset($_POST['submit']) || isset($_POST['show_all_countries_submit'])) {
        //form load setting
        $display_all_countries = $_POST['allcountries'];
        $username = $_POST['username'];
        $password = $_POST['password1'];
        $password2 = $_POST['password2'];
        $email = $_POST['email'];
        $country = $_POST['country'];
        $tos = isset($_POST['TOS']) ? ($_POST['TOS'] == 'ON') : false;


        if (isset($_POST['submit'])) {
            //try to register
            //validate the entered data
            $email_not_ok = !Email::isValidEmail($email);
            $username_not_ok = mb_ereg_match(User::REGEX_USERNAME, $username) ? false : true;
            if ($username_not_ok == false) {
                // username should not be formatted like an email-address
                $username_not_ok = Email::isValidEmail($username);
            }
            $password_not_ok = mb_ereg_match(User::REGEX_PASSWORD, $password) ? false : true;
            $password_diffs = ($password != $password2);

            //check if email is in the database
            $rs = XDb::xSql("SELECT `username` FROM `user` WHERE `email`= ? ", $email);
            if (XDb::xFetchArray($rs) > 0) {
                $email_exists = true;
            } else {
                $email_exists = false;
            }

            //check if username is in the database
            $rs = XDb::xSql("SELECT `username` FROM `user` WHERE `username`= ? ", $username);
            if (XDb::xFetchArray($rs) > 0) {
                $username_exists = true;
            } else {
                $username_exists = false;
            }

            $all_ok = false;
            if ((!$email_not_ok) &&
                    (!$username_not_ok) &&
                    (!$password_not_ok) &&
                    (!$password_diffs) &&
                    (!$email_exists)) {
                if ($username_exists == false) {
                    if ($tos == true) {
                        $all_ok = true;
                    }
                }
            }

            if ($all_ok) {
                //send email
                //generate random password
                $activationcode = mb_strtoupper(mb_substr(md5(uniqid('')), 0, 13));

                $country_name = tr($country);

                $uuid = Uuid::create();
                if (strtotime("2008-11-01 00:00:00") <= strtotime(date("Y-m-d h:i:s")))
                    $rules_conf_req = 1;
                else
                    $rules_conf_req = 0;
                //insert the user
                XDb::xSql(
                    "INSERT INTO `user` ( `user_id`, `username`, `password`, `email`, `latitude`,
                                          `longitude`, `last_modified`, `login_faults`, `login_id`, `is_active_flag`,
                                          `was_loggedin`, `country`, `date_created`,
                                          `uuid`, `activation_code`, `node`, `rules_confirmed` )
                    VALUES ('', ?, ?, ?, NULL, NULL, NOW(), '0', '0', '0', '0', ?, NOW(), ?, ?, ?, ?)",
                    $username, hash('sha512', md5($password)), // WRTODO - could be better
                    $email, $country, $uuid, $activationcode, $oc_nodeid, $rules_conf_req);

                EmailSender::sendActivationMessage(__DIR__ . '/tpl/stdstyle/email/user_activation.email.html',
                    $username, $country, $activationcode, $email, $uuid);

                //display confirmationpage
                $tplname = 'register_confirm';
                tpl_set_var('country', htmlspecialchars($country_name, ENT_COMPAT, 'UTF-8'));
            }
            else {
                //set error strings
                if ($email_not_ok)
                    tpl_set_var('email_message', $error_email_not_ok);
                if ($username_not_ok)
                    tpl_set_var('username_message', $error_username_not_ok);
                if ($email_exists)
                    tpl_set_var('email_message', $error_email_exists);
                if ($username_exists)
                    tpl_set_var('username_message', $error_username_exists);

                if ($password_not_ok)
                    tpl_set_var('password_message', $error_password_not_ok);
                else
                if ($password_diffs)
                    tpl_set_var('password_message', $error_password_diffs);

                if ($tos == false)
                    tpl_set_var('tos_message', $error_tos);
            }
        }
        else if (isset($_POST['show_all_countries_submit'])) {
            //display all countries
            $display_all_countries = 1;
        }
    } else {
        //set to defaults
        $display_all_countries = 0;
        $username = '';
        $email = '';
        $country = $default_country;
        $tos = false;
    }

    tpl_set_var('email', htmlspecialchars($email, ENT_COMPAT, 'UTF-8'));
    tpl_set_var('username', htmlspecialchars($username, ENT_COMPAT, 'UTF-8'));

    //make countries list

    if ($country == 'XX') {
        $stmp = '<option value="XX" selected="selected">' . $no_answer . '</option>';
    } else {
        $stmp = '<option value="XX">' . $no_answer . '</option>';
    }

    if ($display_all_countries == 0) {
        tpl_set_var('all_countries_submit', '<input class="btn btn-default btn-sm" type="submit" name="show_all_countries_submit" value="' . $allcountries . '" />');
    } else {
        $query = 'SELECT `short` FROM `countries` WHERE 1 ORDER BY `short` ASC';

        $s = $db->simpleQuery($query);
        $dbResult = $db->dbResultFetchAll($s);

        foreach ($dbResult as $key => $value) {
            $defaultCountryList[] = $value['short'];
        }
    }
    foreach ($defaultCountryList as $countryCode) {
        if ($country == $countryCode) {
            $stmp .= '<option value="' . $countryCode . '" selected="selected">' . htmlspecialchars(tr($countryCode), ENT_COMPAT, 'UTF-8') . "</option>\n";
        } else {
            $stmp .= '<option value="' . $countryCode . '">' . htmlspecialchars(tr($countryCode), ENT_COMPAT, 'UTF-8') . "</option>\n";
        }
    }

    tpl_set_var('countries_list', $stmp);
    unset($stmp);

    tpl_set_var('show_all_countries', $display_all_countries);
}

//make the template and send it out
tpl_BuildTemplate();
