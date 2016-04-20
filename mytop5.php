<?php

require('./lib/common.inc.php');
require($stylepath . '/mytop5.inc.php');

if ($error == false) {
    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
        die();
    }

    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

    tpl_set_var('msg_delete', '');

    $dbc = new dataBase();

    if ($action == 'delete') {
        $cache_id = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] + 0 : 0;
        if ($cache_id != 0) {

            $query = "SELECT cache_id FROM cache_rating WHERE cache_id = :cache_id AND user_id = :user_id ";
            $params = array(
                "cache_id" => array(
                    "value" => $cache_id,
                    "data_type" => "int"
                ),
                "user_id" => array(
                    "value" => $usr['userid'],
                    "data_type" => "int"
                )
            );


            $dbc->paramQuery($query, $params);

            if ($dbc->rowCount() == 0) {
                // cache is not on top list of this user => ignore
            } else {
                $query = "DELETE FROM cache_rating WHERE cache_id = :cache_id AND user_id = :user_id";
                $dbc->paramQuery($query, $params);

                // Notify OKAPI's replicate module of the change.
                // Details: https://github.com/opencaching/okapi/issues/265
                require_once($rootpath . 'okapi/facade.php');
                \okapi\Facade::schedule_user_entries_check($cache_id, $usr['userid']);
                \okapi\Facade::disable_error_handling();

                $query = "SELECT name FROM caches WHERE cache_id = :cache_id LIMIT 1";
                $params = array(
                    "cache_id" => array(
                        "value" => $cache_id,
                        "data_type" => "int"
                ));

                $cachename = "!!!!!!!";
                $s = $dbc->paramQuery($query, $params);
                if ( $res = $dbc->dbResultFetchOneRowOnly($s) ){
                    $cachename = $res["name"];
                } else
                    $cachename = "-----";

                $msg_delete = mb_ereg_replace('{cacheid}', $cache_id, $msg_delete);
                tpl_set_var('msg_delete', mb_ereg_replace('{cachename}', $cachename, $msg_delete));
                tpl_set_var('jt', $cachename);
            }
        }
        else {
            // ignore, invalid Cache-ID ... when it's boring somewhen we can give a msg
        }
    }

    $tplname = 'mytop5';

    $i = 0;
    $content = '';

    $query = "  SELECT `cache_rating`.`cache_id` AS `cache_id`, `caches`.`name` AS `cachename`,
                caches.type as cache_type, caches.user_id,
                `user`.`username` AS `ownername`, `user`.`user_id` AS `owner_id`
                FROM `cache_rating`, `caches` , `user`
                WHERE `cache_rating`.`cache_id` = `caches`.`cache_id`
                AND `caches`.`user_id`=`user`.`user_id`
                  AND `cache_rating`.`user_id`= :user_id ORDER BY `caches`.`name` ASC";

    $params = array(
        "user_id" => array(
            "value" => $usr['userid'],
            "data_type" => "int"
        )
    );

    $stmt = $dbc->paramQuery($query, $params);

    if ($dbc->rowCount($stmt) != 0) {
        while ($r = $dbc->dbResultFetch($stmt)) {
            $thisline = $viewtop5_line;

            $cacheicon = myninc::checkCacheStatusByUser($r, $usr['userid']);

            $thisline = mb_ereg_replace('{cacheicon}', $cacheicon, $thisline);
            $thisline = mb_ereg_replace('{cachename}', htmlspecialchars($r['cachename'], ENT_COMPAT, 'UTF-8'), $thisline);
            $thisline = mb_ereg_replace('{cacheid}', htmlspecialchars($r['cache_id'], ENT_COMPAT, 'UTF-8'), $thisline);
            $thisline = mb_ereg_replace('{ownername}', htmlspecialchars($r['ownername'], ENT_COMPAT, 'UTF-8'), $thisline);
            $thisline = mb_ereg_replace('{owner_id}', htmlspecialchars($r['owner_id'], ENT_COMPAT, 'UTF-8'), $thisline);


            if (($i % 2) == 1)
                $thisline = mb_ereg_replace('{bgcolor}', $bgcolor2, $thisline);
            else
                $thisline = mb_ereg_replace('{bgcolor}', $bgcolor1, $thisline);

            $content .= $thisline;
            $i++;
        }
    }
    else {
        $content = $notop5;
    }

    unset($dbc);
    tpl_set_var('top5', $content);
    tpl_BuildTemplate();
}
?>
