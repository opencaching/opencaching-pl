<?php

use src\Models\Admin\AdminNote;
use src\Models\ApplicationContainer;
use src\Models\OcConfig\OcConfig;
use src\Models\User\User;
use src\Utils\Database\XDb;
use src\Utils\Generators\Uuid;

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
        if (XDb::xSql("UPDATE caches SET status = 5 WHERE cache_id= ? ", $cacheid)) {
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
    // check if user is in OC Team Member
    $user = User::fromUserIdFactory($userid);
    if (!$user || !$user->hasOcTeamRole()) {
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
    global $absolute_server_URI;

    $user = ApplicationContainer::GetAuthorizedUser();
    if(!$user) {
        return;
    }

    $user_id = getCacheOwnerId($cacheid);

    $cachename = getCachename($cacheid);
    if ($msgType == 0) {
        $email_content = file_get_contents(__DIR__ . '/resources/email/activated_cache.email');
    } else {
        $email_content = file_get_contents(__DIR__ . '/resources/email/archived_cache.email');
    }
    $email_headers = "Content-Type: text/plain; charset=utf-8\r\n";
    $email_headers .= "From: " . OcConfig::getSiteName() . " <" . OcConfig::getEmailAddrOcTeam() . ">\r\n";
    $email_headers .= "Reply-To: " . OcConfig::getEmailAddrOcTeam() . "\r\n";
    $email_content = mb_ereg_replace('{server}', $absolute_server_URI, $email_content);
    $email_content = mb_ereg_replace('{cachename}', $cachename, $email_content);
    $email_content = mb_ereg_replace('{cacheid}', $cacheid, $email_content);
    $email_content = mb_ereg_replace('{octeamEmailsSignature}', OcConfig::getOcteamEmailsSignature(), $email_content);
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
        mb_send_mail($user->getEmail(), tr('viewPending_01') . ": " . $cachename, tr('viewPending_02') . ":\n" . $email_content, $email_headers);
        // generate automatic log about status cache
        $log_text = htmlspecialchars(tr("viewPending_03"));
        $log_uuid = Uuid::create();
        XDb::xSql(
            "INSERT INTO `cache_logs`
                (`cache_id`, `user_id`, `type`, `date`, `text`, `text_html`, `date_created`, `last_modified`, `uuid`, `node`)
            VALUES (?, ?, '12', NOW(), ?, '2', NOW(), NOW(), ?, ?)",
            $cacheid, $user->getUserId(), $log_text, $log_uuid, OcConfig::getSiteNodeId());

    } else {
        //send email to owner
        mb_send_mail($owner_email['email'], tr('viewPending_04') . ": " . $cachename, $email_content, $email_headers);
        //send email to approver
        mb_send_mail($user->getEmail(), tr('viewPending_04') . ": " . $cachename, tr('viewPending_05') . ":\n" . $email_content, $email_headers);

        // generate automatic log about status cache
        $log_text = htmlspecialchars(tr("viewPending_06"));
        $log_uuid = Uuid::create();
        XDb::xSql(
            "INSERT INTO `cache_logs`
                (`id`, `cache_id`, `user_id`, `type`, `date`,
                 `text`, `text_html`, `date_created`, `last_modified`, `uuid`,
                 `node`)
            VALUES ('', ?, ?, ?, NOW(),
                    ?, ?, NOW(), NOW(), ?,
                    ?)",
            $cacheid, $user->getUserId(), 12,
            $log_text, 2, $log_uuid,
            OcConfig::getSiteNodeId());
    }
}

require_once(__DIR__ . '/lib/common.inc.php');

$view = tpl_getView();
$user = ApplicationContainer::Instance()->getLoggedUser();

if (empty($user) || !$user->hasOcTeamRole()) {
    $view->setTemplate('viewpendings_error');
    $view->buildView();
}

$view->setTemplate('viewpendings');

$content = '';
if (isset($_GET['cacheid'])) {
    if (isset($_GET['assign'])) {
        if (assignUserToCase($_GET['assign'], $_GET['cacheid'])) {
            $confirm = "<p>" . tr("viewPending_07") . " " . getUsername($_GET['assign']) . " " . tr("viewPending_08") . ".</p>";
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
                    assignUserToCase($user->getUserId(), $_GET['cacheid']);
                    notifyOwner($_GET['cacheid'], 0);
                    AdminNote::addAdminNote($user->getUserId(), $_GET['user_id'], true, AdminNote::CACHE_PASS, $_GET['cacheid']);
                    $confirm = "<p> " . tr("viewPending_09") . ".</p>";
                } else {
                    $confirm = "<p> " . tr("viewPending_10") . ".</p>";
                }
            } else if (isset($_GET['confirm']) && isset($_GET['user_id']) && $_GET['confirm'] == 2) {
                // declined - change status to archived and notify the owner now
                if (declineCache($_GET['cacheid'])) {
                    assignUserToCase($user->getUserId(), $_GET['cacheid']);
                    notifyOwner($_GET['cacheid'], 1);
                    AdminNote::addAdminNote($user->getUserId(), $_GET['user_id'], true, AdminNote::CACHE_BLOCKED, $_GET['cacheid']);
                    $confirm = "<p> " . tr("viewPending_11") . ".</p>";
                } else {
                    $confirm = "<p> " . tr("viewPending_12") . ".</p>";
                }
            } else if ($_GET['action'] == 1 && isset($_GET['user_id'])) {
                // require confirmation
                $confirm = "<p> " . tr("viewPending_13") . " \"<a href='viewcache.php?cacheid=" . $_GET['cacheid'] . "'>" . getCachename($_GET['cacheid']) . "</a>\" " . tr("viewPending_14") . " " . getCacheOwnername($_GET['cacheid']) . ". " . tr("viewPending_15") . ".</p>";
                $confirm .= "<p><a class='btn btn-success' href='viewpendings.php?user_id=" . $_GET['user_id'] . "&amp;cacheid=" . $_GET['cacheid'] . "&amp;confirm=1'>" . tr("viewPending_16") . "</a>
                        <a class='btn btn-default' href='viewpendings.php'>" . tr("viewPending_17") . "</a></p>";
            } else if ($_GET['action'] == 2 && isset($_GET['user_id'])) {
                // require confirmation
                $confirm = "<p> " . tr("viewPending_18") . " \"<a href='viewcache.php?cacheid=" . $_GET['cacheid'] . "'>" . getCachename($_GET['cacheid']) . "</a>\" " . tr("viewPending_14") . " " . getCacheOwnername($_GET['cacheid']) . ". " . tr("viewPending_19") . ".</p>";
                $confirm .= "<p><a class='btn btn-danger' href='viewpendings.php?user_id=" . $_GET['user_id'] . "&amp;cacheid=" . $_GET['cacheid'] . "&amp;confirm=2'>" . tr("viewPending_20") . "</a>
                        <a class='btn btn-default' href='viewpendings.php'>" . tr("viewPending_17") . "</a></p>";
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
    "SELECT cache_status.id AS cs_id, cache_status.pl AS cache_status,
                cache_owner.username AS username, cache_owner.user_id AS user_id,
                caches.cache_id AS cache_id, caches.name AS cachename,
                IFNULL(`cache_location`.`adm3`, '') AS `adm3`, caches.date_created AS date_created,
                last_log.id AS last_log_id, last_log.date AS last_log_date,
                last_log.user_id AS last_log_author, log_author.username AS last_log_username,
                last_log.text AS last_log_text
        FROM cache_status, `caches`
        LEFT JOIN `cache_location` ON `caches`.`cache_id` = `cache_location`.`cache_id`
        LEFT JOIN (
            SELECT id, cache_id, text, user_id, date
            FROM cache_logs logs
            WHERE date = (SELECT MAX(date) FROM cache_logs WHERE cache_id = logs.cache_id)
            ) AS last_log ON caches.cache_id = last_log.cache_id
           LEFT JOIN user AS cache_owner
               ON caches.user_id = cache_owner.user_id
           LEFT JOIN user AS log_author
               ON last_log.user_id = log_author.user_id
        WHERE cache_status.id = caches.status
        AND caches.status = 4
        GROUP BY caches.cache_id
        ORDER BY caches.date_created DESC");

$row_num = 0;
while ($report = XDb::xFetchArray($stmt)) {
    $assignedUserId = getAssignedUserId($report['cache_id']);

    if (!$assignedUserId && new DateTime($report['date_created']) < new DateTime('5 days ago')) {
        //set alert for forgotten cache
        $trstyle = "alert";
    } else if ($user->getUserId() == $assignedUserId) {
        //highlight caches assigned to current user
        $trstyle = "highlighted";
    } else {
        $trstyle = "";
    }

    if ($row_num % 2)
        $bgcolor = "bgcolor1";
    else
        $bgcolor = "bgcolor2";

    $content .= "<tr class='" . $trstyle . "'>\n";
    $content .= "<td class='" . $bgcolor . "'>
                        <a href='viewcache.php?cacheid=" . $report['cache_id'] . "'>" . nonEmptyCacheName($report['cachename']) . "</a><br/>
                           <a class=\"links\" href='viewprofile.php?userid=" . $report['user_id'] . "'>" . $report['username'] . "</a><br/>
                        <span style=\"font-weight:bold;font-size:10px;color:blue;\">" . $report['adm3'] . "</span>
                    </td>\n";

    $content .= "<td class='alertable " . $bgcolor . "'> " . $report['date_created'] . "</td>\n";

    $content .= "<td class='" . $bgcolor . "'>" . $report['last_log_date'] . "<br/>
                <a class=\"links truncated\" href='viewprofile.php?userid=" . $report['last_log_author'] . "'>" . $report['last_log_username'] . "</a><br/>
                <a class=\"truncated\" href='viewlogs.php?logid=" . $report['last_log_id'] . "' title='" . strip_tags($report['last_log_text']) . "'>" . strip_tags($report['last_log_text']) . "</a>
                </td>\n";

    $content .= "<td class='" . $bgcolor . "'><img src=\"/images/blue/arrow.png\" alt=\"\" />&nbsp;<a class=\"links\" href='viewpendings.php?user_id=" . $report['user_id'] . "&amp;cacheid=" . $report['cache_id'] . "&amp;action=1'>" . tr('accept') . "</a><br/>
            <img src=\"/images/blue/arrow.png\" alt=\"\" />&nbsp;<a class=\"links\" href='viewpendings.php?user_id=" . $report['user_id'] . "&amp;cacheid=" . $report['cache_id'] . "&amp;action=2'>" . tr('block') . "</a><br/>
            <img src=\"/images/blue/arrow.png\" alt=\"\" />&nbsp;<a class=\"links\" href='viewpendings.php?cacheid=" . $report['cache_id'] . "&amp;assign=" . $user->getUserId() . "'>" . tr('assign_yourself') . "</a></td>\n";
    $content .= "<td class='" . $bgcolor . "'><a class=\"links\" href='viewprofile.php?userid=" . $assignedUserId . "'>" . getUsername($assignedUserId) . "</a><br/></td>";
    $content .= "</tr>\n";
    $row_num++;
}
tpl_set_var('content', $content);
$view->buildView();
