<?php

use Utils\View\View;
use Utils\Uri\Uri;
use Utils\I18n\I18n;

//set the global template-name variable
function tpl_set_tplname($local_tpl_name){
    global $tplname;
    $tplname = $local_tpl_name;
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

    if (isset($vars[$name])) {
        return $vars[$name];
    } else {
        return false;
    }
}


//redirect to another site to display, i.e. to view a cache after logging
function tpl_redirect($page)
{
    global $absolute_server_URI;

    //page has to be the filename without domain i.e. 'viecache.php?cacheid=1'
    write_cookie_settings();
    http_write_no_cache();

    header("Location: " . $absolute_server_URI . $page);
    exit;
}

function tpl_get_current_page()
{
    #       $pos = strrchr($_SERVER['SCRIPT_NAME'], '/');
    #       return substr($_SERVER['REQUEST_URI'], $pos);
    return substr($_SERVER["REQUEST_URI"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1);
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
    $clause = substr($matches[0], 2, strlen($matches[0]) - 4);

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


    if (is_array($vars)) {
        foreach ($vars as $varname => $varvalue) {
            if ($no_eval_vars[$varname] == false || $noeval) {
                $str = mb_ereg_replace('{' . $varname . '}', $varvalue, $str);
            } else {
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

function tpl_errorMsg($tplnameError, $msg)
{
    global $tplname;

    $tplname = 'error';
    tpl_set_var('error_msg', $msg);
    tpl_set_var('tplname', $tplnameError);

    tpl_BuildTemplate();
    exit;
}


//TODO: this is temporary solution for backward compatibility
// $view will be a context variable in further implementaion
/**
 * @return View
 */
function tpl_getView(){

    global $view;
    return $view;
}

// TODO: set PHP var which can be accessed inside tpl file
function setViewVar($name, $value){

    global $view;
    $view->setVar($name, $value);
}

function set_tpl_subtitle($title)
{
    global $tpl_subtitle;
    $tpl_subtitle = $title;
}

//read the templates and echo it to the user
function tpl_BuildTemplate($dbdisconnect = true, $minitpl = false, $noCommonTemplate=false)
{
    //template handling vars
    global $stylepath, $tplname, $vars, $lang, $menu, $config, $usr;

    // object
    /** @var View $view */
    global $view;


    $view->setVar('languageFlags', I18n::getLanguagesFlagsData($lang));

    //load main template
    if ($minitpl){
        $sCode = file_get_contents($stylepath . '/common/mini.tpl.php');
    }else if ($noCommonTemplate){
        $sCode = '{template}';
    }else if (isset($_REQUEST['print']) && $_REQUEST['print'] == 'y'){
        $sCode = file_get_contents($stylepath . '/common/main_print.tpl.php');
    }else if (isset($_REQUEST['popup']) && $_REQUEST['popup'] == 'y'){
        $sCode = file_get_contents($stylepath . '/common/popup.tpl.php');
    }else {
        $sCode = file_get_contents($stylepath . '/common/main.tpl.php');
    }

    //global css files:
    $view->setVar('screenCss', Uri::getLinkWithModificationTime('/tpl/stdstyle/css/style_screen.css'));
    $view->setVar('printCss', Uri::getLinkWithModificationTime('/tpl/stdstyle/css/style_print.css'));
    $view->setVar('backgroundSeason', $view->getSeasonCssName());

    //does template exist?
    if (!file_exists($stylepath . '/' . $tplname . '.tpl.php')) {
        //set up the error template
        $error = true;
        tpl_set_var('error_msg', "Page not found");
        tpl_set_var('tplname', $tplname);
        $tplname = 'error';
    }

    //read the template
    $sTemplate = file_get_contents($stylepath . '/' . $tplname . '.tpl.php');
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
    eval('?>'.$sCode);
}

//store the cookie vars
function write_cookie_settings()
{
    global $cookie, $lang;

    //language
    $cookie->set('lang', $lang);

    //send cookie
    $cookie->header();
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

/* TODO: NOT USED ANYWHERE...

//clear all template vars
function tpl_clear_vars()
{
unset($GLOBALS['vars']);
unset($GLOBALS['no_eval_vars']);
}

*/