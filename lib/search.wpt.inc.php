<?php

setlocale(LC_TIME, 'pl_PL.UTF-8');

        global $content, $bUseZip, $sqldebug, $usr, $hide_coords, $dbcSearch;
    set_time_limit(1800);
        $wptSize[1] = 'Inny'; //'Other'
        $wptSize[2] = 'Mikro'; //'Micro'
        $wptSize[3] = 'Mala'; //'Small'
        $wptSize[4] = 'Normalna'; //'Regular'
        $wptSize[5] = 'Duza'; //'Large'
        $wptSize[6] = 'Duza'; //'Large'
        $wptSize[7] = 'Wirtualna'; //'Virtual'

        $wptType[1] = 'Unknown Cache';
        $wptType[2] = 'Traditional Cache';
        $wptType[3] = 'Multi-Cache';
        $wptType[4] = 'Virtual Cache';
        $wptType[5] = 'Webcam Cache';
        $wptType[6] = 'Event Cache';
        $wptType[7] = 'Quiz';
        $wptType[8] = 'Moving Cache';
        $wptType[10] = 'Unknown Cache';

                    if( $usr || !$hide_coords )
                {
                    //prepare the output
                    $caches_per_page = 20;

                    $sql = 'SELECT ';

                    if (isset($lat_rad) && isset($lon_rad))
                    {
                                    $sql .= getCalcDistanceSqlFormula($usr !== false, $lon_rad * 180 / 3.14159, $lat_rad * 180 / 3.14159, 0, $multiplier[$distance_unit]) . ' `distance`, ';
                    }
                    else
                    {
                                    if ($usr === false)
                                    {
                                                    $sql .= '0 distance, ';
                                    }
                                    else
                                    {
                                                    //get the users home coords
                                                    $rs_coords = sql("SELECT `latitude`, `longitude` FROM `user` WHERE `user_id`='&1'", $usr['userid']);
                                                    $record_coords = sql_fetch_array($rs_coords);

                                                    if ((($record_coords['latitude'] == NULL) || ($record_coords['longitude'] == NULL)) || (($record_coords['latitude'] == 0) || ($record_coords['longitude'] == 0)))
                                                    {
                                                                    $sql .= '0 distance, ';
                                                    }
                                                    else
                                                    {
                                                                    //TODO: load from the users-profile
                                                                    $distance_unit = 'km';

                                                                    $lon_rad = $record_coords['longitude'] * 3.14159 / 180;
                    $lat_rad = $record_coords['latitude'] * 3.14159 / 180;

                                                                    $sql .= getCalcDistanceSqlFormula($usr !== false, $record_coords['longitude'], $record_coords['latitude'], 0, $multiplier[$distance_unit]) . ' `distance`, ';
                                                    }
                                                    mysql_free_result($rs_coords);
                                    }
                    }

                    $sql .= '`caches`.`cache_id` `cache_id`, `caches`.`status` `status`, `caches`.`type` `type`, `caches`.`size` `size`,
                        `caches`.`user_id` `user_id`, ';
                    if ($usr === false)
                    {
                        $sql .= ' `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, 0 as cache_mod_cords_id FROM `caches` ';
                    }
                    else
                    {
                        $sql .= ' IFNULL(`cache_mod_cords`.`longitude`, `caches`.`longitude`) `longitude`, IFNULL(`cache_mod_cords`.`latitude`,
                            `caches`.`latitude`) `latitude`, IFNULL(cache_mod_cords.id,0) as cache_mod_cords_id FROM `caches`
                        LEFT JOIN `cache_mod_cords` ON `caches`.`cache_id` = `cache_mod_cords`.`cache_id` AND `cache_mod_cords`.`user_id` = '
                            . $usr['userid'];
                    }
                    $sql .= ' WHERE `caches`.`cache_id` IN (' . $sqlFilter . ')';

                    $sortby = $options['sort'];
                    if (isset($lat_rad) && isset($lon_rad) && ($sortby == 'bydistance'))
                    {
                                    $sql .= ' ORDER BY distance ASC';
                    }
                    else if ($sortby == 'bycreated')
                    {
                                    $sql .= ' ORDER BY date_created DESC';
                    }
                    else // by name
                    {
                                    $sql .= ' ORDER BY name ASC';
                    }

                    //startat?
                    $startat = isset($_REQUEST['startat']) ? $_REQUEST['startat'] : 0;
                    if (!is_numeric($startat)) $startat = 0;

                    if (isset($_REQUEST['count']))
                                    $count = $_REQUEST['count'];
                    else
                                    $count = $caches_per_page;

                    $maxlimit = 1000000000;

                    if ($count == 'max') $count = $maxlimit;
                    if (!is_numeric($count)) $count = 0;
                    if ($count < 1) $count = 1;
                    if ($count > $maxlimit) $count = $maxlimit;

                    $sqlLimit = ' LIMIT ' . $startat . ', ' . $count;

                    // cleanup (old gpxcontent lingers if gpx-download is cancelled by user)
                    $dbcSearch->simpleQuery( 'DROP TEMPORARY TABLE IF EXISTS `wptcontent`');
                    $dbcSearch->reset();

                    // temporäre tabelle erstellen
                    $dbcSearch->simpleQuery( 'CREATE TEMPORARY TABLE `wptcontent` ' . $sql . $sqlLimit);
                    $dbcSearch->reset();

                    $dbcSearch->simpleQuery( 'SELECT COUNT(*) `count` FROM `wptcontent`');
                    $rCount = $dbcSearch->dbResultFetch();
                    $dbcSearch->reset();

                    if ($rCount['count'] == 1)
                    {
                                    $dbcSearch->simpleQuery('SELECT `caches`.`wp_oc` `wp_oc` FROM `wptcontent`, `caches` WHERE `wptcontent`.`cache_id`=`caches`.`cache_id` LIMIT 1');
                                    $rCount = $dbcSearch->dbResultFetch();
                                    $dbcSearch->reset();

                                    $sFilebasename = $rName['wp_oc'];
                    }
        else {
            if ($options['searchtype'] == 'bywatched') {
                $sFilebasename = 'watched_caches';
            } elseif ($options['searchtype'] == 'bylist') {
                $sFilebasename = 'cache_list';
            } else {
                $rsName = sql('SELECT `queries`.`name` `name` FROM `queries` WHERE `queries`.`id`= &1 LIMIT 1', $options['queryid']);
                $rName = sql_fetch_array($rsName);
                mysql_free_result($rsName);
                if (isset($rName['name']) && ($rName['name'] != '')) {
                    $sFilebasename = trim($rName['name']);
                    $sFilebasename = str_replace(" ", "_", $sFilebasename);
                } else {
                    $sFilebasename = 'ocpl' . $options['queryid'];
                }
            }
        }

        $bUseZip = ($rCount['count'] > 50);
        $bUseZip = $bUseZip || (isset($_REQUEST['zip']) && ($_REQUEST['zip'] == '1'));
        $bUseZip = false;
                    if ($bUseZip == true)
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
                                                    header('content-type: application/zip');
                                                    header('Content-Disposition: attachment; filename=' . $sFilebasename . '.zip');
                                    }
                                    else
                                    {
                                                    header('Content-type: application/wpt');
                                                    header('Content-Disposition: attachment; filename=' . $sFilebasename . '.wpt');
                                    }
                    }

                    // ok, ausgabe ...

                    /*
                                    cacheid
                                    name
                                    lon
                                    lat

                                    archivedflag
                                    type
                                    size
                                    difficulty
                                    terrain
                                    username
                    */

                    $sql = 'SELECT `wptcontent`.`cache_id` `cacheid`, IF(wptcontent.cache_id IN (SELECT cache_id FROM cache_logs WHERE deleted=0 AND user_id='.$usr['userid'].' AND (type=1 OR type=8)),1,0) as found, `wptcontent`.`longitude` `longitude`, `wptcontent`.`latitude` `latitude`, `wptcontent`.cache_mod_cords_id, `caches`.`date_hidden` `date_hidden`, `caches`.`name` `name`, `caches`.`wp_oc` `wp_oc`, `cache_type`.`short` `typedesc`, `cache_size`.`pl` `sizedesc`, `caches`.`terrain` `terrain`, `caches`.`difficulty` `difficulty`, `user`.`username` `username` , `caches`.`size` `size`, `caches`.`status` `status`, `caches`.`type` `type` FROM `wptcontent`, `caches`, `cache_type`, `cache_size`, `user` WHERE `wptcontent`.`cache_id`=`caches`.`cache_id` AND `wptcontent`.`type`=`cache_type`.`id` AND `wptcontent`.`size`=`cache_size`.`id` AND `wptcontent`.`user_id`=`user`.`user_id`';

                    $dbcSearch->simpleQuery( $sql, $sqldebug);

                    append_output("OziExplorer Waypoint File Version 1.1\r\n");
                    append_output("WGS 84\r\n");
                    append_output("Reserved 2\r\n");
                    append_output("Reserved 3\r\n");


                    while($r = $dbcSearch->dbResultFetch() )
                    {


                        $lat = sprintf('%01.6f', $r['latitude']);
                        $lon = sprintf('%01.6f', $r['longitude']);

                        //modified coords
                        if ($r['cache_mod_cords_id'] > 0) {  //check if we have user coords
                            $r['mod_suffix']= '[F]';
                        } else {
                            $r['mod_suffix']= '';
                        }

                        $name = PLConvert('UTF-8','POLSKAWY',str_replace(',','',$r['mod_suffix'].$r['name']));
                        $username = PLConvert('UTF-8','POLSKAWY',str_replace(',','',$r['username']));
                        $type = $wptType[$r['type']];
                        $size = $wptSize[$r['size']];
                        $difficulty = sprintf('%01.1f', $r['difficulty'] / 2);
                        $terrain = sprintf('%01.1f', $r['terrain'] / 2);
                        $cacheid = $r['wp_oc'];
                        $id = $r['cacheid'];
                        $sql_u = "SELECT user_id FROM caches WHERE cache_id = ".intval($id);
                        $date_hidden = $r['date_hidden'];
                        $userid = @mysql_result(@mysql_query($sql_u),0);

                        $kolor = 16776960;
                        if( $userid == $usr['userid'] )
                            $kolor = 65280;
                        if( $r['status'] == 3 || $r['status'] == 2 )
                            $kolor = 255;
                        if( $r['found'] )
                            $kolor = 65535;
                        $sss= "SELECT ozi_filips FROM user WHERE user_id=".$usr['userid'];
                        $r['ozi_filips']=@mysql_result(@mysql_query($sss),0);
                        if( $r['ozi_filips']!=""||$r['ozi_filips']!=null )
                            $attach = $r['ozi_filips']."\\op\\".$r['wp_oc'][2]."\\".$r['wp_oc'][3]."\\".$r['wp_oc'][4].$r['wp_oc'][5].".html";
                        else
                            $attach = "";
                        // remove double slashes
                        $attach = str_replace("\\\\", "\\", $attach);
                        //$line = $name . " by " . $username . " - " . $type . " (" . $difficulty . "/" . $terrain . ")";
                        $line = "$cacheid/$difficulty/$terrain/$size";
//                      Ograniczenie opisu do 40 znakow
//                      if (strlen($line) > 40) {
//                          $line = substr($line, 0, 40);
//                      }
                        //$wpt_date = floor((strtotime(substr($date_hidden,0,10)) - strtotime("1970-01-01")) / (60*60*24)) + 25570;
                        // number,name,lat,lon,date,symbol,stat, map form, fcolor,bcolor,desc(40),p direct,g disp,prox,alit,fsize,fstyle,symb size,prox,prox,prox
                        //$record  = "-1,$cacheid,$lat,$lon,$wpt_date,0,1,4,0,16777215,$line,0,0,0,-777,8,0,17,0,10.0,2,$attach,,\r\n";

                        $record  = "-1,$name,$lat,$lon,,117,1,4,0,$kolor,$line,0,0,0, -777,8,0,17,0,10.0,2,$attach,,\r\n";

                        append_output($record);
                        ob_flush();
                    }
                    $dbcSearch->reset();
                    unset($cdb);
                    if ($sqldebug == true) sqldbg_end();

                    // phpzip versenden
                    if ($bUseZip == true)
                    {
                                    $phpzip->add_data($sFilebasename . '.wpt', $content);
                                    echo $phpzip->save($sFilebasename . '.zip', 'b');
                    }

                    exit;
                    }

                    function convert_string($str)
                    {
                                    $newstr = iconv("UTF-8", "ASCII//TRANSLIT", $str);
                                    if ($newstr == false)
                                                    return "--- charset error ---";
                                    else
                                                    return $newstr;
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


        /*
Funkcja do konwersji polskich znakow miedzy roznymi systemami kodowania.
Zwraca skonwertowany tekst.

Argumenty:
$source - string - źródłowe kodowanie
$dest - string - źródłowe kodowanie
$tekst - string - tekst do konwersji

Obsługiwane formaty kodowania to:
POLSKAWY (powoduje zamianę polskich liter na ich łacińskie odpowiedniki)
ISO-8859-2
WINDOWS-1250
UTF-8
ENTITIES (zamiana polskich znaków na encje html)

Przyklad:
echo(PlConvert('UTF-8','ISO-8859-2','Zażółć gęślą jaźń.'));
*/
function PlConvert($source,$dest,$tekst)
{
    $source=strtoupper($source);
    $dest=strtoupper($dest);
    if($source==$dest) return $tekst;

    $chars['POLSKAWY']    =array('a','c','e','l','n','o','s','z','z','A','C','E','L','N','O','S','Z','Z');
    $chars['ISO-8859-2']  =array("\xB1","\xE6","\xEA","\xB3","\xF1","\xF3","\xB6","\xBC","\xBF","\xA1","\xC6","\xCA","\xA3","\xD1","\xD3","\xA6","\xAC","\xAF");
    $chars['WINDOWS-1250']=array("\xB9","\xE6","\xEA","\xB3","\xF1","\xF3","\x9C","\x9F","\xBF","\xA5","\xC6","\xCA","\xA3","\xD1","\xD3","\x8C","\x8F","\xAF");
    $chars['UTF-8']       =array('ą','ć','ę','ł','ń','ó','ś','ź','ż','Ą','Ć','Ę','Ł','Ń','Ó','Ś','Ź','Ż');
    $chars['ENTITIES']    =array('ą','ć','ę','ł','ń','ó','ś','ź','ż','Ą','Ć','Ę','Ł','Ń','Ó','Ś','Ź','Ż');

    if(!isset($chars[$source])) return false;
    if(!isset($chars[$dest])) return false;

    return str_replace($chars[$source],$chars[$dest],$tekst);
}

?>
