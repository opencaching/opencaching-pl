<?php
/***************************************************************************
                                                                ./login.php
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

   Unicode Reminder ăĄă˘

     handle login, logout and forgotten passwords

     used template(s): login
     parameter(s):     email       post    email of the login account
                       password    post    password of the login account

 ****************************************************************************/

    //prepare the templates and include all neccessary
    require_once('./lib/common.inc.php');

    $no_tpl_build = false;
    $message = false;

    //Preprocessing
    if ($error == false)
    {
        //load language specific variables
        require_once($stylepath . '/login.inc.php');

        $target = '';
        if (isset($_REQUEST['target'])) $target = $_REQUEST['target'];
        if ($target == '')
        {
            // wenn im REQUEST nix war, guck mal im POST
            if (isset($_POST['target'])) $target = $_POST['target'];
        }
        if ($target == '') $target = 'index.php';

        if (isset($_REQUEST['action']))
        {
            if ($_REQUEST['action'] == 'cookieverify')
            {
                // wir sollten eingeloggt sein ... kucken, ob cookie gesetzt ...
                if (!isset($_COOKIE[$opt['cookie']['name'] . 'data']))
                    tpl_errorMsg('login', $cookies_error);
                else
                    tpl_redirect($target);

                exit;
            }
        }

        //set up the template replacements
        tpl_set_var('username', '');
        tpl_set_var('target', $target);

        //already logged in?
        if ($usr == false)
        {
            //set login template
            $tplname = 'login';

            //get the login email address and password
            $usr['email'] = isset($_POST['email']) ? $_POST['email'] : '';
            $usr['password'] = isset($_POST['password']) ? $_POST['password'] : '';

            if (($usr['email'] != '') && ($usr['password'] != ''))
            {
                //try to log in
                $retval = auth_login($usr['email'], $usr['password']);

                //delete password
                unset($usr['password']);

                if ($retval == false)
                {
                    //login not ok
                    switch ($autherr)
                    {
                        case AUTHERR_TOOMUCHLOGINS:
                            $message = $error_toomuchlogins;
                            break;
                        case AUTHERR_INVALIDEMAIL:
                            $message = $error_invalidemail;
                            break;
                        case AUTHERR_WRONGAUTHINFO:
                            $message = $error_wrongauthinfo;
                            break;
                        case AUTHERR_USERNOTACTIVE:
                            $message = $error_usernotactive;
                            break;
                        default:
                            $message = $error_loginnotok;
                            break;
                    }

                    //login not ok
                    unset($usr['email']);
                    unset($usr);
                    $usr = false;
                }
                else
                {
                    //login ok
                    session_start();
                    $_SESSION['print_list']=array();
                    $usr['userid'] = $retval;
                    $usr['username'] = auth_UsernameFromID($usr['userid']);
                    tpl_redirect('login.php?action=cookieverify&target=' . urlencode($target));
                    //echo 't='.$target;
                    exit;
                }
            }
            else if(isset($_REQUEST['target']))
            {
                //$message = $emptyform;
            }
        }
        else
        {
        tpl_redirect('login.php?action=cookieverify&target=' . urlencode($target));

            //logout before login
            /*$tplname = 'message';
            tpl_set_var('messagetitle', $message_logout_before_login_title);
            $message = $message_logout_before_login;*/
        }
    }

    if($message != '')
    {
        tpl_set_var('message_start', $message_start);
        tpl_set_var('message_end', $message_end);
        tpl_set_var('message', $message);
    }
    else
    {
        tpl_set_var('message_start', '');
        tpl_set_var('message_end', '');
        tpl_set_var('message', '');
    }

    if ($no_tpl_build == false)
    {
        //make the template and send it out
        tpl_BuildTemplate();
    }
?>
