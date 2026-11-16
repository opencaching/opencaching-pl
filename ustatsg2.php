<?php

use src\Utils\Database\XDb;
use src\Models\OcConfig\OcConfig;
use src\Models\ApplicationContainer;

require_once (__DIR__.'/lib/common.inc.php');


//user logged in?
if (!ApplicationContainer::GetAuthorizedUser()) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);
    exit;
}

// check for old-style parameters
if (isset($_REQUEST['userid'])) {
    $user_id = $_REQUEST['userid'];
}


/** @var View */
$view->setTemplate('ustat');
$view->setVar('userId', $user_id);
$view->setVar('displayCreatedStats', true);


$content = "";

$rsGeneralStat = XDb::xSql(
    "SELECT  hidden_count, founds_count, log_notes_count, notfounds_count, username
    FROM `user` WHERE user_id= ? LIMIT 1", $user_id);

$user_record = XDb::xFetchArray($rsGeneralStat);
tpl_set_var('username', htmlspecialchars($user_record['username']));
if ($user_record['founds_count'] == 0) {
    $content .= '<p>&nbsp;</p><p>&nbsp;</p><div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;<img src="/images/blue/logs.png" class="icon32" alt="Caches Find" title="Caches Find" />&nbsp;&nbsp;&nbsp;' . tr("graph_find") . '</p></div><br /><br /><p> <b>' . tr("there_is_no_caches_found") . '</b></p>';
} else {

    // calculate diif days between date of register on OC  to current date
    $ddays = XDb::xMultiVariableQueryValue(
        "SELECT TO_DAYS( NOW() ) - TO_DAYS(`date_created`) `diff` FROM `user` WHERE user_id= :1 LIMIT 1 ", 0, $user_id);

    $rsGeneralStat = XDb::xSql(
        "SELECT YEAR(`date_created`) usertime,hidden_count, founds_count, log_notes_count, username
        FROM `user` WHERE user_id= ? LIMIT 1", $user_id);
    if ($rsGeneralStat !== false) {
        $user_record = XDb::xFetchArray($rsGeneralStat);
        XDb::xFreeResults($rsGeneralStat);

        tpl_set_var('username', htmlspecialchars($user_record['username']));
    }
    $content .='<p>&nbsp;</p><p>&nbsp;</p><div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;<img src="/images/blue/logs.png" class="icon32" alt="Caches Find" title="Caches Find" />&nbsp;&nbsp;&nbsp;' . tr("graph_find") . '</p></div><br />';
    $content .= '<p><img src="graphs/PieGraphustat.php?userid=' . $user_id . '&amp;t=cf"  border="0" alt="" width="500" height="300" /></p>';

    $year = date("Y");

    $content .= '<p><img src="graphs/BarGraphustat.php?userid=' . $user_id . '&amp;t=cfm' . $year . '"  border="0" alt="" width="500" height="200" /></p>';

    if ($user_record['usertime'] != $year) {
        $yearr = $year - 1;
        $content .= '<p><img src="graphs/BarGraphustat.php?userid=' . $user_id . '&amp;t=cfm' . $yearr . '"  border="0" alt="" width="500" height="200" /></p>';
    }

    $content .= '<p><img src="graphs/BarGraphustat.php?userid=' . $user_id . '&amp;t=cfy"  border="0" alt="" width="500" height="200" /></p>';
}


if (OcConfig::getOcNode() === 'pl') {
    $content .= '<p style="margin-left: 125px;"><img src="graphs/UserCacheMap.php?userid=' . intval($user_id) . '&amp;type=found&amp;rand=' . rand() . '" border="0" alt="" width="250" height="235" /></p>';
}

tpl_set_var('content', $content);

tpl_BuildTemplate();
