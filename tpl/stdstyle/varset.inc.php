<?php

tpl_set_var('title', htmlspecialchars($pagetitle, ENT_COMPAT, 'UTF-8'));
tpl_set_var('htmlheaders', '');
tpl_set_var('lang', $lang);
tpl_set_var('style', $style);
tpl_set_var('loginbox', '&nbsp;');
tpl_set_var('bodyMod', '');


//tpl_set_var('cachemap_js', '');
tpl_set_var('cachemap_header', '');


//tpl_set_var('cachemap_count', '0');
//tpl_set_var('cachemap_lat', "var lat = [0];");
//tpl_set_var('cachemap_lon', "var lon = [0];");
//tpl_set_var('cachemap_label', "var label = [0];");
//tpl_set_var('cachemap_cacheid', "var cache_id = [0];");
//tpl_set_var('cachemap_author', "var author = [0];");
//tpl_set_var('cachemap_icon', "var cache_icon = [0];");
//tpl_set_var('cachemap_old', "var cache_old = [0];");

//tpl_set_var('cachemap_f_found', '');
//tpl_set_var('cachemap_f_own', '');
//tpl_set_var('cachemap_f_unknown', '');
//tpl_set_var('cachemap_f_ignored', '');
//tpl_set_var('cachemap_f_traditional', '');
//tpl_set_var('cachemap_f_multi', '');
//tpl_set_var('cachemap_f_virtual', '');
//tpl_set_var('cachemap_f_webcam', '');
//tpl_set_var('cachemap_f_event', '');
//tpl_set_var('cachemap_f_quiz', '');
//tpl_set_var('cachemap_f_math', '');
//tpl_set_var('cachemap_f_moving', '');
//tpl_set_var('cachemap_f_drivein', '');
//tpl_set_var('cachemap_f_active', '');
//tpl_set_var('cachemap_f_unavailable', '');
//tpl_set_var('cachemap_f_archived', '');
//tpl_set_var('cachemap_f_newonly', '');
//tpl_set_var('cachemap_f_ofound', '');
//tpl_set_var('coords', '');
//tpl_set_var('cachemap_c_u_f', '');

//set up main template specific string
$sLoggedOut = '<form action="login.php" method="post" enctype="application/x-www-form-urlencoded" name="login" dir="ltr" style="display: inline;" class="form-group-sm">' . tr('user_or_email') . ':&nbsp;<input name="email" size="10" type="text" class="form-control input100" value="" />&nbsp;' . tr('password') . ':&nbsp;<input name="password" size="10" type="password" class="form-control input100" value="" />&nbsp;<input type="hidden" name="action" value="login" /><input type="hidden" name="target" value="{target}" /><input type="submit" name="LogMeIn" value="' . tr('login') . '" class="btn btn-primary btn-sm" /></form>';
$sLoggedIn = tr('logged_as') . ' <a href="viewprofile.php">{username}</a> - <a href="logout.php?token={logout_cookie}">' . tr('logout') . '</a>';

// target in Loginbox setzen
$target = basename($_SERVER['PHP_SELF']) . '?';

// REQUEST-Variablen durchlaufen und an target anhaengen
$allowed = array('cacheid', 'userid', 'logid', 'desclang', 'descid', 'wp');
reset($_REQUEST);
while (list ($varname, $varvalue) = each($_REQUEST)) {
    if (in_array($varname, $allowed)) {
        $target .= $varname . '=' . $varvalue . '&';
    }
}
if (mb_substr($target, -1) == '?' || mb_substr($target, -1) == '&')
    $target = mb_substr($target, 0, -1);
$sLoggedOut = mb_ereg_replace('{target}', $target, $sLoggedOut);


$tpl_subtitle = '';

$error_prefix = '<span class="errormsg">';
$error_suffix = '</span>';

