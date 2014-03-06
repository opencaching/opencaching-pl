<?php
/***************************************************************************
    *
    *   This program is free software; you can redistribute it and/or modify
    *   it under the terms of the GNU General Public License as published by
    *   the Free Software Foundation; either version 2 of the License, or
    *   (at your option) any later version.
    *
    ***************************************************************************/

/****************************************************************************

   Unicode Reminder ăĄă˘

     include the newcaches HTML file

 ****************************************************************************/

    //prepare the templates and include all neccessary
    require_once('./lib/common.inc.php');
    require_once('./lib/cache_icon.inc.php');
    require_once($rootpath . 'lib/caches.inc.php');
    require_once($stylepath . '/lib/icons.inc.php');

    global $usr, $dateFormat;

    //Preprocessing
    if ($error == false)
    {
        //get the news
        $tplname = 'newcaches';
//      require('tpl/stdstyle/newcaches.inc.php');
        require($stylepath . '/newcaches.inc.php');

        $startat = isset($_REQUEST['startat']) ? $_REQUEST['startat'] : 0;
        $startat = $startat + 0;

        $perpage = 50;
        $startat -= $startat % $perpage;

        $content = '';
        $rs = sql('SELECT `caches`.`cache_id` `cacheid`,
                            `user`.`user_id` `userid`,
                            `user`.`user_id` `user_id`,
                            `caches`.`country` `country`,
                            `caches`.`cache_id` `cache_id`,
                            `caches`.`type` `type`,
                            `caches`.`type` `cache_type`,
                            `caches`.`name` `cachename`,
                            `caches`.`wp_oc` `wp_name`,
                            `user`.`username` `username`,
                            `caches`.`date_created` `date_created`,
                            `caches`.`date_hidden` `date_hidden`,
                            IF((`caches`.`date_hidden`>`caches`.`date_created`), `caches`.`date_hidden`, `caches`.`date_created`) AS `date`,
                            `cache_type`.`icon_large` `icon_large`,
                            IFNULL(`cache_location`.`adm3`,\'\') `region`,
                            `PowerTrail`.`id` AS PT_ID,
							`PowerTrail`.`name` AS PT_name,
							`PowerTrail`.`type` As PT_type,
							`PowerTrail`.`image` AS PT_image
                        FROM (`caches` LEFT JOIN `cache_location` ON `caches`.`cache_id`=`cache_location`.`cache_id` 
                        			   LEFT JOIN `powerTrail_caches` ON `caches`.`cache_id` = `powerTrail_caches`.`cacheId`
                        			   LEFT JOIN `PowerTrail` ON `PowerTrail`.`id` = `powerTrail_caches`.`PowerTrailId`  AND `PowerTrail`.`status` = 1 ), `user`, `cache_type`
                       
                        WHERE `caches`.`date_hidden` <= NOW()
                        AND `caches`.`date_created` <= NOW()
                        AND `caches`.`user_id`=`user`.`user_id`
                        AND `cache_type`.`id`=`caches`.`type`
                        AND `caches`.`status` = 1
                        ORDER BY IF((`caches`.`date_hidden`>`caches`.`date_created`), `caches`.`date_hidden`, `caches`.`date_created`) DESC,
                        `caches`.`cache_id` DESC
                        LIMIT ' . ($startat+0) . ', ' . ($perpage+0));
                        
           //PowerTrail vel GeoPath variables
			$pt_cache_intro_tr = tr('pt_cache');
			$pt_icon_title_tr =  tr('pt139');     
			              
        while ($r = sql_fetch_array($rs))
        {
            $rss = sql("SELECT `pl` `country_name` FROM `countries` WHERE `short` = '&1'",$r['country']);
            $rr = sql_fetch_array($rss);
            $thisline = $tpl_line;

    $rs_log = sql("SELECT cache_logs.id, cache_logs.cache_id AS cache_id,
                              cache_logs.type AS log_type,
                              cache_logs.date AS log_date,
                log_types.icon_small AS icon_small, COUNT(gk_item.id) AS geokret_in
            FROM (cache_logs INNER JOIN caches ON (caches.cache_id = cache_logs.cache_id)) INNER JOIN log_types ON (cache_logs.type = log_types.id)
                            LEFT JOIN   gk_item_waypoint ON gk_item_waypoint.wp = caches.wp_oc
                            LEFT JOIN   gk_item ON gk_item.id = gk_item_waypoint.id AND
                            gk_item.stateid<>1 AND gk_item.stateid<>4 AND gk_item.typeid<>2 AND gk_item.stateid !=5
            WHERE cache_logs.deleted=0 AND cache_logs.cache_id=&1
             GROUP BY cache_logs.id ORDER BY cache_logs.date_created DESC LIMIT 1",$r['cacheid']);

            if (mysql_num_rows($rs_log) != 0)
            {
            $r_log = sql_fetch_array($rs_log);


            $thisline = mb_ereg_replace('{log_image}','<img src="tpl/stdstyle/images/' . $r_log['icon_small'] . '" border="0" alt="" />',$thisline);
            } else {
            $thisline = mb_ereg_replace('{log_image}','&nbsp;', $thisline); }

            if ( $r_log['geokret_in'] !=0)
                    {
            $thisline = mb_ereg_replace('{gkimage}','&nbsp;<img src="images/gk.png" border="0" alt="" title="GeoKret" />', $thisline);
                    }
                    else
                    {
            $thisline = mb_ereg_replace('{gkimage}','&nbsp;', $thisline);
                    }

            $thisline = mb_ereg_replace('{cacheid}', $r['cacheid'], $thisline);
            $thisline = mb_ereg_replace('{userid}', $r['userid'], $thisline);
            
           // PowerTrail vel GeoPath icon
			if (isset($r['PT_ID']))  {
				 	$PT_icon = icon_geopath_small($r['PT_ID'],$r['PT_image'],$r['PT_name'],$r['PT_type'],$pt_cache_intro_tr,$pt_icon_title_tr);
				 	$thisline = mb_ereg_replace('{GPicon}',$PT_icon, $thisline);
				 } else {
				 	$thisline = mb_ereg_replace('{GPicon}','<img src="images/rating-star-empty.png" class="icon16" alt="" title="" />', $thisline);
				 };
            
            $thisline = mb_ereg_replace('{cachetype}', htmlspecialchars(cache_type_from_id($r['type'], $lang), ENT_COMPAT, 'UTF-8'), $thisline);
            $thisline = mb_ereg_replace('{cachename}', htmlspecialchars($r['cachename'], ENT_COMPAT, 'UTF-8'), $thisline);
            $thisline = mb_ereg_replace('{username}', htmlspecialchars($r['username'], ENT_COMPAT, 'UTF-8'), $thisline);
//          if ($r['country']=='PL') {
            $thisline = mb_ereg_replace('{region}', htmlspecialchars($r['region'], ENT_COMPAT, 'UTF-8'), $thisline);
//          else { $thisline = mb_ereg_replace('{region}', '', $thisline);}
            $thisline = mb_ereg_replace('{date}', date($dateFormat, strtotime($r['date'])), $thisline);
            $thisline = mb_ereg_replace('{country}', htmlspecialchars(strtolower($r['country']), ENT_COMPAT, 'UTF-8'), $thisline);

            $cacheicon = myninc::checkCacheStatusByUser($r, $usr['userid']);
            $thisline = mb_ereg_replace('{imglink}', $cacheicon, $thisline);
            $thisline = mb_ereg_replace('{country_name}', htmlspecialchars($rr['country_name'], ENT_COMPAT, 'UTF-8'), $thisline);
            $content .= $thisline . "\n";
            mysql_free_result($rs_log);
        }
        mysql_free_result($rs);
        tpl_set_var('newcaches', $content);

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

        $pages = '';
        if ($startat > 0)
            $pages .= '<a href="newcaches.php?startat=0">{first_img}</a> <a href="newcaches.php?startat=' . ($startat - 100) . '">{prev_img}</a> ';
        else
            $pages .= '{first_img_inactive} {prev_img_inactive} ';

        for ($i = $frompage; $i <= $topage; $i++)
        {
            if ($i == $thissite)
                $pages .= $i . ' ';
            else
                $pages .= '<a href="newcaches.php?startat=' . ($i - 1) * $perpage . '">' . $i . '</a> ';
        }
        if ($thissite < $topage)
            $pages .= '<a href="newcaches.php?startat=' . ($startat + $perpage) . '">{next_img}</a> <a href="newcaches.php?startat=' . (ceil($count / 100) * 100 - 100) . '">{last_img}</a>';
        else
            $pages .= '{next_img_inactive} {last_img_inactive}';

        $pages = mb_ereg_replace('{prev_img}', $prev_img, $pages);
        $pages = mb_ereg_replace('{next_img}', $next_img, $pages);
        $pages = mb_ereg_replace('{last_img}', $last_img, $pages);
        $pages = mb_ereg_replace('{first_img}', $first_img, $pages);

        $pages = mb_ereg_replace('{prev_img_inactive}', $prev_img_inactive, $pages);
        $pages = mb_ereg_replace('{next_img_inactive}', $next_img_inactive, $pages);
        $pages = mb_ereg_replace('{first_img_inactive}', $first_img_inactive, $pages);
        $pages = mb_ereg_replace('{last_img_inactive}', $last_img_inactive, $pages);

        tpl_set_var('pages', $pages);
    }

    //make the template and send it out
    tpl_BuildTemplate();
?>
