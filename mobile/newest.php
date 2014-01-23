<?php

    require_once("./lib/common.inc.php");

    db_connect();

    $query = "select date_hidden, name,  latitude, longitude, wp_oc, user_id, type from caches where status='1' and date_hidden<now() order by date_hidden desc limit 10";
    $wynik = db_query($query);
    $ile = mysql_num_rows($wynik);
    $tpl -> assign("ile",$ile);

    $znalezione = array();
    $lista = array();
    $tpl->assign("address","viewcache");

    while($rekord = mysql_fetch_assoc($wynik)){

        $query="select username from user where user_id = ".$rekord['user_id'].";";
        $wynik2=db_query($query);
        $wiersz=mysql_fetch_assoc($wynik2);

        $query="select ".$lang." from cache_type where id = ".$rekord['type'].";";
        $wynik2=db_query($query);
        $wiersz2=mysql_fetch_row($wynik2);

        $rekord['username']=$wiersz['username'];
        $rekord['date_hidden']=date("d-m-Y",strtotime($rekord['date_hidden']));
        $rekord['N'] = cords($rekord['latitude']);
        $rekord['E'] = cords($rekord['longitude']);
        $rekord['typetext']=$wiersz2[0];

        $lista[]=$rekord['wp_oc'];
        $znalezione [] = $rekord;

    }

    $tpl -> assign ('lista',$lista);
    $tpl -> assign("max", 1);
    $tpl -> assign("znalezione", $znalezione);
    $tpl -> display('./tpl/find2.tpl');

?>