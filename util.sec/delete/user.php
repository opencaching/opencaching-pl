<?php
 /***************************************************************************
                                                    ./util/deletecache/cache.php
                                                            -------------------
        begin                : June 28 2006
        copyright            : (C) 2006 The OpenCaching Group
        forum contact at     : http://www.opencaching.com/phpBB2

    ***************************************************************************/

 /***************************************************************************

        Unicode Reminder ??

        Script zum vollständigen entfernen von Benutzern.
        Schutz über htpasswd!

    ***************************************************************************/
    //prepare the templates and include all neccessary

    $rootpath = '../../';
    global $dynbasepath;
    header('Content-type: text/html; charset=utf-8');
    require($rootpath . 'lib/common.inc.php');

    function remove_watch($cache_id, $user_id) {
        //remove watch
        sql('DELETE FROM cache_watches WHERE cache_id=\'' . sql_escape($cache_id) . '\' AND user_id=\'' . sql_escape($user_id) . '\'');
        //remove from caches
        $rs = sql('SELECT watcher FROM caches WHERE cache_id=\'' . sql_escape($cache_id) . '\'');
        if (mysql_num_rows($rs) > 0) {
            $record = mysql_fetch_array($rs);
            sql('UPDATE caches SET watcher=\'' . ($record['watcher'] - 1) . '\' WHERE cache_id=\'' . sql_escape($cache_id) . '\'');
        }
    }

    if( $usr['admin'] )
    {

        $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

        if ($action == 'delete')
        {
            $username = isset($_REQUEST['username']) ? $_REQUEST['username'] : 0;
            $commit = isset($_REQUEST['commit']) ? $_REQUEST['commit'] : 0;

            if ($commit != 1) die('Kein Commit!');

            $rsUser = sql("SELECT `user_id` FROM `user` WHERE `username`='&1'", $username);
            if (mysql_num_rows($rsUser) != 1) die(mysql_num_rows($rsUser) . ' Benutzer gefunden');
            $rUser = sql_fetch_array($rsUser);
            $userid = $rUser['user_id'];
            mysql_free_result($rsUser);

            if (sqlValue("SELECT COUNT(*) FROM `caches` WHERE `user_id`='" . sql_escape($userid) . "'", 0) > 0)
                die('Es sind noch Caches vorhanden! <a href="../../search.php?searchto=searchbyowner&showresult=1&expert=0&output=HTML&sort=bydistance&f_userowner=0&f_userfound=0&f_inactive=0&f_ignored=0&owner=' . urlencode($username) . '">Suchen</a>');

            // removed_objects
            sql("INSERT IGNORE INTO `removed_objects` (`localid`, `uuid`, `type`, `removed_date`, `node`)
                            SELECT `id` AS `localid`, `uuid`, 6, NOW(), &2 FROM `pictures` WHERE `user_id`=&1 AND `object_type`=1
                            UNION
                            SELECT `user_id` AS `localid`, `uuid`, 4, NOW(), &2 FROM `user` WHERE `user_id`=&1
                            UNION
                            SELECT `id` AS `localid`, `uuid`, 1, NOW(), &2 FROM `cache_logs` WHERE `user_id`=&1", $userid, $oc_nodeid);

            // pictures
            $rs = sql("SELECT `url` FROM `pictures` WHERE  `user_id`=&1 AND `object_type`=1", $userid);
            while ($r = sql_fetch_assoc($rs))
            {
                $filename = $r['url'];
                while (mb_strpos($filename, '/') !== false)
                    $filename = mb_substr($filename, mb_strpos($filename, '/') + 1);

                if (is_file($picdir . '/' . $filename))
                {
                    unlink($picdir . '/' . $filename);
                    echo $filename . "<br />";
                }
            }
            sql("DELETE FROM `pictures` WHERE `user_id`=&1 AND `object_type`=1", $userid);

            // statpic
            if (is_file($dynbasepath . 'images/statpics/statpic' . $userid . '.jpg'))
                unlink($dynbasepath . 'images/statpics/statpic' . $userid . '.jpg');

            // queries
            sql("DELETE FROM `queries` WHERE `user_id`=&1", $userid);

            // cache_watches
            $rs = sql('SELECT cache_id FROM cache_watches WHERE user_id = &1', $userid);
            if (mysql_num_rows($rs) > 0) {
                for ($i = 0; $i < mysql_num_rows($rs); $i++) {
                    $record = sql_fetch_array($rs);
                    remove_watch($record['cache_id'], $userid);
                }
            }

            // watches_notified
            sql("DELETE FROM `watches_notified` WHERE `user_id`=&1", $userid);

            // cache_logs
            $rs = sql("SELECT `id`, `cache_id`, `type` FROM `cache_logs` WHERE `user_id`=&1", $userid);
            while ($r = sql_fetch_assoc($rs))
            {
                sql("DELETE FROM `cache_logs` WHERE `id`=&1", $r['id']);

                if ($r['type'] == 1)
                    sql("UPDATE `caches` SET `founds`=`founds`-1 WHERE `cache_id`=&1", $r['cache_id']);
                else if ($r['type'] == 2)
                    sql("UPDATE `caches` SET `notfounds`=`notfounds`-1 WHERE `cache_id`=&1", $r['cache_id']);
                else if ($r['type'] == 3)
                    sql("UPDATE `caches` SET `notes`=`notes`-1 WHERE `cache_id`=&1", $r['cache_id']);
            }

            // user
            sql("DELETE FROM `user` WHERE `user_id`=&1", $userid);

            echo 'Benutzer gelöscht';

            exit;
        }
        else if ($action == 'showuser')
        {
            $username = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';

            $rs = sql("SELECT `user`.`user_id`, `user`.`username`, `user`.`email`, `user`.`activation_code`, `user`.`last_login`, `user`.`is_active_flag`, `user`.`hidden_count`, `user`.`founds_count`, `log_notes_count`, `notfounds_count` FROM `user` WHERE `user`.`username`='&1' LIMIT 1", $username);
            if (mysql_num_rows($rs) != 0)
            {
                $r = sql_fetch_assoc($rs);
                mysql_free_result($rs);
    ?>
    <html>
        <body>
            <form action="cache.php" method="get">

            </form>
            <table>
    <?php
                echo '<tr><td>Name:</td><td><a href="../../viewprofile.php?userid=' . urlencode($r['user_id']) . '">' . htmlspecialchars($r['username'], ENT_COMPAT, 'UTF-8') . '</a></td></tr>';
                echo '<tr><td>EMail:</td><td>' . $r['email'] . '</td></tr>';
                echo '<tr><td>Aktivierungscode:</td><td>' . $r['activation_code'] . '</td></tr>';
                echo '<tr><td>Letzter Login:</td><td>' . $r['last_login'] . '</td></tr>';
                echo '<tr><td>Aktiv:</td><td>' . $r['is_active_flag'] . '</td></tr>';
                echo '<tr><td>Versteckt:</td><td>' . $r['hidden_count'] . '</td></tr>';
                echo '<tr><td>Logeinträge:</td><td>' . ($r['founds_count'] + $r['log_notes_count'] + $r['notfounds_count']) . '</td></tr>';

                echo '<tr>
                                <td>&nbsp;</td>
                                <td>
                                    <form action="user.php" method="get">
                                        <input type="hidden" name="action" value="delete" />
                                        <input type="hidden" name="username" value="' . $r['username'] . '" />
                                        <input type="checkbox" id="commit" name="commit" value="1" /><label for="commit">wirklich?</label><br />
                                        <input type="submit" value="Löschen" />
                                    </form>
                                </td>
                            </tr>';
                echo '<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
                echo '<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
    ?>
            </table>
        </body>
    </html>
    <?php
                exit;
            }
        }
    ?>
    <html>
        <body>
            <form action="user.php" method="get">
                <input type="hidden" name="action" value="showuser" />
                Benutzername <input type="text" name="username" size="20" />
                <input type="submit" value="Auswählen" />
            </form>
        </body>
    </html>
<?php
}
?>
