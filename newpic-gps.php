<?php
/***************************************************************************
                                                                ./newpic.php
                                                            -------------------
        begin                : Wed August 18 2005
        copyright            : (C) 2004 The OpenCaching Group
        forum contact at     : http://www.opencaching.com/phpBB2

    ***************************************************************************/

/***************************************************************************
    *
    *   This program is free software; you can redistribute it and/or modify
    *   it under the terms of the GNU General Public License as published by
    *   the Free Software Foundation; either version 2 of the License, or
    *   (at your option) any later version.
    *
    ***************************************************************************/

/****************************************************************************

   Unicode Reminder ăĄă˘

     add a picture for "my profile", caches, logs etc.

     requiered page-arguments
       - logged in (userid)
       - objectid                ... cacheid, logid, etc
       - type                    ... type of the object

       userid must be owner of the object referenced by objectid

 ****************************************************************************/

    //prepare the templates and include all neccessary
    require_once('./lib/common.inc.php');

    //Preprocessing
    if ($error == false)
    {
        //user logged in?
        if ($usr == false)
        {
            $target = urlencode(tpl_get_current_page());
            tpl_redirect('login.php?target='.$target);
        }
        else
        {
            $tplname = 'newpic-gps';
            require_once($stylepath . '/newpic.inc.php');

            $objectid = isset($_REQUEST['objectid']) ? $_REQUEST['objectid'] : 0;
            $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : -1;

            $bSpoiler = isset($_REQUEST['spoiler']) ? $_REQUEST['spoiler'] : 0;
            if (($bSpoiler != 0) && ($bSpoiler != 1)) $bSpoiler = 0;

            $bNoDisplay = isset($_REQUEST['notdisplay']) ? $_REQUEST['notdisplay'] : 0;
            if (($bNoDisplay != 0) && ($bNoDisplay != 1)) $bNoDisplay = 0;

            $title = isset($_REQUEST['title']) ? stripslashes($_REQUEST['title']) : '';

            $allok = true;
            if (!is_numeric($objectid)) $allok = false;
            if (!is_numeric($type)) $allok = false;

            if ($allok == true)
            {
              //check if object exists and we are the owner (allowed to upload a pic)
              switch ($type)
              {
                    // log
                    case 1:
                        $rs = sql("SELECT `user_id`, `cache_id` FROM `cache_logs` WHERE `deleted`=0 AND `id`='&1'", $objectid);

                        if (mysql_num_rows($rs) == 0)
                          $allok = false;
                        else
                        {
                          $r = sql_fetch_array($rs);

                          if ($r['user_id'] != $usr['userid'] && $usr['admin']==false )
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
                        $rs = sql("SELECT `user_id`, `cache_id`, `name`, `longitude`, `latitude`, `wp_oc`  FROM `caches` WHERE `cache_id`='&1'", $objectid);

                        if (mysql_num_rows($rs) == 0)
                          $allok = false;
                        else
                        {
                            $r = sql_fetch_array($rs);

                                tpl_set_var('cachename', htmlspecialchars($r['name'], ENT_COMPAT, 'UTF-8'));
                                tpl_set_var('cacheid', $r['cache_id']);
                                tpl_set_var('pictypedesc', $pictypedesc_cache);

                            if ($r['user_id'] != $usr['userid'] && $usr['admin']==false)
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
                if (isset($_REQUEST['submit']))
                {
                    if (isset($_FILES['file']['error']))
                    {
                        if ($_FILES['file']['error'] == UPLOAD_ERR_NO_FILE)
                        {
                            $errnofilegiven = true;
                            $allok = false;
                        }
                    }
                }

                $errnotitle = false;
                if (($title == '') && (isset($_REQUEST['submit'])))
                {
                    $allok = false;
                    $errnotitle = true;
                }

              if ($allok == true)
              {
                    //ok, wir haben eine gĂźltige objectid und sind der owner ... also form anzeigen oder in DB eintragen ...
                    if (isset($_REQUEST['submit']))
                    {
                        // kucken, ob die Datei erfolgreich hochgeladen wurde
                        if ($_FILES['file']['error'] != 0)
                        {
                            // huch ... keine Ahnung was ich da noch machen soll ?!
                            $tplname = 'message';
                            tpl_set_var('messagetitle', $message_title_internal);
                            tpl_set_var('message_start', '');
                            tpl_set_var('message_end', '');
                            tpl_set_var('message', $message_internal);
                            tpl_BuildTemplate();
                            exit;
                        }
                        else
                        {
                            // Dateiendung korrekt?
                            $fna = mb_split('\\.', $_FILES['file']['name']);
                            $extension = mb_strtolower($fna[count($fna) - 1]);

                            if (mb_strpos($picextensions, ';' . $extension . ';') === false)
                            {
                                $tplname = 'message';
                                tpl_set_var('messagetitle', $message_title_wrongext);
                                tpl_set_var('message_start', '');
                                tpl_set_var('message_end', '');
                                tpl_set_var('message', $message_wrongext);
                                tpl_BuildTemplate();
                                exit;
                            }

                            // Datei zu groĂź?
                            if ($_FILES['file']['size'] > $maxpicsize)
                            {
                                $tplname = 'message';
                                tpl_set_var('messagetitle', $message_title_toobig);
                                tpl_set_var('message_start', '');
                                tpl_set_var('message_end', '');
                                tpl_set_var('message', $message_toobig);
                                tpl_BuildTemplate();
                                exit;
                            }

                            $uuid = create_uuid();

                            // datei verschieben und in DB eintragen
                            move_uploaded_file($_FILES['file']['tmp_name'], $picdir . '/' . $uuid . '.' . $extension);

                            /*
                             * [EN] add EXIF (GPS info) to image
                             *
                             * [PL] dodanie tagów EXIF (kordów)
                            */


                            if($type==2)
                            {
                                //PHP EXIF Library
                                require_once('lib/pel/gps.php');

                                // pobranie danych EXIF z obrazka
                                $exif = @exif_read_data($picdir.'/'.$uuid.'.'.$extension);

                                // sprawdzenie czy są już wpisane kordy
                                if(@!isset($exif["GPSLatitude"]))
                                {

                                    // addGpsInfo(file,description,comment,artist,make,model,longitude,latitude)
                                    addGpsInfo($picdir.'/'.$uuid.'.'.$extension, $r['wp_oc'], $r['name'], "opencaching.pl", "OC PL", "OC PL", $r['longitude'], $r['latitude']);
                                }


                            }
                            /*
                            */

                            sql("INSERT INTO pictures (`uuid`,
                                                                                 `url`,
                                                                                 `last_modified`,
                                                                                 `title`,
                                                                                 `description`,
                                                                                 `desc_html`,
                                                                                 `date_created`,
                                                                                 `last_url_check`,
                                                                                 `object_id`,
                                                                                 `object_type`,
                                                                                 `user_id`,
                                                                                 `local`,
                                                                                 `spoiler`,
                                                                                 `display`,
                                                                                 `node`
                                                            ) VALUES ('&1', '&2', NOW(), '&3', '', 0, NOW(), NOW(),'&4', '&5', '&6', 1, '&7', '&8', '&9')",
                                                            $uuid, $picurl . '/' . $uuid . '.' . $extension, $title, $objectid, $type, $usr['userid'], ($bSpoiler == 1) ? '1' : '0', ($bNoDisplay == 1) ? '0' : '1', $oc_nodeid);

                            switch ($type)
                            {
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
                    tpl_set_var('title', htmlspecialchars($title, ENT_COMPAT, 'UTF-8'));
                    tpl_set_var('maxpicsize', $maxpicsize);
                    tpl_set_var('submit', $submit);

                    tpl_set_var('errnotitledesc', '');
                    tpl_set_var('errnopicgivendesc', '');
              }
              else
              {
                    if (($errnofilegiven == true) || ($errnotitle = true))
                    {
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
                    else
                    {
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
?>
