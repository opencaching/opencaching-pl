<?php

use Utils\Database\XDb;
global $bgcolor1, $bgcolor2;

function colorCacheStatus($text, $id)
{
    switch ($id) {
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
    if (str_replace(" ", "", $cacheName) == "")
        return "[bez nazwy]";
    return $cacheName;
}

function getUsername($userid)
{
    return XDb::xMultiVariableQueryValue(
        "SELECT username FROM user WHERE user_id= :1 LIMIT 1",
        null, $userid);
}

function getCachename($cacheid)
{
    return XDb::xMultiVariableQueryValue(
        "SELECT name FROM caches WHERE cache_id= :1 LIMIT 1",
        null, $cacheid);
}

function getCacheOwnername($cacheid)
{
    return XDb::xMultiVariableQueryValue(
        "SELECT username FROM user WHERE user_id= :1 LIMIT 1",
        null, getCacheOwnerId($cacheid));
}

function getCacheOwnerId($cacheid)
{
    return XDb::xMultiVariableQueryValue(
        "SELECT user_id FROM caches WHERE cache_id= :1 LIMIT 1",
        null, $cacheid);
}

function actionRequired($cacheid)
{
    // check if cache requires activation
    return XDb::xMultiVariableQueryValue(
        "SELECT status FROM caches WHERE cache_id= :1 AND status = 4",
        null, $cacheid);
}

function activateCache($cacheid)
{
    // activate the cache by changing its status to yet unavailable
    if (actionRequired($cacheid)) {
        if ( XDb::xSql("UPDATE caches SET status = 5 WHERE cache_id= ? ", $cacheid) ) {
            return true;
        } else
            return false;
    }
    return false;
}

function declineCache($cacheid)
{
    // activate the cache by changing its status to yet unavailable
    if (actionRequired($cacheid)) {
        if (XDb::xSql("UPDATE caches SET status = 6 WHERE cache_id= ? ", $cacheid)) {
            return true;
        } else
            return false;
    }
    return false;
}

function getAssignedUserId($cacheid)
{
    // check if cache requires activation
    return XDb::xMultiVariableQueryValue(
        "SELECT user_id FROM approval_status WHERE cache_id= :1 LIMIT 1",
        false, $cacheid);
}

function assignUserToCase($userid, $cacheid)
{
    // check if user is in RR
    if( 0 == XDb::xMultiVariableQueryValue(
        "SELECT COUNT(user_id) FROM user WHERE admin = 1 AND user_id = :1 ", 0, $userid)){

        return false;
    }

    XDb::xSql(
        "INSERT INTO approval_status (cache_id, user_id, status, date_approval)
        VALUES ( ?, ?, 2, NOW())
        ON DUPLICATE KEY UPDATE user_id = ?",
        $cacheid, $userid, $userid);
}

function notifyOwner($cacheid, $msgType)
{
    // msgType - 0 = cache accepted, 1 = cache declined (=archived)
    global $stylepath, $usr, $octeam_email, $site_name, $absolute_server_URI, $octeamEmailsSignature;
    $user_id = getCacheOwnerId($cacheid);

    $cachename = getCachename($cacheid);
    if ($msgType == 0) {
        $email_content = file_get_contents($stylepath . '/email/activated_cache.email');
    } else {
        $email_content = file_get_contents($stylepath . '/email/archived_cache.email');
    }
    $email_headers = "Content-Type: text/plain; charset=utf-8\r\n";
    $email_headers .= "From: $site_name <$octeam_email>\r\n";
    $email_headers .= "Reply-To: $octeam_email\r\n";
    $email_content = mb_ereg_replace('{server}', $absolute_server_URI, $email_content);
    $email_content = mb_ereg_replace('{cachename}', $cachename, $email_content);
    $email_content = mb_ereg_replace('{cacheid}', $cacheid, $email_content);
    $email_content = mb_ereg_replace('{octeamEmailsSignature}', $octeamEmailsSignature, $email_content);
    $email_content = mb_ereg_replace('{cacheArchived_01}', tr('cacheArchived_01'), $email_content);
    $email_content = mb_ereg_replace('{cacheArchived_02}', tr('cacheArchived_02'), $email_content);
    $email_content = mb_ereg_replace('{cacheArchived_03}', tr('cacheArchived_03'), $email_content);
    $email_content = mb_ereg_replace('{cacheArchived_04}', tr('cacheArchived_04'), $email_content);
    $email_content = mb_ereg_replace('{cacheArchived_05}', tr('cacheArchived_05'), $email_content);
    $email_content = mb_ereg_replace('{Cacheactivated_01}', tr('Cacheactivated_01'), $email_content);
    $email_content = mb_ereg_replace('{Cacheactivated_02}', tr('Cacheactivated_02'), $email_content);
    $email_content = mb_ereg_replace('{Cacheactivated_03}', tr('Cacheactivated_03'), $email_content);
    $email_content = mb_ereg_replace('{Cacheactivated_04}', tr('Cacheactivated_04'), $email_content);
    $email_content = mb_ereg_replace('{Cacheactivated_05}', tr('Cacheactivated_05'), $email_content);


    $owner_email['email'] = XDb::xMultiVariableQueryValue(
        "SELECT `email` FROM `user` WHERE `user_id`= :1 LIMIT 1", '', $user_id);

    if ($msgType == 0) {
        //send email to owner
        mb_send_mail($owner_email['email'], tr('viewPending_01') . ": " . $cachename, $email_content, $email_headers);
        //send email to approver
        mb_send_mail($usr['email'], tr('viewPending_01') . ": " . $cachename, tr('viewPending_02') . ":\n" . $email_content, $email_headers);
        // generate automatic log about status cache
        $log_text = tr("viewPending_03");
        $log_uuid = create_uuid();
        XDb::xSql(
            "INSERT INTO `cache_logs`
                (`id`, `cache_id`, `user_id`, `type`, `date`, `text`, `text_html`, `text_htmledit`, `date_created`, `last_modified`, `uuid`, `node`,`encrypt`)
            VALUES ('', ?, ?, '12', NOW(), ?, '0', '0', NOW(), NOW(), ?, '2', '0')",
            $cacheid, $usr['userid'], $log_text, $log_uuid);

    } else {
        //send email to owner
        mb_send_mail($owner_email['email'], tr('viewPending_04') . ": " . $cachename, $email_content, $email_headers);
        //send email to approver
        mb_send_mail($usr['email'], tr('viewPending_04') . ": " . $cachename, tr('viewPending_05') . ":\n" . $email_content, $email_headers);

        // generate automatic log about status cache
        $log_text = tr("viewPending_06");
        $log_uuid = create_uuid();
        XDb::xSql(
            "INSERT INTO `cache_logs`
                (`id`, `cache_id`, `user_id`, `type`, `date`, `text`, `text_html`, `text_htmledit`, `date_created`, `last_modified`, `uuid`, `node`,`encrypt`)
            VALUES ('', ?, ?, ?, NOW(), ?, ?, ?, NOW(), NOW(), ?, ?, ?)",
            $cacheid, $usr['userid'], 12, $log_text, 0, 0, $log_uuid, 2, 0);
    }
}

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');
$tplname = 'viewpendings';
$content = '';
// tylko dla członków COG
if ($error == false && $usr['admin']) {
    if (isset($_GET['cacheid'])) {
        if (isset($_GET['assign'])) {
            if (assignUserToCase($_GET['assign'], $_GET['cacheid'])) {
                $confirm = "<p>" . tr("viewPending_07") . " " . getUsername($_GET['assign']) . " " . tr("viewPending_07") . ".</p>";
                tpl_set_var('confirm', $confirm);
            } else {
                tpl_set_var('confirm', '');
            }
        } else {
            if (actionRequired($_GET['cacheid'])) {
                // requires activation
                if (isset($_GET['confirm']) && isset($_GET['user_id']) && $_GET['confirm'] == 1) {
                    // confirmed - change the status and notify the owner now
                    if (activateCache($_GET['cacheid'])) {
                        assignUserToCase($usr['userid'], $_GET['cacheid']);
                        notifyOwner($_GET['cacheid'], 0);
                        lib\Objects\User\AdminNote::addAdminNote($usr['userid'], $_GET['user_id'], true, lib\Objects\User\AdminNote::CACHE_PASS, $_GET['cacheid']);
                        $confirm = "<p> " . tr("viewPending_09") . ".</p>";
                    } else {
                        $confirm = "<p> " . tr("viewPending_10") . ".</p>";
                    }
                } else if (isset($_GET['confirm']) && isset($_GET['user_id']) && $_GET['confirm'] == 2) {
                    // declined - change status to archived and notify the owner now
                    if (declineCache($_GET['cacheid'])) {
                        assignUserToCase($usr['userid'], $_GET['cacheid']);
                        notifyOwner($_GET['cacheid'], 1);
                        lib\Objects\User\AdminNote::addAdminNote($usr['userid'], $_GET['user_id'], true, lib\Objects\User\AdminNote::CACHE_BLOCKED, $_GET['cacheid']);
                        $confirm = "<p> " . tr("viewPending_11") . ".</p>";
                    } else {
                        $confirm = "<p> " . tr("viewPending_12") . ".</p>";
                    }
                } else if ($_GET['action'] == 1 && isset($_GET['user_id'])) {
                    // require confirmation
                    $confirm = "<p> " . tr("viewPending_13") . " \"<a href='viewcache.php?cacheid=" . $_GET['cacheid'] . "'>" . getCachename($_GET['cacheid']) . "</a>\" " . tr("viewPending_14") . " " . getCacheOwnername($_GET['cacheid']) . ". " . tr("viewPending_15") . ".</p>";
                    $confirm .= "<p><a href='viewpendings.php?user_id=".$_GET['user_id']."&amp;cacheid=" . $_GET['cacheid'] . "&amp;confirm=1'>" . tr("viewPending_16") . "</a> |
                        <a href='viewpendings.php'>" . tr("viewPending_17") . "</a></p>";
                } else if ($_GET['action'] == 2 && isset($_GET['user_id'])) {
                    // require confirmation
                    $confirm = "<p> " . tr("viewPending_18") . " \"<a href='viewcache.php?cacheid=" . $_GET['cacheid'] . "'>" . getCachename($_GET['cacheid']) . "</a>\" " . tr("viewPending_14") . " " . getCacheOwnername($_GET['cacheid']) . ". " . tr("viewPending_19") . ".</p>";
                    $confirm .= "<p><a href='viewpendings.php?user_id=".$_GET['user_id']."&amp;cacheid=" . $_GET['cacheid'] . "&amp;confirm=2'>" . tr("viewPending_20") . "</a> |
                        <a href='viewpendings.php'>" . tr("viewPending_17") . "</a></p>";
                }
                tpl_set_var('confirm', $confirm);
            } else {
                tpl_set_var('confirm', '<p>' . tr('viewPending_21') . '.</p>');
            }
        }
    } else {
        tpl_set_var('confirm', '');
    }

    $stmt = XDb::xSql(
        "SELECT cache_status.id AS cs_id, cache_status.pl AS cache_status, user.username AS username,
                user.user_id AS user_id, caches.cache_id AS cache_id, caches.name AS cachename,
                IFNULL(`cache_location`.`adm3`, '') AS `adm3`, caches.date_created AS date_created
        FROM cache_status, user, (
            `caches` LEFT JOIN `cache_location` ON `caches`.`cache_id` = `cache_location`.`cache_id`)
        WHERE cache_status.id = caches.status
            AND caches.user_id = user.user_id
            AND caches.status = 4
        ORDER BY caches.date_created DESC");

    $row_num = 0;
    while ($report = XDb::xFetchArray($stmt)) {
        $assignedUserId = getAssignedUserId($report['cache_id']);
        if ($row_num % 2)
            $bgcolor = "bgcolor1";
        else
            $bgcolor = "bgcolor2";

        $content .= "<tr>\n";
        $content .= "<td class='" . $bgcolor . "'><a class=\"links\" href='viewcache.php?cacheid=" . $report['cache_id'] . "'>" . nonEmptyCacheName($report['cachename']) . "</a><br/><span style=\"font-weight:bold;font-size:10px;color:blue;\">" . $report['adm3'] . "</span></td>\n";
        $content .= "<td class='" . $bgcolor . "'>" . $report['date_created'] . "</td>\n";
        $content .= "<td class='" . $bgcolor . "'><a class=\"links\" href='viewprofile.php?userid=" . $report['user_id'] . "'>" . $report['username'] . "</a></td>\n";
        $content .= "<td class='" . $bgcolor . "'><img src=\"tpl/stdstyle/images/blue/arrow.png\" alt=\"\" />&nbsp;<a class=\"links\" href='viewpendings.php?user_id=".$report['user_id']."&amp;cacheid=" . $report['cache_id'] . "&amp;action=1'>" . tr('accept') . "</a><br/>
            <img src=\"tpl/stdstyle/images/blue/arrow.png\" alt=\"\" />&nbsp;<a class=\"links\" href='viewpendings.php?user_id=".$report['user_id']."&amp;cacheid=" . $report['cache_id'] . "&amp;action=2'>" . tr('block') . "</a><br/>
            <img src=\"tpl/stdstyle/images/blue/arrow.png\" alt=\"\" />&nbsp;<a class=\"links\" href='viewpendings.php?cacheid=" . $report['cache_id'] . "&amp;assign=" . $usr['userid'] . "'>" . tr('assign_yourself') . "</a></td>\n";
        $content .= "<td class='" . $bgcolor . "'><a class=\"links\" href='viewprofile.php?userid=" . $assignedUserId . "'>" . getUsername($assignedUserId) . "</a><br/></td>";
        $content .= "</tr>\n";
        $row_num++;
    }
    tpl_set_var('content', $content);
}
else {
    $tplname = 'viewpendings_error';
}
tpl_BuildTemplate();
?>


