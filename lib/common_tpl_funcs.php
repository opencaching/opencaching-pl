<?php

use src\Controllers\PageLayout\MainLayoutController;
use src\Utils\Uri\Uri;
use src\Utils\View\View;

//set the global template-name variable
function tpl_set_tplname($local_tpl_name)
{
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


//redirect to another site to display, i.e. to view a cache after logging
function tpl_redirect($page)
{
    global $absolute_server_URI;

    //page has to be the filename without domain i.e. 'viecache.php?cacheid=1'
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
// $view will be a context variable in further implementation
/**
 * @return View
 */
function tpl_getView(): View
{
    global $view;
    if (!$view) {
        $view = new View();
    }
    return $view;
}

//read the templates and echo it to the user
function tpl_BuildTemplate($minitpl = false, $noCommonTemplate = false)
{
    //template handling vars
    global $tplname, $vars, $config;

    // object
    /** @var View $view */
    global $view;

    MainLayoutController::initLegacy(); // init vars for main-layout

    if ($view->showGdprPage()) {
        $tplname = 'userProfile/gdpr';
    }

    //load main template
    if ($minitpl) {
        $sCode = file_get_contents(__DIR__ . '/../src/Views/common/mini.tpl.php');
    } else if ($noCommonTemplate) {
        $sCode = '{template}';
    } else if (isset($_REQUEST['print']) && $_REQUEST['print'] == 'y') {
        $sCode = file_get_contents(__DIR__ . '/../src/Views/common/main_print.tpl.php');
    } else if (isset($_REQUEST['popup']) && $_REQUEST['popup'] == 'y') {
        $sCode = file_get_contents(__DIR__ . '/../src/Views/common/popup.tpl.php');
    } else {
        $sCode = file_get_contents(__DIR__ . '/../src/Views/common/main.tpl.php');
    }

    //global css files:
    $view->setVar('screenCss', Uri::getLinkWithModificationTime('/css/style_screen.css'));
    $view->setVar('responsiveCss', Uri::getLinkWithModificationTime('/css/style_responsive.css'));
    $view->setVar('printCss', Uri::getLinkWithModificationTime('/css/style_print.css'));
    $view->setVar('backgroundSeason', $view->getSeasonCssName());

    //does template exist?
    if (!file_exists(__DIR__ . '/../src/Views/' . $tplname . '.tpl.php')) {
        //set up the error template
        tpl_set_var('error_msg', tr('page_not_found'));
        tpl_set_var('tplname', $tplname);
        $tplname = 'error';
    }

    //read the template
    $sTemplate = file_get_contents(__DIR__ . '/../src/Views/' . $tplname . '.tpl.php');
    $sCode = mb_ereg_replace('{template}', $sTemplate, $sCode);


    //process the template replacements
    $sCode = tpl_do_replace($sCode);

    $sCode = tpl_do_translate($sCode);

    //send http-no-caching-header
    http_write_no_cache();

    // write UTF8-Header
    header('Content-type: text/html; charset=utf-8');

    //run the template code
    $v = $view; // $v is tpl alias to $view
    $GLOBALS['_lastTplUsed'] = $tplname;
    eval('?>' . $sCode);
}

function http_write_no_cache()
{
    // HTTP/1.1
    header("Cache-Control: no-cache");
    // Date in the past
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    // always modified
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
}

