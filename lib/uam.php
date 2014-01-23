<?php

    /***************************************************************************
        *
        *   This program is free software; you can redistribute it and/or modify
        *   it under the terms of the GNU General Public License as published by
        *   the Free Software Foundation; either version 2 of the License, or
        *   (at your option) any later version.
        *
        ***************************************************************************/

    /****************************************************************************

8 Naglowek pliku = BB 22 D5 3F + 4 bajty ilosc rekordow 1D 00 00 00 = 29 rekordow
Rekordy (kadzy 362 znaki)
8 wspol w uk 1992, 4 bajty Y potem 4 balty X
1 Priorytet punktu (0-4)
64 nazwa punktu
255 Opis
1 Czy widoczny na mapie (0 nie 1 tak)
1 Numer kategorii (99 uzytkownika) ma byc 99
32 Nazwa kategoru usera np Geocaching

    ****************************************************************************/
    require_once("./lib/cs2cs.inc.php");
    global $user, $bUseZip, $sqldebug;

    $uamXY = "{lat}{prio}{name}";
    $uamprio = sprintf('%c',0);
    $uam4b = sprintf('%c',0);
    $uamname = "{name}";
    $uamopis = "{opis}";
    $uamH = sprintf('%c%c%c%c',187,34,213,63);
    $uamnr = "{liczba}";
    $uamB = sprintf('%c%c%s%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c',1,99,Geocaching,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);




//      $sFilebasename = 'ocpl';
//
//  $bUseZip = ($rCount['count'] > 20);
//  $bUseZip = $bUseZip || (isset($_REQUEST['zip']) && ($_REQUEST['zip'] == '1'));
//
/*  if ($bUseZip == true)
    {
        $content = '';
        require_once($rootpath . 'lib/phpzip/ss_zip.class.php');
        $phpzip = new ss_zip('',6);
    }

    // ok, ausgabe starten

    if ($sqldebug == false)
    {
        if ($bUseZip == true)
        {
            header("content-Type: application/zip");
            header("Content-Transfer-Encoding:binary");
            header('Content-Disposition: attachment; filename=' . $sFilebasename . '.zip');
        }
        else
        {
            header("Content-Type: application/uam");
            header("Content-Transfer-Encoding:binary");
            header("Content-Disposition: attachment; filename=" . $sFilebasename . ".uam");

        }
    }

*/
$file = fopen("/var/www/htdocs/tmp/test.bin", "wb");

fwrite($file, $uamH);
    $thisline0 = $uamnr;
//  $lk=dec2hex($lrek,8);
//  $k1=substr($lk,6,2);$k2=substr($lk,4,2);$k3=substr($lk,2,2); $k4=substr($lk,0,2);
//  $k1k2k3k4="$k1$k2$k3$k4";
//  $as=hexstr($k1k2k3k4);
    $thisline0 = mb_ereg_replace('{liczba}',pack('L',100), $thisline0);
fwrite($file, $thisline0);


    mysql_query("SET NAMES 'cp1250'");
    $rs = sql('SELECT `caches`.`wp_oc` `wp_oc`, `caches`.`cache_id` `cacheid`, `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`,`caches`.`name` `name`,`user`.`username` `username`,`caches`.`desc_languages` `desc_languages`, `cache_desc`.`desc` `desc`,`cache_desc`.`desc_html` `html` FROM `caches`, `user`, `cache_desc` WHERE `caches`.`cache_id`=`cache_desc`.`cache_id` AND `caches`.`default_desclang`=`cache_desc`.`language` AND `caches`.`user_id`=`user`.`user_id`');
    while($r = sql_fetch_array($rs))
    {
        $thisline1 = $uamXY;
        $lat =$r['latitude'];
        $lon = $r['longitude'];
        $utm = cs2cs_1992($lat, $lon);
        $l11="";
        $l22="";
settype($uam[0], "integer");
settype($uam[1], "integer");
        $l11=$utm[0];
        $l22=$utm[1];
//      $y4=hexdec(substr($l1,0,2)); $y3=hexdec(substr($l1,2,2)); $y2=hexdec(substr($l1,4,2));$y1=hexdec(substr($l1,6,2));
//      $x4=hexdec(substr($l2,0,2)); $x3=hexdec(substr($l2,2,2)); $x2=hexdec(substr($l2,4,2));$x1=hexdec(substr($l2,6,2));
//      $x="$x1$x2$x3$x4";
//      $y="$y1$y2$y3$y4";
//      $l1122=hexstr($y);
//      $l22=hexstr($x);
//      $thisline1 = mb_ereg_replace('{lat}',$l22,$thisline1);
        $thisline1 = mb_ereg_replace('{lat}',pack('LL',$l11,$l22),$thisline1);
        $thisline1 = mb_ereg_replace('{prio}',$uamprio,$thisline1);
$wp=$r['wp_oc'];;
$n1=$r['name'];
$name="$wp - ";
//$name= substr($n2,0,64);
$length=strlen($name);
if ($length < 64) {
$diff = 64-$length;
     $thisline1 = mb_ereg_replace('{name}',$name,$thisline1);
     fwrite($file,$thisline1);
    for ($i=1; $i<=$diff; $i++) {
    $tmp = sprintf('%c',0);
    fwrite($file,$tmp);
        }
    }
    else {
     $thisline1 = mb_ereg_replace('{name}',$name, $thisline1);
fwrite($file,$thisline1);
    }

$thisline3 = $uamopis;
$username = convert_string($r['username']);
$opis1="Zalozona przez $username - Opis - ";
//if ($r['html'] == 0)
//      {
            $opis2 =strip_tags($r['desc']);
//      }
//      else
//      {
//          $opis2 = html2txt($r['desc']);
//      }
$opis12="$opis1$opis2";
$opiss= substr($opis12, 0, 255);
$length = strlen($opiss);
if ($length < 255) {
$diff = 255-$length;
     $thisline3 = mb_ereg_replace('{opis}', $opiss, $thisline3);
     append_output($thisline3);
    for ($i=1; $i<=$diff; $i++) {
    $tmp = sprintf('%c',0);
    fwrite($file, $tmp);
}
    }
    else {
     $thisline3 = mb_ereg_replace('{opis}', $opiss, $thisline3);
fwrite($file, $thisline3);

    }
fwrite($file, $uamB);

    }
    mysql_free_result($rs);



//  if ($sqldebug == true) sqldbg_end();

    // phpzip versenden
//  if ($bUseZip == true)
//  {
//      $phpzip->add_data($sFilebasename . '.uam', $content);
//      echo $phpzip->save($sFilebasename . '.zip', 'b');
//  }
fclose($file);
    exit;



// Fukcje

    function convert_string($str)
    {
        $newstr = iconv("UTF-8", "WINDOWS-1250", $str);
        if ($newstr == false)
            return $str;
        else
            return $newstr;
    }

    function xmlentities($str)
    {
        $from[0] = '&'; $to[0] = '&amp;';
        $from[1] = '<'; $to[1] = '&lt;';
        $from[2] = '>'; $to[2] = '&gt;';
        $from[3] = '"'; $to[3] = '&quot;';
        $from[4] = '\''; $to[4] = '&apos;';

        for ($i = 0; $i <= 4; $i++)
            $str = mb_ereg_replace($from[$i], $to[$i], $str);

        return $str;
    }

    function append_output($str)
    {
        global $content, $bUseZip, $sqldebug;
        if ($sqldebug == true) return;

        if ($bUseZip == true)
            $content .= $str;
        else
            echo $str;
    }
function hexstr($hex)
{
   $string="";
      for ($i=0;$i<=strlen($hex)-1;$i+=2)
             $string.=chr(hexdec($hex[$i].$hex[$i+1]));
            return $string;
        }

function dec2hex($number, $length) {
  $hexval="";
    while ($number>0) {
        $remainder=$number%16;
        if ($remainder<10)
              $hexval=$remainder.$hexval;
              elseif ($remainder==10)
                    $hexval="a".$hexval;
                    elseif ($remainder==11)
                      $hexval="b".$hexval;
                          elseif ($remainder==12)
                            $hexval="c".$hexval;
                            elseif ($remainder==13)
                                  $hexval="d".$hexval;
                                  elseif ($remainder==14)
                                        $hexval="e".$hexval;
                                        elseif ($remainder==15)
                                          $hexval="f".$hexval;
                                              $number=floor($number/16);
                                            }
                                              while (strlen($hexval)<$length) $hexval="0".$hexval;
                                              //this is just to add zero's at the beginning to make hexval a certain length
                                                return $hexval;
                    }

    function hex2ascii($hex){
    $ascii='';
    $hex=str_replace(" ", "", $hex);
    for($i=0; $i<strlen($hex); $i=$i+2) {
    $ascii.=chr(hexdec(substr($hex, $i, 2)));
    }
    return($ascii);
    }
    function html2txt($html)
    {
//      $str = mb_ereg_replace("\r\n", '', $html);
//      $str = mb_ereg_replace("\n", '', $str);
        $str = mb_ereg_replace('<br />', "\n", $str);
        $str = strip_tags($str);
        return $str;
    }

    function lf2crlf($str)
    {
        return mb_ereg_replace("\r\r\n" ,"\r\n" , mb_ereg_replace("\n" ,"\r\n" , $str));
    }

?>
