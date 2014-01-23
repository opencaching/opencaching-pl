<?php
/***************************************************************************
    *
    *   This program is free software; you can redistribute it and/or modify
    *   it under the terms of the GNU General Public License as published by
    *   the Free Software Foundation; either version 2 of the License, or
    *   (at your option) any later version.
    *
    *  UTF-8 ąść
    ***************************************************************************/

//prepare the templates and include all neccessary
    require_once('./lib/common.inc.php');
        global $stat_menu;
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

    // check for old-style parameters
        if (isset($_REQUEST['userid']))
        {
            $user_id = $_REQUEST['userid'];

        }

                $tplname = 'ustat';
                $stat_menu = array(
                    'title' => 'Statystyka',
                    'menustring' => 'Statystyka',
                    'siteid' => 'statlisting',
                    'navicolor' => '#E8DDE4',
                    'visible' => false,
                    'filename' => 'viewprofile.php?userid='.$user_id,
                    'submenu' => array(
                        array(
                            'title' => tr('generla_stat'),
                            'menustring' => tr('general_stat'),
                            'visible' => true,
                            'filename' => 'viewprofile.php?userid='.$user_id,
                            'newwindow' => false,
                            'siteid' => 'general_stat',
                            'icon' => 'images/actions/stat'
                        ),
                        array(
                            'title' => tr('graph_find'),
                            'menustring' => tr('graph_find'),
                            'visible' => true,
                            'filename' => 'ustatsg2.php?userid='.$user_id,
                            'newwindow' => false,
                            'siteid' => 'findstat',
                            'icon' => 'images/actions/stat'
                        )
                    )
                );


    $content="";

    $rsGeneralStat =sql("SELECT hidden_count, founds_count, log_notes_count, notfounds_count, username FROM `user` WHERE user_id=&1 ",$user_id);

            $user_record = sql_fetch_array($rsGeneralStat);
            tpl_set_var('username',$user_record['username']);
        if ($user_record['hidden_count'] == 0) {
            $content .= '<p>&nbsp;</p><p>&nbsp;</p><div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;<img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="Caches created" title="Caches created" />&nbsp;&nbsp;&nbsp;'.tr("graph_created").'</p></div><br /><br /><p> <b>'.tr("there_is_no_caches_registered").'</b></p>';
                          }
                          else
                          {


    // calculate diif days between date of register on OC  to current date
      $rdd=sql("select TO_DAYS(NOW()) - TO_DAYS(`date_created`) `diff` from `user` WHERE user_id=&1 ",$user_id);
      $ddays = mysql_fetch_array($rdd);
      mysql_free_result($rdd);
      // calculate days caching
     // sql ("SELECT COUNT(*) FROM cache_logs WHERE type=1 AND user_id=&1 GROUP BY GROUP BY YEAR(`date_created`), MONTH(`date_created`), DAY(`date_created`)",$user_id);

    $rsGeneralStat =sql("SELECT YEAR(`date_created`) usertime, hidden_count, founds_count, log_notes_count, username FROM `user` WHERE user_id=&1 ",$user_id);
    if ($rsGeneralStat !== false){
            $user_record = sql_fetch_array($rsGeneralStat);

            tpl_set_var('username',$user_record['username']);
}
            $content .='<p>&nbsp;</p><p>&nbsp;</p><div class="content2-container bg-blue02"><p class="content-title-noshade-size1">&nbsp;<img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="Caches created" title="Caches created" />&nbsp;&nbsp;&nbsp;'.tr("graph_created").'</p></div><br />';
            $content .= '<p><img src="graphs/PieGraphustat.php?userid=' . $user_id . '&amp;t=cc' . '" border="0" alt="" width="500" height="300" /></p>';

    $year=date("Y");
    $rsCreateCachesMonth = sql("SELECT COUNT(*) `count`, MONTH(`date_created`) `month`, YEAR(`date_created`) `year` FROM `caches` WHERE YEAR(`date_created`)=&1 AND status <> 4 AND status <> 5 AND user_id=&2 GROUP BY MONTH(`date_created`), YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC, MONTH(`date_created`) ASC",$year,$user_id);

                if ($rsCreateCachesMonth !== false) {
//          while ($rccm = mysql_fetch_array($rsCreateCachesYear)){


        $content .= '<p><img src="graphs/BarGraphustat.php?userid=' . $user_id . '&amp;t=ccm' . $year . '" border="0" alt="" width="500" height="200" /></p>';
        if ($user_record['usertime'] != $year){
        $yearr= $year-1;
        $content .= '<p><img src="graphs/BarGraphustat.php?userid=' . $user_id . '&amp;t=ccm' . $yearr . '" border="0" alt="" width="500" height="200" /></p>';
                    }
//              }
        }
            mysql_free_result($rsGeneralStat);
            mysql_free_result($rsCreateCachesMonth);
    $rsCreateCachesYear= sql("SELECT COUNT(*) `count`,YEAR(`date_created`) `year` FROM `caches` WHERE status <> 4 AND status <> 5 AND user_id=&1 GROUP BY YEAR(`date_created`) ORDER BY YEAR(`date_created`) ASC",$user_id);

    if ($rsCreateCachesYear !== false){
    $content .= '<p><img src="graphs/BarGraphustat.php?userid=' . $user_id . '&amp;t=ccy" border="0" alt="" width="500" height="200" /></p>';

    }

            mysql_free_result($rsCreateCachesYear);
            }
            tpl_set_var('content',$content);
    }
}
    tpl_BuildTemplate();
?>
