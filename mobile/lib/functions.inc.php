<?php

    function db_connect(){
        global $dbserver, $dbusername, $dbpasswd, $dbname;
        @mysql_connect($dbserver,$dbusername,$dbpasswd) or
            die('MySQL error #1');
        @mysql_select_db($dbname) or
            die('MySQL error #2');

        //uwaga!
        @mysql_query('SET CHARACTER SET utf8') or
            die('MySQL error #3');

        //uwaga!
        @mysql_query('SET collation_connection = utf8_general_ci') or
            die('MySQL error #4');
    }

    function db_query($query){
        $wynik = mysql_query($query)
        or die('MySQL query error');
        return $wynik;
    }

    function cords2($cord){
        $Ntemp = number_format ($cord, 5 );
        $N = (int) $Ntemp.'?';
        $Ntemp-=(int) $Ntemp;
        $Ntemp=$Ntemp/0.0166666;
        $N=number_format( $Ntemp, 3);
        return $N;
    }

    // zmienia format wyświetlenia 00.0000000 -> 00 00.000
    function cords($cord){
        $Ntemp = number_format ($cord, 5 );
        $N = (int) $Ntemp.'° ';
        $Ntemp-=(int) $Ntemp;
        $Ntemp=$Ntemp/0.0166666;
        $N.=number_format( $Ntemp, 3)."'";
        return $N;
    }

    // zmienia format wyświetlenia 00 00.000 -> 00.0000000
    function zamiana($Nstopien,$Nminuty){
        $Ntemp=(int)($Nminuty*1666.66);
        if($Nminuty<10)
            $N=$Nstopien.".0".$Ntemp;
        else
            $N=$Nstopien.".".$Ntemp;
        return $N;
    }

    // nowy system oceniania
    function score2ratingnum($score){
        if($score >= 2.2)
            return 4;
        else if($score >= 1.4)
            return 3;
        else if($score >= 0.1)
            return 2;
        else if($score >= -1.0)
            return 1;
        else
            return 0;
    }


    // dostosowanie html'a do wersji mobilnej
    function html2desc($desc){
        $tempdesc=$desc;
        $tempdesc=preg_replace('/<\/p>(\n|\s)*<p>/','<br/><br/>',$tempdesc);
        $tempdesc=str_replace('<p>','',$tempdesc);
        $tempdesc=str_replace('</p>','',$tempdesc);
        return $tempdesc;
    }

    function html2hint($hint){
        $temphint=$hint;
        $temphint=str_replace("'","`",$temphint);
        $temphint=str_replace("\"","``",$temphint);
        $temphint=str_replace("&quot;","``",$temphint);
        return $temphint;
    }

    function html2log($log){
        $templog=$log;
        $templog=preg_replace('/<\/p>(\n|\s)*<p>/',' ',$templog);
        $templog=strip_tags($templog);
        return $templog;
    }

    // helper dla gpx'ów
    function gpxhelper($text){
        $text=str_replace("&nbsp;"," ",$text);
        $text=str_replace("&quot;","\"",$text);
        $text=str_replace("&oacute;","&",$text);
        $text=str_replace("&","&amp;",$text);
        $text=strip_tags($text);
        $text=preg_replace('/[[:cntrl:]]/', '', $text);
        return $text;
    }

?>