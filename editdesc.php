<?php

use Utils\Database\XDb;
use lib\Objects\GeoCache\GeoCache;

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

//Preprocessing
if ($error) {
    tpl_errorMsg('editdesc', "Error?");
    exit;
}

$descid = ( isset($_REQUEST['descid']) && is_numeric($_REQUEST['descid']) ) ? $_REQUEST['descid'] : 0;

//user logged in?
if ($usr == false) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);
    exit;
}

$desc_rs = XDb::xSql(
    "SELECT `cache_desc`.`cache_id` `cache_id`, `cache_desc`.`language`
            `language`, `caches`.`name` `name`, `caches`.`user_id` `user_id`,
            `cache_desc`.`desc` `desc`, `cache_desc`.`hint` `hint`, `cache_desc`.`short_desc` `short_desc`,
            `cache_desc`.`desc_html` `desc_html`
    FROM `caches`, `cache_desc`
    WHERE (`caches`.`cache_id` = `cache_desc`.`cache_id`)
        AND `cache_desc`.`id`= ? LIMIT 1", $descid);

if ( $desc_record = XDb::xFetchArray($desc_rs) ) {

    $desc_lang = $desc_record['language'];
    $cache_id = $desc_record['cache_id'];

    if ($desc_record['user_id'] != $usr['userid'] && !$usr['admin']) {
        tpl_errorMsg('editdesc', "You're not an owner of this cache!");
        exit;
    }

    $tplname = 'editdesc';

    tpl_set_var('desc_err', '');
    $show_all_langs = false;

    //save to DB?
    if (isset($_POST['post'])) {
        //here we read all used information from the form if submitted
        $short_desc = $_POST['short_desc'];
        $hints = $_POST['hints'];
        $desclang = $_POST['desclang'];
        $show_all_langs = isset($_POST['show_all_langs_value']) ? $_POST['show_all_langs_value'] : 0;
        if (!is_numeric($show_all_langs))
            $show_all_langs = 0;

        // Text from textarea
        $desc = $_POST['desc'];
        $desc = userInputFilter::purifyHtmlString($desc);
        $hints = htmlspecialchars($hints, ENT_COMPAT, 'UTF-8');

        if (isset($_POST['submitform'])) {

            // consider whether language does not already exist
            $cacheLang = XDb::xMultiVariableQueryValue(
                "SELECT COUNT(*) `count` FROM `cache_desc`
                WHERE `cache_id`= :1 AND `id` != :2 AND `language`= :3 ",
                0, $desc_record['cache_id'], $descid, $desclang);

            if ( $cacheLang > 0){
                tpl_errorMsg('editdesc', "There is such languages description for this cache...");
            }

            /* Prevent binary data in cache descriptions, e.g. <img src='data:...'> tags. */
            if (strlen($desc) > 300000) {
                tpl_errorMsg('editdesc', tr('error3KCharsExcedeed'));
            }

            XDb::xSql(
                "UPDATE `cache_desc` SET
                    `last_modified`=NOW(), `desc_html`= '2', `desc_htmledit`= '1',
                    `desc`= ?, `short_desc`= ?, `hint`= ?, `language`= ?
                WHERE `id`= ? ",
                $desc, $short_desc, nl2br($hints), $desclang, $descid);

            // update description languages in the cache record;
            // this also updates the modification date
            GeoCache::setCacheDefaultDescLang($desc_record['cache_id']);

            // redirect to cachepage
            tpl_redirect('editcache.php?cacheid=' . urlencode($desc_record['cache_id']));
            exit;
        } else if (isset($_POST['show_all_langs'])) {
            $show_all_langs = true;
        }
    } else {
        //here we read all used information from the DB
        $short_desc = strip_tags($desc_record['short_desc']);
        $hints = strip_tags($desc_record['hint']);
        $desc_lang = $desc_record['language'];
        $desc = $desc_record['desc'];
    }

    $eLang = XDb::xEscape($lang);

    //here we only set up the template variables
    tpl_set_var('desc', htmlspecialchars($desc, ENT_COMPAT, 'UTF-8'), true);
    if ($show_all_langs == false) {
        $r_list = XDb::xMultiVariableQueryValue(
            "SELECT `list_default_$eLang` AS `list` FROM `languages`
            WHERE `short`= :1 LIMIT 1", 0, $desc_lang);
        if ($r_list == 0)
            $show_all_langs = true;
    }

    $languages = '';

    $rs = XDb::xSql(
        "SELECT `$eLang` `name`, `short` `short` FROM `languages`
        WHERE `short` NOT IN (
            SELECT `language` FROM `cache_desc`
            WHERE `cache_id`= ? AND `language` != ?
        ) " .
        (($show_all_langs == false) ? " AND `list_default_$eLang`=1 " : "") .
        "ORDER BY `$eLang` ASC",
        $desc_record['cache_id'], $desc_lang);

    while ($r = XDb::xFetchArray($rs))
        $languages .= '<option value="' . $r['short'] . '"' . (($r['short'] == $desc_lang) ? ' selected="selected"' : '') . '>' . htmlspecialchars($r['name'], ENT_COMPAT, 'UTF-8') . '</option>' . "\n";
    XDb::xFreeResults($rs);
    tpl_set_var('desclangs', $languages);


    if ($show_all_langs == false) {
        $show_all_langs_submit = '&nbsp;<input type="submit" name="show_all_langs" value="' . tr('edDescShowAll') . '" />';
        tpl_set_var('show_all_langs_submit', $show_all_langs_submit);
    } else {
        tpl_set_var('show_all_langs_submit', '');
    }

    tpl_set_var('show_all_langs_value', (($show_all_langs == false) ? 0 : 1));
    tpl_set_var('short_desc', htmlspecialchars($short_desc, ENT_COMPAT, 'UTF-8'));
    tpl_set_var('hints', $hints);
    tpl_set_var('descid', $descid);
    tpl_set_var('cacheid', htmlspecialchars($desc_record['cache_id'], ENT_COMPAT, 'UTF-8'));
    tpl_set_var('desclang', htmlspecialchars($desc_lang, ENT_COMPAT, 'UTF-8'));
    tpl_set_var('desclang_name', htmlspecialchars(db_LanguageFromShort($desc_lang), ENT_COMPAT, 'UTF-8'));
    tpl_set_var('cachename', htmlspecialchars($desc_record['name'], ENT_COMPAT, 'UTF-8'));
}


//make the template and send it out
tpl_set_var('language4js', $lang);
tpl_BuildTemplate();

