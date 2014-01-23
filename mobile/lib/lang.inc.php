<?php

    if(isset($_COOKIE['lang']))

        switch($_COOKIE['lang']){
        case 'en':
            $lang='en';
            break;
        case 'pl':
            $lang='pl';
            break;
        case 'nl':
            $lang='nl';
            break;
        case 'de':
            $lang='de';
            break;
        default:
            $lang='pl';

    }
    else
        $lang='pl';

    $fhandle = fopen(dirname(__FILE__) . "/lang/".$lang, "r");

    if($fhandle) {

        while($line = fgets($fhandle, 4096)) {
            $pos = strpos($line, ' ');
            $short = substr($line, 0, $pos);
            $translation =substr($line, $pos+1, -1);
            $translation = rtrim($translation, "\r\n");
            $tpl->assign($short,$translation);
        }
        fclose($fhandle);
    }

?>
