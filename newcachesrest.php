<?php

use Utils\Database\XDb;
use Utils\Text\Formatter;
use Utils\I18n\I18n;

//prepare the templates and include all neccessary
require_once (__DIR__.'/lib/common.inc.php');
require_once (__DIR__.'/tpl/stdstyle/lib/icons.inc.php');

//Preprocessing
if ($error == false) {
    //get the news
    $tplname = 'newcachesrest';
    require(__DIR__.'/tpl/stdstyle/newcachesrest.inc.php');

    $content = '';
    $cache_country = '';

    $lang_db = I18n::getLangForDbTranslations('countries');

    $rs = XDb::xSql(
        "SELECT `caches`.`cache_id` `cache_id`,
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
                `countries`.$lang_db `country`,
                `cache_type`.`icon_small` `icon_small`,
                `PowerTrail`.`id` AS PT_ID,
                `PowerTrail`.`name` AS PT_name,
                `PowerTrail`.`type` As PT_type,
                `PowerTrail`.`image` AS PT_image
        FROM (`caches`
            LEFT JOIN `powerTrail_caches` ON `caches`.`cache_id` = `powerTrail_caches`.`cacheId`
            LEFT JOIN `PowerTrail` ON `PowerTrail`.`id` = `powerTrail_caches`.`PowerTrailId`  AND `PowerTrail`.`status` = 1 ), `user`, `countries`, `cache_type`
        WHERE `caches`.`user_id`=`user`.`user_id`
            AND `countries`.`short`=`caches`.`country`
            AND `caches`.`type` != 6
            AND `caches`.`status` = 1
            AND `caches`.`country` NOT IN($countryParamNewcacherestPhp)
            AND `caches`.`type`=`cache_type`.`id`
            AND `caches`.`date_hidden` <= NOW()
            AND `caches`.`date_created` <= NOW()
        ORDER BY IF((`caches`.`date_hidden`>`caches`.`date_created`), `caches`.`date_hidden`, `caches`.`date_created`) DESC, `caches`.`cache_id` DESC
        LIMIT 0, 200");

    while( $record = XDb::xFetchArray($rs) ){
        //group by country
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
            'icon_small' => $record['icon_small'],
            'PT_ID' => $record['PT_ID'],
            'PT_name' => $record['PT_name'],
            'PT_type' => $record['PT_type'],
            'PT_image' => $record['PT_image']
        );
    }

    if (isset($newcaches)) {

        //PowerTrail vel GeoPath variables
        $pt_cache_intro_tr = tr('pt_cache');
        $pt_icon_title_tr = tr('pt139');

        foreach ($newcaches AS $countryname => $country_record) {
            $cache_country = '<tr><td colspan="7" class="content-title-noshade-size3">' . htmlspecialchars($countryname, ENT_COMPAT, 'UTF-8') . '</td></tr>';
            $content .= $cache_country;
            foreach ($country_record AS $cache_record) {
                $thisline = $tpl_line;


                $rs_log = XDb::xSql(
                    "SELECT cache_logs.id, cache_logs.cache_id AS cache_id,
                            cache_logs.type AS log_type, cache_logs.date AS log_date,
                            log_types.icon_small AS icon_small, COUNT(gk_item.id) AS geokret_in
                    FROM (cache_logs INNER JOIN caches ON (caches.cache_id = cache_logs.cache_id))
                        INNER JOIN log_types ON (cache_logs.type = log_types.id)
                        LEFT JOIN gk_item_waypoint ON gk_item_waypoint.wp = caches.wp_oc
                        LEFT JOIN gk_item ON gk_item.id = gk_item_waypoint.id
                            AND gk_item.stateid<>1 AND gk_item.stateid<>4 AND gk_item.typeid<>2 AND gk_item.stateid !=5
                    WHERE cache_logs.deleted=0 AND cache_logs.cache_id= ?
                    GROUP BY cache_logs.id
                    ORDER BY cache_logs.date_created DESC
                    LIMIT 1", $cache_record['cache_id']);

                if ( $r_log = XDb::xFetchArray($rs_log) ) {
                    $thisline = mb_ereg_replace('{logimage}', '<img src="tpl/stdstyle/images/' . $r_log['icon_small'] . '" alt="">', $thisline);
                } else {
                    $thisline = mb_ereg_replace('{logimage}', '&nbsp;', $thisline);
                }
                if ( $r_log && $r_log['geokret_in'] != '0' ) {
                    $thisline = mb_ereg_replace('{gkimage}', '&nbsp;<img src="images/gk.png" alt="" title="GeoKret">', $thisline);
                } else {
                    $thisline = mb_ereg_replace('{gkimage}', '&nbsp;', $thisline);
                }
                XDb::xFreeResults($rs_log);

                // PowerTrail vel GeoPath icon
                if (isset($cache_record['PT_ID'])) {
                    $PT_icon = icon_geopath_small($cache_record['PT_ID'], $cache_record['PT_image'], $cache_record['PT_name'], $cache_record['PT_type'], $pt_cache_intro_tr, $pt_icon_title_tr);
                    $thisline = mb_ereg_replace('{GPicon}', $PT_icon, $thisline);
                } else {
                    $thisline = mb_ereg_replace('{GPicon}', '<img src="images/rating-star-empty.png" class="icon16" alt="" title="">', $thisline);
                };


                $thisline = mb_ereg_replace('{cacheid}', $cache_record['cache_id'], $thisline);
                $thisline = mb_ereg_replace('{userid}', $cache_record['userid'], $thisline);
                $thisline = mb_ereg_replace('{cachename}', htmlspecialchars($cache_record['name'], ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{username}', htmlspecialchars($cache_record['username'], ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{date}', Formatter::date($cache_record['date']), $thisline);
                $thisline = mb_ereg_replace('{imglink}', 'tpl/stdstyle/images/' . $cache_record['icon_small'], $thisline);
                $content .= $thisline . "\n";
            }$content .= '<tr><td colspan="7">&nbsp;</td></tr>';
        }
    }


    XDb::xFreeResults($rs);
    tpl_set_var('newcachesrest', $content);
}
//make the template and send it out
tpl_BuildTemplate();
