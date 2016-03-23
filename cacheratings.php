<?php

use Utils\Database\XDb;
//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');
require_once('./lib/cache_icon.inc.php');
require_once($rootpath . 'lib/caches.inc.php');
require_once($stylepath . '/lib/icons.inc.php');

global $usr;
//Preprocessing
if ($error == false) {
    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
    } else {
        require($stylepath . '/newcaches.inc.php');
        // get the news
        $tplname = 'cacheratings';

        $startat = isset($_REQUEST['startat']) ? $_REQUEST['startat'] : 0;
        $startat = $startat + 0;

        $perpage = 50;
        $startat -= $startat % $perpage;

        //start_ratings.include
        $rs = XDb::xQuery(
            'SELECT  `user`.`user_id` `user_id`,
                `user`.`username` `username`,
                `caches`.`cache_id` `cache_id`,
                `caches`.`name` `name`,
                `caches`.`type` AS `cache_type`,
                `cache_type`.`icon_large` `icon_large`,
                count(`cache_rating`.`cache_id`) as `anzahl`,
                `PowerTrail`.`id` AS PT_ID,
                `PowerTrail`.`name` AS PT_name,
                `PowerTrail`.`type` As PT_type,
                `PowerTrail`.`image` AS PT_image
             FROM `caches`
                LEFT JOIN `powerTrail_caches` ON `caches`.`cache_id` = `powerTrail_caches`.`cacheId`
                LEFT JOIN `PowerTrail` ON (
                    `PowerTrail`.`id` = `powerTrail_caches`.`PowerTrailId`  AND `PowerTrail`.`status` = 1),
                    `user`, `cache_type`, `cache_rating`
             WHERE `caches`.`user_id`=`user`.`user_id`
                AND `cache_rating`.`cache_id`=`caches`.`cache_id`
                AND `caches`.`status`=1  AND `caches`.`type` <> 6
                AND `caches`.`type`=`cache_type`.`id`
             GROUP BY `user`.`user_id`, `user`.`username`, `caches`.`cache_id`, `caches`.`name`, `cache_type`.`icon_large`
             ORDER BY `anzahl` DESC, `caches`.`name` ASC
             LIMIT '.XDb::xEscape($startat).','.XDb::xEscape($perpage));

        $tr_myn_click_to_view_cache = tr('myn_click_to_view_cache');
        $cacheline = '<tr><td>&nbsp;</td><td><span class="content-title-noshade txt-blue08" >{rating_absolute}</span></td><td>{GPicon}</td><td><a class="links" href="viewcache.php?cacheid={cacheid}"><img src="{cacheicon}" class="icon16" alt="' . $tr_myn_click_to_view_cache . '" title="' . $tr_myn_click_to_view_cache . '" /></a></td><td><strong><a class="links" href="viewcache.php?cacheid={cacheid}">{cachename}</a></strong></td><td><strong><a class="links" href="viewprofile.php?userid={userid}">{username}</a></strong></td></tr>';

        if (XDb::xNumRows($rs) == 0) {
            $file_content = '<tr><td colspan="5"><strong>' . tr('recommendation_rating_none') . '</strong></td></tr>';
        } else {

            //powertrail vel geopath variables
            $pt_cache_intro_tr = tr('pt_cache');
            $pt_icon_title_tr = tr('pt139');

            $file_content ='';
            $rows = 0;
            while( $record = XDb::xFetchArray($rs)){
                $rows++;
                //$cacheicon = 'tpl/stdstyle/images/'.getSmallCacheIcon($record['icon_large']);

                $thisline = $cacheline;
                $thisline = mb_ereg_replace('{cacheid}', urlencode($record['cache_id']), $thisline);
                $thisline = mb_ereg_replace('{cachename}', htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8'), $thisline);
                // PowerTrail vel GeoPath icon
                if (isset($record['PT_ID'])) {
                    $PT_icon = icon_geopath_small($record['PT_ID'], $record['PT_image'], $record['PT_name'], $record['PT_type'], $pt_cache_intro_tr, $pt_icon_title_tr);
                    $thisline = mb_ereg_replace('{GPicon}', $PT_icon, $thisline);
                } else {
                    $thisline = mb_ereg_replace('{GPicon}', '<img src="images/rating-star-empty.png" class="icon16" alt="" title="" />', $thisline);
                };

                $thisline = mb_ereg_replace('{userid}', urlencode($record['user_id']), $thisline);
                $thisline = mb_ereg_replace('{username}', htmlspecialchars($record['username'], ENT_COMPAT, 'UTF-8'), $thisline);
                $thisline = mb_ereg_replace('{rating_absolute}', $record['anzahl'], $thisline);

                $cacheicon = myninc::checkCacheStatusByUser($record, $usr['userid']);

                $thisline = mb_ereg_replace('{cacheicon}', $cacheicon, $thisline);

                $file_content .= $thisline . "\n";
            }
            tpl_set_var('num_ratings', $rows);

        }
    }

    tpl_set_var('content', $file_content);

    $rs = XDb::xQuery('SELECT COUNT(*) `count`
            FROM `caches`
            WHERE caches.`status`=1  AND caches.type <> 6
            AND caches.`topratings`!=0');
    $r = XDb::xFetchArray($rs);
    $count = $r['count'];
    XDb::xFreeResults($rs);

    $frompage = $startat / 100 - 3;
    if ($frompage < 1)
        $frompage = 1;

    $topage = $frompage + 8;
    if (($topage - 1) * $perpage > $count)
        $topage = ceil($count / $perpage);

    $thissite = $startat / 100 + 1;

    $pages = '';
    if ($startat > 0)
        $pages .= '<a href="cacheratings.php?startat=0">{first_img}</a> <a href="cacheratings.php?startat=' . ($startat - 100) . '">{prev_img}</a> ';
    else
        $pages .= '{first_img_inactive} {prev_img_inactive} ';

    for ($i = $frompage; $i <= $topage; $i++) {
        if ($i == $thissite)
            $pages .= $i . ' ';
        else
            $pages .= '<a href="cacheratings.php?startat=' . ($i - 1) * $perpage . '">' . $i . '</a> ';
    }
    if ($thissite < $topage)
        $pages .= '<a href="cacheratings.php?startat=' . ($startat + $perpage) . '">{next_img}</a> <a href="cacheratings.php?startat=' . (ceil($count / 100) * 100 - 100) . '">{last_img}</a>';
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
