<?php

    require_once("./lib/common.inc.php");

    function makeUrl($url){

        $url = str_replace(array('ą','Ą','ę','Ę','Ż','ż','Ź','ź','Ć','ć','Ó','ó','ń','Ń','ł','Ł', 'ś', 'Ś', ' '),
        array('a','A','e','E','Z','z','Z','z','C','c','O','o','n','N','l','L', 's', 'S', '_'),$url);
        return $url;

    }

    function wytnij($tab,$start,$end){

    $temp=Array();

    for($i=$start;$i<$end;$i++){
        if(!empty($tab[$i]))
        $temp[]=$tab[$i];
    }
    return $temp;

    }

    function stronicowanie($page,$address,$znalezione,$ile,$url){

        global $tpl;

        $na_stronie=10;

        if($ile<=$na_stronie)
            $znalezione =wytnij($znalezione,0,$na_stronie);
        else{
            if(!isset($page)){

                $znalezione =wytnij($znalezione,0,$na_stronie);
                $next_page='2';
            }elseif(isset($page) && !empty($page) && preg_match("/^\d+$/", $page)){
                $start=(($page-1)*$na_stronie);
                $end=$start+$na_stronie;
                $znalezione = wytnij($znalezione,$start,$end);

                if(empty($znalezione)){
                    header('Location: '.$url.'&page=1');
                    exit;
                }else{
                    if(((($page-1)*$na_stronie)+$na_stronie)<$ile)
                        $next_page=$page+1;
                    $prev_page=$page-1;
                }
            }
        }

        $tpl -> assign ('action',$action);
        $tpl -> assign ('url',$url);
        $tpl -> assign ('next_page',$next_page);
        $tpl -> assign ('prev_page',$prev_page);
        $tpl -> assign("address",$address);
        $tpl -> assign("znalezione", $znalezione);
        $tpl -> assign("ile", $ile);

        $max=ceil($ile/$na_stronie);

        if ($max=='0')
            $max='1';

        $tpl -> assign ('max',$max);

        $tpl -> display('tpl/find2.tpl');
    }

    if(isset($_GET['ns']) && isset($_GET['ew']) && isset($_GET['radius']) && isset($_GET['Nstopien']) && isset($_GET['Nminuty']) && isset($_GET['Estopien']) && isset($_GET['Eminuty'])){
        if( !empty($_GET['ns']) && !empty($_GET['ew']) && !empty($_GET['radius'])
        && !empty($_GET['Nstopien']) && !empty($_GET['Nminuty']) && !empty($_GET['Estopien']) && !empty($_GET['Eminuty'])
        && ($_GET['ns']=='N' || $_GET['ns']=='S') && ($_GET['ew'] == 'E' || $_GET['ew'] == 'W')
        && preg_match("/^\d+$/", $_GET['radius']) && $_GET['radius']>=1 && $_GET['radius']<=25
        && preg_match("/^\d+$/", $_GET['Nstopien']) && $_GET['Nstopien']>=0 && $_GET['Nstopien']<=90
        && preg_match("/^\d+$/", $_GET['Estopien']) && $_GET['Estopien']>=0 && $_GET['Estopien']<=180
        && preg_match("/^\d{1,2}\.\d{1,3}$/",$_GET['Eminuty'])  && preg_match("/^\d{1,2}\.\d{1,3}$/",$_GET['Nminuty'])
        && $_GET['Nminuty']>=0 && $_GET['Nminuty']<60 && $_GET['Eminuty']>=0 && $_GET['Eminuty']<60){

            db_connect();

            $kord1=zamiana($_GET['Nstopien'],$_GET['Nminuty']);
                if($_GET['ns']=='S') $kord1 = "-".$kord1;

            $kord2=zamiana($_GET['Estopien'],$_GET['Eminuty']);
                if($_GET['ew'] == 'W') $kord2 = "-".$kord2;

            $jsonurl = "$absolute_server_URI/okapi/services/caches/search/nearest?&center=".$kord1."|".$kord2."&status=Available&radius=".$_GET['radius']."&consumer_key=HpLvDvvjmG3HkeX8RsgU&limit=500 ";

            $input = file_get_contents($jsonurl);
            $output = json_decode($input,true);

            $znalezione = array();
            $lista = array();

            $i=0;

            while( list($klucz, $wartosc) = each($output['results']) ){

                $query = "select status,cache_id,name, score, latitude, longitude, user_id, type from caches where wp_oc = '".$wartosc."'";
                $wynik = db_query ($query);
                $wiersz=mysql_fetch_assoc($wynik);

                //if($wiersz['wp_oc']=='OP210B') continue;

                $ilat1=Deg2Rad(0.50 + $kord1 * 360000.0);
                $ilon1=Deg2Rad(0.50 + $kord2 * 360000.0);
                $ilat2=Deg2Rad(0.50 + $wiersz['latitude'] * 360000.0);
                $ilon2=Deg2Rad(0.50 + $wiersz['longitude'] * 360000.0);

                $dist=round(acos((sin($kord1)*sin($wiersz['latitude']))+(cos($kord1)*cos($wiersz['latitude'])*cos(abs($kord2-$wiersz['longitude']))))*111.19,1);

                $lat1=$kord1;
                $lon1=$kord2;
                $lat2=$wiersz['latitude'];
                $lon2=$wiersz['longitude'];

                $result = 0.0;

                $ilat1 = (0.50 + $lat1 * 360000.0);
                $ilat2 = (0.50 + $lat2 * 360000.0);
                $ilon1 = (0.50 + $lon1 * 360000.0);
                $ilon2 = (0.50 + $lon2 * 360000.0);

                $lat1 = Deg2Rad($lat1);
                $lon1 = Deg2Rad($lon1);
                $lat2 = Deg2Rad($lat2);
                $lon2 = Deg2Rad($lon2);

                if (($ilat1 == $ilat2) && ($ilon1 == $ilon2)){
                }else if ($ilon1 == $ilon2){
                    if ($ilat1 > $ilat2)
                        $result = 180.0;
                }else{
                    $c = acos(sin($lat2)*sin($lat1) + cos($lat2)*cos($lat1)*cos(($lon2-$lon1)));
                    $A = asin(cos($lat2)*sin(($lon2-$lon1))/sin($c));
                    $result = Rad2Deg($A);

                    if (($ilat2 > $ilat1) && ($ilon2 > $ilon1)){
                    }else if (($ilat2 < $ilat1) && ($ilon2 < $ilon1)){
                        $result = 180.0 - $result;
                    }else if (($ilat2 < $ilat1) && ($ilon2 > $ilon1)){
                        $result = 180.0 - $result;
                    }else if (($ilat2 > $ilat1) && ($ilon2 < $ilon1)){
                        $result += 360.0;
                    }
                }

                $kier = round($result,1);

                if(($kier>=337.5 && $kier<360) || ($kier>=0 && $kier<22.5))
                    $kier='N';
                if($kier>=22.5 && $kier<67.5)
                    $kier='NE';
                if($kier>=67.5 && $kier<112.5)
                    $kier='E';
                if($kier>=112.5 && $kier<157.5)
                    $kier='SE';
                if($kier>=157.5 && $kier<202.5)
                    $kier='S';
                if($kier>=202.5 && $kier<247.5)
                    $kier='SW';
                if($kier>=247.5 && $kier<292.5)
                    $kier='W';
                if($kier>=292.5 && $kier<337.5)
                    $kier='NW';

                $query="select ".$lang." from cache_type where id = '".$wiersz['type']."';";
                $wynik2=db_query($query);
                $wiersz2=mysql_fetch_row($wynik2);
                $rekord['typetext']=$wiersz2[0];

                if(isset($_SESSION['user_id'])) {
                    $query2 = "select 1 from cache_logs where user_id = '".$_SESSION['user_id']."' and type = '1' and deleted='0' and cache_id ='".$wiersz['cache_id']."';";
                    $wynik2 = db_query($query2);
                    $if_found=mysql_fetch_row($wynik2);

                    if($if_found[0]!='1'){
                        $query2 = "select 2 from cache_logs where user_id = '".$_SESSION['user_id']."' and type = '2' and deleted='0' and cache_id ='".$wiersz['cache_id']."';";
                        $wynik2 = db_query($query2);
                        $if_found=mysql_fetch_row($wynik2);
                    }

                    $if_found=$if_found[0];
                }

                if(isset($_GET['skip_mine']) && isset($_SESSION['user_id'])) {
                    if($wiersz['user_id']==$_SESSION['user_id']) continue;
                }

                if(isset($_GET['skip_found']) && isset($_SESSION['user_id'])) {
                    if($if_found==1) continue;
                }

                if(isset($_GET['skip_ignored']) && isset($_SESSION['user_id'])) {
                    $query9="select 1 from cache_ignore where user_id='".$_SESSION['user_id']."' and cache_id='".$wiersz['cache_id']."'";
                    $wynik9 = db_query($query9);
                    $if_ignored=mysql_fetch_row($wynik9);
                    $if_ignored=$if_ignored[0];
                    if ($if_ignored==1) continue;
                }

                if(isset($_GET['skip_inactive'])) {
                    if($wiersz['status']>1) continue;
                }

                $rekord['user_id']=$wiersz['user_id'];
                $rekord['name']=$wiersz['name'];
                $rekord['status']=$wiersz['status'];
                $rekord['score']=score2ratingnum($wiersz['score']);
                $rekord['latitude']=$wiersz['latitude'];
                $rekord['longitude']=$wiersz['longitude'];
                $rekord['wp_oc']=$wartosc;
                $rekord['N'] = cords($rekord['latitude']);
                $rekord['E'] = cords($rekord['longitude']);
                $rekord['distance']=$dist;
                $rekord['kier']=$kier;
                $rekord['if_found'] = $if_found;
                $query="select username from user where user_id = '".$rekord['user_id']."';";
                $wynik=db_query($query);
                $wiersz=mysql_fetch_assoc($wynik);

                $rekord['username']=$wiersz['username'];

                $znalezione [] = $rekord;
                $lista[]=$rekord['wp_oc'];
                $i++;

            }

            $url=$_SERVER['REQUEST_URI'];

            $tpl -> assign ('lista',$lista);
            stronicowanie($_GET['page'],'viewcache',$znalezione,$i,$url);

            exit;

        }else
            $tpl -> assign('error',1);


    }elseif(isset($_POST['city']) && isset($_POST['radius']) ){
        if(!empty($_POST['city']) && !empty($_POST['radius']) && preg_match("/^\d+$/", $_POST['radius']) && $_POST['radius']>=1 && $_POST['radius']<=25){
            $city=makeUrl($_POST['city']);

            $jsonurl = "http://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=".$city;

            $input = file_get_contents($jsonurl);
            $output = json_decode($input,true);
            $output=$output[results][0][geometry][location];


            $lat_h=intval($output[lat]);
            $lat_m=cords2($output[lat]);
            $lon_h=intval($output[lng]);
            $lon_m=cords2($output[lng]);

            if($lat_h>0)
                $ns='N';
            else{
                $ns='S';
                $lat_h=abs($lat_h);
            }

            if($lon_h>0)
                $ew='E';
            else{
                $ew='W';
                $lon_h=abs($lon_h);
            }

            $link="./near.php?ns=".$ns."&Nstopien=".$lat_h."&Nminuty=".$lat_m."&ew=".$ew."&Estopien=".$lon_h."&Eminuty=".$lon_m."&radius=".$_POST['radius'];

            if(isset($_POST['skip_mine'])) {
                $link.="&skip_mine=on";
            }

            if(isset($_POST['skip_found'])) {
                $link.="&skip_found=on";
            }

            if(isset($_POST['skip_ignored'])) {
                $link.="&skip_ignored=on";
            }

            if(isset($_POST['skip_inactive'])) {
                $link.="&skip_inactive=on";
            }

            header("Location: ".$link);
            exit;

        }else
            $tpl -> assign('error',2);
    }

    $tpl -> display('./tpl/near.tpl');

?>
