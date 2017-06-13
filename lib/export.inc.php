<?php

/**
 * -- This function was moved here from search.inc.* --
 * Replace LF with CRLF
 * @param unknown $str
 */
function lf2crlf($str)
{
    return str_replace("\r\r\n" ,"\r\n" , str_replace("\n" ,"\r\n" , $str));
}

/**
 * -- This function was moved here from search.inc.* --
 * Minimal HTML->plain_text transformation
 * @param unknown $str
 */
function html2txt($html)
{
    $str = mb_ereg_replace('/[[:cntrl:]]/', '', $html);
    $str = str_replace("\r\n", '', $str);
    $str = str_replace("\n", '', $str);
    $str = str_replace('<br />', "\n", $str);
    $str = str_replace('<br>', "\n", $str);
    $str = str_replace('</p>', "\n", $str);
    $str = str_replace('<li>', "-", $str);
    $str = str_replace('&quot;', '"', $str);
    $str = str_replace('&amp;', '&', $str);
    $str = str_replace('&lt;', '<', $str);
    $str = str_replace('&gt;', '>', $str);
    $str = str_replace(']]>', '', $str);
    $str = strip_tags($str);

    return $str;
}

/**
 * -- This function was moved here from search.inc.* --
 * Replace reserved XML characters with entities
 * @param unknown $str
 */
function xmlentities($str) {
    $from[0] = '&'; $to[0] = '&amp;';
    $from[1] = '<'; $to[1] = '&lt;';
    $from[2] = '>'; $to[2] = '&gt;';
    $from[3] = '"'; $to[3] = '&quot;';
    $from[4] = '\''; $to[4] = '&apos;';
    $from[5] = ']]>'; $to[5] = ']] >';

    for ($i = 0; $i <= 4; $i ++)
        $str = str_replace($from[$i], $to[$i], $str);
    $str = mb_ereg_replace('/[[:cntrl:]]/', '', $str);
    return $str;
}

/**
 * -- This function was moved here from search.inc.* --
 * Cleanup textReplace reserved XML characters with entities
 * @param unknown $str
 */
function cleanup_text($str) {
    //$str= tidy_html_description($str);
    //$str = convert_lang('UTF-8','LATIN',$str);    // old implentation
    $str = convert_string($str);

    $str = strip_tags($str, "<p><br /><li>");

    $from[] = '&nbsp;'; $to[] = ' ';
    $from[] = '<p>'; $to[] = '';        // <p> -> remove
    $from[] = '</p>'; $to[] = "\n";     // </p> -> new line
    $from[] = '<br />'; $to[] = "\n";
    $from[] = '<br>'; $to[] = "\n";
    $from[] = '<br>'; $to[] = "\n";

    $from[] = '<li>'; $to[] = " - ";    // <li> -> -
    $from[] = '</li>'; $to[] = "\n";    // </li> -> new line

    $from[] = '&oacute;'; $to[] = 'o';
    $from[] = '&quot;'; $to[] = '"';
    $from[] = '&[^;]*;'; $to[] = '';    // html entity encoded characters

    $from[] = '&'; $to[] = '&amp;';
    $from[] = '<'; $to[] = '&lt;';
    $from[] = '>'; $to[] = '&gt;';
    $from[] = ']]>'; $to[] = ']] >';
    $from[] = ''; $to[] = '';

    for ($i = 0; $i < count($from); $i++)
        $str = str_replace($from[$i], $to[$i], $str);
    $str = mb_ereg_replace('/[[:cntrl:]]/', '', $str);
    return $str;
}

/**
 * -- This function was moved here from search.inc.* --
 * Convert regional special characters between character encodings
 *
 * $source  - string    = source encoding
 * $dest    - string    = destinatio encoding
 * $str     - string    = text to convert
 *
 * Valid encodings:
 * LATIN
 * POLSKAWY (powoduje zamianę polskich liter na ich łacińskie odpowiedniki)
 * ISO-8859-2
 * WINDOWS-1250
 * UTF-8
 * ENTITIES (HTML entities)
 *
 * Example: echo(PlConvert('UTF-8','ISO-8859-2','Zażółć gęślą jaźń.'));
 */

function convert_lang($source,$dest,$str)
{
    $source=strtoupper($source);
    $dest=strtoupper($dest);
    if($source==$dest) return $str;
    $chars['LATIN']         =array('a','c','e','l','n','o','s','z','z','A','C','E','L','N','O','S','Z','Z');
    $chars['POLSKAWY']      =array('a','c','e','l','n','o','s','z','z','A','C','E','L','N','O','S','Z','Z');
    $chars['ISO-8859-2']    =array("\xB1","\xE6","\xEA","\xB3","\xF1","\xF3","\xB6","\xBC","\xBF","\xA1","\xC6","\xCA","\xA3","\xD1","\xD3","\xA6","\xAC","\xAF");
    $chars['WINDOWS-1250']  =array("\xB9","\xE6","\xEA","\xB3","\xF1","\xF3","\x9C","\x9F","\xBF","\xA5","\xC6","\xCA","\xA3","\xD1","\xD3","\x8C","\x8F","\xAF");
    $chars['UTF-8']         =array('ą','ć','ę','ł','ń','ó','ś','ź','ż','Ą','Ć','Ę','Ł','Ń','Ó','Ś','Ź','Ż');
    $chars['ENTITIES']      =array('ą','ć','ę','ł','ń','ó','ś','ź','ż','Ą','Ć','Ę','Ł','Ń','Ó','Ś','Ź','Ż');
    if(!isset($chars[$source])) return false;
    if(!isset($chars[$dest])) return false;
        $str = str_replace('a', 'a', $str);
        $str = str_replace('é', 'e', $str);
    return str_replace($chars[$source],$chars[$dest],$str);
}

/**
 * -- This function was moved here from search.inc.* --
 * Replace regional characters with ASCII equivalent, using iconv
 * @param unknown $str
 */
function convert_string($str) {
   if ($str != null) {
        $str = iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", $str);
        if ($str == false) {
            $str = "--- charset error ---";
        }
    }
    return $str;
}
