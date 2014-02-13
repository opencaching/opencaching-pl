<?php

    require_once("./lib/common.inc.php");

    if(isSet($_GET['wp']) && !empty($_GET['wp']) && $_GET['wp']!="OP"){

        db_connect();

        $wp=mysql_real_escape_string($_GET['wp']);

        $query = "select votes,cache_id,topratings,user_id,type,size,status,cache_id,name,longitude,latitude,date_hidden,wp_oc,score,founds,notfounds,notes,picturescount";
        $query .= " from caches where wp_oc = '".$wp."';";
        $wynik = db_query ($query);
        $caches=mysql_fetch_assoc($wynik);

    // dodałem sprawdzanie statusu
        if(empty($caches) || $caches['status'] == 4 || $caches['status'] == 5 || $caches['status'] == 6)

            $tpl->assign("error","1");

        else{

            $query="select username from user where user_id = ".$caches['user_id'].";";
            $wynik=db_query($query);
            $user=mysql_fetch_row($wynik);

            $query="select cache_desc.desc,hint,short_desc from cache_desc where cache_id ='".$caches['cache_id'].'\'';
            $query.=' order by field(`language`, \'pl\', \'en\', \'de\', \'nl\') ASC;';

            $wynik = db_query ($query);

            $i=0;

            while($rekord = mysql_fetch_assoc($wynik)){

                if($i>0)
                    $cache_desc['desc'].="<br/><br/>";
                $cache_desc['desc'].=$rekord['desc'];
                if($i>0)
                    $cache_desc['short_desc'].="<br/>";
                $cache_desc['short_desc'].=$rekord['short_desc'];
                if($i>0)
                    $cache_desc['hint'].="\\n\\n";
                $cache_desc['hint'].=$rekord['hint'];

                $i++;

            }

            $query="select attrib_id from caches_attributes where cache_id ='".$caches['cache_id'].'\';';
            $cache_attributes = db_query ($query);

            $query="select ".$lang." from cache_type where id =".$caches['type'].';';
            $wynik = db_query ($query);
            $cache_type=mysql_fetch_row($wynik);

            $query="select ".$lang." from cache_size where id =".$caches['size'].';';
            $wynik = db_query ($query);
            $cache_size=mysql_fetch_row($wynik);

            $query="select ".$lang." from cache_status where id =".$caches['status'].';';
            $wynik = db_query ($query);
            $cache_status=mysql_fetch_row($wynik);

            if(isset($_SESSION['user_id'])) {
                $query2 = "select 1 from cache_logs where user_id = '".$_SESSION['user_id']."' and type = '1' and deleted='0' and cache_id ='".$caches['cache_id']."';";
                $wynik2 = db_query($query2);
                $if_found=mysql_fetch_row($wynik2);

                if($if_found[0]!='1'){$query2 = "select 2 from cache_logs where user_id = '".$_SESSION['user_id']."' and type = '2' and deleted='0' and cache_id ='".$caches['cache_id']."';";
                $wynik2 = db_query($query2);
                $if_found=mysql_fetch_row($wynik2); }

                $if_found=$if_found[0];
            }

            $cache_info = array();

            $cache_info['if_found']=$if_found;

            if ($caches['votes']>3)
                $cache_info['score']=score2ratingnum($caches['score']);
            else
                $cache_info['score']=5;

            if(isset($_SESSION['user_id'])){
                $query3 = "select id from cache_watches where user_id = '".$_SESSION['user_id']."' and cache_id ='".$caches['cache_id']."';";
                $wynik3 = db_query($query3);
                $watched=mysql_fetch_row($wynik3);
                $watched = $watched[0];
                if (!empty($watched))
                    $cache_info['watched']=$watched ;
                else
                    $cache_info['watched']=-1;
            }

            $cache_info['cache_id']=$caches['cache_id'];
            $cache_info['name']=$caches['name'];
            $cache_info['short_desc']=$cache_desc['short_desc'];
            $cache_info['longitude']=number_format ( $caches['longitude'], 5 );
            $cache_info['latitude']=number_format ( $caches['latitude'], 5 );
            $cache_info['N']=cords($caches['latitude']);
            $cache_info['E']=cords($caches['longitude']);
            $cache_info['type']=$cache_type[0];
            $cache_info['size']=$cache_size[0];
            $cache_info['status2']=$caches['status'];
            $cache_info['status']=$cache_status[0];
            $cache_info['hidden_date']=date('j.m.Y', strtotime($caches['date_hidden']));
            $cache_info['wp_oc']=$caches['wp_oc'];
            $cache_info['owner']=$user[0];
            $cache_info['user_id']=$caches['user_id'];
            $cache_info['founds']=$caches['founds'];
            $cache_info['notfounds']=$caches['notfounds'];
            $cache_info['notes']=$caches['notes'];
            $cache_info['desc']=html2desc($cache_desc['desc']);
            $cache_info['hint']=html2hint($cache_desc['hint']);

            $cache_info['picturescount']=$caches['picturescount'];
            $cache_info['topratings']=$caches['topratings'];

            $tpl -> assign("cache", $cache_info);

            if($caches['picturescount']>0){

                $query = "select url, title, spoiler from pictures where object_id = '".$caches['cache_id']."' and display=1 and object_type=2;";
                $wynik = db_query ($query);

                $znalezione = array();

                while($rekord = mysql_fetch_assoc($wynik))
                    $photos[] = $rekord;

                $tpl -> assign("photos_list", $photos);

            }

            if(!empty($cache_attributes)){

                $attr_text="";

                while($rekord = mysql_fetch_assoc($cache_attributes)){

                    $query="select text_long from cache_attrib where id ='".$rekord['attrib_id']."' and language = '".$lang."';";
                    $wynik = db_query ($query);
                    $attr=mysql_fetch_row($wynik);
                    $attr_text .= "\\n".$attr[0]."\\n";

                }

                $tpl -> assign("attr_text", $attr_text);
            }

            $gk="";

//          $query="select name,distancetravelled from gk_item where latitude ='".$caches['latitude']."' and longitude='".$caches['longitude']."';";
$query="SELECT gk_item.id, name, distancetravelled FROM gk_item INNER JOIN gk_item_waypoint ON (gk_item.id = gk_item_waypoint.id) WHERE gk_item_waypoint.wp = '".$caches['wp_oc']."' AND stateid<>1 AND stateid<>4 AND stateid <>5 AND typeid<>2 AND missing=0";
            $wynik = db_query ($query);
            //print mysql_num_rows($wynik);
            while($rekord = mysql_fetch_assoc($wynik))      {
                //print $rekord['name'];
                $gk.=$rekord['name']." - ".$rekord['distancetravelled']." km\\n\\n";

                }

            $tpl -> assign("gk", $gk);

        }
    }
    else{
        header('Location: ./index.php');
        exit;
    }

    $tpl -> display('tpl/viewcache.tpl');

?>
