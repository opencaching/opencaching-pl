<?php
//prepare the templates and include all neccessary
    require_once('./lib/common.inc.php');

    if( $usr['admin'] )
    {
        $tplname = 'admin_searchuser';

        $options['username'] = isset($_POST['username']) ? $_POST['username'] : '';

        if(!isset($options['username'])) {
            $options['username']= '';
        }

        if ($options['username'] != '') {
            $query = "SELECT user_id FROM user WHERE username = '" . sql_escape($options['username']) . "'";;
            $rs = sql($query);
            if (mysql_num_rows($rs) != 0) { // Przekierowanie do profilu użytkownika
                $record = sql_fetch_array($rs);
                tpl_set_var('username', '');
                tpl_set_var('not_found', '');
                tpl_redirect('admin_users.php?userid=' . htmlspecialchars($record['user_id'], ENT_COMPAT, 'UTF-8'));
            } else { // Nie znaleziono użytkownika
          tpl_set_var('username', $options['username']);
          tpl_set_var('not_found', '<b>'.tr('Nie znaleziono użytkownika').': '. $options['username'] .'</b><br/><br/>');
            }
            mysql_free_result($rs);
        } else {
      tpl_set_var('username', '');
      tpl_set_var('not_found', '');
        }

        tpl_BuildTemplate();
    }
?>
