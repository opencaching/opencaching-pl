<?php

    require_once("./lib/common.inc.php");

    db_connect();

    if(isset($_GET['more']) && ($_GET['more']=='1' || $_GET['more']=='2' || $_GET['more']=='3' || $_GET['more']=='4')){
        switch($_GET['more']){
            case '1':
                $query = "select name, wp_oc, founds from caches where type!='4' and type!='5' order by founds desc limit 50 ";
                $wynik = db_query ($query);
                $znalezione = Array();
                while($odp2=mysql_fetch_assoc($wynik)){
                    $odp['name']=$odp2['name'];
                    $odp['wp_oc']=$odp2['wp_oc'];
                    $odp['founds']=$odp2['founds'];
                    $znalezione[]=$odp;
                }
                $tpl -> assign ('caches_found',$znalezione);
            break;
            case '2':
                $query = "select name,wp_oc,ile from caches inner join (select cache_id as yy,ile from(select cache_id,count(*) as ile from cache_rating group by cache_id)tab order by ile desc limit 50)a on yy=caches.cache_id where type!='4' and type!='5'";
                $wynik = db_query ($query);
                $znalezione = Array();
                while($odp2=mysql_fetch_assoc($wynik)){
                    $odp['name']=$odp2['name'];
                    $odp['ile']=$odp2['ile'];
                    $odp['wp_oc']=$odp2['wp_oc'];
                    $znalezione[]=$odp;
                }
                $tpl -> assign ('caches_rat',$znalezione);
            break;
            case '3':
                $query = "select username,user_id,founds_count from user order by founds_count desc limit 50";
                $wynik = db_query ($query);
                $znalezione = Array();
                while($odp2=mysql_fetch_assoc($wynik)){
                    $odp['username']=$odp2['username'];
                    $odp['user_id']=$odp2['user_id'];
                    $odp['founds_count']=$odp2['founds_count'];
                    $znalezione[]=$odp;
                }
                $tpl -> assign ('user_found',$znalezione);
            break;
            case '4':
                $query = "select username,user_id,hidden_count from user order by hidden_count desc limit 50";
                $wynik = db_query ($query);
                $znalezione = Array();
                while($odp2=mysql_fetch_assoc($wynik)){
                    $odp['username']=$odp2['username'];
                    $odp['user_id']=$odp2['user_id'];
                    $odp['hidden_count']=$odp2['hidden_count'];
                    $znalezione[]=$odp;
                }
                $tpl -> assign ('user_hidden',$znalezione);
            break;
        }

        $tpl -> display('tpl/stats2.tpl');
    }
    else{
        $query = "select count(*) from caches where (status=1 OR status=2 OR status=3)";
        $wynik = db_query ($query);
        $odp=mysql_fetch_row($wynik);
        $tpl -> assign ('ile',$odp[0]);

        $query = "select count(*) from caches where status = 1";
        $wynik = db_query ($query);
        $odp=mysql_fetch_row($wynik);
        $tpl -> assign ('ile_akt',$odp[0]);

        $query = "select count(*) from cache_logs where (`type`=1 OR `type`=2) AND `deleted`=0";
        $wynik = db_query ($query);
        $odp=mysql_fetch_row($wynik);
        $tpl -> assign ('founds',$odp[0]);

        $query = "SELECT COUNT(*) AS `users` FROM (SELECT DISTINCT `user_id` FROM `cache_logs` WHERE (`type`=1 OR `type`=2) AND `deleted`=0 UNION DISTINCT SELECT DISTINCT `user_id` FROM `caches`) AS `t`";
        $wynik = db_query ($query);
        $odp=mysql_fetch_row($wynik);
        $tpl -> assign ('user',$odp[0]);

        $query = "select name, wp_oc, founds from caches where type!='4' and type!='5' order by founds desc limit 5 ";
        $wynik = db_query ($query);
        $znalezione = Array();
        while($odp2=mysql_fetch_assoc($wynik)){
            $odp['name']=$odp2['name'];
            $odp['wp_oc']=$odp2['wp_oc'];
            $odp['founds']=$odp2['founds'];
            $znalezione[]=$odp;
        }
        $tpl -> assign ('caches_found',$znalezione);

/*
$query = "select name,wp_oc,ile
        from caches inner join (select cache_id as yy,
        ile from(select cache_id,count(*) as ile from cache_rating group by cache_id)tab order by ile desc limit 5)a on yy=caches.cache_id where type!='4' and type!='5'";
*/
    $query = "SELECT `caches`.`wp_oc` `wp_oc`,
                `caches`.`name` `name`,
                count(`cache_rating`.`cache_id`) as `ile`
            FROM `caches`, `cache_rating`
            WHERE `cache_rating`.`cache_id`=`caches`.`cache_id`
              AND `status`=1  AND `type` <> 6
            GROUP BY `caches`.`name`, `caches`.`cache_id`
            ORDER BY `ile` DESC, `caches`.`name` ASC
            LIMIT 5";
        $wynik = db_query ($query);
        $znalezione = Array();
        while($odp2=mysql_fetch_assoc($wynik)){
            $odp['name']=$odp2['name'];
            $odp['ile']=$odp2['ile'];
            $odp['wp_oc']=$odp2['wp_oc'];
            $znalezione[]=$odp;
        }
        $tpl -> assign ('caches_rat',$znalezione);

        $query = "select username,user_id,founds_count from user order by founds_count desc limit 5";
        $wynik = db_query ($query);
        $znalezione = Array();
        while($odp2=mysql_fetch_assoc($wynik)){
            $odp['username']=$odp2['username'];
            $odp['user_id']=$odp2['user_id'];
            $odp['founds_count']=$odp2['founds_count'];
            $znalezione[]=$odp;
        }
        $tpl -> assign ('user_found',$znalezione);

        $query = "select username,user_id,hidden_count from user order by hidden_count desc limit 5";
        $wynik = db_query ($query);
        $znalezione = Array();
        while($odp2=mysql_fetch_assoc($wynik)){
            $odp['username']=$odp2['username'];
            $odp['user_id']=$odp2['user_id'];
            $odp['hidden_count']=$odp2['hidden_count'];
            $znalezione[]=$odp;
        }
        $tpl -> assign ('user_hidden',$znalezione);
        $tpl -> display('tpl/stats.tpl');
    }

?>