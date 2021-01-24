<?php

use src\Controllers\MyRecommendationsController;
use src\Utils\Database\XDb;
use src\Utils\Database\OcDb;
use src\Models\PowerTrail\PowerTrail;
use src\Models\User\User;
use src\Models\GeoCache\GeoCacheLog;
use src\Models\GeoCache\GeoCache;
use src\Models\OcConfig\OcConfig;
use src\Models\MeritBadge\MeritBadge;
use src\Utils\Text\TextConverter;
use src\Utils\DateTime\Year;
use src\Utils\Uri\SimpleRouter;
use src\Utils\Uri\OcCookie;
use src\Utils\View\View;
use src\Controllers\MeritBadgeController;
use src\Utils\Text\Formatter;
use src\Models\Admin\AdminNoteSet;
use src\Models\User\UserStats;
use src\Utils\Debug\StopWatch;
use src\Models\ApplicationContainer;

const ADMINNOTES_PER_PAGE = 10;

require_once (__DIR__.'/lib/common.inc.php');

StopWatch::click('start');

//user logged in?
$loggedUser = ApplicationContainer::GetAuthorizedUser();
if (!$loggedUser) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);
    exit;
}

$infoMsg = '';
$errorMsg = '';


if (isset($_REQUEST['infoMsg'])) {
    $infoMsg = strip_tags(urldecode($_REQUEST['infoMsg']));
}
if (isset($_REQUEST['errorMsg'])) {
    $errorMsg = strip_tags(urldecode($_REQUEST['errorMsg']));
}
if (isset($_REQUEST['save'])) {

    if (isset($_REQUEST['checkBadges']))
        OcCookie::set("checkBadges", !$_REQUEST['checkBadges']);

    if (isset($_REQUEST['checkGeoPaths']))
        OcCookie::set("checkGeoPaths", !$_REQUEST['checkGeoPaths']);
}

$checkBadges = OcCookie::getOrDefault("checkBadges", 1);
$checkGeoPaths = OcCookie::getOrDefault("checkGeoPaths", 1);


$cache_line = '<li style="margin: -0.9em 0px 0.9em 0px; padding: 0px 0px 0px 10px; list-style-type: none; line-height: 1.6em; font-size: 12px;">{cacheimage}&nbsp;{cachestatus} &nbsp; {date} &nbsp; <a class="links" href="viewcache.php?cacheid={cacheid}">{cachename}</a>&nbsp;&nbsp;<strong>[{wpname}]</strong></li>';
$cache_notpublished_line = '<li style="margin: -0.9em 0px 0.9em 0px; padding: 0px 0px 0px 10px; list-style-type: none; line-height: 1.6em; font-size: 115%;">{cacheimage}&nbsp;{cachestatus} &nbsp; <a class="links" href="editcache.php?cacheid={cacheid}">{date}</a> &nbsp; <a class="links" href="viewcache.php?cacheid={cacheid}">{cachename}</a>&nbsp;&nbsp;<strong>[{wpname}]</strong></li>';
$log_line = '<li style="margin: -0.9em 0px 0.9em 0px; padding: 0px 0px 0px 10px; list-style-type: none; line-height: 1.6em; font-size: 12px;">{gkimage}&nbsp;{rateimage}&nbsp; {logimage} &nbsp; <a class="links" href="viewcache.php?cacheid={cacheid}"><img src="/images/{cacheimage}" border="0" alt=""></a>&nbsp; {date} &nbsp; <a class="links" href="viewlogs.php?logid={logid}" onmouseover="Tip(\'{logtext}\', PADDING,5, WIDTH,280,SHADOW,true)" onmouseout="UnTip()">{cachename}</a>&nbsp;&nbsp;<strong>[{wpname}]</strong></li>';
$cache_line_my_caches = '<li style="margin: -0.9em 0px 0.9em 0px; padding: 0px 0px 0px 10px; list-style-type: none; line-height: 1.6em; font-size: 12px;">{gkimage}&nbsp; {rateimage} &nbsp;{logimage} &nbsp; <a class="links" href="viewcache.php?cacheid={cacheid}"><img src="/images/{cacheimage}" border="0" alt=""></a>&nbsp; {date} &nbsp; <a class="links" href="viewlogs.php?logid={logid}" onmouseover="Tip(\'{logtext}\', PADDING,5, WIDTH,280,SHADOW,true)" onmouseout="UnTip()">{cachename}</a>&nbsp;&nbsp;<strong>[{wpname}]</strong>&nbsp;<img src="/images/blue/arrow.png" alt="">&nbsp; <a class="links" href="viewprofile.php?userid={userid}">{username}</a></li>';

// check for old-style parameters
if (isset($_REQUEST['userid'])) {
    $user_id = $_REQUEST['userid'];
} else {
    $user_id = $loggedUser->getUserId();
}

require (__DIR__.'/src/Views/lib/icons.inc.php');
$tplname = 'viewprofile';

/** @var View */
$view = tpl_getView();
$view->setVar('userid', $user_id);
$view->loadJQuery();

$content = "";

$database = OcDb::instance();

$rddQuery = "select TO_DAYS(NOW()) - TO_DAYS(`date_created`) `diff` from `user` WHERE user_id=:1 LIMIT 1";
$s = $database->multiVariableQuery($rddQuery, $user_id);
$ddays = $database->dbResultFetchOneRowOnly($s);

StopWatch::click(__LINE__);

$user = User::fromUserIdFactory($user_id,
    "user_id, role, guru, hidden_count, founds_count, is_active_flag, email, password, log_notes_count,
     notfounds_count, username, last_login, date_created, description");

if (is_null($user)) {
    tpl_errorMsg("viewprofile", "User not found!");
    exit;
}

StopWatch::click(__LINE__);

tpl_set_var('username', htmlspecialchars($user->getUserName()));
if (Year::isPrimaAprilisToday() && OcConfig::isPAFakeUserNameEnabled()) {
    tpl_set_var('username', tr('primaAprilis1'));
}
tpl_set_var('registered', Formatter::date($user->getDateCreated()));

$description = $user->getDescription();

tpl_set_var('description', nl2br($description));
if ($description != "") {
    tpl_set_var('description_start', '');
    tpl_set_var('description_end', '');
} else {
    tpl_set_var('description_start', '<!--');
    tpl_set_var('description_end', '-->');
}
$pimage = 'profile2';
$pinfo = "OC user";

if ($user->isGuide()) {
    $pimage = 'guide';
    $pinfo = "Przewodnik";
}

if ($user->hasOcTeamRole()) {
    $pimage = 'admins';
    $pinfo = "OC Team user";
}

tpl_set_var('profile_img', $pimage);
tpl_set_var('profile_info', $pinfo);

$guide_info = '<br>';
if ($user_id == $loggedUser->getUserId()) {
    // Number of recommendations
    $nrecom = XDb::xMultiVariableQueryValue(
        "SELECT SUM(topratings) as nrecom FROM caches WHERE `caches`.`user_id`= :1 ",
        0, $loggedUser->getUserId());

    StopWatch::click(__LINE__);

    // new with recommendations
    $guides = OcConfig::instance()->getGuidesConfig();

    if ($nrecom >= $guides['guideGotRecommendations'] && !$user->isGuide() && $user_id == $loggedUser->getUserId()) {
        $guide_info = '<div class="content-title-noshade box-blue"><table><tr><td><img style="margin-right: 10px;margin-left:10px;" src="/images/blue/info-b.png" alt="guide"></td><td>
            <span style="font-size:12px;"> ' . tr('guru_17') . '
            <a class="links" href="myprofile.php?action=change"> ' . tr('guru_18') . '</a>.
            ' . tr('guru_19') . ' <a class="links" href="/guide">' . tr('guru_20') . '</a>.</span>
            </td></tr></table></div><br>';
    }
}
tpl_set_var('guide_info', $guide_info);

StopWatch::click(__LINE__);

/* set last_login to one of 5 categories
 *   1 = this month or last month
 *   2 = between one and 6 months
 *   3 = between 6 and 12 months
 *   4 = more than 12 months
 *   5 = unknown, we need this, because we dont
 *       know the last_login of all accounts.
 *       Can be removed after one year.
 *   6 = user account is not active
 */
if (! $user->isActive()) {
    tpl_set_var('lastlogin', tr('user_not_active'));
} else {
    tpl_set_var('lastlogin', tr($user->getLastLoginPeriodString()));
}
tpl_set_var('lastloginClass', $user->getLastLoginPeriodClass());

//Admin Note (table only)
if ($loggedUser->hasOcTeamRole()) {
    $content .= '<div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;<img src="/images/blue/logs.png" class="icon32" alt="Cog Note" title="Cog Note"> ' . tr('admin_notes') . '</p></div>';
    $content .= '<div class="notice">'.tr('admin_notes_visible').'</div><p><a href="' . SimpleRouter::getLink('Admin.UserAdmin', 'index', $user_id) . '" class="links">'.tr('admin_user_management').' <img src="/images/misc/linkicon.png" alt="user admin"></a></p>';
    $adminNotes = AdminNoteSet::getNotesForUser($user, ADMINNOTES_PER_PAGE);

    if (empty($adminNotes)) {
        $content .= '<p>' . tr("admin_notes_no_infos") . '</p>';
    } else {
        $content .= '<table class="table table-striped full-width">
          <tr>
            <th colspan="2">' . tr("admin_notes_table_title") . '</th>
          </tr>';
        foreach ($adminNotes as $adminNote) {
            $content .= '<tr>
              <td>' . Formatter::dateTime($adminNote->getDateTime()) . '
              - <a class="links" href="'. $adminNote->getAdmin()->getProfileUrl() . '">' . htmlspecialchars($adminNote->getAdmin()->getUserName()) . '</a></td><td>';
            if ($adminNote->isAutomatic()) {
                $content .= '<img title="'.tr("admin_notes_auto").'" alt="' . tr("admin_notes_auto") . '" class="icon16" src="' . $adminNote->getAutomaticPictureUrl() . '"> ';
                $content .= tr($adminNote->getContentTranslationKey());
                if (! empty($adminNote->getCacheId())) {
                    $content .= ' <a class="links" href="' . $adminNote->getCache()->getCacheUrl() . '">' . $adminNote->getCache()->getCacheName() . ' (' . $adminNote->getCache()->getGeocacheWaypointId() . ')</a>';
                }

            } else {
                $content .= '<img title="'.tr("admin_notes_man").'" alt="'.tr("admin_notes_man").'" class="icon16" src="' . $adminNote->getAutomaticPictureUrl() . '"> ';
                $content .= $adminNote->getContent();
            }
            $content .= '</td></tr>';
        }
        $content .= '</table>';
    }

    if (AdminNoteSet::getNotesForUserCount($user) > ADMINNOTES_PER_PAGE) {
        $content .= '<a href="' . SimpleRouter::getLink('Admin.UserAdmin', 'index', $user_id) . '" class="btn btn-default btn-sm">' . tr('more') . '</a>';
    }
}

StopWatch::click(__LINE__);

if (Year::isPrimaAprilisToday() && OcConfig::isPAUserStatsRandEnabled()) {
    $act = rand(-10, 10);
} else {
    $act = $user->getFoundGeocachesCount() + $user->getNotFoundGeocachesCount() + $user->getHiddenGeocachesCount();
}

$content .= '<br><p>&nbsp;</p><div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;<img src="/images/blue/event.png" class="icon32" alt="Caches Find" title="Caches Find">&nbsp;&nbsp;&nbsp;' . tr('user_activity01') . '</p></div><br><p><span class="content-title-noshade txt-blue08">' . tr('user_activity02') . '</span>:&nbsp;<strong>' . $act . '</strong></p>';

//////////////////////////////////////////////////////////////////////////////

//Merit badges
if ($config['meritBadges']) {

    $content .= buildOpenCloseButton($user_id, $checkBadges, "merit_badge.png", "checkBadges", tr('merit_badges'), "Merit badges");

    if ($checkBadges)
        $content .= buildMeritBadges($user_id);
}
////////////////////////////////////////////////////////////////////////////

StopWatch::click(__LINE__);

// PowerTrails stats

if ($powerTrailModuleSwitchOn) {

    $content .= buildOpenCloseButton($user_id, $checkGeoPaths, "powerTrailGenericLogo.png", "checkGeoPaths", tr('gp_mainTitile'), "geoPaths");

    if ($checkGeoPaths) {
    //geoPaths medals
        $content .= buildPowerTrailIcons(UserStats::getGeoPathsCompleted($user->getUserId()));
        $content .= '<p><span class="content-title-noshade txt-blue08">' . tr('pt140') . '</span>:&nbsp;<strong>' . powerTrailBase::getUserPoints($user_id) . '</strong> (' . tr('pt093') . ' ' . powerTrailBase::getPoweTrailCompletedCountByUser($user_id) . ')</p>';
        $pointsEarnedForPlacedCaches = powerTrailBase::getOwnerPoints($user_id);

        $content .= buildPowerTrailIcons(UserStats::getGeoPathsOwned($user->getUserId()));
        $content .= '<p><span class="content-title-noshade txt-blue08">' . tr('pt224') . '</span>:&nbsp;<strong>' . $pointsEarnedForPlacedCaches['totalPoints'] . '</strong></p>';
    }
}

StopWatch::click(__LINE__);

// -----------  begin Find section -------------------------------------
$content .= '<p>&nbsp;</p><div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;<img src="/images/blue/cache-open.png" class="icon32" alt="Caches Find" title="Caches Find">&nbsp;&nbsp;&nbsp;' . tr('stat_number_found') . '</p></div><br>';

$seek = XDb::xMultiVariableQueryValue(
    "SELECT COUNT(*) FROM cache_logs
    WHERE (type=1 OR type=2) AND cache_logs.deleted='0' AND user_id= :1
    GROUP BY YEAR(`date`), MONTH(`date`), DAY(`date`)",
    0, $user_id);

if ($seek == 0) {
    $content .= '<br><p> <b>' . tr('not_found_caches') . '</b></p>';
} else {

    StopWatch::click(__LINE__);

    $events_count = XDb::xMultiVariableQueryValue(
        "SELECT COUNT(*) events_count FROM cache_logs
        WHERE user_id= :1 AND type=7 AND deleted=0", 0, $user_id );

    StopWatch::click(__LINE__);

    // !!! LIMIT 3: logically should be limit 1 but LIMIT 3 has much better performance
    // more detail for example here: https://stackoverflow.com/questions/17747871/why-is-mysql-slow-when-using-limit-in-my-query
    $days_since_first_find = XDb::xMultiVariableQueryValue(
        "SELECT datediff(now(), date) as old FROM cache_logs
        WHERE deleted=0 AND user_id = :1 AND type=1 ORDER BY date LIMIT 3",
        0, $user_id);

    StopWatch::click(__LINE__);

    $rsfc2 = XDb::xSql(
        "SELECT cache_logs.cache_id cache_id, DATE_FORMAT(cache_logs.date,'%d-%m-%Y') data, caches.wp_oc cache_wp
        FROM cache_logs, caches
        WHERE caches.cache_id=cache_logs.cache_id AND cache_logs.type='1'
            AND cache_logs.user_id= ? AND cache_logs.deleted='0'
        ORDER BY cache_logs.date DESC LIMIT 1", $user_id );
    $rfc2 = XDb::xFetchArray($rsfc2);

    StopWatch::click(__LINE__);

    $rcNumber = XDb::xMultiVariableQueryValue(
        "SELECT COUNT(*) number FROM cache_logs
        WHERE type=1 AND cache_logs.deleted='0' AND user_id= :1
        GROUP BY YEAR(`date`), MONTH(`date`), DAY(`date`)
        ORDER BY number DESC LIMIT 1", 0, $user_id);

    StopWatch::click(__LINE__);

    $moved = XDb::xMultiVariableQueryValue(
        "SELECT COUNT(*) FROM `cache_logs`
        WHERE type=4 AND cache_logs.deleted='0' AND user_id= :1",
        0, $user_id);

    StopWatch::click(__LINE__);

    $num_rows = XDb::xMultiVariableQueryValue(
        "SELECT COUNT(*) FROM (
            SELECT COUNT(*) FROM cache_logs
            WHERE type=1 AND cache_logs.deleted='0' AND user_id= :1
            GROUP BY YEAR(`date`), MONTH(`date`), DAY(`date`)
        ) AS COUNTS_IN_DAYS",
        0, $user_id );

    StopWatch::click(__LINE__);

    $found = $user->getFoundGeocachesCount();
    $userNotFounds = $user->getNotFoundGeocachesCount();

    if ($num_rows == 0) {
        $aver2 = 0;
    } else {
        $aver2 = round(($found / $num_rows), 2);
    }


    if (Year::isPrimaAprilisToday() && OcConfig::isPAUserStatsRandEnabled()) {
        $found = rand(-10, 10);
        $userNotFounds = rand(666, 9999);
    }

    $content .= '<p><span class="content-title-noshade txt-blue08">' . tr('total_number_found_caches') . ':</span><strong> ' . $found . '</strong>';
    if ($found == 0) {
        $content .= '</p>';
    } else {
        $content .= '&nbsp;&nbsp;&nbsp;<img src="/images/blue/arrow.png" alt=""> [<a class="links" href="my_logs.php?userid=' . $user_id . '&logtypes=1">' . tr('show') . '</a>]</p>';
    }

    $content .= '<p><span class="content-title-noshade txt-blue08">' . tr('total_dnf_caches') . ':</span> <strong>' . $userNotFounds . '</strong>';

    if ($userNotFounds == 0) {
        $content .= '</p>';
    } else {
        $content .= '&nbsp;&nbsp;&nbsp;<img src="/images/blue/arrow.png" alt=""> [<a class="links" href="my_logs.php?userid=' . $user_id . '&logtypes=2">' . tr('show') . '</a>]</p>';
    }
    $content .= '<p><span class="content-title-noshade txt-blue08">' . tr('total_comments') . ':</span> <strong>' . $user->getLogNotesCount() . '</strong>';
    if ($user->getLogNotesCount() == 0) {
        $content .= '</p>';
    } else {
        $content .= '&nbsp;&nbsp;&nbsp;<img src="/images/blue/arrow.png" alt=""> [<a class="links" href="my_logs.php?userid=' . $user_id . '&logtypes=3">' . tr('show') . '</a>]</p>';
    }
    $content .= '<p><span class="content-title-noshade txt-blue08">' . tr('total_moved') . ':</span> <strong>' . $moved . '</strong>';
    if ($moved == 0) {
        $content .= '</p>';
    } else {
        $content .= '&nbsp;&nbsp;&nbsp;<img src="/images/blue/arrow.png" alt=""> [<a class="links" href="my_logs.php?userid=' . $user_id . '&logtypes=4">' . tr('show') . '</a>]</p>';
    }
    $content .= '<p><span class="content-title-noshade txt-blue08">' . tr('total_attended_events') . ':</span> <strong>' . $events_count . '</strong>';
    if ($events_count == 0) {
        $content .= '</p>';
    } else {
        $content .= '&nbsp;&nbsp;&nbsp;<img src="/images/blue/arrow.png" alt=""> [<a class="links" href="my_logs.php?userid=' . $user_id . '&logtypes=7">' . tr('show') . '</a>]</p>';
    }

    $recomendf = XDb::xMultiVariableQueryValue(
        "SELECT COUNT(*) FROM `cache_rating` WHERE `user_id`= :1 ", 0, $user_id);

    $content .= '<p><span class="content-title-noshade txt-blue08">' . tr('number_recommendations_given') . ':</span> <strong>' . $recomendf . '</strong>';

    StopWatch::click(__LINE__);

    if ($recomendf == 0) {
        $content .= '</p>';
    } else {
        if ($loggedUser->getUserId() == $user_id) {
            $link_togo = SimpleRouter::getLink(MyRecommendationsController::class, 'recommendations');
        } else {
            $link_togo = SimpleRouter::getLink(MyRecommendationsController::class, 'recommendations', $user_id);
        }
        $content .= '&nbsp;&nbsp;&nbsp;<img src="/images/blue/arrow.png" alt=""> [<a class="links" href="' . $link_togo . '">' . tr('show') . '</a>]</p>';
    }

    $content .= '<p><span class="content-title-noshade txt-blue08">' . tr('days_caching') . ':</span> <strong>' . $num_rows . '</strong>&nbsp;' . tr('from_total_days') . ': <strong>' . $ddays['diff'] . '</strong></p>';
    $content .= '<p><span class="content-title-noshade txt-blue08">' . tr('average_caches') . ':</span> <strong>' . sprintf("%u", $aver2) . '</strong></p>';

    $content .= '<p><span class="content-title-noshade txt-blue08">' . tr('most_caches_find_day') . ':</span> <strong>' . sprintf("%u", $rcNumber) . '</strong></p>';
    $content .= '<p><span class="content-title-noshade txt-blue08">' . tr('latest_cache') . ':</span>&nbsp;&nbsp;';

    if ( $rfc2 != false ) {
        $content .= '<strong><a class="links" href="viewcache.php?cacheid=' . $rfc2['cache_id'] . '">' . $rfc2['cache_wp'] . '</a>&nbsp;&nbsp;</strong>(' . $rfc2['data'] . ')</p>';
    } else {
        $content .= '</p>';
    }


    if ( $found >= 10 ) {
        $content .= '<br><table style="border-collapse: collapse; font-size: 110%;" width="250" border="1"><tr><td colspan="3" align="center" bgcolor="#DBE6F1"><b>' . tr('milestones') . '</b></td> </tr><tr><td bgcolor="#EEEDF9"><b> Nr </b></td> <td bgcolor="#EEEDF9"><b>'.tr('date').'</b></td> <td bgcolor="#EEEDF9"><b>'.tr('cache').'</b> </td> </tr>';

        if ( $found > 101 ) {
            $milestone = 100;
        }
        else {

            $milestone = 10;
        }

        $rsms = XDb::xSql(
        "SET @r = 1;
        SELECT * FROM
        (
            SELECT *,@r:=@r+1 row FROM (

                SELECT cache_logs.cache_id cache_id, DATE_FORMAT(cache_logs.date,'%d-%m-%Y') data, caches.wp_oc cache_wp
                FROM cache_logs, caches
                WHERE caches.cache_id=cache_logs.cache_id AND cache_logs.type='1'
                AND cache_logs.user_id= ? AND cache_logs.deleted='0'
                ORDER BY cache_logs.date ASC

            ) B
        ) A
        WHERE row=2 OR row % $milestone =1 ORDER BY row ASC", $user_id);

        $rsms->nextRowset(); //to switch to second query results :)
        while( $rms = XDb::xFetchArray($rsms)) {
            $content .= '<tr> <td>' . ($rms['row']-1) . '</td><td>' . $rms['data'] . '</td><td><a class="links" href="viewcache.php?cacheid=' . $rms['cache_id'] . '">' . $rms['cache_wp'] . '</a></td></tr>';
        }

        $content .= '</table>';
        XDb::xFreeResults($rsms);
    } // $found > 10

    XDb::xFreeResults($rsfc2);

    StopWatch::click(__LINE__);

    //ftf Ajax
    $content .= '<hr><center>
    <a href="javascript:void(0);" onclick="ajaxGetFTF();" id="showFtfBtn">' . tr('viewprofileFTF') . '</a>
    <center><img style="display:none" id="commentsLoader" src="images/misc/ptPreloader.gif"></center>
    <div id="ftfDiv" style="display:none"></div></center><input type="hidden" id="userId" value="' . $user_id . '">';

    //------------ begin owner section
    //          if ($user_id == $loggedUser->getUserId())
    //          {
    StopWatch::click(__LINE__);

    $rs_logs = XDb::xSql(
        "SELECT cache_logs.id, cache_logs.cache_id AS cache_id, cache_logs.type AS log_type,
            cache_logs.text AS log_text, DATE_FORMAT(cache_logs.date,'%d-%m-%Y')  AS log_date,
            caches.name AS cache_name, caches.wp_oc AS wp_name, user.username AS user_name,
            cache_logs.user_id AS luser_id,
            user.user_id AS user_id, caches.user_id AS cache_owner, caches.type AS cache_type,
            cache_type.icon_small AS cache_icon_small, log_types.icon_small AS icon_small,
            IF(ISNULL(`cache_rating`.`cache_id`), 0, 1) AS `recommended`,COUNT(gk_item.id) AS geokret_in
        FROM ((cache_logs INNER JOIN caches ON (caches.cache_id = cache_logs.cache_id)))
            INNER JOIN user ON (cache_logs.user_id = user.user_id)
            INNER JOIN log_types ON (cache_logs.type = log_types.id)
            INNER JOIN cache_type ON (caches.type = cache_type.id)
            LEFT JOIN `cache_rating` ON `cache_logs`.`cache_id`=`cache_rating`.`cache_id`
                AND `cache_logs`.`user_id`=`cache_rating`.`user_id`
            LEFT JOIN   gk_item_waypoint ON gk_item_waypoint.wp = caches.wp_oc
            LEFT JOIN   gk_item ON gk_item.id = gk_item_waypoint.id
                AND gk_item.stateid<>1 AND gk_item.stateid<>4 AND gk_item.typeid<>2 AND gk_item.stateid !=5
            WHERE (caches.status=1 OR caches.status=2 OR caches.status=3) AND cache_logs.deleted=0
                AND `cache_logs`.`user_id`= ?  AND cache_logs.type <> 12
            GROUP BY cache_logs.id
            ORDER BY cache_logs.date_created DESC
            LIMIT 5", $user_id);
    StopWatch::click(__LINE__);

    if (XDb::xNumRows($rs_logs) != 0) {

        $content .= '<p>&nbsp;</p><p><span class="content-title-noshade txt-blue08">' . tr('latest_logs_by_user') . ':</span>&nbsp;&nbsp;<img src="/images/blue/arrow.png" alt=""> [<a class="links" href="my_logs.php?userid=' . $user_id . '">' . tr('show_all') . '</a>] ';
        $content .= ' <a class="links" href="/rss/my_logs.xml?userid=' . $user_id . '"><img src="/images/misc/rss.svg" class="icon16" alt="RSS icon"></a>';
        $content .= '</p><br><div><ul style="margin: -0.9em 0px 0.9em 0px; padding: 0px 0px 0px 10px; list-style-type: none; line-height: 1.2em; font-size: 115%;">';

        while( $record_logs = XDb::xFetchArray($rs_logs) ) {
            $tmp_log = $log_line;
            if ($record_logs['geokret_in'] != '0') {
                $tmp_log = mb_ereg_replace('{gkimage}', '<img src="images/gk.png" border="0" alt="" title="GeoKret">', $tmp_log);
            } else {
                $tmp_log = mb_ereg_replace('{gkimage}', '<img src="images/rating-star-empty.png" border="0" alt="">', $tmp_log);
            }

            if ($record_logs['recommended'] == 1 && $record_logs['log_type'] == 1) {
                $tmp_log = mb_ereg_replace('{rateimage}', '<img src="images/rating-star.png" border="0" alt="">', $tmp_log);
            } else {
                $tmp_log = mb_ereg_replace('{rateimage}', '<img src="images/rating-star-empty.png" border="0" alt="">', $tmp_log);
            }
            $tmp_log = mb_ereg_replace('{logimage}', icon_log_type($record_logs['icon_small'], "..."), $tmp_log);
            $tmp_log = mb_ereg_replace('{cacheimage}', $record_logs['cache_icon_small'], $tmp_log);
            $tmp_log = mb_ereg_replace('{date}', $record_logs['log_date'], $tmp_log);
            $tmp_log = mb_ereg_replace('{cachename}', htmlspecialchars($record_logs['cache_name'], ENT_COMPAT, 'UTF-8'), $tmp_log);
            $tmp_log = mb_ereg_replace('{wpname}', htmlspecialchars($record_logs['wp_name'], ENT_COMPAT, 'UTF-8'), $tmp_log);
            $tmp_log = mb_ereg_replace('{cacheid}', htmlspecialchars(urlencode($record_logs['cache_id']), ENT_COMPAT, 'UTF-8'), $tmp_log);
            $tmp_log = mb_ereg_replace('{logid}', htmlspecialchars(urlencode($record_logs['id']), ENT_COMPAT, 'UTF-8'), $tmp_log);

            $logtext = '<b>' . htmlspecialchars($record_logs['user_name']) . '</b>:<br>';
            $logtext .= GeoCacheLog::cleanLogTextForToolTip( $record_logs['log_text'] );
            $tmp_log = mb_ereg_replace('{logtext}', $logtext, $tmp_log);

            $content .= "\n" . $tmp_log;
        }
        $content .= '</ul></div>';
        XDb::xFreeResults($rs_logs);
    }

    //          }
    // ----------- end owner section
    //          $content .='<p>&nbsp;</p><div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;<img src="/images/blue/event.png" class="icon32" alt="Caches Find" title="Caches Find">&nbsp;&nbsp;&nbsp;Odwiedzone województwa podczas poszukiwań (w przygotowaniu)</p></div><p><img src="images/PLmapa250.jpg" alt=""></p>';
}
StopWatch::click(__LINE__);

//------------ end find section
//------------ begin created caches ---------------------------
$content .= '<p>&nbsp;</p><div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;<img src="/images/blue/cache.png" class="icon32" alt="Caches created" title="Caches created">&nbsp;&nbsp;&nbsp;' . tr('stat_created_caches') . '</p></div><br>';

if ($user->getHiddenGeocachesCount() == 0) {
    $content .= '<br><p> <b>' . tr('not_caches_created') . '</b></p>';
} else {

    // nie licz spotkan, skrzynek jeszcze nieaktywnych, zarchiwizowanych i wstrzymanych
    $hidden = XDb::xMultiVariableQueryValue(
        "SELECT COUNT(*) FROM caches
        WHERE user_id=:1 AND status <> 2 AND status <> 3 AND status <> 4
            AND status <> 5 AND status <> 6 AND type <> 6",
        0, $user_id);

    StopWatch::click(__LINE__);

    $hidden_event = XDb::xMultiVariableQueryValue(
        "SELECT COUNT(*) FROM caches WHERE user_id= :1 AND status <> 4 AND status <> 5 AND status <> 6 AND type=6",
        0, $user_id);

    StopWatch::click(__LINE__);

    $rscc2 = XDb::xSql(
        "SELECT cache_id, wp_oc, DATE_FORMAT(date_created,'%d-%m-%Y') data FROM caches
        WHERE status <> 4 AND status <> 5 AND status <> 6 AND user_id= ?
        GROUP BY YEAR(`date_created`), MONTH(`date_created`), DAY(`date_created`)
        ORDER BY YEAR(`date_created`) DESC, MONTH(`date_created`) DESC, DAY(`date_created`) DESC, HOUR(`date_created`) DESC LIMIT 1", $user_id);
    $rcc2 = XDb::xFetchArray($rscc2);

    StopWatch::click(__LINE__);

    $rcNumber = XDb::xMultiVariableQueryValue(
        "SELECT COUNT(*) number FROM caches
        WHERE status <> 4 AND status <> 5 AND user_id= :1
        GROUP BY YEAR(`date_created`), MONTH(`date_created`), DAY(`date_created`)
        ORDER BY number DESC LIMIT 1", 0, $user_id);

    $num_rows = XDb::xMultiVariableQueryValue(
        "SELECT COUNT(*) FROM (
            SELECT COUNT(*) FROM caches
            WHERE status <> 2 AND status <> 3 AND status <> 5 AND status <> 4 AND user_id= :1
            GROUP BY YEAR(`date_created`), MONTH(`date_created`), DAY(`date_created`)
        )AS COUNTS_IN_DAYS", 0, $user_id);

    if ($num_rows>0)
        $aver2 = round(($user->getHiddenGeocachesCount() / $num_rows), 2);
    else
        $aver2 = 0;

    StopWatch::click(__LINE__);

    // total owned
    $total_owned_caches = $database->multiVariableQueryValue(
            "select count(cache_id) from caches where user_id = :1 and status in (1,2,3) and type not in (6)", 0, $user_id);
    // total adopted
    $total_owned_caches_adopted = $database->multiVariableQueryValue(
            "select count(cache_id) from caches where user_id = :1 and org_user_id <> user_id and status in (1,2,3) and type not in (6)", 0, $user_id);
    // created and owned
    $total_created_and_owned_caches = $database->multiVariableQueryValue(
            "select count(cache_id) from caches where user_id = :1 and (org_user_id = user_id or org_user_id is null) and status in (1,2,3) and type not in (6)", 0, $user_id);
    // created, but given to adoption
    $total_created_caches_adopted = $database->multiVariableQueryValue(
            "select count(cache_id) from caches where org_user_id = :1 and org_user_id <> user_id and status in (1,2,3) and type not in (6)", 0, $user_id);
    $total_created_caches = $total_created_and_owned_caches + $total_created_caches_adopted;

    $content .= '<p><span class="content-title-noshade txt-blue08">' . tr('total_owned_caches') . ':  </span><strong>' . $total_owned_caches . '</strong>';
    if ($total_owned_caches == 0) {
        $content .= '</p>';
    } else {
        $content .= '&nbsp;&nbsp;&nbsp;<img src="/images/blue/arrow.png" alt=""> [<a class="links" href="search.php?showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bycreated&amp;ownerid=' . $user_id . '&amp;cachetype=1111101111&amp;searchbyowner=&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0">' . tr('show') . '</a>]</p>';
        if ($total_owned_caches_adopted > 0) {
            $content .= '<p><span class="content-title-noshade txt-blue08">' . tr('total_owned_caches_adopted') . ':  </span><strong>' . $total_owned_caches_adopted . '</strong></p>';
        }
    }
    if ($total_created_caches > 0 && ($total_owned_caches_adopted > 0 or $total_created_caches_adopted > 0)) {
        $content .= '<p><span class="content-title-noshade txt-blue08">' . tr('total_created_caches') . ':  </span><strong>' . $total_created_caches . '</strong></p>';
        if ($total_created_caches_adopted > 0) {
            $content .= '<p><span class="content-title-noshade txt-blue08">' . tr('total_created_caches_adopted') . ':  </span><strong>' . $total_created_caches_adopted . '</strong></p>';
        }
    }

    $content .= '<p><span class="content-title-noshade txt-blue08">' . tr('total_of_active_caches') . ':  </span><strong>' . $hidden . '</strong>';
    if ($hidden == 0) {
        $content .= '</p>';
    } else {
        $content .= '&nbsp;&nbsp;&nbsp;<img src="/images/blue/arrow.png" alt=""> [<a class="links" href="search.php?showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bycreated&amp;ownerid=' . $user_id . '&amp;cachetype=1111101111&amp;searchbyowner=&amp;f_inactive=1&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0">' . tr('show') . '</a>]</p>';
    }

    StopWatch::click(__LINE__);

    $hidden_temp = XDb::xMultiVariableQueryValue(
        "SELECT COUNT(*) FROM `caches` WHERE status=2 AND `user_id`= :1", 0, $user_id);

    $content .= '<p><span class="content-title-noshade txt-blue08">' . tr('number_temp_caches') . ':  </span><strong>' . $hidden_temp . '</strong></p>';

    $hidden_arch = XDb::xMultiVariableQueryValue(
        "SELECT COUNT(*) FROM `caches` WHERE status=3 AND type <> 6 AND `user_id`= :1 ", 0, $user_id);

    $content .= '<p><span class="content-title-noshade txt-blue08">' . tr('number_archived_caches') . ': </span><strong>' . $hidden_arch . '</strong></p>';

    $content .= '<p><span class="content-title-noshade txt-blue08">' . tr('number_created_events') . ':  </span><strong>' . $hidden_event . '</strong>';
    if ($hidden_event == 0) {
        $content .= '</p>';
    } else {
        $content .= '&nbsp;&nbsp;&nbsp;<img src="/images/blue/arrow.png" alt=""> [<a class="links" href="search.php?searchto=searchbyowner&amp;showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bycreated&amp;ownerid=' . $user_id . '&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;f_geokret=0&amp;country=&amp;cachetype=0000010000">' . tr('show') . '</a>]</p>';
    }

    StopWatch::click(__LINE__);

    $recomendr = XDb::xMultiVariableQueryValue(
        "SELECT COUNT(*) FROM `cache_rating`, caches
        WHERE `cache_rating`.`cache_id`=`caches`.`cache_id` AND caches.type <> 6
            AND `caches`.`user_id`= :1 ", 0, $user_id);

    $recommend_caches = XDb::xMultiVariableQueryValue(
        "SELECT COUNT(*) FROM caches
        WHERE `caches`.`topratings` >= 1 AND caches.type <> 6 AND `caches`.`user_id`= :1 ", 0, $user_id);

    if ($recomendr != 0) {
        $ratio = sprintf("%u", ($recommend_caches / $total_owned_caches) * 100);
        $content .= '<p><span class="content-title-noshade txt-blue08">' . tr('number_obtain_recommendations') . ':</span> <strong>' . $recomendr . '</strong> ' . tr('for') . ' <strong>' . $recommend_caches . '</strong> ' . tr('_caches_') . ' &nbsp;&nbsp;&nbsp;<img src="/images/blue/arrow.png" alt=""> [<a class="links" href="search.php?showresult=1&amp;expert=0&amp;output=HTML&amp;cachetype=1111101111&amp;sort=bycreated&amp;ownerid=' . $user_id . '&amp;searchbyowner=&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;cacherating=1">' . tr('show') . '</a>]</p>
                     <p><span class="content-title-noshade txt-blue08">' . tr('ratio_recommendations') . ':</span> <strong>' . $ratio . '%</strong></p>';
    }

    StopWatch::click(__LINE__);

    $numberGK_in_caches = XDb::xMultiVariableQueryValue(
        "SELECT count(*) FROM gk_item, gk_item_waypoint, caches
        WHERE gk_item_waypoint.wp = caches.wp_oc
            AND gk_item.id = gk_item_waypoint.id AND gk_item.stateid <> 1
            AND gk_item.stateid <> 4 AND gk_item.stateid <> 5 AND gk_item.typeid <> 2
            AND `caches`.`user_id`= :1 ", 0, $user_id);

    if ($numberGK_in_caches != 0) {
        $content .= '<p><span class="content-title-noshade txt-blue08">' . tr('number_gk_in_caches') . ':</span> <strong>' . $numberGK_in_caches . '</strong></p>';
        $content .= '<p><span class="content-title-noshade txt-blue08">' . tr('days_caching') . ':</span> <strong>' . $num_rows . '</strong> ' . tr('from_total_days') . ': <strong>' . $ddays['diff'] . '</strong></p>';
    }

    $content .= '<p><span class="content-title-noshade txt-blue08">' . tr('average_caches') . ':</span> <strong>' . sprintf("%u", $aver2) . '</strong></p>';

    $content .= '<p><span class="content-title-noshade txt-blue08">' . tr('most_caches_made_day') . ':</span> <strong>' . sprintf("%u", $rcNumber) . '</strong></p>';
    $content .= '<p><span class="content-title-noshade txt-blue08">' . tr('latest_created_cache') . ':</span>&nbsp;&nbsp;<strong><a class="links" href="viewcache.php?cacheid=' . $rcc2['cache_id'] . '">' . $rcc2['wp_oc'] . '</a>&nbsp;&nbsp;</strong>(' . $rcc2['data'] . ')</p>';

    if ( $total_created_and_owned_caches >= 10 ) {
        $content .= '<br><table style="border-collapse: collapse; font-size: 110%;" width="250" border="1"><tr><td colspan="3" align="center" bgcolor="#DBE6F1"><b>' . tr('milestones') . '</b></td> </tr><tr><td bgcolor="#EEEDF9"><b> Nr </b></td> <td bgcolor="#EEEDF9"><b>'.tr('date').'</b></td> <td bgcolor="#EEEDF9"><b>'.tr('cache').'</b> </td> </tr>';

        if ( $total_created_and_owned_caches > 101 ) {
            $milestone = 100;
        } else {
            $milestone = 10;
        }

        /*
         * I don't know why - probably this is a bug,
         * but without unset($rsms) query below don't return any results
         */
        unset($rsms);
        $rsms = XDb::xSql(
            "SET @r = 1;
            SELECT * FROM
            (
                SELECT *,@r:=@r+1 row FROM (

                    SELECT cache_id, wp_oc, DATE_FORMAT(date_created,'%d-%m-%Y') data
                    FROM caches
                    WHERE user_id= ? AND status <> 4 AND status <> 5 AND status <> 6 AND type <> 6
                    ORDER BY
                        YEAR(`date_created`) ASC,
                        MONTH(`date_created`) ASC,
                        DAY(`date_created`) ASC,
                        HOUR(`date_created`) ASC

                ) B
            ) A
            WHERE row=2 OR row % $milestone =1 ORDER BY row ASC", $user_id);

        $rsms->nextRowset(); //to switch to second query results :)
        while( $rms = XDb::xFetchArray($rsms)) {
            $content .= '<tr> <td>' . ($rms['row']-1) . '</td><td>' . $rms['data'] . '</td><td><a class="links" href="viewcache.php?cacheid=' . $rms['cache_id'] . '">' . $rms['wp_oc'] . '</a></td></tr>';
        }

        $content .= '</table>';
        XDb::xFreeResults($rsms);
    } //$total_created_and_owned_caches > 0

    XDb::xFreeResults($rscc2);

    StopWatch::click(__LINE__);

    $rs_logs = XDb::xSql(
        "SELECT cache_logs.id, cache_logs.cache_id AS cache_id, cache_logs.type AS log_type,
                cache_logs.text AS log_text, DATE_FORMAT(cache_logs.date,'%d-%m-%Y') AS log_date,
                caches.name AS cache_name, caches.wp_oc AS wp_name,
                caches.user_id AS cache_owner, cache_logs.user_id AS luser_id, user.username AS user_name,
                user.user_id AS user_id, caches.type AS cache_type, cache_type.icon_small AS cache_icon_small,
                log_types.icon_small AS icon_small, IF(ISNULL(`cache_rating`.`cache_id`), 0, 1) AS `recommended`,
                COUNT(gk_item.id) AS geokret_in
        FROM ((cache_logs
            INNER JOIN caches ON (caches.cache_id = cache_logs.cache_id)))
            INNER JOIN user ON (cache_logs.user_id = user.user_id)
            INNER JOIN log_types ON (cache_logs.type = log_types.id)
            INNER JOIN cache_type ON (caches.type = cache_type.id)
            LEFT JOIN `cache_rating` ON `cache_logs`.`cache_id`=`cache_rating`.`cache_id`
                AND `cache_logs`.`user_id`=`cache_rating`.`user_id`
            LEFT JOIN gk_item_waypoint ON gk_item_waypoint.wp = caches.wp_oc
            LEFT JOIN gk_item ON gk_item.id = gk_item_waypoint.id
                AND gk_item.stateid<>1 AND gk_item.stateid<>4 AND gk_item.typeid<>2 AND gk_item.stateid !=5
            WHERE (caches.status=1 OR caches.status=2 OR caches.status=3) AND cache_logs.deleted=0 AND `caches`.`user_id`= ?
                AND `cache_logs`.`cache_id`=`caches`.`cache_id`
                AND `user`.`user_id`=`cache_logs`.`user_id`
            GROUP BY cache_logs.id
            ORDER BY `cache_logs`.`date_created` DESC
            LIMIT 5", $user_id);

    if (XDb::xNumRows($rs_logs) != 0) {
        $content .= '<p>&nbsp;</p><p><span class="content-title-noshade txt-blue08">' . tr('latest_logs_in_caches') . ':</span> <img src="/images/blue/arrow.png" alt=""> [<a class="links" href="mycaches_logs.php?userid=' . $user_id . '">' . tr('show_all') . '</a>] ';
        $content .= ' <a class="links" href="/rss/mycaches_logs.xml?userid=' . $user_id . '"><img src="/images/misc/rss.svg" class="icon16" alt="RSS icon"></a>';

        $content .= '</p><br><div><ul style="margin: -0.9em 0px 0.9em 0px; padding: 0px 0px 0px 10px; list-style-type: none; line-height: 1.6em; font-size: 12px;">';

        while ($record_logs = XDb::xFetchArray($rs_logs)) {

            $tmp_log = $cache_line_my_caches;

            if ($record_logs['geokret_in'] != '0') {
                $tmp_log = mb_ereg_replace('{gkimage}', '<img src="images/gk.png" border="0" alt="" title="GeoKret">', $tmp_log);
            } else {
                $tmp_log = mb_ereg_replace('{gkimage}', '<img src="images/rating-star-empty.png" border="0" alt="">', $tmp_log);
            }

            if ($record_logs['recommended'] == 1 && $record_logs['log_type'] == 1) {
                $tmp_log = mb_ereg_replace('{rateimage}', '<img src="images/rating-star.png" border="0" alt="">', $tmp_log);
            } else {
                $tmp_log = mb_ereg_replace('{rateimage}', '<img src="images/rating-star-empty.png" border="0" alt="">', $tmp_log);
            }
            $tmp_log = mb_ereg_replace('{logimage}', icon_log_type($record_logs['icon_small'], "..."), $tmp_log);
            $tmp_log = mb_ereg_replace('{cacheimage}', $record_logs['cache_icon_small'], $tmp_log);
            $tmp_log = mb_ereg_replace('{date}', $record_logs['log_date'], $tmp_log);
            $tmp_log = mb_ereg_replace('{cachename}', htmlspecialchars($record_logs['cache_name'], ENT_COMPAT, 'UTF-8'), $tmp_log);
            $tmp_log = mb_ereg_replace('{wpname}', htmlspecialchars($record_logs['wp_name'], ENT_COMPAT, 'UTF-8'), $tmp_log);
            $tmp_log = mb_ereg_replace('{cacheid}', htmlspecialchars($record_logs['cache_id'], ENT_COMPAT, 'UTF-8'), $tmp_log);

            // ukrywanie nicka autora komentarza COG przed zwykłym userem
            // (Łza)
            if (($record_logs['log_type'] == 12) && (!$loggedUser->hasOcTeamRole())) {
                $record_logs['user_name'] = 'Centrum Obsługi Geocachera';
                $record_logs['user_id'] = 0;
            }
            // koniec ukrywania nicka autora komentarza COG

            $tmp_log = mb_ereg_replace('{userid}', htmlspecialchars($record_logs['user_id'], ENT_COMPAT, 'UTF-8'), $tmp_log);
            $tmp_log = mb_ereg_replace('{username}', htmlspecialchars($record_logs['user_name'], ENT_COMPAT, 'UTF-8'), $tmp_log);
            $tmp_log = mb_ereg_replace('{logid}', htmlspecialchars(urlencode($record_logs['id']), ENT_COMPAT, 'UTF-8'), $tmp_log);

            $logtext = '<b>' . htmlspecialchars($record_logs['user_name'], ENT_COMPAT) . '</b>:<br>';
            $logtext .= GeoCacheLog::cleanLogTextForToolTip( $record_logs['log_text'] );

            $tmp_log = mb_ereg_replace('{logtext}', $logtext, $tmp_log);

            $content .= "\n" . $tmp_log;
        }
        XDb::xFreeResults($rs_logs);
        $content .= '</ul></div><br>';
    }
}

StopWatch::click(__LINE__);

//  ----------------- begin  owner section  ----------------------------------
if ($user_id == $loggedUser->getUserId() || $loggedUser->hasOcTeamRole()) {
    $rscheck = XDb::xMultiVariableQueryValue(
        "SELECT count(*) FROM caches
        WHERE (status = 4 OR status = 5 OR status = 6) AND `user_id`= :1", 0, $user_id);

    if ($rscheck != 0) {
        $content .= '<br><div class="content-title-noshade box-blue">';
    }

    //get not published caches DATE_FORMAT(`caches`.`date_activate`,'%d-%m-%Y'),

    $geocachesNotPublished = $user->getGeocachesNotPublished();

    if ($geocachesNotPublished->count() > 0) {
        $content .= '<p><span class="content-title-noshade txt-blue08">' . tr('not_yet_published') . ':</span></p><br><div><ul style="margin: -0.9em 0px 0.9em 0px; padding: 0px 0px 0px 10px; list-style-type: none; line-height: 1.2em; font-size: 115%;">';
        foreach ($geocachesNotPublished as $geocache) {
            $content .= "\n" . buildGeocacheHtml($geocache, $cache_notpublished_line);
        }
        $content .= '</ul></div>';
    }

    $waitAproveGeocaches = $user->getGeocachesWaitApprove();
    if ($waitAproveGeocaches->count() > 0) {
        $content .= '<br><p><span class="content-title-noshade txt-blue08">' . tr('caches_waiting_approve') . ':</span></p><br><div><ul style="margin: -0.9em 0px 0.9em 0px; padding: 0px 0px 0px 10px; list-style-type: none; line-height: 1.2em; font-size: 115%;">';
        foreach ($waitAproveGeocaches as $geocache) {
            $content .= "\n" . buildGeocacheHtml($geocache, $cache_line);
        }
        $content .= '</ul></div>';
    }

    $geocachesBlocked = $user->getGeocachesBlocked();
    if ($geocachesBlocked->count() > 0) {
        $content .= '<br><p><span class="content-title-noshade txt-blue08">' . tr('caches_blocked') . ':</span></p><br><div><ul style="margin: -0.9em 0px 0.9em 0px; padding: 0px 0px 0px 10px; list-style-type: none; line-height: 1.2em; font-size: 115%;">';
        foreach ($geocachesBlocked as $geocache) {
            $content .= "\n" . buildGeocacheHtml($geocache, $cache_line);
        }
        $content .= '</ul></div>';
    }

    if ($rscheck != 0) {
        $content .= '</div>';
    }
}
// ------------------ end owner section ---------------------------------
//------------ end created caches section ------------------------------
StopWatch::click(__LINE__);

tpl_set_var('content', $content);

$view->setVar('infoMsg', $infoMsg);
$view->setVar('errorMsg', $errorMsg);

StopWatch::displayResults();
$view->buildView();



/**
 * generate html string displaying geoPaths completed by user (power trail) medals
 * @author Andrzej Łza Woźniak, 2013-11-23
 */
function buildPowerTrailIcons(ArrayObject $powerTrails)
{
    $allowedPtStatus = array(
        PowerTrail::STATUS_OPEN, PowerTrail::STATUS_INSERVICE, PowerTrail::STATUS_CLOSED
    );
    $result = '<table width="100%"><tr><td>';
    /* @var $powertrail PowerTrail */
    foreach ($powerTrails as $powertrail) {
        if (in_array($powertrail->getStatus(), $allowedPtStatus)) {
            $result .= '<div class="ptMedal"><table style="padding-top: 7px;" align="center" height="51" width="51"><tr><td width=52 height=52 valign="center" align="center"><a title="' . $powertrail->getName() . '" href="powerTrail.php?ptAction=showSerie&ptrail=' . $powertrail->getId() . '"><img class="imgPtMedal" src="' . $powertrail->getImage() . '"></a></td></tr><tr><td align="center"><img src="' . $powertrail->getFootIcon() . '"></td></tr></table></div><div class="ptMedalSpacer"></div>';
        }
    }
    return $result . '</td></tr></table><br><br>';
}

function buildGeocacheHtml(GeoCache $geocache, $html)
{
    $ocConfig = OcConfig::instance();
    $html = mb_ereg_replace('{cacheimage}', '<img src="'.$geocache->getCacheIcon().'" width="16">', $html);
    $html = mb_ereg_replace('{cachestatus}', htmlspecialchars(tr($geocache->getStatusTranslationKey()), ENT_COMPAT, 'UTF-8'), $html);
    $html = mb_ereg_replace('{cacheid}', htmlspecialchars(urlencode($geocache->getCacheId()), ENT_COMPAT, 'UTF-8'), $html);
    if ($geocache->getDateActivate() === null) {
        $html = mb_ereg_replace('{date}', tr('no_time_indicated'), $html);
    } else {
        $html = mb_ereg_replace('{date}', Formatter::date($geocache->getDateActivate()), $html);
    }
    $html = mb_ereg_replace('{cachename}', htmlspecialchars($geocache->getCacheName(), ENT_COMPAT, 'UTF-8'), $html);
    $html = mb_ereg_replace('{wpname}', htmlspecialchars($geocache->getWaypointId(), ENT_COMPAT, 'UTF-8'), $html);
    return $html;
}


function buildMeritBadges($user_id) {

$meritBadgeCtrl = new MeritBadgeController();
$userCategories = $meritBadgeCtrl->buildArrayUserCategories($user_id);

$content_badge_rows = '';
$content = '';

foreach ($userCategories as $oneCategory) {

    $badgesInCategory = $meritBadgeCtrl->buildArrayUserMeritBadgesInCategory( $user_id, $oneCategory->getId() );

    $content_badge = '';

    foreach ($badgesInCategory as $oneBadge) {

        $short_desc = MeritBadge::prepareShortDescription(  $oneBadge->getOBadge()->getShortDescription(),
                                                            $oneBadge->getNextVal(),
                                                            $oneBadge->getCurrVal());

        $short_desc = mb_ereg_replace( "'", "\\'", $short_desc);

        $element='<div class="Badge-div-element-small">
        <a href="badge.php?badge_id={badge_id}&user_id={user_id}" onmouseover="Tip(\'{content_tip}\', PADDING,10)" onmouseout="UnTip()">
            <div class="Badge-pie-progress-small" role="progressbar" data-goal="{progresbar_curr_val}" data-trackcolor="#d9d9d9" data-barcolor="{progresbar_color}" data-barsize="{progresbar_size}" aria-valuemin="0" aria-valuemax="{progresbar_next_val}">
            <div class="pie_progress__content"><img src="{picture}" class="Badge-pic-small" /><br>
            </div>
            </div>
        </a>
        </div>';

        $element=mb_ereg_replace('{content_tip}',
            "<div style =\'width:500px;\'><img src=\'{picture}\' style= \'float: left;margin-right:20px;\' /> \\
             <p style=\'font-size:20px; font-weight:bold;\'> {name} <br>\\
             <span style=\'font-size:13px; font-weight:normal; font-style:italic;\'> {short_desc} </span></p> \\
             <p style=\'font-size:13px;font-weight:normal;\'>\\"
            .tr('merit_badge_level_name').": <b>{level_name}</b><br>\\"
            .tr('merit_badge_number').": <b>{curr_val}</b><br>\\"
            .tr('merit_badge_next_level_threshold').": <b>{next_val}</b><br>\\
             </p></div>", $element);
        $element=mb_ereg_replace('{name}', $oneBadge->getOBadge()->getName(), $element);
        $element=mb_ereg_replace('{short_desc}', $short_desc , $element);
        $element=mb_ereg_replace('{picture}', $oneBadge->getPicture(), $element );
        $element=mb_ereg_replace('{level_name}', $oneBadge->getOLevel()->getLevelName(), $element );
        $element=mb_ereg_replace('{badge_id}', $oneBadge->getBadgeId(), $element );
        $element=mb_ereg_replace('{user_id}', $user_id, $element );
        $element=mb_ereg_replace('{curr_val}', $oneBadge->getCurrVal(), $element );
        $element=mb_ereg_replace('{progresbar_curr_val}', MeritBadge::getProgressBarCurrValue($oneBadge->getOLevel()->getPrevThreshold(), $oneBadge->getCurrVal(), $oneBadge->getNextVal()), $element );
        $element=mb_ereg_replace('{progresbar_next_val}', MeritBadge::getProgressBarValueMax($oneBadge->getOLevel()->getPrevThreshold(), $oneBadge->getNextVal()), $element );
        $element=mb_ereg_replace('{progresbar_size}', MeritBadge::getBarSize( $oneBadge->getLevelId(),  $oneBadge->getOBadge()->getLevelsNumber() ), $element );
        $element=mb_ereg_replace('{progresbar_color}', MeritBadge::getColor( $oneBadge->getLevelId(), $oneBadge->getOBadge()->getLevelsNumber() ), $element );
        $element=mb_ereg_replace('{next_val}', MeritBadge::prepareTextThreshold($oneBadge->getNextVal()), $element );

        $content_badge.= $element;
    }

    $content_badge_rows .= mb_ereg_replace('{content_badge}', $content_badge,
        "<tr class='Badge-table-view'><td><span class='Badge-category-small'>{category_table}</span><br>{content_badge}</tr></td>");

    $content_badge_rows = mb_ereg_replace('{category_table}', $oneCategory->getName(), $content_badge_rows);
}

$content .= mb_ereg_replace('{content_badge_rows}', $content_badge_rows, '
                <table width="770px">
                    <tbody>
                    {content_badge_rows}
                    </tbody>
                </table>
                <br>');
$content .= "<a class='links'  href='user_badges.php?user_id=$user_id'>[".tr('merit_badge_show_details')."]</a>&nbsp;&nbsp;&nbsp;&nbsp;";
$content .= "<a class='links'  href='user_badges.php?user_id=999999'>[".tr('merit_badge_show_list')."]</a><br><br>";
return $content;
}



function buildOpenCloseButton($userid, $check, $pic, $field, $txt, $title) {
$content = "<form action='/viewprofile.php' style='display:inline;'>";

$content .= "<div class='content2-container bg-blue02'>
                                <table style='width: 100%; padding: 5px;'><tr><td>
                                <p class='content-title-noshade-size1'>
                                <img src='/images/blue/$pic' width='33' class='icon32' alt='$title' title='$title'>&nbsp;$txt".
                                "</p></td>";

$content .= "<td style='text-align: right'>
            <button type='submit' class='btn btn-primary btn-sm'>";

if ($check == 1) $content .= "&nbsp;-&nbsp;"; else $content .= "&nbsp;+&nbsp;";
$content .= "</td></tr></table>";
$content .= "
<input type='hidden' name='userid' value='$userid' >
<input type='hidden' name='save' value='true' >
<input type='hidden' name='$field' value='$check'>
</div></form>";

return $content;
}
