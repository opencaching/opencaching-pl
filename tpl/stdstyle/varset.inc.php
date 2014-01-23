<?php
/***************************************************************************
                                            ./tpl/stdstyle/varset.inc.php
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

   Unicode Reminder ??

     template specific variables setup

 ****************************************************************************/

    //set all main template replacement to default values

    tpl_set_var('title', htmlspecialchars($pagetitle, ENT_COMPAT, 'UTF-8'));
    tpl_set_var('htmlheaders', '');
    tpl_set_var('lang', $lang);
    tpl_set_var('style', $style);
    tpl_set_var('loginbox', '&nbsp;');
    tpl_set_var('functionsbox', '<a href="index.php?page=search">{{search}}</a> | <a href="index.php?page=sitemap">{{main_page}}</a>');
    tpl_set_var('runtime', '');
    tpl_set_var('bodyMod', '');


    tpl_set_var('cachemap_js', '');
    tpl_set_var('cachemap_header', '');
    tpl_set_var('viewcache_header', '');
    tpl_set_var('ga_script_header', '');


    tpl_set_var('cachemap_count', '0');
    tpl_set_var('cachemap_lat', "var lat = [0];");
    tpl_set_var('cachemap_lon', "var lon = [0];");
    tpl_set_var('cachemap_label', "var label = [0];");
    tpl_set_var('cachemap_cacheid', "var cache_id = [0];");
    tpl_set_var('cachemap_author', "var author = [0];");
    tpl_set_var('cachemap_icon', "var cache_icon = [0];");
    tpl_set_var('cachemap_old', "var cache_old = [0];");

    tpl_set_var('cachemap_f_found', '');
    tpl_set_var('cachemap_f_own', '');
    tpl_set_var('cachemap_f_unknown', '');
    tpl_set_var('cachemap_f_ignored', '');
    tpl_set_var('cachemap_f_traditional', '');
    tpl_set_var('cachemap_f_multi', '');
    tpl_set_var('cachemap_f_virtual', '');
    tpl_set_var('cachemap_f_webcam', '');
    tpl_set_var('cachemap_f_event', '');
    tpl_set_var('cachemap_f_quiz', '');
    tpl_set_var('cachemap_f_math', '');
    tpl_set_var('cachemap_f_moving', '');
    tpl_set_var('cachemap_f_drivein', '');
    tpl_set_var('cachemap_f_active', '');
    tpl_set_var('cachemap_f_unavailable', '');
    tpl_set_var('cachemap_f_archived', '');
    tpl_set_var('cachemap_f_newonly', '');
    tpl_set_var('cachemap_f_ofound', '');
    tpl_set_var('coords', '');
    tpl_set_var('cachemap_c_u_f', '');
    //set up main template specific string
    $sLoggedOut = '<form action="login.php" method="post" enctype="application/x-www-form-urlencoded" name="login" dir="ltr" style="display: inline;">'.tr('user').':&nbsp;<input name="email" size="10" type="text" class="textboxes" value="" />&nbsp;'.tr('password').':&nbsp;<input name="password" size="10" type="password" class="textboxes" value="" />&nbsp;<input type="hidden" name="action" value="login" /><input type="hidden" name="target" value="{target}" /><input type="submit" name="LogMeIn" value="'.tr('login').'" class="formbuttons" style="width:50px;" /></form>';
    $sLoggedIn = tr('logged_as').' <a href="myhome.php">{username}</a> - <a href="logout.php">'.tr('logout').'</a>';

    // target in Loginbox setzen
    $target = basename($_SERVER['PHP_SELF']).'?';

    // REQUEST-Variablen durchlaufen und an target anhaengen
    $allowed = array('cacheid', 'userid', 'logid', 'desclang', 'descid', 'wp');
    reset ($_REQUEST);
    while (list ($varname, $varvalue) = each ($_REQUEST))
    {
        if (in_array($varname, $allowed))
        {
            $target .= $varname.'='.$varvalue.'&';
        }
    }
    if (mb_substr($target, -1) == '?' || mb_substr($target, -1) == '&') $target = mb_substr($target, 0, -1);
    $sLoggedOut = mb_ereg_replace('{target}', $target, $sLoggedOut);

    $functionsbox_start_tag = '';
    $functionsbox_middle_tag = ' | ';
    $functionsbox_end_tag = '';

    $tpl_subtitle = '';

    //other vars
//  $login_required = '<p style="margin-top:0px;margin-left:0px;width:550px;background-color:#e5e5e5;border:1px solid black;text-align:left;padding:3px 8px 3px 8px;">Um die von dir aufgerufene Seite anzuzeigen, musst du eingeloggt sein!</p>';
    $login_required = 'Strona którą wywołałeś wymaga zalogowania się!';

    $dberrormsg = 'Błąd instrukcji bazy danych.';

    $error_prefix = '<span class="errormsg">';
    $error_suffix = '</span>';
?>
