<?php

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

if ($usr['admin']) {

    if (isset($_REQUEST['userid'])) {
        $user_id = $_REQUEST['userid'];
        tpl_set_var('userid', $user_id);
    }

    if (checkField('countries', $lang))
        $lang_db = $lang;
    else
        $lang_db = "en";

    $rsuser = sql("SELECT hidden_count, founds_count, log_notes_count, notfounds_count,last_login,
                                username, date_created,description, email,is_active_flag,
                                stat_ban,activation_code,hide_flag,countries.$lang_db country, verify_all
                                FROM `user` LEFT JOIN countries ON (user.country=countries.short) WHERE user_id=&1 ", $user_id);

    $record = sql_fetch_array($rsuser);

    $user = new \lib\Objects\User\User(array('userId'=>$_REQUEST['userid']));
    $user->loadExtendedSettings();

    if(isset($_POST['save']) && isset($_POST['note_content']) && $_POST['note_content']!="") {
        lib\Objects\User\AdminNote::addAdminNote($usr['userid'], $user_id, false, $_POST['note_content']);
        Header("Location: viewprofile.php?userid=".$user_id);
    }

    if (isset($_GET['stat_ban']) && $_GET['stat_ban'] == 1 && $usr['admin']) {
        $sql = "UPDATE user SET stat_ban = 1 - stat_ban WHERE user_id = " . intval($user_id);
        if ($record["stat_ban"] == 0) {
            $record["stat_ban"] = 1;
            lib\Objects\User\AdminNote::addAdminNote($usr['userid'], $user_id, true, lib\Objects\User\AdminNote::BAN_STATS);
        }
        else if($record["stat_ban"] == 1) {
            $record["stat_ban"] = 0;
            lib\Objects\User\AdminNote::addAdminNote($usr['userid'], $user_id, true, lib\Objects\User\AdminNote::UNBAN_STATS);
        }
        mysql_query($sql);
    }
    if(isset($_GET['hide_flag'])){
        if ($_GET['hide_flag'] == 10 && $usr['admin']) {
            $sql = "UPDATE user SET hide_flag = 10  WHERE user_id = " . intval($user_id);
            mysql_query($sql);
        }
        if ($_GET['hide_flag'] == 0 && $usr['admin']) {
            $sql = "UPDATE user SET hide_flag = 0  WHERE user_id = " . intval($user_id);
            mysql_query($sql);
        }
    }
// force all caches to be verified - sql
    if(isset($_GET['verify_all'])) {
        if ($_GET['verify_all'] == 1 && $usr['admin']) {
            $sql = "UPDATE user SET verify_all = '1'  WHERE user_id = '" . intval($user_id) . "'";
            $record["verify_all"] = 1;
            lib\Objects\User\AdminNote::addAdminNote($usr['userid'], $user_id, true, lib\Objects\User\AdminNote::VERIFY_ALL);
            mysql_query($sql) or die(mysql_error());
        }
        if ($_GET['verify_all'] == 0 && $usr['admin']) {
            $sql = "UPDATE user SET verify_all = 0  WHERE user_id = " . intval($user_id);
            lib\Objects\User\AdminNote::addAdminNote($usr['userid'], $user_id, true, lib\Objects\User\AdminNote::NO_VERIFY_ALL);
            $record["verify_all"] = 0;
            mysql_query($sql);
        }
    }
// end force

    if(isset($_REQUEST['ignoreFoundLimit']) && $_REQUEST['ignoreFoundLimit'] != ''){
        $newIgnoreFoundLimit = intval($_REQUEST['ignoreFoundLimit']);
        if ($newIgnoreFoundLimit == 1) {
            lib\Objects\User\AdminNote::addAdminNote($usr['userid'], $user->getUserId(), true, lib\Objects\User\AdminNote::IGNORE_FOUND_LIMIT);
        } else {
            lib\Objects\User\AdminNote::addAdminNote($usr['userid'], $user->getUserId(), true, lib\Objects\User\AdminNote::IGNORE_FOUND_LIMIT_RM);
        }
        $db = \lib\Database\DataBaseSingleton::Instance();
        $db->multiVariableQuery('INSERT INTO user_settings (user_id, newcaches_no_limit) VALUES (:2, :1) ON DUPLICATE KEY UPDATE newcaches_no_limit = :1', $newIgnoreFoundLimit, $user->getUserId());
        $db->reset();
        unset ($user);
        $user = new \lib\Objects\User\User(array('userId'=>$_REQUEST['userid']));
        $user->loadExtendedSettings();;
    }

    //ban
    if (isset($_GET['is_active_flag']) && $_GET['is_active_flag'] == 1 && $usr['admin']) {
        $sql = "UPDATE user SET is_active_flag = 1 - is_active_flag, `activation_code`='' WHERE user_id = " . intval($user_id);
        if ($record["is_active_flag"] == 0) {
            $record["is_active_flag"] = 1;
            lib\Objects\User\AdminNote::addAdminNote($usr['userid'], $user_id, true, lib\Objects\User\AdminNote::UNBAN);
        }
        else if($record["is_active_flag"] == 1) {
            $record["is_active_flag"] = 0;
            lib\Objects\User\AdminNote::addAdminNote($usr['userid'], $user_id, true, lib\Objects\User\AdminNote::BAN);
        }
        mysql_query($sql);
    }

    if ($usr['userid'] == $super_admin_id) {
        tpl_set_var('remove_all_logs', '<p><img src="tpl/stdstyle/images/blue/arrow2.png" alt="" align="middle" />&nbsp;&nbsp;<a href="removelog.php?userid=' . $user_id . '"><font color="#ff0000">' . tr('admin_users_remove_logs') . '</font></a>&nbsp;<img src="' . $stylepath . '/images/blue/atten-red.png" align="top" alt="" /></p>');
    } else
        tpl_set_var('remove_all_logs', '');


    if (!$record['activation_code']) {
        tpl_set_var('activation_codes', tr('account_is_actived'));
    } else {
        tpl_set_var('activation_codes', $record['activation_code']);
    }


    if ($record['last_login'] == "0000-00-00 00:00:00") {
        $userlogin = tr('NoDataAvailable');
    } else {
        $userlogin = strftime("%d-%m-%Y", strtotime($record['last_login']));
    }
    tpl_set_var('lastlogin', $userlogin);

    tpl_set_var('username', $record['username']);
    tpl_set_var('country', htmlspecialchars($record['country'], ENT_COMPAT, 'UTF-8'));
    tpl_set_var('registered', strftime("%d-%m-%Y", strtotime($record['date_created'])));
    tpl_set_var('email', strip_tags($record['email']));
    tpl_set_var('description', nl2br($record['description']));


    if ($record['is_active_flag'])
        tpl_set_var('is_active_flags', '&nbsp;<a href="admin_users.php?userid=' . $user_id . '&amp;is_active_flag=1"><font color="#ff0000">' . tr('lock') . ' ' . tr('user_account') . '</font></a>&nbsp;<img src="' . $stylepath . '/images/blue/atten-red.png" align="top" alt="" />');
    else
        tpl_set_var('is_active_flags', '&nbsp;<a href="admin_users.php?userid=' . $user_id . '&amp;is_active_flag=1"><font color="#228b22">' . tr('unlock') . ' ' . tr('user_account') . '</font></a>&nbsp;<img src="' . $stylepath . '/images/blue/atten-green.png" align="bottom" alt="" />');


    if (!$record['stat_ban'])
        tpl_set_var('stat_ban', '&nbsp;<a href="admin_users.php?userid=' . $user_id . '&amp;stat_ban=1"><font color="#ff0000">' . tr('lock') . ' ' . tr('user_stats') . '</font></a>&nbsp;<img src="' . $stylepath . '/images/blue/atten-red.png" align="top" alt="" />');
    else
        tpl_set_var('stat_ban', '&nbsp;<a href="admin_users.php?userid=' . $user_id . '&amp;stat_ban=1"><font color="#228b22">' . tr('unlock') . ' ' . tr('user_stats') . '</font></a>&nbsp;<img src="' . $stylepath . '/images/blue/atten-green.png" align="top" alt="" />');

    if($user->getFoundGeocachesCount() < 10){
        $ignoreFoundLimit = $user->isIngnoreGeocacheLimitWhileCreatingNewGeocache();
        if($ignoreFoundLimit){
            $translation = tr('ignoreFoundLimitRm');
        } else {
            $translation = tr('ignoreFoundLimitAdd');
        }
        $ignoreFoundLimitHtml = '<p><img src="tpl/stdstyle/images/blue/arrow2.png" alt="" align="middle" />&nbsp;&nbsp;<span class="content-title-noshade txt-blue08"><a href="admin_users.php?userid=' . $user_id . '&amp;ignoreFoundLimit='. (int) !$ignoreFoundLimit .'"><font color="#ff0000">'.$translation.'</font></a><img src="' . $stylepath . '/images/blue/atten-red.png" align="top" alt="" /></span></p>';
        tpl_set_var('ignoreFoundLimit', $ignoreFoundLimitHtml);
    } else {
        tpl_set_var('ignoreFoundLimit', '');
    }

    // force all caches to be verified - form
    $verify_all = $record['verify_all'];

    if ($verify_all == 0) {
        tpl_set_var('hide_flag', '<p><img src="tpl/stdstyle/images/blue/arrow2.png" alt="" align="middle" />&nbsp;&nbsp;<a href="admin_users.php?userid=' . $user_id . '&amp;verify_all=1"><font color="#ff0000">' . tr('admin_users_verify_all') . '</font></a>&nbsp;<img src="' . $stylepath . '/images/blue/atten-red.png" align="top" alt="" /></p>');
    } else {
        tpl_set_var('hide_flag', '<p><img src="tpl/stdstyle/images/blue/arrow2.png" alt="" align="middle" />&nbsp;&nbsp;<a href="admin_users.php?userid=' . $user_id . '&amp;verify_all=0"><font color="#228b22">' . tr('admin_users_verify_none') . '</font></a>&nbsp;<img src="' . $stylepath . '/images/blue/atten-green.png" align="top" alt="" /></p>');
    }

    tpl_set_var('form_title', tr('admin_notes_content'));
    tpl_set_var('submit_button', tr('pt032'));

    $tplname = 'admin_users';
    tpl_BuildTemplate();
}
