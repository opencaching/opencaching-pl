<?php

use Utils\Database\OcDb;
use Utils\Database\XDb;
//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
    } else {
        $tplname = 'searchuser';
        $options['username'] = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
        if (!isset($options['username'])) {
            $options['username'] = '';
        }
        if ($options['username'] != '') {

            $query = "SELECT user_id, username, date_created FROM user WHERE username LIKE :username ORDER BY username ASC";
            $params = array(
                "username" =>
                array(
                    "value" => '%' . XDb::xEscape($options['username']) . '%',
                    "data_type" => "string"
                ),
            );

            $dbc = OcDb::instance();
            $s = $dbc->paramQuery($query, $params);

            $bgcolor1 = '#eeeeee';
            $bgcolor2 = '#ffffff';
            $line = '<tr bgcolor={bgcolor}><td><a href=viewprofile.php?userid={user_id}>{username}</a></td><td>&nbsp;</td><td nowrap style="text-align:center;">{date_created}</td><td nowrap style="text-align:center;"></td></tr>';
            $lines = "";

            $ilosc = $dbc->rowCount($s);
            if ($ilosc != 0) {
                if ($ilosc == 1) {
                    $record = $dbc->dbResultFetch($s);
                    tpl_redirect("viewprofile.php?userid=" . $record['user_id']);
                } else {
                    $i = 0;
                    while ($record = $dbc->dbResultFetch($s)) {
                        $tmp_line = $line;
                        $tmp_line = mb_ereg_replace('{bgcolor}', ($i % 2 == 0) ? $bgcolor1 : $bgcolor2, $tmp_line);
                        $tmp_line = mb_ereg_replace('{username}', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'), $tmp_line);
                        $tmp_line = mb_ereg_replace('{user_id}', htmlspecialchars($record['user_id'], ENT_COMPAT, 'UTF-8'), $tmp_line);
                        $tmp_line = mb_ereg_replace('{date_created}', htmlspecialchars(fixPlMonth(strftime($dateformat, strtotime($record['date_created']))), ENT_COMPAT, 'UTF-8'), $tmp_line);

                        $lines .= $tmp_line . "\n";
                        $i++;
                    };

                    tpl_set_var('lines', $lines);
                    tpl_set_var('username', '');
                    tpl_set_var('not_found', '');
                }
            } else { // User not found
                tpl_set_var('username', $options['username']);
                tpl_set_var('not_found', '<b>' . tr("message_user_not_found") . ': ' . $options['username'] . '</b><br/><br/>');
                tpl_set_var('lines', '');
            }
        } else {
            tpl_set_var('username', '');
            tpl_set_var('not_found', '');
            tpl_set_var('lines', '');
        }
    }
}
tpl_BuildTemplate();
