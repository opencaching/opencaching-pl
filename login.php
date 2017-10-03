<?php

use Utils\View\View;
use lib\Objects\User\UserAuthorization;

require_once('./lib/common.inc.php');


// check target to redirect after login
$target = 'index.php';
if (isset($_REQUEST['target'])){
    $target = $_REQUEST['target'];
}


if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'cookieverify') {
    if (UserAuthorization::isAuthCookiePresent()){
        tpl_redirect($target);
    } else {
        // display message if cookie can't be set in browser
        tpl_errorMsg('login', tr('login_cantSetCookie'));
    }
    exit;
}


global $usr;

if($usr != false){
    //alredy logged in...
    tpl_redirect('login.php?action=cookieverify&target=' . urlencode($target));
    exit;
}


// if-currently-not-logged-in

tpl_set_tplname('login');
$view = tpl_getView();
$view->loadJQuery();

$view->setVar('target', $target);

if(isset($_POST['email']) && isset($_POST['password'])){

    $userEmail = $_POST['email'];
    $userPassword = $_POST['password'];

    if ( !empty($userEmail) && !empty($userPassword)) {

        $loginResult = UserAuthorization::checkCredensials($userEmail, $userPassword);

        switch ($loginResult) {
            case UserAuthorization::LOGIN_OK:
                tpl_redirect('login.php?action=cookieverify&target=' . urlencode($target));
                exit;

                break;
            case UserAuthorization::LOGIN_TOOMUCHLOGINS:
                $view->setVar('errorMsg', tr('login_tooManyTries'));
                break;
            case UserAuthorization::LOGIN_USERNOTACTIVE:
                $view->setVar('errorMsg', tr('error_usernotactive'));
                break;
            case UserAuthorization::LOGIN_BADUSERPW:
            default:
                $view->setVar('errorMsg', tr('login_badCredentials'));
        }

    }else{
        $view->setVar('errorMsg', tr('login_badCredentials'));
    }
}else{ // just display login page
    $view->setVar('errorMsg', null);
}

tpl_BuildTemplate();

