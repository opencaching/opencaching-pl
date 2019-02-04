<?php

use Utils\Database\XDb;
use lib\Objects\GeoCache\PrintList;
use lib\Objects\GeoCache\UserCacheCoords;

//prepare the templates and include all neccessary
require_once (__DIR__ . '/lib/common.inc.php');

//user logged in?
if ($usr == false) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);
    exit;
}

include(__DIR__.'/tpl/stdstyle/mylist.inc.php');
$tplname = 'mylist';

$bml_id = 0;
tpl_set_var('title_text', $standard_title);

if ( empty( PrintList::GetContent() ) ) {

    tpl_set_var('list', $no_list);
    tpl_set_var('print_delete_list', '');
    tpl_set_var('export_list', '');
} else {
    $cache_list = XDb::xEscape( implode(",", PrintList::GetContent() ) );
    $rs = XDb::xSql(
        "SELECT `cache_id`, `name`, `type`,`last_found`
        FROM `caches` WHERE `cache_id` IN ( $cache_list )
        ORDER BY `name`");
    $list = '';
    $i = 0;
    while( $record = XDb::xFetchArray($rs) ){
        $tmp_list = $i++ % 2 == 0 ? $list_e : $list_o;

        //modified coords
        if (($record['type'] == '7' || $record['type'] == '1' || $record['type'] == '3' ) &&
                $usr != false) {  //check if quiz (7) or other(1) or multi (3) and user is logged

            if ( UserCacheCoords::getCoords($usr['userid'], $record ['cache_id']) ) {
                $tmp_list = str_replace('{mod_suffix}', '[F]', $tmp_list);
            } else {
                $tmp_list = str_replace('{mod_suffix}', '', $tmp_list);
            }
        } else {
            $tmp_list = str_replace('{mod_suffix}', '', $tmp_list);
        };


        $tmp_list = mb_ereg_replace('{cachename}', htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8'), $tmp_list);
        if ($record['last_found'] == NULL || $record['last_found'] == '0000-00-00 00:00:00') {
            $tmp_list = mb_ereg_replace('{lastfound}', htmlspecialchars($no_found_date, ENT_COMPAT, 'UTF-8'), $tmp_list);
        } else {
            $tmp_list = mb_ereg_replace('{lastfound}', htmlspecialchars(strftime(
                $GLOBALS['config']['dateformat'], strtotime($record['last_found'])), ENT_COMPAT, 'UTF-8'), $tmp_list);
        }

        $tmp_list = mb_ereg_replace('{urlencode_cacheid}', htmlspecialchars(urlencode($record['cache_id']), ENT_COMPAT, 'UTF-8'), $tmp_list);
        $tmp_list = mb_ereg_replace('{cacheid}', htmlspecialchars($record['cache_id'], ENT_COMPAT, 'UTF-8'), $tmp_list);

        $list .= $tmp_list . "\n";
    }

    tpl_set_var('list', $list);
    tpl_set_var('print_delete_list', $print_delete_list);
    tpl_set_var('export_list', $export_list);
}

//make the template and send it out
tpl_BuildTemplate();
