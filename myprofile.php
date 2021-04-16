<?php

use src\Models\ApplicationContainer;
use src\Models\OcConfig\OcConfig;
use src\Utils\Database\XDb;
use src\Utils\Text\Formatter;
use src\Utils\Text\Validator;
use src\Utils\Uri\Uri;

require_once(__DIR__ . '/lib/common.inc.php');

$db = XDb::instance();
$description = "";

$user = ApplicationContainer::GetAuthorizedUser();
if (!$user) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);
}

$guidesConfig = OcConfig::instance()->getGuidesConfig();
tpl_set_var('desc_updated', '');
tpl_set_var('displayGeoPathSection', displayGeoPathSection('table'));
if (isset($_POST['description'])) {
    $q = "UPDATE user SET description = :1 WHERE user_id=:2";
    $db->multiVariableQuery($q, strip_tags($_POST['description']), (int)$user->getUserId());
    tpl_set_var('desc_updated', "<font color='green'>" . tr('desc_updated') . "</font>");
}

tpl_set_tplname('myprofile');
$using_permantent_login_message = tr('no_auto_logout');

// check user can set as Geocaching guide
// Number of recommendations
$nrecom = $db->multiVariableQueryValue("SELECT SUM(topratings) as nrecom FROM caches WHERE `caches`.`user_id`= :1",
    0, $user->getUserId());

if ($nrecom >= $guidesConfig['guideGotRecommendations']) {
    tpl_set_var('guide_start', '');
    tpl_set_var('guide_end', '');
} else {
    tpl_set_var('guide_start', '<!--');
    tpl_set_var('guide_end', '-->');
}

$s = $db->multiVariableQuery(
    "SELECT `description`, `guru`,`username`, `email`, `date_created`, `permanent_login_flag`,
                    `power_trail_email`, `ozi_filips` FROM `user` WHERE `user_id`=:1 ", $user->getUserId());

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
tpl_set_var('userid', (int)$user->getUserId());
tpl_set_var('profileurl', $absolute_server_URI . 'viewprofile.php?userid=' . ($user->getUserId()));
tpl_set_var('statlink', Uri::getAbsUri('/stats/statPic/' . $user->getUserId()));
tpl_set_var('username', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'));
tpl_set_var('username_html', htmlspecialchars(htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'), ENT_COMPAT, 'UTF-8'));
tpl_set_var('email', htmlspecialchars($record['email'], ENT_COMPAT, 'UTF-8'));
tpl_set_var('registered_since', Formatter::date($record['date_created']));

/* GeoKretyApi - display if secid from geokrety is set; (by Łza) */
$GKAPIKeyQuery = "SELECT `secid` FROM `GeoKretyAPI` WHERE `userID` =:1";
$s = $db->multiVariableQuery($GKAPIKeyQuery, $user->getUserId());
if ($db->rowCount($s) > 0) {
    tpl_set_var('GeoKretyApiIntegration', tr('yes'));
} else {
    tpl_set_var('GeoKretyApiIntegration', tr('no'));
}
$GKAPIKeyrecord = $db->dbResultFetchOneRowOnly($s);

tpl_set_var('GeoKretyApiSecid', isset($GKAPIKeyrecord['secid']) ? $GKAPIKeyrecord['secid'] : '');

// misc user options
$using_permantent_login = $record['permanent_login_flag'];
$geoPathsEmail = $record['power_trail_email'];

if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
    if ($action == 'change') { // display the change form
        tpl_set_tplname('myprofile_change');
        $error_username_not_ok = '<span class="errormsg">' . tr('username_incorrect') . '</span>';
        $error_username_exists = '<span class="errormsg">' . tr('username_exists') . '</span>';
        if ($nrecom >= $guidesConfig['guideGotRecommendations']) {
            tpl_set_var('guide_start', '');
            tpl_set_var('guide_end', '');
        } else {
            tpl_set_var('guide_start', '<!--');
            tpl_set_var('guide_end', '-->');
        }
        $guide = isset($_POST['guide']) ? (int)$_POST['guide'] : 0;
        if (isset($_POST['submit'])) {
            // load datas from form
            $username = $_POST['username'];
            $ozi_path = strip_tags($_POST['ozi_path']);
            tpl_set_var('ozi_path', $ozi_path);

            $using_permantent_login = isset($_POST['using_permanent_login']) ? (int)$_POST['using_permanent_login'] : 0;
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
            $geoPathsEmail = isset($_POST['geoPathsEmail']) ? (int)$_POST['geoPathsEmail'] : 0;
            if ($geoPathsEmail == 1) {
                tpl_set_var('geoPathsEmailCheckboxChecked', ' checked="checked"');
            } else {
                tpl_set_var('geoPathsEmailCheckboxChecked', '');
            }

            $GeoKretyApiSecid = addslashes($_POST['GeoKretyApiSecid']);

            // set user messages
            tpl_set_var('username', htmlspecialchars($username));
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
            $username_not_ok = !Validator::isValidUsername($username);

            if ($username_not_ok) {
                tpl_set_var('username_message', $error_username_not_ok);
            } else {
                if ($username != $user->getUserName()) {
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
                if (!($username_not_ok || $username_exists || $secid_not_ok)) {

                    /* GeoKretyApi - insert or update in DB user secid from Geokrety */
                    if (strlen($GeoKretyApiSecid) == 128) {
                        $db->multiVariableQuery(
                            "insert into `GeoKretyAPI` (`userID`, `secid`)
                                 values (:1, :2) on duplicate key update `secid`=:2",
                            $user->getUserId(), $GeoKretyApiSecid);

                        tpl_set_var('GeoKretyApiIntegration', tr('yes'));
                    } elseif ($GeoKretyApiSecid == '') {
                        $db->multiVariableQuery("DELETE FROM `GeoKretyAPI` WHERE `userID` = :1", $user->getUserId());
                        tpl_set_var('GeoKretyApiIntegration', tr('no'));
                    }

                    $db->multiVariableQuery("UPDATE `user`
                                  SET `last_modified` = NOW(),
                                      `permanent_login_flag`=:1,
                                      `power_trail_email`=:2 , `ozi_filips`=:3, `guru`=:4
                                  WHERE `user_id`=:5", $using_permantent_login, $geoPathsEmail, $ozi_path, $guide, (int)$user->getUserId());

                    // update user nick
                    if ($username != $user->getUserName()) {
                        $db->beginTransaction();
                        $q = "select count(id) from user_nick_history where user_id = :1";
                        $hist_count = $db->multiVariableQueryValue($q, 0, (int)$user->getUserId());
                        if ($hist_count == 0) {
                            // no history at all
                            $q = "insert into user_nick_history (user_id, date_from, date_to, username) select user_id, date_created, now(), username from user where user_id = :1";
                            $db->multiVariableQuery($q, (int)$user->getUserId());
                        } else {
                            // close previous entry
                            $q = "update user_nick_history set date_to = NOW() where date_to is null and user_id = :1";
                            $db->multiVariableQuery($q, (int)$user->getUserId());
                        }
                        // update and save current nick
                        $q = "update user set username = :1 where user_id = :2";
                        $db->multiVariableQuery($q, $username, (int)$user->getUserId());
                        $q = "insert into user_nick_history (user_id, date_from, username) select user_id, now(), username from user where user_id = :1";
                        $db->multiVariableQuery($q, (int)$user->getUserId());
                        $db->commit();

                        // $usr['username'] = $username; //TODO: update username?!
                    }

                    tpl_set_tplname('myprofile');
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
    }
}

// build useroptions
$user_options = '';
if ($using_permantent_login == 1) {
    $user_options .= $using_permantent_login_message . '<br>';
}
if ($geoPathsEmail == 1) {
    $user_options .= '<div style="display: ' . displayGeoPathSection('div') . '">' . tr('pt235') . '</div><br>';
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


// make the template and send it out
tpl_BuildTemplate();

function displayGeoPathSection($type)
{
    if (OcConfig::areGeopathsSupported()) {
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
