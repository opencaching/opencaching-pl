<?php
if(!isset($rootpath)) $rootpath = '';
require_once('./lib/common.inc.php');
    function orderBy($orderId)
    {
        switch( $orderId )
        {
            case "0":
                return "name";
            case "1":
                return "date_hidden";
            /*case "2":
                return "type";
            case "3":
                return "status";*/
            default:
                return "name";
        }
    }

    function orderType($orderType)
    {
        switch( $orderType )
        {
            case "0":
                return "DESC";
            default:
                return "ASC";
        }
    }

    function listUserCaches($userid)
    {
        // lists all approved caches belonging to user
        $cacheList = array();
        $i = 0;
        if(!isset($_GET['orderId'])) $_GET['orderId'] = ' ';
        if(!isset($_GET['orderType'])) $_GET['orderType'] = ' ';
        $sql = "SELECT cache_id, name, date_hidden FROM caches WHERE user_id='".sql_escape(intval($userid))."' AND status <> 4 AND type != 10 ORDER BY ".sql_escape(orderBy($_GET['orderId']))." ".sql_escape(orderType($_GET['orderType']));
        $query = mysql_query($sql);
        while( $cache = mysql_fetch_array($query) )
        {
            $cacheList[$i] = $cache;
            $i++;
        }
            return $cacheList;
    }

    function listPendingCaches($userid)
    {
        $cacheList = array();
        $i = 0;
        $sql = "SELECT cache_id, name, date_hidden FROM caches WHERE cache_id IN (SELECT cache_id FROM chowner WHERE user_id = '".sql_escape($userid)."')";
        $query = mysql_query($sql);
        while( $cache = mysql_fetch_array($query) )
        {
            $cacheList[$i] = $cache;
            $i++;
        }
            return $cacheList;
    }

    function getUsername($userid)
    {
        $sql = "SELECT username FROM user WHERE user_id='".sql_escape(intval($userid))."'";
        $query = mysql_query($sql) or die();
        if( mysql_num_rows($query) > 0)
            return mysql_result($query,0);
        return -1;
    }

    function getUserEmail($userid)
    {
        $sql = "SELECT email FROM user WHERE user_id='".sql_escape(intval($userid))."'";
        $query = mysql_query($sql) or die();
        if( mysql_num_rows($query) > 0)
            return mysql_result($query,0);
        return -1;
    }

    function getCacheName($cacheid)
    {
        $sql = "SELECT name FROM caches WHERE cache_id='".sql_escape(intval($cacheid))."'";
        $query = mysql_query($sql) or die();
        if( mysql_num_rows($query) > 0)
            return mysql_result($query,0);
        return -1;
    }

    function getCacheOwner($cacheid)
    {
        $sql = "SELECT user_id FROM caches WHERE cache_id='".sql_escape(intval($cacheid))."'";
        $query = mysql_query($sql) or die();
        if( mysql_num_rows($query) > 0)
            return mysql_result($query,0);
        return -1;
    }

    function isUserOwner($userid, $cacheid)
    {
        //if( $usr['admin'])
        //  return 1;
        $sql = "SELECT count(*) FROM caches WHERE cache_id='".sql_escape(intval($cacheid))."' AND user_id='".sql_escape(intval($userid))."'";
        $query = mysql_query($sql) or die();
        if( mysql_num_rows($query) > 0)
            return mysql_result($query,0);
        return -1;
    }

    function doesUserExist($username)
    {
        $sql = "SELECT user_id FROM user WHERE username='".sql_escape(strip_tags($username))."'";
        $query = mysql_query($sql) or die();
        if( mysql_num_rows($query) > 0)
            return mysql_result($query,0);
        return -1;
    }

    function isRequestPending($cacheid)
    {
        // czy skrzynka cacheid juz oczekuje na zmiane wlasciciela?
        $sql = "SELECT count(*) FROM chowner WHERE cache_id='".sql_escape(intval($cacheid))."'";
        $query = mysql_query($sql) or die();
        if( mysql_num_rows($query) > 0)
            return mysql_result($query,0);
        return -1;
    }

    function isAcceptanceNeeded($userid)
    {
        $sql = "SELECT count(*) FROM chowner WHERE user_id='".sql_escape(intval($userid))."'";
        $query = mysql_query($sql) or die();
        if( mysql_num_rows($query) > 0)
            return mysql_result($query,0);
        return -1;
    }

    function emailHeaders() {
        global $usr, $site_name;
        $email_headers = "Content-Type: text/plain; charset=utf-8\r\n";
        $email_headers .= "From: $site_name <$octeam_email>\r\n";
        $email_headers .= "Reply-To: ".$usr['email']."\r\n";
        return $email_headers;
    }

    //prepare the templates and include all neccessary

    $tplname = 'chowner';

    // tylko dla zalogowanych
    if ($error == false && isset($usr['userid']))
    {
        tpl_set_var('error_msg', "");
        tpl_set_var('info_msg', "");
        tpl_set_var('start_przejmij', "<!--");
        tpl_set_var('end_przejmij', "-->");
        tpl_set_var('acceptList', "");
        tpl_set_var('cacheList', "");

        // wybor wlasciciela - mozna zmieniac tylko swoje skrzynki... chyba, ze jest sie czlonkiem oc team
        if( isset($_GET['cacheid']) && (isUserOwner($usr['userid'], $_GET['cacheid']) && !isset($_GET['abort']) && !isset($_GET['accept'])))
        {
            tpl_set_var('cachename', getCacheName($_GET['cacheid']));
            tpl_set_var('cacheid', $_GET['cacheid']);
            $tplname = "chowner_chooseuser";
        }
        else
        {
            if( isset($_GET['accept']) && $_GET['accept'] == 1 )
            {
                $sql = "SELECT count(*) FROM chowner WHERE cache_id = '".sql_escape(intval($_GET['cacheid']))."' AND user_id = '".sql_escape(intval($usr['userid']))."'";
                $potwierdzenie = mysql_result(mysql_query($sql),0);
                if( $potwierdzenie > 0 )
                // zmiana wlasciciela
                {
                    $oldOwnerId = sql_escape(getCacheOwner($_GET['cacheid']));


                    tpl_set_var("error_msg", "Wystąpił błąd podczas zmiany właściciela skrzynki.<br /><br />");
                    tpl_set_var("info_msg", "");
                    $sql = "DELETE FROM chowner WHERE cache_id = '".sql_escape(intval($_GET['cacheid']))."' AND user_id = '".sql_escape(intval($usr['userid']))."'";
                    mysql_query($sql);
                    $sql = "UPDATE caches SET user_id = '".sql_escape(intval($usr['userid']))."' WHERE cache_id='".sql_escape(intval($_GET['cacheid']))."'";
                    mysql_query($sql);
                    $sql = "UPDATE pictures SET user_id = '".sql_escape(intval($usr['userid']))."' WHERE object_id = '".sql_escape(intval($_GET['cacheid']))."'";
                    mysql_query($sql);

                    $sql = "UPDATE user SET hidden_count = hidden_count - 1 WHERE user_id = '".$oldOwnerId."'";
                    mysql_query($sql);

                    $sql = "UPDATE user SET hidden_count = hidden_count + 1 WHERE user_id = '".sql_escape(intval($usr['userid']))."'";
                    mysql_query($sql);

                    // put log into cache logs.
                    $log_text = '<font color="blue"><b>'.tr('adopt00').' <a href="'.$absolute_server_URI.'viewprofile.php?userid='.$oldOwnerId.'">'.getUsername($oldOwnerId).'</a> ';
                    $log_text .= tr('adopt01').' <a href="'.$absolute_server_URI.'viewprofile.php?userid='.$usr['userid'].'">'.getUsername($usr['userid']).'</a></font></b>. ';
                    sql("INSERT INTO `cache_logs` (`id`, `cache_id`, `user_id`, `type`, `date`, `text`, `text_html`, `text_htmledit`, `date_created`, `last_modified`, `uuid`, `node`)
                                    VALUES        ('',   '&1',       '&2',      '&3',   NOW(),  '&4',   '&5',         '&6', NOW(), NOW(), '&7', '&8')",
                                    sql_escape(intval($_GET['cacheid'])), -1, 3, $log_text, 1, 1, create_uuid(), $oc_nodeid);

                    tpl_set_var("error_msg", "");
                    tpl_set_var("info_msg", " ".tr('adopt_15')." ".getCacheName($_GET['cacheid'])."<br /><br />");

                    mb_send_mail(getUserEmail($oldOwnerId), tr('adopt_18'), "Witaj!\nNowym właścicielem Twojej skrzynki: ".getCacheName($_GET['cacheid']). tr('adopt_21').": ".$usr['username'].".", emailHeaders());
                    }
            }
            if( isset($_GET['accept']) && $_GET['accept'] == 0 )
            {
                // odrzucenie zmiany
                $oldOwnerId = getCacheOwner($_GET['cacheid']);

                $sql = "DELETE FROM chowner WHERE cache_id = '".sql_escape($_GET['cacheid'])."' AND user_id = '".sql_escape($usr['userid'])."'";
                mysql_query($sql);
                if( mysql_affected_rows() > 0 )
                {
                    tpl_set_var("info_msg", "Zaproszenie do przejęcia skrzynki zostało przez Ciebie odrzucone.<br /><br />");
                    mb_send_mail(getUserEmail($oldOwnerId), "[OC PL] Użytkownik nie przyjął Twojej skrzynki", "Witaj!\nNiestety użytkownik: ".$usr['username']." nie chce być nowym właścicielem Twojej skrzynki: ".getCacheName($_GET['cacheid']).".", emailHeaders());
                }
                else
                    tpl_set_var("error_msg", "Wystąpił błąd podczas zmiany właściciela skrzynki.<br /><br />");

            }

            if( isset($_GET['abort']) && isUserOwner($usr['userid'], $_GET['cacheid']))
            {
                // anulowanie procedury przejecia
                $sql = "DELETE FROM chowner WHERE cache_id = '".sql_escape(intval($_GET['cacheid']))."'";
                mysql_query($sql);
                if( mysql_affected_rows() > 0 )
                    tpl_set_var('info_msg', " ".tr('adopt_16')." <br /><br />");
                else
                    tpl_set_var('error_msg', " ".tr('adopt_17')." <br /><br />");
            }

            if( isAcceptanceNeeded($usr['userid']) )
            {
                // skrzynka czeka na moja akceptacje
                tpl_set_var('start_przejmij', "");
                tpl_set_var('end_przejmij', "");
                $acceptList = '';
                foreach( listPendingCaches($usr['userid']) as $cache) {
                    $acceptList .= "<tr><td>";
                    $acceptList .= "<a href='viewcache.php?cacheid=".$cache['cache_id']."'>";
                    $acceptList .= $cache['name']."</a>";
                    $acceptList .= " <a href='chowner.php?cacheid=".$cache['cache_id']."&accept=1'>[<font color='green'>".tr('adopt_12')."</font>]</a>";
                    $acceptList .= " <a href='chowner.php?cacheid=".$cache['cache_id']."&accept=0'>[<font color='#ff0000'>".tr('adopt_13')."</font>]</a>";


                    $acceptList .= "</td>
                    <td>".$cache['date_hidden']."</td>
                    </tr>
                    ";
                }
                tpl_set_var('acceptList', $acceptList);
            }

            if( isset($_POST['username']) )
            {
                if( doesUserExist($_POST['username']) > 0 )
                {
                    // przekazywanie samemu sobie
                    //if( $usr['username'] == $_POST['username'] )
                    //  tpl_set_var('error_msg', "Nie możesz przekazać skrzynki samemu sobie...<br /><br />");
                    //else
                    {
                        // uzytkownik istnieje, mozna kontynuowac procedure
                        $newUserId = doesUserExist($_POST['username']);
                        $sql = "INSERT INTO chowner (cache_id, user_id) VALUES (".sql_escape(intval($_REQUEST['cacheid'])).", ".$newUserId.")";
                        mysql_query($sql);
                        if( mysql_affected_rows() > 0 ){
                            tpl_set_var('info_msg'," ".tr('chowner00')." <br /><br />");
                            mb_send_mail(getUserEmail($newUserId), tr('chowner01'), tr('chowner02').": ".$usr['username']." ".tr('chowner03').": ".getCacheName($_REQUEST['cacheid']).". ".tr('chowner04'), emailHeaders());
                        }
                        else
                            tpl_set_var('error_msg', "Wystąpił błąd podczas rozpoczynania procedury zmiany właściciela skrzynki.<br /><br />");
                    }
                }
                else
                    tpl_set_var('error_msg', "Użytkownik ".$_POST['username']." nie istnieje.<br /><br />");
            }
            // strona glowna - wybor skrzynki
            $cacheList = '';
            $bgColor='#ffffff';
            foreach( listUserCaches($usr['userid']) as $cache)
            {
                if ($bgColor=='#ffffff') $bgColor='#eeffee';
                else $bgColor='#ffffff';
                $cacheList .= '<tr bgcolor="'.$bgColor.'">
                <td>
                ';
                if( !isRequestPending($cache['cache_id']))
                    $cacheList .= "<a href='chowner.php?cacheid=".$cache['cache_id']."'>";
                $cacheList .= $cache['name'];
                if( isRequestPending($cache['cache_id']))
                {
                    $cacheList .= "</a> <a href='chowner.php?cacheid=".$cache['cache_id']."&abort=1'>[<font color='#ff0000'>".tr('adopt_14')."</font>]";
                }
                $cacheList .= "</a>";

                $cacheList .= "</td>
                <td>".$cache['date_hidden']."</td>
                </tr>
                ";
            }
            tpl_set_var('cacheList', $cacheList);
        }
        tpl_BuildTemplate();
    }
    else
        header("Location: index.php");


?>
