<?php
use Utils\I18n\I18n;
use Utils\Uri\Uri;
use Utils\I18n\Languages;
use Utils\Text\UserInputFilter;
use Utils\Uri\OcCookie;

//English must be always supported
define('FAILOVER_LANGUAGE', 'en');

$language = array();

function initTranslations()
{
    global $lang, $language;
    $result = false;

    //language changed?
    if(isset($_REQUEST['lang'])){
        $lang = $_REQUEST['lang'];
    } else {
        $lang = OcCookie::getOrDefault('lang', $lang);
    }

    //check if $lang is supported by site
    if(!I18n::isTranslationSupported($lang)){
        // requested language is not supported - display error...
        tpl_set_tplname('error/langNotSupported');
        header("HTTP/1.0 404 Not Found");
        $view = tpl_getView();

        $view->loadJQuery();
        $view->setVar("localCss",
            Uri::getLinkWithModificationTime('/tpl/stdstyle/error/error.css'));
        $view->setVar('requestedLang', UserInputFilter::purifyHtmlString($lang));
        $lang = FAILOVER_LANGUAGE;

        $view->setVar('allLanguageFlags', I18n::getLanguagesFlagsData());
        load_language_file($lang);

        tpl_BuildTemplate();
        exit;
    }

    load_language_file($lang);
    Languages::setLocale($lang);
}

function load_language_file($lang)
{
    global $language;
    $languageFilename = __DIR__ . "/languages/" . $lang.'.php';
    if(!file_exists($languageFilename)){
        return false;
    }
    include ($languageFilename);
    $language[$lang] = $translations;
    return true;
}

function postProcessTr(&$ref)
{
    if (strpos($ref, "{") !== false)
        return tpl_do_replace($ref, true);
    else
        return $ref;
}

function getFailoverTranslation($str) {
    global $language;
    $result = null;
    if (!isset($language[FAILOVER_LANGUAGE])) {
        load_language_file(FAILOVER_LANGUAGE);
    }
    if (
        isset($language[FAILOVER_LANGUAGE][$str])
        && $language[FAILOVER_LANGUAGE][$str]
    ) {
        $result = postProcessTr($language[FAILOVER_LANGUAGE][$str]);
    }
    return $result;
}

function getTranslation($str, $lang) {
    global $language;
    $result = null;

    if (isset($language[$lang][$str]) && $language[$lang][$str]) {
        $result = postProcessTr($language[$lang][$str]);
    } else {
        $result = getFailoverTranslation($str);
    }
    if ($result == null) {
        $result = "No translation available (identifier: $str)-todo";
    }
    return $result;
}

/**
 * Return tranlated string
 *
 * @param string $str - translation key
 * @param array $args - arguments to insert into string (see vsprinf for details)
 * @return string - localized string
 */
function tr($str, array $args = null)
{
    global $language, $lang;
    if(is_null($args)){
        return getTranslation($str, $lang);
    }else{
        return vprintf(getTranslation($str, $lang), $args);
    }
}

function tr2($str, $lang)
{
    global $language;

    if (!isset($language[$lang])) {
        load_language_file($lang);
    }

    return getTranslation($str, $lang);
}

// returns true if given traslation is available
function tr_available($str){

    global $language, $lang;

    return isset($language[$lang][$str]) && $language[$lang][$str];
}
