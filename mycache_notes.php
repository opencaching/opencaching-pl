<?php

use Utils\Database\OcDb;
//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

global $usr;

function CleanSpecChars($log, $flg_html)
{
    $log_text = $log;

    if ($flg_html == 1) {
        $log_text = htmlspecialchars($log_text, ENT_COMPAT, 'UTF-8');
    }

    $log_text = str_replace("\r\n", " ", $log_text);
    $log_text = str_replace("\n", " ", $log_text);
    $log_text = str_replace("'", "-", $log_text);
    $log_text = str_replace("\"", " ", $log_text);

    return $log_text;
}

//Preprocessing
if ($error == false) {
    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
    } else {
        $tplname = 'mycache_notes';
        //get user record
        $userid = $usr['userid'];

        $tr_COG = tr('cog_user_name');
        $no_found_date = '---';

        $db = OcDb::instance();
        if (isset($_REQUEST["delete"])) {
            $note_id = $_REQUEST["delete"];
            //remove
            $query = "DELETE FROM `cache_notes` WHERE `note_id`=:1 AND `user_id`=:2";
            $db->multiVariableQuery($query, $note_id, $userid);
        }
        if (isset($_REQUEST["delete_coords"])) {
            $coords_id = $_REQUEST["delete_coords"];
            //remove
            $query = "DELETE FROM `cache_mod_cords` WHERE `id`=:1 AND `user_id`=:2";
            $db->multiVariableQuery($query, $coords_id, $userid);
        }
        //else
        {
            $query = "
                            SELECT `cache_notes`.`cache_id` `cacheid`,
                                `cache_notes`.`desc` `notes_desc`,
                                `caches`.`name` `cache_name`,
                                `cache_type`.`icon_small` `icon_large`,
                                `caches`.`type` `cache_type`,
                                `caches`.`cache_id` `cache_id`,
                                `caches`.`user_id` `user_id`,
                                note_id,
                                cl.text AS log_text,
                                cl.type AS log_type,
                                cl.user_id AS luser_id,
                                cl.date AS log_date,
                                cl.deleted AS log_deleted,
                                log_types.icon_small AS icon_small,
                                user.username AS user_name,
                                cache_mod_cords.id as cache_mod_cords_id,
                                cache_mod_cords.longitude,
                                cache_mod_cords.latitude
                            FROM
                                `cache_notes`
                                INNER JOIN `caches` ON (`cache_notes`.`cache_id`=`caches`.`cache_id`)
                                INNER JOIN cache_type ON (caches.type = cache_type.id)
                                left outer JOIN cache_logs as cl ON (caches.cache_id = cl.cache_id)
                                left outer JOIN log_types ON (cl.type = log_types.id)
                                left outer JOIN user ON (cl.user_id = user.user_id)
                                left outer JOIN cache_mod_cords ON (
                                        cache_mod_cords.user_id = cache_notes.user_id
                                        AND cache_mod_cords.cache_id = cache_notes.cache_id
                                    )
                            WHERE
                                `cache_notes`.`user_id`=:1
                                AND `cache_type`.`id`=`caches`.`type`
                                AND
                                    ( cl.id is null or cl.id =
                                    ( SELECT id
                                        FROM cache_logs cl_id
                                        WHERE cl.cache_id = cl_id.cache_id and cl_id.date =

                                            ( SELECT max( cache_logs.date )
                                                FROM cache_logs
                                                WHERE cl.cache_id = cache_id
                                            )
                                            limit 1
                                        ))
                            GROUP BY `cacheid`
                            UNION
                            SELECT `cache_mod_cords`.`cache_id` `cacheid`,
                                `cache_notes`.`desc` `notes_desc`,
                                `caches`.`name` `cache_name`,
                                `cache_type`.`icon_small` `icon_large`,
                                `caches`.`type` `cache_type`,
                                `caches`.`cache_id` `cache_id`,
                                `caches`.`user_id` `user_id`,
                                note_id,
                                cl.text AS log_text,
                                cl.type AS log_type,
                                cl.user_id AS luser_id,
                                cl.date AS log_date,
                                cl.deleted AS log_deleted,
                                log_types.icon_small AS icon_small,
                                user.username AS user_name,
                                cache_mod_cords.id as cache_mod_cords_id,
                                cache_mod_cords.longitude,
                                cache_mod_cords.latitude
                            FROM
                                cache_mod_cords
                                INNER JOIN `caches` ON (`cache_mod_cords`.`cache_id`=`caches`.`cache_id`)
                                INNER JOIN cache_type ON (caches.type = cache_type.id)
                                left outer JOIN cache_logs as cl ON (caches.cache_id = cl.cache_id)
                                left outer JOIN log_types ON (cl.type = log_types.id)
                                left outer JOIN user ON (cl.user_id = user.user_id)
                                left outer JOIN cache_notes ON (
                                        cache_notes.user_id = cache_mod_cords.user_id
                                        AND cache_notes.cache_id = cache_mod_cords.cache_id
                                    )
                            WHERE
                                `cache_mod_cords`.`user_id`=:1
                                AND `cache_type`.`id`=`caches`.`type`
                                AND
                                    ( cl.id is null or cl.id =
                                    ( SELECT id
                                        FROM cache_logs cl_id
                                        WHERE cl.cache_id = cl_id.cache_id and cl_id.date =

                                            ( SELECT max( cache_logs.date )
                                                FROM cache_logs
                                                WHERE cl.cache_id = cache_id
                                            )
                                            limit 1
                                        ))
                            GROUP BY `cacheid`
                            ORDER BY `cache_name`, log_date DESC";
            $db->multiVariableQuery($query, $userid);

            $count = $db->rowCount();
            if ($count != 0) {
                $notes = "";
                $bgcolor1 = '#ffffff';
                $bgcolor2 = '#eeeeee';

                for ($i = 0; $i < $count; $i++) {
                    $bgcolor = ( $i % 2 ) ? $bgcolor1 : $bgcolor2;

                    $notes_record = $db->dbResultFetch();
                    $cacheicon = myninc::checkCacheStatusByUser($notes_record, $usr['userid']);

                    $user_coords = '&nbsp;';
                    if ($notes_record['latitude'] != null && $notes_record['longitude'] != null) {
                        $user_coords = mb_ereg_replace(" ", "&nbsp;", htmlspecialchars(help_latToDegreeStr($notes_record['latitude'], 1), ENT_COMPAT, 'UTF-8')) . '&nbsp;' . mb_ereg_replace(" ", "&nbsp;", htmlspecialchars(help_lonToDegreeStr($notes_record['longitude'], 1), ENT_COMPAT, 'UTF-8'));
                        $user_coords.= '&nbsp;<a class="links"  href="mycache_notes.php?delete_coords=' . $notes_record['cache_mod_cords_id'] . '" onclick="return confirm(\'' . tr('coordsmod_info_02') . '\');"><img style="vertical-align: middle;" src="tpl/stdstyle/images/log/16x16-trash.png" title=' . tr('reset_coords') . ' /></a>';
                    }

                    $delete_user_note = '&nbsp;';
                    if ($notes_record['notes_desc'] != null) {
                        $delete_user_note = '<a class="links"  href="mycache_notes.php?delete={noteid}" onclick="return confirm(\'' . tr("mycache_notes_01") . '\');"><img style="vertical-align: middle;" src="tpl/stdstyle/images/log/16x16-trash.png" alt="" title=' . tr('delete') . ' /></a>';
                    }

                    $notes .= '<tr>
                                <td style="background-color: {bgcolor}"><img src="' . $cacheicon . '" alt="" /></td>
                                <td align="left"  style="background-color: {bgcolor}"><a  href="viewcache.php?cacheid={cacheid}" onmouseover="if (\'{notes_text}\' != \'\') Tip(\'{notes_text}\', OFFSETY, 25, OFFSETX, -135, PADDING,5, WIDTH,280,SHADOW,true)" onmouseout="UnTip()">' . $notes_record['cache_name'] . '</a></td>
                                <td style="background-color: {bgcolor}">&nbsp;</td>
                                <td nowrap style="background-color: {bgcolor}">' . $user_coords . '</td>
                                <td nowrap style="text-align:center; background-color: {bgcolor}">{lastfound}</td>
                                <td nowrap style="text-align:center; background-color: {bgcolor}"><img src="tpl/stdstyle/images/{icon_name}" border="0" alt="" onmouseover="Tip(\'{log_text}\', OFFSETY, 25, OFFSETX, -135, PADDING,5, WIDTH,280,SHADOW,true)" onmouseout="UnTip()"/></td>
                                <td style="background-color: {bgcolor}; text-align:center">' . $delete_user_note . '</td></tr>';
                    $notes = mb_ereg_replace('{bgcolor}', $bgcolor, $notes);
                    $notes = mb_ereg_replace('{cacheid}', $notes_record["cacheid"], $notes);
                    $notes = mb_ereg_replace('{noteid}', $notes_record["note_id"], $notes);
                    if ($notes_record['log_date'] == NULL || $notes_record['log_date'] == '0000-00-00 00:00:00') {
                        $notes = mb_ereg_replace('{lastfound}', htmlspecialchars($no_found_date, ENT_COMPAT, 'UTF-8'), $notes);
                    } else {
                        $notes = mb_ereg_replace('{lastfound}', htmlspecialchars(strftime($dateformat, strtotime($notes_record['log_date'])), ENT_COMPAT, 'UTF-8'), $notes);
                    };

                    if ($notes_record["log_deleted"] == 1) {  // if last record is deleted change icon and text
                        $log_text = tr('vl_Record_deleted');
                        $notes_record['icon_small'] = "log/16x16-trash.png";
                    } else {
                        $log_text = CleanSpecChars($notes_record['log_text'], 1);
                    };


                    if ($notes_record['log_type'] == 12 && !$usr['admin']) {
                        $notes_record['user_id'] = '0';
                        $notes_record['user_name'] = $tr_COG;
                    };


                    $log_text = "<b>" . $notes_record['user_name'] . ":</b><br>" . $log_text;
                    $notes = mb_ereg_replace('{log_text}', $log_text, $notes);
                    $notes_text = CleanSpecChars($notes_record['notes_desc'], 1);
                    $notes = mb_ereg_replace('{notes_text}', $notes_text, $notes);
                    $notes = mb_ereg_replace('{icon_name}', $notes_record['icon_small'], $notes);
                }


                tpl_set_var('notes_content', $notes);
            } else {
                tpl_set_var('notes_content', '<br/><span style="font-size: 14px;">&nbsp;&nbsp;<strong>' . tr('no_note') . '</strong></span><br/></br/>');
            }
        }
        unset($db);
    }
}

tpl_BuildTemplate();
