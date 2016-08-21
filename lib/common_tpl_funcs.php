<?php

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
    //echo 'p='.$page;
    //die();
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
function tpl_getView(){

    global $view;
    return $view;
}

// TODO: set PHP var which can be accessed inside tpl file
function setViewVar($name, $value){

    global $view;
    $view->setVar($name, $value);
}



//read the templates and echo it to the user
function tpl_BuildTemplate($dbdisconnect = true, $minitpl = false, $noCommonTemplate=false)
{
    //template handling vars
    global $stylepath, $tplname, $vars, $langpath, $lang_array, $lang, $language, $menu, $config, $usr;

    // object
    global $view;

    //language specific expression
    global $error_pagenotexist;
    //only for debbuging
    global $bScriptExecution;

    $bScriptExecution->Stop();
    tpl_set_var('scripttime', sprintf('%1.3f', $bScriptExecution->Diff()));
    tpl_set_var('language_flags', writeLanguageFlags($lang_array));

    $bTemplateBuild = new Cbench;
    $bTemplateBuild->Start();

    //set {functionsbox}
    global $page_functions, $functionsbox_start_tag, $functionsbox_middle_tag, $functionsbox_end_tag;

    if (isset($page_functions)) {
        $functionsbox = $functionsbox_start_tag;
        foreach ($page_functions AS $func) {
            if ($functionsbox != $functionsbox_start_tag) {
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
    if ($minitpl)
        $sCode = file_get_contents($stylepath . '/mini.tpl.php');
        else if ($noCommonTemplate)
            $sCode = '{template}';
            else if (isset($_REQUEST['print']) && $_REQUEST['print'] == 'y')
                $sCode = file_get_contents($stylepath . '/main_print.tpl.php');
                else if (isset($_REQUEST['popup']) && $_REQUEST['popup'] == 'y')
                    $sCode = file_get_contents($stylepath . '/popup.tpl.php');
                    else
                        $sCode = file_get_contents($stylepath . '/main.tpl.php');

                        //does template exist?
                        if (!file_exists($stylepath . '/' . $tplname . '.tpl.php')) {
                            //set up the error template
                            $error = true;
                            tpl_set_var('error_msg', htmlspecialchars($error_pagenotexist, ENT_COMPAT, 'UTF-8'));
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




/* TODO: NOT USED ANYWHERE...

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
*/