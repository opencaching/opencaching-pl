<?php

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

$message = false;

//Preprocessing
if ($error == false) {
    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
    } else {
        $tplname = 'editmp3';
        require_once($stylepath . '/editmp3.inc.php');

        $uuid = isset($_REQUEST['uuid']) ? $_REQUEST['uuid'] : 0;
        if (!$uuid)
            $message = $message_mp3_not_found;

        if (!$message) {
            // read from databese and check owner
            if (!$resp = sql("SELECT `mp3`.`display`, `mp3`.`title`, `mp3`.`object_id`, `mp3`.`object_type`, `caches`.`name`, `caches`.`cache_id` FROM `mp3`, `caches`
                                  WHERE `caches`.`cache_id`=`mp3`.`object_id` AND `mp3`.`uuid`='&1' AND `mp3`.`user_id`='&2' LIMIT 1", $uuid, $usr['userid']))
                $message = $message_title_internal;
            else {
                if (!$row = sql_fetch_array($resp))
                    $message = $message_mp3_not_found;
            }

            if (isset($_POST['submit'])) {

                if ($_FILES['file']['name'] != '') {
                    // kucken, ob die Datei erfolgreich hochgeladen wurde
                    if ($_FILES['file']['error'] != 0) {
                        // huch ... keine Ahnung was ich da noch machen soll ?!
                        $tplname = 'message';
                        tpl_set_var('messagetitle', $message_title_internal);
                        tpl_set_var('message_start', '');
                        tpl_set_var('message_end', '');
                        tpl_set_var('message', $message_internal);
                        tpl_BuildTemplate();
                        exit;
                    } else {
                        // Dateiendung korrekt?
                        $fna = mb_split('\\.', $_FILES['file']['name']);
                        $extension = mb_strtolower($fna[count($fna) - 1]);

                        if (mb_strpos($mp3extensions, ';' . $extension . ';') === false) {
                            $tplname = 'message';
                            tpl_set_var('messagetitle', $message_title_wrongext);
                            tpl_set_var('message_start', '');
                            tpl_set_var('message_end', '');
                            tpl_set_var('message', $message_wrongext);
                            tpl_BuildTemplate();
                            exit;
                        }

                        // Datei zu groĂź?
                        if ($_FILES['file']['size'] > $maxmp3size) {
                            $tplname = 'message';
                            tpl_set_var('messagetitle', $message_title_toobig);
                            tpl_set_var('message_start', '');
                            tpl_set_var('message_end', '');
                            tpl_set_var('message', $message_toobig);
                            tpl_BuildTemplate();
                            exit;
                        }


                        // datei verschieben und in DB eintragen
                        //echo $_FILES['file']['tmp_name'], $mp3dir . '/' . $uuid . '.' . $extension;
                        move_uploaded_file($_FILES['file']['tmp_name'], $mp3dir . '/' . $uuid . '.' . $extension);
                    }
                }


                // store

                $row['display'] = isset($_REQUEST['notdisplay']) ? $_REQUEST['notdisplay'] : 0;
                if ($row['display'] == 0)
                    $row['display'] = 1;
                else
                    $row['display'] = 0; // reverse

                $row['title'] = isset($_REQUEST['title']) ? stripslashes($_REQUEST['title']) : '';

                if ($row['title'] == "") {
                    tpl_set_var('errnotitledesc', $errnotitledesc);
                } else {
                    if (!$resp = sql("UPDATE `mp3`
                                            SET `title`='&1',
                                                `display`='&2',
                                                `last_modified`=NOW()
                                          WHERE `uuid`='&3'", $row['title'], (($row['display'] == 1) ? '1' : '0'), $uuid))
                        $message = $message_title_internal;

                    if (!$message)
                        tpl_redirect('editcache.php?cacheid=' . urlencode($row['object_id']));
                }
            }
        }

        if (!$message) {
            // display
            $tplname = 'editmp3';
            $tpl_subtitle = htmlspecialchars($row['name'], ENT_COMPAT, 'UTF-8') . ' - ';
            tpl_set_var('cacheid', htmlspecialchars($row['cache_id'], ENT_COMPAT, 'UTF-8'));
            tpl_set_var('cachename', htmlspecialchars($row['name'], ENT_COMPAT, 'UTF-8'));
            tpl_set_var('title', htmlspecialchars($row['title'], ENT_COMPAT, 'UTF-8'));
            if ($row['title'] <= "")
                tpl_set_var('errnotitledesc', $errnotitledesc);
            else
                tpl_set_var('errnotitledesc', "");
            tpl_set_var('uuid', htmlspecialchars($uuid, ENT_COMPAT, 'UTF-8'));
            tpl_set_var('notdisplaychecked', $row['display'] == '0' ? 'checked' : '');

            if ($row['object_type'] == "2") {
                tpl_set_var('mp3typedesc', $mp3typedesc_cache);
                tpl_set_var('begin_cacheonly', "");
                tpl_set_var('end_cacheonly', "");
            } else if ($row['object_type'] == "1") {
                tpl_set_var('mp3typedesc', $mp3typedesc_log);
                tpl_set_var('begin_cacheonly', "<!--");
                tpl_set_var('end_cacheonly', "-->");
            }
        } else {
            $tplname = 'message';
            tpl_set_var('messagetitle', $message);
            tpl_set_var('message_start', '');
            tpl_set_var('message_end', '');
            tpl_set_var('message', $message);
        }
    }
}

//make the template and send it out
tpl_BuildTemplate();
?>
