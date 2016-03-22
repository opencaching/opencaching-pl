<?php

use Utils\Database\XDb;

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
    } else {

        $rs = XDb::xSql("SELECT `statpic_text`, `statpic_logo` FROM `user` WHERE `user_id`= ? ", $usr['userid']);
        $record = XDb::xFetchArray($rs);


        tpl_set_var('statpic_text', htmlspecialchars($record['statpic_text'], ENT_COMPAT, 'UTF-8'));
        $using_logo = $record['statpic_logo'];

        //display the change form
        $tplname = 'change_statpic';

        // SUBMIT changed data
        if (isset($_POST['submit'])) {
            //load datas from form
            $statpic_text = isset($_POST['statpic_text']) ? mb_substr($_POST['statpic_text'], 0, 30) : 'Opencaching';
            $statpic_logo = isset($_POST['statpic_logo']) ? $_POST['statpic_logo'] + 0 : 0;

            tpl_set_var('statpic_text', $statpic_text);
            tpl_set_var('statpic_logo', $statpic_logo);

            //validate data
            $statpic_text_not_ok = mb_ereg_match(regex_statpic_text, $statpic_text) ? false : true;

            //try to save
            if (!($statpic_text_not_ok)) {
                //in DB updaten
                XDb::xSql(
                    "UPDATE `user` SET `statpic_text`= ?, `statpic_logo`= ?
                     WHERE `user_id`= ? ", $statpic_text, $statpic_logo, $usr['userid']);

                //call eventhandler
                require_once($rootpath . 'lib/eventhandler.inc.php');
                event_change_statpic($usr['userid'] + 0);

                //wieder normal anzeigen
                tpl_redirect('myprofile.php');
            } else {
                tpl_set_var('statpic_text_message', $error_statpic_text);
            }
        } else {
            //load from database
            $statpic_text = $record['statpic_text'];

            $stmp = '';
            $rs2 = XDb::xSql('SELECT `id`, `previewpath`, `description` FROM `statpics`');
            while ($record2 = XDb::xFetchArray($rs2)) {
                $logo_temp = '<tr><td class="content-title-noshade">{statpic_desc}</td><td><input type="radio" name="statpic_logo" class="radio" value={statpic_id}{statpic_selected}/><img src="{statpic_preview}" align=middle /></td></tr><tr><td class="spacer" colspan="2"></td></tr>';
                $logo_temp = mb_ereg_replace('{statpic_id}', $record2['id'], $logo_temp);
                if ($record2['id'] == $using_logo) {
                    $logo_temp = mb_ereg_replace('{statpic_selected}', ' checked="checked"', $logo_temp);
                } else {
                    $logo_temp = mb_ereg_replace('{statpic_selected}', '', $logo_temp);
                }
                $logo_temp = mb_ereg_replace('{statpic_preview}', $record2['previewpath'], $logo_temp);
                $logo_temp = mb_ereg_replace('{statpic_desc}', htmlspecialchars($record2['description'], ENT_COMPAT, 'UTF-8'), $logo_temp);
                $stmp .= $logo_temp;
            }
            if ($stmp == '') {

            } else {
                tpl_set_var('available_logos', $stmp);
            }
            unset($stmp);
            unset($logo_temp);
            XDb::xFreeResults($rs2);
        }

        //set buttons
    }
}

//make the template and send it out
tpl_BuildTemplate();
