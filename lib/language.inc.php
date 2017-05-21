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

function tr($str)
{
    global $language, $lang;

    if (isset($language[$lang][$str]) && $language[$lang][$str]) {
        $ref = &$language[$lang][$str];
        if (strpos($ref, "{") !== false)
            return tpl_do_replace($ref, true);
        else
            return $ref;
    } else
        return "No translation available (identifier: $str)-todo";
}

function tr2($str, $lang)
{
    global $language;
    load_language_file($lang);

    if (@$language[$lang][$str]) {
        $ref = &$language[$lang][$str];
        if (strpos($ref, "{") !== false)
            return tpl_do_replace($ref, true);
        else
            return $ref;
    } else {
        return $str . "No translation available (identifier: $str)-todo";
    }
}

// returns true if given traslation is available
function tr_available($str){

    global $language, $lang;

    return isset($language[$lang][$str]) && $language[$lang][$str];
}

