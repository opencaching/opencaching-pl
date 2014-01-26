<?php
$language = array();

function load_language_file($lang)
{
    global $language;

    $cache_key = "oclang/$lang";
    $result = apc_fetch($cache_key);
    if ($result === false)
    {
        $result = array();
        $fhandle = fopen(dirname(__FILE__) . "/languages/".$lang, "r");
        if($fhandle) {
            while($line = fgets($fhandle, 4096)) {
                $pos = strpos($line, ' ');
                $short = substr($line, 0, $pos);
                $translation =substr($line, $pos+1, -1);
                $translation = rtrim($translation, "\r\n");
                $result[$short]=$translation;
            }
            fclose($fhandle);
        } else {
            $result = null;
        }
        apc_store($cache_key, $result, 60);  # cache it for 60 seconds
    }
    if ($result) {
        $language[$lang] = &$result;
        return true;
    } else {
        return false;
    }
}

function available_languages()
{
    $available_langs = array();
    if ($handle = opendir(dirname(__FILE__).'/languages')) {
        while (false !== ($file = readdir($handle))) {
            if (substr($file, 0, 1) != '.' && strlen($file) == 2) {
                array_push($available_langs, $file);
            }
        }
        closedir($handle);
        return $available_langs;
    }
}

function tr($str)
{
    global $language, $lang;
    if(isset($language[$lang][$str])&&$language[$lang][$str]) {
        $ref = &$language[$lang][$str];
        if (strpos($ref, "{") !== false)
            return tpl_do_replace($ref, true);
        else
            return $ref;
    }
    else
        return "No translation available (identifier: $str)-todo";
}

function tr2($str, $lang) {
    global $language;
    load_language_file($lang);

    if(@$language[$lang][$str]) {
        $ref = &$language[$lang][$str];
        if (strpos($ref, "{") !== false)
            return tpl_do_replace($ref, true);
        else
            return $ref;
    } else {
        return $str . "No translation available (identifier: $str)-todo";
    }
}

?>
