<?php
/***************************************************************************
                                                                ./editdesc.php
                                                            -------------------
        begin                : July 7 2004
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

     edit a cache listing

     used template(s): editcache

     GET/POST Parameter: cacheid, desclang

 ****************************************************************************/

  //prepare the templates and include all neccessary
    require_once('./lib/common.inc.php');

    // Load the TinyMCE compressor class and configuration
    require_once("./lib/tinymce/tiny_mce_gzip.php");
    require_once("./lib/tinymce/config/compressor.php");

    //Preprocessing
    if ($error == false)
    {

    // from removed inc.php - unused???
    // $desc_not_ok_message = '<br /><br /><p style="margin-top:0px;margin-left:0px;width:550px;background-color:#e5e5e5;border:1px solid black;text-align:left;padding:3px 8px 3px 8px;"><span class="errormsg">Użyty HTML kod jest niedozwolony.</span><br />%text%</p><br />';
    // $pictureline = '<td><a href="javascript:SelectFile(\'{link}\');">{title}<br/><img src="{link}" width="180" /></a></td>';
    // $picturelines = '{lines}';



        // check for old-style parameters
        if (isset($_REQUEST['cacheid']) && isset($_REQUEST['desclang']) && !isset($_REQUEST['descid']))
        {
            $cache_id = $_REQUEST['cacheid'];
            $desc_lang = $_REQUEST['desclang'];

            $rs = sql("SELECT `id` FROM `cache_desc` WHERE `cache_id`='&1' AND `language`='&2'", $cache_id, $desc_lang);
            if (mysql_num_rows($rs) == 1)
            {
                $r = sql_fetch_array($rs);
                $descid = $r['id'];
            }
            else
            {
                tpl_errorMsg('editdesc', $error_desc_not_found);
            }
        }
        else
        {
            $descid = isset($_REQUEST['descid']) ? $_REQUEST['descid'] : 0;
            if (is_numeric($descid) == false)
                $descid = 0;
        }

        //user logged in?
        if ($usr == false)
        {
            $target = urlencode(tpl_get_current_page());
            tpl_redirect('login.php?target='.$target);
        }
        else
        {
            mysql_query("SET NAMES 'utf8'");
            $desc_rs = sql("SELECT `cache_desc`.`cache_id` `cache_id`, `cache_desc`.`language` `language`, `caches`.`name` `name`, `caches`.`user_id` `user_id`, `cache_desc`.`desc` `desc`, `cache_desc`.`hint` `hint`, `cache_desc`.`short_desc` `short_desc`, `cache_desc`.`desc_html` `desc_html`, `cache_desc`.`desc_htmledit` `desc_htmledit` FROM `caches`, `cache_desc` WHERE (`caches`.`cache_id` = `cache_desc`.`cache_id`) AND `cache_desc`.`id`='&1'", $descid);
            if (mysql_num_rows($desc_rs) == 1)
            {
                $desc_record = sql_fetch_array($desc_rs);
                $desc_lang = $desc_record['language'];
                $cache_id = $desc_record['cache_id'];

                if ($desc_record['user_id'] == $usr['userid'] || $usr['admin'])
                {
                    $tplname = 'editdesc';

                    tpl_set_var('desc_err', '');
                    $show_all_langs = false;

                    //save to DB?
                    if (isset($_POST['post']))
                    {
                        //here we read all used information from the form if submitted
                        $desc_html = 1;

                        $short_desc = $_POST['short_desc'];
                        $hint = htmlspecialchars($_POST['hints'], ENT_COMPAT, 'UTF-8');
                        $desclang = $_POST['desclang'];
                        $show_all_langs = isset($_POST['show_all_langs_value']) ? $_POST['show_all_langs_value'] : 0;
                        if (!is_numeric($show_all_langs)) $show_all_langs = 0;


                        // Text from textarea
                        $desc = $_POST['desc'];

                        // check input
                        require_once($rootpath . 'lib/class.inputfilter.php');
                        $myFilter = new InputFilter($allowedtags, $allowedattr, 0, 0, 1);
                        $desc = $myFilter->process($desc);

                        if (isset($_POST['submitform']))
                        {
                            // prüfen, ob sprache nicht schon vorhanden
                            $rs = sql("SELECT COUNT(*) `count` FROM `cache_desc` WHERE `cache_id`='&1' AND `id` != '&2' AND `language`='&3'", $desc_record['cache_id'], $descid, $desclang);
                            $r = sql_fetch_array($rs);
                            if ($r['count'] > 0)
                                tpl_errorMsg('editdesc', $error_desc_exists);
                            mysql_free_result($rs);

                            if($desc_html == 0)
                                $desc = nl2br($desc);

                            /* Prevent binary data in cache descriptions, e.g. <img src='data:...'> tags. */

                            if (strlen($desc) > 300000) {
                                tpl_errorMsg('editdesc', tr('error3KCharsExcedeed'));
                            }

                            $desc = tidy_html_description($desc);
                            mysql_query("SET NAMES 'utf8'");
                            sql("UPDATE `cache_desc` SET
                                        `last_modified`=NOW(),
                                    `desc_html`='&1',
                                    `desc_htmledit`='&2',
                                        `desc`='&3',
                                        `short_desc`='&4',
                                        `hint`='&5',
                                        `language`='&6'
                                  WHERE `id`='&7'",
                                        (($desc_html == 1) ? '1' : '0'),
                                        (($desc_htmledit == 1) ? '1' : '0'),
                                        $desc,
                                        $short_desc,
                                        nl2br($hint),
                                        $desclang,
                                        $descid);

                            // beschreibungssprachen im cache-record aktualisieren
                            setCacheDefaultDescLang($desc_record['cache_id']);

                            // redirect to cachepage
                            tpl_redirect('editcache.php?cacheid=' . urlencode($desc_record['cache_id']));
                            exit;
                        }
                        else if (isset($_POST['show_all_langs']))
                        {
                            $show_all_langs = true;
                        }
                    }
                    else
                    {
                        //here we read all used information from the DB
                        $short_desc = strip_tags($desc_record['short_desc']);
                        $hint = strip_tags($desc_record['hint']);
                        $desc_htmledit = $desc_record['desc_htmledit'];
                        $desc_html = $desc_record['desc_html'];
                        $desc_lang = $desc_record['language'];
                        $desc = $desc_record['desc'];
                    }

                    //here we only set up the template variables
                    tpl_set_var('desc', $desc, true);
                    if ($show_all_langs == false)
                    {
                        $rs = sql('SELECT `list_default_' . sql_escape($lang) . "` `list` FROM `languages` WHERE `short`='&1'", $desc_lang);
                        $r = sql_fetch_array($rs);
                        if ($r['list'] == 0)
                            $show_all_langs = true;
                        mysql_free_result($rs);
                    }

                    $languages = '';
                    $sql_nosellangs = 'SELECT `language` FROM `cache_desc` WHERE (`cache_id`=\'' . sql_escape($desc_record['cache_id']) . '\') AND (`language` != \'' . sql_escape($desc_lang) . '\')';
                    $rs = sql('SELECT `' . sql_escape($lang) .  '` `name`, `short` `short` FROM `languages` WHERE `short` NOT IN (' . $sql_nosellangs . ') ' . (($show_all_langs == false) ? 'AND `list_default_' . sql_escape($lang) . '`=1 ' : '') . 'ORDER BY `' . sql_escape($lang) .  '` ASC');
                    while ($r = sql_fetch_array($rs))
                        $languages .= '<option value="' . $r['short'] . '"' . (($r['short'] == $desc_lang) ? ' selected="selected"' : '') . '>' . htmlspecialchars($r['name'], ENT_COMPAT, 'UTF-8') . '</option>' . "\n";
                    mysql_free_result($rs);
                    tpl_set_var('desclangs', $languages);


                    if ($show_all_langs == false){
                        $show_all_langs_submit = '&nbsp;<input type="submit" name="show_all_langs" value="'.tr('edDescShowAll').'" />';
                        tpl_set_var('show_all_langs_submit', $show_all_langs_submit);
                    } else {
                        tpl_set_var('show_all_langs_submit', '');
                    }

                    tpl_set_var('show_all_langs_value', (($show_all_langs == false) ? 0 : 1));
                    tpl_set_var('short_desc', htmlspecialchars($short_desc, ENT_COMPAT, 'UTF-8'));
                    tpl_set_var('hints', $hint);
                    tpl_set_var('descid', $descid);
                    tpl_set_var('cacheid', htmlspecialchars($desc_record['cache_id'], ENT_COMPAT, 'UTF-8'));
                    tpl_set_var('desclang', htmlspecialchars($desc_lang, ENT_COMPAT, 'UTF-8'));
                    tpl_set_var('desclang_name', htmlspecialchars(db_LanguageFromShort($desc_lang), ENT_COMPAT, 'UTF-8'));
                    tpl_set_var('cachename', htmlspecialchars($desc_record['name'], ENT_COMPAT, 'UTF-8'));


                    // TinyMCE
                    $headers = tpl_get_var('htmlheaders') . "\n";
                    //$headers .= '<script language="javascript" type="text/javascript" src="lib/phpfuncs.js"></script>' . "\n";
                    tpl_set_var('htmlheaders', $headers);
                }
                else
                {
                    //TODO: not the owner
                }
            }
            else
                tpl_errorMsg('editdesc', $error_desc_not_found);
        }
    }

    //make the template and send it out
    tpl_set_var('language4js',$lang);
    tpl_BuildTemplate();
?>
