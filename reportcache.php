<?php

function reason($reason)
{
    switch( $reason )
    {
        case 1:
            return tr('reportcache01');
        case 2:
            return tr('reportcache02');
        case 3:
            return tr('reportcache03');
        case 4:
            return tr('reportcache04');
    }
}

//prepare the templates and include all neccessary
global $datetimeFormat, $site_name;
if (!isset($rootpath)) $rootpath = '';
require_once('./lib/common.inc.php');
if($usr==true)
{
    //Preprocessing
    if ($error == false)
    {
        $tplname = 'reportcache';
        tpl_set_var('noreason_error', '');
        tpl_set_var('notext_error', '');
        $cacheid = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid']+0 : 0;
        $sql = "SELECT name, wp_oc, user_id FROM caches WHERE cache_id='".sql_escape($cacheid)."'";
        $query = mysql_query($sql);

        if (mysql_num_rows($query) == 0)
        {
            $tplname = 'reportcache_nocache';
        }
        else
        {
            $cache = @mysql_fetch_array($query) or die("DB error.");
            tpl_set_var('cachename', htmlspecialchars($cache['name'], ENT_COMPAT, 'UTF-8'));
            tpl_set_var('cacheid', $cacheid);

            if( isset($_POST['ok']) )
            {
                if( $_POST['text'] == "" )
                {
                    tpl_set_var('notext_error','&nbsp;<b><font size="1" color="#ff0000">'. tr('reportcache05'). '.</font></b>');
                    $tplname = 'reportcache_notext';
                }
                else if( $_POST['reason'] == 0)
                    tpl_set_var('noreason_error', '&nbsp;<b><font size="1" color="#ff0000">'. tr('reportcache06'). '.</font></b>');
                else
                {
                    // formularz został wysłany

                    // pobierz adres email zglaszajacego
                    $query = sql("SELECT `email` FROM `user` WHERE `user_id`='&1'", $usr['userid']);
                    $cache_reporter = sql_fetch_array($query);

                    if( $_POST['adresat'] == "rr")
                    {
                        $tplname = 'reportcache_sent';
                        // zapisz zgłoszenie w bazie
                        $sql = "INSERT INTO reports (user_id, cache_id, text, type) VALUES ('".sql_escape($usr['userid'])."', '".sql_escape($cacheid)."', '".strip_tags(sql_escape($_POST['text']))."', '".sql_escape(intval($_POST['reason']))."' )";
                        @mysql_query($sql) or die("DB error");

                        // wysłanie powiadomień
                        $email_content = read_file($stylepath . '/email/newreport_octeam.email');

                        $email_content = mb_ereg_replace('{server}', $absolute_server_URI, $email_content);
                        $email_content = mb_ereg_replace('{reportcache10}', tr('reportcache10'), $email_content);
                        $email_content = mb_ereg_replace('{reportcache11}', tr('reportcache11'), $email_content);
                        $email_content = mb_ereg_replace('{reportcache12}', tr('reportcache12'), $email_content);
                        $email_content = mb_ereg_replace('{reportcache13}', tr('reportcache13'), $email_content);
                        $email_content = mb_ereg_replace('{reportcache14}', tr('reportcache14'), $email_content);
                        $email_content = mb_ereg_replace('{reportcache15}', tr('reportcache15'), $email_content);
                        $email_content = mb_ereg_replace('{reportcache16}', tr('reportcache16'), $email_content);
                        $email_content = mb_ereg_replace('{reportcache17}', tr('reportcache17'), $email_content);
                        $email_content = mb_ereg_replace('{reportcache18}', tr('reportcache18'), $email_content);
                        $email_content = mb_ereg_replace('{reportcache19}', tr('reportcache19'), $email_content);
                        $email_content = mb_ereg_replace('{date}', date($datetimeFormat), $email_content);
                        $email_content = mb_ereg_replace('{submitter}', $usr['username'], $email_content);
                        $email_content = mb_ereg_replace('{cachename}', $cache['name'], $email_content);
                        $email_content = mb_ereg_replace('{cache_wp}', $cache['wp_oc'], $email_content);
                        $email_content = mb_ereg_replace('{cacheid}', $cacheid, $email_content);
                        $email_content = mb_ereg_replace('{reason}', reason($_POST['reason']), $email_content);
                        $email_content = mb_ereg_replace('{text}', strip_tags(addslashes($_POST['text'])), $email_content);
                        // send email to RR

                        $emailheaders = "Content-Type: text/plain; charset=utf-8\r\n";
                        $emailheaders .= "From: ". $site_name." <".$cache_reporter['email'].">\r\n";
                        $emailheaders .= "Reply-To: ". $site_name." <".$cache_reporter['email'].">";

                        mb_send_mail($octeam_email, tr('reportcache07')." (".$cache['wp_oc'].")", $email_content, $emailheaders);
                        // echo($octeam_email. tr('reportcache07'). $email_content. $emailheaders);
                    }
                    else
                        $tplname = 'reportcache_sent_owner';
                    //get email address of cache owner
                    if( $_POST['adresat'] == "rr")
                    {
                        $email_content = read_file($stylepath . '/email/newreport_cacheowner.email');
                    }
                    else
                    {
                        $email_content = read_file($stylepath . '/email/newreport_cacheowneronly.email');
                    }

                    $query = sql("SELECT `email` FROM `user` WHERE `user_id`='&1'", $cache['user_id']);
                    $cache_owner = sql_fetch_array($query);

                    $email_content = mb_ereg_replace('{server}', $absolute_server_URI, $email_content);
                    $email_content = mb_ereg_replace('{reportcache10}', tr('reportcache10'), $email_content);
                    $email_content = mb_ereg_replace('{reportcache11}', tr('reportcache11'), $email_content);
                    $email_content = mb_ereg_replace('{reportcache12}', tr('reportcache12'), $email_content);
                    $email_content = mb_ereg_replace('{reportcache15}', tr('reportcache15'), $email_content);
                    $email_content = mb_ereg_replace('{reportcache17}', tr('reportcache17'), $email_content);
                    $email_content = mb_ereg_replace('{reportcache18}', tr('reportcache18'), $email_content);
                    $email_content = mb_ereg_replace('{reportcache19}', tr('reportcache19'), $email_content);
                    $email_content = mb_ereg_replace('{octeamEmailsSignature}', $octeamEmailsSignature, $email_content);
                    $email_content = mb_ereg_replace('{date}', date($datetimeFormat), $email_content);
                    $email_content = mb_ereg_replace('{submitter}', $usr['username'], $email_content);
                    $email_content = mb_ereg_replace('{cachename}', $cache['name'], $email_content);
                    $email_content = mb_ereg_replace('{cache_wp}', $cache['wp_oc'], $email_content);
                    $email_content = mb_ereg_replace('{cacheid}', $cacheid, $email_content);
                    $email_content = mb_ereg_replace('{reason}', reason($_POST['reason']), $email_content);
                    $email_content = mb_ereg_replace('{text}', strip_tags($_POST['text']), $email_content);

                    //send email to cache owner
                    $emailheaders = "Content-Type: text/plain; charset=utf-8\r\n";
                    $emailheaders .= "From: ".$usr['username']." <".$usr['email'].">\r\n";
                    $emailheaders .= "Reply-To: ".$usr['username']." <".$usr['email'].">";
                    $email_content = mb_ereg_replace('{server}', $absolute_server_URI, $email_content);
                    $email_content = mb_ereg_replace('{reportcache10}', tr('reportcache10'), $email_content);
                    $email_content = mb_ereg_replace('{reportcache11}', tr('reportcache11'), $email_content);
                    $email_content = mb_ereg_replace('{reportcache12}', tr('reportcache12'), $email_content);
                    $email_content = mb_ereg_replace('{reportcache14}', tr('reportcache14'), $email_content);
                    $email_content = mb_ereg_replace('{reportcache15}', tr('reportcache15'), $email_content);
                    $email_content = mb_ereg_replace('{reportcache17}', tr('reportcache17'), $email_content);
                    $email_content = mb_ereg_replace('{reportcache18}', tr('reportcache18'), $email_content);
                    $email_content = mb_ereg_replace('{reportcache19}', tr('reportcache19'), $email_content);
                    $email_content = mb_ereg_replace('{reportcache21}', tr('reportcache21'), $email_content);
                    $email_content = mb_ereg_replace('{octeamEmailsSignature}', $octeamEmailsSignature, $email_content);

                    mb_send_mail($cache_owner['email'], tr('reportcache08')." ".$cache['wp_oc'], $email_content, $emailheaders);

                    // send email to cache reporter
                    $emailheaders = "Content-Type: text/plain; charset=utf-8\r\n";
                    $emailheaders .= "From: ". $site_name." <$octeam_email>\r\n";
                    $email_content = read_file($stylepath . '/email/newreport_reporter.email');
                    $email_content = mb_ereg_replace('{server}', $absolute_server_URI, $email_content);
                    $email_content = mb_ereg_replace('{reportcache10}', tr('reportcache10'), $email_content);
                    $email_content = mb_ereg_replace('{reportcache11}', tr('reportcache11'), $email_content);
                    $email_content = mb_ereg_replace('{reportcache12}', tr('reportcache12'), $email_content);
                    $email_content = mb_ereg_replace('{reportcache14}', tr('reportcache14'), $email_content);
                    $email_content = mb_ereg_replace('{reportcache15}', tr('reportcache15'), $email_content);
                    $email_content = mb_ereg_replace('{reportcache17}', tr('reportcache17'), $email_content);
                    $email_content = mb_ereg_replace('{reportcache20}', tr('reportcache20'), $email_content);
                    $email_content = mb_ereg_replace('{octeamEmailsSignature}', $octeamEmailsSignature, $email_content);
                    $email_content = mb_ereg_replace('{date}', date($datetimeFormat), $email_content);
                    $email_content = mb_ereg_replace('{cachename}', $cache['name'], $email_content);
                    $email_content = mb_ereg_replace('{cache_wp}', $cache['wp_oc'], $email_content);
                    $email_content = mb_ereg_replace('{cacheid}', $cacheid, $email_content);
                    $email_content = mb_ereg_replace('{reason}', reason($_POST['reason']), $email_content);
                    $email_content = mb_ereg_replace('{text}', strip_tags($_POST['text']), $email_content);

                    mb_send_mail($cache_reporter['email'], tr('reportcache09')." ".$cache['wp_oc'], $email_content, $emailheaders);

                    //echo($cache_owner['email']. "[OC PL] Zgłoszono problem dotyczący Twojej skrzynki". $email_content. $emailheaders);
                }
            }
        }
    }
    tpl_BuildTemplate();
}
?>
