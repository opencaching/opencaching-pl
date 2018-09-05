<?php

$language = array();

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

function tr($str)
{
    global $language, $lang, $config;

    if (isset($language[$lang][$str]) && $language[$lang][$str]) {
        return postProcessTr($language[$lang][$str]);
    } else if (
        isset($config['failoverLanguage'])
        && isset($language[$config['failoverLanguage']][$str])
        && $language[$config['failoverLanguage']][$str]
    ) {
        return postProcessTr($language[$config['failoverLanguage']][$str]);
    } else {
        return "No translation available (identifier: $str)-todo";
    }
}

function tr2($str, $lang)
{
    global $language, $config;

    if (!isset($language[$lang])) {
        load_language_file($lang);
    }

    if (@$language[$lang][$str]) {
        return postProcessTr($language[$lang][$str]);
    } else if (
        isset($config['failoverLanguage'])
        && isset($language[$config['failoverLanguage']][$str])
        && $language[$config['failoverLanguage']][$str]
    ) {
        return postProcessTr($language[$config['failoverLanguage']][$str]);
    } else {
        return $str . "No translation available (identifier: $str)-todo";
    }
}

// returns true if given traslation is available
function tr_available($str){

    global $language, $lang;

    return isset($language[$lang][$str]) && $language[$lang][$str];
}
