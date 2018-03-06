<?php
use Utils\Database\XDb;
use lib\Objects\User\User;
use Utils\Text\Formatter;

// prepare the templates and include all neccessary
if (! isset($rootpath)) {
    $rootpath = '';
}
require_once ('./lib/common.inc.php');

$db = XDb::instance();
$description = "";
// user logged in?
if ($usr == false) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);
} else {
    tpl_set_var('desc_updated', '');
    tpl_set_var('displayGeoPathSection', displayGeoPatchSection('table'));
    if (isset($_POST['description'])) {
        $q = "UPDATE user SET description = :1 WHERE user_id=:2";
        $db->multiVariableQuery($q, strip_tags($_POST['description']), (int) $usr['userid']);
        tpl_set_var('desc_updated', "<font color='green'>" . tr('desc_updated') . "</font>");
    }

    $tplname = 'myprofile';
    $using_permantent_login_message = tr('no_auto_logout');

    // check user can set as Geocaching guide
    // Number of recommendations
    $nrecom = $db->multiVariableQueryValue("SELECT SUM(topratings) as nrecom FROM caches WHERE `caches`.`user_id`= :1", 0, $usr['userid']);
    if ($nrecom >= 20) {
        tpl_set_var('guide_start', '');
        tpl_set_var('guide_end', '');
    } else {
        tpl_set_var('guide_start', '<!--');
        tpl_set_var('guide_end', '-->');
    }

    $s = $db->multiVariableQuery("SELECT `description`, `guru`,`username`, `email`, `country`, `date_created`, `permanent_login_flag`, `power_trail_email`, `ozi_filips` FROM `user` WHERE `user_id`=:1 ", $usr['userid']);
    $record = $db->dbResultFetchOneRowOnly($s);
    $description = $record['description'];
    tpl_set_var('description', $description);

    if ($record['guru'] == 1) {
        tpl_set_var('guides_start', '');
        tpl_set_var('guides_end', '');
    } else {
        tpl_set_var('guides_start', '<!--');
        tpl_set_var('guides_end', '-->');
    }
    tpl_set_var('userid', (int) $usr['userid']);
    tpl_set_var('profileurl', $absolute_server_URI . 'viewprofile.php?userid=' . ($usr['userid'] + 0));
    tpl_set_var('statlink', $absolute_server_URI . 'statpics/' . ($usr['userid'] + 0) . '.jpg');
    tpl_set_var('username', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'));
    tpl_set_var('username_html', htmlspecialchars(htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'), ENT_COMPAT, 'UTF-8'));
    tpl_set_var('email', htmlspecialchars($record['email'], ENT_COMPAT, 'UTF-8'));
    tpl_set_var('country', htmlspecialchars(tr($record['country']), ENT_COMPAT, 'UTF-8'));
    tpl_set_var('registered_since', Formatter::date($record['date_created']));

    /* GeoKretyApi - display if secid from geokrety is set; (by Łza) */
    $GKAPIKeyQuery = "SELECT `secid` FROM `GeoKretyAPI` WHERE `userID` =:1";
    $s = $db->multiVariableQuery($GKAPIKeyQuery, $usr['userid']);
    if ($db->rowCount($s) > 0) {
        tpl_set_var('GeoKretyApiIntegration', tr('yes'));
    } else {
        tpl_set_var('GeoKretyApiIntegration', tr('no'));
    }
    $GKAPIKeyrecord = $db->dbResultFetchOneRowOnly($s);

    tpl_set_var('GeoKretyApiSecid', $GKAPIKeyrecord['secid']);

    // misc user options
    $using_permantent_login = $record['permanent_login_flag'];
    $geoPathsEmail = $record['power_trail_email'];

    if (isset($_REQUEST['action'])) {
        $action = $_REQUEST['action'];
        if ($action == 'change') { // display the change form
            $tplname = 'myprofile_change';
            $allcountries = tr('show_all');
            $no_answer = tr('no_choice');
            $error_username_not_ok = '<span class="errormsg">' . tr('username_incorrect') . '</span>';
            $error_username_exists = '<span class="errormsg">' . tr('username_exists') . '</span>';
            if ($nrecom >= 20) {
                tpl_set_var('guide_start', '');
                tpl_set_var('guide_end', '');
            } else {
                tpl_set_var('guide_start', '<!--');
                tpl_set_var('guide_end', '-->');
            }
            $guide = isset($_POST['guide']) ? (int) $_POST['guide'] : 0;
            if (isset($_POST['submit']) || isset($_POST['submit_all_countries'])) {
                // load datas from form
                $show_all_countries = $_POST['show_all_countries'];
                $username = $_POST['username'];
                $country = $_POST['country'];
                $ozi_path = strip_tags($_POST['ozi_path']);
                tpl_set_var('ozi_path', $ozi_path);

                $using_permantent_login = isset($_POST['using_permanent_login']) ? (int) $_POST['using_permanent_login'] : 0;
                if ($using_permantent_login == 1) {
                    tpl_set_var('permanent_login_sel', ' checked="checked"');
                } else {
                    tpl_set_var('permanent_login_sel', '');
                }

                if ($guide == 1) {
                    tpl_set_var('guide_sel', ' checked="checked"');
                } else {
                    tpl_set_var('guide_sel', '');
                }
                /* geoPaths - switch on/off notification email */
                $geoPathsEmail = isset($_POST['geoPathsEmail']) ? (int) $_POST['geoPathsEmail'] : 0;
                if ($geoPathsEmail == 1) {
                    tpl_set_var('geoPathsEmailCheckboxChecked', ' checked="checked"');
                } else {
                    tpl_set_var('geoPathsEmailCheckboxChecked', '');
                }

                $GeoKretyApiSecid = addslashes($_POST['GeoKretyApiSecid']);

                // set user messages
                tpl_set_var('username', $username);
                tpl_set_var('username_message', '');

                /* GeoKretyApi validate secid */
                if ((strlen($GeoKretyApiSecid) != 128)) {
                    tpl_set_var('secid_message', tr('GKApi11'));
                    $secid_not_ok = true;
                } else {
                    $secid_not_ok = false;
                    tpl_set_var('secid_message', '');
                }
                if ($GeoKretyApiSecid == '') {
                    $secid_not_ok = false;
                    tpl_set_var('secid_message', '');
                }

                // check if username is in the DB
                $username_exists = false;
                $username_not_ok = mb_ereg_match(User::REGEX_USERNAME, $username) ? false : true;

                if ($username_not_ok) {
                    tpl_set_var('username_message', $error_username_not_ok);
                } else {
                    if ($username != $usr['username']) {
                        $q = "SELECT `username` FROM `user` WHERE `username`=:1 LIMIT 1";
                        $s = $db->multiVariableQuery($q, $username);
                        if ($db->rowCount($s) > 0) {
                            $username_exists = true;
                            tpl_set_var('username_message', $error_username_exists);
                        }
                    }
                }

                // submit
                if (isset($_POST['submit'])) {
                    // try to save
                    if (! ($username_not_ok || $username_exists || $secid_not_ok)) {

                        /* GeoKretyApi - insert or update in DB user secid from Geokrety */
                        if (strlen($GeoKretyApiSecid) == 128) {
                            $db->multiVariableQuery("insert into `GeoKretyAPI` (`userID`, `secid`) values (:1, :2) on duplicate key update `secid`=:2", $usr['userid'], $GeoKretyApiSecid);
                            tpl_set_var('GeoKretyApiIntegration', tr('yes'));
                        } elseif ($GeoKretyApiSecid == '') {
                            $db->multiVariableQuery("DELETE FROM `GeoKretyAPI` WHERE `userID` = :1", $usr['userid']);
                            tpl_set_var('GeoKretyApiIntegration', tr('no'));
                        }

                        $db->multiVariableQuery("UPDATE `user`
                                  SET `last_modified`=NOW(),
                                      `country`=:1, `permanent_login_flag`=:2,
                                      `power_trail_email`=:3 , `ozi_filips`=:4, `guru`=:5
                                  WHERE `user_id`=:6", $country, $using_permantent_login, $geoPathsEmail, $ozi_path, $guide, (int) $usr['userid']);

                        // update user nick
                        if ($username != $usr['username']) {
                            $db->beginTransaction();
                            $q = "select count(id) from user_nick_history where user_id = :1";
                            $hist_count = $db->multiVariableQueryValue($q, 0, (int) $usr['userid']);
                            if ($hist_count == 0) {
                                // no history at all
                                $q = "insert into user_nick_history (user_id, date_from, date_to, username) select user_id, date_created, now(), username from user where user_id = :1";
                                $db->multiVariableQuery($q, (int) $usr['userid']);
                            } else {
                                // close previous entry
                                $q = "update user_nick_history set date_to = NOW() where date_to is null and user_id = :1";
                                $db->multiVariableQuery($q, (int) $usr['userid']);
                            }
                            // update and save current nick
                            $q = "update user set username = :1 where user_id = :2";
                            $db->multiVariableQuery($q, $username, (int) $usr['userid']);
                            $q = "insert into user_nick_history (user_id, date_from, username) select user_id, now(), username from user where user_id = :1";
                            $db->multiVariableQuery($q, (int) $usr['userid']);
                            $db->commit();
                            $usr['username'] = $username;
                        }

                        $tplname = 'myprofile';
                        tpl_set_var('country', htmlspecialchars(tr($country), ENT_COMPAT, 'UTF-8'));
                    }
                }
            } else { // display form
                if ($record['guru'] == 1 || $guide == 1) {
                    tpl_set_var('guides_start', '');
                    tpl_set_var('guides_end', '');
                } else {
                    tpl_set_var('guides_start', '<!--');
                    tpl_set_var('guides_end', '-->');
                }
                $geoPathsEmail = $record['power_trail_email'];
                $show_all_countries = 0;
                $country = $record['country'];
                $guide = $record['guru'];
                $using_permantent_login = $record['permanent_login_flag'];
                $ozi_path = strip_tags($record['ozi_filips']);
                tpl_set_var('ozi_path', $ozi_path);

                if ($using_permantent_login == 1) {
                    tpl_set_var('permanent_login_sel', ' checked="checked"');
                } else {
                    tpl_set_var('permanent_login_sel', '');
                }
                if ($guide == 1) {
                    tpl_set_var('guide_sel', ' checked="checked"');
                } else {
                    tpl_set_var('guide_sel', '');
                }
                if ($geoPathsEmail == 1) {
                    tpl_set_var('geoPathsEmailCheckboxChecked', ' checked="checked"');
                } else {
                    tpl_set_var('geoPathsEmailCheckboxChecked', '');
                }

                // set user messages
                tpl_set_var('username_message', '');
                tpl_set_var('secid_message', '');
            }
            if ($record['guru'] == 1 || $guide == 1) {
                tpl_set_var('guides_start', '');
                tpl_set_var('guides_end', '');
            } else {
                tpl_set_var('guides_start', '<!--');
                tpl_set_var('guides_end', '-->');
            }

            // load the country list
            if ($country == 'XX') {
                $stmp = '<option value="XX" selected="selected">' . $no_answer . '</option>';
            } else {
                $stmp = '<option value="XX">' . $no_answer . '</option>';
            }
            if (isset($_POST['submit_all_countries'])) {
                $show_all_countries = 1;
            }
            if (XDb::xContainsColumn('countries', 'list_default_' . $lang)) {
                $lang_db = $lang;
            } else {
                $lang_db = "en";
            }
            // Country in defaults ?
            if (($show_all_countries == 0) && ($country != 'XX')) {
                $stmt = $db->multiVariableQuery("SELECT `list_default_" . XDb::xEscape($lang_db) . "`
                        FROM `countries` WHERE `short`=:1 LIMIT 1", $country);

                $record2 = $db->dbResultFetchOneRowOnly($stmt);

                if ($record2['list_default_' . $lang_db] == 0) {
                    $show_all_countries = 1;
                } else {
                    $show_all_countries = 0;
                }
            }

            if ($show_all_countries == 1) {
                $rs2 = XDb::xSql("SELECT `" . XDb::xEscape($lang_db) . "`, `list_default_" . XDb::xEscape($lang_db) . "`, `short`, `sort_" . XDb::xEscape($lang_db) . "` FROM `countries` ORDER BY `sort_" . XDb::xEscape($lang_db) . '` ASC', $lang_db);
            } else {
                $rs2 = XDb::xSql("SELECT `" . XDb::xEscape($lang_db) . "`, `list_default_" . XDb::xEscape($lang_db) . "`, `short`, `sort_" . XDb::xEscape($lang_db) . "` FROM `countries` WHERE `list_default_" . XDb::xEscape($lang_db) . "`=1 ORDER BY `sort_" . XDb::xEscape($lang_db) . '` ASC', $lang_db);
            }

            while ($record2 = XDb::xFetchArray($rs2)) {

                if ($record2['short'] == $country) {
                    $stmp .= '<option value="' . $record2['short'] . '" selected="selected">' . htmlspecialchars($record2[$lang_db], ENT_COMPAT, 'UTF-8') . "</option>\n";
                } else {
                    $stmp .= '<option value="' . $record2['short'] . '">' . htmlspecialchars($record2[$lang_db], ENT_COMPAT, 'UTF-8') . "</option>\n";
                }
            }
            XDb::xFreeResults($rs2);

            tpl_set_var('countrylist', $stmp);
            unset($stmp);
            tpl_set_var('show_all_countries', $show_all_countries);

            if ($show_all_countries == 0) {
                tpl_set_var('allcountriesbutton', '<input class="btn btn-default btn-sm" type="submit" name="submit_all_countries" value="' . $allcountries . '" >');
            } else {
                tpl_set_var('allcountriesbutton', '');
            }
        }
    }

    // build useroptions
    $user_options = '';
    if ($using_permantent_login == 1) {
        $user_options .= $using_permantent_login_message . '<br>';
    }
    if ($geoPathsEmail == 1) {
        $user_options .= '<div style="display: ' . displayGeoPatchSection('div') . '">' . tr('pt235') . '</div><br>';
    }
    if ($user_options == '') {
        $user_options = '&nbsp;';
    }
    tpl_set_var('user_options', $user_options);
    $ozi_path = strip_tags($record['ozi_filips']);
    if (isset($_POST['ozi_path'])) {
        tpl_set_var('ozi_path', strip_tags($_POST['ozi_path']));
    } else {
        tpl_set_var('ozi_path', $ozi_path);
    }
}

// make the template and send it out
tpl_BuildTemplate();

function displayGeoPatchSection($type)
{
    global $powerTrailModuleSwitchOn;
    if ($powerTrailModuleSwitchOn === true) {
        switch ($type) {
            case 'div':
                return 'block';
            case 'table':
                return 'table-row';
            default:
                return 'inline';
        }
    }
    return 'none';
}