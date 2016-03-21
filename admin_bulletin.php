<?php

use Utils\Database\XDb;

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

if ($usr['admin']) {
    tpl_set_var('bulletin', "");
    if (isset($_POST['bulletin']) && $_POST['bulletin'] != "" && $_SESSION['submitted'] != true) {
        // podgląd
        $bulletin = addslashes($_POST['bulletin']);

        $_SESSION['bulletin'] = $bulletin;
        tpl_set_var('bulletin', stripslashes(nl2br($bulletin)));
        $tplname = 'admin_bulletin_preview';
        tpl_BuildTemplate();
    } else
    if (isset($_POST['bulletin_final']) && $_POST['bulletin_final'] != "" && $_SESSION['submitted'] != true) {
        // wysłanie
        $email_headers = "Content-Type: text/plain; charset=utf-8\r\n";
        $email_headers .= "From: " . $site_name . " <" . $mail_rr . ">\r\n";
        $email_headers .= "Reply-To: " . $mail_rr . "\r\n";

        $bulletin = ($_SESSION['bulletin']);
        $q = "INSERT INTO bulletins (content, user_id)
                VALUES ('" . XDb::xEscape($bulletin) . "', " . XDb::xEscape(intval($usr['userid'])) . ")";

        XDb::xQuery($q);

        $tr_newsletter_removal = tr('newsletter_removal');
        $bulletin .= "\r\n\r\n" . $tr_newsletter_removal . " " . $absolute_server_URI . "myprofile.php?action=change.";
        //get emails
        $q = "SELECT `email` FROM `user` WHERE `is_active_flag`=1 AND get_bulletin=1 AND rules_confirmed=1";
        $rs = XDb::xQuery($q);
        $tr_newsletter = $short_sitename . " " . tr('newsletter');
        while ($email = XDb::xFetchArray($rs)) {

            mb_send_mail($email['email'], $tr_newsletter . " " . date("Y-m-d"), stripslashes($bulletin), $email_headers);
        }
        $_SESSION['submitted'] = true;
        tpl_set_var('bulletin', stripslashes($_SESSION['bulletin']));
        unset($_SESSION['bulletin']);
        $tplname = 'admin_bulletin_sent';
        tpl_BuildTemplate();
    } else {
        // formularz
        $_SESSION['submitted'] = false;
        $tplname = 'admin_bulletin';
        tpl_BuildTemplate();
    }
}
