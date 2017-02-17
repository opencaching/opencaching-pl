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

function available_languages()
{
    $available_langs = array();
    if ($handle = opendir(dirname(__FILE__) . '/languages')) {
        while (false !== ($file = readdir($handle))) {
            if (substr($file, 0, 1) != '.' && strlen($file) == 6) {
                array_push($available_langs, substr($file, 0,2));
            }
        }
        closedir($handle);
        return $available_langs;
    }
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

