<?php

/* allowed filesize of uploaded pictures in megabytes (MB). (pictures will be resized automaticly) */
$maximumPictureWeight = 3.5;

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
    } else {
        $tplname = 'newpic';

        $submit = tr('submit');
        $pictypedesc_cache = tr('add_new_picture');
        $pictypedesc_log = tr('log_pictures');
        $errnotitledesc = '<span class="errormsg">' . tr('no_title') . '</span>';
        $errnopicgivendesc = '<span class="errormsg">Brak nazwy pliku</span>';
        $message_title_internal = 'Wewnętrzny błąd serwera';
        $message_internal = 'Wystąpił wewnętrzny błąd serwera, jeśli ten błąd powtarza się prosimy o kontakt na adres ocpl @ opencaching.pl. W celu powtórzenia błędu wskazane byłoby załączenie do tego emial obrazek z opisem sytuacji.';
        $message_title_toobig = tr('to_big_data');
        $message_toobig = tr('max_size')." $maximumPictureWeight ".tr('max_size2');
        $message_title_wrongext = tr('bad_format');
        $message_wrongext = tr('bad_format_info');

        $objectid = isset($_REQUEST['objectid']) ? $_REQUEST['objectid'] : 0;
        $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : -1;
        $def_seq = isset($_REQUEST['def_seq']) ? $_REQUEST['def_seq'] : 1; // set up defaul seq for newly added picture

        $bSpoiler = isset($_REQUEST['spoiler']) ? $_REQUEST['spoiler'] : 0;
        if (($bSpoiler != 0) && ($bSpoiler != 1)) {
            $bSpoiler = 0;
        }
        $bNoDisplay = isset($_REQUEST['notdisplay']) ? $_REQUEST['notdisplay'] : 0;
        if (($bNoDisplay != 0) && ($bNoDisplay != 1))
            $bNoDisplay = 0;

        $title = isset($_REQUEST['title']) ? stripslashes($_REQUEST['title']) : '';

        tpl_set_var('maxImageWeight', $maximumPictureWeight);
        $allok = true;
        if (!is_numeric($objectid))
            $allok = false;
        if (!is_numeric($type))
            $allok = false;

        if ($allok == true) {
            //check if object exists and we are the owner (allowed to upload a pic)
            switch ($type) {
                // log
                case 1:
                    $rs = sql("SELECT `user_id`, `cache_id` FROM `cache_logs` WHERE `deleted`=0 AND `id`='&1'", $objectid);

                    if (mysql_num_rows($rs) == 0)
                        $allok = false;
                    else {
                        $r = sql_fetch_array($rs);

                        if ($r['user_id'] != $usr['userid'] && $usr['admin'] == false)
                            $allok = false;

                        $cacheid = $r['cache_id'];
                        tpl_set_var('cacheid', $cacheid);
                        tpl_set_var('pictypedesc', $pictypedesc_log);

                        $rsCache = sql("SELECT `name` FROM `caches` WHERE `cache_id`='&1'", $cacheid);
                        $rCache = sql_fetch_array($rsCache);
                        tpl_set_var('cachename', htmlspecialchars($rCache['name'], ENT_COMPAT, 'UTF-8'));
                        mysql_free_result($rsCache);

                        tpl_set_var('begin_cacheonly', '<!--');
                        tpl_set_var('end_cacheonly', '-->');
                    }

                    mysql_free_result($rs);
                    break;

                // cache
                case 2:
                    $rs = sql("SELECT `user_id`, `cache_id`, `name` FROM `caches` WHERE `cache_id`='&1'", $objectid);

                    if (mysql_num_rows($rs) == 0)
                        $allok = false;
                    else {
                        $r = sql_fetch_array($rs);

                        tpl_set_var('cachename', htmlspecialchars($r['name'], ENT_COMPAT, 'UTF-8'));
                        tpl_set_var('cacheid', $r['cache_id']);
                        tpl_set_var('pictypedesc', $pictypedesc_cache);

                        if ($r['user_id'] != $usr['userid'] && $usr['admin'] == false)
                            $allok = false;
                    }

                    tpl_set_var('begin_cacheonly', '');
                    tpl_set_var('end_cacheonly', '');

                    mysql_free_result($rs);
                    break;

                default:
                    $allok = false;
                    break;
            }

            $errnofilegiven = false;
            if (isset($_REQUEST['submit'])) {
                if (isset($_FILES['file']['error'])) {
                    if ($_FILES['file']['error'] == UPLOAD_ERR_NO_FILE) {
                        $errnofilegiven = true;
                        $allok = false;
                    }
                }
            }

            $errnotitle = false;
            if (($title == '') && (isset($_REQUEST['submit']))) {
                $allok = false;
                $errnotitle = true;
            }

            if ($allok == true) {
                if (isset($_REQUEST['submit'])) {
                    if ($_FILES['file']['error'] != 0) {
                        $tplname = 'message';
                        tpl_set_var('messagetitle', $message_title_internal);
                        tpl_set_var('message_start', '');
                        tpl_set_var('message_end', '');
                        tpl_set_var('message', $message_internal);
                        tpl_BuildTemplate();
                        exit;
                    } else {
                        $fna = mb_split('\\.', $_FILES['file']['name']);
                        $extension = mb_strtolower($fna[count($fna) - 1]);

                        if (mb_strpos($picextensions, ';' . $extension . ';') === false) {
                            $tplname = 'message';
                            tpl_set_var('messagetitle', $message_title_wrongext);
                            tpl_set_var('message_start', '');
                            tpl_set_var('message_end', '');
                            tpl_set_var('message', $message_wrongext);
                            tpl_BuildTemplate();
                            exit;
                        }

                        if ($_FILES['file']['size'] > ($maximumPictureWeight * 1024 * 1024)) { // file too big
                            $tplname = 'message';
                            tpl_set_var('messagetitle', $message_title_toobig);
                            tpl_set_var('message_start', '');
                            tpl_set_var('message_end', '');
                            tpl_set_var('message', $message_toobig);
                            tpl_BuildTemplate();
                            exit;
                        }

                        $uuid = create_uuid();

                        $image = new \lib\SimpleImage();
                        $image->load($_FILES['file']['tmp_name']);
                        if ($image->getHeight() > $image->getWidth() && $image->getHeight()>640) { //portrait
                            $image->resizeToHeight(640);
                        }
                        if ($image->getHeight() <= $image->getWidth() && $image->getWidth()>480)  {
                            $image -> resizeToWidth(640);
                        }
                        $image->save($picdir . '/' . $uuid . '.' . $extension, resolveImageTypeByFileExtension($extension));
                        sql("INSERT INTO pictures (`uuid`, `url`, `last_modified`, `title`, `description`, `desc_html`, `date_created`, `last_url_check`, `object_id`, `object_type`, `user_id`,`local`,`spoiler`,`display`,`node`,`seq`) VALUES ('&1', '&2', NOW(), '&3', '', 0, NOW(), NOW(),'&4', '&5', '&6', 1, '&7', '&8', '&9', '&10')", $uuid, $picurl . '/' . $uuid . '.' . $extension, $title, $objectid, $type, $usr['userid'], ($bSpoiler == 1) ? '1' : '0', ($bNoDisplay == 1) ? '0' : '1', $oc_nodeid, $def_seq);

                        switch ($type) {
                            // log
                            case 1:
                                sql("UPDATE `cache_logs` SET `picturescount`=`picturescount`+1 WHERE `id`='&1'", $objectid);
                                tpl_redirect('viewcache.php?cacheid=' . urlencode($cacheid));
                                break;

                            // cache
                            case 2:
                                sql("UPDATE `caches` SET `picturescount`=`picturescount`+1 WHERE `cache_id`='&1'", $objectid);
                                tpl_redirect('editcache.php?cacheid=' . urlencode($objectid));
                                break;
                        }

                        tpl_redirect_absolute($picurl . '/' . $uuid . '.' . $extension);
                        exit;
                    }
                }

                tpl_set_var('notdisplaychecked', ($bNoDisplay == 1) ? ' checked="checked"' : '');
                tpl_set_var('spoilerchecked', ($bSpoiler == 1) ? ' checked="checked"' : '');
                tpl_set_var('type', htmlspecialchars($type, ENT_COMPAT, 'UTF-8'));
                tpl_set_var('objectid', htmlspecialchars($objectid, ENT_COMPAT, 'UTF-8'));
                tpl_set_var('def_seq', htmlspecialchars($def_seq, ENT_COMPAT, 'UTF-8')); //update hidden value in newpic.tbl.php
                tpl_set_var('title', htmlspecialchars($title, ENT_COMPAT, 'UTF-8'));
                tpl_set_var('maxpicsize', $maxpicsize);
                tpl_set_var('submit', $submit);
                tpl_set_var('errnotitledesc', '');
                tpl_set_var('errnopicgivendesc', '');
            } else {
                if (($errnofilegiven == true) || ($errnotitle = true)) {
                    tpl_set_var('notdisplaychecked', ($bNoDisplay == 1) ? ' checked="checked"' : '');
                    tpl_set_var('spoilerchecked', ($bSpoiler == 1) ? ' checked="checked"' : '');
                    tpl_set_var('type', htmlspecialchars($type, ENT_COMPAT, 'UTF-8'));
                    tpl_set_var('objectid', htmlspecialchars($objectid, ENT_COMPAT, 'UTF-8'));
                    tpl_set_var('title', htmlspecialchars($title, ENT_COMPAT, 'UTF-8'));
                    tpl_set_var('maxpicsize', $maxpicsize);
                    tpl_set_var('submit', $submit);
                    tpl_set_var('errnopicgivendesc', '');
                    tpl_set_var('errnotitledesc', '');

                    if ($errnofilegiven == true)
                        tpl_set_var('errnopicgivendesc', $errnopicgivendesc);

                    if ($errnotitle == true)
                        tpl_set_var('errnotitledesc', $errnotitledesc);
                }
                else {
                    $tplname = 'message';
                    tpl_set_var('messagetitle', $message_title_internal);
                    tpl_set_var('message_start', '');
                    tpl_set_var('message_end', '');
                    tpl_set_var('message', $message_internal);
                }
            }
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
