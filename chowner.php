<?php

use Utils\Database\XDb;
use Utils\Database\OcDb;
if (!isset($rootpath))
    $rootpath = '';
require_once('./lib/common.inc.php');

$db = OcDb::instance();

function mb_send_mail_2($to, $subject, $content, $headers)
{
    global $debug_page;
    if ($debug_page) {
        echo "<pre>mail\nto: $to\nsubject: $subject\n$content</pre>";
    } else {
        mb_send_mail($to, $subject, $content, $headers);
    }
}

function orderBy($orderId)
{
    switch ($orderId) {
        case "0":
            return "name";
        case "1":
            return "date_hidden";
        /* case "2":
          return "type";
          case "3":
          return "status"; */
        default:
            return "name";
    }
}

function orderType($orderType)
{
    switch ($orderType) {
        case "0":
            return "DESC";
        default:
            return "ASC";
    }
}

function listUserCaches($userid)
{
    global $db;
    // lists all approved caches belonging to user
    $q = "SELECT cache_id, name, date_hidden FROM caches WHERE user_id=:1 AND status <> 4 AND type != 10 ORDER BY " . orderBy(@$_GET['orderId']) . " " . orderType(@$_GET['orderType']);
    $db->multiVariableQuery($q, $userid);
    return $db->dbResultFetchAll();
}

function listPendingCaches($userid)
{
    global $db;
    $q = "SELECT cache_id, name, date_hidden FROM caches WHERE cache_id IN (SELECT cache_id FROM chowner WHERE user_id = :1)";
    $db->multiVariableQuery($q, $userid);
    return $db->dbResultFetchAll();
}

function getUsername($userid)
{
    global $db;
    $q = "SELECT username FROM user WHERE user_id=:1";
    return $db->multiVariableQueryValue($q, -1, $userid);
}

function getUserEmail($userid)
{
    global $db;
    $q = "SELECT email FROM user WHERE user_id=:1";
    return $db->multiVariableQueryValue($q, -1, $userid);
}

function getCacheName($cacheid)
{
    global $db;
    $q = "SELECT name FROM caches WHERE cache_id=:1";
    return $db->multiVariableQueryValue($q, -1, $cacheid);
}

function isCachePublished($cacheid)
{
    global $db;
    $q = 'SELECT count(cache_id) FROM caches WHERE cache_id=:1 AND status in (1,2,3)';
    return $db->multiVariableQueryValue($q, 0, $cacheid) > 0;
}

function getCacheOwner($cacheid)
{
    global $db;
    $q = "SELECT user_id FROM caches WHERE cache_id=:1";
    return $db->multiVariableQueryValue($q, -1, $cacheid);
}

function isUserOwner($userid, $cacheid)
{
    global $db;
    $q = "SELECT count(cache_id) FROM caches WHERE cache_id=:1 AND user_id=:2";
    return $db->multiVariableQueryValue($q, 0, $cacheid, $userid);
}

function doesUserExist($username)
{
    global $db;
    $q = "SELECT user_id FROM user WHERE username=:1";
    return $db->multiVariableQueryValue($q, 0, $username);
}

function isRequestPending($cacheid)
{
    global $db;
    // czy skrzynka cacheid juz oczekuje na zmiane wlasciciela?
    $q = "SELECT count(id) FROM chowner WHERE cache_id=:1";
    return $db->multiVariableQueryValue($q, -1, $cacheid);
}

function isAcceptanceNeeded($userid)
{
    global $db;
    $q = "SELECT count(id) FROM chowner WHERE user_id=:1";
    return $db->multiVariableQueryValue($q, -1, $userid);
}

function emailHeaders()
{
    global $usr, $site_name, $octeam_email;
    $email_headers = "Content-Type: text/plain; charset=utf-8\r\n";
    $email_headers .= "From: $site_name <$octeam_email>\r\n";
    $email_headers .= "Reply-To: " . $usr['email'] . "\r\n";
    return $email_headers;
}

//prepare the templates and include all neccessary

$tplname = 'chowner';

// tylko dla zalogowanych
if ($error == false && isset($usr['userid'])) {
    tpl_set_var('error_msg', "");
    tpl_set_var('info_msg', "");
    tpl_set_var('start_przejmij', "<!--");
    tpl_set_var('end_przejmij', "-->");
    tpl_set_var('acceptList', "");
    tpl_set_var('cacheList', "");

    // wybor wlasciciela - mozna zmieniac tylko swoje skrzynki... chyba, ze jest sie czlonkiem oc team
    if (isset($_GET['cacheid']) && (isUserOwner($usr['userid'], $_GET['cacheid']) && !isset($_GET['abort']) && !isset($_GET['accept']))) {
        tpl_set_var('cachename', getCacheName($_GET['cacheid']));
        tpl_set_var('cacheid', $_GET['cacheid']);
        $tplname = "chowner_chooseuser";
    } else {
        if (isset($_GET['accept']) && $_GET['accept'] == 1) {
            $q = "SELECT count(id) FROM chowner WHERE cache_id = :1 AND user_id = :2";
            $potwierdzenie = $db->multiVariableQueryValue($q, 0, $_GET['cacheid'], $usr['userid']);
            if ($potwierdzenie > 0) {
                // zmiana wlasciciela
                tpl_set_var("error_msg", tr('adopt_30'));
                tpl_set_var("info_msg", "");

                $db->beginTransaction();

                require_once($rootpath . 'lib/cache_owners.inc.php');
                $pco = new OrgCacheOwners($db);
                $pco->populateForCache($_GET['cacheid']);

                $oldOwnerId = getCacheOwner($_GET['cacheid']);
                $isCachePublished = isCachePublished($_GET['cacheid']);

                $q = "DELETE FROM chowner WHERE cache_id = :1 AND user_id = :2";
                $db->multiVariableQuery($q, $_GET['cacheid'], $usr['userid']);

                if ($isCachePublished) {
                    $q = "UPDATE caches SET user_id = :2, org_user_id = IF(org_user_id IS NULL, :3, org_user_id) WHERE cache_id= :1";
                    $db->multiVariableQuery($q, $_GET['cacheid'], $usr['userid'], $oldOwnerId);
                } else {
                    $q = "UPDATE caches SET user_id = :2 WHERE cache_id= :1";
                    $db->multiVariableQuery($q, $_GET['cacheid'], $usr['userid']);
                }
                $q = "UPDATE pictures SET user_id = :2 WHERE object_id = :1";
                $db->multiVariableQuery($q, $_GET['cacheid'], $usr['userid']);

                // this should be kept consistent by a trigger
                //$q = "UPDATE user SET hidden_count = hidden_count - 1 WHERE user_id = :1";
                //$db->multiVariableQuery($q, $oldOwnerId);
                //$q = "UPDATE user SET hidden_count = hidden_count + 1 WHERE user_id = :1";
                //$db->multiVariableQuery($q, $usr['userid']);
                // ... but it's not
                //$q = "UPDATE user SET hidden_count = (select count(cache_id) from caches where status in (1,2,3) and user_id = :user_id) WHERE user_id = :user_id";
                //foreach(array($oldOwnerId, $usr['userid']) as $key => $user_id){
                //    $params = array();
                //    $params['user_id']['value'] = $user_id;
                //    $params['user_id']['data_type'] = 'string';
                //    $db->paramQuery($q, $params);
                //}
                // put log into cache logs.
                if ($isCachePublished) {
                    $logMessage = tr('adopt_32');
                    $oldUserName = ' <a href="' . $absolute_server_URI . 'viewprofile.php?userid=' . $oldOwnerId . '">' . getUsername($oldOwnerId) . '</a> ';
                    $newUserName = ' <a href="' . $absolute_server_URI . 'viewprofile.php?userid=' . $usr['userid'] . '">' . getUsername($usr['userid']) . '</a>';

                    $logMessage = str_replace('{oldUserName}', $oldUserName, $logMessage);
                    $logMessage = str_replace('{newUserName}', $newUserName, $logMessage);

                    $q = 'INSERT INTO cache_logs(cache_id, user_id, type, date, text, text_html, text_htmledit, date_created, last_modified, uuid, node)
                                VALUES                (:1,       -1,      3,    NOW(), :2,  1,         1,             NOW(),        NOW(),         :3,   :4)';
                    $db->multiVariableQuery($q, $_GET['cacheid'], $logMessage, create_uuid(), $oc_nodeid);
                }
                $db->commit();

                $message = tr('adopt_15');
                $message = str_replace('{cacheName}', getCacheName($_GET['cacheid']), $message);
                tpl_set_var('error_msg', $message . '<br /><br />');
                tpl_set_var("error_msg", "");

                $mailContent = tr('adopt_31');
                $mailContent = str_replace('\n', "\n", $mailContent);
                $mailContent = str_replace('{userName}', $usr['username'], $mailContent);
                $mailContent = str_replace('{cacheName}', getCacheName($_GET['cacheid']), $mailContent);
                mb_send_mail_2(getUserEmail($oldOwnerId), tr('adopt_18'), $mailContent, emailHeaders());
            }
        }
        if (isset($_GET['accept']) && $_GET['accept'] == 0) {
            // odrzucenie zmiany
            $q = "DELETE FROM chowner WHERE cache_id = :1 AND user_id = :2";
            $db->multiVariableQuery($q, $_GET['cacheid'], $usr['userid']);
            if ($db->rowCount() > 0) {
                tpl_set_var("info_msg", tr('adopt_27') . '<br /><br />');
                $mailContent = tr('adopt_29');
                $mailContent = str_replace('\n', "\n", $mailContent);
                $mailContent = str_replace('{userName}', $usr['username'], $mailContent);
                $mailContent = str_replace('{cacheName}', getCacheName($_REQUEST['cacheid']), $mailContent);
                mb_send_mail_2(getUserEmail($oldOwnerId), tr('adopt_28'), $mailContent, emailHeaders());
            } else
                tpl_set_var("error_msg", tr('adopt_30') . '<br /><br />');
        }

        if (isset($_GET['abort']) && isUserOwner($usr['userid'], $_GET['cacheid'])) {
            // anulowanie procedury przejecia
            $q = "DELETE FROM chowner WHERE cache_id = :1";
            $db->multiVariableQuery($q, $_GET['cacheid']);
            if ($db->rowCount() > 0)
                tpl_set_var('info_msg', " " . tr('adopt_16') . " <br /><br />");
            else
                tpl_set_var('error_msg', " " . tr('adopt_17') . " <br /><br />");
        }

        if (isAcceptanceNeeded($usr['userid'])) {
            // skrzynka czeka na moja akceptacje
            tpl_set_var('start_przejmij', "");
            tpl_set_var('end_przejmij', "");
            $acceptList = '';
            foreach (listPendingCaches($usr['userid']) as $cache) {
                $acceptList .= "<tr><td>";
                $acceptList .= "<a href='viewcache.php?cacheid=" . $cache['cache_id'] . "'>";
                $acceptList .= $cache['name'] . "</a>";
                $acceptList .= " <a href='chowner.php?cacheid=" . $cache['cache_id'] . "&accept=1'>[<font color='green'>" . tr('adopt_12') . "</font>]</a>";
                $acceptList .= " <a href='chowner.php?cacheid=" . $cache['cache_id'] . "&accept=0'>[<font color='#ff0000'>" . tr('adopt_13') . "</font>]</a>";


                $acceptList .= "</td>
                    <td>" . strftime($dateformat, strtotime($cache['date_hidden'])) . "</td>
                    </tr>
                    ";
            }
            tpl_set_var('acceptList', $acceptList);
        }

        if (isset($_POST['username'])) {
            if (doesUserExist($_POST['username']) > 0) {
                // przekazywanie samemu sobie
                //if( $usr['username'] == $_POST['username'] )
                //  tpl_set_var('error_msg', "Nie możesz przekazać skrzynki samemu sobie...<br /><br />");
                //else
                {
                    // uzytkownik istnieje, mozna kontynuowac procedure
                    $newUserId = doesUserExist($_POST['username']);
                    $q = "INSERT INTO chowner (cache_id, user_id) VALUES ( ?, ?)";
                    $stmt = XDb::xSql($q, $_REQUEST['cacheid'], $newUserId);
                    if (XDb::xNumRows($stmt) > 0) {
                        tpl_set_var('info_msg', ' ' . tr('adopt_24') . ' <br /><br />');
                        $mailContent = tr('adopt_26');
                        $mailContent = str_replace('\n', "\n", $mailContent);
                        $mailContent = str_replace('{userName}', $usr['username'], $mailContent);
                        $mailContent = str_replace('{cacheName}', getCacheName($_REQUEST['cacheid']), $mailContent);
                        mb_send_mail_2(getUserEmail($newUserId), tr('adopt_25'), $mailContent, emailHeaders());
                    } else
                        tpl_set_var('error_msg', tr('adopt_22') . '<br /><br />');
                }
            }
            else {
                $message = tr('adopt_23');
                $message = str_replace('{userName}', $_POST['username'], $message);
                tpl_set_var('error_msg', $message . '<br /><br />');
            }
        }
        // strona glowna - wybor skrzynki
        $cacheList = '';
        $bgColor = '#ffffff';
        foreach (listUserCaches($usr['userid']) as $cache) {
            if ($bgColor == '#ffffff')
                $bgColor = '#eeffee';
            else
                $bgColor = '#ffffff';
            $cacheList .= '<tr bgcolor="' . $bgColor . '">
                <td>
                ';
            if (!isRequestPending($cache['cache_id']))
                $cacheList .= "<a href='chowner.php?cacheid=" . $cache['cache_id'] . "'>";
            $cacheList .= $cache['name'];
            if (isRequestPending($cache['cache_id'])) {
                $cacheList .= "</a> <a href='chowner.php?cacheid=" . $cache['cache_id'] . "&abort=1'>[<font color='#ff0000'>" . tr('adopt_14') . "</font>]";
            }
            $cacheList .= "</a>";

            $cacheList .= "</td>
                <td>" . strftime($dateformat, strtotime($cache['date_hidden'])) . "</td>
                </tr>
                ";
        }
        tpl_set_var('cacheList', $cacheList);
    }
    tpl_BuildTemplate();
} else
    header("Location: index.php");
?>
