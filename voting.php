<?php

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

// for admins' eyes only
if( $usr['admin'] )
{
//Preprocessing
if ($error == false)
{
$tplname = 'voting';

$userid = intval(isset($_REQUEST['userid']) ? $_REQUEST['userid']+0 : 0);
if( $_GET['stat_ban'] == 1 && $usr['admin'] )
{
$sql = "UPDATE user SET stat_ban = 1 - stat_ban WHERE user_id = ".intval($userid);
mysql_query($sql);
}

$rs = sql("SELECT report

        if (mysql_num_rows($rs) == 0)
        {
            $tplname = 'error';
            tpl_set_var('tplname', 'voting');
            tpl_set_var('error_msg', $err_no_user);
        }
        else
        {
            $sql = "SELECT description FROM user WHERE user_id = ".$userid;
            $description = @mysql_result(@mysql_query($sql),0);
            tpl_set_var('description',nl2br($description));

            if( $description != "" )
            {
                tpl_set_var('opis_start', '');
                tpl_set_var('opis_end', '');
            }
            else
            {
                tpl_set_var('opis_start', '<!--');
                tpl_set_var('opis_end', '-->');
            }

            $sql = "SELECT COUNT(*) FROM caches WHERE user_id = '$userid' AND status <> 5";
            if( $odp = mysql_query($sql) )
                $hidden_count = mysql_result($odp,0);
            else
                $hidden_count = 0;

            $sql = "SELECT COUNT(*) founds_count
FROM cache_logs
WHERE user_id = '$userid' AND type = 1 AND deleted = 0";

            if( $odp = mysql_query($sql) )
                $founds_count = mysql_result($odp,0);
            else
                $founds_count = 0;

            $sql = "SELECT COUNT(*) not_founds_count
FROM cache_logs
WHERE user_id = '$userid' AND type = 2 AND deleted = 0";

            if( $odp = mysql_query($sql) )
                $not_founds_count = mysql_result($odp,0);
            else
                $not_founds_count = 0;


            $record = sql_fetch_array($rs);
            tpl_set_var('statlink', $absolute_server_URI.'statpics/' . ($userid+0) . '.jpg');
            tpl_set_var('username', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'));
            tpl_set_var('type_found', type_found($userid, 1, $lang));
            tpl_set_var('type_notfound', type_found($userid, 2, $lang));
            tpl_set_var('type_hidden', type_hidden($userid, $lang));
            tpl_set_var('hidden', $hidden_count);
            tpl_set_var('userid', htmlspecialchars($userid, ENT_COMPAT, 'UTF-8'));
            tpl_set_var('hidden', $hidden_count);
            tpl_set_var('founds', $founds_count);
            tpl_set_var('not_founds', $not_founds_count);
            tpl_set_var('recommended', sqlValue("SELECT COUNT(*) FROM `cache_rating` WHERE `user_id` = '" . sql_escape($_REQUEST['userid']) . "'", 0));
            tpl_set_var('maxrecommended', floor($founds_count * rating_percentage / 100));

            tpl_set_var('country', htmlspecialchars($record['country'], ENT_COMPAT, 'UTF-8'));
            tpl_set_var('registered', strftime($dateformat, strtotime($record['date_created'])));
            if( $usr['admin'] )
            {
                tpl_set_var('email', '(<a href="mailto:'.strip_tags($record['email']).'">'.strip_tags($record['email']).'</a>)');

                if( !$record['stat_ban'] )
                    tpl_set_var('stat_ban', '<tr><td align="left" class="header-small" colspan="2">[<a href="viewprofile.php?userid = '.$userid.'&stat_ban = 1"><font color="#ff0000">Zablokuj statystyki tego użytkownika</font></a>]</td></tr>');
else
tpl_set_var('stat_ban', '<tr><td align="left" class="header-small" colspan="2">[<a href="viewprofile.php?userid='.$userid.'&stat_ban=1"><font color="#00ff00">Odblokuj statystyki tego użytkownika</font></a>]</td></tr>');
}
else
{
tpl_set_var('stat_ban', '');
tpl_set_var('email', '');
}
$options = '';
if ($record['pmr_flag'] == 1)
{
$options .= $using_pmr_message;
}

tpl_set_var('options', $options);
tpl_set_var('uuid', htmlspecialchars($record['uuid'], ENT_COMPAT, 'UTF-8'));
}
}

tpl_BuildTemplate();
}
else
header('Location: index.php');
?>
