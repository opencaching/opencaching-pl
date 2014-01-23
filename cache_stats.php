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
        if (isset($_REQUEST['cacheid']))
        {
            $cache_id = $_REQUEST['cacheid'];

        }

    $tplname = 'cache_stats';


    $content="";

    $cachename=sqlValue("SELECT name FROM caches WHERE `cache_id`=$cache_id", 0);
    tpl_set_var('cachename',$cachename);
    $cachetime=sqlValue("SELECT YEAR(`date_hidden`) FROM caches WHERE `cache_id`=$cache_id", 0);

    $rsGeneralStat =sql("SELECT count(*) count FROM `cache_logs` WHERE cache_logs.deleted=0 AND (type=1 OR type=2) AND cache_id=&1 ",$cache_id);

            $cache_record = sql_fetch_array($rsGeneralStat);
        if ($cache_record['count'] == 0) {
            $content .= '<p>&nbsp;</p><p style="background-color: #FFFFFF; margin: 0px; padding: 0px; color: rgb(88,144,168); font-weight: bold; font-size: 14px;">' .$cachename . '<br /> <br />nie ma jeszcze statystyki</b></p>';
                          }
                          else
                          {

        $content .='<center><p style="background-color: #FFFFFF; margin: 0px; padding: 0px; color: rgb(88,144,168); font-weight: bold; font-size: 14px;">'.tr("stat_geocache").': ' . $cachename . '<br /></p>';
        $content .= '<p style="background-color: #FFFFFF; "><img src="graphs/PieGraphcstat.php?cacheid=' . $cache_id . '"  border="0" alt="Statystyka skrzynki" width="400" height="200" /><br /><br />';

        $year=date("Y");

        $content .= '<img src="graphs/BarGraphcstatM.php?cacheid=' . $cache_id . '&amp;t=csm' . $year . '"  border="0" alt="" width="400" height="200" /><br /><br />';
        if ($cachetime != $year) {
        $yearr = $year -1;
        $content .= '<img src="graphs/BarGraphcstatM.php?cacheid=' . $cache_id . '&amp;t=csm' . $yearr . '"  border="0" alt="" width="400" height="200" /><br /><br />';
        }
        $content .= '<img src="graphs/BarGraphcstat.php?cacheid=' . $cache_id . '&amp;t=csy"  border="0" alt="" width="400" height="200" /><br /><br /><br /></p></center>';
            mysql_free_result($rsGeneralStat);
        }
            tpl_set_var('content',$content);
            tpl_set_var('bodyMod', ' bgcolor="#FFFFFF" style="border:none"');
    }
}
    tpl_BuildTemplate(true, true);
?>