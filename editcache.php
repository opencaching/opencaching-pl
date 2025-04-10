<?php

use src\Models\ApplicationContainer;
use src\Models\ChunkModels\UploadModel;
use src\Models\Coordinates\Coordinates;
use src\Models\GeoCache\GeoCache;
use src\Models\GeoCache\GeoCacheCommons;
use src\Models\GeoCache\GeoCacheLog;
use src\Models\OcConfig\OcConfig;
use src\Models\Pictures\OcPicture;
use src\Utils\Database\OcDb;
use src\Utils\Database\XDb;
use src\Utils\Debug\Debug;
use src\Utils\EventHandler\EventHandler;
use src\Utils\Gis\Countries;
use src\Utils\I18n\I18n;
use src\Utils\I18n\Languages;
use src\Utils\Text\Validator;

require_once __DIR__ . '/lib/common.inc.php';

$view->loadJQuery();
$view->loadJQueryUI();
$view->addLocalCss('/views/editCache/editCache.css');
$view->addHeaderChunk('handlebarsJs');
$view->addHeaderChunk('upload/upload');

function build_drop_seq($item_row, $selected_seq, $max_drop, $thisid, $drop_type)
{
    //builds drop-down menu to define sequence for pciture or mp3 - drop_type decides)
    if ($max_drop > 0) {
        switch ($drop_type) {
            case 'pic':
                $drop_label_tit = tr('ec_Sequence');
                break;
            case 'mp3':
                $drop_label_tit = tr('ec_Sequence_mp3');
                break;
        }

        $ret = '<label title="' . $drop_label_tit . '"><select class="form-control input40" onchange="document.getElementById(\'' . $drop_type . '_seq_changed' . $item_row . '\').value=\'yes\'; yes_change(); " id="' . $drop_type . '_seq_select' . $item_row . '" name="' . $drop_type . '_seq_select' . $item_row . '">
        ';

        for ($i = 1; $i <= $max_drop + 1; $i++) { //add extra row so spacer can be added
            if ($i == $selected_seq) {
                $sel = ' selected="true" ';
            } else {
                $sel = '';
            }

            $ret .= '<option value="' . $i . '" label="' . $i . '"' . $sel . '>' . $i . '</option>
            ';
        }
        $ret .= '</select></label>&nbsp;
        ';
        $ret .= '<input type="hidden"  id="' . $drop_type . '_seq_id' . $item_row . '" name="' . $drop_type . '_seq_id' . $item_row . '" value="' . $thisid . '">
        '; //instert picture/mp3 id into hidden fields - item_row is current order based on current seq values
        $ret .= '<input type="hidden" id="' . $drop_type . '_seq_changed' . $item_row . '" name="' . $drop_type . '_seq_changed' . $item_row . '" value="no">
        '; // set hidden field - to be changed by javascript to yes - in such case it will trigger SQL update

        return $ret;
    }
}

//cacheid
$cache_id = 0;

if (isset($_REQUEST['cacheid'])) {
    $cache_id = (int) $_REQUEST['cacheid'];
    $geocache = GeoCache::fromCacheIdFactory($cache_id);
    $view->setVar('geocache', $geocache);
}

//user logged in?
$loggedUser = ApplicationContainer::GetAuthorizedUser();

if (! $loggedUser) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);

    exit;
}

$dbc = OcDb::instance();
$thatquery
    = "SELECT user_id, name, picturescount, mp3count, type, size, date_hidden, date_activate, date_created, longitude, latitude,
                    country, terrain, difficulty, status, search_time, way_length, logpw, wp_gc, wp_nc, wp_ge, wp_tc, node,
                    IFNULL(`cache_location`.`code3`,'') region
            FROM `caches`
                LEFT JOIN `cache_location` ON `caches`.`cache_id`= `cache_location`.`cache_id`
            WHERE `caches`.`cache_id`=:v1";

$params = [];
$params['v1']['value'] = $cache_id;
$params['v1']['data_type'] = 'integer';
$s = $dbc->paramQuery($thatquery, $params);
unset($params);

if ($cache_record = $dbc->dbResultFetch($s)) {
    if ($cache_record['user_id'] == $loggedUser->getUserId() || $loggedUser->hasOcTeamRole()) {
        // from deleted editcache.inc.php:
        $submit = 'Zapisz';
        $remove = tr('delete');
        $edit = tr('edit');
        $error_general = '<div class="warning">' . tr('error_new_cache') . '</div>';
        $error_coords_not_ok = '<br/><img src="images/misc/32x32-impressum.png" class="icon32" alt=""  />&nbsp;<span class="errormsg">' . tr('bad_coordinates') . '</span>';
        $time_not_ok_message = '<br/><img src="images/misc/32x32-impressum.png" class="icon32" alt=""  />&nbsp;<span class="errormsg">' . tr('time_incorrect') . '</span>';
        $way_length_not_ok_message = '<br/><img src="images/misc/32x32-impressum.png" class="icon32" alt=""  />&nbsp;<span class="errormsg">' . tr('distance_incorrect') . '</span>';
        $date_not_ok_message = '<br/><img src="images/misc/32x32-impressum.png" class="icon32" alt=""  />&nbsp;<span class="errormsg">' . tr('date_incorrect') . '</span>';
        $name_not_ok_message = '<br/><img src="images/misc/32x32-impressum.png" class="icon32" alt=""  />&nbsp;<span class="errormsg">' . tr('no_cache_name') . '</span>';

        $size_not_ok_message = '<br/><img src="images/misc/32x32-impressum.png" class="icon32" alt=""  />&nbsp;&nbsp;<span class="errormsg">' . tr('size_incorrect') . '</span>';
        $all_countries_submit = '<input class="btn btn-default btn-sm" type="submit" name="show_all_countries_submit" value="' . tr('show_all_countries') . '"/>';

        $status_message = '&nbsp;<span class="errormsg">' . tr('status_incorrect') . '</span>';

        $nomp3 = '<tr><td colspan="2"><div class="notice">' . tr('no_mp3_files') . '</div></td></tr>';
        $mp3line = '<tr><td colspan="2">{seq_drop_mp3}<img src="images/free_icons/sound.png" class="icon32" alt=""  />&nbsp;<a target="_BLANK" href="{link}">{title}</a>&nbsp;&nbsp;<img src="images/actions/edit-16.png"  align="middle"  alt="" title="" /> [<a href="editmp3.php?uuid={uuid}" onclick="return check_if_proceed();">' . $edit . '</a>] <img src="images/log/16x16-trash.png" border="0" align="middle" class="icon16" alt="" title="" />[<a href="removemp3.php?uuid={uuid}" onclick="if (confirm(\'' . tr('ec_delete_mp3') . '\')) {return check_if_proceed();} else {return false;};">' . $remove . '</a>]</td></tr>';
        $mp3lines = '{lines}<tr><td colspan="2">&nbsp;</td></tr>';

        $nowp = '<div class="notice">' . tr('nowp_notice') . '</div>';
        $wpline = '<tr>{stagehide_start}<td align="center" valign="middle"><center>{number}</center></td>{stagehide_end}<td align="center" valign="middle"><center><img src="{wp_icon}" alt="" title="{type}" /></center></td><td align="center" valign="middle">{type}</td><td align="center" valign="middle"><b><span style="color: rgb(88,144,168)">{lat}<br />{lon}</span></b></td><td align="center" valign="middle">{desc}</td><td align="center" valign="middle"><center><img src="{status}" alt="" /></center></td><td align="center" valign="middle"><center><a class="links" onclick="return check_if_proceed();"  href="editwp.php?wpid={wpid}"><img src="images/actions/edit-16.png" alt="" /></a></center></td><td align="center" valign="middle"><center><a class="links" href="editwp.php?wpid={wpid}&delete" onclick="if (confirm(\'' . tr('ec_delete_wp') . '\')) {return check_if_proceed();} else {return false;};"><img src="images/log/16x16-trash.png" align="middle" class="icon16" alt="" /></a></center></td> </tr>';

        $cache_attrib_js = "new Array({id}, {selected}, '{img_undef}', '{img_large}')";
        $cache_attrib_pic = '<img id="attr{attrib_id}" src="{attrib_pic}" border="0" alt="{attrib_text}" title="{attrib_text}" onmousedown="toggleAttr({attrib_id}); yes_change();" /> ';

        $activation_form = '
        <tr class="form-group-sm">
          <td colspan="2">
             <fieldset style="border: 1px solid black; width: 80%; height: 32%; background-color: #FFFFFF;">
                <legend>&nbsp; <strong>' . tr('submit_new_cache') . '</strong> &nbsp;</legend>
                <input type="radio" onChange="yes_change();" class="radio" name="publish" id="publish_now" value="now" {publish_now_checked}>&nbsp;<label for="publish_now">' . tr('publish_now') . '</label><br />
                <input type="radio" onChange="yes_change();" class="radio" name="publish" id="publish_later" value="later" {publish_later_checked}>&nbsp;<label for="publish_later">' . tr('publish_date') . ':</label>
                <input type="text" class="form-control" id="activateDatePicker" id="activateDatePicker" value="{activate_year}-{activate_month}-{activate_day}" onchange="hiddenDatePickerChange(\'activate\'); selectPublishLater()"/>
                <input class="input40" type="hidden" name="activate_year" id="activate_year" onChange="yes_change();" maxlength="4" value="{activate_year}"/>
                <input class="input20" type="hidden" name="activate_month" id="activate_month" onChange="yes_change();" maxlength="2" value="{activate_month}"/>
                <input class="input20" type="hidden" name="activate_day" id="activate_day" onChange="yes_change();" maxlength="2" value="{activate_day}"/>&nbsp;
                <select name="activate_hour" class="form-control input70" onChange="yes_change();" >{activation_hours}
                </select>&nbsp;–&nbsp;{activate_on_message}<br />
                <input type="radio" onChange="yes_change();" class="radio" name="publish" id="publish_notnow" value="notnow" {publish_notnow_checked}>&nbsp;<label for="publish_notnow">' . tr('dont_publish_yet') . '</label>
              </fieldset>
            </td>
        </tr>
        ';

        //here we read all used information from the form if submitted, otherwise from DB
        // wihout virtuals and webcams
        if (isset($_POST['type'])) {
            if ((($_POST['type'] == GeoCache::TYPE_VIRTUAL && $cache_record['type'] != GeoCache::TYPE_VIRTUAL)
                    || ($_POST['type'] == GeoCache::TYPE_WEBCAM && $cache_record['type'] != GeoCache::TYPE_WEBCAM))
                    && ! $loggedUser->hasOcTeamRole()) {
                $_POST['type'] = $cache_record['type'];
            }
        }

        $cache_name = $_POST['name'] ?? $cache_record['name'];
        $cache_type = $_POST['type'] ?? $cache_record['type'];

        $pic_count_check = $cache_record['picturescount'];

        if ($pic_count_check > 0) {
            if (isset($_POST['pic_seq_select1'])) { // check if in POST mode and in case any picture is attached (re-)update sequence value, providing it was changed - value of pic_seq_change_X)
                for ($i = 1; $i <= $pic_count_check; $i++) {
                    if (! isset($_POST['pic_seq_select' . $i], $_POST['pic_seq_id' . $i])) {
                        continue;
                    }
                    $this_seq = $_POST['pic_seq_select' . $i]; //get new seqence
                    $this_pic_id = $_POST['pic_seq_id' . $i]; //get picutre ID the new seq is applicable to
                    $this_pic_changed = $_POST['pic_seq_changed' . $i]; //get changed status ("yes" or "no")

                    if (isset($this_seq, $this_pic_id) && $this_pic_changed == 'yes') {
                        $thatquery = 'UPDATE `pictures` SET `last_modified`=NOW(), `seq` = :v1 WHERE `id` = :v2';
                        $params['v1']['value'] = (int) $this_seq;
                        $params['v1']['data_type'] = 'integer';
                        $params['v2']['value'] = (int) $this_pic_id;
                        $params['v2']['data_type'] = 'integer';
                        $dbc->paramQuery($thatquery, $params);
                    }
                }
                unset($params);
            }
        }
        // mp3 update start
        $mp3_count_check = $cache_record['mp3count'];

        if ($mp3_count_check > 0) {
            if (isset($_POST['mp3_seq_select1'])) { // check if in POST mode and in case any mp3 is attached (re-)update sequence value, providing it was changed - value of mp3_seq_change_X)
                for ($i = 1; $i <= $mp3_count_check; $i++) {
                    $this_seq = $_POST['mp3_seq_select' . $i]; //get new seqence
                    $this_mp3_id = $_POST['mp3_seq_id' . $i]; //get mp3 ID the new seq is applicable to
                    $this_mp3_changed = $_POST['mp3_seq_changed' . $i]; //get changed status ("yes" or "no")

                    if (isset($this_seq, $this_mp3_id) && $this_mp3_changed == 'yes') {
                        $thatquery = 'UPDATE `mp3` SET `last_modified`=NOW(), `seq` = :v1 WHERE `id` = :v2';
                        $params['v1']['value'] = (int) $this_seq;
                        $params['v1']['data_type'] = 'integer';
                        $params['v2']['value'] = (int) $this_mp3_id;
                        $params['v2']['data_type'] = 'integer';
                        $dbc->paramQuery($thatquery, $params);
                    }
                }
                unset($params);
            }
        }
        // mp3 update end()

        if (! isset($_POST['size'])) {
            if ($cache_type == GeoCache::TYPE_VIRTUAL || $cache_type == GeoCache::TYPE_WEBCAM
                    || $cache_type == GeoCache::TYPE_EVENT) {
                $sel_size = GeoCache::SIZE_NONE;
            } else {
                $sel_size = $cache_record['size'];
            }
        } else {
            $sel_size = $_POST['size'] ?? $cache_record['size'];

            if ($cache_type == GeoCache::TYPE_VIRTUAL || $cache_type == GeoCache::TYPE_WEBCAM || $cache_type == GeoCache::TYPE_EVENT) {
                $sel_size = GeoCache::SIZE_NONE;
            }
        }
        $cache_hidden_day = $_POST['hidden_day'] ?? date('d', strtotime($cache_record['date_hidden']));
        $cache_hidden_month = $_POST['hidden_month'] ?? date('m', strtotime($cache_record['date_hidden']));
        $cache_hidden_year = $_POST['hidden_year'] ?? date('Y', strtotime($cache_record['date_hidden']));

        if (is_null($cache_record['date_activate']) || $cache_record['date_activate'] == 0) {
            $cache_activate_day = $_POST['activate_day'] ?? date('d');
            $cache_activate_month = $_POST['activate_month'] ?? date('m');
            $cache_activate_year = $_POST['activate_year'] ?? date('Y');
            $cache_activate_hour = $_POST['activate_hour'] ?? date('H');
        } else {
            $cache_activate_day = $_POST['activate_day'] ?? date('d', strtotime($cache_record['date_activate']));
            $cache_activate_month = $_POST['activate_month'] ?? date('m', strtotime($cache_record['date_activate']));
            $cache_activate_year = $_POST['activate_year'] ?? date('Y', strtotime($cache_record['date_activate']));
            $cache_activate_hour = $_POST['activate_hour'] ?? date('H', strtotime($cache_record['date_activate']));
        }

        $cache_difficulty = $_POST['difficulty'] ?? $cache_record['difficulty'];
        $cache_terrain = $_POST['terrain'] ?? $cache_record['terrain'];
        $cache_country = $_POST['country'] ?? $cache_record['country'];
        $cache_region = $_POST['region'] ?? $cache_record['region'];
        $show_all_countries = $_POST['show_all_countries'] ?? 0;
        $status = $_POST['status'] ?? $cache_record['status'];
        $status_old = $cache_record['status'];
        $search_time = $_POST['search_time'] ?? $cache_record['search_time'];
        $way_length = $_POST['way_length'] ?? $cache_record['way_length'];

        if ($status_old == GeoCache::STATUS_NOTYETAVAILABLE
                && $status == GeoCache::STATUS_NOTYETAVAILABLE) {
            if (isset($_POST['publish'])) {
                $publish = $_POST['publish'];

                if (! ($publish == 'now' || $publish == 'later' || $publish == 'notnow')) {
                    // somebody messed up the POST-data, so we do not publish the cache, since he isn't published right now (status=5)
                    $publish = 'notnow';
                }
            } else {
                if (is_null($cache_record['date_activate']) || $cache_record['date_activate'] == 0) {
                    $publish = 'notnow';
                } else {
                    $publish = 'later';
                }
            }
        } else {
            $publish = $_POST['publish'] ?? 'now';

            if (! ($publish == 'now' || $publish == 'later' || $publish == 'notnow')) {
                // somebody messed up the POST-data, so the cache has to be published (status<5)
                $publish = 'now';
            }
        }

        $search_time = mb_ereg_replace(',', '.', $search_time);
        $way_length = mb_ereg_replace(',', '.', $way_length);

        if (mb_strpos($search_time, ':') == mb_strlen($search_time) - 3) {
            $st_hours = mb_substr($search_time, 0, mb_strpos($search_time, ':'));
            $st_minutes = mb_substr($search_time, mb_strlen($st_hours) + 1);

            if (is_numeric($st_hours) && is_numeric($st_minutes)) {
                if (($st_minutes >= 0) && ($st_minutes < 60)) {
                    $search_time = $st_hours + $st_minutes / 60;
                }
            }
        }

        // if cache has been placed after 18.06.2010, do not allow passwords in traditional caches.
        $allow_pw = ($cache_type == 2 && 1276884198 < (strtotime($cache_record['date_created']))) ? 0 : 1;

        if ($allow_pw) {
            $log_pw = isset($_POST['log_pw']) ? mb_substr($_POST['log_pw'], 0, 20) : $cache_record['logpw'];

            // don't display log password for admins
            if ($cache_record['user_id'] == $loggedUser->getUserId()) {
                tpl_set_var('logpw_start', '');
                tpl_set_var('logpw_end', '');
            } else {
                tpl_set_var('logpw_start', '<!--');
                tpl_set_var('logpw_end', '-->');
            }
        } else {
            $log_pw = '';
            tpl_set_var('logpw_start', '<!--');
            tpl_set_var('logpw_end', '-->');
        }

        // name
        $name_not_ok = false;

        if (isset($_POST['name'])) {
            if ($_POST['name'] == '') {
                $name_not_ok = true;
            }
        }

        if (isset($_POST['latNS'])) {
            //get coords from post-form
            $coords_latNS = $_POST['latNS'];
            $coords_lonEW = $_POST['lonEW'];
            $coords_lat_h = $_POST['lat_h'];
            $coords_lon_h = $_POST['lon_h'];
            $coords_lat_min = $_POST['lat_min'];
            $coords_lon_min = $_POST['lon_min'];
        } else {
            //get coords from DB
            $coords_lon = $cache_record['longitude'];
            $coords_lat = $cache_record['latitude'];

            if ($coords_lon < 0) {
                $coords_lonEW = 'W';
                $coords_lon = -$coords_lon;
            } else {
                $coords_lonEW = 'E';
            }

            if ($coords_lat < 0) {
                $coords_latNS = 'S';
                $coords_lat = -$coords_lat;
            } else {
                $coords_latNS = 'N';
            }

            $coords_lat_h = floor($coords_lat);
            $coords_lon_h = floor($coords_lon);

            $coords_lat_min = sprintf('%02.3f', round(($coords_lat - $coords_lat_h) * 60, 3));
            $coords_lon_min = sprintf('%02.3f', round(($coords_lon - $coords_lon_h) * 60, 3));
        }

        //here we validate the data
        //coords
        $lon_not_ok = false;

        if (! mb_ereg_match('^[0-9]{1,3}$', $coords_lon_h)) {
            $lon_not_ok = true;
        } else {
            $lon_not_ok = (($coords_lon_h >= 0) && ($coords_lon_h < 180)) ? false : true;
        }

        if (is_numeric($coords_lon_min)) {
            // important: use here |=
            $lon_not_ok |= (($coords_lon_min >= 0) && ($coords_lon_min < 60)) ? false : true;
        } else {
            $lon_not_ok = true;
        }

        //same with lat
        $lat_not_ok = false;

        if (! mb_ereg_match('^[0-9]{1,3}$', $coords_lat_h)) {
            $lat_not_ok = true;
        } else {
            $lat_not_ok = (($coords_lat_h >= 0) && ($coords_lat_h < 180)) ? false : true;
        }

        if (is_numeric($coords_lat_min)) {
            // important: use here |=
            $lat_not_ok |= (($coords_lat_min >= 0) && ($coords_lat_min < 60)) ? false : true;
        } else {
            $lat_not_ok = true;
        }

        //check effort
        $time_not_ok = true;
        tpl_set_var('effort_message', '');

        if (is_numeric($search_time) || ($search_time == '')) {
            $time_not_ok = false;
        }

        if ($time_not_ok) {
            tpl_set_var('effort_message', $time_not_ok_message);
        }
        $way_length_not_ok = true;

        if (is_numeric($way_length) || ($way_length == '')) {
            $way_length_not_ok = false;
        }

        if ($way_length_not_ok) {
            tpl_set_var('effort_message', $way_length_not_ok_message);
        }

        //check hidden_since
        $hidden_date_not_ok = true;

        if (is_numeric($cache_hidden_day) && is_numeric($cache_hidden_month) && is_numeric($cache_hidden_year)) {
            $hidden_date_not_ok = (checkdate($cache_hidden_month, $cache_hidden_day, $cache_hidden_year) == false);
        }

        //check date_activate
        if ($status == 5) {
            $activate_date_not_ok = true;

            if (is_numeric($cache_activate_day) && is_numeric($cache_activate_month) && is_numeric($cache_activate_year) && is_numeric($cache_activate_hour)) {
                $activate_date_not_ok = ((checkdate($cache_activate_month, $cache_activate_day, $cache_activate_year) == false) || $cache_activate_hour < 0 || $cache_activate_hour > 23);
            }
        } else {
            $activate_date_not_ok = false;
        }

        //check status and publish options
        if (($status == GeoCache::STATUS_NOTYETAVAILABLE && $publish == 'now')
                || ($status != GeoCache::STATUS_NOTYETAVAILABLE && ($publish == 'later' || $publish == 'notnow'))) {
            tpl_set_var('status_message', $status_message);
            $status_not_ok = true;
        } else {
            tpl_set_var('status_message', '');
            $status_not_ok = false;
        }

        //check cache size
        $size_not_ok = false;

        if ($sel_size != GeoCache::SIZE_NONE
                && ($cache_type == GeoCache::TYPE_VIRTUAL
                || $cache_type == GeoCache::TYPE_WEBCAM
                || $cache_type == GeoCache::TYPE_EVENT)) {
            $size_not_ok = true;
        }

        // check if the user haven't changed type to 'without container'
        if (isset($_POST['type'])) {
            if ((($_POST['type'] == GeoCache::TYPE_OTHERTYPE && $cache_record['type'] != GeoCache::TYPE_OTHERTYPE) || ($_POST['type'] == GeoCache::TYPE_TRADITIONAL) || ($_POST['type'] == GeoCache::TYPE_MULTICACHE) || ($_POST['type'] == GeoCache::TYPE_QUIZ) || ($_POST['type'] == GeoCache::TYPE_MOVING)) && $sel_size == GeoCache::SIZE_NONE) {
                $size_not_ok = true;
            }
        }

        // if there is already a cache without container, let it stay this way
        if ($cache_record['type'] == GeoCache::TYPE_OTHERTYPE && $cache_record['size'] == GeoCache::SIZE_NONE) {
            tpl_set_var('other_nobox', 'true');
        } else {
            tpl_set_var('other_nobox', 'false');
        }

        // foreign waypoints
        $all_wp_ok = true;

        foreach (['gc', 'nc', 'tc', 'ge'] as $wpType) {
            $wpVar = 'wp_' . $wpType;
            $wpMessageVar = 'wp_' . $wpType . '_message';

            ${$wpVar} = $_POST[$wpVar] ?? $cache_record[$wpVar];

            if (${$wpVar} == '') {
                $wpOk = true;
            } else {
                $validatedCode = Validator::xxWaypoint($wpType, ${$wpVar});
                $wpOk = ($validatedCode !== false);

                if ($wpOk) {
                    ${$wpVar} = $validatedCode;
                }
            }

            if ($wpOk) {
                tpl_set_var($wpMessageVar, '');
            } else {
                tpl_set_var(
                    $wpMessageVar,
                    '<img src="tpl/stdstyle/images/misc/32x32-impressum.png" class="icon32" alt=""  />'
                    . '&nbsp;&nbsp;<span class="errormsg">'
                    . tr("invalid_wp_{$wpType}")
                    . '</span>'
                );
                $all_wp_ok = false;
            }
        }
        unset($wpVar, $wpMessageVar, $wpOk);

        // cache-attributes
        if (isset($_POST['cache_attribs'])) {
            $cache_attribs = mb_split(';', $_POST['cache_attribs']);
        } else {
            // get attribs for this cache from db
            $thatquery = 'SELECT `attrib_id` FROM `caches_attributes` WHERE `cache_id`=:v1';
            $params['v1']['value'] = (int) $cache_id;
            $params['v1']['data_type'] = 'integer';

            $s = $dbc->paramQuery($thatquery, $params);
            unset($params);
            $cache_attribs_count = $dbc->rowCount($s);

            if ($cache_attribs_count > 0) {
                $cache_attribs_all = $dbc->dbResultFetchAll($s);

                unset($cache_attribs);

                for ($i = 0; $i < $cache_attribs_count; $i++) {
                    $record = $cache_attribs_all[$i];
                    $cache_attribs[] = $record['attrib_id'];
                }
                unset($record);
            } else {
                $cache_attribs = [];
            }
        }

        $errors_occured
            = $hidden_date_not_ok || $lat_not_ok || $lon_not_ok || $name_not_ok
            || $time_not_ok || $way_length_not_ok || $size_not_ok || $activate_date_not_ok
            || $status_not_ok || ! $all_wp_ok;

        //try to save to DB?
        if (isset($_POST['submit'])) {
            //prevent un archiving cache by non-admin users
            if ($status_old == GeoCache::STATUS_ARCHIVED
                && ! $loggedUser->hasOcTeamRole()
                && $status != GeoCache::STATUS_ARCHIVED) {
                $status_not_ok = true;
            }

            //all validations ok?
            if (! $errors_occured) {
                $cache_lat = $coords_lat_h + round($coords_lat_min, 3) / 60;

                if ($coords_latNS == 'S') {
                    $cache_lat = -$cache_lat;
                }

                $cache_lon = $coords_lon_h + round($coords_lon_min, 3) / 60;

                if ($coords_lonEW == 'W') {
                    $cache_lon = -$cache_lon;
                }

                if ($publish == 'now') {
                    $activation_date = 'NULL';
                } elseif ($publish == 'later') {
                    $status = 5;
                    $activation_date = "'" . XDb::xEscape(date('Y-m-d H:i:s', mktime($cache_activate_hour, 0, 0, $cache_activate_month, $cache_activate_day, $cache_activate_year))) . "'";
                } elseif ($publish == 'notnow') {
                    $status = 5;
                    $activation_date = 'NULL';
                } else { // should never happen
                    $activation_date = 'NULL';
                }

                //save to DB
                XDb::xSql(
                    "UPDATE `caches`
                             SET `last_modified`=NOW(), `name`=?, `longitude`=?,
                                 `latitude`=?, `type`=?, `date_hidden`=?,
                                 `country`=?, `size`=?, `difficulty`=?, `terrain`=?,
                                 `status`=?, `search_time`=?, `way_length`=?,
                                 `logpw`=?, `wp_gc`=?, `wp_nc`=?, `wp_ge`=?,
                                 `wp_tc`=?,`date_activate` = {$activation_date}
                             WHERE `cache_id`=?",
                    $cache_name,
                    $cache_lon,
                    $cache_lat,
                    $cache_type,
                    date('Y-m-d', mktime(0, 0, 0, $cache_hidden_month, $cache_hidden_day, $cache_hidden_year)),
                    $cache_country,
                    $sel_size,
                    $cache_difficulty,
                    $cache_terrain,
                    $status,
                    $search_time,
                    $way_length,
                    $log_pw,
                    $wp_gc,
                    $wp_nc,
                    $wp_ge,
                    $wp_tc,
                    $cache_id
                );

                if (I18n::isTranslationAvailable($cache_country)) {
                    $adm1 = tr($cache_country);
                } else {
                    Debug::errorLog("Unknown country translation: {$cache_country}");
                    $adm1 = $cache_country;
                }

                $code1 = $cache_country;

                // check if selected country has no districts, then use $default_region
                if ($cache_region == -1) {
                    $cache_region = '0';
                }

                if ($cache_region != '0') {
                    $code3 = $cache_region;
                    $adm3 = XDb::xMultiVariableQueryValue(
                        'SELECT `name` FROM `nuts_codes`
                                 WHERE `code`= :1',
                        0,
                        $cache_region
                    );
                } else {
                    $code3 = null;
                    $adm3 = null;
                }

                XDb::xSql(
                    'INSERT INTO cache_location (cache_id,adm1,adm3,code1,code3)
                                   VALUES (?,?,?,?,?)
                                   ON DUPLICATE KEY UPDATE adm1=?,adm3=?,code1=?,code3=?',
                    $cache_id,
                    $adm1,
                    $adm3,
                    $code1,
                    $code3,
                    $adm1,
                    $adm3,
                    $code1,
                    $code3
                );

                // delete old cache-attributes
                XDb::xSql('DELETE FROM `caches_attributes` WHERE `cache_id`=?', $cache_id);

                // insert new cache-attributes
                for ($i = 0; $i < count($cache_attribs); $i++) {
                    if (($cache_attribs[$i]) > 0) {
                        XDb::xSql(
                            'INSERT INTO `caches_attributes` (`cache_id`, `attrib_id`)
                                    VALUES(?, ?)',
                            $cache_id,
                            $cache_attribs[$i]
                        );
                    }
                }

                updateAltitudeIfNeeded($cache_record, $cache_id);

                $cache = GeoCache::fromCacheIdFactory($cache_id);

                //call eventhandler
                EventHandler::cacheEdit($cache);

                // if old status is not yet published and new status is published => notify-event
                if ($status_old == GeoCache::STATUS_NOTYETAVAILABLE && $status != GeoCache::STATUS_NOTYETAVAILABLE) {
                    GeoCache::touchCache($cache_id);

                    // send new cache event
                    EventHandler::cacheNew($cache);
                }

                //generate automatic logs
                if (($status_old == GeoCache::STATUS_READY
                    || $status_old == GeoCache::STATUS_ARCHIVED
                    || $status_old == GeoCache::STATUS_BLOCKED) && $status == GeoCache::STATUS_UNAVAILABLE) {
                    // generate automatic log about status cache
                    GeoCacheLog::newLog(
                        $cache->getCacheId(),
                        $loggedUser->getUserId(),
                        GeoCacheLog::LOGTYPE_TEMPORARYUNAVAILABLE,
                        tr(GeoCacheLog::translationKey4CacheStatus(GeoCache::STATUS_UNAVAILABLE))
                    );
                }

                if (($status_old == GeoCache::STATUS_READY
                    || $status_old == GeoCache::STATUS_UNAVAILABLE
                    || $status_old == GeoCache::STATUS_BLOCKED) && $status == GeoCache::STATUS_ARCHIVED) {
                    // generate automatic log about status cache
                    GeoCacheLog::newLog(
                        $cache->getCacheId(),
                        $loggedUser->getUserId(),
                        GeoCacheLog::LOGTYPE_ARCHIVED,
                        tr(GeoCacheLog::translationKey4CacheStatus(GeoCache::STATUS_ARCHIVED))
                    );
                }

                if (($status_old == GeoCache::STATUS_UNAVAILABLE
                    || $status_old == GeoCache::STATUS_ARCHIVED
                    || $status_old == GeoCache::STATUS_BLOCKED) && $status == GeoCache::STATUS_READY) {
                    // generate automatic log about status cache
                    GeoCacheLog::newLog(
                        $cache->getCacheId(),
                        $loggedUser->getUserId(),
                        GeoCacheLog::LOGTYPE_READYTOSEARCH,
                        tr(GeoCacheLog::translationKey4CacheStatus(GeoCache::STATUS_READY))
                    );
                }

                if (($status_old == GeoCache::STATUS_READY
                    || $status_old == GeoCache::STATUS_UNAVAILABLE
                    || $status_old == GeoCache::STATUS_ARCHIVED) && $status == GeoCache::STATUS_BLOCKED) {
                    // generate automatic log about status cache
                    GeoCacheLog::newLog(
                        $cache->getCacheId(),
                        $loggedUser->getUserId(),
                        GeoCacheLog::LOGTYPE_ADMINNOTE,
                        tr(GeoCacheLog::translationKey4CacheStatus(GeoCache::STATUS_BLOCKED))
                    );
                }

                //display cache-page
                tpl_redirect(ltrim($cache->getCacheUrl(), '/'));

                exit;
            }
        } elseif (isset($_POST['show_all_countries_submit'])) {
            $show_all_countries = 1;
        }

        //here we only set up the template variables
        //build countrylist
        $countriesoptions = '';

        //check if selected country is in list_default
        if ($show_all_countries == 0) {
            if (! in_array($cache_country, Countries::getCountriesList(true))) {
                $show_all_countries = 1;
            }
        }

        $countryList = Countries::getCountriesList($show_all_countries == 0);

        $sortedCountries = [];

        foreach ($countryList as $countryCode) {
            $sortedCountries[] = [
                'code' => $countryCode,
                'name' => tr($countryCode),
            ];
        }

        $currentLocale = Languages::getCurrentLocale();

        if (function_exists('collator_create') && function_exists('collator_compare')) {
            $collator = collator_create($currentLocale);
            usort($sortedCountries, fn ($a, $b) => collator_compare($collator, $a['name'], $b['name']));
        } else {
            Debug::errorLog('Intl extension (PHP intl) is not enabled. Sorting by locale may not be accurate.');
            usort($sortedCountries, fn ($a, $b) => strcmp($a['name'], $b['name']));
        }

        $countriesoptions = '';

        foreach ($sortedCountries as $country) {
            $selected = $country['code'] == $cache_country ? "selected='selected'" : '';
            $countriesoptions .= "<option value='{$country['code']}' {$selected}>{$country['name']}</option>\n";
        }

        tpl_set_var('countryoptions', $countriesoptions);
        tpl_set_var('cache_region', $cache_region);

        // cache-attributes
        $cache_attrib_list = '';
        $cache_attrib_array = '';
        $cache_attribs_string = '';

        $rs = XDb::xSql('SELECT `id`, `text_long`, `icon_undef`, `icon_large` FROM `cache_attrib`
                                 WHERE `language`= ? ORDER BY `category`, `id`', I18n::getCurrentLang());

        while ($record = XDb::xFetchArray($rs)) {
            $line = $cache_attrib_pic;
            $line = mb_ereg_replace('{attrib_id}', $record['id'], $line);
            $line = mb_ereg_replace('{attrib_text}', $record['text_long'], $line);

            if (in_array($record['id'], $cache_attribs)) {
                $line = mb_ereg_replace('{attrib_pic}', $record['icon_large'], $line);
            } else {
                $line = mb_ereg_replace('{attrib_pic}', $record['icon_undef'], $line);
            }
            $cache_attrib_list .= $line;

            $line = $cache_attrib_js;
            $line = mb_ereg_replace('{id}', $record['id'], $line);

            if (in_array($record['id'], $cache_attribs)) {
                $line = mb_ereg_replace('{selected}', 1, $line);
            } else {
                $line = mb_ereg_replace('{selected}', 0, $line);
            }
            $line = mb_ereg_replace('{img_undef}', $record['icon_undef'], $line);
            $line = mb_ereg_replace('{img_large}', $record['icon_large'], $line);

            if ($cache_attrib_array != '') {
                $cache_attrib_array .= ',';
            }
            $cache_attrib_array .= $line;

            if (in_array($record['id'], $cache_attribs)) {
                if ($cache_attribs_string != '') {
                    $cache_attribs_string .= ';';
                }
                $cache_attribs_string .= $record['id'];
            }
        }
        tpl_set_var('cache_attrib_list', $cache_attrib_list);
        tpl_set_var('jsattributes_array', $cache_attrib_array);
        tpl_set_var('cache_attribs', $cache_attribs_string);

        //difficulty
        $difficulty_options = '';

        for ($i = 2; $i <= 10; $i++) {
            if ($cache_difficulty == $i) {
                $difficulty_options .= '<option value="' . $i . '" selected="selected">' . $i / 2 . '</option>';
            } else {
                $difficulty_options .= '<option value="' . $i . '">' . $i / 2 . '</option>';
            }
            $difficulty_options .= "\n";
        }
        tpl_set_var('difficultyoptions', $difficulty_options);

        //build terrain options
        $terrain_options = '';

        for ($i = 2; $i <= 10; $i++) {
            if ($cache_terrain == $i) {
                $terrain_options .= '<option value="' . $i . '" selected="selected">' . $i / 2 . '</option>';
            } else {
                $terrain_options .= '<option value="' . $i . '">' . $i / 2 . '</option>';
            }
            $terrain_options .= "\n";
        }
        tpl_set_var('terrainoptions', $terrain_options);

        $cacheLimitByTypePerUser = GeoCache::getUserActiveCachesCountByType($loggedUser->getUserId());

        //build typeoptions
        $types = '';

        foreach (GeoCacheCommons::CacheTypesArray() as $type) {
            // blockforbidden cache types
            if (($type != $cache_type) && in_array($type, OcConfig::getNoNewCacheOfTypesArray())
                && ! $loggedUser->hasOcTeamRole()) {
                continue;
            }

            if (isset($config['cacheLimitByTypePerUser'][$cache_type])
                && $cacheLimitByTypePerUser[$cache_type] >= $config['cacheLimitByTypePerUser'][$cache_type]
                && ! $loggedUser->hasOcTeamRole()) {
                continue;
            }

            if (isset($cacheLimitByTypePerUser[$type], $config['cacheLimitByTypePerUser'][$type])
                && $cacheLimitByTypePerUser[$type] >= $config['cacheLimitByTypePerUser'][$type]
                && ! $loggedUser->hasOcTeamRole()) {
                continue;
            }

            if ($type == $cache_type) {
                $types .= '<option value="' . $type . '" selected="selected">'
                    . htmlspecialchars(tr(GeoCacheCommons::CacheTypeTranslationKey($type)), ENT_COMPAT, 'UTF-8') . '</option>';
            } else {
                $types .= '<option value="' . $type . '">'
                    . htmlspecialchars(tr(GeoCacheCommons::CacheTypeTranslationKey($type)), ENT_COMPAT, 'UTF-8') . '</option>';
            }
        }
        tpl_set_var('typeoptions', $types);

        //build sizeoptions
        $sizes = '';

        foreach (GeoCache::CacheSizesArray() as $size) {
            // blockforbidden cache sizes
            if ($size != $sel_size
                && ! in_array($size, OcConfig::getEnabledCacheSizesArray())
            ) {
                continue;
            }

            if ($size == GeoCache::SIZE_NONE && $sel_size != GeoCache::SIZE_NONE) {
                continue;
            }

            if ($size == $sel_size) {
                $sizes .= '<option value="' . $size . '" selected="selected">' . tr(GeoCache::CacheSizeTranslationKey($size)) . '</option>';
            } else {
                $sizes .= '<option value="' . $size . '">' . tr(GeoCache::CacheSizeTranslationKey($size)) . '</option>';
            }
        }
        tpl_set_var('sizeoptions', $sizes);

        // Display cache descriptions list
        $descList = GeoCache::getDescriptions($cache_id);
        $cache_descs = '';

        foreach ($descList as $descId => $descLang) {
            if (count($descList) > 1) {
                $remove_url = 'removedesc.php?cacheid=' . urlencode($cache_id) . '&desclang=' . urlencode($descLang);
                $removedesc = '&nbsp;&nbsp;<img src="images/log/16x16-trash.png" border="0" align="middle" class="icon16" alt="">'
                    . ' [<a href="' . htmlspecialchars($remove_url, ENT_COMPAT, 'UTF-8') . '" onclick="return check_if_proceed();">' . tr('delete') . '</a>]';
            } else {
                $removedesc = '';
            }

            $edit_url = '/CacheDesc/edit/' . $geocache->getWaypointId() . "/{$descLang}";
            $cache_descs
                .= '<tr>
                            <td colspan="2">
                                <img src="images/flags/' . strtolower($descLang) . '.gif" class="icon16" alt="">
                                    &nbsp;' . htmlspecialchars(Languages::languageNameFromCode($descLang, I18n::getCurrentLang()), ENT_COMPAT, 'UTF-8') . '&nbsp;&nbsp;
                                <img src="images/actions/edit-16.png" border="0" align="middle" alt="">
                                [<a href="' . htmlspecialchars($edit_url, ENT_COMPAT, 'UTF-8') . '" onclick="return check_if_proceed();">' . tr('edit') . '</a>]'
                        . $removedesc
                    . '</td>
                         </tr>';
        }
        tpl_set_var('cache_descs', $cache_descs);

        //Status
        $statusoptions = '';

        if ((($status_old == GeoCache::STATUS_ARCHIVED || $status_old == GeoCache::STATUS_BLOCKED)
            && ! $loggedUser->hasOcTeamRole()) || $status_old == GeoCache::STATUS_WAITAPPROVERS) {
            $disablestatusoption = ' disabled';
        } else {
            $disablestatusoption = '';
        }
        tpl_set_var('disablestatusoption', $disablestatusoption);

        foreach (GeoCache::CacheStatusArray() as $tmpstatus) {
            //hide id 4 => hidden by approvers, hide id 5 if it is not the current status
            if (($tmpstatus != GeoCache::STATUS_WAITAPPROVERS || $status_old == GeoCache::STATUS_WAITAPPROVERS)
                && ($tmpstatus != GeoCache::STATUS_NOTYETAVAILABLE || $status_old == GeoCache::STATUS_NOTYETAVAILABLE)
                && ($tmpstatus != GeoCache::STATUS_BLOCKED || $loggedUser->hasOcTeamRole())) {
                if ($tmpstatus == $status) {
                    $statusoptions .= '<option value="' . $tmpstatus . '" selected="selected">' . tr(GeoCache::CacheStatusTranslationKey($tmpstatus)) . '</option>';
                } else {
                    $statusoptions .= '<option value="' . $tmpstatus . '">' . tr(GeoCache::CacheStatusTranslationKey($tmpstatus)) . '</option>';
                }
            }
        }
        tpl_set_var('statusoptions', $statusoptions);

        // show activation form?
        if ($status_old == GeoCache::STATUS_NOTYETAVAILABLE) { // status = not yet published
            $tmp = $activation_form;
            $tmp = mb_ereg_replace('{activate_day}', htmlspecialchars($cache_activate_day, ENT_COMPAT, 'UTF-8'), $tmp);
            $tmp = mb_ereg_replace('{activate_month}', htmlspecialchars($cache_activate_month, ENT_COMPAT, 'UTF-8'), $tmp);
            $tmp = mb_ereg_replace('{activate_year}', htmlspecialchars($cache_activate_year, ENT_COMPAT, 'UTF-8'), $tmp);
            $tmp = mb_ereg_replace('{publish_now_checked}', ($publish == 'now') ? 'checked' : '', $tmp);
            $tmp = mb_ereg_replace('{publish_later_checked}', ($publish == 'later') ? 'checked' : '', $tmp);
            $tmp = mb_ereg_replace('{publish_notnow_checked}', ($publish == 'notnow') ? 'checked' : '', $tmp);
            $activation_hours = '';

            for ($i = 0; $i <= 23; $i++) {
                if ($cache_activate_hour == $i) {
                    $activation_hours .= '<option value="' . $i . '" selected="selected">' . $i . ':00</options>';
                } else {
                    $activation_hours .= '<option value="' . $i . '">' . $i . ':00</options>';
                }
                $activation_hours .= "\n";
            }
            $tmp = mb_ereg_replace('{activation_hours}', $activation_hours, $tmp);

            if ($activate_date_not_ok) {
                $tmp = mb_ereg_replace('{activate_on_message}', $date_not_ok_message, $tmp);
            } else {
                $tmp = mb_ereg_replace('{activate_on_message}', tr('newcacheDateFormat'), $tmp);
            }

            tpl_set_var('activation_form', $tmp);
        } else {
            tpl_set_var('activation_form', '');
        }

        // prepare the list of pictures for thich geocache
        $picList = OcPicture::getListForParent(OcPicture::TYPE_CACHE, $cache_id);
        $view->setVar('picList', $picList);
        $view->setVar('picParentId', $cache_id);
        $view->setVar('picParentType', OcPicture::TYPE_CACHE);

        // prepare the upload model for pictures upload
        /** @var UploadModel */
        $uploadModel = UploadModel::PicUploadFactory(OcPicture::TYPE_CACHE, $cache_id);
        $view->setVar('picsUploadModelJson', $uploadModel->getJsonParams());

        //MP3 files only for type of cache:
        if ($cache_record['type'] == GeoCache::TYPE_OTHERTYPE
                || $cache_record['type'] == GeoCache::TYPE_MULTICACHE
                || $cache_record['type'] == GeoCache::TYPE_QUIZ
        ) {
            if ($cache_record['mp3count'] > 0) {
                $mp3files = '';
                $thatquery = 'SELECT `id`, `url`, `title`, `uuid`, `seq` FROM `mp3` WHERE `object_id`=:v1 AND `object_type`=2 ORDER BY seq, date_created';
                $params['v1']['value'] = (int) $cache_id;
                $params['v1']['data_type'] = 'integer';

                $s = $dbc->paramQuery($thatquery, $params);
                $mp3_count = $dbc->rowCount($s);
                $mp3_all = $dbc->dbResultFetchAll($s);

                $thatquery = 'SELECT `seq` FROM `mp3` WHERE `object_id`=:v1 AND `object_type`=2 ORDER BY `seq` DESC'; //get highest seq number for this cache
                $s = $dbc->paramQuery($thatquery, $params); //params are same as a few lines above
                $max_seq_record = $dbc->dbResultFetch($s);
                unset($params);  //clear to avoid overlaping on next paramQuery (if any))

                $max_seq_number = ($max_seq_record['seq'] ?? 0);

                if ($max_seq_number < $mp3_count) {
                    $max_seq_number = $mp3_count;
                }
                tpl_set_var('def_seq_m', $max_seq_number + 1); // set default seq for mp3 to be added (if link is click) - this line updated link to newmp3.php)  )

                for ($i = 0; $i < $mp3_count; $i++) {
                    $tmpline1 = $mp3line;
                    $mp3_record = $mp3_all[$i];
                    $tmpline1 = mb_ereg_replace('{seq_drop_mp3}', build_drop_seq($i + 1, $mp3_record['seq'], $max_seq_number, $mp3_record['id'], 'mp3'), $tmpline1);
                    $tmpline1 = mb_ereg_replace('{link}', htmlspecialchars($mp3_record['url'], ENT_COMPAT, 'UTF-8'), $tmpline1);
                    $tmpline1 = mb_ereg_replace('{title}', htmlspecialchars($mp3_record['title'], ENT_COMPAT, 'UTF-8'), $tmpline1);
                    $tmpline1 = mb_ereg_replace('{uuid}', htmlspecialchars($mp3_record['uuid'], ENT_COMPAT, 'UTF-8'), $tmpline1);
                    $mp3files .= $tmpline1;
                }

                $mp3files = mb_ereg_replace('{lines}', $mp3files, $mp3lines);
                tpl_set_var('mp3files', $mp3files);
                tpl_set_var('hidemp3_start', '');
                tpl_set_var('hidemp3_end', '');
            } else {
                tpl_set_var('mp3files', $nomp3);
                tpl_set_var('def_seq_m', 1); //set default sequence to 1 for add mp3 link (in case there is no mp3 at all yet))
                tpl_set_var('hidemp3_start', '');
                tpl_set_var('hidemp3_end', '');
            }
        } else {
            tpl_set_var('mp3files', '<br>');
            tpl_set_var('hidemp3_start', '<!--');
            tpl_set_var('hidemp3_end', '-->');
        }

        //Add Waypoint
        $lang_db = I18n::getLangForDbTranslations('waypoint_type');

        $cache_type = $cache_record['type'];

        if ($cache_type != GeoCache::TYPE_MOVING) {
            tpl_set_var('waypoints_start', '');
            tpl_set_var('waypoints_end', '');
            $eLang = XDb::xEscape($lang_db);
            $wp_rs = XDb::xSql(
                "SELECT `wp_id`, `type`, `longitude`, `latitude`,  `desc`, `status`, `stage`,
                                `waypoint_type`.`{$eLang}` wp_type, waypoint_type.icon wp_icon
                         FROM `waypoints` INNER JOIN waypoint_type ON (waypoints.type = waypoint_type.id)
                         WHERE `cache_id`=? ORDER BY `stage`,`wp_id`",
                $cache_id
            );

            if (XDb::xNumRows($wp_rs) != 0) {
                $waypoints = '<table id="gradient" cellpadding="5" width="97%" border="1" style="border-collapse: collapse; font-size: 11px; line-height: 1.6em; color: #000000; ">';
                $waypoints .= '<tr>';

                if ($cache_type == GeoCache::TYPE_OTHERTYPE
                        || $cache_type == GeoCache::TYPE_MULTICACHE
                        || $cache_type == GeoCache::TYPE_QUIZ) {
                    $waypoints .= '<th align="center" valign="middle" width="30"><b>' . tr('stage_wp') . '</b></th>';
                }

                $waypoints .= '<th width="32"><b>' . tr('symbol_wp') . '</b></th><th width="32"><b>' . tr('type_wp') . '</b></th><th width="32"><b>' . tr('coordinates_wp') . '</b></th><th><b>' . tr('describe_wp') . '</b></th><th width="22"><b>' . tr('status_wp') . '</b></th><th width="22"><b>' . tr('edit') . '</b></th><th width="22"><b>' . tr('delete') . '</b></th></tr>';

                while ($wp_record = XDb::xFetchArray($wp_rs)) {
                    $tmpline1 = $wpline;

                    $coords_lat = mb_ereg_replace(' ', '&nbsp;', htmlspecialchars(Coordinates::donNotUse_latToDegreeStr($wp_record['latitude']), ENT_COMPAT, 'UTF-8'));
                    $coords_lon = mb_ereg_replace(' ', '&nbsp;', htmlspecialchars(Coordinates::donNotUse_lonToDegreeStr($wp_record['longitude']), ENT_COMPAT, 'UTF-8'));

                    $tmpline1 = mb_ereg_replace('{wp_icon}', htmlspecialchars($wp_record['wp_icon'], ENT_COMPAT, 'UTF-8'), $tmpline1);
                    $tmpline1 = mb_ereg_replace('{type}', htmlspecialchars($wp_record['wp_type'], ENT_COMPAT, 'UTF-8'), $tmpline1);
                    $tmpline1 = mb_ereg_replace('{lon}', $coords_lon, $tmpline1);
                    $tmpline1 = mb_ereg_replace('{lat}', $coords_lat, $tmpline1);
                    $tmpline1 = mb_ereg_replace('{desc}', nl2br($wp_record['desc']), $tmpline1);
                    $tmpline1 = mb_ereg_replace('{wpid}', $wp_record['wp_id'], $tmpline1);

                    if ($cache_type == GeoCache::TYPE_OTHERTYPE
                            || $cache_type == GeoCache::TYPE_MULTICACHE
                            || $cache_type == GeoCache::TYPE_QUIZ) {
                        $tmpline1 = mb_ereg_replace('{stagehide_end}', '', $tmpline1);
                        $tmpline1 = mb_ereg_replace('{stagehide_start}', '', $tmpline1);

                        if ($wp_record['stage'] == 0) {
                            $tmpline1 = mb_ereg_replace('{number}', '', $tmpline1);
                        } else {
                            $tmpline1 = mb_ereg_replace('{number}', $wp_record['stage'], $tmpline1);
                        }
                    } else {
                        $tmpline1 = mb_ereg_replace('{stagehide_end}', '-->', $tmpline1);
                        $tmpline1 = mb_ereg_replace('{stagehide_start}', '<!--', $tmpline1);
                    }

                    if ($wp_record['status'] == GeoCache::STATUS_READY) {
                        $status_icon = 'images/free_icons/accept.png';
                    }

                    if ($wp_record['status'] == GeoCache::STATUS_UNAVAILABLE) {
                        $status_icon = 'images/free_icons/error.png';
                    }

                    if ($wp_record['status'] == GeoCache::STATUS_ARCHIVED) {
                        $status_icon = 'images/free_icons/stop.png';
                    }
                    $tmpline1 = mb_ereg_replace('{status}', $status_icon, $tmpline1);
                    $waypoints .= $tmpline1;
                }//while row
                $waypoints .= '</table>';
                $waypoints .= '<br><img src="images/free_icons/accept.png" class="icon16" alt="">&nbsp;<span>' . tr('wp_status1') . '</span>';
                $waypoints .= '<br><img src="images/free_icons/error.png" class="icon16" alt="">&nbsp;<span>' . tr('wp_status2') . '</span>';
                $waypoints .= '<br><img src="images/free_icons/stop.png" class="icon16" alt="">&nbsp;<span>' . tr('wp_status3') . '</span>';
                tpl_set_var('cache_wp_list', $waypoints);
            } else {
                tpl_set_var('cache_wp_list', $nowp);
            }
            XDb::xFreeResults($wp_rs);
        } else {
            tpl_set_var('waypoints_start', '<!--');
            tpl_set_var('waypoints_end', '-->');
        }

        tpl_set_var('cacheid', htmlspecialchars($cache_id, ENT_COMPAT, 'UTF-8'));
        tpl_set_var('name', htmlspecialchars($cache_name, ENT_COMPAT, 'UTF-8'));

        tpl_set_var('date_day', htmlspecialchars($cache_hidden_day, ENT_COMPAT, 'UTF-8'));
        tpl_set_var('date_month', htmlspecialchars($cache_hidden_month, ENT_COMPAT, 'UTF-8'));
        tpl_set_var('date_year', htmlspecialchars($cache_hidden_year, ENT_COMPAT, 'UTF-8'));

        tpl_set_var('selLatN', ($coords_latNS == 'N') ? ' selected="selected"' : '');
        tpl_set_var('selLatS', ($coords_latNS == 'S') ? ' selected="selected"' : '');
        tpl_set_var('selLonE', ($coords_lonEW == 'E') ? ' selected="selected"' : '');
        tpl_set_var('selLonW', ($coords_lonEW == 'W') ? ' selected="selected"' : '');
        tpl_set_var('lat_h', htmlspecialchars($coords_lat_h, ENT_COMPAT, 'UTF-8'));
        tpl_set_var('lat_min', htmlspecialchars($coords_lat_min, ENT_COMPAT, 'UTF-8'));
        tpl_set_var('lon_h', htmlspecialchars($coords_lon_h, ENT_COMPAT, 'UTF-8'));
        tpl_set_var('lon_min', htmlspecialchars($coords_lon_min, ENT_COMPAT, 'UTF-8'));

        tpl_set_var('name_message', ($name_not_ok == true) ? $name_not_ok_message : '');
        tpl_set_var('lon_message', ($lon_not_ok == true) ? $error_coords_not_ok : '');
        tpl_set_var('lat_message', ($lat_not_ok == true) ? $error_coords_not_ok : '');
        tpl_set_var('date_message', ($hidden_date_not_ok == true) ? $date_not_ok_message : '');
        tpl_set_var('size_message', ($size_not_ok == true) ? $size_not_ok_message : '');
        tpl_set_var('limits_promixity', $config['oc']['limits']['proximity']);
        tpl_set_var('short_sitename', OcConfig::getSiteShortName());

        if ($errors_occured) {
            tpl_set_var('general_message', $error_general);
        } else {
            tpl_set_var('general_message', '');
        }
        tpl_set_var('cacheid_urlencode', htmlspecialchars(urlencode($cache_id), ENT_COMPAT, 'UTF-8'));
        tpl_set_var('show_all_countries', $show_all_countries);
        tpl_set_var('show_all_countries_submit', ($show_all_countries == 0) ? $all_countries_submit : '');
        $st_hours = floor($search_time);
        $st_minutes = sprintf('%02d', round(($search_time - $st_hours) * 60, 1));
        tpl_set_var('search_time', $st_hours . ':' . $st_minutes);
        tpl_set_var('way_length', $way_length);
        tpl_set_var('log_pw', htmlspecialchars($log_pw, ENT_COMPAT, 'UTF-8'));
        tpl_set_var('wp_gc', htmlspecialchars($wp_gc, ENT_COMPAT, 'UTF-8'));
        tpl_set_var('wp_nc', htmlspecialchars($wp_nc, ENT_COMPAT, 'UTF-8'));
        tpl_set_var('wp_tc', htmlspecialchars($wp_tc, ENT_COMPAT, 'UTF-8'));
        tpl_set_var('wp_ge', htmlspecialchars($wp_ge, ENT_COMPAT, 'UTF-8'));
        tpl_set_var('reset', tr('reset'));
        tpl_set_var('submit', $submit);
    }else{
        $view->redirect('/');
    }
}else{
    $view->redirect('/');
}

unset($dbc);

$view->loadJQuery();
//make the template and send it out
tpl_set_tplname('editcache');
tpl_set_var('language4js', I18n::getCurrentLang());
tpl_BuildTemplate();

/**
 * if coordinates were changed, update altitude
 * @param array $oldCacheRecord
 * @param int $cacheId
 */
function updateAltitudeIfNeeded($oldCacheRecord, $cacheId)
{
    $geoCache = GeoCache::fromCacheIdFactory($cacheId);
    $oldCoords = Coordinates::FromCoordsFactory($oldCacheRecord['latitude'], $oldCacheRecord['longitude']);
    $newCoords = $geoCache->getCoordinates();

    if ($newCoords && $oldCoords && ! $newCoords->areSameAs($oldCoords)) {
        //cords was changed - update altitude value
        $geoCache->updateAltitude();
    }
}
