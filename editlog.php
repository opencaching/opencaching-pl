<?php

use okapi\Facade;
use src\Controllers\MeritBadgeController;
use src\Models\ApplicationContainer;
use src\Models\GeoCache\GeoCache;
use src\Models\GeoCache\GeoCacheCommons;
use src\Models\GeoCache\GeoCacheLog;
use src\Models\GeoCache\GeoCacheLogCommons;
use src\Models\GeoCache\MobileCacheMove;
use src\Models\OcConfig\OcConfig;
use src\Utils\Database\XDb;
use src\Utils\EventHandler\EventHandler;
use src\Utils\I18n\I18n;
use src\Utils\Text\InputFilter;
use src\Utils\Text\UserInputFilter;

//prepare the templates and include all necessary
require_once __DIR__ . '/lib/common.inc.php';
global $config;

//logid
$log_id = 0;
$view = tpl_getView();

if (isset($_REQUEST['logid'])) {
    $log_id = $_REQUEST['logid'];
}

//user logged in?
$loggedUser = ApplicationContainer::GetAuthorizedUser();

if (! $loggedUser) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);

    exit;
}

//does log with this logid exist?
$log_rs = XDb::xSql(
    'SELECT `cache_logs`.`cache_id` AS `cache_id`, `cache_logs`.`node` AS `node`, `cache_logs`.`text` AS `text`,
                    `cache_logs`.`date` AS `date`, `cache_logs`.`user_id` AS `user_id`, `cache_logs`.`type` AS `logtype`,
                    `cache_logs`.`text_html` AS `text_html`,
                    `cache_logs`.`last_modified` AS `last_modified`, `caches`.`name` AS `cachename`, `caches`.`status` AS `cachestatus`,
                    `caches`.`type` AS `cachetype`, `caches`.`user_id` AS `cache_user_id`, `caches`.`logpw` as `logpw`
            FROM `cache_logs` INNER JOIN `caches` ON (`caches`.`cache_id`=`cache_logs`.`cache_id`)
            WHERE `id`= ? AND `deleted` = ? LIMIT 1',
    $log_id,
    0
);

$log_record = XDb::xFetchArray($log_rs);

if ($log_record) {
    require __DIR__ . '/src/Views/editlog.inc.php';

    require __DIR__ . '/src/Views/rating.inc.php';

    if ($log_record['node'] != OcConfig::getSiteNodeId()) {
        tpl_errorMsg('editlog', $error_wrong_node);

        exit;
    }

    //is this log from this user?
    if (($log_record['user_id'] == $loggedUser->getUserId()
        && ($loggedUser->hasOcTeamRole() || ($log_record['cachestatus'] != 4 && $log_record['cachestatus'] != 6)))) {
        $tplname = 'editlog';
        $view->loadJquery();

        //load settings
        $cache_name = $log_record['cachename'];
        $cache_type = $log_record['cachetype'];
        $cache_user_id = $log_record['cache_user_id'];
        $log_type = $_POST['logtype'] ?? $log_record['logtype'];
        $log_date_min = $_POST['logmin'] ?? date('i', strtotime($log_record['date']));
        $log_date_hour = $_POST['loghour'] ?? date('H', strtotime($log_record['date']));
        $log_date_day = $_POST['logday'] ?? date('d', strtotime($log_record['date']));
        $log_date_month = $_POST['logmonth'] ?? date('m', strtotime($log_record['date']));
        $log_date_year = $_POST['logyear'] ?? date('Y', strtotime($log_record['date']));
        $top_cache = isset($_POST['rating']) ? $_POST['rating'] + 0 : 0;

        $log_pw = '';
        $use_log_pw = ! ((($log_record['logpw'] == null) || ($log_record['logpw'] == '')));

        if (($use_log_pw) && $log_record['logtype'] == 1) {
            $use_log_pw = false;
        }

        if ($use_log_pw) {
            $log_pw = $log_record['logpw'];
        }

        // check if user has exceeded his top5% limit
        $userRecoCountForThisCache = XDb::xMultiVariableQueryValue(
            'SELECT COUNT(`cache_id`) FROM `cache_rating`
                    WHERE `user_id`= :1 AND `cache_id`=:2 ',
            0,
            $log_record['user_id'],
            $log_record['cache_id']
        );

        $user_founds = XDb::xMultiVariableQueryValue(
            'SELECT `founds_count` FROM `user` WHERE `user_id`= :1 ',
            0,
            $log_record['user_id']
        );

        $user_tops = XDb::xMultiVariableQueryValue(
            'SELECT COUNT(`user_id`) FROM `cache_rating` WHERE `user_id`= :1 ',
            0,
            $log_record['user_id']
        );

        if ($userRecoCountForThisCache == 0) {
            if (($user_founds * GeoCacheCommons::RECOMENDATION_RATIO / 100) < 1) {
                $top_cache = 0;
                $recommendationsNr = 100 / GeoCacheCommons::RECOMENDATION_RATIO - $user_founds;
                $rating_msg = mb_ereg_replace('{recommendationsNr}', "{$recommendationsNr}", $rating_too_few_founds);
            } elseif ($user_tops < floor($user_founds * GeoCacheCommons::RECOMENDATION_RATIO / 100)) {
                if ($cache_user_id != $loggedUser->getUserId()) {
                    $rating_msg = mb_ereg_replace('{chk_sel}', '', $rating_allowed . '<br />' . $rating_stat);
                } else {
                    $rating_msg = mb_ereg_replace(
                        '{chk_dis}',
                        ' disabled="disabled"',
                        $rating_own . '<br />' . $rating_stat
                    );
                }
                $rating_msg = mb_ereg_replace(
                    '{max}',
                    floor($user_founds * GeoCacheCommons::RECOMENDATION_RATIO / 100),
                    $rating_msg
                );
                $rating_msg = mb_ereg_replace('{curr}', $user_tops, $rating_msg);
            } else {
                $top_cache = 0;
                $recommendationsNr = ((1 + $user_tops) * 100 / GeoCacheCommons::RECOMENDATION_RATIO) - $user_founds;
                $rating_msg = mb_ereg_replace('{recommendationsNr}', "{$recommendationsNr}", $rating_too_few_founds);

                $rating_msg .= '<br />' . $rating_maxreached;
            }
        } else {
            if ($cache_user_id != $loggedUser->getUserId()) {
                $rating_msg = mb_ereg_replace('{chk_sel}', ' checked', $rating_allowed . '<br />' . $rating_stat);
            } else {
                $rating_msg = mb_ereg_replace('{chk_dis}', ' disabled', $rating_own . '<br />' . $rating_stat);
            }
            $rating_msg = mb_ereg_replace(
                '{max}',
                floor($user_founds * GeoCacheCommons::RECOMENDATION_RATIO / 100),
                $rating_msg
            );
            $rating_msg = mb_ereg_replace('{curr}', $user_tops, $rating_msg);
        }

        // sp2ong 28.I.2010 recommendation all caches except events
        if ($log_record['cachetype'] != 6) {
            tpl_set_var('rating_message', mb_ereg_replace('{rating_msg}', $rating_msg, $rating_tpl));
        } else {
            tpl_set_var('rating_message', '');
        }

        // fuer alte Versionen von OCProp
        if (isset($_POST['submit']) && ! isset($_POST['version2'])) {
            $_POST['submitform'] = $_POST['submit'];
        }

        // Text from textarea
        $log_text = isset($_POST['logtext']) ? ($_POST['logtext']) : ($log_record['text']);

        // fuer alte Versionen von OCProp
        if (isset($_POST['submit']) && ! isset($_POST['version2'])) {
            $log_text = iconv('ISO-8859-1', 'UTF-8', $log_text);
        }

        // check input
        $log_text = InputFilter::cleanupUserInput($log_text);

        //validate date
        $date_not_ok = true;

        if (is_numeric($log_date_day) && is_numeric($log_date_month) && is_numeric($log_date_year) && is_numeric(
            $log_date_hour
        ) && is_numeric($log_date_min)) {
            $date_not_ok = (checkdate(
                $log_date_month,
                $log_date_day,
                $log_date_year
            ) == false || $log_date_hour < 0 || $log_date_hour > 23 || $log_date_min < 0 || $log_date_min > 60);

            if (! $date_not_ok) {
                if (isset($_POST['submitform'])) {
                    if (mktime(
                        $log_date_hour,
                        $log_date_min,
                        0,
                        $log_date_month,
                        $log_date_day,
                        $log_date_year
                    ) >= time()) {
                        $date_not_ok = true;
                    } else {
                        $date_not_ok = false;
                    }
                }
            }
        } else {
            $date_not_ok = true;
        }

        if ($cache_type == 6) {
            switch ($log_type) {
                case 1:
                case 2:
                    $logtype_not_ok = true;
                    break;
                default:
                    $logtype_not_ok = false;
                    break;
            }
        } else {
            switch ($log_type) {
                case 7:
                case 8:
                    $logtype_not_ok = true;
                    break;
                default:
                    $logtype_not_ok = false;
                    break;
            }
        }

        // not a type-found log? then ignore the rating
        $founds = XDb::xMultiVariableQueryValue(
            "SELECT count(*) as founds FROM `cache_logs`
                    WHERE user_id=:1 AND cache_id= :2 AND type='1' AND deleted=0",
            0,
            $log_record['user_id'],
            $log_record['cache_id']
        );

        if ($founds == 0) {
            if ($log_type != 1 && $log_type != 7 /* && $log_type != 3 */) {
                $top_cache = 0;
            }
        }

        $pw_not_ok = false;

        if (($use_log_pw) && $log_type == 1) {
            if (isset($_POST['log_pw'])) {
                if (mb_strtolower($log_pw) != mb_strtolower($_POST['log_pw'])) {
                    $pw_not_ok = true;
                    $all_ok = false;
                }
            } else {
                $pw_not_ok = true;
                $all_ok = false;
            }
        }

        if (isset($_POST['submitform']) && $log_type == GeoCacheLog::LOGTYPE_MOVED) {
            $newDate = date(
                'Y-m-d H:i:s',
                mktime($log_date_hour, $log_date_min, 0, $log_date_month, $log_date_day, $log_date_year)
            );
            $logObj = GeoCacheLog::fromLogIdFactory($log_id);
            MobileCacheMove::updateDateOnLogEdit($logObj, $newDate);
        }

        //store?
        if (isset($_POST['submitform']) && ! $date_not_ok && ! $logtype_not_ok && ! $pw_not_ok) {
            //store changed data

            // The following code will update last_modified even
            // if nothing else is changed in cache_logs. For the case this
            // is to be optimized so that last_modified is updated
            // only if there is a real modification, don't forget to update it also
            // if just a recommendation ("rating") is added or withdrawn (which is
            // stored in another table)! This is also necessary for proper OKAPI
            // replication of log entries
            // (see https://github.com/opencaching/okapi/issues/383).

            // check if log is edited less than $config['cache_log']['edit_time'] minutes ago
            // if yes - don't change edit_count field
            // https://github.com/opencaching/opencaching-pl/issues/696
            if (floor((time() - strtotime($log_record['last_modified'])) / 60) <= $config['cache_log']['edit_time']) {
                XDb::xSql(
                    'UPDATE `cache_logs`
                        SET `type`=?, `date`=?, `text`=?, `text_html`=?, `last_modified`=NOW(),
                            `edit_by_user_id` = ?
                        WHERE `id`=?',
                    // 1
                    $log_type,
                    // 2
                    date(
                        'Y-m-d H:i:s',
                        mktime($log_date_hour, $log_date_min, 0, $log_date_month, $log_date_day, $log_date_year)
                    ),
                    // 3
                    UserInputFilter::purifyHtmlString(((true) ? $log_text : nl2br($log_text))),
                    // 4
                    2,
                    $loggedUser->getUserId(),
                    $log_id
                );
            } else {
                XDb::xSql(
                    'UPDATE `cache_logs`
                        SET `type`=?, `date`=?, `text`=?, `text_html`=?, `last_modified`=NOW(),
                            `edit_by_user_id` = ?, `edit_count`= edit_count + 1
                        WHERE `id`=?',
                    // 1
                    $log_type,
                    // 2
                    date(
                        'Y-m-d H:i:s',
                        mktime($log_date_hour, $log_date_min, 0, $log_date_month, $log_date_day, $log_date_year)
                    ),
                    // 3
                    UserInputFilter::purifyHtmlString(((true) ? $log_text : nl2br($log_text))),
                    // 4
                    2,
                    $loggedUser->getUserId(),
                    $log_id
                );
            }

            //update user-stat if type changed
            if ($log_record['logtype'] != $log_type) {
                $user_rs = XDb::xSql(
                    'SELECT `founds_count`, `notfounds_count`, `log_notes_count` FROM `user`
                            WHERE `user_id`=? ',
                    $log_record['user_id']
                );
                $user_record = XDb::xFetchArray($user_rs);
                XDb::xFreeResults($user_rs);

                if ($log_record['logtype'] == 1) {
                    $user_record['founds_count']--;

                    // recalc scores for this cache
                    XDb::xSql(
                        'DELETE FROM `scores` WHERE `user_id` = ? AND `cache_id` = ?',
                        $log_record['user_id'],
                        $log_record['cache_id']
                    );

                    $liczba = XDb::xMultiVariableQueryValue(
                        'SELECT count(*) FROM scores WHERE cache_id=:1',
                        0,
                        $log_record['cache_id']
                    );

                    $suma = XDb::xMultiVariableQueryValue(
                        'SELECT SUM(score) FROM scores WHERE cache_id=:1',
                        0,
                        $log_record['cache_id']
                    );

                    // obliczenie nowej sredniej
                    if ($liczba != 0) {
                        $srednia = $suma / $liczba;
                    } else {
                        $srednia = 0;
                    }

                    XDb::xSql(
                        'UPDATE caches SET votes= ?, score= ? WHERE cache_id= ? ',
                        $liczba,
                        $srednia,
                        $log_record['cache_id']
                    );
                } elseif ($log_record['logtype'] == 2) {
                    $user_record['notfounds_count']--;
                } elseif ($log_record['logtype'] == 3) {
                    $user_record['log_notes_count']--;
                }

                // for the case that one of the fields is NULL
                $user_record['founds_count'] = $user_record['founds_count'] + 0;
                $user_record['notfounds_count'] = $user_record['notfounds_count'] + 0;
                $user_record['log_notes_count'] = $user_record['log_notes_count'] + 0;

                if ($log_type == 1) {
                    $user_record['founds_count']++;
                } elseif ($log_type == 2) {
                    $user_record['notfounds_count']++;

                    if ($founds <= 1) {
                        $top_cache = 0;
                    }
                } elseif ($log_type == 3) {
                    $user_record['log_notes_count']++;

                    if ($founds <= 1) {
                        $top_cache = 0;
                    }
                }

                XDb::xSql(
                    'UPDATE `user` SET `founds_count`=?, `notfounds_count`=?, `log_notes_count`=?
                            WHERE `user_id`= ?',
                    $user_record['founds_count'],
                    $user_record['notfounds_count'],
                    $user_record['log_notes_count'],
                    $log_record['user_id']
                );

                unset($user_record);

                //call eventhandler
                EventHandler::event_change_log_type($log_record['user_id'] + 0);
            }

            //update cache-stat if type or log_date changed
            $cache_rs = XDb::xSql(
                'SELECT `founds`, `notfounds`, `notes` FROM `caches` WHERE `cache_id`=?',
                $log_record['cache_id']
            );

            $cache_record = XDb::xFetchArray($cache_rs);
            XDb::xFreeResults($cache_rs);

            if ($log_record['logtype'] != $log_type) {
                if ($log_record['logtype'] == 1 || $log_record['logtype'] == 7) {
                    $cache_record['founds']--;
                } elseif ($log_record['logtype'] == 2 || $log_record['logtype'] == 8) {
                    $cache_record['notfounds']--;
                } elseif ($log_record['logtype'] == 3) {
                    $cache_record['notes']--;
                }

                // for the case that one of the fields is NULL
                $cache_record['founds'] = $cache_record['founds'] + 0;
                $cache_record['notfounds'] = $cache_record['notfounds'] + 0;
                $cache_record['notes'] = $cache_record['notes'] + 0;

                if ($log_type == 1 || $log_type == 7) {
                    $cache_record['founds']++;
                } elseif ($log_type == 2 || $log_type == 8) {
                    $cache_record['notfounds']++;
                } elseif ($log_type == 3) {
                    $cache_record['notes']++;
                }
            }

            // update top-list
            if ($top_cache == 1) {
                XDb::xSql(
                    'INSERT IGNORE INTO `cache_rating` (`user_id`, `cache_id`)
                            VALUES(?, ?)',
                    $log_record['user_id'],
                    $log_record['cache_id']
                );
            } else {
                XDb::xSql(
                    'DELETE FROM `cache_rating`
                            WHERE `user_id`=? AND `cache_id`=?',
                    $log_record['user_id'],
                    $log_record['cache_id']
                );
            }

            // Notify OKAPI's replicate module of the change.
            // Details: https://github.com/opencaching/okapi/issues/265
            Facade::schedule_user_entries_check($log_record['cache_id'], $log_record['user_id']);

            //Update last found
            $lastFoundDate = XDb::xMultiVariableQueryValue(
                'SELECT MAX(`date`) AS `date` FROM `cache_logs`
                         WHERE `type` IN (:1, :2) AND `cache_id`= :3 AND deleted = 0',
                'NULL',
                GeoCacheLog::LOGTYPE_FOUNDIT,
                GeoCacheLog::LOGTYPE_ATTENDED,
                $log_record['cache_id']
            );

            XDb::xSql(
                'UPDATE `caches` SET `last_found`=?, `founds`=?, `notfounds`=?, `notes`=? WHERE `cache_id`=?',
                $lastFoundDate,
                $cache_record['founds'],
                $cache_record['notfounds'],
                $cache_record['notes'],
                $log_record['cache_id']
            );

            $badgetParam = '';

            if ($config['meritBadges']) {
                $cache_id = $log_record['cache_id'];

                if ($log_type == GeoCacheLog::LOGTYPE_FOUNDIT
                    || $log_type == GeoCacheLog::LOGTYPE_ATTENDED) {
                    $ctrlMeritBadge = new MeritBadgeController();

                    $changedLevelBadgesIds = $ctrlMeritBadge->updateTriggerLogCache(
                        $cache_id,
                        $loggedUser->getUserId()
                    );
                    $titledIds = $ctrlMeritBadge->updateTriggerTitledCache($cache_id, $loggedUser->getUserId());

                    if ($changedLevelBadgesIds != '' && $titledIds != '') {
                        $changedLevelBadgesIds .= ',';
                    }

                    $changedLevelBadgesIds .= $titledIds;

                    if ($changedLevelBadgesIds != '') {
                        $badgetParam = '&badgesPopupFor=' . $changedLevelBadgesIds;
                    }

                    $ctrlMeritBadge->updateTriggerCacheAuthor($cache_id);
                }
            }

            unset($cache_record);

            //display cache page
            tpl_redirect('viewcache.php?cacheid=' . urlencode($log_record['cache_id']) . $badgetParam);

            exit;
        }

        // check if user has already found this cache and is not editing the found log (i.e. is able to change another comment's type to 'found')
        $already_found_in_other_comment = 0;

        $founds2 = XDb::xMultiVariableQueryValue(
            "SELECT count(*) as founds FROM `cache_logs`
                    WHERE user_id= :1 AND cache_id= :2 AND type='1' AND deleted=0",
            0,
            $loggedUser->getUserId(),
            $log_record['cache_id']
        );

        if ($founds2 > 0) {
            $founds3 = XDb::xMultiVariableQueryValue(
                'SELECT count(*) as founds FROM `cache_logs`
                        WHERE id= :1 AND type=1 AND deleted=0',
                0,
                $log_id
            );

            if ($founds3 == 0) {
                $already_found_in_other_comment = 1;
            }
        }

        //build logtypeoptions
        $logtypeoptions = '';

        foreach (GeoCacheLogCommons::logTypesArray() as $type) {
            // skip types allowed only for cacheOwner (9,10,11)
            $allowedOnlyForOwner = [
                GeoCacheLogCommons::LOGTYPE_READYTOSEARCH,
                GeoCacheLogCommons::LOGTYPE_ARCHIVED,
                GeoCacheLogCommons::LOGTYPE_TEMPORARYUNAVAILABLE,
            ];

            if (in_array($type, $allowedOnlyForOwner)
                && $log_record['user_id'] != $cache_user_id && ! $loggedUser->hasOcTeamRole()) {
                continue;
            }

            // Only COG can write or edit COG comment
            if ($type == GeoCacheLogCommons::LOGTYPE_ADMINNOTE && ! $loggedUser->hasOcTeamRole()) {
                continue;
            }

            // skip current type of log
            if ($log_record['logtype'] != $type && $log_record['cachestatus'] != GeoCacheCommons::STATUS_READY) {
                continue;
            }

            if ($log_record['logtype'] != $type                                // not same as current type
                && $log_record['cachestatus'] == GeoCacheCommons::STATUS_READY    // not ready-to-search
                && $log_record['user_id'] == $cache_user_id                       // is owner
                && $type != GeoCacheLogCommons::LOGTYPE_COMMENT
                && $type != GeoCacheLogCommons::LOGTYPE_MADEMAINTENANCE) {
                continue;
            }

            if ($already_found_in_other_comment) {
                if ($type == GeoCacheLogCommons::LOGTYPE_FOUNDIT
                    || $type == GeoCacheLogCommons::LOGTYPE_DIDNOTFIND
                    || $type == GeoCacheLogCommons::LOGTYPE_ATTENDED
                    || $type == GeoCacheLogCommons::LOGTYPE_WILLATTENDED
                    || $type == GeoCacheLogCommons::LOGTYPE_ARCHIVED
                    || $type == GeoCacheLogCommons::LOGTYPE_READYTOSEARCH
                    || $type == GeoCacheLogCommons::LOGTYPE_TEMPORARYUNAVAILABLE) {
                    continue;
                }
            }

            if ($cache_type == GeoCache::TYPE_EVENT) {
                if ($type == GeoCacheLogCommons::LOGTYPE_FOUNDIT
                    || $type == GeoCacheLogCommons::LOGTYPE_DIDNOTFIND
                    || $type == GeoCacheLogCommons::LOGTYPE_MOVED
                    || $type == GeoCacheLogCommons::LOGTYPE_NEEDMAINTENANCE
                    || $type == GeoCacheLogCommons::LOGTYPE_ARCHIVED
                    || $type == GeoCacheLogCommons::LOGTYPE_READYTOSEARCH
                    || $type == GeoCacheLogCommons::LOGTYPE_TEMPORARYUNAVAILABLE) {
                    continue;
                }
            } elseif ($cache_type == GeoCache::TYPE_MOVING) {
                if ($type == GeoCacheLogCommons::LOGTYPE_ATTENDED
                    || $type == GeoCacheLogCommons::LOGTYPE_WILLATTENDED
                    || $type == GeoCacheLogCommons::LOGTYPE_ARCHIVED) {
                    continue;
                }
            } else {
                if ($log_record['user_id'] == $cache_user_id // is owner
                    && ($type == GeoCacheLogCommons::LOGTYPE_FOUNDIT
                        || $type == GeoCacheLogCommons::LOGTYPE_DIDNOTFIND
                        || $type == GeoCacheLogCommons::LOGTYPE_MOVED
                        || $type == GeoCacheLogCommons::LOGTYPE_NEEDMAINTENANCE
                        || $type == GeoCacheLogCommons::LOGTYPE_ATTENDED
                        || $type == GeoCacheLogCommons::LOGTYPE_WILLATTENDED)) {
                    continue;
                }

                if ($log_record['user_id'] != $cache_user_id
                    && ($type == GeoCacheLogCommons::LOGTYPE_MOVED
                        || $type == GeoCacheLogCommons::LOGTYPE_ATTENDED
                        || $type == GeoCacheLogCommons::LOGTYPE_WILLATTENDED
                        || $type == GeoCacheLogCommons::LOGTYPE_ARCHIVED
                        || $type == GeoCacheLogCommons::LOGTYPE_READYTOSEARCH
                        || $type == GeoCacheLogCommons::LOGTYPE_TEMPORARYUNAVAILABLE)) {
                    continue;
                }
            }

            if ($type == $log_type) {
                $logtypeoptions .= '<option value="' . $type . '" selected="selected">'
                    . htmlspecialchars(
                        tr(GeoCacheLogCommons::typeTranslationKey($type)),
                        ENT_COMPAT,
                        'UTF-8'
                    ) . '</option>' . "\n";
            } else {
                $logtypeoptions .= '<option value="' . $type . '">'
                    . htmlspecialchars(
                        tr(GeoCacheLogCommons::typeTranslationKey($type)),
                        ENT_COMPAT,
                        'UTF-8'
                    ) . '</option>' . "\n";
            }
        }

        //set template vars
        tpl_set_var('cachename', htmlspecialchars($cache_name, ENT_COMPAT, 'UTF-8'));
        tpl_set_var('logtypeoptions', $logtypeoptions);
        tpl_set_var('logmin', htmlspecialchars($log_date_min, ENT_COMPAT, 'UTF-8'));
        tpl_set_var('loghour', htmlspecialchars($log_date_hour, ENT_COMPAT, 'UTF-8'));
        tpl_set_var('logday', htmlspecialchars($log_date_day, ENT_COMPAT, 'UTF-8'));
        tpl_set_var('logmonth', htmlspecialchars($log_date_month, ENT_COMPAT, 'UTF-8'));
        tpl_set_var('logyear', htmlspecialchars($log_date_year, ENT_COMPAT, 'UTF-8'));
        tpl_set_var('cachename', htmlspecialchars($cache_name, ENT_COMPAT, 'UTF-8'));
        tpl_set_var('cacheid', $log_record['cache_id']);
        tpl_set_var('logid', $log_id);
        tpl_set_var('date_message', $date_not_ok ? $date_message : '');
        tpl_set_var('bodyMod', ' onload="chkMoved()"');

        $log_text = UserInputFilter::purifyHtmlStringAndDecodeHtmlSpecialChars($log_text, $log_record['text_html']);
        tpl_set_var('logtext', htmlspecialchars($log_text, ENT_NOQUOTES, 'UTF-8'), true);

        if ($use_log_pw && $log_pw != '') {
            if ($pw_not_ok && isset($_POST['submitform'])) {
                tpl_set_var('log_pw_field', $log_pw_field_pw_not_ok);
            } else {
                tpl_set_var('log_pw_field', $log_pw_field);
            }
        } else {
            tpl_set_var('log_pw_field', '');
        }
    } else {
        header('Location: viewcache.php?cacheid=' . $log_record['cache_id']);
    }
} else {
    // no such log or log marked as deleted
    echo 'no_such_log...?!';

    exit();
}

//make the template and send it out
tpl_set_var('language4js', I18n::getCurrentLang());
tpl_BuildTemplate();
