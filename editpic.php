<?php

use src\Utils\Database\XDb;
use src\Models\OcConfig\OcConfig;
use src\Utils\Img\OcImage;


//prepare the templates and include all neccessary
require_once(__DIR__.'/lib/common.inc.php');

$message = false;

//Preprocessing
if ($error == false) {
    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
    } else {
        $tplname = 'editpic';
        $view = tpl_getView();
        $view->setVar('maxPicSize',  $config['limits']['image']['filesize'] * 1024 * 1024);

        tpl_set_var('maxpicsizeMB', $config['limits']['image']['filesize']);
        tpl_set_var('maxpicresolution', $config['limits']['image']['pixels_text']);
        tpl_set_var('picallowedformats', $config['limits']['image']['extension_text']);

        $uuid = isset($_REQUEST['uuid']) ? $_REQUEST['uuid'] : 0;
        if (!$uuid) {
            $message = tr('no_picture');
        }

        if (!$message) {
            $owner = XDb::xMultiVariableQueryValue(
                "SELECT `user_id` FROM `pictures` WHERE `uuid`= :1 LIMIT 1", 0, $uuid);

            if ($usr['admin'] || $owner == $usr['userid']) {

                $rs = XDb::xSql(
                    "SELECT `pictures`.`spoiler`, `pictures`.`display`, `pictures`.`title`, `pictures`.`object_id`,
                            `pictures`.`object_type`, `caches`.`name`, `caches`.`cache_id`
                    FROM `pictures`, `caches`
                    WHERE `caches`.`cache_id`=`pictures`.`object_id` AND `pictures`.`uuid`= ? AND `pictures`.`user_id`= ? LIMIT 1",
                    $uuid, $owner);

                if (!$row = XDb::xFetchArray($rs)) {
                    $message = tr('no_picture');
                }
            } else {
                $tplname = 'error';
                tpl_set_var('tplname', 'editpic.php');
                tpl_set_var('error_msg', tr('noaccess_error_01'));
                tpl_BuildTemplate();
                exit;
            }
        }

        if (!$message) {

            if (isset($_POST['submit'])) {

                $filePath = null;
                if ($_FILES['file']['name'] != '') {
                    // check if the file has been uploaded successfully
                    if ($_FILES['file']['error'] != 0) {
                        // oops ... no idea what I should do now
                        $tplname = 'message';
                        tpl_set_var('messagetitle', tr('file_err_internal_title'));
                        tpl_set_var('message_start', '');
                        tpl_set_var('message_end', '');
                        tpl_set_var('message', tr('file_err_internal_file'));
                        tpl_BuildTemplate();
                        exit;
                    } else {
                        // file extension ok?
                        $fna = mb_split('\\.', $_FILES['file']['name']);
                        $extension = mb_strtolower($fna[count($fna) - 1]);

                        if (mb_strpos($config['limits']['image']['extension'], ';' . $extension . ';') === false) {
                            $tplname = 'message';
                            tpl_set_var('messagetitle', tr('image_bad_format'));
                            tpl_set_var('message_start', '');
                            tpl_set_var('message_end', '');
                            tpl_set_var('message', tr('image_bad_format_info'));
                            tpl_BuildTemplate();
                            exit;
                        }

                        if ($_FILES['file']['size'] > round($config['limits']['image']['filesize'] * 1024 * 1024) ) {
                            // file too big
                            $tplname = 'message';
                            tpl_set_var('messagetitle', tr('image_err_too_big'));
                            tpl_set_var('message_start', '');
                            tpl_set_var('message_end', '');
                            tpl_set_var('message', tr('image_max_size'));
                            tpl_BuildTemplate();
                            exit;
                        }


                        if ($config['limits']['image']['resize'] == 1 && $_FILES['file']['size'] > round($config['limits']['image']['resize_larger'] * 1024 * 1024) ) {
                            // Apply resize to uploaded image
                            $filePath = OcImage::createThumbnail(
                                $_FILES['file']['tmp_name'],
                                OcConfig::getPicUploadFolder(true) . '/' . $uuid,
                                [$config['limits']['image']['width'], $config['limits']['image']['height']]);

                        } else {
                            // Save uploaded image AS IS
                            $filePath = OcConfig::getPicUploadFolder(true) . '/' . $uuid . '.' . $extension;
                            move_uploaded_file($_FILES['file']['tmp_name'], $filePath);
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
                    
                    if($filePath) {
                        XDb::xSql(
                            "UPDATE `pictures` SET `title`= ?, `display`= ?, `spoiler`= ?, `last_modified` = NOW(), url = ?
                             WHERE `uuid`= ? ",
                            $row['title'],
                            (($row['display'] == 1) ? '1' : '0'), (($row['spoiler'] == 1) ? '1' : '0'),
                            OcConfig::getPicBaseUrl().'/'.basename($filePath), $uuid);
                    } else {
                        XDb::xSql(
                            "UPDATE `pictures` SET `title`= ?, `display`= ?, `spoiler`= ?, `last_modified` = NOW()
                             WHERE `uuid`= ? ",
                            $row['title'], (($row['display'] == 1) ? '1' : '0'), (($row['spoiler'] == 1) ? '1' : '0'), $uuid);
                    }
                    
                    
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
                tpl_set_var('errnotitledesc', '<span class="errormsg">' . tr('image_err_no_title') . '</span>');
            else
                tpl_set_var('errnotitledesc', "");
            tpl_set_var('uuid', htmlspecialchars($uuid, ENT_COMPAT, 'UTF-8'));
            tpl_set_var('spoilerchecked', $row['spoiler'] == '1' ? 'checked' : '');
            tpl_set_var('notdisplaychecked', $row['display'] == '0' ? 'checked' : '');

            if ($row['object_type'] == "2") {
                tpl_set_var('pictypedesc', tr('cache_pictures'));
                tpl_set_var('begin_cacheonly', "");
                tpl_set_var('end_cacheonly', "");
            } else if ($row['object_type'] == "1") {
                tpl_set_var('pictypedesc', tr('log_pictures'));
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
