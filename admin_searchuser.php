<?php

use Utils\Database\XDb;
use Utils\Uri\SimpleRouter;
use Utils\Text\UserInputFilter;

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

if ($usr['admin']) {
    $tplname = 'admin_searchuser';

    $options = [];
    $options['username'] = isset($_POST['username']) ? $_POST['username'] : '';
    $options['username'] = UserInputFilter::purifyHtmlString($options['username']);

    if ($options['username'] != '') {

        $rs = XDb::xSql(
            "SELECT user_id FROM user WHERE username = ? LIMIT 1", $options['username']);
        $record = XDb::xFetchArray($rs);
        if ($record != false) { // Przekierowanie do profilu użytkownika
            tpl_set_var('username', '');
            tpl_set_var('not_found', '');
            tpl_redirect(ltrim(SimpleRouter::getLink('Admin.UserAdmin', 'index', $record['user_id']), '/'));
        } else { // Nie znaleziono użytkownika
            tpl_set_var('username', $options['username']);
            tpl_set_var('not_found', '<div class="callout callout-warning">' . tr("message_user_not_found") . ': ' . $options['username'] . '</div>');
        }
        XDb::xFreeResults($rs);
    } else {
        tpl_set_var('username', '');
        tpl_set_var('not_found', '');
    }

    tpl_BuildTemplate();
}
