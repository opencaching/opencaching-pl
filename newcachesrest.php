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

    global $lang, $rootpath;


    //prepare the templates and include all neccessary
    require_once('./lib/common.inc.php');
    require_once('./lib/cache_icon.inc.php');

    //Preprocessing
    if ($error == false)
    {
        //get the news
        $tplname = 'newcachesrest';
        require('tpl/stdstyle/newcachesrest.inc.php');
//      require($stylepath . '/newcachesresst.inc.php');


        $content = '';
        $cache_country='';

        if(checkField('countries','list_default_'.$lang) )
                $lang_db = $lang;
            else
                $lang_db = "en";

    $rs = sql(" SELECT  `caches`.`cache_id` `cache_id`,
                `caches`.`user_id` `userid`,
                `user`.`username` `username`,
                `caches`.`country` `countryshort`,
                `caches`.`longitude` `longitude`,
                `caches`.`latitude` `latitude`,
                `caches`.`wp_oc` `wp_name`,
                `caches`.`name` `name`,
                `caches`.`date_hidden` `date_hidden`,
                `caches`.`date_created` `date_created`,
                IF((`caches`.`date_hidden`>`caches`.`date_created`), `caches`.`date_hidden`, `caches`.`date_created`) AS `date`,
                `countries`.`&1` `country`,
                `cache_type`.`icon_large` `icon_large`
            FROM `caches`, `user`, `countries`, `cache_type`
            WHERE `caches`.`user_id`=`user`.`user_id`
              AND `countries`.`short`=`caches`.`country`
              AND `type` != 6
              AND `caches`.`status` = 1
              AND `caches`.`country` NOT IN($countryParamNewcacherestPhp)
              AND `caches`.`type`=`cache_type`.`id`
                AND `caches`.`date_hidden` <= NOW()
                AND `caches`.`date_created` <= NOW()
            ORDER BY IF((`caches`.`date_hidden`>`caches`.`date_created`), `caches`.`date_hidden`, `caches`.`date_created`) DESC, `caches`.`cache_id` DESC
            LIMIT 0, 200", $lang_db);

    for ($i = 0; $i < mysql_num_rows($rs); $i++)
    {
        //group by country
        $record = sql_fetch_array($rs);
        $newcaches[$record['country']][] = array(
            'name' => $record['name'],
            'wp_name' => $record['wp_name'],
            'userid' => $record['userid'],
            'username' => $record['username'],
            'cache_id' => $record['cache_id'],
            'country' => $record['countryshort'],
            'longitude' => $record['longitude'],
            'latitude' => $record['latitude'],
            'date' => $record['date'],
            'icon_large' => $record['icon_large']
        );
    }

//  uksort($newcaches, 'cmp');


    if (isset($newcaches))
    {
        foreach ($newcaches AS $countryname => $country_record)
        {
            $cache_country = '<tr><td colspan="6" class="content-title-noshade-size3">' . htmlspecialchars($countryname, ENT_COMPAT, 'UTF-8') . '</td></tr>';
            $content .= $cache_country;
            foreach ($country_record AS $cache_record)
            {
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
                       GROUP BY cache_logs.id ORDER BY cache_logs.date_created DESC LIMIT 1",$cache_record['cache_id']);



            if (mysql_num_rows($rs_log) != 0)
            {
            $r_log = sql_fetch_array($rs_log);



            $thisline = mb_ereg_replace('{logimage}','<img src="tpl/stdstyle/images/' . $r_log['icon_small'] . '" border="0" alt="" />',$thisline);
            } else {
            $thisline = mb_ereg_replace('{logimage}','&nbsp;', $thisline); }
            if ( $r_log['geokret_in'] !='0')
                    {
            $thisline = mb_ereg_replace('{gkimage}','&nbsp;<img src="images/gk.png" border="0" alt="" title="GeoKret" />', $thisline);
                    }
                    else
                    {
            $thisline = mb_ereg_replace('{gkimage}','&nbsp;', $thisline);
                    }
            mysql_free_result($rs_log);
            $thisline = mb_ereg_replace('{cacheid}', $cache_record['cache_id'], $thisline);
            $thisline = mb_ereg_replace('{userid}', $cache_record['userid'], $thisline);
            $thisline = mb_ereg_replace('{cachename}', htmlspecialchars($cache_record['name'], ENT_COMPAT, 'UTF-8'), $thisline);
            $thisline = mb_ereg_replace('{username}', htmlspecialchars($cache_record['username'], ENT_COMPAT, 'UTF-8'), $thisline);
            $thisline = mb_ereg_replace('{date}', date('d-m-Y', strtotime($cache_record['date'])), $thisline);
            $thisline = mb_ereg_replace('{imglink}', 'tpl/stdstyle/images/'.getSmallCacheIcon($cache_record['icon_large']), $thisline);
//          $thisline = mb_ereg_replace('{{hidden_by}}', htmlspecialchars(tr('created_by'), ENT_COMPAT, 'UTF-8'), $thisline);
            $content .= $thisline . "\n";
            }$content .= '<tr><td colspan="5">&nbsp;</td></tr>';
        }

    }


    mysql_free_result($rs);
    tpl_set_var('newcachesrest', $content);

    }
    //make the template and send it out
    tpl_BuildTemplate();
?>
