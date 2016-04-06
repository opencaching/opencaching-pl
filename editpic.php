<?php

use Utils\Database\XDb;
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
        $tplname = 'editpic';
        require_once($stylepath . '/editpic.inc.php');

        $uuid = isset($_REQUEST['uuid']) ? $_REQUEST['uuid'] : 0;
        if (!$uuid)
            $message = $message_picture_not_found;

        $owner = XDb::xMultiVariableQueryValue(
            "SELECT `user_id` FROM `pictures` WHERE `uuid`= :1 LIMIT 1", 0, $uuid);

        if ($usr['admin'] || $owner == $usr['userid']) {
            if (!$message) {

                $rs = XDb::xSql(
                    "SELECT `pictures`.`spoiler`, `pictures`.`display`, `pictures`.`title`, `pictures`.`object_id`,
                            `pictures`.`object_type`, `caches`.`name`, `caches`.`cache_id`
                    FROM `pictures`, `caches`
                    WHERE `caches`.`cache_id`=`pictures`.`object_id` AND `pictures`.`uuid`= ? AND `pictures`.`user_id`= ? LIMIT 1",
                    $uuid, $owner);

                if (!$row = XDb::xFetchArray($rs)) {
                    $message = $message_picture_not_found;
                }
            }
        }

        if (!$message) {

            if (isset($_POST['submit'])) {

                if ($_FILES['file']['name'] != '') {
                    // check if the file has been uploaded successfully
                    if ($_FILES['file']['error'] != 0) {
                        // oops ... no idea what I should do now
                        $tplname = 'message';
                        tpl_set_var('messagetitle', $message_title_internal);
                        tpl_set_var('message_start', '');
                        tpl_set_var('message_end', '');
                        tpl_set_var('message', $message_internal);
                        tpl_BuildTemplate();
                        exit;
                    } else {
                        // file extension ok?
                        $fna = mb_split('\\.', $_FILES['file']['name']);
                        $extension = mb_strtolower($fna[count($fna) - 1]);

                        if (mb_strpos($config['limits']['image']['extension'], ';' . $extension . ';') === false) {
                            $tplname = 'message';
                            tpl_set_var('messagetitle', $message_title_wrongext);
                            tpl_set_var('message_start', '');
                            tpl_set_var('message_end', '');
                            tpl_set_var('message', $message_wrongext);
                            tpl_BuildTemplate();
                            exit;
                        }

                        // file too big?
                        if ($_FILES['file']['size'] > $config['limits']['image']['filesize'] * 1024 * 1024) {
                            $tplname = 'message';
                            tpl_set_var('messagetitle', $message_title_toobig);
                            tpl_set_var('message_start', '');
                            tpl_set_var('message_end', '');
                            tpl_set_var('message', $message_toobig);
                            tpl_BuildTemplate();
                            exit;
                        }


                        if ($config['limits']['image']['resize'] == 1 && $_FILES['file']['size'] > 102400) {
                            // Apply resize to uploaded image
                            $image = new \lib\SimpleImage();
                            $image->load($_FILES['file']['tmp_name']);
                            if ($image->getHeight() > $image->getWidth() && $image->getHeight() > $config['limits']['image']['height']) { //portrait
                                $image->resizeToHeight($config['limits']['image']['height']);
                            }
                            if ($image->getHeight() <= $image->getWidth() && $image->getWidth() > $config['limits']['image']['width'])  {
                                $image -> resizeToWidth($config['limits']['image']['width']);
                            }
                            $image->save($picdir . '/' . $uuid . '.' . $extension, resolveImageTypeByFileExtension($extension));
                        } else {
                            // Save uploaded image AS IS
                            move_uploaded_file($_FILES['file']['tmp_name'], $picdir . '/' . $uuid . '.' . $extension);
                        }
                    }
                }


                // store
                $row['spoiler'] = isset($_REQUEST['spoiler']) ? $_REQUEST['spoiler'] : 0;
                if (($row['spoiler'] != 0) && ($row['spoiler'] != 1))
                    $row['spoiler'] = 0;

                $row['display'] = isset($_REQUEST['notdisplay']) ? $_REQUEST['notdisplay'] : 0;
                if ($row['display'] == 0)
                    $row['display'] = 1;
                else
                    $row['display'] = 0; // reverse

                $row['title'] = isset($_REQUEST['title']) ? stripslashes($_REQUEST['title']) : '';

                if ($row['title']) {
                    XDb::xSql(
                        "UPDATE `pictures` SET `title`= ?, `display`= ?, `spoiler`= ?, `last_modified` = NOW()
                         WHERE `uuid`= ? ",
                        $row['title'], (($row['display'] == 1) ? '1' : '0'), (($row['spoiler'] == 1) ? '1' : '0'), $uuid);

                    switch ($row['object_type']) {
                        // log - currently not used, because log pictures cannot be edited
                        case 1:
                            XDb::xSql(
                                "UPDATE `cache_logs` SET `last_modified`=NOW() WHERE `id`= ?",
                                $row['object_id']);
                            break;

                        // cache
                        case 2:
                            XDb::xSql(
                                "UPDATE `caches` SET `last_modified`=NOW() WHERE `cache_id`= ?",
                                $row['object_id']);
                            break;
                    }

                    tpl_redirect('editcache.php?cacheid=' . urlencode($row['object_id']));
                }
            }
        }

        if (!$message) {
            // display
            $tplname = 'editpic';
            $tpl_subtitle = htmlspecialchars($row['name'], ENT_COMPAT, 'UTF-8') . ' - ';
            tpl_set_var('cacheid', htmlspecialchars($row['cache_id'], ENT_COMPAT, 'UTF-8'));
            tpl_set_var('cachename', htmlspecialchars($row['name'], ENT_COMPAT, 'UTF-8'));
            tpl_set_var('title', htmlspecialchars($row['title'], ENT_COMPAT, 'UTF-8'));
            if ($row['title'] <= "")
                tpl_set_var('errnotitledesc', $errnotitledesc);
            else
                tpl_set_var('errnotitledesc', "");
            tpl_set_var('uuid', htmlspecialchars($uuid, ENT_COMPAT, 'UTF-8'));
            tpl_set_var('spoilerchecked', $row['spoiler'] == '1' ? 'checked' : '');
            tpl_set_var('notdisplaychecked', $row['display'] == '0' ? 'checked' : '');

            if ($row['object_type'] == "2") {
                tpl_set_var('pictypedesc', $pictypedesc_cache);
                tpl_set_var('begin_cacheonly', "");
                tpl_set_var('end_cacheonly', "");
            } else if ($row['object_type'] == "1") {
                tpl_set_var('pictypedesc', $pictypedesc_log);
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

function resolveImageTypeByFileExtension($fileExtension)
{
    $extension = strtoupper($fileExtension);
    switch ($extension){
        case 'JPG':
        case 'JPEG':
            return IMAGETYPE_JPEG;
        case 'PNG':
            return IMAGETYPE_PNG;
        case 'GIF':
            return IMAGETYPE_GIF;
    }
    return IMAGETYPE_JPEG;
}
