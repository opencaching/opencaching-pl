<?php

use Utils\Database\XDb;
use lib\Objects\GeoCache\GeoCache;
use Utils\Generators\Uuid;
use Utils\Text\UserInputFilter;
use Utils\I18n\I18n;
use lib\Objects\OcConfig\OcConfig;

require_once (__DIR__.'/lib/common.inc.php');

//Preprocessing
if ($error == false) {
    $cache_id = 0;
    if (isset($_REQUEST['cacheid'])) {
        $cache_id = $_REQUEST['cacheid'];
    }

    //user logged in?
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target=' . $target);
    } else {
        //user must be the owner of the cache
        $cache_rs = XDb::xSql("SELECT `user_id`, `name` FROM `caches` WHERE `cache_id`= ? LIMIT 1", $cache_id);

        if( $cache_record = XDb::xFetchArray($cache_rs)){
            XDb::xFreeResults($cache_rs);

            if ($cache_record['user_id'] == $usr['userid'] || $usr['admin']) {
                $tplname = 'newdesc';

                tpl_set_var('desc_err', '');
                $show_all_langs = false;

                $lang_message = '<br/><span class="errormsg">' . tr('lngExist') . '</span>';
                $show_all_langs_submit = '<input type="submit" class="btn btn-default btn-sm" name="show_all_langs_submit" value="' . tr('edDescShowAll') . '"/>';


                //get the posted data
                $show_all_langs = isset($_POST['show_all_langs']) ? $_POST['show_all_langs'] : 0;
                $short_desc = isset($_POST['short_desc']) ? $_POST['short_desc'] : '';

                $hints = isset($_POST['hints']) ? $_POST['hints'] : '';
                $sel_lang = isset($_POST['desc_lang']) ? $_POST['desc_lang'] : I18n::getCurrentLang();
                $desc = isset($_POST['desc']) ? $_POST['desc'] : '';

                $desc = UserInputFilter::purifyHtmlString($desc);
                $hints = htmlspecialchars($hints, ENT_COMPAT, 'UTF-8');

                $desc_lang_exists = false;

                //save to db?
                if (isset($_POST['submitform'])) {
                    /* Prevent binary data in cache descriptions, e.g. <img src='data:...'> tags. */
                    if (strlen($desc) > 300000) {
                        die(tr('error3KCharsExcedeed'));
                    }

                    //check if the entered language already exists
                    $desc_rs = XDb::xSql("SELECT `id` FROM `cache_desc`
                                          WHERE `cache_id`=? AND `language`=?", $cache_id, $sel_lang);
                    $desc_lang_exists = ( XDb::xFetchArray($desc_rs) != false);

                    if ($desc_lang_exists == false) {
                        $desc_uuid = Uuid::create();

                        $rr_comment = XDb::xMultiVariableQueryValue("
                            SELECT rr_comment
                            FROM cache_desc
                            WHERE cache_id = :1 AND rr_comment <> ''
                            LIMIT 1
                        ", '', $cache_id);

                        //add to DB
                        XDb::xSql("INSERT INTO `cache_desc` (`id`,`cache_id`,`language`,`desc`,`desc_html`,`desc_htmledit`,
                                                       `hint`,`short_desc`,`last_modified`,`uuid`,`node`,`rr_comment`)
                             VALUES ('', ?, ?, ?, 2, ?, ?, ?, NOW(), ?, ?, ?)",
                             $cache_id, $sel_lang, $desc, '1', nl2br($hints), $short_desc, $desc_uuid,
                             OcConfig::getSiteNodeId(), $rr_comment);


                        // update cache-record, including last modification date
                        GeoCache::setCacheDefaultDescLang($cache_id);

                        tpl_redirect('editcache.php?cacheid=' . urlencode($cache_id));
                        exit;
                    }
                } elseif (isset($_POST['show_all_langs_submit'])) {
                    $show_all_langs = 1;
                }

                //build langslist
                $langoptions = '';
                $q_nosellangs = 'SELECT `language` FROM `cache_desc` WHERE `cache_id`=\'' . XDb::xEscape($cache_id) . '\'';

                $eLang = XDb::xEscape(I18n::getCurrentLang());
                if ($show_all_langs == 0) {
                    $langs_rs = XDb::xSql("SELECT `$eLang`, `short` FROM `languages`
                                           WHERE `short` NOT IN (" . $q_nosellangs . ")
                                                AND `list_default_" . $eLang . "` = 1 ORDER BY `$eLang` ASC");
                } else {
                    $langs_rs = XDb::xSql("SELECT `$eLang`, `short` FROM `languages`
                                           WHERE `short` NOT IN (" . $q_nosellangs . ") ORDER BY `$eLang` ASC");
                }

                $rs = XDb::xSql("SELECT COUNT(*) `count` FROM `cache_desc`
                           WHERE `cache_id`=? AND `language`=?", $cache_id, $sel_lang);
                $r = XDb::xFetchArray($rs);
                $bSelectFirst = ($r['count'] == 1);
                XDb::xFreeResults($rs);

                while( $langs_record = XDb::xFetchArray($langs_rs) ){

                    if (($langs_record['short'] == $sel_lang) || ($bSelectFirst == true)) {
                        $bSelectFirst = false;
                        $langoptions .= '<option value="' . htmlspecialchars($langs_record['short'], ENT_COMPAT, 'UTF-8') . '" selected="selected">' . htmlspecialchars($langs_record[I18n::getCurrentLang()], ENT_COMPAT, 'UTF-8') . '</option>';
                    } else {
                        $langoptions .= '<option value="' . htmlspecialchars($langs_record['short'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($langs_record[I18n::getCurrentLang()], ENT_COMPAT, 'UTF-8') . '</option>';
                    }

                    $langoptions .= "\n";
                }
                tpl_set_var('langoptions', $langoptions);
                unset($langs_rs);

                //here we set the template vars
                tpl_set_var('cachename', htmlspecialchars($cache_record['name'], ENT_COMPAT, 'UTF-8'));
                tpl_set_var('cacheid', htmlspecialchars($cache_id, ENT_COMPAT, 'UTF-8'));

                tpl_set_var('lang_message', $desc_lang_exists ? $lang_message : '');

                tpl_set_var('show_all_langs', $show_all_langs);
                tpl_set_var('show_all_langs_submit', ($show_all_langs == 0) ? $show_all_langs_submit : '');
                tpl_set_var('short_desc', htmlspecialchars($short_desc, ENT_COMPAT, 'UTF-8'));
                tpl_set_var('desc', htmlspecialchars($desc, ENT_COMPAT, 'UTF-8'), true);
                tpl_set_var('hints', $hints);

                tpl_set_var('language4js', I18n::getCurrentLang());
            } else {
                tpl_redirect('');
            }
        } else {
            XDb::xFreeResults($cache_rs);
            //TODO: cache not exist
        }
    }
}

//make the template and send it out
tpl_BuildTemplate();
