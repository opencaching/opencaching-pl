<?php
/***************************************************************************
                                                                ./newcaches.php
                                                            -------------------
        begin                : Mon June 28 2004
        copyright            : (C) 2004 The OpenCaching Group
        forum contact at     : http://www.opencaching.com/phpBB2

    ***************************************************************************/

/***************************************************************************
    *
    *   This program is free software; you can redistribute it and/or modify
    *   it under the terms of the GNU General Public License as published by
    *   the Free Software Foundation; either version 2 of the License, or
    *   (at your option) any later version.
    *
    ***************************************************************************/

/****************************************************************************

   Unicode Reminder メモ

     include the newcaches HTML file

 ****************************************************************************/

    //prepare the templates and include all neccessary
    require_once('./lib/common.inc.php');
    require_once('./lib/cache_icon.inc.php');

    //Preprocessing
    if ($error == false)
    {
        //get the news
        $tplname = 'newrecomend';
        require('tpl/stdstyle/newrecomend.inc.php');

        $startat = isset($_REQUEST['startat']) ? $_REQUEST['startat'] : 0;
        $startat = $startat + 0;

        $perpage = 500;
        $startat -= $startat % $perpage;

        $content = '';
    $rs = sql('SELECT   `user`.`user_id` `user_id`,
                `user`.`username` `username`,
                `caches`.`cache_id` `cache_id`,
                `caches`.`name` `name`,
                `cache_type`.`icon_large` `icon_large`,
                count(`cache_rating`.`cache_id`) as `anzahl`
            FROM `caches`, `user`, `cache_type`, `cache_rating`
            WHERE `caches`.`user_id`=`user`.`user_id`
              AND `cache_rating`.`cache_id`=`caches`.`cache_id`
              AND `status`=1
              AND `caches`.`type`=`cache_type`.`id`
            GROUP BY `user`.`user_id`, `user`.`username`, `caches`.`cache_id`, `caches`.`name`, `cache_type`.`icon_large`
            ORDER BY `anzahl` DESC, `caches`.`name` ASC
             LIMIT ' . ($startat+0) . ', ' . ($perpage+0));

//      $rs = sql('SELECT `caches`.`cache_id` `cacheid`, `user`.`user_id` `userid`, `caches`.`country` `country`, `caches`.`name` `cachename`, `user`.`username` `username`, `caches`.`date_created` `date_created`, `cache_type`.`icon_large` `icon_large` FROM `caches`, `user`, `cache_type` WHERE `caches`.`user_id`=`user`.`user_id` AND `caches`.`type`=`cache_type`.`id` AND `caches`.`status` != 5 ORDER BY `caches`.`date_created` DESC LIMIT ' . ($startat+0) . ', ' . ($perpage+0));
        while ($r = sql_fetch_array($rs))
        {
            $thisline = $tpl_line;
        $thisline = $cacheline;
        $thisline = mb_ereg_replace('{cacheid}', urlencode($record['cache_id']), $thisline);
        $thisline = mb_ereg_replace('{cachename}', htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8'), $thisline);
        $thisline = mb_ereg_replace('{userid}', urlencode($record['user_id']), $thisline);
        $thisline = mb_ereg_replace('{username}', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'), $thisline);
        $thisline = mb_ereg_replace('{rating_absolute}', $record['anzahl'], $thisline);
        $thisline = mb_ereg_replace('{cacheicon}', 'tpl/stdstyle/images/'.$record['icon_large'], $thisline);


//          $thisline = mb_ereg_replace('{cacheid}', $r['cacheid'], $thisline);
//          $thisline = mb_ereg_replace('{userid}', $r['userid'], $thisline);
//          $thisline = mb_ereg_replace('{cachename}', htmlspecialchars($r['cachename'], ENT_COMPAT, 'UTF-8'), $thisline);
//          $thisline = mb_ereg_replace('{username}', htmlspecialchars($r['username'], ENT_COMPAT, 'UTF-8'), $thisline);
//          $thisline = mb_ereg_replace('{date}', date('d.m.Y', strtotime($r['date_created'])), $thisline);
//          $thisline = mb_ereg_replace('{country}', htmlspecialchars($r['country'], ENT_COMPAT, 'UTF-8'), $thisline);
//          $thisline = mb_ereg_replace('{imglink}', 'tpl/stdstyle/images/'.getSmallCacheIcon($r['icon_large']), $thisline);

            $content .= $thisline . "\n";
        }
        mysql_free_result($rs);
        tpl_set_var('newrecomend', $content);

        $rs = sql('SELECT COUNT(*) `count` FROM `caches`');
        $r = sql_fetch_array($rs);
        $count = $r['count'];
        mysql_free_result($rs);

        $frompage = $startat / 100 - 3;
        if ($frompage < 1) $frompage = 1;

        $topage = $frompage + 8;
        if (($topage - 1) * $perpage > $count)
            $topage = ceil($count / $perpage);

        $thissite = $startat / 100 + 1;

        $pages = '<b>';
        if ($startat > 0)
            $pages .= '<a href="newrecomend.php?startat=0">&lt;&lt;</a> <a href="newrecomend.php?startat=' . ($startat - 100) . '">&lt;</a> ';
        else
            $pages .= '&lt;&lt; &lt; ';

        for ($i = $frompage; $i <= $topage; $i++)
        {
            if ($i == $thissite)
                $pages .= $i . ' ';
            else
                $pages .= '<a href="newrecomend.php?startat=' . ($i - 1) * 100 . '">' . $i . '</a> ';
        }
        if ($thissite < $topage)
            $pages .= '<a href="newrecomend.php?startat=' . ($startat + 100) . '">&gt;</a> <a href="newrecomend.php?startat=' . (ceil($count / 100) * 100 - 100) . '">&gt;&gt;</a></b>';
        else
            $pages .= '&gt; &gt;&gt;</b>';

        tpl_set_var('pages', $pages);
    }

    //make the template and send it out
    tpl_BuildTemplate();
?>
