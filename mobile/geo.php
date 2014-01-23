<?php

    require_once("./lib/common.inc.php");

    function check_wp($wpts){

        foreach ($wpts as &$wp) {
            if(!preg_match("/^OP((\d)|([A-Z])){4}$/", $wp)) return false;
        }
        return true;

    }

    if(isset($_GET['wp']) && !empty($_GET['wp']) && isset($_GET['output']) && !empty($_GET['output'])){

        db_connect();

        $wpts=explode("|",mysql_real_escape_string($_GET['wp']));
        $output=mysql_real_escape_string($_GET['output']);

        if(preg_match("/^((gpx)|(gpxgc)|(loc)|(wpt)|(uam)){1}$/", $output)){
            if(check_wp($wpts)){

                $znalezione = array();

                $i=0;

                foreach ($wpts as &$wp) {

                    $query="select difficulty,terrain,size,status,user_id,type,cache_id,date_hidden,name,latitude,longitude from caches where wp_oc='".$wp."'"; //print $query;
                    $wynik=db_query($query);
                    $wiersz=mysql_fetch_assoc($wynik);

                    $query="select user_id,username from user where user_id=".$wiersz['user_id'];
                    $wynik=db_query($query);
                    $wiersz2=mysql_fetch_assoc($wynik);

                    $query="select en from cache_type where id=".$wiersz['type'];
                    $wynik=db_query($query);
                    $wiersz3=mysql_fetch_assoc($wynik);

                    $query="select en from cache_status where id=".$wiersz['status'];
                    $wynik=db_query($query);
                    $wiersz4=mysql_fetch_assoc($wynik);

                    $query="select en from cache_size where id=".$wiersz['size'];
                    $wynik=db_query($query);
                    $wiersz5=mysql_fetch_assoc($wynik);

                    $query="select short_desc,cache_desc.desc from cache_desc where cache_id=".$wiersz['cache_id'];
                    $wynik=db_query($query);
                    $wiersz6=mysql_fetch_assoc($wynik);

                    $query="select attrib_id from caches_attributes where cache_id=".$wiersz['cache_id'];
                    $wynik=db_query($query);

                    while($rekord = mysql_fetch_assoc($wynik)){

                        $query="select text_long from cache_attrib where id ='".$rekord['attrib_id']."' and language = '".$lang."';";
                        $wynik2 = db_query ($query);
                        $attr=mysql_fetch_row($wynik2);
                        $attr_text .= $attr[0]." | ";
                        $attr_text = gpxhelper($attr_text);

                    }

                    $logs=array();
                    $query="select cache_logs.text,cache_logs.id,cache_logs.date,user.username,log_types.en from (cache_logs inner join user on cache_logs.user_id = user.user_id) inner join log_types on log_types.id=cache_logs.type where cache_logs.cache_id=".$wiersz['cache_id']." order by cache_logs.id desc";
                    $wynik=db_query($query);

                    while($rekord = mysql_fetch_assoc($wynik)){

                        $rekord2['id']=$rekord['id'];
                        $rekord2['date']=date("Y-m-d",strtotime($rekord['date']));
                        $rekord2['time']=date("H:i:s",strtotime($rekord['date']));
                        $rekord2['type']=gpxhelper($rekord['en']);
                        $rekord2['text']=gpxhelper($rekord['text']);
                        $rekord2['username']=gpxhelper($rekord['username']);

                        $logs[]=$rekord2;
                    }

                    $geokrets=array();
                    $query="select name from gk_item where latitude ='".$wiersz['latitude']."' and longitude='".$wiersz['longitude']."';";
                    $wynik = db_query ($query);

                    while($rekord = mysql_fetch_assoc($wynik)){

                        $rekord2['name']=gpxhelper($rekord['name']);
                        $geokrets[]=$rekord2;

                    }

                    $rekord['owner']=gpxhelper($wiersz2['username']);
                    $rekord['user_id']=gpxhelper($wiersz2['user_id']);
                    $rekord['name']=gpxhelper($wiersz['name']);
                    $rekord['difficulty']=gpxhelper($wiersz['difficulty']);
                    $rekord['terrain']=gpxhelper($wiersz['terrain']);
                    $rekord['type']=gpxhelper($wiersz3['en']);
                    $rekord['size']=gpxhelper($wiersz5['en']);
                    $rekord['status']=gpxhelper($wiersz4['en']);
                    $rekord['cache_id']=$wiersz['cache_id'];
                    $rekord['latitude']=$wiersz['latitude'];
                    $rekord['longitude']=$wiersz['longitude'];
                    $rekord['date_hidden']=date("Y-m-d",strtotime($wiersz['date_hidden']));
                    $rekord['wp_oc']=$wp;
                    $rekord['desc']=gpxhelper($wiersz6['desc']);
                    $rekord['short_desc']=gpxhelper($wiersz6['short_desc']);
                    $rekord['hint']=gpxhelper($wiersz6['hint']);
                    $rekord['attr']=$attr_text;
                    $rekord['logs']=$logs;
                    $rekord['geokrets']=$geokrets;

                    $znalezione[]=$rekord;
                    $i++;

                }

                $tpl -> assign ('date',date("Y-m-d"));
                $tpl -> assign ('time',date("H:i:s"));
                $tpl -> assign ('znalezione',$znalezione);

                if($i==1)
                    $filename=$wpts[0].".";
                else
                    $filename='results.';

                if ($_GET['output']=='gpxgc')
                    $filename.='gpx';
                else
                    $filename.=$_GET['output'];

                header("Content-disposition: attachment; filename=".$filename);
                header("Content-Type: application/force-download");
                header("Content-Transfer-Encoding: binary");

                switch($_GET['output']){
                    case 'gpx':
                        $tpl -> display('./lib/formatters/gpx.tpl');
                    break;
                    case 'gpxgc':
                        $tpl -> display('./lib/formatters/gpxgc.tpl');
                    break;
                    case 'loc':
                        $tpl -> display('./lib/formatters/loc.tpl');
                    break;
                    case 'wpt':
                        $tpl -> display('./lib/formatters/wpt.tpl');
                    break;
                    case 'uam':
                        $tpl -> display('./lib/formatters/uam.tpl');
                    break;
                }
            }
        }
    }

?>