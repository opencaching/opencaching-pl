<?php

if (isset($_COOKIE['lang']))
    switch ($_COOKIE['lang']) {
        case 'en':
            $lang2 = 'en';
            break;
        case 'pl':
            $lang2 = 'pl';
            break;
        case 'nl':
            $lang2 = 'nl';
            break;
        case 'de':
            $lang2 = 'de';
            break;
        case 'ro':
            $lang2 = 'ro';
            break;
        default:
            $lang2 = $lang;
    } else
    $lang2 = $lang;

$fhandle = fopen(dirname(__FILE__) . "/lang/" . $lang2, "r");

if ($fhandle) {

    while ($line = fgets($fhandle, 4096)) {
        $pos = strpos($line, ' ');
        $short = substr($line, 0, $pos);
        $translation = substr($line, $pos + 1, -1);
        $translation = rtrim($translation, "\r\n");
        $tpl->assign($short, $translation);
    }
    fclose($fhandle);
}
?>
