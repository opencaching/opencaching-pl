<?php

use Utils\Database\XDb;
global $site_name;
$saved = "";
$email_sent = "";
$email_form = "";

function writeReason($type)
{
    switch ($type) {
        case '1':
            return tr('cache_reports_12');
        case '2':
            return tr('cache_reports_13');
        case '3':
            return tr('cache_reports_14');
        case '4':
            return tr('cache_reports_15');
    }
}

function writeStatus($status)
{
    switch ($status) {
        case '0':
            return "<span class='txt-red10'>" . tr('cache_reports_16') . "</span>";
        case '1':
            return "<span class='txt-red05'>" . tr('cache_reports_17') . "</span>";
        case '2':
            return "<span class='txt-green10'>" . tr('cache_reports_18') . "</span>";
        case '3':
            return "<span class='txt-blue10'>" . tr('cache_reports_19') . "</span>";
    }
}

function colorCacheStatus($text, $id)
{
    switch ($id) {
        case '1':
            return "<span class='txt-green10'>$text</span>";
        case '2':
            return "<span class='txt-red05'>$text</span>";
        case '3':
            return "<span class='txt-red10'>$text</span>";
        default:
            return "<span class='txt-grey05'>$text</span>";
    }
}

function nonEmptyCacheName($cacheName)
{
    if (str_replace(" ", "", $cacheName) == "")
        return "[bez nazwy]";
    return $cacheName;
}

function writeRe($status)
{
    switch ($status) {
        case '0':
            return tr("cache_reports_26");
        case '1':
            return tr("cache_reports_27");
        case '2':
            return tr("cache_reports_28");
    }
}

function getUsername($userid)
{
    return XDb::xMultiVariableQueryValue(
        "SELECT username FROM user WHERE user_id= :1 ", null,intval($userid) );
}

function getCachename($reportid)
{
    return XDb::xMultiVariableQueryValue(
        "SELECT caches.name FROM caches, reports
         WHERE reports.id = :1 AND reports.cache_id = caches.cache_id",
         null, intval($reportid) );
}

function makeSchemaReplace($text)
{
    global $usr;
    $text = str_replace("%rr_member_name%", $usr['username'], $text);
    $text = str_replace("%cachename%", getCachename($_REQUEST['reportid']), $text);
    return $text;
}

function getSchemas($receiver)
{
    // $receiver - who can receive the message:
    // 0 - reporter
    // 1 - cacher owner
    // 2 - both
    switch ($receiver) {
        case 0:
        case 1:
            $q_receiver = "WHERE receiver = " . intval($receiver) . " OR receiver = 2";
            break;
        case 2:
            $q_receiver = "WHERE receiver = 2";
            break;
        default:
            $q_receiver = "";
            break;
    }

    $text_result = "<p class='content-title-noshade-size1'>" . tr('cache_reports_31') . "</p><br/>
        <table border='0'>
        ";

    $stmt = XDb::xSql("SELECT name, shortdesc, text FROM email_schemas " . $q_receiver . " ORDER BY id ASC");
    while ($schema = XDb::xFetchArray($stmt)) {
        $text_result .= "
            <tr>
                <td><input class='radio-white' type='radio' name='schema' onclick='addtext(this);' value='" . makeSchemaReplace($schema['text']) . "' id='r_" . $schema['name'] . "'/></td>
                <td>
                    <span class='content-title-noshade'>" . $schema['shortdesc'] . "</span><br/>
                    <label for='r_" . $schema['name'] . "'>" . makeSchemaReplace(nl2br($schema['text'])) . "</label><br/><br/></td>
            </tr>
            ";
    }
    $text_result .= "</table>";
    return $text_result;
}

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');
$tplname = 'viewreport';

// tylko dla członków Rady
if ($error == false && $usr['admin']) {
    // sprawdzenie czy nastąpiło żądanie zmiany statusu lub usunięcia zgłoszenia, lub edycja notatki

    if (isset($_GET['mailto'])) {
        // show mail form
        $email_form = "
    <input type='hidden' name='reportid' value='" . intval($_REQUEST['reportid']) . "'>
    <input type='hidden' name='mailto' value='" . intval($_REQUEST['mailto']) . "'>
    <textarea name='email_content' cols='120' rows='8'>" . $_POST['init_text'] . "</textarea>
    <br />
    <input type='submit' value=" . tr('cache_reports_29') . ">
    <a href='viewreport.php?reportid=" . $_REQUEST['reportid'] . "'>" . tr('cache_reports_30') . "</a>
    <br/>
";
        $email_form .= getSchemas($_REQUEST['mailto']);
    }
    if (isset($_REQUEST['reportid']) && isset($_REQUEST['email_content']) && isset($_REQUEST['mailto']) && $_REQUEST['email_content'] != "") {

        $stmt = XDb::xSql(
            "SELECT reports.user_id as user_id, reports.cache_id as cache_id FROM reports
            WHERE reports.id = ? ", $_REQUEST['reportid']);

        $report = XDb::xFetchArray($stmt);

        $email_content = stripslashes($_REQUEST['email_content']);
        $note_content = " Wysłanie e-maila do " . writeRe($_REQUEST['mailto']) . ":<br/><i>" . $email_content . "</i>";

        $stmt = XDb::xSql("SELECT user_id, name FROM caches WHERE cache_id= ? ", $report['cache_id']);
        $cache_info = XDb::xFetchArray($stmt);

        $cache_user_id = $cache_info['user_id'];
        $report_user_id = $report['user_id'];
        $email_headers = "Content-Type: text/plain; charset=utf-8\r\n";
        $email_headers .= "From: $site_name <$octeam_email>\r\n";
        $email_headers .= "Reply-To: $octeam_email\r\n";
//          $email_headers .= "Reply-To: ".$usr['email']."\r\n";
//          $email_headers .= "CC: $octeam_email\r\n$octeam_email";

        switch ($_REQUEST['mailto']) {
            case "0":
                //get email address of reporter
                $query = XDb::xSql("SELECT `email` FROM `user` WHERE `user_id`=?", $report_user_id);
                $report_email = XDb::xFetchArray($query);
                //send email to reporter
                mb_send_mail($report_email['email'], tr('cache_reports_32') . ": " . $cache_info['name'], $email_content, $email_headers);
                mb_send_mail($usr['email'], tr('cache_reports_32') . ": " . $cache_info['name'], tr('cache_reports_33') . ":\n" . $email_content, $email_headers);
                break;

            case "1":
                //get email address of cache owner
                $query = XDb::xSql("SELECT `email` FROM `user` WHERE `user_id`=?", $cache_user_id);
                $report_email = XDb::xFetchArray($query);
                //send email to cache owner
                mb_send_mail($report_email['email'], tr('cache_reports_32') . ": " . $cache_info['name'], $email_content, $email_headers);
                mb_send_mail($usr['email'], tr('cache_reports_32') . ": " . $cache_info['name'], tr('cache_reports_33') . ":\n" . $email_content, $email_headers);
                break;

            case "2":
                //get email address of reporter
                $query = XDb::xSql("SELECT `email` FROM `user` WHERE `user_id`= ? ", $report_user_id);
                $report_email = XDb::xFetchArray($query);
                //send email to reporter
                mb_send_mail($report_email['email'], tr('cache_reports_32') . ": " . $cache_info['name'], $email_content, $email_headers);

                //get email address of cache owner
                $query = XDb::xSql("SELECT `email` FROM `user` WHERE `user_id`= ? ", $cache_user_id);
                $report_email = XDb::xFetchArray($query);

                //send email to cache owner
                mb_send_mail($report_email['email'], tr('cache_reports_32') . ": " . $cache_info['name'], $email_content, $email_headers);
                mb_send_mail($usr['email'], tr('cache_reports_32') . ": " . $cache_info['name'], tr('cache_reports_33') . ":\n" . $email_content, $email_headers);

                break;
        }

        $email_sent = "<b><span class='txt-green10'>E-mail został wysłany do " . writeRe($_REQUEST['mailto']) . ".</span></b>";

        $note = nl2br(XDb::xEscape($note_content));
        $now = XDb::xEscape(date("Y-m-d H:i:s"));
        XDb::xSql(
            "UPDATE reports SET
                note=CONCAT('[" . $now . "] <b> ? /b>: " . $note . "<br />', note),
                changed_by= ? , changed_date='" . $now . "'
            WHERE id= ? ",
            $usr['username'], $usr['userid'], intval($_REQUEST['reportid']));

    }

    tpl_set_var('confirm_resp_change', "");
    tpl_set_var('confirm_status_change', "");
    if (isset($_POST['new_resp']) && isset($_REQUEST['reportid'])) {

        XDb::xSql(
            "UPDATE reports SET responsible_id = ? WHERE id= ?",
            intval($_POST['respSel']), intval($_REQUEST['reportid']));

        if ($_POST['respSel'] != 0)
            tpl_set_var('confirm_resp_change', "<b><span class='txt-green10'>" . tr('cache_reports_36') ." ". getUsername($_POST['respSel']) . ".</span></b>");
        else
            tpl_set_var('confirm_resp_change', "<b><span class='txt-green10'>" . tr('cache_reports_37') .".</span></b>");
    }

    if (isset($_POST['new_status']) && isset($_REQUEST['reportid'])) {

        XDb::xSql(
            "UPDATE reports SET status= ? , changed_by= ? , changed_date=? WHERE id= ? ",
            intval($_POST['statusSel']), intval($usr['userid']), date("Y-m-d H:i:s"), intval($_REQUEST['reportid']) );

        tpl_set_var('confirm_status_change', "<b><span class='txt-green10'>" . tr('cache_reports_38') ." " . writeStatus($_POST['statusSel']) . ".</span></b>");
        if ($_POST['statusSel'] == 3) {
            // jezeli zmieniono status na "zajrzyj tu!", nastepuje rozeslanie maili do rr

            $stmt = XDb::xSql(
                "SELECT reports.cache_id as cache_id, reports.`type` as `type`, caches.cache_id, caches.name as name
                FROM reports, caches
                WHERE reports.id = ? AND reports.cache_id = caches.cache_id",
                intval($_REQUEST['reportid']));

            $report = XDb::xFetchArray($stmt);

            $email_content = $usr['username'] . " prosi, żebyś zajrzał do zgłoszenia problemu http://www.opencaching.pl/viewreport.php?reportid=" . intval($_REQUEST['reportid']) . " - " . $report['name'] . " (" . writeReason($report['type']) . ").";

            $email_headers = "Content-Type: text/plain; charset=utf-8\r\n";
            $email_headers .= "From: $site_name <$octeam_email>\r\n";
            $email_headers .= "Reply-To: $octeam_email\r\n";

            //send email to rr
            mb_send_mail($octeam_email, tr('cache_reports_32') . ": " . $report['name'], $email_content, $email_headers);
        }
    }
    if (isset($_POST['note']) && isset($_REQUEST['reportid'])) {

        $res = XDb::xSql("SELECT responsible_id FROM reports WHERE id = ? ", intval($_REQUEST['reportid']));

        if( $responsible_id = XDb::xFetchArray($res)){
            if ($responsible_id == "") {
                XDb::xSql("UPDATE reports SET status = 1, responsible_id = ? WHERE id = ? ",
                    $usr['userid'], intval($_REQUEST['reportid']));
            }
        }
        $note = XDb::xEscape(strip_tags($_POST['note']));
        if ($note != "") {

            XDb::xSql(
                "UPDATE reports SET
                note=CONCAT( '[', ? ,'] <b> ', ? ,' </b>: $note <br />', note),
                changed_by= ? , changed_date= ?
                WHERE id= ? ",
                date("Y-m-d H:i:s"), $usr['username'], intval($usr['userid']), date("Y-m-d H:i:s"), intval($_REQUEST['reportid']) );

            $saved = "<b><span class='txt-green10'>Notatka została zapisana.</span></b>";
        }
    }

    $query = XDb::xSql(
        "SELECT cache_status.id as cs_id,caches.user_id AS cache_ownerid, cache_status.$lang as cache_status,
                reports.id as report_id, reports.user_id as user_id, reports.note as note,
                reports.changed_by as changed_by, reports.changed_date as changed_date, reports.cache_id as cache_id,
                reports.type as type, reports.text as text, reports.submit_date as submit_date,
                reports.responsible_id as responsible_id, reports.status as status, user.username as username,
                user.user_id as user_id, caches.name as cachename, caches.status AS c_status,
                IFNULL(`cache_location`.`adm3`, '') AS `adm3`
        FROM cache_status, reports, user, (`caches` LEFT JOIN `cache_location` ON `caches`.`cache_id` = `cache_location`.`cache_id`)
        WHERE cache_status.id = caches.status AND reports.id = ?
            AND user.user_id = reports.user_id AND caches.cache_id = reports.cache_id ORDER BY submit_date ASC",
        intval($_REQUEST['reportid']));

    if($report = XDb::xFetchArray($query)){

        $username = XDb::xMultiVariableQueryValue(
            "SELECT username FROM user WHERE user_id = :1 ", null, $report['user_id'] );

        $userlogin_query = XDb::xMultiVariableQueryValue(
            "SELECT last_login FROM user WHERE user_id= :1 ", null, $report['cache_ownerid'] );

        if ( $userlogin_query == "0000-00-00 00:00:00") {
            $userlogin = "brak danych lub więcej niż 12 miesięcy temu";
        } else {
            $userlogin = strftime("%Y-%m-%d", strtotime($userlogin_query) );
        }

        $content = "<tr>";

        $content .= "<td><span class='content-title-noshade-size05'>" . $report['report_id'] . "</span></td>";
        $content .= "<td><span class='content-title-noshade-size05'>" . $report['submit_date'] . "</span></td>";
        $content .= "<td><a class='content-title-noshade-size04' href='viewcache.php?cacheid=" . $report['cache_id'] . "'>" . nonEmptyCacheName($report['cachename']) . "</a><br/> <a title=\"Użytkownik logowal się ostatnio: " . $userlogin . "\" class=\"links\" href=\"viewprofile.php?userid=" . $report['cache_ownerid'] . "\">" . getUsername($report['cache_ownerid']) . "</a><br/><span style=\"font-weight:bold;font-size:10px;color:blue;\">" . $report['adm3'] . "</span></td>";
        $content .= "<td><span class='content-title-noshade-size05'>" . colorCacheStatus($report['cache_status'], $report['c_status']) . "</span></td>";
        $content .= "<td><span class='content-title-noshade-size05'>" . writeReason($report['type']) . "</span></td>";
        $content .= "<td><a class='content-title-noshade-size05' href='viewprofile.php?userid=" . $report['user_id'] . "'>" . $report['username'] . "</a></td>";
        //$content .= "<td><a href='viewprofile.php?userid=".$report['responsible_id']."'>".getUsername($report['responsible_id'])."</a></td>";

        $content .= "<td>";
        $content .= "<select name='respSel'>";
        $content .= "<option value='0'>brak</option>";
        $selected = "";

        $admins_query = XDb::xSql("SELECT user_id, username FROM user WHERE admin=1");
        while ($admins = XDb::xFetchArray($admins_query)) {
            if ($report['responsible_id'] == $admins['user_id']) {
                $selected = "selected='selected'";
            } else
                $selected = "";
            $content .= "<option value='" . $admins['user_id'] . "' $selected>" . $admins['username'] . "</option>";
        }
        $content .= "</select><br /><input type='submit' name='new_resp' value=" . tr('cache_reports_20') . ">";
        $content .= "</td>";

        $content .= "<td>";
        $content .= "<select name='statusSel'>";
        for ($i = 0; $i < 4; $i++) {
            if ($report['status'] == $i) {
                $selected = "selected='selected'";
            } else {
                $selected = "";
            }
            $content .= "<option value='" . $i . "' $selected>" . writeStatus($i) . "</option>";
        }

        $content .= "</select><br /><input type='hidden' name='reportid' value='" . $report['report_id'] . "'><input type='submit' name='new_status' value=" . tr('cache_reports_20') . ">";

        $content .= "</td>";
        $content .= "<td><span class='content-title-noshade-size05'>" . ($report['changed_by'] == '0' ? '' : (getUsername($report['changed_by']) . '<br/>(' . ($report['changed_date']) . ')')) . "</span></td>\n";
        $content .= "</tr>\n";

        tpl_set_var('content', $content);
        tpl_set_var('report_text_lbl', tr('cache_reports_21'));
        tpl_set_var('report_text', strip_tags($report['text']));
        tpl_set_var('perform_action_lbl', tr('cache_reports_22'));

        if (!isset($_GET['mailto'])) {
            $active_form = "<input type='hidden' name='reportid' value='" . intval($_REQUEST['reportid']) . "'/><textarea name='note' cols='80' rows='5'></textarea><br /><input type='submit' value=" . tr('cache_reports_23') . ">&nbsp;" . $saved;
            tpl_set_var('note_lbl', tr("cache_reports_24"));
        } else {
            // display email form
            tpl_set_var('note_lbl', tr("cache_reports_25") . writeRe($_REQUEST['mailto']));
            $active_form = $email_form;
        }
        tpl_set_var('note_area', nl2br($report['note']));
        tpl_set_var('active_form', $active_form);

        $actions = '';
        $mail_actions = '';
        for ($i = 0; $i < 3; $i++)
            $mail_actions .= "<li><a href='viewreport.php?reportid=" . $report['report_id'] . "&amp;mailto=$i'>" . tr('cache_reports_25') . "  " . writeRe($i) . "</a></li>";

        //$actions .= "<br /><li><a href='viewreport.php?reportid=".$report['report_id']."&amp;delete=1'>usuń zgłoszenie</a></li>";


        tpl_set_var('reportid', $report['report_id']);
        tpl_set_var('actions', $actions);
        tpl_set_var('mail_actions', $mail_actions);
        tpl_set_var('email_form', $email_form);
        tpl_set_var('email_sent', $email_sent);
    } else
        $tplname = 'viewreport_notfound';
} else
    $tplname = 'viewreports_error';
tpl_BuildTemplate();
?>
