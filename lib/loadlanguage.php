<?php
use Utils\I18n\I18n;

if (!isset($rootpath))
    $rootpath = './';

require_once($rootpath . 'lib/language.inc.php');

global $lang, $cookie;
if ($cookie->is_set('lang')) {
    $lang = $cookie->get('lang');
}

//language changed?
if(isset($_REQUEST['lang'])){
    $lang = $_REQUEST['lang'];
}

//check if $lang is supported by site
if(!I18n::isTranslationSupported($lang)){
    /*
     tpl_set_tplname('error/langNotSupported');

     $view->setVar('requestedLang', $lang);
     $lang = 'en'; //English must be always supported


     tpl_BuildTemplate();

     */

    echo("Error: The specified language ($lang) is not supported!<br/>");
    echo("Please select on of supported language versions:&nbsp;");
    foreach (I18n::getLanguagesFlagsData() as $lName=>$lData){
        echo '<a href="'.$lData['link'].'">'.strtoupper($lName).'</a>&nbsp;';
    }
    exit;
}



// load language settings
load_language_file($lang);


switch ($lang) {
    case 'pl':
        setlocale(LC_CTYPE, 'pl_PL.UTF-8');
        setlocale(LC_TIME, 'pl_PL.UTF-8');
        break;
    case 'nl':
        setlocale(LC_CTYPE, 'nl_NL.UTF-8');
        setlocale(LC_TIME, 'nl_NL.UTF-8');
        break;
    case 'fr':
        setlocale(LC_CTYPE, 'fr_FR.UTF-8');
        setlocale(LC_TIME, 'fr_FR.UTF-8');
        break;
    case 'de':
        setlocale(LC_CTYPE, 'de_DE.UTF-8');
        setlocale(LC_TIME, 'de_DE.UTF-8');
        break;
    case 'sv':
        setlocale(LC_CTYPE, 'sv_SV.UTF-8');
        setlocale(LC_TIME, 'sv_SV.UTF-8');
        break;
    case 'es':
        setlocale(LC_CTYPE, 'es_ES.UTF-8');
        setlocale(LC_TIME, 'es_ES.UTF-8');
        break;
    case 'cs':
        setlocale(LC_CTYPE, 'cs_CS.UTF-8');
        setlocale(LC_TIME, 'cs_CS.UTF-8');
        break;
    case 'ro':
        setlocale(LC_CTYPE, 'ro_RO.UTF-8');
        setlocale(LC_TIME, 'ro_RO.UTF-8');
        break;
    case 'hu':
        setlocale(LC_CTYPE, 'hu_HU.UTF-8');
        setlocale(LC_TIME, 'hu_HU.UTF-8');
        break;
    default:
        setlocale(LC_CTYPE, 'en_EN');
        setlocale(LC_TIME, 'en_EN');
        break;
}
