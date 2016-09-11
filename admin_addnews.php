<?php

use Utils\Database\XDb;
use Utils\Email\Email;

//prepare the templates and include all neccessary
global $octeamEmailsSignature;
require_once('./lib/common.inc.php');

if ($usr['admin']) {

    // don't send e-mail for approval
    $use_news_approving = true;

    //Preprocessing
    if ($error == false) {
        //get the news
        $tplname = 'admin_addnews';
        require($stylepath . '/news.inc.php');
        require($stylepath . '/admin_addnews.inc.php');

        $topicid = isset($_REQUEST['topic']) ? $_REQUEST['topic'] : 1;
        $newstext = isset($_REQUEST['newstext']) ? stripslashes($_REQUEST['newstext']) : '';
        $newshtml = isset($_REQUEST['newshtml']) ? $_REQUEST['newshtml'] : 0;
        $email = isset($_REQUEST['email']) ? stripslashes($_REQUEST['email']) : '';

        $emailok = false;
        tpl_set_var('email_error', '');

        if (isset($_REQUEST['submit'])) {
            $emailok = Email::isValidEmail($email);

            if ($emailok == true) {
                // filtern und ausgabe vorbereiten
                $tplname = 'admin_addnews_confirm';

                if ($newshtml == 0)
                    $newstext = htmlspecialchars($newstext, ENT_COMPAT, 'UTF-8');
                else {
                    require_once($rootpath . 'lib/class.inputfilter.php');
                    $myFilter = new InputFilter($allowedtags, $allowedattr, 0, 0, 1);
                    $newstext = $myFilter->process($newstext);
                }

                $rs = XDb::xSql("SELECT `name` FROM `news_topics` WHERE `id`= ? ", $topicid);
                $r = XDb::xFetchArray($rs);
                XDb::xFreeResults($rs);

                $newscontent = $tpl_newstopic;
                $newscontent = mb_ereg_replace('{date}', date('d.m.Y h:i:s', time()), $newscontent);
                $newscontent = mb_ereg_replace('{topic}', $r['name'], $newscontent);
                $newscontent = mb_ereg_replace('{message}', $newstext, $newscontent);
                tpl_set_var('newscontent', $newscontent);

                // in DB schreiben
                XDb::xSql("INSERT INTO `news` (`date_posted`, `content`, `topic`, `display`)
                           VALUES (NOW(), ?, ?, ?)",
                           $newstext, $topicid, ($use_news_approving == true) ? 0 : 1);

                // email versenden
                if ($use_news_approving == true) {
                    $mailcontent = file_get_contents($stylepath . '/email/newstopic.email');
                    $mailcontent = mb_ereg_replace('{email}', $email, $mailcontent);
                    $mailcontent = mb_ereg_replace('{date}', date('d.m.Y H:i:s', time()), $mailcontent);
                    $mailcontent = mb_ereg_replace('{newsconent}', $newstext, $mailcontent);
                    $mailcontent = mb_ereg_replace('{newNewsTopic_01}', tr('newNewsTopic_01'), $mailcontent);
                    $mailcontent = mb_ereg_replace('{newNewsTopic_02}', tr('newNewsTopic_02'), $mailcontent);
                    $mailcontent = mb_ereg_replace('{newNewsTopic_03}', tr('newNewsTopic_03'), $mailcontent);
                    $mailcontent = mb_ereg_replace('{newNewsTopic_04}', tr('newNewsTopic_04'), $mailcontent);
                    $mailcontent = mb_ereg_replace('{octeamEmailsSignature}', $octeamEmailsSignature, $mailcontent);

                    //TODO: use Email() class here...
                    mb_send_mail($news_approver_email, $email_subject, $mailcontent, $emailheaders);
                }

                // erfolg anzeigen
                tpl_BuildTemplate();
                exit;
            }

            tpl_set_var('email_error', $email_error_message);
        }

        tpl_set_var('newstext', htmlspecialchars($newstext, ENT_COMPAT, 'UTF-8'));
        tpl_set_var('newshtml', ($newshtml == 1) ? ' checked="checked"' : '');
        tpl_set_var('email', htmlspecialchars($email, ENT_COMPAT, 'UTF-8'));

        // topics erstellen
        $topics = '';
        $rs = XDb::xSql("SELECT `id`, `name` FROM `news_topics` ORDER BY `id` ASC");
        while ($r = XDb::xFetchArray($rs)) {
            if ($r['id'] == $topicid)
                $topics .= '<option value="' . $r['id'] . '" selected="selected">' . htmlspecialchars($r['name'], ENT_COMPAT, 'UTF-8') . '</option>' . "\n";
            else
                $topics .= '<option value="' . $r['id'] . '">' . htmlspecialchars($r['name'], ENT_COMPAT, 'UTF-8') . '</option>' . "\n";
        }
        XDb::xFreeResults($rs);
        tpl_set_var('topics', $topics);
    }

    //make the template and send it out
    tpl_BuildTemplate();
}
?>
