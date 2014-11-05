<?php

if ((!isset($GLOBALS['no-session'])) || ($GLOBALS['no-session'] == false))
    session_start();

/***************************************************************************
    *
    *   This program is free software; you can redistribute it and/or modify
    *   it under the terms of the GNU General Public License as published by
    *   the Free Software Foundation; either version 2 of the License, or
    *   (at your option) any later version.
    *
    ***************************************************************************/

/****************************************************************************

        Unicode Reminder ??

    sets up all neccessary variables and handle template and database-things
    also useful functions

    parameter: lang       get/post/cookie   used language
               style      get/post/cookie   used style

 ****************************************************************************/

/**
 *  load opencaching library for connect with database.
 *  library is based on PDO library and should be used with database connection.
 *  see inside this file for instructions how to use it.
 */

if ((!isset($GLOBALS['no-ob'])) || ($GLOBALS['no-ob'] == false))
    ob_start();
if ((!isset($GLOBALS['oc_waypoint'])) && isset($GLOBALS['ocWP']))
   $GLOBALS['oc_waypoint'] = $GLOBALS['ocWP'];

    // we are in HTML-mode ... maybe plain (for CLI scripts)
    global $interface_output;
    global $menu;
    $interface_output = 'html';

    //JG - niezainicjowana zmienna, 2013.10.18
    if (!isset($rootpath)) $rootpath = './';
    require_once($rootpath . 'lib/language.inc.php');

    $lang_array = available_languages();//array("pl", "en", "sv", "de", "cs", "fr", "es");
    $datetimeformat = '%d %B %Y o godz. %H:%M:%S ';
    $dateformat = '%d %B %Y';
    $simpledateformat = '%d.%m.%Y';

    $STATUS =     array( "READY" => 1,
                         "TEMP_UNAVAILABLE" => 2,
                         "ARCHIVED" => 3,
                         "HIDDEN_FOR_APPROVAL" => 4,
                         "NOT_YET_AVAILABLE" => 5,
                         "BLOCKED" => 6
                        );

    $CACHESIZE =  array( "MICRO" => 2,
                         "SMALL" => 3,
                         "NORMAL" => 4,
                         "LARGE" => 5,
                         "VERY_LARGE" => 6,
                         "NO_CONTAINER" => 7
                       );

    // set default CSS
    tpl_set_var('css', 'main.css');

    //detecting errors
    $error = false;

    //no slashes in variables! originally from phpBB2 copied
    // starypatyk 2011.08.20 - zablokowane wywolanie set_magic_quotes_runtime
    // powoduje ostrzezenia E_DEPRECATED - po co byla ta funkcja???
    // set_magic_quotes_runtime(0); // Disable magic_quotes_runtime

    if (get_magic_quotes_gpc())
    {
        if (is_array($_GET))
        {
            while (list($k, $v) = each($_GET))
            {
                if (is_array($_GET[$k]))
                {
                    while (list($k2, $v2) = each($_GET[$k]))
                    {
                        $_GET[$k][$k2] = stripslashes($v2);
                    }
                    @reset($_GET[$k]);
                }
                else
                {
                    $_GET[$k] = stripslashes($v);
                }
            }
            @reset($_GET);
        }

        if (is_array($_POST))
        {
            while (list($k, $v) = each($_POST))
            {
                if (is_array($_POST[$k]))
                {
                    while (list($k2, $v2) = each($_POST[$k]))
                    {
                        $_POST[$k][$k2] = stripslashes($v2);
                    }
                    @reset($_POST[$k]);
                }
                else
                {
                    $_POST[$k] = stripslashes($v);
                }
            }
            @reset($_POST);
        }

        if (is_array($HTTP_COOKIE_VARS))
        {
            while (list($k, $v) = each($HTTP_COOKIE_VARS))
            {
                if (is_array($HTTP_COOKIE_VARS[$k]))
                {
                    while (list($k2, $v2) = each($HTTP_COOKIE_VARS[$k]))
                    {
                        $HTTP_COOKIE_VARS[$k][$k2] = stripslashes($v2);
                    }
                    @reset($HTTP_COOKIE_VARS[$k]);
                }
                else
                {
                    $HTTP_COOKIE_VARS[$k] = stripslashes($v);
                }
            }
            @reset($HTTP_COOKIE_VARS);
        }
    }

    if (!isset($rootpath)) $rootpath = './';

    require_once($rootpath . 'lib/clicompatbase.inc.php');

    // load HTML specific includes
    require_once($rootpath . 'lib/cookie.class.php');

    //site in service?
    if ($site_in_service == false)
    {
        header('Content-type: text/html; charset=utf-8');
        $page_content = read_file($rootpath . 'html/outofservice.tpl.php');
        die($page_content);
    }

    //by default, use start template
    if (!isset($tplname)) $tplname = 'start';

    //restore cookievars[]
    load_cookie_settings();

    require_once($rootpath . 'lib/loadlanguage.php');

    require_once($rootpath . 'lib/xml2ary.inc.php');
    // set footer tpl varset

    $ok = false;
    foreach($lang_array as $lang_element)
    {
        if( $lang_element == $lang )
        {
            $ok = true;
            break;
        }
    }
    if( !$ok )
        die('Critical Error: The specified language does not exist!');

    //style changed?
    if (isset($_POST['style']))
    {
        $style = $_POST['style'];
    }
    if (isset($_GET['style']))
    {
        $style = $_GET['style'];
    }

    //does the style exist?
    if (!file_exists($rootpath . 'tpl/' . $style . '/'))
    {
        die('Critical Error: The specified style does not exist!');
    }

    //set up the style path
    if (!isset($stylepath)) $stylepath = $rootpath. 'tpl/' . $style;

    //set up the language path
    if (!isset($langpath)) $langpath = $stylepath . '';

    //load language specific strings
    require_once($langpath . '/expressions.inc.php');

    //set up the defaults for the main template
    require_once($stylepath . '/varset.inc.php');

    // thumbs-dir/url
    if (!isset($thumbdir)) $thumbdir = $picdir . '/thumbs';
    if (!isset($thumburl)) $thumburl = $picurl . '/thumbs';




    //open a databse connection
    db_connect();

    if ($dblink === false)
    {
        //error while connecting to the database
        $error = true;

        //set up error report
        tpl_set_var('error_msg', htmlspecialchars(mysql_error(), ENT_COMPAT, 'UTF-8'));
        tpl_set_var('tplname', $tplname);
        $tplname = 'error';
    }
    else
    {
        // include the authentication functions
        require($rootpath . 'lib/auth.inc.php');

        //user authenification from cookie
        auth_user();
        if ($usr == false)
        {
            //no user logged in
            if (isset($_POST['target']))
            {
                $target = $_POST['target'];
            }
            elseif (isset($_REQUEST['target']))
            {
                $target = $_REQUEST['target'];
            }
            elseif (isset($_GET['target']))
            {
                $target = $_GET['target'];
            }
            else
            {
                $target = '{target}';
            }
            $sLoggedOut = mb_ereg_replace('{target}', $target, $sLoggedOut);
            tpl_set_var('loginbox', $sLoggedOut);
        }
        else
        {

            // check for user_id in session
            if( !isset($_SESSION['user_id']) )
            {
                $_SESSION['user_id'] = $usr['userid'];
            }
            //user logged in
            // check for rules confirmation
            if( (strtotime("2008-11-01 00:00:00") <= strtotime(date("Y-m-d h:i:s"))) )
            {
                $sql = "SELECT `rules_confirmed` FROM `user` WHERE `user_id` = '".sql_escape(intval($usr['userid']))."'";
                $rules_confirmed = mysql_result(mysql_query($sql),0);
                if( $rules_confirmed == 0 )
                {
                    if( !isset($_SESSION['called_from_confirm']) )
                        header("Location: confirm.php");
                    else
                        unset($_SESSION['called_from_confirm']);
                }
            }


            $sTmpString = mb_ereg_replace('{username}', $usr['username'], $sLoggedIn);
            tpl_set_var('loginbox', $sTmpString);
            unset($sTmpString);

            // check XY home if OK redirect to myn
            //$latitude =sqlValue("SELECT `latitude` FROM user WHERE user_id='" . sql_escape($usr['userid']) . "'", 0);
            //$longitude =sqlValue("SELECT `longitude` FROM user WHERE user_id='" . sql_escape($usr['userid']) . "'", 0);

            //if (($longitude!=NULL && $latitude!=NULL) ||($longitude!=0 && $latitude!=0) ) {
            //header('Location: myneighborhood.php');}


        }
    }

    // zeitmessung
    require_once($rootpath . 'lib/bench.inc.php');
    $bScriptExecution = new Cbench;
    $bScriptExecution->start();

    // rating conversion array
        $ratingDesc = array(
                                tr('rating_poor'),
                                tr('rating_mediocre'),
                                tr('rating_avarage'),
                                tr('rating_good'),
                                tr('rating_excellent'),
                            );
    // prima-aprilis joke ;-)
    if ((date('m') == 4) and (date('d') == 1)) {
        $ratingDesc = array(
                                tr('rating_poor_1A'),
                                tr('rating_mediocre_1A'),
                                tr('rating_avarage_1A'),
                                tr('rating_good_1A'),
                                tr('rating_excellent_1A'),
                            );
    }

    tpl_set_var('site_name', $site_name);
    tpl_set_var('wiki_url', $wiki_url);
    tpl_set_var('rules_url', $rules_url);
    tpl_set_var('cache_params_url', $cache_params_url);
    tpl_set_var('contact_mail', $contact_mail);
    tpl_set_var('rating_desc_url', $rating_desc_url);

    function score2ratingnum($score)
    {
        if($score >= 2.2)
            return 4;
        else if($score >= 1.4)
            return 3;
        else if($score >= 0.1)
            return 2;
        else if($score >= -1.0)
            return 1;
        else
            return 0;
    }

    function score2rating($score)
    {
        global $ratingDesc;
        return $ratingDesc[score2ratingnum($score)];
    }

    function new2oldscore($score)
    {
        if($score == 4)
            return 3.0;
        else if($score == 3)
            return 1.7;
        else if($score == 2)
            return 0.7;
        else if($score == 1)
            return 0.5;
        else
            return -2.0;
    }

    function season()
    {
        $season = date("z");

        if ($season <= 171 and $season >= 79)
            $m_season = "spring";
        else if ($season <= 264 and $season >= 172)
            $m_season = "summer";
        else if ($season <= 330 and $season >= 265)
            $m_season = "autumn";
        else
            $m_season = "winter";
        return $m_season;
    }
    function validate_style($style)
    {
        switch($style) {
            case "spring":
            case "summer":
            case "autumn":
            case "winter":
            case "christmas":
            case "easter":
            case "test":
                return $style;
        }
        return "";
    }
    $season = isset($_GET['season'])?validate_style($_GET['season']):season();
    tpl_set_var("season", $season);


    // Convert from -3..3 to 1..5: update scores set score = (score +3)*5/6+1


    // get the country name from a given shortage
    // on success return the name, otherwise false
    function db_CountryFromShort($countrycode)
    {
        global $dblink, $lang;

        //no databse connection?
        if ($dblink === false) return false;

        //select the right record
        if(checkField('cache_status',$lang) )
                $lang_db = $lang;
            else
                $lang_db = "en";

        $rs = sql("SELECT `short`, `&1` FROM `countries` WHERE `short`='&2'", $lang_db, $countrycode);
        if (mysql_num_rows($rs) > 0)
        {
            $record = sql_fetch_array($rs);

            //return the country
            return $record[$lang_db];
        }
        else
        {
            //country not found
            return false;
        }
    }

    // get the language from a given shortage
    // on success return the name, otherwise false
    function db_LanguageFromShort($langcode)
    {
        global $dblink, $lang;

        //no databse connection?
        if ($dblink === false) return false;

        //select the right record
        $rs = sql("SELECT `short`, `&1` FROM `languages` WHERE `short`='&2'", $lang, $langcode);
        if (mysql_num_rows($rs) > 0)
        {
            $record = sql_fetch_array($rs);

            //return the language
            return $record[$lang];
        }
        else
        {
            //language not found
            return false;
        }
    }

    //get the stored settings and authentification data from the cookie
    function load_cookie_settings()
    {
        global $cookie, $lang, $style;

        //speach
        if ($cookie->is_set('lang'))
        {
            $lang = $cookie->get('lang');
        }

        //style
        if ($cookie->is_set('style'))
        {
            $style = $cookie->get('style');
        }
    }

    //store the cookie vars
    function write_cookie_settings()
    {
        global $cookie, $lang, $style;

        //language
        $cookie->set('lang', $lang);

        //style
        $cookie->set('style', $style);

        //send cookie
        $cookie->header();
    }

    //returns the cookie value, otherwise false
    function get_cookie_setting($name)
    {
        global $cookie;

        if ($cookie->is_set($name))
        {
            return $cookie->get($name);
        }
        else
        {
            return false;
        }
    }

    //sets the cookie value
    function set_cookie_setting($name, $value)
    {
        global $cookie;
        $cookie->set($name, $value);
    }

    //set a template replacement
    //set no_eval true to prevent this contents from php-parsing.
    //Important when replacing something that the user has posted
    //in HTML code and could contain \<\? php-Code \?\>
    function tpl_set_var($name, $value, $no_eval = true)
    {
        global $vars, $no_eval_vars;
        $vars[$name] = $value;
        $no_eval_vars[$name] = $no_eval;
    }

    //get a template replacement, otherwise false
    function tpl_get_var($name)
    {
        global $vars;

        if (isset($vars[$name]))
        {
            return $vars[$name];
        }
        else
        {
            return false;
        }
    }

    //clear all template vars
    function tpl_clear_vars()
    {
        unset($GLOBALS['vars']);
        unset($GLOBALS['no_eval_vars']);
    }

    //page function replaces {functionsbox} in main template
    function tpl_set_page_function($id, $html_code)
    {
        global $page_functions;

        $page_functions[$id] = $html_code;
    }

    function tpl_unset_page_function($id)
    {
        global $page_functions;

        unset($page_functions[$id]);
    }

    function tpl_clear_page_functions()
    {
        unset($GLOBALS['page_functions']);
    }

    function writeLanguageFlags($languages)
    {
        global $lang;
        $language_flags = "";
        foreach($languages as $s_lang)
        {
            $_SERVER['QUERY_STRING'] = str_replace("&lang=".$s_lang,"",$_SERVER['QUERY_STRING']);
            $_SERVER['QUERY_STRING'] = str_replace("lang=".$s_lang,"",$_SERVER['QUERY_STRING']);
        }
        foreach($languages as $s_lang)
        {
            if( $s_lang != $lang)
            {
                $language_flags .= '<li><a rel="nofollow" style="text-decoration:none;" href="'.($_SERVER['PHP_SELF']);

                if(strlen($_SERVER['QUERY_STRING']) > 0)
                    $language_flags .= '?'.htmlspecialchars($_SERVER['QUERY_STRING']) . '&amp;lang='.$s_lang.'"><img class="img-navflag" border="0" src="images/'.$s_lang.'.jpg" alt="'.$s_lang.' version" title=""/>&nbsp;';
                else
                    $language_flags .= '?lang='.$s_lang.'"><img class="img-navflag" border="0" src="images/'.$s_lang.'.png" alt="'.$s_lang.' version" title=""/>&nbsp;';

                $language_flags .= '</a></li>';
            }
        }
        return $language_flags;
    }

    //read the templates and echo it to the user
    function tpl_BuildTemplate($dbdisconnect=true, $minitpl=false)
    {
        //template handling vars
        global $stylepath, $tplname, $vars, $langpath, $lang_array, $lang, $language;
        //language specific expression
        global $error_pagenotexist;
        //only for debbuging
        global $b, $bScriptExecution;

        $bScriptExecution->Stop();
        tpl_set_var('scripttime', sprintf('%1.3f', $bScriptExecution->Diff()));
        tpl_set_var('language_flags',writeLanguageFlags($lang_array));

        $bTemplateBuild = new Cbench;
        $bTemplateBuild->Start();

        //set {functionsbox}
        global $page_functions, $functionsbox_start_tag, $functionsbox_middle_tag, $functionsbox_end_tag;

        if (isset($page_functions))
        {
            $functionsbox = $functionsbox_start_tag;
            foreach ($page_functions AS $func)
            {
                if ($functionsbox != $functionsbox_start_tag)
                {
                    $functionsbox .= $functionsbox_middle_tag;
                }
                $functionsbox .= $func;
            }
            $functionsbox .= $functionsbox_end_tag;

            tpl_set_var('functionsbox', $functionsbox);
        }
        //include language specific expressions, so that they are available in the template code
        include $langpath . '/expressions.inc.php';

        //load main template
        if($minitpl)
            $sCode = read_file($stylepath . '/mini.tpl.php');
        else if (isset($_REQUEST['print']) && $_REQUEST['print'] == 'y')
            $sCode = read_file($stylepath . '/main_print.tpl.php');
        else if (isset($_REQUEST['popup']) && $_REQUEST['popup'] == 'y')
            $sCode = read_file($stylepath . '/popup.tpl.php');
        else
            $sCode = read_file($stylepath . '/main.tpl.php');
        $sCode = '?>' . $sCode . '<?';

        //does template exist?
        if (!file_exists($stylepath . '/' . $tplname . '.tpl.php'))
        {
            //set up the error template
            $error = true;
            tpl_set_var('error_msg', htmlspecialchars($error_pagenotexist, ENT_COMPAT, 'UTF-8'));
            tpl_set_var('tplname', $tplname);
            $tplname = 'error';
        }

        //read the template
        $sTemplate = read_file($stylepath . '/' . $tplname . '.tpl.php');
        $sCode = mb_ereg_replace('{template}', $sTemplate, $sCode);





        //process the template replacements
        $sCode = tpl_do_replace($sCode);

        $sCode = tpl_do_translate($sCode);

        //store the cookie
        write_cookie_settings();

        //send http-no-caching-header
        http_write_no_cache();

        // write UTF8-Header
        header('Content-type: text/html; charset=utf-8');

        //run the template code
        eval($sCode);



        //disconnect the database
        if ($dbdisconnect) db_disconnect();
    }

    function http_write_no_cache()
    {
        // HTTP/1.1
        header("Cache-Control: no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        // HTTP/1.0
        header("Pragma: no-cache");
        // Date in the past
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        // always modified
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    }

    //redirect to another site to display, i.e. to view a cache after logging
    function tpl_redirect($page)
    {
        global $absolute_server_URI;

        //page has to be the filename without domain i.e. 'viecache.php?cacheid=1'
        write_cookie_settings();
        http_write_no_cache();
//echo 'p='.$page;
//die();
        header("Location: " . $absolute_server_URI . $page);
        exit;
    }

    function tpl_get_current_page()
    {
#       $pos = strrchr($_SERVER['SCRIPT_NAME'], '/');
#       return substr($_SERVER['REQUEST_URI'], $pos);
        return substr($_SERVER["REQUEST_URI"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
    }

    //redirect to another absolute url
    function tpl_redirect_absolute($absolute_server_URI)
    {
        //page has to be the filename with domain i.e. 'http://abc.de/viecache.php?cacheid=1'
        write_cookie_settings();
        http_write_no_cache();

        header("Location: " . $absolute_server_URI);
        exit;
    }


    function handle_translation_clause($matches)
    {
        $clause = substr($matches[0], 2, strlen($matches[0])-4);;

        return tr($clause);
    }

    function tpl_do_translate($str)
    {
        return preg_replace_callback('/{{.*?}}/', 'handle_translation_clause', $str);
    }


    //process the template replacements
    //no_eval_replace - if true, variables will be replaced that are
    //                  marked as "no_eval"
    function tpl_do_replace($str, $noeval = false)
    {
        global $vars, $no_eval_vars;


        if (is_array($vars))
        {
            foreach ($vars as $varname=>$varvalue)
            {
                if ($no_eval_vars[$varname] == false || $noeval)
                {
                    $str = mb_ereg_replace('{' . $varname . '}', $varvalue, $str);
                }
                else
                {
                    $replave_var_name = 'tpl_replace_var_' . $varname;

                    global $$replave_var_name;
                    $$replave_var_name = $varvalue;

                    //replace using php-echo
                    $str = mb_ereg_replace('{' . $varname . '}', '<?php global $' . $replave_var_name . '; echo $tpl_replace_var_' . $varname . '; ?>', $str);
                }
            }
        }



        return $str;
    }

    /* help_ for usefull functions
    *
    */

    // decimal longitude to string E/W hhh°mm.mmm
    function help_lonToDegreeStr($lon, $type = 1)
    {
        if ($lon < 0)
        {
            $retval = 'W ';
            $lon = -$lon;
        }
        else
        {
            $retval = 'E ';
        }


        if($type == 1) {
            $retval = $retval . sprintf("%02d", floor($lon)) . '° ';
            $lon = $lon - floor($lon);
            $retval = $retval . sprintf("%06.3f", round($lon * 60, 3)) . '\'';
        }
        else if($type == 0){
            $retval .= sprintf("%.5f", $lon) . '° ';
        }
        else if($type == 2) {
            $retval = $retval . sprintf("%02d", floor($lon)) . '° ';
            $lon = $lon - floor($lon);
            $lon *= 60;
            $retval = $retval . sprintf("%02d", floor($lon)) . '\' ';

            $lonmin = $lon - floor($lon);
            $retval = $retval . sprintf("%02.02f", $lonmin*60) . '\'\'';
        }

        return $retval;
    }

    // decimal latitude to string N/S hh°mm.mmm
    function help_latToDegreeStr($lat, $type = 1)
    {
        if ($lat < 0)
        {
            $retval = 'S ';
            $lat = -$lat;
        }
        else
        {
            $retval = 'N ';
        }

        if($type == 1) {
            $retval = $retval . sprintf("%02d", floor($lat)) . '° ';
            $lat = $lat - floor($lat);
            $retval = $retval . sprintf("%06.3f", round($lat * 60, 3)) . '\'';
        }
        else if($type == 0){
            $retval .= sprintf("%.5f", $lat) . '° ';
        }
        else if($type == 2) {
            $retval = $retval . sprintf("%02d", floor($lat)) . '° ';
            $lat = $lat - floor($lat);
            $lat *= 60;
            $retval = $retval . sprintf("%02d", floor($lat)) . '\' ';

            $latmin = $lat - floor($lat);
            $retval = $retval . sprintf("%02.02f", $latmin*60) . '\'\'';
        }

        return $retval;
    }

    // decimal longitude to array(direction, h, min)
    function help_lonToArray($lon)
    {
        if ($lon < 0)
        {
            $dir = 'W';
            $lon = -$lon;
        }
        else
        {
            $dir = 'E';
        }

        $h = sprintf("%02d", floor($lon));
        $lon = $lon - floor($lon);
        $min = sprintf("%06.3f", round($lon * 60, 3));

        return array($dir, $h, $min);
    }

    // decimal longitude to array(direction, h_int, min_int, sec_int, min_float)
    function help_lonToArray2($lon)
        {
                list($dir, $lon_h_int, $lon_min_float) = help_lonToArray($lon);

                $lon_min_int = sprintf("%02d", floor($lon_min_float));

        $lon_min_frac = $lon_min_float - $lon_min_int;
        $lon_sec_float = sprintf("%02.2f", $lon_min_frac * 60);

        return array($dir, $lon_h_int, $lon_min_int, $lon_sec_float, $lon_min_float);
    }

    // decimal latitude to array(direction, h, min)
    function help_latToArray($lat)
    {
        if ($lat < 0)
        {
            $dir = 'S';
            $lat = -$lat;
        }
        else
        {
            $dir = 'N';
        }

        $h = sprintf("%02d", floor($lat));
        $lat = $lat - floor($lat);
        $min = sprintf("%06.3f", round($lat * 60, 3));

        return array($dir, $h, $min);
    }

    // decimal latitude to array(direction, h_int, min_int, sec_int, min_float)
    function help_latToArray2($lat)
        {
                list($dir, $lat_h_int, $lat_min_float) = help_latToArray($lat);

                $lat_min_int = sprintf("%02d", floor($lat_min_float));

        $lat_min_frac = $lat_min_float - $lat_min_int;
        $lat_sec_float = sprintf("%02.2f", $lat_min_frac * 60);

        return array($dir, $lat_h_int, $lat_min_int, $lat_sec_float, $lat_min_float);
    }

    // create qth locator
    function help_latlongToQTH($lat, $lon)
    {

    $lon += 180;
    $l[0] = floor($lon/20);     $lon -= 20*$l[0];
    $l[2] = floor($lon/2);      $lon -= 2 *$l[2];
    $l[4] = floor($lon*60/5);

    $lat += 90;
    $l[1] = floor($lat/10);     $lat -= 10*$l[1];
    $l[3] = floor($lat);        $lat -=    $l[3];
    $l[5] = floor($lat*120/5);

    return sprintf("%c%c%c%c%c%c", $l[0]+65, $l[1]+65, $l[2]+48, $l[3]+48,
                       $l[4]+65, $l[5]+65);
    }

    //perform str_rot13 without renaming HTML-Tags
    function str_rot13_html($str)
    {
        $delimiter[0][0] = '&'; // start-char
        $delimiter[0][1] = ';'; // end-char
        $delimiter[1][0] = '<';
        $delimiter[1][1] = '>';
        $delimiter[2][0] = '[';
        $delimiter[2][1] = ']';

        $retval = '';

        while (mb_strlen($retval) < mb_strlen($str))
        {
            $nNextStart = false;
            $sNextEndChar = '';
            foreach ($delimiter AS $del)
            {
                $nThisStart = mb_strpos($str, $del[0], mb_strlen($retval));

                if ($nThisStart !== false)
                    if (($nNextStart > $nThisStart) || ($nNextStart === false))
                    {
                        $nNextStart = $nThisStart;
                        $sNextEndChar = $del[1];
                    }
            }

            if ($nNextStart === false)
            {
                $retval .= str_rot13(mb_substr($str, mb_strlen($retval), mb_strlen($str) - mb_strlen($retval)));
            }
            else
            {
                // crypted part
                $retval .= str_rot13(mb_substr($str, mb_strlen($retval), $nNextStart - mb_strlen($retval)));

                // uncrypted part
                $nNextEnd = mb_strpos($str, $sNextEndChar, $nNextStart);

                if ($nNextEnd === false)
                    $retval .= mb_substr($str, $nNextStart, mb_strlen($str) - mb_strlen($retval));
                else
                    $retval .= mb_substr($str, $nNextStart, $nNextEnd - $nNextStart + 1);
            }
        }

        return $retval;
    }

    if (!function_exists("stripos")) {
        function stripos($str,$needle,$pos) {
            return mb_strpos(mb_strtolower($str),mb_strtolower($needle),$pos);
        }
    }

    function help_addHyperlinkToURL($text)
    {
        $texti = mb_strtolower($text);
        $retval = '';
        $curpos = 0;
        $starthttp = mb_strpos($texti, 'http://', $curpos);
        $endhttp = false;
        while (($starthttp !== false) || ($endhttp >= mb_strlen($text)))
        {
            $endhttp1 = mb_strpos($text, ' ', $starthttp); if ($endhttp1 === false) $endhttp1 = mb_strlen($text);
            $endhttp2 = mb_strpos($text, "\n", $starthttp); if ($endhttp2 === false) $endhttp2 = mb_strlen($text);
            $endhttp3 = mb_strpos($text, "\r", $starthttp); if ($endhttp3 === false) $endhttp3 = mb_strlen($text);
            $endhttp4 = mb_strpos($text, '<', $starthttp); if ($endhttp4 === false) $endhttp4 = mb_strlen($text);
            $endhttp5 = mb_strpos($text, '] ', $starthttp); if ($endhttp5 === false) $endhttp5 = mb_strlen($text);
            $endhttp6 = mb_strpos($text, ')', $starthttp); if ($endhttp6 === false) $endhttp6 = mb_strlen($text);
            $endhttp7 = mb_strpos($text, '. ', $starthttp); if ($endhttp7 === false) $endhttp7 = mb_strlen($text);

            $endhttp = min($endhttp1, $endhttp2, $endhttp3, $endhttp4, $endhttp5, $endhttp6, $endhttp7);

            $retval .= mb_substr($text, $curpos, $starthttp - $curpos);
            $url = mb_substr($text, $starthttp, $endhttp - $starthttp);
            $retval .= '<a href="' . $url . '" alt="" target="_blank">' . $url . '</a>';

            $curpos = $endhttp ;
            if ($curpos >= mb_strlen($text)) break;
            $starthttp = mb_strpos(mb_strtolower($text), 'http://', $curpos);
        }

        $retval .= mb_substr($text, $curpos);

        return $retval;
    }

    function tpl_errorMsg($tplnameError, $msg)
    {
        global $tplname;

        $tplname = 'error';
        tpl_set_var('error_msg', $msg);
        tpl_set_var('tplname', $tplnameError);

        tpl_BuildTemplate();
        exit;
    }

    #
    # RFC(2)822 Email Parser
    #
    # By Cal Henderson <cal@iamcal.com>
    # This code is licensed under a Creative Commons Attribution-ShareAlike 2.5 License
    # http://creativecommons.org/licenses/by-sa/2.5/
    #
    # Revision 4
    #

    ##################################################################################

    function is_valid_email_address($email){

                // sprawdzenie czy email nie pochodzi z domeny no-mail.pl
                if( strpos($email,"no-mail.") !== false )
                    return 0;

        ####################################################################################
        #
        # NO-WS-CTL       =       %d1-8 /         ; US-ASCII control characters
        #                         %d11 /          ;  that do not include the
        #                         %d12 /          ;  carriage return, line feed,
        #                         %d14-31 /       ;  and white space characters
        #                         %d127
        # ALPHA          =  %x41-5A / %x61-7A   ; A-Z / a-z
        # DIGIT          =  %x30-39

        $no_ws_ctl    = "[\\x01-\\x08\\x0b\\x0c\\x0e-\\x1f\\x7f]";
        $alpha        = "[\\x41-\\x5a\\x61-\\x7a]";
        $digit        = "[\\x30-\\x39]";
        $cr        = "\\x0d";
        $lf        = "\\x0a";
        $crlf        = "($cr$lf)";


        ####################################################################################
        #
        # obs-char        =       %d0-9 / %d11 /          ; %d0-127 except CR and
        #                         %d12 / %d14-127         ;  LF
        # obs-text        =       *LF *CR *(obs-char *LF *CR)
        # text            =       %d1-9 /         ; Characters excluding CR and LF
        #                         %d11 /
        #                         %d12 /
        #                         %d14-127 /
        #                         obs-text
        # obs-qp          =       "\" (%d0-127)
        # quoted-pair     =       ("\" text) / obs-qp

        $obs_char    = "[\\x00-\\x09\\x0b\\x0c\\x0e-\\x7f]";
        $obs_text    = "($lf*$cr*($obs_char$lf*$cr*)*)";
        $text        = "([\\x01-\\x09\\x0b\\x0c\\x0e-\\x7f]|$obs_text)";
        $obs_qp        = "(\\x5c[\\x00-\\x7f])";
        $quoted_pair    = "(\\x5c$text|$obs_qp)";


        ####################################################################################
        #
        # obs-FWS         =       1*WSP *(CRLF 1*WSP)
        # FWS             =       ([*WSP CRLF] 1*WSP) /   ; Folding white space
        #                         obs-FWS
        # ctext           =       NO-WS-CTL /     ; Non white space controls
        #                         %d33-39 /       ; The rest of the US-ASCII
        #                         %d42-91 /       ;  characters not including "(",
        #                         %d93-126        ;  ")", or "\"
        # ccontent        =       ctext / quoted-pair / comment
        # comment         =       "(" *([FWS] ccontent) [FWS] ")"
        # CFWS            =       *([FWS] comment) (([FWS] comment) / FWS)

        #
        # note: we translate ccontent only partially to avoid an infinite loop
        # instead, we'll recursively strip comments before processing the input
        #

        $wsp        = "[\\x20\\x09]";
        $obs_fws    = "($wsp+($crlf$wsp+)*)";
        $fws        = "((($wsp*$crlf)?$wsp+)|$obs_fws)";
        $ctext        = "($no_ws_ctl|[\\x21-\\x27\\x2A-\\x5b\\x5d-\\x7e])";
        $ccontent    = "($ctext|$quoted_pair)";
        $comment    = "(\\x28($fws?$ccontent)*$fws?\\x29)";
        $cfws        = "(($fws?$comment)*($fws?$comment|$fws))";
        $cfws        = "$fws*";


        ####################################################################################
        #
        # atext           =       ALPHA / DIGIT / ; Any character except controls,
        #                         "!" / "#" /     ;  SP, and specials.
        #                         "$" / "%" /     ;  Used for atoms
        #                         "&" / "'" /
        #                         "*" / "+" /
        #                         "-" / "/" /
        #                         "=" / "?" /
        #                         "^" / "_" /
        #                         "" / "{" /
        #                         "|" / "}" /
        #                         "~"
        # atom            =       [CFWS] 1*atext [CFWS]

        $atext        = "($alpha|$digit|[\\x21\\x23-\\x27\\x2a\\x2b\\x2d\\x2e\\x3d\\x3f\\x5e\\x5f\\x60\\x7b-\\x7e])";
        $atom        = "($cfws?$atext+$cfws?)";


        ####################################################################################
        #
        # qtext           =       NO-WS-CTL /     ; Non white space controls
        #                         %d33 /          ; The rest of the US-ASCII
        #                         %d35-91 /       ;  characters not including "\"
        #                         %d93-126        ;  or the quote character
        # qcontent        =       qtext / quoted-pair
        # quoted-string   =       [CFWS]
        #                         DQUOTE *([FWS] qcontent) [FWS] DQUOTE
        #                         [CFWS]
        # word            =       atom / quoted-string

        $qtext        = "($no_ws_ctl|[\\x21\\x23-\\x5b\\x5d-\\x7e])";
        $qcontent    = "($qtext|$quoted_pair)";
        $quoted_string    = "($cfws?\\x22($fws?$qcontent)*$fws?\\x22$cfws?)";
        $word        = "($atom|$quoted_string)";


        ####################################################################################
        #
        # obs-local-part  =       word *("." word)
        # obs-domain      =       atom *("." atom)

        $obs_local_part    = "($word(\\x2e$word)*)";
        $obs_domain    = "($atom(\\x2e$atom)*)";


        ####################################################################################
        #
        # dot-atom-text   =       1*atext *("." 1*atext)
        # dot-atom        =       [CFWS] dot-atom-text [CFWS]

        $dot_atom_text    = "($atext+(\\x2e$atext+)*)";
        $dot_atom    = "($cfws?$dot_atom_text$cfws?)";


        ####################################################################################
        #
        # domain-literal  =       [CFWS] "[" *([FWS] dcontent) [FWS] "]" [CFWS]
        # dcontent        =       dtext / quoted-pair
        # dtext           =       NO-WS-CTL /     ; Non white space controls
        #
        #                         %d33-90 /       ; The rest of the US-ASCII
        #                         %d94-126        ;  characters not including "[",
        #                                         ;  "]", or "\"

        $dtext        = "($no_ws_ctl|[\\x21-\\x5a\\x5e-\\x7e])";
        $dcontent    = "($dtext|$quoted_pair)";
        $domain_literal    = "($cfws?\\x5b($fws?$dcontent)*$fws?\\x5d$cfws?)";


        ####################################################################################
        #
        # local-part      =       dot-atom / quoted-string / obs-local-part
        # domain          =       dot-atom / domain-literal / obs-domain
        # addr-spec       =       local-part "@" domain

        $local_part    = "($dot_atom|$quoted_string|$obs_local_part)";
        $domain        = "($dot_atom|$domain_literal|$obs_domain)";
        $addr_spec    = "($local_part\\x40$domain)";


        #
        # we need to strip comments first (repeat until we can't find any more)
        #

        $done = 0;

        while(!$done){
            $new = preg_replace("!$comment!", '', $email);
            if (strlen($new) == strlen($email)){
                $done = 1;
            }
            $email = $new;
        }


        #
        # now match what's left
        #

        return preg_match("!^$addr_spec$!", $email) ? 1 : 0;
        }

    function crypt_text($text)
    {
        global $sql_debug_cryptkey;

        /* Open module, and create IV */
        $td = mcrypt_module_open('tripledes', '', 'ecb', '');
        $key = substr($sql_debug_cryptkey, 0, mcrypt_enc_get_key_size($td));
        $iv_size = mcrypt_enc_get_iv_size($td);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

        /* Initialize encryption handle */
        if (mcrypt_generic_init($td, $key, $iv) != -1)
        {
            /* Encrypt data */
            $c_t = mcrypt_generic($td, $text);

            /* Clean up */
            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);

            return $c_t;
        }

        return false;
    }

    function decrypt_text($text)
    {
        global $sql_debug_cryptkey;

        /* Open module, and create IV */
        $td = mcrypt_module_open('tripledes', '', 'ecb', '');
        $key = substr($sql_debug_cryptkey, 0, mcrypt_enc_get_key_size($td));
        $iv_size = mcrypt_enc_get_iv_size($td);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

        /* Initialize encryption handle */
        if (mcrypt_generic_init($td, $key, $iv) != -1)
        {
            $p_t = mdecrypt_generic($td, $text);

            /* Clean up */
            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);

            return $p_t;
        }

        return false;
    }

    if( isset($usr['userid']))
        $usr['admin'] = mysql_result(mysql_query("SELECT admin FROM user WHERE user_id='".sql_escape($usr['userid'])."'"),0);

    function checkField($tableName,$columnName)
    {
        global $dbname;
        $tableFields = mysql_list_fields($dbname, $tableName);
        for($i=0;$i<mysql_num_fields($tableFields);$i++)
        {
            if(mysql_field_name($tableFields, $i)==$columnName)
            return 1;
        } //end of loop
        return 0;
    } //end of function

    function isPasswordRequired($cache_id)
    {
        // check if cache is password protected
        $password_req = sql("SELECT logpw FROM caches WHERE cache_id = &1",sql_escape($cache_id));
        if(mysql_num_rows($password_req) == 0)
                return false;
        $lm = sql_fetch_array($password_req);
        mysql_free_result($password_req);
        if( $lm['logpw'] == "" )
            return false;
        else
            return true;
    } // end isPasswordRequired
//'
    function coordToLocation($lat, $lon)
    {
        global $lang;
        $xml = "";
        $file = fopen('http://maps.google.com/maps/geo?q='.$lat.','.$lon.'&output=xml&oe=utf8&sensor=false&key=your_api_key&hl='.$lang, 'r');
        while (!feof($file))
        {
            $xml .= fread($file, 1024);
    }
        fclose($file);

        $array = xml2ary($xml);
        for( $i=0;$i<20;$i++)
        {
            $kraj = $array["kml"]["_c"]["Response"]["_c"]["Placemark"][$i]["_c"]["AddressDetails"]["_c"]["Country"]["_c"]["CountryName"]["_v"];
            $wojewodztwo = $array["kml"]["_c"]["Response"]["_c"]["Placemark"][$i]["_c"]["AddressDetails"]["_c"]["Country"]["_c"]["AdministrativeArea"]["_c"]["AdministrativeAreaName"]["_v"];
            $miasto = $array["kml"]["_c"]["Response"]["_c"]["Placemark"][$i]["_c"]["AddressDetails"]["_c"]["Country"]["_c"]["AdministrativeArea"]["_c"]["SubAdministrativeArea"]["_c"]["SubAdministrativeAreaName"]["_v"];

            if( $kraj != "" && $wojewodztwo != "" /*&& $miasto != ""*/)
                break;
        }
        if( $kraj == "" || $wojewodztwo == "" /*|| $miasto == ""*/ )
            $dziubek = "";
        else
            $dziubek = ">";
        return array( "kraj"=>$kraj, "woj"=>$wojewodztwo, "miasto"=>$miasto, "dziubek"=>$dziubek);
    }
    function coordToLocationOk($lat, $lon)
    {
        $xml = "";
        $file = fopen('http://maps.google.com/maps/geo?q='.$lat.','.$lon.'&output=xml&oe=utf8&sensor=false&key=your_api_key&hl=pl', 'r');
        while (!feof($file)) {
            $xml .= fread($file, 1024);
        }
        fclose($file);

        $array = xml2ary($xml);
        for( $i=0;$i<20;$i++) {
            $country = $array["kml"]["_c"]["Response"]["_c"]["Placemark"][$i]["_c"]["AddressDetails"]["_c"]["Country"]["_c"]["CountryName"]["_v"];
            $adm1 = $array["kml"]["_c"]["Response"]["_c"]["Placemark"][$i]["_c"]["AddressDetails"]["_c"]["Country"]["_c"]["AdministrativeArea"]["_c"]["AdministrativeAreaName"]["_v"];
            $adm2 = $array["kml"]["_c"]["Response"]["_c"]["Placemark"][$i]["_c"]["AddressDetails"]["_c"]["Country"]["_c"]["AdministrativeArea"]["_c"]["SubAdministrativeArea"]["_c"]["SubAdministrativeAreaName"]["_v"];

            if( $country != "" && $adm1 != "")
                break;
        }
        return array( $country, $adm1, $adm2 );
    }
    function cacheToLocationold($cache_id)
    {
        $res = sql("SELECT country, adm1, adm2 FROM cache_loc INNER
            JOIN caches ON (cache_loc.cache_id = caches.cache_id)
            WHERE cache_loc.cache_id = ?
            AND caches.latitude = cache_loc.latitude AND caches.longitude = cache_loc.longitude
            AND lang = ?", $cache_id, $lang);



    }

function typeToLetter($type)
{
    switch($type)
    {
        case "1":
        default:
            return "u";
        case "2":
            return "t";
        case "3":
            return "m";
        case "4":
            return "v";
        case "5":
            return "w";
        case "6":
            return "e";
        case "7":
            return "q";
        case "8":
            return "m";
    }
}

function wpToId($wp)
{
    $ocWP = $GLOBALS['oc_waypoint'];
    $wpType = mb_substr($wp, 0, 2);
    switch( $wpType )
    {
        case $ocWP:
            $tab_name = "wp_oc";
            break;
        case 'GC':
            $tab_name = "wp_gc";
            break;
        case 'GE':
            $tab_name = "wp_nc";
            break;
        default:
            return "";
    }
    $sql = "SELECT cache_id FROM caches WHERE ".$tab_name." = '".sql_escape($wp)."' LIMIT 1";
    return mysql_result(mysql_query($sql),1);
}

function fixPlMonth($string)
{
    $string = str_ireplace('styczeń', 'stycznia', $string);
    $string = str_ireplace('luty', 'lutego', $string);
    $string = str_ireplace('marzec', 'marca', $string);
    $string = str_ireplace('kwiecień', 'kwietnia', $string);
    $string = str_ireplace('maj', 'maja', $string);
    $string = str_ireplace('czerwiec', 'czerwca', $string);
    $string = str_ireplace('lipiec', 'lipca', $string);
    $string = str_ireplace('sierpień', 'sierpnia', $string);
    $string = str_ireplace('wrzesień', 'września', $string);
    $string = str_ireplace('październik', 'października', $string);
    $string = str_ireplace('listopad', 'listopada', $string);
    $string = str_ireplace('grudzień', 'grudnia', $string);
    return $string;
}

function tidy_html_description($text)
{
    // 2014-05-25 I removed function: stripslashes. There were problems with backslashes - triPPer
    return htmlspecialchars_decode($text);
    //return htmlspecialchars_decode(stripslashes($text));

    // old way, I have no idea what is going there and why, so I leave it as is for resque if above line will work not corrrect..
    $options = array("input-encoding" => "utf8", "output-encoding" => "utf8", "output-xhtml" => true, "doctype" => "omit", "show-body-only" => true, "char-encoding" => "utf8", "quote-ampersand" => true, "quote-nbsp" => true, "wrap" => 0);
    $tidy =  tidy_parse_string(html_entity_decode($text, ENT_NOQUOTES, "UTF-8"), $options);
    tidy_clean_repair($tidy);
    return $tidy;
}

function run_in_bg($Command, $Priority = 0)
{
    if($Priority)
        $PID = shell_exec("nohup nice -n $Priority $Command 2> /dev/null & echo $!");
    else
        $PID = shell_exec("nohup $Command 2> /dev/null & echo $!");
     return($PID);
}

function is_running($PID)
{
    exec("ps $PID", $ProcessState);
    return(count($ProcessState) >= 2);
}

function wait_for_pid($pid)
{
    while(is_running($pid)) usleep(100000);
}

function encrypt($text, $key)
{
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, $iv));
}

function decrypt($text, $key)
{
    if(!$text)
        return "";
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($text), MCRYPT_MODE_ECB, $iv), "\0");
}

function validate_msg($cookietext)
{
    if(!ereg("[0-9]+ This is a secret message", $cookietext))
        return false;

    $num=0;
    sscanf($cookietext, "%d", $num);
    return $num;
}

function get_image_for_interval($folder, $interval = 1800)
{
    $extList = array();
    $extList['gif'] = 'image/gif';
    $extList['jpg'] = 'image/jpeg';
    $extList['jpeg'] = 'image/jpeg';
    $extList['png'] = 'image/png';

    $img = null;

    if (substr($folder,-1) != '/') {
    $folder = $folder . '/';
    }

    $fileList = array();
    $handle = opendir($folder);
    while (false !== ($file = readdir($handle)))
    {
    $file = $folder . $file;
    if (is_file($file)) {
                $file_info = pathinfo($file);
                if (isset($extList[strtolower($file_info['extension'])]))
                {
                        $fileList[] = $file;
                }
        }
    }
    closedir($handle);

    if (count($fileList) > 0)
    {
    $imageNumber = floor(time() / $interval) % count($fileList);
    $img = $fileList[$imageNumber];
    return $img;
    }
    return "";
}

/**
 * class autoloader
 */
require_once __DIR__.'/ClassPathDictionary.php';

/**
 * class witch common methods
 */
class common
{

    /**
     * add slashes to each element of $array.
     * @param array $array
     */
    public static function sanitize(&$array){
        foreach ($array as $key => $value) {
            if(is_array($value)){
                self::sanitize($value);
            } else {
                $array[$key] = addslashes(htmlspecialchars($value));
            }
        }
    }


    /* (not used yet - for future use)
    private $powerTrailModuleSwitchOn = false;
    public function __construct() {
        include_once __DIR__.'/settings.inc.php';
        $this->powerTrailModuleSwitchOn = $powerTrailModuleSwitchOn;
    }
    */


    public static function cleanupText($str)
    {
        $str = strip_tags($str, "<li>");
        $from[] = '<p>&nbsp;</p>'; $to[] = '';
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
        $from[] = '('; $to[] = '[';
        $from[] = ')'; $to[] = ']';
        $from[] = '&'; $to[] = '';
        $from[] = '\''; $to[] = '';
        $from[] = '"'; $to[] = '';
        $from[] = '<'; $to[] = '';
        $from[] = '>'; $to[] = '';
        $from[] = ']]>'; $to[] = ']] >';
        $from[] = ''; $to[] = '';
        for ($i = 0; $i < count($from); $i++) {
            $str = str_replace($from[$i], $to[$i], $str);
        }
        return self::filterevilchars($str);
    }

    public static function buildCacheSizeSelector($sel_type, $sel_size)
    {
        $cache = cache::instance();
        $cacheSizes = $cache->getCacheSizes();

        $sizes = '<option value="-1">'.tr('select_one').'</option>';
        foreach ($cacheSizes as $size) {
            if( $sel_type == 6 ) {
                if ($size['id'] == cache::SIZE_NOCONTAINER ) {
                    $sizes .= '<option value="' . $size['id'] . '" selected="selected">' . tr($size['translation']) . '</option>';
                    tpl_set_var('is_disabled_size', '');
                } else {
                    $sizes .= '<option value="' . $size['id'] . '">' . tr($size['translation']) . '</option>';
                    tpl_set_var('is_disabled_size', 'disabled');
                }
            } elseif( $size['id'] != cache::SIZE_NOCONTAINER ) {
                if ($size['id'] == $sel_size ) {
                    $sizes .= '<option value="' . $size['id'] . '" selected="selected">' .tr($size['translation']) . '</option>';
                } else {
                    $sizes .= '<option value="' . $size['id'] . '">' . tr($size['translation']) . '</option>';
                }
            }
        }
        return $sizes;
    }
    /**
     * @param type $db
     */
    public static function getUserActiveCacheCountByType(dataBase $db, $userId ){
        $query = 'SELECT type, count(*) as cacheCount FROM `caches` WHERE `user_id` = :1 AND STATUS !=3 GROUP by type';
        $db->multiVariableQuery($query, $userId);
        $userCacheCountByType = $db->dbResultFetchAll();
        $cacheLimitByTypePerUser = array();
        foreach ($userCacheCountByType as $cacheCount) {
            $cacheLimitByTypePerUser[$cacheCount['type']] = $cacheCount['cacheCount'];
        }
        return $cacheLimitByTypePerUser;
    }

    private static function filterevilchars($str) {
        return str_replace('[\\x00-\\x09|\\x0A-\\x0E-\\x1F]', '', $str);
    }




}

?>
