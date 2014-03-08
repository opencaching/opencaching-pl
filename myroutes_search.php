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

   Unicode Reminder メモ

 ****************************************************************************/

    //prepare the templates and include all neccessary
    require_once('./lib/common.inc.php');
    require_once($rootpath . 'lib/calculation.inc.php');
    require_once('./lib/cache_icon.inc.php');
    require_once($rootpath . 'lib/caches.inc.php');
    require_once($stylepath . '/lib/icons.inc.php');
    global $content, $bUseZip, $sqldebug, $usr;
    global $default_lang, $cache_attrib_jsarray_line, $cache_attrib_img_line;
    global $lang, $language, $dateFormat;
    $database=new dataBase;
    set_time_limit(1800);
    //Preprocessing
    if ($error == false)
    {
        //user logged in?
        if ($usr == false)
        {
            $target = urlencode(tpl_get_current_page());
            tpl_redirect('login.php?target='.$target);
        }
        else
        {
            $tplname = 'myroutes_search';
            $user_id = $usr['userid'];

            if (isset($_REQUEST['routeid']))
            {
            $route_id = $_REQUEST['routeid'];
            }
            if (isset($_POST['routeid'])){
            $route_id = $_POST['routeid'];}

            if (isset($_POST['distance'])){
            $distance = $_POST['distance'];}


            tpl_set_var('cachemap_header', '<script src="//maps.googleapis.com/maps/api/js?libraries=geometry&amp;sensor=false&amp;language='.$lang.'" type="text/javascript"></script>');

            $database->paramQuery(
                'SELECT `user_id`,`name`, `description`, `radius`, `options` FROM `routes` WHERE `route_id`=:route_id AND `user_id`=:user_id',
                array ('user_id'=> array('value' => $user_id, 'data_type' => 'integer'),
                        'route_id'=> array('value' => $route_id, 'data_type' => 'integer'))
            );
            $record = $database->dbResultFetch();
            $distance=$record['radius'];
            tpl_set_var('routes_name',$record['name']);
            tpl_set_var('distance',$record['radius']);
            tpl_set_var('routeid',$route_id);
            $database->paramQuery(
                'SELECT  length(`options`) `optsize`, `options` FROM `routes` WHERE `route_id`=:route_id AND `user_id`=:user_id',
                array ('user_id'=> array('value' => $user_id, 'data_type' => 'integer'),
                        'route_id'=> array('value' => $route_id, 'data_type' => 'integer'))
            );
            $rec = $database->dbResultFetch();
            $optsize= $rec['optsize'];


            if (isset($_POST['cache_attribs_not']))
            {
                if ($_POST['cache_attribs_not'] != '')
                    $options['cache_attribs_not'] = mb_split(';', $_POST['cache_attribs_not']);
                else
                    $options['cache_attribs_not'] = array();
            }
            else
                $options['cache_attribs_not'] = array();


            if (isset($_POST['cache_attribs']))
            {
                if ($_POST['cache_attribs'] != '')
                    $options['cache_attribs'] = mb_split(';', $_POST['cache_attribs']);
                else
                    $options['cache_attribs'] = array();
            }
            else
                $options['cache_attribs'] = array();




        if (isset($_POST['submit']) || isset($_POST['submit_map']) )
        {

            $options['f_userowner'] = isset($_POST['f_userowner']) ? $_POST['f_userowner'] : '';
            $options['f_userfound'] = isset($_POST['f_userfound']) ? $_POST['f_userfound'] : '';
            $options['f_inactive'] = isset($_POST['f_inactive']) ? $_POST['f_inactive'] : '';
            $options['f_ignored'] = isset($_POST['f_ignored']) ? $_POST['f_ignored'] : '';

            $options['cachetype1'] = isset($_POST['cachetype1']) ? $_POST['cachetype1'] : '';
            $options['cachetype2'] = isset($_POST['cachetype2']) ? $_POST['cachetype2'] : '';
            $options['cachetype3'] = isset($_POST['cachetype3']) ? $_POST['cachetype3'] : '';
            $options['cachetype4'] = isset($_POST['cachetype4']) ? $_POST['cachetype4'] : '';
            $options['cachetype5'] = isset($_POST['cachetype5']) ? $_POST['cachetype5'] : '';
            $options['cachetype6'] = isset($_POST['cachetype6']) ? $_POST['cachetype6'] : '';
            $options['cachetype7'] = isset($_POST['cachetype7']) ? $_POST['cachetype7'] : '';
            $options['cachetype8'] = isset($_POST['cachetype8']) ? $_POST['cachetype8'] : '';
            $options['cachetype9'] = isset($_POST['cachetype9']) ? $_POST['cachetype9'] : '';
            $options['cachetype10'] = isset($_POST['cachetype10']) ? $_POST['cachetype10'] : '';

            $options['cachesize_1'] = isset($_POST['cachesize_1']) ? $_POST['cachesize_1'] : '';
            $options['cachesize_2'] = isset($_POST['cachesize_2']) ? $_POST['cachesize_2'] : '';
            $options['cachesize_3'] = isset($_POST['cachesize_3']) ? $_POST['cachesize_3'] : '';
            $options['cachesize_4'] = isset($_POST['cachesize_4']) ? $_POST['cachesize_4'] : '';
            $options['cachesize_5'] = isset($_POST['cachesize_5']) ? $_POST['cachesize_5'] : '';
            $options['cachesize_6'] = isset($_POST['cachesize_6']) ? $_POST['cachesize_6'] : '';
            $options['cachesize_7'] = isset($_POST['cachesize_7']) ? $_POST['cachesize_7'] : '';

            $options['cachevote_1'] = isset($_POST['cachevote_1']) ? $_POST['cachevote_1'] : '';
            $options['cachevote_2'] = isset($_POST['cachevote_2']) ? $_POST['cachevote_2'] : '';
            $options['cachenovote'] = isset($_POST['cachenovote']) ? $_POST['cachenovote'] : '';

            $options['cachedifficulty_1'] = isset($_POST['cachedifficulty_1']) ? $_POST['cachedifficulty_1'] : '';
            $options['cachedifficulty_2'] = isset($_POST['cachedifficulty_2']) ? $_POST['cachedifficulty_2'] : '';

            $options['cacheterrain_1'] = isset($_POST['cacheterrain_1']) ? $_POST['cacheterrain_1'] : '';
            $options['cacheterrain_2'] = isset($_POST['cacheterrain_2']) ? $_POST['cacheterrain_2'] : '';

            $options['cacherating'] = isset($_POST['cacherating']) ? $_POST['cacherating'] : '';
//          $options['cache_attribs'] = isset($_POST['cache_attribs']) ? $_POST['cache_attribs'] : '';
//          $options['cache_attribs_not'] = isset($_POST['cache_attribs_not']) ? $_POST['cache_attribs_not'] : '';

        } elseif ($optsize!="0" || isset($_POST['back'])) {
                $options= unserialize($rec['options']);
        } else {
            $options['f_userowner'] = isset($_POST['f_userowner']) ? $_POST['f_userowner'] : '1';
            $options['f_userfound'] = isset($_POST['f_userfound']) ? $_POST['f_userfound'] : '1';
            $options['f_inactive'] = isset($_POST['f_inactive']) ? $_POST['f_inactive'] : '1';
            $options['f_ignored'] = isset($_POST['f_ignored']) ? $_POST['f_ignored'] : '1';

            $options['cachetype1'] = isset($_POST['cachetype1']) ? $_POST['cachetype1'] : '1';
            $options['cachetype2'] = isset($_POST['cachetype2']) ? $_POST['cachetype2'] : '1';
            $options['cachetype3'] = isset($_POST['cachetype3']) ? $_POST['cachetype3'] : '1';
            $options['cachetype4'] = isset($_POST['cachetype4']) ? $_POST['cachetype4'] : '1';
            $options['cachetype5'] = isset($_POST['cachetype5']) ? $_POST['cachetype5'] : '1';
            $options['cachetype6'] = isset($_POST['cachetype6']) ? $_POST['cachetype6'] : '1';
            $options['cachetype7'] = isset($_POST['cachetype7']) ? $_POST['cachetype7'] : '1';
            $options['cachetype8'] = isset($_POST['cachetype8']) ? $_POST['cachetype8'] : '1';
            $options['cachetype9'] = isset($_POST['cachetype9']) ? $_POST['cachetype9'] : '1';
            $options['cachetype10'] = isset($_POST['cachetype10']) ? $_POST['cachetype10'] : '1';

            $options['cachesize_1'] = isset($_POST['cachesize_1']) ? $_POST['cachesize_1'] : '1';
            $options['cachesize_2'] = isset($_POST['cachesize_2']) ? $_POST['cachesize_2'] : '1';
            $options['cachesize_3'] = isset($_POST['cachesize_3']) ? $_POST['cachesize_3'] : '1';
            $options['cachesize_4'] = isset($_POST['cachesize_4']) ? $_POST['cachesize_4'] : '1';
            $options['cachesize_5'] = isset($_POST['cachesize_5']) ? $_POST['cachesize_5'] : '1';
            $options['cachesize_6'] = isset($_POST['cachesize_6']) ? $_POST['cachesize_6'] : '1';
            $options['cachesize_7'] = isset($_POST['cachesize_7']) ? $_POST['cachesize_7'] : '1';

            $options['cachevote_1'] = isset($_POST['cachevote_1']) ? $_POST['cachevote_1'] : '-3';
            $options['cachevote_2'] = isset($_POST['cachevote_2']) ? $_POST['cachevote_2'] : '3';
            $options['cachenovote'] = isset($_POST['cachenovote']) ? $_POST['cachenovote'] : '1';

            $options['cachedifficulty_1'] = isset($_POST['cachedifficulty_1']) ? $_POST['cachedifficulty_1'] : '1';
            $options['cachedifficulty_2'] = isset($_POST['cachedifficulty_2']) ? $_POST['cachedifficulty_2'] : '5';

            $options['cacheterrain_1'] = isset($_POST['cacheterrain_1']) ? $_POST['cacheterrain_1'] : '1';
            $options['cacheterrain_2'] = isset($_POST['cacheterrain_2']) ? $_POST['cacheterrain_2'] : '5';

            $options['cacherating'] = isset($_POST['cacherating']) ? $_POST['cacherating'] : '0';
//          $options['cache_attribs'] = isset($_POST['cache_attribs']) ? $_POST['cache_attribs'] : '';
//          $options['cache_attribs_not'] = isset($_POST['cache_attribs_not']) ? $_POST['cache_attribs_not'] : '';

            }

        // for myroute_result
        $logs =isset($_POST['nrlogs']) ? $_POST['nrlogs'] : '';
        $cache_logs = isset($_POST['cache_log']) ? $_POST['cache_log'] : '0';
        tpl_set_var('all_logs_caches', ($logs == 0) ? ' checked="checked"' : '');
        tpl_set_var('min_logs_caches', ($logs > 0) ? ' checked="checked"' : '');
        tpl_set_var('nrlogs', ($logs > 0) ? $logs : 0);
        tpl_set_var('min_logs_caches_disabled', ($logs == 0) ? ' disabled="disabled"' : '');



    $cache_attrib_jsarray_line = "new Array('{id}', {state}, '{text_long}', '{icon}', '{icon_no}', '{icon_undef}', '{category}')";
    $cache_attrib_img_line = '<img id="attrimg{id}" src="{icon}" title="{text_long}" alt="{text_long}" onmousedown="switchAttribute({id})" style="cursor: pointer;" />&nbsp;';

  $lang_attribute = $lang;
  if ($lang != $lang) { $lang_attribute = $lang; }



function attr_jsline($tpl, $options, $id, $textlong, $iconlarge, $iconno, $iconundef, $category)
{
    $line = $tpl;

        $line = mb_ereg_replace('{id}', $id, $line);

        if (array_search($id, $options['cache_attribs']) === false)
        {
            if (array_search($id, $options['cache_attribs_not']) === false)
                $line = mb_ereg_replace('{state}', 0, $line);
            else
                $line = mb_ereg_replace('{state}', 2, $line);
        }
        else
            $line = mb_ereg_replace('{state}', 1, $line);

        $line = mb_ereg_replace('{text_long}', addslashes($textlong), $line);
        $line = mb_ereg_replace('{icon}', $iconlarge, $line);
        $line = mb_ereg_replace('{icon_no}', $iconno, $line);
        $line = mb_ereg_replace('{icon_undef}', $iconundef, $line);
        $line = mb_ereg_replace('{category}', $category, $line);

        return $line;
}

function attr_image($tpl, $options, $id, $textlong, $iconlarge, $iconno, $iconundef, $category)
{
    $line = $tpl;

    $line = mb_ereg_replace('{id}', $id, $line);
    $line = mb_ereg_replace('{text_long}', $textlong, $line);

    if (array_search($id, $options['cache_attribs']) === false)
    {
        if (array_search($id, $options['cache_attribs_not']) === false)
            $line = mb_ereg_replace('{icon}', $iconundef, $line);
        else
            $line = mb_ereg_replace('{icon}', $iconno, $line);
    }
    else
        $line = mb_ereg_replace('{icon}', $iconlarge, $line);
    return $line;
}


    // cache-attributes
    $attributes_jsarray = '';
    $attributes_img = '';
    $attributesCat2_img = '';

    $database->multiVariableQuery(
        'SELECT `id`, `text_long`, `icon_large`, `icon_no`, `icon_undef`, `category` FROM `cache_attrib` WHERE `language`=:1 ORDER BY `id`',
        $lang_attribute
    );

    while ($record = $database->dbResultFetch())
    {

        // icon specified
        $line = attr_jsline($cache_attrib_jsarray_line, $options, $record['id'], $record['text_long'], $record['icon_large'], $record['icon_no'], $record['icon_undef'], $record['category']);

        if ($attributes_jsarray != '') $attributes_jsarray .= ",\n";
        $attributes_jsarray .= $line;

        $line = attr_image($cache_attrib_img_line, $options, $record['id'], $record['text_long'], $record['icon_large'], $record['icon_no'], $record['icon_undef'], $record['category']);


        if ($record['category'] != 1)
            $attributesCat2_img .= $line;
        else
            $attributes_img .= $line;
    }
    $line = attr_jsline($cache_attrib_jsarray_line, $options, "99", tr("with_password"), "images/attributes/password.png", "images/attributes/password-no.png", "images/attributes/password-undef.png", 0);
    $attributes_jsarray .= ",\n".$line;

    $line = attr_image($cache_attrib_img_line, $options, "99", tr("with_password"), "images/attributes/password.png", "images/attributes/password-no.png", "images/attributes/password-undef.png", 0);
    $attributes_img .= $line;

    tpl_set_var('cache_attrib_list', $attributes_img);
    tpl_set_var('cache_attribCat2_list', $attributesCat2_img);
    tpl_set_var('attributes_jsarray', $attributes_jsarray);
    tpl_set_var('hidopt_attribs', implode(';', $options['cache_attribs']));
    tpl_set_var('hidopt_attribs_not', implode(';', $options['cache_attribs_not']));

    tpl_set_var('f_inactive_checked', ($options['f_inactive'] == 1) ? ' checked="checked"' : '');
    tpl_set_var('hidopt_inactive', ($options['f_inactive'] == 1) ? '1' : '0');

    tpl_set_var('f_ignored_disabled', ($usr['userid'] == 0) ? ' disabled="disabled"' : '');
    if ($usr['userid'] != 0)
        tpl_set_var('f_ignored_disabled', ($options['f_ignored'] == 1) ? ' checked="checked"' : '');
    tpl_set_var('hidopt_ignored', ($options['f_ignored'] == 1) ? '1' : '0');

    tpl_set_var('f_userfound_disabled', ($usr['userid'] == 0) ? ' disabled="disabled"' : '');
    if ($usr['userid'] != 0)
        tpl_set_var('f_userfound_disabled', ($options['f_userfound'] == 1) ? ' checked="checked"' : '');
    tpl_set_var('hidopt_userfound', ($options['f_userfound'] == 1) ? '1' : '0');

    tpl_set_var('f_userowner_disabled', ($usr['userid'] == 0) ? ' disabled="disabled"' : '');
    if ($usr['userid'] != 0)
        tpl_set_var('f_userowner_disabled', ($options['f_userowner'] == 1) ? ' checked="checked"' : '');
    tpl_set_var('hidopt_userowner', ($options['f_userowner'] == 1) ? '1' : '0');


    if (isset($options['cacherating'])) {
        tpl_set_var('all_caches_checked', ($options['cacherating'] == 0) ? ' checked="checked"' : '');
        tpl_set_var('cacherating', $options['cacherating']);
        tpl_set_var('recommended_caches_checked', ($options['cacherating'] > 0) ? ' checked="checked"' : '');
        tpl_set_var('cache_min_rec', ($options['cacherating'] > 0) ? $options['cacherating'] : 0);
        tpl_set_var('min_rec_caches_disabled', ($options['cacherating'] == 0) ? ' disabled="disabled"' : '');
    }


    if (isset($options['cachedifficulty_1']))
    {   $cdf=$options['cachedifficulty_1']*2;
    tpl_set_var('cdf'.$cdf.'',' selected="selected"');}

    if (isset($options['cachedifficulty_2']))
    {   $cd=$options['cachedifficulty_2']*2;
    tpl_set_var('cdt'.$cd.'',' selected="selected"');}

    if (isset($options['cacheterrain_1']))
    {   $cd=$options['cacheterrain_1']*2;
    tpl_set_var('ctf'.$cd.'',' selected="selected"');}

    if (isset($options['cacheterrain_2']))
    {   $cd=$options['cacheterrain_2']*2;
    tpl_set_var('ctt'.$cd.'',' selected="selected"');}

    if (isset($options['cachevote_1']))
    {   $cd=abs(round($options['cachevote_1']*2));
    tpl_set_var('cvf'.$cd.'',' selected="selected"');}

    if (isset($options['cachevote_2']))
    {   $cd=round($options['cachevote_2']*2);
    tpl_set_var('cvt'.$cd.'',' selected="selected"');}

    if ($options['cachenovote']==1)
    {
    tpl_set_var('cachev', ' checked="checked"');} else {
    tpl_set_var('cachev', '');}

    if (isset($options['cachetype1']))
    {tpl_set_var('cachetype1',  ($options['cachetype1'] == 1) ? ' checked="checked"' : '');}
    if (isset($options['cachetype2']))
    {tpl_set_var('cachetype2',  ($options['cachetype2'] == 1) ? ' checked="checked"' : '');}
        if (isset($options['cachetype3']))
    {tpl_set_var('cachetype3',  ($options['cachetype3'] == 1) ? ' checked="checked"' : '');}
        if (isset($options['cachetype4']))
    {tpl_set_var('cachetype4',  ($options['cachetype4'] == 1) ? ' checked="checked"' : '');}
        if (isset($options['cachetype5']))
    {tpl_set_var('cachetype5',  ($options['cachetype5'] == 1) ? ' checked="checked"' : '');}
        if (isset($options['cachetype6']))
    {tpl_set_var('cachetype6',  ($options['cachetype6'] == 1) ? ' checked="checked"' : '');}
        if (isset($options['cachetype7']))
    {tpl_set_var('cachetype7',  ($options['cachetype7'] == 1) ? ' checked="checked"' : '');}
        if (isset($options['cachetype8']))
    {tpl_set_var('cachetype8',  ($options['cachetype8'] == 1) ? ' checked="checked"' : '');}
            if (isset($options['cachetype9']))
    {tpl_set_var('cachetype9',  ($options['cachetype9'] == 1) ? ' checked="checked"' : '');}
            if (isset($options['cachetype10']))
    {tpl_set_var('cachetype10',  ($options['cachetype10'] == 1) ? ' checked="checked"' : '');}

    if (isset($options['cachesize_1']))
    {tpl_set_var('cachesize_1',  ($options['cachesize_1'] == 1) ? ' checked="checked"' : '');}
    if (isset($options['cachesize_2']))
    {tpl_set_var('cachesize_2',  ($options['cachesize_2'] == 1) ? ' checked="checked"' : '');}
        if (isset($options['cachesize_3']))
    {tpl_set_var('cachesize_3',  ($options['cachesize_3'] == 1) ? ' checked="checked"' : '');}
        if (isset($options['cachesize_4']))
    {tpl_set_var('cachesize_4',  ($options['cachesize_4'] == 1) ? ' checked="checked"' : '');}
        if (isset($options['cachesize_5']))
    {tpl_set_var('cachesize_5',  ($options['cachesize_5'] == 1) ? ' checked="checked"' : '');}
        if (isset($options['cachesize_6']))
    {tpl_set_var('cachesize_6',  ($options['cachesize_6'] == 1) ? ' checked="checked"' : '');}
        if (isset($options['cachesize_7']))
    {tpl_set_var('cachesize_7',  ($options['cachesize_7'] == 1) ? ' checked="checked"' : '');}



                // SQL additional options
                if(!isset($options['f_userowner'])) $options['f_userowner']='0';
                if($options['f_userowner'] != 0) { $sql_where[] = '`caches`.`user_id`!=\'' . $usr['userid'] .'\''; }

                if(!isset($options['f_userfound'])) $options['f_userfound']='0';
                if($options['f_userfound'] != 0)
                {
                    $sql_where[] = '`caches`.`cache_id` NOT IN (SELECT `cache_logs`.`cache_id` FROM `cache_logs` WHERE `cache_logs`.`deleted`=0 AND `cache_logs`.`user_id`=\'' . sql_escape($usr['userid']) . '\' AND `cache_logs`.`type` IN (1, 7))';
                }

                if(!isset($options['f_inactive'])) $options['f_inactive']='0';
                if($options['f_inactive'] != 0)  $sql_where[] = '`caches`.`status`=1';

                if(isset($usr))
                {
                    if(!isset($options['f_ignored'])) $options['f_ignored']='0';
                    if($options['f_ignored'] != 0)
                    {
                        $sql_where[] = '`caches`.`cache_id` NOT IN (SELECT `cache_ignore`.`cache_id` FROM `cache_ignore` WHERE `cache_ignore`.`user_id`=\'' . sql_escape($usr['userid']) . '\')';
                    }
                }


                if(isset($options['cache_attribs']) && count($options['cache_attribs']) > 0)
                {
                    for($i=0; $i < count($options['cache_attribs']); $i++)
                    {
                        if($options['cache_attribs'][$i] == 99) // special password attribute case
                            $sql_where[] = '`caches`.`logpw` != ""';
                        else {
                            $sql_from[] = '`caches_attributes` `a' . ($options['cache_attribs'][$i]+0) . '`';
                            $sql_where[] = '`a' . ($options['cache_attribs'][$i]+0) . '`.`cache_id`=`caches`.`cache_id`';
                            $sql_where[] = '`a' . ($options['cache_attribs'][$i]+0) . '`.`attrib_id`=' . ($options['cache_attribs'][$i]+0);
                        }
                    }
                }

                if(isset($options['cache_attribs_not']) && count($options['cache_attribs_not']) > 0)
                {
                    for($i=0; $i < count($options['cache_attribs_not']); $i++)
                    {
                        if($options['cache_attribs_not'][$i] == 99) // special password attribute case
                            $sql_where[] = '`caches`.`logpw` = ""';
                        else
                            $sql_where[] = 'NOT EXISTS (SELECT `caches_attributes`.`cache_id` FROM `caches_attributes` WHERE `caches_attributes`.`cache_id`=`caches`.`cache_id` AND `caches_attributes`.`attrib_id`=\'' . sql_escape($options['cache_attribs_not'][$i]) . '\')';
                    }
                }



                $cachetype = array();

                if (isset($options['cachetype1']) && ($options['cachetype1'] == '1')) { $cachetype[] = '1'; }
                if (isset($options['cachetype2']) && ($options['cachetype2'] == '1')) { $cachetype[] = '2'; }
                if (isset($options['cachetype3']) && ($options['cachetype3'] == '1')) { $cachetype[] = '3'; }
                if (isset($options['cachetype4']) && ($options['cachetype4'] == '1')) { $cachetype[] = '4'; }
                if (isset($options['cachetype5']) && ($options['cachetype5'] == '1')) { $cachetype[] = '5'; }
                if (isset($options['cachetype6']) && ($options['cachetype6'] == '1')) { $cachetype[] = '6'; }
                if (isset($options['cachetype7']) && ($options['cachetype7'] == '1')) { $cachetype[] = '7'; }
                if (isset($options['cachetype8']) && ($options['cachetype8'] == '1')) { $cachetype[] = '8'; }
                if (isset($options['cachetype9']) && ($options['cachetype9'] == '1')) { $cachetype[] = '9'; }
                if (isset($options['cachetype10']) && ($options['cachetype10'] == '1')) { $cachetype[] = '10'; }

                if ((sizeof($cachetype) > 0) && (sizeof($cachetype) < 10)) {
                        $sql_where[] = '`caches`.`type` IN (' . sql_escape(implode(",", $cachetype)) . ')';
                    }


                $cachesize = array();

                if (isset($options['cachesize_1']) && ($options['cachesize_1'] == '1')) { $cachesize[] = '1'; }
                if (isset($options['cachesize_2']) && ($options['cachesize_2'] == '1')) { $cachesize[] = '2'; }
                if (isset($options['cachesize_3']) && ($options['cachesize_3'] == '1')) { $cachesize[] = '3'; }
                if (isset($options['cachesize_4']) && ($options['cachesize_4'] == '1')) { $cachesize[] = '4'; }
                if (isset($options['cachesize_5']) && ($options['cachesize_5'] == '1')) { $cachesize[] = '5'; }
                if (isset($options['cachesize_6']) && ($options['cachesize_6'] == '1')) { $cachesize[] = '6'; }
                if (isset($options['cachesize_7']) && ($options['cachesize_7'] == '1')) { $cachesize[] = '7'; }
                if ((sizeof($cachesize) > 0) && (sizeof($cachesize) < 7)) {
                    $sql_where[] = '`caches`.`size` IN (' . implode(' , ', $cachesize) . ')';
                }

                if(!isset($options['cachevote_1']) && !isset($options['cachevote_2'])) {
                    $options['cachevote_1']='';
                    $options['cachevote_2']='';
                }
                if( ( ($options['cachevote_1'] != '') && ($options['cachevote_2'] != '') ) && ( ($options['cachevote_1'] != '0') || ($options['cachevote_2'] != '6') ) && ( (!isset($options['cachenovote'])) || ($options['cachenovote'] != '1') ) )
                {
                    $sql_where[] = '`caches`.`score` BETWEEN \'' . sql_escape($options['cachevote_1']) . '\' AND \'' . sql_escape($options['cachevote_2']) . '\' AND `caches`.`votes` > 3';
                } else if ( ($options['cachevote_1'] != '') && ($options['cachevote_2'] != '') && ( ($options['cachevote_1'] != '0') || ($options['cachevote_2'] != '6') ) && isset($options['cachenovote']) && ($options['cachenovote'] == '1') )  {
                    $sql_where[] = '((`caches`.`score` BETWEEN \'' . sql_escape($options['cachevote_1']) . '\' AND \'' . sql_escape($options['cachevote_2']) . '\' AND `caches`.`votes` > 3) OR (`caches`.`votes` < 4))';
                }

                if(!isset($options['cachedifficulty_1']) && !isset($options['cachedifficulty_2'])) {
                    $options['cachedifficulty_1']='';
                    $options['cachedifficulty_2']='';
                }
                if((($options['cachedifficulty_1'] != '') && ($options['cachedifficulty_2'] != '')) && (($options['cachedifficulty_1'] != '1') || ($options['cachedifficulty_2'] != '5')))
                {
                    $sql_where[] = '`caches`.`difficulty` BETWEEN \'' . sql_escape($options['cachedifficulty_1'] * 2) . '\' AND \'' . sql_escape($options['cachedifficulty_2'] * 2) . '\'';
                }

                if(!isset($options['cacheterrain_1']) && !isset($options['cacheterrain_2'])) {
                    $options['cacheterrain_1']='';
                    $options['cacheterrain_2']='';
                }

                if((($options['cacheterrain_1'] != '') && ($options['cacheterrain_2'] != '')) && (($options['cacheterrain_1'] != '1') || ($options['cacheterrain_2'] != '5')))
                {
                    $sql_where[] = '`caches`.`terrain` BETWEEN \'' . sql_escape($options['cacheterrain_1'] * 2) . '\' AND \'' . sql_escape($options['cacheterrain_2'] * 2) . '\'';
                }

                if($options['cacherating'] > 0) {
                    $sql_where[] = '`caches`.`topratings` >= \'' . $options['cacherating'] .'\'';
                }

                    // show only published caches
                    //  HIDDEN_FOR_APPROVAL
                    $sql_where[] = '`caches`.`status` != 4';
                    //  NOT_YET_AVAILABLE
                    $sql_where[] = '`caches`.`status` != 5';
                    //   BLOCKED
                    $sql_where[] = '`caches`.`status` != 6';
                    // search byname
                    $sql_select[] = '`caches`.`cache_id` `cache_id`';

                    $sql_from[] = '`caches`';
                    //do the search
                    $sqlFilter = 'SELECT ' . implode(',', $sql_select) .
                        ' FROM ' . implode(',', $sql_from) .
                        ' WHERE ' . implode(' AND ', $sql_where);
//echo $sqlFilter;

    function getPictures($cacheid, $picturescount)
    {
        global $dblink;
        global $thumb_max_width;
        global $thumb_max_height;

        $database = new dataBase;
        $database->multiVariableQuery(
            'SELECT uuid, title, url, spoiler FROM pictures WHERE object_id=:1 AND object_type=2 AND display=1 ORDER BY date_created',
            $cacheid
        );

        while ($r = $database->dbResultFetch())
        {
            $retval .= '&lt;img src="'.$r['url'].'"&gt;&lt;br&gt;'.cleanup_text($r['title']).'&lt;br&gt;';
        }

        return $retval;
    }


    function xmlentities($str)
    {
        $from[0] = '&'; $to[0] = '&amp;';
        $from[1] = '<'; $to[1] = '&lt;';
        $from[2] = '>'; $to[2] = '&gt;';
        $from[3] = '"'; $to[3] = '&quot;';
        $from[4] = '\''; $to[4] = '&apos;';
        $from[5] = ']]>'; $to[5] = ']] >';

        for ($i = 0; $i <= 4; $i++)
            $str = str_replace($from[$i], $to[$i], $str);
        $str = preg_replace('/[[:cntrl:]]/', '', $str);
        return $str;
    }
    function append_output($str)
    {
        global $content, $bUseZip, $sqldebug;
        if ($sqldebug == true) return;

        if ($bUseZip == true)
            $content .= $str;
        else
            echo $str;
            }
function PlConvert($source,$dest,$tekst)
{
    $source=strtoupper($source);
    $dest=strtoupper($dest);
    if($source==$dest) return $tekst;

    $chars['POLSKAWY']    =array('a','c','e','l','n','o','s','z','z','A','C','E','L','N','O','S','Z','Z');
    $chars['ISO-8859-2']  =array("\xB1","\xE6","\xEA","\xB3","\xF1","\xF3","\xB6","\xBC","\xBF","\xA1","\xC6","\xCA","\xA3","\xD1","\xD3","\xA6","\xAC","\xAF");
    $chars['WINDOWS-1250']=array("\xB9","\xE6","\xEA","\xB3","\xF1","\xF3","\x9C","\x9F","\xBF","\xA5","\xC6","\xCA","\xA3","\xD1","\xD3","\x8C","\x8F","\xAF");
    $chars['UTF-8']       =array('ą','ć','ę','ł','ń','ó','ś','ź','ż','Ą','Ć','Ę','Ł','Ń','Ó','Ś','Ź','Ż');
    $chars['ENTITIES']    =array('ą','ć','ę','ł','ń','ó','ś','ź','ż','Ą','Ć','Ę','Ł','Ń','Ó','Ś','Ź','Ż');

    if(!isset($chars[$source])) return false;
    if(!isset($chars[$dest])) return false;

    $tekst = str_replace('a', 'a', $tekst);
    $tekst = str_replace('é', 'e', $tekst);

    return str_replace($chars[$source],$chars[$dest],$tekst);
}
    function cleanup_text($str)
        {
          $str = strip_tags($str, "<li>");
          $from[] = '&nbsp;'; $to[] = ' ';
          $from[] = '<p>'; $to[] = '';
         $from[] = '\n'; $to[] = '';
         $from[] = '\r'; $to[] = '';
          $from[] = '</p>'; $to[] = "";
          $from[] = '<br>'; $to[] = "";
          $from[] = '<br />'; $to[] = "";
         $from[] = '<br/>'; $to[] = "";

          $from[] = '<li>'; $to[] = " - ";
          $from[] = '</li>'; $to[] = "";

          $from[] = '&oacute;'; $to[] = 'o';
          $from[] = '&quot;'; $to[] = '"';
          $from[] = '&[^;]*;'; $to[] = '';

          $from[] = '&'; $to[] = '';
          $from[] = '\''; $to[] = '';
          $from[] = '"'; $to[] = '';
          $from[] = '<'; $to[] = '';
          $from[] = '>'; $to[] = '';
          $from[] = '('; $to[] = ' -';
          $from[] = ')'; $to[] = '- ';
          $from[] = ']]>'; $to[] = ']] >';
     $from[] = ''; $to[] = '';

          for ($i = 0; $i < count($from); $i++)
            $str = str_replace($from[$i], $to[$i], $str);
        $str = preg_replace('/[[:cntrl:]]/', '', $str);

          return $str;
        }



/**
    * function cache_distances ($lat1, $lon1, $lat2, $lon2)
  */
    function cache_distances($lat1, $lon1, $lat2, $lon2) {
        if ( ( $lon1 == $lon2 ) AND ( $lat1 == $lat2 ) ) {
        return(0);
        } else {
        $earth_radius = 6378;
        foreach(array("lat1","lon1","lat2","lon2") as $ordinate)
        $$ordinate = $$ordinate*(pi()/180);
        $dist = acos(cos($lat1)*cos($lon1)*cos($lat2)*cos($lon2) +
        cos($lat1)*sin($lon1)*cos($lat2)*sin($lon2) +
        sin($lat1)*sin($lat2)) * $earth_radius;
            return($dist);
            }
        }

//*************************************************************************
// Find all the caches that appear with $distance from each point in the defined $route_id.
//*************************************************************************
function caches_along_route($route_id, $distance) {

    $initial_cache_list = array();
    $inter_cache_list = array();
    $final_cache_list = array();

    // Get caches where within the minimum bounding box of the route
    // Actually, add the distance to the minimum bounding box
    //  1 degree is around 110km (close enough)
    $smallestLat = 0;
    $largestLat = 0;
    $smallestLon = 0;
    $largestLon = 0;

    $database = new dataBase;
    $database->multiVariableQuery(
        'SELECT min(`route_points`.`lat`) `smallest_lat`,
            max(`route_points`.`lat`) `largest_lat`,
            min(`route_points`.`lon`) `smallest_lon`,
            max(`route_points`.`lon`) `largest_lon`
        FROM `route_points` WHERE `route_id`=:1',
        $route_id);
    $data = $database->dbResultFetch();
    if ($data){
        $smallestLat = $data['smallest_lat'];
        $largestLat = $data['largest_lat'];
        $smallestLon = $data['smallest_lon'];
        $largestLon = $data['largest_lon'];
    }

    // 110 km is width of 1 deg
    $bounds_min_lat = $smallestLat - $distance/110;
    $bounds_max_lat = $largestLat + $distance/110;
    $bounds_min_lon = $smallestLon - $distance/110;
    $bounds_max_lon = $largestLon + $distance/110;

    $mapcenterLat = ($bounds_min_lat + $bounds_max_lat)/2;
    $mapcenterLon = ($bounds_min_lon + $bounds_max_lon)/2;
    tpl_set_var('latlonmin', $bounds_min_lat.','.$bounds_min_lon);
    tpl_set_var('latlonmax', $bounds_max_lat.','.$bounds_max_lon);
    tpl_set_var('mapcenterLat', $mapcenterLat);
    tpl_set_var('mapcenterLon', $mapcenterLon);

    $database->multiVariableQuery(
        'SELECT wp_oc waypoint, latitude lat, longitude lon FROM caches
          WHERE latitude>:1
            AND latitude<:2
            AND longitude>:3
            AND longitude<:4
            AND status not in (3, 4, 5, 6)',
        $bounds_min_lat, $bounds_max_lat, $bounds_min_lon, $bounds_max_lon);
    while ($row = $database->dbResultFetch()) {
        $initial_cache_list[] =array("waypoint"=>$row['waypoint'],"lat"=>$row['lat'],"lon"=>$row['lon']);
    }
    $points = array();
    $database->paramQuery(
        'SELECT * FROM route_points WHERE route_id = :route_id  ORDER BY point_nr',
        array ('route_id'=> array('value' => $route_id, 'data_type' => 'integer'))
    );

    while ($row = $database->dbResultFetch()) {
        $points[] = array("lat"=>$row["lat"],"lon"=>$row["lon"]);
    }
    foreach ($initial_cache_list as $list) {
        foreach ($points as $point) {
            $route_distance =cache_distances($point["lat"],$point["lon"],$list["lat"],$list["lon"]);
            if ( $route_distance <= $distance ) {
                if ( !$inter_cache_list[$list['waypoint']] ) {
                    $final_cache_list[] = $list['waypoint'];
                    $inter_cache_list[$list['waypoint']] = $list['waypoint'];
                    break;
                }
            }
        }
    }

    return $final_cache_list;
}
// end of function

function set_route_options($route_id, $options) {
    $database = new dataBase;
    $database->paramQuery(
        'UPDATE `routes` SET `options`=:options WHERE `route_id`=:route_id',
        array ('route_id'=> array('value' => $route_id, 'data_type' => 'integer'),
            'options'=> array('value' => serialize($options), 'data_type' => 'string'),
        )
    );
}
// end of function

    if (isset($_POST['back_list'])) {
        // store options in DB
        set_route_options($route_id, $options);
        tpl_redirect('myroutes.php');
        exit;
    }

    if (isset($_POST['submit']) || isset($_POST['submit_map'])) {
        $database->paramQuery(
            'SELECT `user_id`,`name`, `description`, `radius` FROM `routes` WHERE `route_id`=:route_id',
            array ('route_id'=> array('value' => $route_id, 'data_type' => 'integer'))
        );
        $record = $database->dbResultFetch();
        $distance=$record['radius'];
        tpl_set_var('route_name',$record['name']);

        $caches_list=caches_along_route($route_id, $distance);

        // store options in DB
        set_route_options($route_id, $options);

        // get first point of route to calculate distance to cache and sort list by distance
        $lon = 0;
        $lat = 0;
        $database->paramQuery(
            'SELECT `route_points`.`lat`, `route_points`.`lon` FROM `route_points` WHERE `route_id`=:route_id ORDER BY `route_points`.`point_nr` LIMIT 1',
            array ('route_id'=> array('value' => $route_id, 'data_type' => 'integer'))
        );
        $record = $database->dbResultFetch();
        if ($record){
            $lon = $record['lon'];
            $lat = $record['lat'];
        }

        // yes, I know that this SQL is a little violation of bound parameters concept, but
        // first of all, rewrite $sqlFilter to use PDO is terrible job,
        // second, using IN operator with dynamic list is pain in the ass :( - unless we have better
        // database wrapper to handle that automatically
        $database->simpleQuery("SELECT (". getSqlDistanceFormula($lon, $lat, 0, 1) .") `distance`,
                            `caches`.`cache_id` `cacheid`,
                            `user`.`user_id` `userid`,
                            `caches`.`type` `type`,
                            `caches`.`name` `cachename`,
                            `caches`.`latitude` `latitude`,
                            `caches`.`longitude` `longitude`,
                            `caches`.`wp_oc` `wp_oc`,
                            `user`.`username` `username`,
                            `caches`.`date_created` `date_created`,
                            `caches`.`date_hidden` `date`,
                            `cache_type`.`icon_large` `icon_large`,
                            `caches`.`topratings` `topratings`
                    FROM `caches`,`user`, `cache_type`
                    WHERE `caches`.`wp_oc` IN('".implode("', '", $caches_list)."')
                    AND `caches`.`user_id`=`user`.`user_id`
                        AND `cache_type`.`id`=`caches`.`type`
                       AND `caches`.`cache_id` IN (" . $sqlFilter . ") ORDER BY distance");

        $ncaches=$database->rowCount();

        tpl_set_var('number_caches',$ncaches);
        if ($ncaches==0){
            tpl_set_var('list_empty_start','<!--');
            tpl_set_var('list_empty_end','-->');
            tpl_set_var('file_content','');
        }else{
            tpl_set_var('list_empty_start','');
            tpl_set_var('list_empty_end','');
        }
        $point="";

        $database_inner = new dataBase;
        while ($r = $database->dbResultFetch()){

            if (isset($_POST['submit_map'])){
                $y=$r['longitude'];
                $x=$r['latitude'];
                $point.=sprintf("addMarker(%s,%s,'%s',%s,'%s','%s','%s',%s);\n",
                        $x, $y, getSmallCacheIcon($r['icon_large']), $r[cacheid], addslashes($r[cachename]), $r[wp_oc], addslashes($r[username]), $r[topratings]);
                tpl_set_var('points', $point);
            } else {
                $file_content .= '<tr>';
                $file_content .= '<td style="width: 90px;">'. date($dateFormat, strtotime($r['date'])) . '</td>';
        //      $file_content .= '<td style="width: 22px;"><span style="font-weight:bold;color: blue;">'.sprintf("%01.1f",$r['distance']). '</span></td>';
                if ($r['topratings']!=0) {
                    $file_content .= '<td style="width: 22px;"><span style="font-weight:bold;color: green;">'.$r['topratings']. '</span></td>';
                }else{
                    $file_content .= '<td style="width: 22px;">&nbsp;&nbsp;</td>';
                }
                $file_content .= '<td width="22">&nbsp;<img src="tpl/stdstyle/images/' .getSmallCacheIcon($r['icon_large']) . '" border="0" alt=""/></td>';
                $file_content .= '<td><b><a class="links" href="viewcache.php?cacheid=' . htmlspecialchars($r['cacheid'], ENT_COMPAT, 'UTF-8') . '" target="_blank" >' . htmlspecialchars($r['cachename'], ENT_COMPAT, 'UTF-8') . '</a></b></td>';
                $file_content .= '<td width="32"><b><a class="links" href="viewprofile.php?userid='.htmlspecialchars($r['userid'], ENT_COMPAT, 'UTF-8') . '"  target="_blank">' .htmlspecialchars($r['username'], ENT_COMPAT, 'UTF-8'). '</a></b></td>';

                $database_inner->multiVariableQuery(
                        'SELECT cache_logs.id AS id, cache_logs.cache_id AS cache_id,
                            cache_logs.type AS log_type,
                            DATE_FORMAT(cache_logs.date,\'%Y-%m-%d\') AS log_date,
                            cache_logs.text AS log_text,
                            caches.user_id AS cache_owner,
                            cache_logs.encrypt encrypt,
                            cache_logs.user_id AS luser_id,
                            user.username AS user_name,
                            user.user_id AS user_id,
                            log_types.icon_small AS icon_small, COUNT(gk_item.id) AS geokret_in
                        FROM (cache_logs INNER JOIN caches ON (caches.cache_id = cache_logs.cache_id)) INNER JOIN user ON (cache_logs.user_id = user.user_id) INNER JOIN log_types ON (cache_logs.type = log_types.id)
                            LEFT JOIN   gk_item_waypoint ON gk_item_waypoint.wp = caches.wp_oc
                            LEFT JOIN   gk_item ON gk_item.id = gk_item_waypoint.id AND
                                gk_item.stateid<>1 AND gk_item.stateid<>4 AND gk_item.typeid<>2 AND gk_item.stateid !=5
                        WHERE cache_logs.deleted=0 AND cache_logs.cache_id=:1
                        GROUP BY cache_logs.id ORDER BY cache_logs.date_created DESC LIMIT 1',
                    $r['cacheid']);

                if ($database_inner->rowCount() != 0) {
                    $r_log = $database_inner->dbResultFetch();
                    $file_content .= '<td style="width: 80px;">'. htmlspecialchars(date($dateFormat, strtotime($r_log['log_date'])), ENT_COMPAT, 'UTF-8') . '</td>';

                    $file_content .= '<td width="22"><b><a class="links" href="viewlogs.php?logid=' . htmlspecialchars($r_log['id'], ENT_COMPAT, 'UTF-8') . '" onmouseover="Tip(\'';
                    $file_content .= '<b>'.$r_log['user_name'].'</b>:&nbsp;';
                    if ( $r_log['encrypt']==1 && $r_log['cache_owner']!=$usr['userid'] && $r_log['luser_id']!=$usr['userid']){
                    $file_content .= "<img src=\'/tpl/stdstyle/images/free_icons/lock.png\' alt=\`\` /><br/>";}
                    if ( $r_log['encrypt']==1 && ($r_log['cache_owner']==$usr['userid']|| $r_log['luser_id']==$usr['userid'])){
                    $file_content .= "<img src=\'/tpl/stdstyle/images/free_icons/lock_open.png\' alt=\`\` /><br/>";}
                    $data = cleanup_text(str_replace("\r\n", " ", $r_log['log_text']));
                    $data = str_replace("\n", " ",$data);
                    if ( $r_log['encrypt']==1 && $r_log['cache_owner']!=$usr['userid'] && $r_log['luser_id']!=$usr['userid'])
                    {//crypt the log ROT13, but keep HTML-Tags and Entities
                    $data = str_rot13_html($data);} else {$file_content .= "<br/>";}
                    $file_content .=$data;
                    $file_content .= '\',OFFSETY, 25, OFFSETX, -135, PADDING,5, WIDTH,280,SHADOW,true)" onmouseout="UnTip()" target="_blank"><img src="tpl/stdstyle/images/' . $r_log['icon_small'] . '" border="0" alt=""/></a></b></td>';
                    $file_content .= '<td>&nbsp;&nbsp;<b><a class="links" href="viewprofile.php?userid=' . htmlspecialchars($r_log['user_id'], ENT_COMPAT, 'UTF-8') . '" target="_blank">' . htmlspecialchars($r_log['user_name'], ENT_COMPAT, 'UTF-8') . '</a></b></td>';

                }

                $file_content .= "</tr>";
            }
        }
        unset($database_inner);

        tpl_set_var('file_content',$file_content);
        if (isset($_POST['submit_map'])) {
            $tplname = 'myroutes_result_map';
        } else {
            $tplname = 'myroutes_result';
        }
    } //end submit

    if (isset($_POST['submit_gpx_with_photos']))
    {
        // create cache list
        $caches_list=caches_along_route($route_id, $distance);
        $database->simpleQuery(
            'SELECT `caches`.`wp_oc`, `caches`.`cache_id`
                FROM `caches`
                WHERE `caches`.`wp_oc` IN(\''.implode('\', \'', $caches_list).'\')
                  AND `caches`.`cache_id` IN (' . $sqlFilter . ')'
        );
        $waypoints_tab = array();
        $cache_ids_tab = array();
        while ($r = $database->dbResultFetch())
        {
            $waypoints_tab[] = $r['wp_oc'];
            $cache_ids_tab[] = $r['cache_id'];
        }

        $okapi_max_caches = 50;
        $caches_count = count($waypoints_tab);
        // too much caches for one zip file - generate webpage instead
        if ($caches_count > $okapi_max_caches){
            $tplname = 'garminzip';

            tpl_set_var('zip_total_cache_count', $caches_count);
            tpl_set_var('zip_max_count', $okapi_max_caches);

            $options = array();
            $options['showresult'] = 1;
            $options['searchtype'] = 'bylist';
            $options['cache_ids'] = $cache_ids_tab;

            $options_text = serialize($options);

            $queryid = $database->paramQueryValue(
                'select `queries`.`id` from `queries` where `user_id` = 0 and `options` = :options',
                -1, // default value
                array ('options'=> array('value' => $options_text, 'data_type' => 'string'))
            );
            if ($queryid > 0){
                $database->multiVariableQuery(
                    'UPDATE `queries` SET `last_queried` = NOW() WHERE `id` = :1',
                    $queryid
                );
            } else {
                $database->paramQuery(
                    'INSERT INTO `queries` (`user_id`, `last_queried`, `uuid`, `options`) VALUES ( 0, NOW(), UUID(), :options)',
                    array ('options'=> array('value' => $options_text, 'data_type' => 'large'))
                );
                $queryid = $database->lastInsertId();
            }
            $links_content = '';
            $forlimit=intval($caches_count/$okapi_max_caches)+1;
            for($i=1;$i<=$forlimit;$i++)
            {
                $zipname='ocpl'.$queryid.'.zip?startat=0&count=max&zip=1&zippart='.$i.(isset($_REQUEST['okapidebug'])?'&okapidebug':'');
                $links_content .= '<li><a class="links" href="'.$zipname.'" title="Garmin ZIP file (part '.$i.')">ocpl'.$queryid.'-'.$i.'.zip</a></li>';
            }
            tpl_set_var('zip_links', $links_content);
            tpl_BuildTemplate();
        } else {
            require_once($rootpath.'okapi/facade.php');
            try
            {
                $waypoints = implode("|",$waypoints_tab);
                // TODO: why the langpref is fixed to pl? shouldn't it depend on current user/session language?
                $okapi_response =  \okapi\Facade::service_call('services/caches/formatters/garmin',
                    $usr['userid'],
                    array('cache_codes' => $waypoints, 'langpref' => 'pl',
                          'location_source'=> 'alt_wpt:user-coords', 'location_change_prefix' => '(F)'));

                // Modifying OKAPI's default HTTP Response headers.
                $okapi_response->content_type = 'application/zip';
                $okapi_response->content_disposition = 'attachment; filename=myroute.zip';

                // This outputs headers and the ZIP file.
                $okapi_response->display();
            }
            catch (\okapi\BadRequest $e)
            {
                # In case of bad requests, simply output OKAPI's error response.
                # In case of other, internal errors, don't catch the error. This
                # will cause OKAPI's default error hangler to kick in (so the admins
                # will get informed).

                header('Content-Type: text/plain');
                echo $e;
            }
        }
        exit;
    }


        if (isset($_POST['submit_gpx']))
        {
            $database->paramQuery(
                'SELECT `user_id`,`name`, `description`, `radius` FROM `routes` WHERE `route_id`=:route_id',
                array ('route_id'=> array('value' => $route_id, 'data_type' => 'integer'))
            );
            $record = $database->dbResultFetch();
            $distance=$record['radius'];
            tpl_set_var('route_name',$record['name']);
    $gpxHead =
'<?xml version="1.0" encoding="utf-8"?>
<gpx xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" version="1.0" creator="www.opencaching.pl" xsi:schemaLocation="http://www.topografix.com/GPX/1/0 http://www.topografix.com/GPX/1/0/gpx.xsd http://www.groundspeak.com/cache/1/0/1 http://www.groundspeak.com/cache/1/0/1/cache.xsd http://www.gsak.net/xmlv1/5 http://www.gsak.net/xmlv1/5/gsak.xsd" xmlns="http://www.topografix.com/GPX/1/0">
    <name>Cache Listing Generated from Opencaching.pl</name>
    <desc>Cache Listing Generated from Opencaching.pl {wpchildren}</desc>
    <author>OpenCaching.PL</author>
    <email>ocpl@opencaching.pl</email>
    <url>http://www.opencaching.pl</url>
    <urlname>Opencaching.pl - Geocaching w Polsce</urlname>
    <time>{{time}}</time>
    <keywords>cache, geocache</keywords>
';

$gpxLine = '
    <wpt lat="{lat}" lon="{lon}">
        <time>{{time}}</time>
        <name>{{waypoint}}</name>
        <desc>{cachename} by {owner}, {type_text} ({difficulty}/{terrain})</desc>
        <url>http://www.opencaching.pl/viewcache.php?cacheid={cacheid}</url>
        <urlname>{cachename} by {owner}, {type_text}</urlname>
        <sym>Geocache</sym>
        <type>Geocache|{type}</type>
        <groundspeak:cache id="{cacheid}" available="{available}" archived="{{archived}}" xmlns:groundspeak="http://www.groundspeak.com/cache/1/0/1">
            <groundspeak:name>{cachename}</groundspeak:name>
            <groundspeak:placed_by>{owner}</groundspeak:placed_by>
            <groundspeak:owner id="{owner_id}">{owner}</groundspeak:owner>
            <groundspeak:type>{type}</groundspeak:type>
            <groundspeak:container>{container}</groundspeak:container>
            <groundspeak:difficulty>{difficulty}</groundspeak:difficulty>
            <groundspeak:terrain>{terrain}</groundspeak:terrain>
            <groundspeak:country>Polska</groundspeak:country>
            <groundspeak:state>{region}</groundspeak:state>
            <groundspeak:short_description html="False">{shortdesc}</groundspeak:short_description>
            <groundspeak:long_description html="True">{desc}{rr_comment}&lt;br&gt;{{images}}</groundspeak:long_description>
            <groundspeak:encoded_hints>{hints}</groundspeak:encoded_hints>
            <groundspeak:logs>
            {logs}
            </groundspeak:logs>
            <groundspeak:travelbugs>
            {geokrety}
            </groundspeak:travelbugs>
        </groundspeak:cache>
    </wpt>
    {cache_waypoints}
';


$gpxLog = '
                <groundspeak:log id="{id}">
                    <groundspeak:date>{date}</groundspeak:date>
                    <groundspeak:type>{type}</groundspeak:type>
                    <groundspeak:finder id="{finder_id}">{username}</groundspeak:finder>
                    <groundspeak:text encoded="False">{{text}}</groundspeak:text>
                </groundspeak:log>
';

$gpxGeoKrety = '<groundspeak:travelbug id="{geokret_id}" ref="{geokret_ref}">
        <groundspeak:name>{geokret_name}</groundspeak:name>
        </groundspeak:travelbug>
        ';

$gpxWaypoints = '<wpt lat="{wp_lat}" lon="{wp_lon}">
    <time>{{time}}</time>
    <name>{waypoint} {wp_stage}</name>
    <cmt>{desc}</cmt>
    <desc>{wp_type_name}</desc>
    <url>http://opencaching.pl/viewcache.php?cacheid={cacheid}</url>
    <urlname>{waypoint} {wp_stage}</urlname>
    <sym>{wp_type}</sym>
    <type>Waypoint|{wp_type}</type>
    <gsak:wptExtension xmlns:gsak="http://www.gsak.net/xmlv1/5">
    <gsak:Parent>{waypoint}</gsak:Parent>
    <gsak:Code>{waypoint} {wp_stage}</gsak:Code>
    <gsak:Child_Flag>false</gsak:Child_Flag>
    <gsak:Child_ByGSAK>false</gsak:Child_ByGSAK>
    </gsak:wptExtension>
  </wpt>
';

    $gpxFoot = '</gpx>';

    $gpxTimeFormat = 'Y-m-d\TH:i:s\Z';

    $gpxAvailable[0] = 'False'; //OC: Unavailable
    $gpxAvailable[1] = 'True';  //OC: Available
    $gpxAvailable[2] = 'False'; //OC: Unavailable
    $gpxAvailable[3] = 'False'; //OC: Archived

    $gpxArchived[0] = 'False';  //OC: Unavailable
    $gpxArchived[1] = 'False';  //OC: Available
    $gpxArchived[2] = 'False';  //OC: Unavailable
    $gpxArchived[3] = 'True';   //OC: Archived

    $gpxContainer[0] = 'Unknown';   //OC: Other
    $gpxContainer[2] = 'Micro';     //OC: Micro
    $gpxContainer[3] = 'Small';     //OC: Small
    $gpxContainer[4] = 'Regular';   //OC: Regular
    $gpxContainer[5] = 'Large';     //OC: Large
    $gpxContainer[6] = 'Large';     //OC: Large
    $gpxContainer[7] = 'Virtual';   //OC: Virtual

    // known by gpx
    $gpxType[1] = 'Unknown Cache';      //OC: Other;
    $gpxType[2] = 'Traditional Cache';  //OC: Traditional
    $gpxType[3] = 'Multi-cache';        //OC: Multi
    $gpxType[4] = 'Virtual Cache';      //OC: Virtual
    $gpxType[5] = 'Webcam Cache';       //OC: Webcam
    $gpxType[6] = 'Event Cache';        //OC: Event

    $gpxType[7] = 'Unknown Cache';      //OC: Quiz
    $gpxType[8] = 'Unknown Cache';      //OC: Moving
    $gpxType[9] = 'Unknown Cache';      //OC: PodCache
    $gpxType[10] = 'Unknown Cache';     //OC: Educache
    $gpxType[11] = 'Unknown Cache';     //OC: Challenge cache
    // other
    //$gpxType[] = 'Unknown Cache';
    //$gpxType[] = 'Earthcache';
    //$gpxType[] = 'Cache In Trash Out Event';
    //$gpxType[] = 'Letterbox Hybrid';
    //$gpxType[] = 'Locationless (Reverse) Cache';

    // nazwy skrzynek do description
    $gpxGeocacheTypeText[1] = 'Unknown Cache';
    $gpxGeocacheTypeText[2] = 'Traditional Cache';
    $gpxGeocacheTypeText[3] = 'Multi-Cache';
    $gpxGeocacheTypeText[4] = 'Virtual Cache';
    $gpxGeocacheTypeText[5] = 'Webcam Cache';
    $gpxGeocacheTypeText[6] = 'Event Cache';
    $gpxGeocacheTypeText[7] = 'Quiz';
    $gpxGeocacheTypeText[8] = 'Moving Cache';
    $gpxGeocacheTypeText[9] = 'Podcast cache';


    $gpxLogType[0] = 'Write note';          //OC: Other
    $gpxLogType[1] = 'Found it';            //OC: Found
    $gpxLogType[2] = 'Didn\'t find it';     //OC: Not Found
    $gpxLogType[3] = 'Write note';          //OC: Note
    $gpxLogType[4] = 'Write note';          //OC: Note
    $gpxLogType[5] = 'Needs Maintenance';           //OC: Note
    $gpxLogType[6] = 'Needs Archived';          //OC: Other
    $gpxLogType[7] = 'Attended';            //OC: Found
    $gpxLogType[8] = 'Will Attend';     //OC: Not Found
    $gpxLogType[9] = 'Archive';             //OC: Note
    $gpxLogType[10] = 'Enable Listing';             //OC: Note
    $gpxLogType[11] = 'Temporarily Disable Listing';            //OC: Note
    $gpxLogType[12] = 'Post Reviewer Note';             //OC: Note

// create cache list
$caches_list=caches_along_route($route_id, $distance);

 $sql=("SELECT
    `caches`.`cache_id` `cache_id`,
    `caches`.`wp_oc` `cache_wp`,
    `caches`.`status` `status`,
    `caches`.`type` `type`,
    `caches`.`size` `size`,
    `caches`.`longitude` `longitude`,
    `caches`.`latitude` `latitude`,
    `caches`.`user_id` `user_id` ,
    `caches`.`votes` `votes`,
    `caches`.`score` `score`,
    `caches`.`topratings` `topratings`
            FROM `caches`
            WHERE `caches`.`wp_oc` IN('".implode("', '", $caches_list)."') AND `caches`.`cache_id` IN (" . $sqlFilter . ")");

        // cleanup (old gpxcontent lingers if gpx-download is cancelled by user)
        // BSz: does TEMPORARY TABLES work with PDO? In dataBase class, we are instantinating new PDO
        // object (== new connection) for every query
        sql('DROP TEMPORARY TABLE IF EXISTS `gpxcontent`');

        // temporäre tabelle erstellen
        sql('CREATE TEMPORARY TABLE `gpxcontent` ' . $sql);

        $rsCount = sql('SELECT COUNT(*) `count` FROM `gpxcontent`');
        $rCount = sql_fetch_array($rsCount);
        mysql_free_result($rsCount);

            $sFilebasename = "myroute";


        $bUseZip = ($rCount['count'] > 50);
        $bUseZip = $bUseZip || (isset($_REQUEST['zip']) && ($_REQUEST['zip'] == '1'));
    //  $bUseZip = true;
        if ($bUseZip == true)
        {
            $content = '';
            require_once($rootpath . 'lib/phpzip/ss_zip.class.php');
            $phpzip = new ss_zip('',6);
        }

        // ok, ausgabe starten

        if ($sqldebug == false)
        {
            if ($bUseZip == true)
            {
                header("content-type: application/zip");
                header('Content-Disposition: attachment; filename=' . $sFilebasename . '.zip');
            }
            else
            {
                header("Content-type: application/gpx");
                header("Content-Disposition: attachment; filename=" . $sFilebasename . ".gpx");
            }
        }
        $children='';
        $gpxHead = str_replace('{{time}}', date($gpxTimeFormat, time()), $gpxHead);
        $rss = sql('SELECT `gpxcontent`.`cache_id` `cacheid` FROM `gpxcontent`');
        while($rs = sql_fetch_array($rss))
        {
        $rwp = sql("SELECT  `status` FROM `waypoints` WHERE  `waypoints`.`cache_id`=&1 AND `waypoints`.`status`='1'", $rs['cacheid']);
        if (mysql_num_rows($rwp) != 0) {$children="(HasChildren)";}
        }
        $gpxHead = str_replace('{wpchildren}', $children, $gpxHead);
        append_output($gpxHead);

        // ok, ausgabe ...
        $rs = sql('SELECT `gpxcontent`.`cache_id` `cacheid`, `gpxcontent`.`longitude` `longitude`, `gpxcontent`.`latitude` `latitude`, `caches`.`wp_oc` `waypoint`, `caches`.`date_hidden` `date_hidden`, `caches`.`picturescount` `picturescount`, `caches`.`name` `name`, `caches`.`country` `country`, `caches`.`terrain` `terrain`, `caches`.`difficulty` `difficulty`, `caches`.`desc_languages` `desc_languages`, `caches`.`size` `size`, `caches`.`type` `type`, `caches`.`status` `status`, `user`.`username` `username`, `gpxcontent`.`user_id` `owner_id`, `cache_desc`.`desc` `desc`, `cache_desc`.`short_desc` `short_desc`, `cache_desc`.`hint` `hint`, `cache_desc`.`rr_comment`, `caches`.`logpw`,`caches`.`votes` `votes`,`caches`.`score` `score`, `caches`.`topratings` `topratings` FROM `gpxcontent`, `caches`, `user`, `cache_desc` WHERE `gpxcontent`.`cache_id`=`caches`.`cache_id` AND `caches`.`cache_id`=`cache_desc`.`cache_id` AND `caches`.`default_desclang`=`cache_desc`.`language` AND `gpxcontent`.`user_id`=`user`.`user_id`');
        while($r = sql_fetch_array($rs))
        {
            $thisline = $gpxLine;
            $lat = sprintf('%01.5f', $r['latitude']);
            $thisline = str_replace('{lat}', $lat, $thisline);

            $lon = sprintf('%01.5f', $r['longitude']);
            $thisline = str_replace('{lon}', $lon, $thisline);

            $time = date($gpxTimeFormat, strtotime($r['date_hidden']));
            $thisline = str_replace('{{time}}', $time, $thisline);
            $thisline = str_replace('{{waypoint}}', $r['waypoint'], $thisline);
            $thisline = str_replace('{cacheid}', $r['cacheid'], $thisline);
            $thisline = str_replace('{cachename}', cleanup_text($r['name']), $thisline);
//          $thisline = str_replace('{country}', $r['country'], $thisline);
            $region = sqlValue("SELECT `adm3` FROM `cache_location` WHERE `cache_id`='" . sql_escape($r['cacheid']) . "'", 0);
            $thisline = str_replace('{region}', $region, $thisline);

            if ($r['hint'] == '')
                $thisline = str_replace('{hints}', '', $thisline);
            else
                $thisline = str_replace('{hints}', cleanup_text($r['hint']), $thisline);

            $logpw = ($r['logpw']==""?"":"UWAGA! W skrzynce znajduje się hasło - pamiętaj o jego zapisaniu!<br />");

            $thisline = str_replace('{shortdesc}', cleanup_text($r['short_desc']), $thisline);
            $thisline = str_replace('{desc}', cleanup_text($logpw.$r['desc']), $thisline);
            if( $r['rr_comment'] == '' )
                $thisline = str_replace('{rr_comment}', '', $thisline);
            else
                $thisline = str_replace('{rr_comment}', cleanup_text("<br /><br />--------<br />".$r['rr_comment']."<br />"), $thisline);

            $thisline = str_replace('{{images}}', getPictures($r['cacheid'], false, $r['picturescount']), $thisline);

            if (isset($gpxType[$r['type']]))
                $thisline = str_replace('{type}', $gpxType[$r['type']], $thisline);
            else
                $thisline = str_replace('{type}', $gpxType[1], $thisline);

            if (isset($gpxGeocacheTypeText[$r['type']]))
                $thisline = str_replace('{type_text}', $gpxGeocacheTypeText[$r['type']], $thisline);
            else
                $thisline = str_replace('{type_text}', $gpxGeocacheTypeText[1], $thisline);

            if (isset($gpxContainer[$r['size']]))
                $thisline = str_replace('{container}', $gpxContainer[$r['size']], $thisline);
            else
                $thisline = str_replace('{container}', $gpxContainer[0], $thisline);

            if (isset($gpxAvailable[$r['status']]))
                $thisline = str_replace('{available}', $gpxAvailable[$r['status']], $thisline);
            else
                $thisline = str_replace('{available}', $gpxAvailable[1], $thisline);

            if (isset($gpxArchived[$r['status']]))
                $thisline = str_replace('{{archived}}', $gpxArchived[$r['status']], $thisline);
            else
                $thisline = str_replace('{{archived}}', $gpxArchived[1], $thisline);

            $difficulty = sprintf('%01.1f', $r['difficulty'] / 2);
            $difficulty = str_replace('.0', '', $difficulty); // garmin devices cannot handle .0 on integer values
            $thisline = str_replace('{difficulty}', $difficulty, $thisline);

            $terrain = sprintf('%01.1f', $r['terrain'] / 2);
            $terrain = str_replace('.0', '', $terrain);
            $thisline = str_replace('{terrain}', $terrain, $thisline);

            $thisline = str_replace('{owner}', xmlentities($r['username']), $thisline);
            $thisline = str_replace('{owner_id}', xmlentities($r['owner_id']), $thisline);

            $rsAttributes = sql("SELECT `caches_attributes`.`attrib_id`, `cache_attrib`.`text_long` FROM `caches_attributes`, `cache_attrib` WHERE `caches_attributes`.`cache_id`=&1 AND `caches_attributes`.`attrib_id` = `cache_attrib`.`id` AND `cache_attrib`.`language` = 'PL' ORDER BY `caches_attributes`.`attrib_id`", $r['cacheid']);

            // logs ermitteln
            $logentries = '';

    if (( $r['votes'] > 3 ) ||  ( $r['topratings'] > 0 ) || (mysql_num_rows($rsAttributes) > 0 )) {
//              $thislog = $gpxLog;
//              <groundspeak:log id="1">
//                  <groundspeak:date>{date}</groundspeak:date>
//                  <groundspeak:type>{type}</groundspeak:type>
//                  <groundspeak:finder id="{finder_id}">{username}</groundspeak:finder>
//                  <groundspeak:text encoded="False">{{text}}</groundspeak:text>
//              </groundspeak:log>

//              $thislog = str_replace('{id}', "0", $thislog);
//              $thislog = str_replace('{date}', date("Y-m-d") ."T00:00:00", $thislog);
//              $thislog = str_replace('{username}', "SYSTEM", $thislog);
//              $thislog = str_replace('{finder_id}', "0", $thislog);
//              $thislog = str_replace('{type}', "Write note", $thislog);
            // Attributes

                $thislogs ='<groundspeak:log id="1">';
                $thislogs .='<groundspeak:date>' .date("Y-m-d\TH:i:s\Z").'</groundspeak:date>';
                $thislogs .='<groundspeak:finder id="0">SYSTEM</groundspeak:finder>';
                $thislogs .='<groundspeak:text encoded="False">';
                if (mysql_num_rows($rsAttributes) > 0) {
                $attributes = 'Atrybuty: ';
            while ($rAttribute = sql_fetch_array($rsAttributes))
            {
                    $attributes .= cleanup_text(xmlentities($rAttribute['text_long']));
                    $attributes .=  " | ";
            }
            $thislogs .= $attributes;
             }

            if( $r['votes'] > 3 ){

                $score = cleanup_text(score2rating($r['score']));
                $thislogs .= "\nOcena skrzynki: " .$score. "\n";
            }
            if( $r['topratings'] > 0 ){
            $thislogs .= "Rekomendacje: " .$r['topratings']. "\n";}
                $rsArea = sql("SELECT `npa_areas`.`id` AS `npaId`, `npa_areas`.`sitename` AS `npaSitename`, `npa_areas`.`sitecode` AS `npaSitecode`, `npa_areas`.`sitetype` AS `npaSitetype`
                 FROM `cache_npa_areas`
           INNER JOIN `npa_areas` ON `cache_npa_areas`.`npa_id`=`npa_areas`.`id`
                WHERE `cache_npa_areas`.`cache_id`='&1'",$r['cacheid']);
                if (mysql_num_rows($rsArea) != 0)
                { $thislogs .= "NATURA 2000: ";
                while( $npa = mysql_fetch_array($rsArea) )
                    {
             $thislogs .= $npa['npaSitename']." - ".$npa['npaSitecode']. ",";}
             }


                $thislogs .= '</groundspeak:text></groundspeak:log>';

                $logentries .= $thislogs . "\n";
        }
            // set number of logs output


            if ($cache_logs!=0 && $logs!=0){$limit=" LIMIT ".$logs;}
            if ($cache_logs==0) {$limit="";}
            if ($logs!=0 || $cache_logs==0) {
            $rsLogs = sql("SELECT `cache_logs`.`id`, `cache_logs`.`type`, `cache_logs`.`date`, `cache_logs`.`text`, `user`.`username`, `cache_logs`.`user_id` `userid` FROM `cache_logs`, `user` WHERE `cache_logs`.`deleted`=0 AND `cache_logs`.`user_id`=`user`.`user_id` AND `cache_logs`.`cache_id`=&1 ORDER BY `cache_logs`.`date` DESC, `cache_logs`.`id` DESC $limit", $r['cacheid']);
            while ($rLog = sql_fetch_array($rsLogs))
            {
                $thislog = $gpxLog;

                $thislog = str_replace('{id}', $rLog['id'], $thislog);
                $thislog = str_replace('{date}', date($gpxTimeFormat, strtotime($rLog['date'])), $thislog);
                $thislog = str_replace('{username}', xmlentities($rLog['username']), $thislog);
                $thislog = str_replace('{finder_id}', xmlentities($rLog['userid']), $thislog);
                if (isset($gpxLogType[$rLog['type']]))
                    $logtype = $gpxLogType[$rLog['type']];
                else
                    $logtype = $gpxLogType[0];

                $thislog = str_replace('{type}', $logtype, $thislog);
                $thislog = str_replace('{{text}}', cleanup_text($rLog['text']), $thislog);
                $logentries .= $thislog . "\n";
            }
            }
            $thisline = str_replace('{logs}', $logentries, $thisline);


            // Travel Bug GeoKrety
            $waypoint = $r['waypoint'];
            $geokrety = '';
            $geokret_sql = "SELECT id, name FROM gk_item WHERE id IN (SELECT id FROM gk_item_waypoint WHERE wp = '".sql_escape($waypoint)."') AND stateid<>1 AND stateid<>4 AND stateid <>5 AND typeid<>2";
            $geokret_query = sql($geokret_sql);

                while( $geokret = sql_fetch_array($geokret_query) )
                {

                $thisGeoKret = $gpxGeoKrety;
                $gk_wp = strtoupper(dechex($geokret['id']));
                while (mb_strlen($gk_wp) < 4) $gk_wp = '0' . $gk_wp;
                $gkWP = 'GK' . mb_strtoupper($gk_wp);
                    $thisGeoKret = str_replace('{geokret_id}',xmlentities($geokret['id']) , $thisGeoKret);
                    $thisGeoKret = str_replace('{geokret_ref}',$gkWP, $thisGeoKret);
                    $thisGeoKret = str_replace('{geokret_name}', cleanup_text(xmlentities($geokret['name'])), $thisGeoKret);

                    $geokrety .= $thisGeoKret;// . "\n";

            }
            $thisline = str_replace('{geokrety}', $geokrety, $thisline);
// Waypoints
            $waypoints = '';
            $rswp = sql("SELECT  `longitude`, `cache_id`, `latitude`,`desc`,`stage`, `type`, `status`,`waypoint_type`.`pl` `wp_type_name` FROM `waypoints` INNER JOIN waypoint_type ON (waypoints.type = waypoint_type.id) WHERE  `waypoints`.`cache_id`=&1 ORDER BY `waypoints`.`stage`", $r['cacheid']);
            while ($rwp = sql_fetch_array($rswp))
            {
            if ($rwp['status']==1) {
                $thiswp = $gpxWaypoints;
                $lat = sprintf('%01.5f', $rwp['latitude']);
                $thiswp = str_replace('{wp_lat}', $lat, $thiswp);
                $lon = sprintf('%01.5f', $rwp['longitude']);
                $thiswp = str_replace('{wp_lon}', $lon, $thiswp);
                $thiswp = str_replace('{waypoint}', $waypoint,$thiswp);
                $thiswp = str_replace('{cacheid}', $rwp['cache_id'],$thiswp);
                $thiswp = str_replace('{{time}}', $time, $thiswp);
                $thiswp = str_replace('{wp_type_name}', $rwp['wp_type_name'], $thiswp);
                if ($rwp['stage'] !=0) {
                $thiswp = str_replace('{wp_stage}', " Etap" .$rwp['stage'], $thiswp);
                } else {
                $thiswp = str_replace('{wp_stage}',$rwp['wp_type_name'] , $thiswp);}
                $thiswp = str_replace('{desc}', cleanup_text($rwp['desc']), $thiswp);
                if ($rwp['type']==5){$thiswp = str_replace('{wp_type}', "Parking Area", $thiswp);}
                if ($rwp['type']==1){$thiswp = str_replace('{wp_type}', "Flag, Green", $thiswp);}
                if ($rwp['type']==2){$thiswp = str_replace('{wp_type}', "Flag, Green", $thiswp);}
                if ($rwp['type']==3){$thiswp = str_replace('{wp_type}', "Flag, Red", $thiswp);}
                if ($rwp['type']==4){$thiswp = str_replace('{wp_type}', "Circle with X", $thiswp);}
                $waypoints .= $thiswp;
                }
            }
            $thisline = str_replace('{cache_waypoints}', $waypoints, $thisline);




            append_output($thisline);
            ob_flush();
        }
        mysql_free_result($rs);

        append_output($gpxFoot);

        if ($sqldebug == true) sqldbg_end();

        // phpzip versenden
        if ($bUseZip == true)
        {
            $phpzip->add_data($sFilebasename . '.gpx', $content);
            echo $phpzip->save($sFilebasename . '.zip', 'b');
        }

    exit;
        } //end GPX output





        }
    }

    //make the template and send it out
    tpl_BuildTemplate();
?>
