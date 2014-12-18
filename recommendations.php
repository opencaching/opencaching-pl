<?php

/* * *************************************************************************
  ./recommendations.php
  -------------------
  begin                : November 4 2005
  copyright            : (C) 2005 The OpenCaching Group
  forum contact at     : http://www.opencaching.com/phpBB2

 * ************************************************************************* */

/* * **************************************************************************

  Unicode Reminder メモ

  shows recommendations based on the top-ratings

 * ************************************************************************** */

require('./lib/common.inc.php');
require($stylepath . '/recommendations.inc.php');

if ($error == false) {
    $tplname = 'recommendations';

    $cache_id = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] : 0;
    $cache_id += 0; // make sure we get a number value
    $setname = '';

    $i = 0;
    $content = '';
    $rs = sql(" SELECT  COUNT(`cache_rating`.`cache_id`)/(SELECT `topratings` FROM `caches` WHERE `cache_id`='&1')*100 `treffer`,
                    `cache_rating`.`cache_id` `cache_id`,
                    `caches`.`name` `cachename`
                    FROM `cache_rating`, `caches`
                WHERE `cache_rating`.`user_id` IN (SELECT `user_id` FROM `cache_rating` WHERE `cache_id`='&1')
                  AND `cache_rating`.`cache_id` = `caches`.`cache_id`
                GROUP BY `cache_rating`.`cache_id`, `caches`.`name`
                ORDER BY `treffer` DESC, `caches`.`name`
                LIMIT 26", $cache_id);

    if (mysql_num_rows($rs) > 1) {
        while ($r = sql_fetch_array($rs)) {
            if ($r['cache_id'] == $cache_id) {
                tpl_set_var('cachename', '&quot;' . htmlspecialchars($r['cachename'], ENT_COMPAT, 'UTF-8') . '&quot;');
            } else {
                $thisline = $recommendation_line;

                $thisline = mb_ereg_replace('{cachename}', htmlspecialchars($r['cachename'], ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{cacheid}', htmlspecialchars($r['cache_id'], ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{treffer}', htmlspecialchars(round($r['treffer'], 0), ENT_COMPAT, 'UTF-8'), $thisline);

                if (($i % 2) == 1)
                    $thisline = mb_ereg_replace('{bgcolor}', $bgcolor2, $thisline);
                else
                    $thisline = mb_ereg_replace('{bgcolor}', $bgcolor1, $thisline);

                $content .= $thisline;
                $i++;
            }
        }
        mysql_free_result($rs);
    }
    else {
        $content = $norecommendations;
    }

    $thiscache = sqlValue('SELECT `name` FROM `caches` WHERE `cache_id`=\'' . sql_escape($cache_id) . '\'', '-----');
    tpl_set_var('cachename', htmlspecialchars($thiscache, ENT_COMPAT, 'UTF-8'));
    tpl_set_var('cacheid', $cache_id);

    tpl_set_var('recommendations', $content);
    tpl_BuildTemplate();
}
?>
