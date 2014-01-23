<?php

    require_once("./lib/common.inc.php");

    function find_news($start,$end){

        global $tpl;
        global $lang;
        global $znalezione;

        $wp=mysql_real_escape_string($_GET['wp']);

        $query = "select id,type,user_id,date,text,deleted from cache_logs where cache_id = (select cache_id from caches where wp_oc = '".$wp."') order by date desc limit ".$start.",".$end;
        $wynik = db_query ($query);

        $query="select name from caches where cache_id = (select cache_id from caches where wp_oc = '".$wp."');";
        $wynik2=db_query($query);
        $caches=mysql_fetch_row($wynik2);

        $tpl -> assign("name", $caches[0]);

        $znalezione = array();

        while($logs=mysql_fetch_assoc($wynik)){

            if($logs['deleted']==0) {

                $query="select username from user where user_id = '".$logs['user_id']."';";
                $wynik3=db_query($query);
                $user=mysql_fetch_row($wynik3);

                $logs2['id']=$logs['id'];
                $logs2['user_id']=$logs['user_id'];
                $logs2['newtype']=$logs['type'];
                $logs2['newdate']=date('j.m.Y', strtotime($logs['date']));
                $logs2['username']=$user[0];
                $logs2['newtext']=html2log($logs['text']);

                $znalezione [] = $logs2;

            }
        }

        $tpl -> assign("wp_oc", $wp);
        $tpl -> assign("logs", $znalezione);

    }

    if(isSet($_GET['wp']) && !empty($_GET['wp']) && $_GET['wp']!="OP"){

        db_connect();

        $wp=mysql_real_escape_string($_GET['wp']);

        $na_stronie=10;

        $query="select count(*) from cache_logs where cache_id = (select cache_id from caches where wp_oc = '".$wp."') and deleted='0' order by date desc;";
        $wynik=db_query($query);
        $ile=mysql_fetch_row($wynik);
        $ile=$ile[0];

        $url=$_SERVER['REQUEST_URI'];

        $tpl -> assign("ile", $ile);

        $max=ceil($ile/$na_stronie);

        if ($max=='0')
            $max='1';

        $tpl -> assign ('max',$max);

        require_once("./lib/paging.inc.php");

        $tpl -> assign ('next_page',$next_page);
        $tpl -> assign ('prev_page',$prev_page);

    }else {
        header('Location: ./index.php');
        exit;
    }

    $tpl -> display('tpl/logs.tpl');

?>