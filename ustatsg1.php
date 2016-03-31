<?php

use Utils\Database\XDb;

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');
global $stat_menu;
//Preprocessing
if ($error == false) {

    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
    } else {

        // check for old-style parameters
        if (isset($_REQUEST['userid'])) {
            $user_id = $_REQUEST['userid'];
        }
        if ($user_id != $usr['userid']) {
            // do not highlight My stats menu item if browsing other users stats
            $mnu_siteid = 'start';
        }

        $tplname = 'ustat';
        $stat_menu = array(
            'title' => 'Statystyka',
            'menustring' => 'Statystyka',
            'siteid' => 'statlisting',
            'navicolor' => '#E8DDE4',
            'visible' => false,
            'filename' => 'viewprofile.php?userid=' . $user_id,
            'submenu' => array(
                array(
                    'title' => tr('generla_stat'),
                    'menustring' => tr('general_stat'),
                    'visible' => true,
                    'filename' => 'viewprofile.php?userid=' . $user_id,
                    'newwindow' => false,
                    'siteid' => 'general_stat',
                    'icon' => 'images/actions/stat'
                ),
                array(
                    'title' => tr('graph_find'),
                    'menustring' => tr('graph_find'),
                    'visible' => true,
                    'filename' => 'ustatsg2.php?userid=' . $user_id,
                    'newwindow' => false,
                    'siteid' => 'findstat',
                    'icon' => 'images/actions/stat'
                )
            )
        );


        $content = '';

        $rsGeneralStat = XDb::xSql(
            "SELECT hidden_count, founds_count, log_notes_count, notfounds_count, username
            FROM `user` WHERE user_id=? ", $user_id);

        $user_record = XDb::xFetchArray($rsGeneralStat);
        tpl_set_var('username', $user_record['username']);
        if ($user_record['hidden_count'] == 0) {
            $content .= '<p>&nbsp;</p><p>&nbsp;</p><div class="content2-container bg-blue02">
                         <p class="content-title-noshade-size1">
                        &nbsp;<img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="Caches created" title="Caches created" />
                        &nbsp;&nbsp;&nbsp;' . tr("graph_created") . '</p></div><br /><br />
                        <p><b>' . tr("there_is_no_caches_registered") . '</b></p>';
        } else {


            // calculate diif days between date of register on OC  to current date
            $rdd = XDb::xSql(
                "SELECT TO_DAYS(NOW()) - TO_DAYS(`date_created`) `diff` from `user`
                WHERE user_id= ? ", $user_id);

            $ddays = XDb::xFetchArray($rdd);
            XDb::xFreeResults($rdd);

            // calculate days caching
            $rsGeneralStat = XDb::xSql(
                "SELECT YEAR(`date_created`) usertime, hidden_count, founds_count, log_notes_count, username
                FROM `user` WHERE user_id= ? ", $user_id);

            if ($rsGeneralStat !== false) {
                $user_record = XDb::xFetchArray($rsGeneralStat);

                tpl_set_var('username', $user_record['username']);
            }
            $content .='<p>&nbsp;</p><p>&nbsp;</p><div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;<img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="Caches created" title="Caches created" />&nbsp;&nbsp;&nbsp;' . tr("graph_created") . '</p></div><br />';
            $content .= '<p><img src="graphs/PieGraphustat.php?userid=' . $user_id . '&amp;t=cc' . '" border="0" alt="" width="500" height="300" /></p>';

            $year = date("Y");

            $content .= '<p><img src="graphs/BarGraphustat.php?userid=' . $user_id . '&amp;t=ccm' . $year . '" border="0" alt="" width="500" height="200" /></p>';
            if ($user_record['usertime'] != $year) {
                $yearr = $year - 1;
                $content .= '<p><img src="graphs/BarGraphustat.php?userid=' . $user_id . '&amp;t=ccm' . $yearr . '" border="0" alt="" width="500" height="200" /></p>';
            }

            XDb::xFreeResults($rsGeneralStat);

            $content .= '<p><img src="graphs/BarGraphustat.php?userid=' . $user_id . '&amp;t=ccy" border="0" alt="" width="500" height="200" /></p>';

        }
        tpl_set_var('content', $content);
    }
}
tpl_BuildTemplate();

