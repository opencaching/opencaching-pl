<?php
global $bgcolor1, $bgcolor2;

    function colorCacheStatus($text, $id )
    {
        switch( $id )
        {
            case '1':
                return "<font color='green'>$text</font>";
            case '2':
                return "<font color='orange'>$text</font>";
            case '3':
                return "<font color='red'>$text</font>";
            default:
                return "<font color='gray'>$text</font>";
        }
    }

    function nonEmptyCacheName($cacheName)
    {
        if( str_replace(" ", "", $cacheName) == "" )
            return "[bez nazwy]";
        return $cacheName;
    }

    function getUsername($userid)
    {
        $sql = "SELECT username FROM user WHERE user_id='".sql_escape(intval($userid))."'";
        $query = mysql_query($sql) or die();
        if( mysql_num_rows($query) > 0)
            return mysql_result($query,0);
        return null;
    }

    function getCachename($cacheid)
    {
        $sql = "SELECT name FROM caches WHERE cache_id='".sql_escape(intval($cacheid))."'";
        $query = mysql_query($sql) or die();
        if( mysql_num_rows($query) > 0)
            return mysql_result($query,0);
        return null;
    }

    function getCacheOwnername($cacheid)
    {
        $sql = "SELECT user_id FROM caches WHERE cache_id='".sql_escape(intval($cacheid))."'";
        $query = mysql_query($sql) or die();
        if( mysql_num_rows($query) > 0)
            return getUsername(mysql_result($query,0));
        return null;
    }

    function getCacheOwnerId($cacheid)
    {
        $sql = "SELECT user_id FROM caches WHERE cache_id='".sql_escape(intval($cacheid))."'";
        $query = mysql_query($sql) or die();
        if( mysql_num_rows($query) > 0)
            return mysql_result($query,0);
        return null;
    }

    function actionRequired($cacheid)
    {
        // check if cache requires activation
        $sql = "SELECT status FROM caches WHERE cache_id='".sql_escape(intval($cacheid))."' AND status = 4";
        $query = mysql_query($sql) or die();
        if( mysql_num_rows($query) > 0)
            return true;
        return false;
    }

    function activateCache($cacheid)
    {
        // activate the cache by changing its status to yet unavailable
        if( actionRequired($cacheid) )
        {
            $sql = "UPDATE caches SET status = 5 WHERE cache_id='".sql_escape(intval($cacheid))."'";
            if( mysql_query($sql) )
            {
                sql("UPDATE sysconfig SET value = value - 1 WHERE name = 'hidden_for_approval'");
                return true;
            }
            else
                return false;

        }
        return false;
    }

    function declineCache($cacheid)
    {
        // activate the cache by changing its status to yet unavailable
        if( actionRequired($cacheid) )
        {
            $sql = "UPDATE caches SET status = 6 WHERE cache_id='".sql_escape(intval($cacheid))."'";
            if( mysql_query($sql) )
            {
                sql("UPDATE sysconfig SET value = value - 1 WHERE name = 'hidden_for_approval'");
                return true;
            }
            else
                return false;
        }
        return false;
    }

    function getAssignedUserId($cacheid)
    {
        // check if cache requires activation
        $sql = "SELECT user_id FROM approval_status WHERE cache_id='".sql_escape(intval($cacheid))."'";
        $query = mysql_query($sql) or die();
        if( mysql_num_rows($query) > 0 )
            return mysql_result($query,0);
        return false;
    }

    function assignUserToCase($userid, $cacheid)
    {
        // check if user is in RR
        $sql = "SELECT user_id FROM user WHERE admin = 1 AND user_id = '".sql_escape(intval($userid))."'";
        if( mysql_num_rows(mysql_query($sql)) == 0 )
            return false;

        $sql = "INSERT INTO approval_status (cache_id, user_id, status, date_approval) VALUES
                        (".sql_escape(intval($cacheid)).", ".sql_escape(intval($userid)).", 2,NOW())
                        ON DUPLICATE KEY UPDATE user_id = '".sql_escape(intval($userid))."'";
        $query = mysql_query($sql) or die();
    }

    function notifyOwner($cacheid, $msgType)
    {
        // msgType - 0 = cache accepted, 1 = cache declined (=archived)
        global $stylepath, $usr, $octeam_email, $site_name;
        $user_id = getCacheOwnerId($cacheid);

        $cachename = getCachename($cacheid);
        if( $msgType == 0 )
        {
            $email_content = read_file($stylepath . '/email/activated_cache.email');
        }
        else
        {
            $email_content = read_file($stylepath . '/email/archived_cache.email');
        }
        $email_content = mb_ereg_replace('%cachename%', $cachename, $email_content);
        $email_content = mb_ereg_replace('%cacheid%', $cacheid, $email_content);
        $email_headers = "Content-Type: text/plain; charset=utf-8\r\n";
        $email_headers .= "From: $site_name <$octeam_email>\r\n";
        $email_headers .= "Reply-To: $octeam_email\r\n";

        $query = sql("SELECT `email` FROM `user` WHERE `user_id`='&1'", $user_id);
        $owner_email = sql_fetch_array($query);

      if( $msgType == 0 )
        {
            //send email to owner
            mb_send_mail($owner_email['email'], tr('viewPending_01'). ":" .$cachename, $email_content, $email_headers);
            //send email to approver
            mb_send_mail($usr['email'], tr('viewPending_01'). ":" .$cachename, tr('viewPending_02'). ":\n".$email_content, $email_headers);
            // generate automatic log about status cache
            $log_text=tr("viewPending_03");
            $log_uuid = create_uuid();
            sql("INSERT INTO `cache_logs` (`id`, `cache_id`, `user_id`, `type`, `date`, `text`, `text_html`, `text_htmledit`, `date_created`, `last_modified`, `uuid`, `node`,`encrypt`)
                                     VALUES ('', '&1', '&2', '&3', NOW(), '&4', '&5', '&6', NOW(), NOW(), '&7', '&8','&9')",
                                     $cacheid, $usr['userid'], 12, $log_text, 0, 0, $log_uuid, 2, 0);


        }
        else
        {
            //send email to owner
            mb_send_mail($owner_email['email'], tr('viewPending_04'). ":" .$cachename, $email_content, $email_headers);
            //send email to approver
            mb_send_mail($usr['email'], tr('viewPending_04'). ":" .$cachename, tr('viewPending_05'). ":\n".$email_content, $email_headers);

            // generate automatic log about status cache
            $log_text=tr("viewPending_06");
            $log_uuid = create_uuid();
            sql("INSERT INTO `cache_logs` (`id`, `cache_id`, `user_id`, `type`, `date`, `text`, `text_html`, `text_htmledit`, `date_created`, `last_modified`, `uuid`, `node`,`encrypt`)
                                     VALUES ('', '&1', '&2', '&3', NOW(), '&4', '&5', '&6', NOW(), NOW(), '&7', '&8','&9')",
                                     $cacheid, $usr['userid'], 12, $log_text, 0, 0, $log_uuid, 2, 0);


    }


    }

    //prepare the templates and include all neccessary
    require_once('./lib/common.inc.php');
    $tplname = 'viewpendings';
    $content = '';
    // tylko dla członków COG
    if ($error == false && $usr['admin'])
    {
        if( isset($_GET['cacheid']) )
        {
            if( isset($_GET['assign']) )
            {
                if( assignUserToCase($_GET['assign'], $_GET['cacheid']) )
                {
                    $confirm = "<p>".tr("viewPending_07")." ".getUsername(sql_escape($_GET['assign']))." ".tr("viewPending_07").".</p>";
                    tpl_set_var('confirm', $confirm);
                }
                else
                {
                    tpl_set_var('confirm', '');
                }
            }
            else
            {
                if( actionRequired($_GET['cacheid']) )
                {
                    // requires activation
                    if( isset($_GET['confirm']) && $_GET['confirm'] == 1 )
                    {
                        // confirmed - change the status and notify the owner now
                        if( activateCache($_GET['cacheid']) )
                        {
                            assignUserToCase($usr['userid'], $_GET['cacheid']);
                            notifyOwner($_GET['cacheid'], 0);
                            $confirm = "<p> ".tr("viewPending_09").".</p>";
                        }
                        else
                        {
                            $confirm = "<p> ".tr("viewPending_10").".</p>";
                        }
                    }
                    else if( isset($_GET['confirm']) && $_GET['confirm'] == 2 )
                    {
                        // declined - change status to archived and notify the owner now
                        if( declineCache($_GET['cacheid']) )
                        {
                            assignUserToCase($usr['userid'], $_GET['cacheid']);
                            notifyOwner($_GET['cacheid'], 1);
                            $confirm = "<p> ".tr("viewPending_11").".</p>";
                        }
                        else
                        {
                            $confirm = "<p> ".tr("viewPending_12").".</p>";
                        }
                    }
                    else if( $_GET['action'] == 1 )
                    {
                        // require confirmation
                        $confirm = "<p> ".tr("viewPending_13")." \"<a href='viewcache.php?cacheid=".$_GET['cacheid']."'>".getCachename($_GET['cacheid'])."</a>\" ".tr("viewPending_14")." ".getCacheOwnername($_GET['cacheid']).". ".tr("viewPending_15").".</p>";
                        $confirm .= "<p><a href='viewpendings.php?cacheid=".$_GET['cacheid']."&amp;confirm=1'>".tr("viewPending_16")."</a> |
                        <a href='viewpendings.php'>".tr("viewPending_17")."</a></p>";
                    }
                    else if( $_GET['action'] == 2 )
                    {
                        // require confirmation
                        $confirm = "<p> ".tr("viewPending_18")." \"<a href='viewcache.php?cacheid=".$_GET['cacheid']."'>".getCachename($_GET['cacheid'])."</a>\" ".tr("viewPending_14")." ".getCacheOwnername($_GET['cacheid']).". ".tr("viewPending_19").".</p>";
                        $confirm .= "<p><a href='viewpendings.php?cacheid=".$_GET['cacheid']."&amp;confirm=2'>".tr("viewPending_20")."</a> |
                        <a href='viewpendings.php'>".tr("viewPending_17")."</a></p>";
                    }
                    tpl_set_var('confirm', $confirm);
                }
                else
                {
                    tpl_set_var('confirm', '<p>'.tr('viewPending_21').'.</p>');
                }
            }
        }
        else
        {
            tpl_set_var('confirm', '');
        }
        $sql = "SELECT cache_status.id AS cs_id,
                                     cache_status.pl AS cache_status,
                                     user.username AS username,
                                     user.user_id AS user_id,
                                     caches.cache_id AS cache_id,
                                     caches.name AS cachename,
                                    IFNULL(`cache_location`.`adm3`, '') AS `adm3`,
                                     caches.date_created AS date_created
                        FROM cache_status, user, (`caches` LEFT JOIN `cache_location` ON `caches`.`cache_id` = `cache_location`.`cache_id`)
                        WHERE cache_status.id = caches.status
                                    AND caches.user_id = user.user_id
                                    AND caches.status = 4  ORDER BY caches.date_created DESC";
        $query = mysql_query($sql) or die("DB error");
        $row_num = 0;
        while( $report = mysql_fetch_array($query) )
        {
            $assignedUserId = getAssignedUserId($report['cache_id']);
            if( $row_num % 2 )
                $bgcolor = "bgcolor1";
            else
                $bgcolor = "bgcolor2";

            $content .= "<tr>\n";
            $content .= "<td class='".$bgcolor."'><a class=\"links\" href='viewcache.php?cacheid=".$report['cache_id']."'>".nonEmptyCacheName($report['cachename'])."</a><br/><span style=\"font-weight:bold;font-size:10px;color:blue;\">".$report['adm3']."</span></td>\n";
            $content .= "<td class='".$bgcolor."'>".$report['date_created']."</td>\n";
            $content .= "<td class='".$bgcolor."'><a class=\"links\" href='viewprofile.php?userid=".$report['user_id']."'>".$report['username']."</a></td>\n";
            $content .= "<td class='".$bgcolor."'><img src=\"tpl/stdstyle/images/blue/arrow.png\" alt=\"\" />&nbsp;<a class=\"links\" href='viewpendings.php?cacheid=".$report['cache_id']."&amp;action=1'>".tr('accept')."</a><br/>
            <img src=\"tpl/stdstyle/images/blue/arrow.png\" alt=\"\" />&nbsp;<a class=\"links\" href='viewpendings.php?cacheid=".$report['cache_id']."&amp;action=2'>".tr('block')."</a><br/>
            <img src=\"tpl/stdstyle/images/blue/arrow.png\" alt=\"\" />&nbsp;<a class=\"links\" href='viewpendings.php?cacheid=".$report['cache_id']."&amp;assign=".$usr['userid']."'>".tr('assign_yourself')."</a></td>\n";
            $content .= "<td class='".$bgcolor."'><a class=\"links\" href='viewprofile.php?userid=".$assignedUserId."'>".getUsername($assignedUserId)."</a><br/></td>";
            $content .= "</tr>\n";
            $row_num++;
        }
        tpl_set_var('content', $content);
    }
    else
    {
        $tplname = 'viewpendings_error';
    }
    tpl_BuildTemplate();

?>
