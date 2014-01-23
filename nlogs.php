<?php
    /***************************************************************************
                                                ./nlogs.php
                                                                -------------------
            begin                : July 9 2004
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

   Unicode Reminder ăĄă˘

        new logs

    ****************************************************************************/
    global $lang, $rootpath;

    if (!isset($rootpath)) $rootpath = '';

    //include template handling
    require_once($rootpath . 'lib/common.inc.php');
    require_once($rootpath . 'lib/cache_icon.inc.php');
//  require_once($stylepath . '/lib/icons.inc.php');

//Preprocessing
if ($error == false)
{
        //get the news
        $tplname = 'nlogs';

    //newlogs.inc.php
    $rs = sql(" SELECT `cache_logs`.`id`
            FROM `cache_logs`, `caches`
            WHERE `cache_logs`.`cache_id`=`caches`.`cache_id`
              AND `caches`.`status` != 4
                AND `caches`.`status` != 5
                AND `caches`.`status` != 6
            ORDER BY `cache_logs`.`date` DESC, `cache_logs`.`last_modified` DESC
            LIMIT 0, 150");
    $log_ids = '';
    for ($i = 0; $i < mysql_num_rows($rs); $i++)
    {
        $record = sql_fetch_array($rs);
        if ($i > 0)
        {
            $log_ids .= ', ' . $record['id'];
        }
        else
        {
            $log_ids = $record['id'];
        }
    }
    mysql_free_result($rs);

    $rs = sql("SELECT cache_logs.cache_id AS cache_id,
                              cache_logs.type AS log_type,
                              cache_logs.date AS log_date,
                              caches.name AS cache_name,
                              countries.pl AS country_name,
                              user.username AS user_name,
                                  log_types.icon_small AS icon_small
                      FROM ((cache_logs INNER JOIN caches ON (caches.cache_id = cache_logs.cache_id)) INNER JOIN countries ON (caches.country = countries.short)) INNER JOIN user ON (cache_logs.user_id = user.user_id) INNER JOIN log_types ON (cache_logs.type = log_types.id)
                       WHERE cache_logs.id IN (" . $log_ids . ")
                       ORDER BY cache_logs.date DESC, cache_logs.last_modified DESC");
    //$rs = mysql_query($sql);

    for ($i = 0; $i < mysql_num_rows($rs); $i++)
    {
        //group by country
        $record = sql_fetch_array($rs);

        $newlogs[$record['country_name']][] = array(
            'cache_id'   => $record['cache_id'],
            'log_type'   => $record['log_type'],
            'log_date'   => $record['log_date'],
            'cache_name' => $record['cache_name'],
            'user_name'  => $record['user_name'],
                        'icon_small' => $record['icon_small']
        );
    }

    //sort by country name
    uksort($newlogs, 'cmp');

    $file_content = '
        <table class="content">
            <colgroup>
                <col width="100">
            </colgroup>
            <tr><td class="header"><img src="tpl/stdstyle/images/description/22x22-logs.png" border="0" width="22" height="22" alt="Cachesuche" title="Cachesuche" align="middle" /><font size="4">  <b>Najnowsze wpisy do LOGów</b></font></td></tr>
            <tr><td class="spacer"></td></tr>
            ';

    if (isset($newlogs))
    {
        foreach ($newlogs AS $countryname => $country_record)
        {
            $file_content .= '<tr><td class="header-small"><b>' . htmlspecialchars($countryname, ENT_COMPAT, 'UTF-8') . '</b></td></tr>';

            foreach ($country_record AS $log_record)
            {

                $file_content .= "<tr><td>";
                $file_content .= htmlspecialchars(date("d.m.Y", strtotime($log_record['log_date'])), ENT_COMPAT, 'UTF-8');
                $file_content .= ' <img src="/tpl/stdstyle/images/' . $log_record['icon_small'] . '" class="icon16" align="left" alt="" title="" />';
                $file_content .= ' - <a href="viewcache.php?cacheid=' . htmlspecialchars($log_record['cache_id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($log_record['cache_name'], ENT_COMPAT, 'UTF-8') . '</a>';

                if ($log_record['log_type'] == 1)
                {
                    $file_content .= ' znalazł ' . htmlspecialchars($log_record['user_name'], ENT_COMPAT, 'UTF-8') .  '';
                }
                elseif ($log_record['log_type'] == 2)
                {
                    $file_content .= ' nie znalazł ' . htmlspecialchars($log_record['user_name'], ENT_COMPAT, 'UTF-8') .  '';
                }
                elseif ($log_record['log_type'] == 3)
                {
                    $file_content .= ' komentarz ' . htmlspecialchars($log_record['user_name'], ENT_COMPAT, 'UTF-8') .  '';
                }

                $file_content .= "</td></tr>";
                $file_content .= "\n";
            }
        }
    }
    $file_content .= '</table>';
    //$n_file = fopen("/tpl/stdstyle/html/newlogs.tpl.php", 'w');
    //fwrite($n_file, $file_content);
    //fclose($n_file);
    tpl_set_var('file_content',$file_content);
    unset($newcaches);

    //user definied sort function

}
function cmp($a, $b)
    {
        if ($a == $b)
        {
            return 0;
        }
        return ($a > $b) ? 1 : -1;
    }
//make the template and send it out
tpl_BuildTemplate();
?>
