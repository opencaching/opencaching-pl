<?php

use Utils\Database\XDb;

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

if ($usr['admin']) {
    $tplname = 'admin_searchuser';

    $options['username'] = isset($_POST['username']) ? $_POST['username'] : '';

    if (!isset($options['username'])) {
        $options['username'] = '';
    }

    if ($options['username'] != '') {
        $query = "SELECT user_id FROM user WHERE username = '" . XDb::xEscape($options['username']) . "'";

        $rs = XDb::xSql($query);
        if (XDb::xNumRows($rs) != 0) { // Przekierowanie do profilu użytkownika
            $record = XDb::xFetchArray($rs);
            tpl_set_var('username', '');
            tpl_set_var('not_found', '');
            tpl_redirect('admin_users.php?userid=' . htmlspecialchars($record['user_id'], ENT_COMPAT, 'UTF-8'));
        } else { // Nie znaleziono użytkownika
            tpl_set_var('username', $options['username']);
            tpl_set_var('not_found', '<b>' . tr("message_user_not_found") . ': ' . $options['username'] . '</b><br/><br/>');
        }
        XDb::xFreeResults($rs);
    } else {
        tpl_set_var('username', '');
        tpl_set_var('not_found', '');
    }

    tpl_BuildTemplate();
}
