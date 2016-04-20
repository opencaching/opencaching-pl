<?php

use Utils\Database\OcDb;
use Utils\Database\XDb;

    //prepare the templates and include all neccessary
    if (!isset($rootpath)) $rootpath = '';
    require_once('./lib/common.inc.php');
    require_once('./lib/search.inc.php');
    require_once('./lib/search-signatures.inc.php');
    global $dbcSearch, $lang, $TestStartTime, $usr;

    //4test
    $TestStartTime = new DateTime('now');

    $dbcSearch = OcDb::instance();
    $dbc = OcDb::instance();

    // extract user data for KML search
    $usr = requestSigner::extract_user($usr);
    if ($usr == false) {
        $target = urlencode(tpl_get_current_page());
        tpl_redirect('login.php?target='.$target);
        exit;
    }

    //Preprocessing
    if ($error == false) {

        $tplname = 'search';
        require($stylepath . '/search.inc.php');
        require($rootpath . 'lib/caches.inc.php');

        common::sanitize($_REQUEST);

        //km => target-unit
        $multiplier['km'] = 1;
        $multiplier['sm'] = 0.62137;
        $multiplier['nm'] = 0.53996;
        if (isset($_REQUEST['queryid']) || isset($_REQUEST['showresult']))
        {
            $bCookieQueryid = false;
            $queryid = isset($_REQUEST['queryid']) ? addslashes($_REQUEST['queryid']) : 0;
        }
        else
        {
            $bCookieQueryid = true;
            $queryid = get_cookie_setting('lastqueryid');
            if ($queryid == false) $queryid = 0;

            if ($queryid != 0)
            {
                // check if query exists
                $sqlstr="SELECT COUNT(*) `count` FROM `queries` WHERE id= :1";
                $s = $dbc->multiVariableQuery($sqlstr, $queryid);
                $rCount=$dbc->dbResultFetch($s);

                if ($rCount['count'] == 0)
                    $queryid = 0;

                $dbc->reset($s);
            }

            if ($queryid == 0)
            {
                // das Suchformular wird initialisiert (keine Vorbelegungen vorhanden)
                $_REQUEST['cache_attribs'] = '';
                $rs = XDb::xSql('SELECT `id` FROM `cache_attrib` WHERE `default`=1');
                while ($r = XDb::xFetchArray($rs))
                {
                    if ($_REQUEST['cache_attribs'] != '') $_REQUEST['cache_attribs'] .= ';';
                    $_REQUEST['cache_attribs'] .= $r['id'];
                }
                XDb::xFreeResults($rs);

                $_REQUEST['cache_attribs_not'] = '';
                $rs = XDb::xSql('SELECT `id` FROM `cache_attrib` WHERE `default`=2');
                while ($r = XDb::xFetchArray($rs))
                {
                    if ($_REQUEST['cache_attribs_not'] != '') $_REQUEST['cache_attribs_not'] .= ';';
                    $_REQUEST['cache_attribs_not'] .= $r['id'];
                }
                XDb::xFreeResults($rs);
            }
        }
        $queryid = $queryid + 0;

        if ($queryid != 0)
        {
            //load options from db
            $sqlstr = "SELECT `user_id`, `options` FROM `queries` WHERE id= :1 AND (`user_id`=0 OR `user_id`= :2)";
            $s = $dbc->multiVariableQuery($sqlstr, $queryid, $usr['userid']+0);

            if ($dbc->rowCount($s) == 0)
            {
                $tplname = 'error';
                tpl_set_var('tplname', 'search.php');
                tpl_set_var('error_msg', $error_query_not_found);
                tpl_BuildTemplate();
                exit;
            }
            else
            {
                $record = $dbc->dbResultFetch($s);

                $options = unserialize($record['options']);
                if ($record['user_id'] != 0)
                    $options['userid'] = $record['user_id'];

                $dbc->reset($s);

                $options['queryid'] = $queryid;

                $sqlstr = "UPDATE `queries` SET `last_queried`=NOW() WHERE `id`= :1";
                $s = $dbc->multiVariableQuery($sqlstr, $queryid );
                $dbc->reset();

                // Ă¤nderbare werte Ăźberschreiben
                if (isset($_REQUEST['output']))
                    $options['output'] =  $_REQUEST['output'];

                if (isset($_REQUEST['showresult']))
                {
                    $options['showresult'] = $_REQUEST['showresult'];
                }
                else
                {
                    if ($bCookieQueryid == true)
                    {
                        $options['showresult'] = 0;
                    }
                }

                // finderid in finder umsetzen
                $options['finderid'] = isset($options['finderid']) ? $options['finderid'] + 0 : 0;
                if(isset($options['finder']) && $options['finderid'] > 0)
                {
                    $sqlstr = "SELECT `username` FROM `user` WHERE `user_id`= :1";
                    $dbc->multiVariableQuery($sqlstr, $options['finderid'] );

                    if($dbc->rowCount() == 1)
                    {
                        $record_name = $dbc->dbResultFetch();
                        $options['finder'] = $record_name['username'];
                    }
                    unset($record_name);
                    $dbc->reset();
                }

                // ownerid in owner umsetzen
                $options['ownerid'] = isset($options['ownerid']) ? $options['ownerid'] + 0 : 0;
                if(isset($options['owner']) && $options['ownerid'] > 0)
                {
                    $sqlstr="SELECT `username` FROM `user` WHERE `user_id`= :1";
                    $dbc->multiVariableQuery($sqlstr, $options['ownerid'] );

                    if($dbc->rowCount() == 1)
                    {
                        $record_name = $dbc->dbResultFetch();
                        $options['owner'] = $record_name['username'];
                    }
                    unset($record_name);
                    $dbc->reset();
                }
            }
        }
        else
        {
            // hack
            if(isset($_REQUEST['searchto']) && ($_REQUEST['searchto'] != ''))
            {
                unset($_REQUEST['searchbyname']);
                unset($_REQUEST['searchbydistance']);
                unset($_REQUEST['searchbyowner']);
                unset($_REQUEST['searchbyfinder']);
                unset($_REQUEST['searchbyplz']);
                unset($_REQUEST['searchbyort']);
                unset($_REQUEST['searchbyfulltext']);
                unset($_REQUEST['searchbywaypoint']);
                unset($_REQUEST['searchbywaypointname']);
                $_REQUEST[$_REQUEST['searchto']] = "hoho";
            }

            //get the taken search options and backup them in the queries table (to view "the next page")
            $options['f_userowner'] = isset($_REQUEST['f_userowner']) ? $_REQUEST['f_userowner'] : 1;
            $options['f_userfound'] = isset($_REQUEST['f_userfound']) ? $_REQUEST['f_userfound'] : 1;
            $options['f_inactive'] = isset($_REQUEST['f_inactive']) ? $_REQUEST['f_inactive'] : 1;
            $options['f_ignored'] = isset($_REQUEST['f_ignored']) ? $_REQUEST['f_ignored'] : 1;
            $options['f_watched'] = isset($_REQUEST['f_watched']) ? $_REQUEST['f_watched'] : 0;
            $options['f_geokret'] = isset($_REQUEST['f_geokret']) ? $_REQUEST['f_geokret'] : 0;
            $options['expert'] = isset($_REQUEST['expert']) ? $_REQUEST['expert'] : 0;
            $options['showresult'] = isset($_REQUEST['showresult']) ? $_REQUEST['showresult'] : 0;
            $options['output'] = isset($_REQUEST['output']) ? $_REQUEST['output'] : 'HTML';
            $options['gpxLogLimit'] = isset($_REQUEST['gpxLogLimit']) ? (int) $_REQUEST['gpxLogLimit'] : false;
            if(isset($_REQUEST['showonmap'])) {
                $_REQUEST['output'] = 'MAP';
                $options['output'] = 'MAP';
            }

            $options['logtype'] = isset($_REQUEST['logtype']) ? $_REQUEST['logtype'] : '';

            if (isset($_REQUEST['cache_attribs']))
            {
                if ($_REQUEST['cache_attribs'] != '')
                    $options['cache_attribs'] = mb_split(';', $_REQUEST['cache_attribs']);
                else
                    $options['cache_attribs'] = array();
            }
            else
                $options['cache_attribs'] = array();

            if (isset($_REQUEST['cache_attribs_not']))
            {
                if ($_REQUEST['cache_attribs_not'] != '')
                    $options['cache_attribs_not'] = mb_split(';', $_REQUEST['cache_attribs_not']);
                else
                    $options['cache_attribs_not'] = array();
            }
            else
                $options['cache_attribs_not'] = array();

            if (!isset($_REQUEST['unit']))
            {
                $options['unit'] = 'km';
            }
            elseif (mb_strtolower($_REQUEST['unit']) == 'sm')
            {
                $options['unit'] = 'sm';
            }
            elseif (mb_strtolower($_REQUEST['unit']) == 'nm')
            {
                $options['unit'] = 'nm';
            }
            else
            {
                $options['unit'] = 'km';
            }

            if (isset($_REQUEST['searchbyname']))
            {
                $options['searchtype'] = 'byname';
                $options['cachename'] = isset($_REQUEST['cachename']) ? stripslashes($_REQUEST['cachename']) : '';
            }
            elseif (isset($_REQUEST['searchbyowner']))
            {
                $options['searchtype'] = 'byowner';

                $options['ownerid'] = isset($_REQUEST['ownerid']) ? $_REQUEST['ownerid'] : 0;
                $options['owner'] = isset($_REQUEST['owner']) ? stripslashes($_REQUEST['owner']) : '';
            }
            elseif (isset($_REQUEST['searchbyfinder']))
            {
                $options['searchtype'] = 'byfinder';

                $options['finderid'] = isset($_REQUEST['finderid']) ? $_REQUEST['finderid'] : 0;
                $options['finder'] = isset($_REQUEST['finder']) ? stripslashes($_REQUEST['finder']) : '';
            }
            elseif (isset($_REQUEST['searchbyort']))
            {
                $options['searchtype'] = 'byort';

                $options['ort'] = isset($_REQUEST['ort']) ? stripslashes($_REQUEST['ort']) : '';
                $options['locid'] = isset($_REQUEST['locid']) ? $_REQUEST['locid'] : 0;
                $options['locid'] = $options['locid'] + 0;

                $options['distance'] = isset($_REQUEST['distance']) ? $_REQUEST['distance'] : 0;
            }
            elseif (isset($_REQUEST['searchbyplz']))
            {
                $options['searchtype'] = 'byplz';

                $options['plz'] = isset($_REQUEST['plz']) ? stripslashes($_REQUEST['plz']) : '';
                $options['locid'] = isset($_REQUEST['locid']) ? $_REQUEST['locid'] : 0;
                $options['locid'] = $options['locid'] + 0;
            }
            elseif (isset($_REQUEST['searchbydistance']))
            {
                $options['searchtype'] = 'bydistance';

                $options['latNS'] = isset($_REQUEST['latNS']) ? $_REQUEST['latNS'] : 'N';
                $options['lonEW'] = isset($_REQUEST['lonEW']) ? $_REQUEST['lonEW'] : 'E';

                $options['lat_h'] = isset($_REQUEST['lat_h']) ? $_REQUEST['lat_h'] : 0;
                $options['lon_h'] = isset($_REQUEST['lon_h']) ? $_REQUEST['lon_h'] : 0;
                $options['lat_min'] = isset($_REQUEST['lat_min']) ? $_REQUEST['lat_min'] : 0;
                $options['lon_min'] = isset($_REQUEST['lon_min']) ? $_REQUEST['lon_min'] : 0;

                $options['distance'] = isset($_REQUEST['distance']) ? $_REQUEST['distance'] : 0;
            }
            elseif (isset($_REQUEST['searchbyfulltext']))
            {
                $options['searchtype'] = 'byfulltext';

                $options['ft_name'] = isset($_REQUEST['ft_name']) ? $_REQUEST['ft_name']+0 : 0;
                $options['ft_desc'] = isset($_REQUEST['ft_desc']) ? $_REQUEST['ft_desc']+0 : 0;
                $options['ft_logs'] = isset($_REQUEST['ft_logs']) ? $_REQUEST['ft_logs']+0 : 0;
                $options['ft_pictures'] = isset($_REQUEST['ft_pictures']) ? $_REQUEST['ft_pictures']+0 : 0;

                $options['fulltext'] = isset($_REQUEST['fulltext']) ? $_REQUEST['fulltext'] : '';
            }
            elseif (isset($_REQUEST['searchbycacheid']))
            {
                $options['searchtype'] = 'bycacheid';
                $options['cacheid'] = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] : 0;
                if (!is_numeric($options['cacheid'])) $options['cacheid'] = 0;
            }
            elseif (isset($_REQUEST['searchbywaypoint']) OR isset($_REQUEST['searchbywaypointname']))
            {
                if (isset($_REQUEST['searchbywaypointname']))
                {
                    $options['searchtype'] = 'bywaypointname';
                    $options['cachename'] = isset($_REQUEST['waypointname']) ? stripslashes($_REQUEST['waypointname']) : '';
                    $options['waypoint'] = isset($_REQUEST['waypointname']) ? $_REQUEST['waypointname'] : '';
                }
                else
                {
                    $options['searchtype'] = 'bywaypoint';
                    $options['waypoint'] = isset($_REQUEST['waypoint']) ? $_REQUEST['waypoint'] : '';
                }
                $options['waypoint'] = mb_trim($options['waypoint']);
                $options['waypointtype'] = mb_strtolower(mb_substr($options['waypoint'], 0, 2));
                $ocWP=strtolower($GLOBALS['oc_waypoint']);
                if ( mb_ereg_match('(oc|'.$ocWP.'[a-z0-9]{4})$', mb_strtolower($options['waypoint'])) ) //O?xxxx
                {
                    $options['waypointtype'] = 'oc';
                }
                elseif ( mb_ereg_match('(n[a-f0-9]{5})$', mb_strtolower($options['waypoint'])) ) //Navicache.com
                {
                    $options['waypointtype'] = 'nc';
                }
                elseif ( mb_ereg_match('([a-zA-Z0-9]{4})$', $options['waypoint']) ) //xxxxx -> O?xxxxx
                {
                    $options['waypointtype'] = 'oc';
                    $options['waypoint'] = $ocWP.$options['waypoint'];
                }
                elseif ( !mb_ereg_match('(gc[a-z0-9]{4,5})$', mb_strtolower($options['waypoint'])) ) //GC and others - test
                {
                    $options['waypoint'] = '';
                }
            }
            elseif (isset($_REQUEST['searchbywatched']))
            {
                $options['searchtype'] = 'bywatched';
            }
            elseif (isset($_REQUEST['searchbylist']))
            {
                $options['searchtype'] = 'bylist';
            }
            elseif (isset($_REQUEST['searchbypt']))
            {
                $options['searchtype'] = 'bypt';
            }
            else
            {
                if (isset($_REQUEST['showresult'])){
                    tpl_errorMsg('search', tr("waypoint_error1"));
                } else {
                    $options['searchtype'] = 'byname';
                    $options['cachename'] = '';
                }
            }

            $options['sort'] = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'bydistance';
            $options['country'] = isset($_REQUEST['country']) ? $_REQUEST['country'] : '';
            $options['region'] = isset($_REQUEST['region']) ? $_REQUEST['region'] : '';
            $options['cachetype'] = isset($_REQUEST['cachetype']) ? $_REQUEST['cachetype'] : '111111111';

            $options['cachesize_1'] = isset($_REQUEST['cachesize_1']) ? $_REQUEST['cachesize_1'] : 1;
            $options['cachesize_2'] = isset($_REQUEST['cachesize_2']) ? $_REQUEST['cachesize_2'] : 1;
            $options['cachesize_3'] = isset($_REQUEST['cachesize_3']) ? $_REQUEST['cachesize_3'] : 1;
            $options['cachesize_4'] = isset($_REQUEST['cachesize_4']) ? $_REQUEST['cachesize_4'] : 1;
            $options['cachesize_5'] = isset($_REQUEST['cachesize_5']) ? $_REQUEST['cachesize_5'] : 1;
            $options['cachesize_6'] = isset($_REQUEST['cachesize_6']) ? $_REQUEST['cachesize_6'] : 1;
            $options['cachesize_7'] = isset($_REQUEST['cachesize_7']) ? $_REQUEST['cachesize_7'] : 1;

            $options['cachevote_1'] = isset($_REQUEST['cachevote_1']) ? $_REQUEST['cachevote_1'] : '';
            $options['cachevote_2'] = isset($_REQUEST['cachevote_2']) ? $_REQUEST['cachevote_2'] : '';
            $options['cachenovote'] = isset($_REQUEST['cachenovote']) ? $_REQUEST['cachenovote'] : 1;

            $options['cachedifficulty_1'] = isset($_REQUEST['cachedifficulty_1']) ? $_REQUEST['cachedifficulty_1'] : '';
            $options['cachedifficulty_2'] = isset($_REQUEST['cachedifficulty_2']) ? $_REQUEST['cachedifficulty_2'] : '';

            $options['cacheterrain_1'] = isset($_REQUEST['cacheterrain_1']) ? $_REQUEST['cacheterrain_1'] : '';
            $options['cacheterrain_2'] = isset($_REQUEST['cacheterrain_2']) ? $_REQUEST['cacheterrain_2'] : '';

            $options['cacherating'] = isset($_REQUEST['cacherating']) ? $_REQUEST['cacherating'] : 0;

            if ($options['showresult'] != 0)
            {
                //save the search-options in the database
                if (isset($options['queryid']) && (isset($options['userid'])))
                {
                    if ($options['userid'] != 0){
                        $sqlstr = "UPDATE `queries` SET `options`= :1, `last_queried`=NOW() WHERE `id`= :2 AND `user_id`= :3";
                        $dbc->multiVariableQuery($sqlstr, serialize($options), $options['queryid'], $options['userid'] );
                        $dbc->reset();
                    }
                }
                else
                {
                    XDb::xSql(
                        'INSERT INTO `queries` (`user_id`, `options`, `uuid`, `last_queried`)
                        VALUES (0, ?, UUID(), NOW())',
                        serialize($options));

                    $options['queryid'] = XDb::xLastInsertId();
                    //TODO - add method lastInsertId () to dataBase
                }
            }
            else
            {
                $options['queryid'] = 0;
            }
        }
        set_cookie_setting('lastqueryid', $options['queryid']);

        //remove old queries (after 1 hour without use)
        $removedate = date('Y-m-d H:i:s', time() - 3600);
        $sqlstr = "DELETE FROM `queries` WHERE `last_queried` < :1 AND `user_id`=0";
        $dbc->multiVariableQuery($sqlstr, $removedate );
        $dbc->reset();

        //prepare output
        if(!isset($options['showresult'])) $options['showresult']='0';
        if ($options['showresult'] == 1)
        {

            if(!isset($options['output'])) $options['output']='';
            if ((mb_strpos($options['output'], '.') !== false) ||
                (mb_strpos($options['output'], '/') !== false) ||
                (mb_strpos($options['output'], '\\') !== false)
               )
            {
                $options['output'] = 'HTML';
            }

            //make a list of cache-ids that are in the result
            if(!isset($options['expert'])) $options['expert']='';
            if ($options['expert'] == 0)
            {
                $sql_select = array();
                $sql_from = array();
                $sql_join = array();
                $sql_where = array();
                $sql_having = array();
                $sql_group = array();

                // show only published caches
                $sql_where[] = '`caches`.`status` != 4';
                $sql_where[] = '`caches`.`status` != 5';
                if(!$usr['admin'])
                {
                    $sql_where[] = '`caches`.`status` != 6';
                }


                //check the entered data and build SQL
                if(!isset($options['searchtype'])) $options['searchtype']='';
                if ($options['searchtype'] == 'byname')
                {
                    $sql_select[] = '`caches`.`cache_id` `cache_id`';
                    $sql_from[] = '`caches`';
                    $sql_where[] = '`caches`.`name` LIKE \'%' . XDb::xEscape($options['cachename']) . '%\'';
                }
                elseif ($options['searchtype'] == 'byowner')
                {
                    if ($options['ownerid'] != 0)
                    {
                        $sql_select[] = '`caches`.`cache_id` `cache_id`';
                        $sql_from[] = '`caches`';
                        $sql_where[] = '`user_id`=\'' . XDb::xEscape($options['ownerid']) . '\'';
                    }
                    else
                    {
                        $sql_select[] = '`caches`.`cache_id` `cache_id`';
                        $sql_from[] = '`caches`, `user`';
                        $sql_where[] = '`caches`.`user_id`=`user`.`user_id`';
                        $sql_where[] = '`user`.`username`=\'' . XDb::xEscape($options['owner']) . '\'';
                    }
                }
                elseif (($options['searchtype'] == 'byplz') || ($options['searchtype'] == 'byort'))
                {
                    $locid = $options['locid'];

                    if ($options['searchtype'] == 'byplz')
                    {
                        if ($locid == 0)
                        {
                            $plz = $options['plz'];

                            $sqlstr = "SELECT `loc_id` FROM `geodb_textdata` WHERE `text_type`=500300000 AND `text_val`= :1 ";
                            $dbc->multiVariableQuery($sqlstr, XDb::xEscape($plz) );
                            if ($dbc->rowCount() == 0)
                            {
                                $options['error_plz'] = true;
                                outputSearchForm($options);
                                unset($dbc);
                                unset($dbcSearch);
                                exit;
                            }
                            elseif ($dbc->rowCount() == 1)
                            {
                                $r = $dbc->dbResultFetch();
                                $locid = $r['loc_id'];
                                $dbc->reset();
                            }
                            else
                            {
                                // ok, viele locations ... alle auflisten ...
                                outputLocidSelectionForm($sqlstr, $options);
                                unset($dbc);
                                unset($dbcSearch);
                                exit;
                            }
                        }

                        // ok, wir haben einen ort ... koordinaten ermitteln
                        $locid = $locid + 0;

                        $sqlstr = "SELECT `lon`, `lat` FROM `geodb_coordinates` WHERE `loc_id`= :1 AND coord_type=200100000";
                        $dbc->multiVariableQuery($sqlstr, $locid );

                        if ( $dbc->rowCount() )
                        {
                            // ok ... wir haben koordinaten ...
                            $r = $dbc->dbResultFetch();

                            $lat = $r['lat'] + 0;
                            $lon = $r['lon'] + 0;

                            $distance_unit = 'km';
                            $distance = 20;

                            // ab hier selber code wie bei bydistance ... TODO: in funktion auslagern

                            //all target caches are between lat - max_lat_diff and lat + max_lat_diff
                            $max_lat_diff = $distance / (111.12 * $multiplier[$distance_unit]);

                            //all target caches are between lon - max_lon_diff and lon + max_lon_diff
                            //TODO: check!!!
                            $max_lon_diff = $distance * 180 / (abs(sin((90 - $lat) * 3.14159 / 180 )) * 6378 * $multiplier[$distance_unit] * 3.14159);

                            $lon_rad = $lon * 3.14159 / 180;
                            $lat_rad = $lat * 3.14159 / 180;

                            $sqlstr = 'CREATE TEMPORARY TABLE result_caches ENGINE=MEMORY
                                                    SELECT
                                                        (' . getCalcDistanceSqlFormula($usr !== false,$lon, $lat, $distance, $multiplier[$distance_unit]) . ') `distance`,
                                                        `caches`.`cache_id` `cache_id`
                                                    FROM `caches` FORCE INDEX (`latitude`)
                                                    LEFT JOIN `cache_mod_cords` ON `caches`.`cache_id` = `cache_mod_cords`.`cache_id` AND `cache_mod_cords`.`user_id` = :1
                                                    WHERE IFNULL(cache_mod_cords.longitude, `caches`.`longitude`) >  :2
                                                        AND IFNULL(cache_mod_cords.longitude, `caches`.`longitude`)  <  :3
                                                        AND IFNULL(cache_mod_cords.latitude, `caches`.`latitude`)  > :4
                                                        AND IFNULL(cache_mod_cords.latitude, `caches`.`latitude`) < :5
                                                    HAVING `distance` < :6';


                            $dbcSearch->multiVariableQuery( $sqlstr, $usr['userid'], ($lon - $max_lon_diff), ($lon + $max_lon_diff), ($lat - $max_lat_diff), ($lat + $max_lat_diff), $distance );

                            $sqlstr = 'ALTER TABLE result_caches ADD PRIMARY KEY ( `cache_id` )';
                            $dbcSearch->simpleQuery( $sqlstr );

                            $sql_select[] = '`result_caches`.`cache_id`';
                            $sql_from[] = '`result_caches`, `caches`';
                            $sql_where[] = '`caches`.`cache_id`=`result_caches`.`cache_id`';

                            $dbc->reset();
                        }
                        else
                        {
                            $options['error_locidnocoords'] = true;
                            outputSearchForm($options);
                            unset($dbc);
                            unset($dbcSearch);
                            exit;
                        }
                    }
                    else if ($options['searchtype'] == 'byort')
                    {
                        if ($locid == 0)
                        {
                            require_once($rootpath . 'lib/search.inc.php');

                            $ort = trim($options['ort']);
                            $simpletexts = search_text2sort($ort);

                            $simpletextsarray = mb_split(' |-|/|,', $simpletexts);

                            $sqlhashes = '';
                            $wordscount = 0;
                            foreach ($simpletextsarray AS $text)
                            {
                                if ($text != '')
                                {
                                    $searchstring = search_text2simple($text);

                                    if ($sqlhashes != '') $sqlhashes .= ' OR ';
                                    $sqlhashes .= '`gns_search`.`simplehash`=' . sprintf("%u", crc32($searchstring));

                                    $wordscount++;
                                }
                            }

                            if ($sqlhashes == '')
                            {
                                $options['error_noort'] = true;
                                outputSearchForm($options);
                            }

                            // temporĂ¤re tabelle erstellen und dann eintrĂ¤ge entfernen, die nicht mindestens so oft vorkommen wie worte gegeben wurden
                            XDb::xSql('CREATE TEMPORARY TABLE tmpuniids (`uni_id` int(11) NOT NULL, `cnt` int(11) NOT NULL, `olduni` int(11) NOT NULL, `simplehash` int(11) NOT NULL) ENGINE=MEMORY SELECT `gns_search`.`uni_id` `uni_id`, 0 `cnt`, 0 `olduni`, `simplehash` FROM `gns_search` WHERE ' . $sqlhashes);
                            XDb::xSql('ALTER TABLE `tmpuniids` ADD INDEX (`uni_id`)');
//  BUGFIX: dieser Code sollte nur ausgefĂźhrt werden, wenn mehr als ein Suchbegriff eingegeben wurde
//                  damit alle EintrĂ¤ge gefiltert, die nicht alle Suchbegriffe enthalten
//                  nun wird dieser Quellcode auch ausgefĂźhrt, um mehrfache uni_id's zu filtern
//          Notwendig, wenn nach Baden gesucht wird => Baden-Baden war doppelt in der Liste
//                          if ($wordscount > 1)
//                          {
                                XDb::xSql('CREATE TEMPORARY TABLE `tmpuniids2` (`uni_id` int(11) NOT NULL, `cnt` int(11) NOT NULL, `olduni` int(11) NOT NULL) ENGINE=MEMORY SELECT `uni_id`, COUNT(*) `cnt`, 0 olduni FROM `tmpuniids` GROUP BY `uni_id` HAVING `cnt` >= ' . $wordscount);
                                XDb::xSql('ALTER TABLE `tmpuniids2` ADD INDEX (`uni_id`)');
                                XDb::xSql('DROP TABLE `tmpuniids`');
                                XDb::xSql('ALTER TABLE `tmpuniids2` RENAME `tmpuniids`');
//                          }

//    add: SELECT g2.uni FROM `tmpuniids` JOIN gns_locations g1 ON tmpuniids.uni_id=g1.uni JOIN gns_locations g2 ON g1.ufi=g2.ufi WHERE g1.nt!='N' AND g2.nt='N'
// remove: SELECT g1.uni FROM `tmpuniids` JOIN gns_locations g1 ON tmpuniids.uni_id=g1.uni JOIN gns_locations g2 ON g1.ufi=g2.ufi WHERE g1.nt!='N' AND g2.nt='N'

                            // und jetzt noch alle englischen bezeichnungen durch deutsche ersetzen (wo mĂśglich) ...
                            XDb::xSql('CREATE TEMPORARY TABLE `tmpuniidsAdd` (`uni` int(11) NOT NULL, `olduni` int(11) NOT NULL, PRIMARY KEY  (`uni`)) ENGINE=MEMORY SELECT g2.uni uni, g1.uni olduni FROM `tmpuniids` JOIN gns_locations g1 ON tmpuniids.uni_id=g1.uni JOIN gns_locations g2 ON g1.ufi=g2.ufi WHERE g1.nt!=\'N\' AND g2.nt=\'N\' GROUP BY uni');
                            XDb::xSql('CREATE TEMPORARY TABLE `tmpuniidsRemove` (`uni` int(11) NOT NULL, PRIMARY KEY  (`uni`)) ENGINE=MEMORY SELECT DISTINCT g1.uni uni FROM `tmpuniids` JOIN gns_locations g1 ON tmpuniids.uni_id=g1.uni JOIN gns_locations g2 ON g1.ufi=g2.ufi WHERE g1.nt!=\'N\' AND g2.nt=\'N\'');
                            XDb::xSql('DELETE FROM tmpuniids WHERE uni_id IN (SELECT uni FROM tmpuniidsRemove)');
                            XDb::xSql('DELETE FROM tmpuniidsAdd WHERE uni IN (SELECT uni_id FROM tmpuniids)');
                            XDb::xSql('INSERT INTO tmpuniids (uni_id, olduni) SELECT uni, olduni FROM tmpuniidsAdd');
                            XDb::xSql('DROP TABLE tmpuniidsAdd');
                            XDb::xSql('DROP TABLE tmpuniidsRemove');

                            $rs = XDb::xSql('SELECT `uni_id` FROM tmpuniids');
                            if (XDb::xNumRows($rs) == 0)
                            {
                                XDb::xFreeResults($rs);

                                $options['error_ort'] = true;
                                outputSearchForm($options);
                                unset($dbc);
                                unset($dbcSearch);
                                exit;
                            }
                            elseif (XDb::xNumRows($rs) == 1)
                            {
                                $r = XDb::xFetchArray($rs);
                                XDb::xFreeResults($rs);

                                // wenn keine 100%ige Ăźbereinstimmung nochmals anzeigen
                                $locid = $r['uni_id'] + 0;
                                $sqlstr = "SELECT `full_name` FROM `gns_locations` WHERE `uni`= :1 LIMIT 1";
                                $dbc->multiVariableQuery($sqlstr, $locid );
                                $rCmp = $dbc->dbResultFetch();

                                if (mb_strtolower($rCmp['full_name']) != mb_strtolower($ort))
                                {
                                    outputUniidSelectionForm('SELECT `uni_id`, `olduni` FROM `tmpuniids`', $options);
                                }

                                $dbc->reset();
                            }
                            else
                            {
                                XDb::xFreeResults($rs);
                                outputUniidSelectionForm('SELECT `uni_id`, `olduni` FROM `tmpuniids`', $options);
                                unset($dbc);
                                unset($dbcSearch);
                                exit;
                            }
                        }


                        // ok, wir haben einen ort ... koordinaten ermitteln
                        $locid = $locid + 0;
                        $sqlstr="SELECT `lon`, `lat` FROM `gns_locations` WHERE `uni`= :1 LIMIT 1";
                        $dbc->multiVariableQuery($sqlstr, $locid );

                        if ( $dbc->rowCount() )
                        {
                            $r =  $dbc->dbResultFetch();
                            // ok ... wir haben koordinaten ...

                            $lat = $r['lat'] + 0;
                            $lon = $r['lon'] + 0;

                            $dbc->reset();

                            $lon_rad = $lon * 3.14159 / 180;
                            $lat_rad = $lat * 3.14159 / 180;

                            //$distance_unit = 'km';
                            //$distance = 20;

                            $distance = $options['distance'];
                            $distance_unit = $options['unit'];

                            // ab hier selber code wie bei bydistance ... TODO: in funktion auslagern

                            //all target caches are between lat - max_lat_diff and lat + max_lat_diff
                            $max_lat_diff = $distance / (111.12 * $multiplier[$distance_unit]);

                            //all target caches are between lon - max_lon_diff and lon + max_lon_diff
                            //TODO: check!!!
                            $max_lon_diff = $distance * 180 / (abs(sin((90 - $lat) * 3.14159 / 180 )) * 6378 * $multiplier[$distance_unit] * 3.14159);

                            $sqlstr = 'CREATE TEMPORARY TABLE result_caches ENGINE=MEMORY
                                                    SELECT
                                                        (' . getCalcDistanceSqlFormula($usr !== false,$lon, $lat, $distance, $multiplier[$distance_unit]) . ') `distance`,
                                                        `caches`.`cache_id` `cache_id`
                                                    FROM `caches` FORCE INDEX (`latitude`)
                                                LEFT JOIN `cache_mod_cords` ON `caches`.`cache_id` = `cache_mod_cords`.`cache_id` AND `cache_mod_cords`.`user_id` = :1
                                                    WHERE IFNULL(cache_mod_cords.longitude, `caches`.`longitude`) >  :2
                                                        AND IFNULL(cache_mod_cords.longitude, `caches`.`longitude`)  < :3
                                                        AND IFNULL(cache_mod_cords.latitude, `caches`.`latitude`)  >  :4
                                                        AND IFNULL(cache_mod_cords.latitude, `caches`.`latitude`) < :5
                                                    HAVING `distance` < :6';

                            $dbcSearch->multiVariableQuery( $sqlstr, $usr['userid'], ($lon - $max_lon_diff), ($lon + $max_lon_diff), ($lat - $max_lat_diff), ($lat + $max_lat_diff), $distance );

                            $sqlstr = 'ALTER TABLE result_caches ADD PRIMARY KEY ( `cache_id` )';
                            $dbcSearch->simpleQuery( $sqlstr );

                            $sql_select[] = '`result_caches`.`cache_id`';
                            $sql_from[] = '`result_caches`, `caches`';
                            $sql_where[] = '`caches`.`cache_id`=`result_caches`.`cache_id`';
                        }
                        else
                        {
                            $options['error_locidnocoords'] = true;
                            outputSearchForm($options);
                            unset($dbc);
                            unset($dbcSearch);
                            exit;
                        }
                    }
                }
                elseif ($options['searchtype'] == 'byfinder')
                {
                    if ($options['finderid'] != 0)
                    {
                        $finder_id = $options['finderid'];
                    }
                    else
                    {
                        //get the userid
                        $sqlstr = "SELECT `user_id` FROM `user` WHERE `username`= :1";
                        $dbc->multiVariableQuery($sqlstr, $options['finder'] );
                        $finder_record = $dbc->dbResultFetch();
                        $finder_id = $finder_record['user_id'];
                        $dbc->reset();
                    }

                    $sql_select[] = '`caches`.`cache_id` `cache_id`';
                    $sql_from[] = '`caches`, `cache_logs`';
                    $sql_where[] = '`caches`.`cache_id`=`cache_logs`.`cache_id`';
                    $sql_where[] = '`cache_logs`.`user_id`=\'' . XDb::xEscape($finder_id) . '\'';
                    $sql_where[] = '`cache_logs`.`deleted`=0';
                    if( $options['logtype'] == "" )
                        $sql_where[] = '(`cache_logs`.`type`=1 OR `cache_logs`.`type`=7)'; // found und attended
                    else
                        $sql_where[] = '(`cache_logs`.`type`='.intval($options['logtype']).')'; // found und attended

                }
                elseif ($options['searchtype'] == 'bydistance')
                {
                    //check the entered data
                    $latNS = $options['latNS'];
                    $lonEW = $options['lonEW'];

                    $lat_h = $options['lat_h'];
                    $lon_h = $options['lon_h'];
                    $lat_min = $options['lat_min'];
                    $lon_min = $options['lon_min'];

                    $distance = $options['distance'];
                    $distance_unit = $options['unit'];

                    if (is_numeric($lon_h) && is_numeric($lon_min))
                    {
                        if (($lon_h >= 0) && ($lon_h < 180) && ($lon_min >= 0) && ($lon_min < 60))
                        {
                            $lon = $lon_h + $lon_min / 60;
                            if ($lonEW == 'W') $lon = -$lon;
                        }
                    }

                    if (is_numeric($lat_h) && is_numeric($lat_min))
                    {
                        if (($lat_h >= 0) && ($lat_h < 90) && ($lat_min >= 0) && ($lat_min < 60))
                        {
                            $lat = $lat_h + $lat_min / 60;
                            if ($latNS == 'S') $lat = -$lat;
                        }
                    }

                    if ((!isset($lon)) || (!isset($lat)) || (!is_numeric($distance)))
                    {
                        outputSearchForm($options);
                        unset($dbc);
                        unset($dbcSearch);
                        exit;
                    }

                    //make the sql-String

                    //all target caches are between lat - max_lat_diff and lat + max_lat_diff
                    $max_lat_diff = $distance / (111.12 * $multiplier[$distance_unit]);

                    //all target caches are between lon - max_lon_diff and lon + max_lon_diff
                    //TODO: check!!!
                    $max_lon_diff = $distance * 180 / (abs(sin((90 - $lat) * 3.14159 / 180 )) * 6378 * $multiplier[$distance_unit] * 3.14159);

                    $lon_rad = $lon * 3.14159 / 180;
                    $lat_rad = $lat * 3.14159 / 180;

                    $sqlstr ='CREATE TEMPORARY TABLE result_caches ENGINE=MEMORY
                    SELECT
                        (' . getCalcDistanceSqlFormula($usr !== false,$lon, $lat, $distance, $multiplier[$distance_unit]) . ') `distance`,
                        `caches`.`cache_id` `cache_id`
                    FROM `caches` FORCE INDEX (`latitude`)
                        LEFT JOIN `cache_mod_cords` ON `caches`.`cache_id` = `cache_mod_cords`.`cache_id` AND `cache_mod_cords`.`user_id` = :1
                            WHERE IFNULL(cache_mod_cords.longitude, `caches`.`longitude`) > :2
                                AND IFNULL(cache_mod_cords.longitude, `caches`.`longitude`)  < :3
                                AND IFNULL(cache_mod_cords.latitude, `caches`.`latitude`)  > :4
                                AND IFNULL(cache_mod_cords.latitude, `caches`.`latitude`) < :5
                    HAVING `distance` < :6';

                    $dbcSearch->multiVariableQuery( $sqlstr, $usr['userid'], ($lon - $max_lon_diff), ($lon + $max_lon_diff), ($lat - $max_lat_diff), ($lat + $max_lat_diff), $distance );

                    $sqlstr = 'ALTER TABLE result_caches ADD PRIMARY KEY ( `cache_id` )';
                    $dbcSearch->simpleQuery( $sqlstr );

                    $sql_select[] = '`result_caches`.`cache_id`';
                    $sql_from[] = '`result_caches`, `caches`';
                    $sql_where[] = '`caches`.`cache_id`=`result_caches`.`cache_id`';
                }
                elseif ($options['searchtype'] == 'bycacheid')
                {
                    $sql_select[] = '`caches`.`cache_id` `cache_id`';
                    $sql_from[] = '`caches`';
                    $sql_where[] = '`caches`.`cache_id`=\'' . XDb::xEscape($options['cacheid']) . '\'';
                }
                elseif ($options['searchtype'] == 'bywatched')
                {
                    $sql_select[] = '`caches`.`cache_id` `cache_id`';
                    $sql_from[] = '`caches`';
                    $sql_where[] = '`caches`.`cache_id` IN ( SELECT `cache_watches`.`cache_id` FROM `cache_watches` WHERE `cache_watches`.`user_id` =  \'' . XDb::xEscape($usr['userid']) . '\' )';
                }
                elseif ($options['searchtype'] == 'bylist')
                {
                    if (isset($_REQUEST['SearchCacheID']))
                    {
                        $cache_bylist = $_REQUEST['SearchCacheID'];
                    } else
                    if (isset($options['cache_ids'])){
                        foreach($options['cache_ids'] as $key=>$val ){
                            $options['cache_ids'][$key] = XDb::xEscape($val);
                        }
                        $cache_bylist = implode(',', $options['cache_ids']);
                    } else
                    if (count($_SESSION['print_list']) == 0) {
                        $cache_bylist = -1;
                    } else {
                        $cache_bylist = implode(",", $_SESSION['print_list']);
                    }

                    $sql_select[] = '`caches`.`cache_id` `cache_id`';
                    $sql_from[] = '`caches`';
                    $sql_where[] = '`caches`.`cache_id` IN ('. $cache_bylist .')';
                }
                // generate gpx from cache list generated by PowerTrail module
                elseif ($options['searchtype'] == 'bypt') {
                    //print '<pre>';
                    //print_r($_SESSION['geoPathCacheList']);
                    if (isset($_SESSION['geoPathCacheList']) && count($_SESSION['geoPathCacheList']) > 0){
                        $cache_bylist = implode(",", $_SESSION['geoPathCacheList']);
                        $options['gpxPtFileName'] = $_SESSION['ptName'];
                    } else {
                        $options['ptId'] = (int) $_REQUEST['ptId'];
                        $cache_bylist = implode(",",powerTrailBase::getPtCachesIds($options['ptId']));
                        $ptDetails = powerTrailBase::getPtDbRow($options['ptId']);
                        $options['gpxPtFileName'] = powerTrailBase::clearPtNames($ptDetails['name']);
                    }
                    $sql_select[] = '`caches`.`cache_id` `cache_id`';
                    $sql_from[] = '`caches`';
                    $sql_where[] = '`caches`.`cache_id` IN ('. $cache_bylist .')';
                    // unset ($_SESSION['geoPathCacheList']);
                }
                elseif ($options['searchtype'] == 'bywaypoint' && $options['waypoint']!='')
                {
                    $sql_select[] = '`caches`.`cache_id` `cache_id`';
                    $sql_from[] = '`caches`';
                    $sql_where[] = '`caches`.`wp_' . XDb::xEscape($options['waypointtype']) . '`=\'' . XDb::xEscape($options['waypoint']) . '\'';
                } // end added by bebe
                elseif ($options['searchtype'] == 'bywaypointname')
                {
                    $sql_select[] = '`caches`.`cache_id` `cache_id`';
                    $sql_from[] = '`caches`';
                    if ($options['waypoint'] == '')
                    {
                        $sql_where[] = '`caches`.`name` LIKE \'%' . XDb::xEscape($options['cachename']) . '%\'';
                    }
                    else
                    {
                        $sql_where[] = '`caches`.`name` LIKE \'%' . XDb::xEscape($options['cachename']) . '%\' OR `caches`.`wp_' . XDb::xEscape($options['waypointtype']) . '`=\'' . XDb::xEscape($options['waypoint']) . '\'';
                    }
                }
                elseif ($options['searchtype'] == 'byfulltext')
                {
                    require_once($rootpath . 'lib/ftsearch.inc.php');

                    $fulltext = $options['fulltext'];
                    $hashes = ftsearch_hash($fulltext);

                    if (count($hashes) == 0)
                    {
                        $options['error_nofulltext'] = true;
                        outputSearchForm($options);
                    }

                    $ft_types = array();
                    if (isset($options['ft_name']) && $options['ft_name'])
                        $ft_types[] = 2;
                    if (isset($options['ft_logs']) && $options['ft_logs'])
                        $ft_types[] = 1;
                    if (isset($options['ft_desc']) && $options['ft_desc'])
                        $ft_types[] = 3;
                    if (isset($options['ft_pictures']) && $options['ft_pictures'])
                        $ft_types[] = 6;
                    if (count($ft_types) == 0)
                        $ft_types[] = 0;

                    $sql_select[] = '`caches`.`cache_id` `cache_id`';

                    $n = 1;
                    foreach ($hashes AS $k => $h)
                    {
                        $sql_from[] = '`search_index` `s' . $n . '`';

                        if ($n > 1)
                            $sql_where[] = '`s' . ($n-1) . '`.`cache_id`=`s' . $n . '`.`cache_id`';

                        $sql_where[] = '`s' . $n . '`.`hash`=\'' . XDb::xEscape($h) . '\'';
                        $sql_where[] = '`s' . $n . '`.`object_type` IN (' . implode(',', $ft_types) . ')';

                        $n++;
                    }

                    $sql_from[] = '`caches`';
                    $sql_where[] = '`s1`.`cache_id`=`caches`.`cache_id`';

                    $queryFilter = 'SELECT DISTINCT ' . implode(',', $sql_select) .
                            ' FROM ' . implode(',', $sql_from) .
                            ' WHERE ' . implode(' AND ', $sql_where);

                    $dbcSearch->simpleQuery('CREATE TEMPORARY TABLE `tmpFTCaches` (`cache_id` int (11) PRIMARY KEY) ' . $queryFilter);
                    $dbcSearch->reset();

                    $sql_select = array();
                    $sql_from = array();
                    $sql_where = array();

                    $sql_select[] = '`caches`.`cache_id` `cache_id`';
                    $sql_from[] = '`tmpFTCaches`';
                    $sql_from[] = '`caches`';
                    $sql_where[] = '`caches`.`cache_id`=`tmpFTCaches`.`cache_id`';
                }
                else
                {
                    tpl_errorMsg('search', tr("waypoint_error2"));
                }

                // additional options
                if(!isset($options['f_userowner'])) $options['f_userowner']='0';
                if($options['f_userowner'] != 0) { $sql_where[] = '`caches`.`user_id`!=\'' . $usr['userid'] .'\''; }

                if(!isset($options['f_userfound'])) $options['f_userfound']='0';
                if($options['f_userfound'] != 0)
                {
                    $sql_where[] = '`caches`.`cache_id` NOT IN (SELECT `cache_logs`.`cache_id` FROM `cache_logs` WHERE `cache_logs`.`deleted`=0 AND `cache_logs`.`user_id`=\'' . XDb::xEscape($usr['userid']) . '\' AND `cache_logs`.`type` IN (1, 7))';
                }

                if(!isset($options['f_geokret'])) $options['f_geokret']='0';
                //TODO SQL dla GeoKretow
                //if($options['f_geokret'] != 0) { $sql_where[] = '`caches`.`user_id`!=\'' . $usr['userid'] .'\''; }

                if(!isset($options['f_inactive'])) $options['f_inactive']='0';
                if($options['f_inactive'] != 0)  $sql_where[] = '`caches`.`status`=1';

                if(isset($usr))
                {
                    if(!isset($options['f_ignored'])) $options['f_ignored']='0';
                    if($options['f_ignored'] != 0)
                    {
                        $sql_where[] = '`caches`.`cache_id` NOT IN (SELECT `cache_ignore`.`cache_id` FROM `cache_ignore` WHERE `cache_ignore`.`user_id`=\'' . XDb::xEscape($usr['userid']) . '\')';
                    }
                    if(!isset($options['f_watched'])) $options['f_watched']='0';
                    if($options['f_watched'] != 0)
                    {
                        $sql_where[] = '`caches`.`cache_id` NOT IN (SELECT `cache_watches`.`cache_id` FROM `cache_watches` WHERE `cache_watches`.`user_id`=\'' . XDb::xEscape($usr['userid']) . '\')';
                    }
                }

                if(!isset($options['country'])) $options['country']='';
                if($options['country'] != '')
                {
                    $sql_where[] = '`caches`.`country`=\'' . XDb::xEscape($options['country']) . '\'';
                }

                if(!isset($options['region'])) $options['region']='';
                if($options['region'] != '')
                {
                    $sql_where[] = '`caches`.`cache_id` IN (SELECT `cache_location`.`cache_id` FROM `cache_location` WHERE `cache_location`.`code3`=\'' . XDb::xEscape($options['region']) . '\')';
                }

                if(!isset($options['cachetype'])) $options['cachetype']='111111111';
                $pos = strpos($options['cachetype'], '0');

                //echo $options['cachetype'];

                if($pos !== false)
                {
                    $c_type = array();
                    for ($i=0; $i<strlen($options['cachetype']);$i++){
                        if ($options['cachetype'][$i] == '1') {
                            $c_type[] = $i+1;
                        }
                    }

                    if (count($c_type) >= 1) {
                        $sql_where[] = '`caches`.`type` IN (' . XDb::xEscape(implode(",", $c_type)) . ')';
                    }
                }
                if(isset($options['cache_attribs']) && count($options['cache_attribs']) > 0)
                {
                    for($i=0; $i < count($options['cache_attribs']); $i++)
                    {
                        if($options['cache_attribs'][$i] == 99) // special password attribute case
                            $sql_where[] = '`caches`.`logpw` != ""';
                        else {
                            $sql_from[] = '`caches_attributes` `a' . ($options['cache_attribs'][$i]+0) . '`';
                            $sql_where[] = '`a' . ($options['cache_attribs'][$i]+0) . '`.`cache_id`=`caches`.`cache_id`';
                            $sql_where[] = '`a' . ($options['cache_attribs'][$i]+0) . '`.`attrib_id`=' . ($options['cache_attribs'][$i]+0);
                        }
                    }
                }

                if(isset($options['cache_attribs_not']) && count($options['cache_attribs_not']) > 0)
                {
                    for($i=0; $i < count($options['cache_attribs_not']); $i++)
                    {
                        if($options['cache_attribs_not'][$i] == 99) // special password attribute case
                            $sql_where[] = '`caches`.`logpw` = ""';
                        else
                            $sql_where[] = 'NOT EXISTS (SELECT `caches_attributes`.`cache_id` FROM `caches_attributes` WHERE `caches_attributes`.`cache_id`=`caches`.`cache_id` AND `caches_attributes`.`attrib_id`=\'' . XDb::xEscape($options['cache_attribs_not'][$i]) . '\')';
                    }
                }

                $cachesize = array();

                if (isset($options['cachesize_1']) && ($options['cachesize_1'] == '1')) { $cachesize[] = '1'; }
                if (isset($options['cachesize_2']) && ($options['cachesize_2'] == '1')) { $cachesize[] = '2'; }
                if (isset($options['cachesize_3']) && ($options['cachesize_3'] == '1')) { $cachesize[] = '3'; }
                if (isset($options['cachesize_4']) && ($options['cachesize_4'] == '1')) { $cachesize[] = '4'; }
                if (isset($options['cachesize_5']) && ($options['cachesize_5'] == '1')) { $cachesize[] = '5'; }
                if (isset($options['cachesize_6']) && ($options['cachesize_6'] == '1')) { $cachesize[] = '6'; }
                if (isset($options['cachesize_7']) && ($options['cachesize_7'] == '1')) { $cachesize[] = '7'; }

                if ((sizeof($cachesize) > 0) && (sizeof($cachesize) < 7)) {
                    $sql_where[] = '`caches`.`size` IN (' . implode(' , ', $cachesize) . ')';
                }

                if(!isset($options['cachevote_1']) && !isset($options['cachevote_2'])) {
                    $options['cachevote_1']='';
                    $options['cachevote_2']='';
                }
                if( ( ($options['cachevote_1'] != '') && ($options['cachevote_2'] != '') ) && ( ($options['cachevote_1'] != '0') || ($options['cachevote_2'] != '6') ) && ( (!isset($options['cachenovote'])) || ($options['cachenovote'] != '1') ) )
                {
                    $sql_where[] = '`caches`.`score` BETWEEN \'' . XDb::xEscape($options['cachevote_1']) . '\' AND \'' . XDb::xEscape($options['cachevote_2']) . '\' AND `caches`.`votes` > 3';
                } else if ( ($options['cachevote_1'] != '') && ($options['cachevote_2'] != '') && ( ($options['cachevote_1'] != '0') || ($options['cachevote_2'] != '6') ) && isset($options['cachenovote']) && ($options['cachenovote'] == '1') )  {
                    $sql_where[] = '((`caches`.`score` BETWEEN \'' . XDb::xEscape($options['cachevote_1']) . '\' AND \'' . XDb::xEscape($options['cachevote_2']) . '\' AND `caches`.`votes` > 3) OR (`caches`.`votes` < 4))';
                }

                if(!isset($options['cachedifficulty_1']) && !isset($options['cachedifficulty_2'])) {
                    $options['cachedifficulty_1']='';
                    $options['cachedifficulty_2']='';
                }
                if((($options['cachedifficulty_1'] != '') && ($options['cachedifficulty_2'] != '')) && (($options['cachedifficulty_1'] != '1') || ($options['cachedifficulty_2'] != '5')))
                {
                    $sql_where[] = '`caches`.`difficulty` BETWEEN \'' . XDb::xEscape($options['cachedifficulty_1'] * 2) . '\' AND \'' . XDb::xEscape($options['cachedifficulty_2'] * 2) . '\'';
                }

                if(!isset($options['cacheterrain_1']) && !isset($options['cacheterrain_2'])) {
                    $options['cacheterrain_1']='';
                    $options['cacheterrain_2']='';
                }

                if((($options['cacheterrain_1'] != '') && ($options['cacheterrain_2'] != '')) && (($options['cacheterrain_1'] != '1') || ($options['cacheterrain_2'] != '5')))
                {
                    $sql_where[] = '`caches`.`terrain` BETWEEN \'' . XDb::xEscape($options['cacheterrain_1'] * 2) . '\' AND \'' . XDb::xEscape($options['cacheterrain_2'] * 2) . '\'';
                }

                if($options['cacherating'] > 0) {
                    $sql_where[] = '`caches`.`topratings` >= \'' . $options['cacherating'] .'\'';
                }

                //do the search
                $join = sizeof($sql_join) ? ' LEFT JOIN ' . implode(' AND ', $sql_join) : '';
                $group = sizeof($sql_group) ? ' GROUP BY ' . implode(', ', $sql_group) : '';
                $having = sizeof($sql_having) ? ' HAVING ' . implode(' AND ', $sql_having) : '';

                $queryFilter = 'SELECT ' . implode(',', $sql_select) .
                        ' FROM ' . implode(',', $sql_from) .
                        $join .
                        ' WHERE ' . implode(' AND ', $sql_where) .
                        $group .
                        $having;

            }
            else
            {
            }

            //go to final output preparation
            if (!file_exists($rootpath . 'lib/search.' . mb_strtolower($options['output']) . '.inc.php'))
            {
                tpl_set_var('tplname', $tplname);
                $tplname = 'error';
                tpl_set_var('error_msg', $outputformat_notexist);
            }
            else
            {
                //process and output the search result
                require($rootpath . 'lib/search.' . mb_strtolower($options['output']) . '.inc.php');
                exit;
            }
        }
        else
        {
            $options['show_all_countries'] = isset($_REQUEST['show_all_countries']) ? $_REQUEST['show_all_countries'] : 0;

            if (isset($_REQUEST['show_all_countries_submit']))
            {
                $options['show_all_countries'] = 1;
            }

            //return the search form
            if ($options['expert'] == 1)
            {
                //expert mode
                tpl_set_var('formmethod', 'post');
            }
            else
            {
                outputSearchForm($options);
                exit;
            }
        }
    }

    unset($dbc);
    unset($dbcSearch);

    /*
    //user logged in?
    }
    */
    tpl_BuildTemplate();

function outputSearchForm($options)
{
    global $stylepath, $usr, $error_plz, $error_locidnocoords, $error_ort, $error_noort, $error_nofulltext;
    global $default_lang, $search_all_countries, $cache_attrib_jsarray_line, $cache_attrib_img_line;
    global $lang, $language, $config;

    //simple mode (only one easy filter)
    $filters = file_get_contents($stylepath . '/search.simple.tpl.php');
    tpl_set_var('filters', $filters, false);
    tpl_set_var('formmethod', 'get');

    // checkboxes
    if (isset($options['sort']))
        $bBynameChecked = ($options['sort'] == 'byname');
    else
        $bBynameChecked = ($usr['userid'] == 0);
    tpl_set_var('byname_checked', ($bBynameChecked == true) ? ' checked="checked"' : '');

    if (isset($options['sort']))
        $bBydistanceChecked = ($options['sort'] == 'bydistance');
    else
        $bBydistanceChecked = ($usr['userid'] != 0);
    tpl_set_var('bydistance_checked', ($bBydistanceChecked == true) ? ' checked="checked"' : '');

    if (isset($options['sort']))
        $bBycreatedChecked = ($options['sort'] == 'bycreated');
    else
        $bBycreatedChecked = ($usr['userid'] == 0);
    tpl_set_var('bycreated_checked', ($bBycreatedChecked == true) ? ' checked="checked"' : '');

    tpl_set_var('hidopt_sort', $options['sort']);

    tpl_set_var('f_inactive_checked', ($options['f_inactive'] == 1) ? ' checked="checked"' : '');
    tpl_set_var('hidopt_inactive', ($options['f_inactive'] == 1) ? '1' : '0');

    tpl_set_var('f_ignored_disabled', ($usr['userid'] == 0) ? ' disabled="disabled"' : '');
    if ($usr['userid'] != 0)
        tpl_set_var('f_ignored_disabled', ($options['f_ignored'] == 1) ? ' checked="checked"' : '');
    tpl_set_var('hidopt_ignored', ($options['f_ignored'] == 1) ? '1' : '0');

    tpl_set_var('f_userfound_disabled', ($usr['userid'] == 0) ? ' disabled="disabled"' : '');
    if ($usr['userid'] != 0)
        tpl_set_var('f_userfound_disabled', ($options['f_userfound'] == 1) ? ' checked="checked"' : '');
    tpl_set_var('hidopt_userfound', ($options['f_userfound'] == 1) ? '1' : '0');

    tpl_set_var('f_userowner_disabled', ($usr['userid'] == 0) ? ' disabled="disabled"' : '');
    if ($usr['userid'] != 0)
        tpl_set_var('f_userowner_disabled', ($options['f_userowner'] == 1) ? ' checked="checked"' : '');
    tpl_set_var('hidopt_userowner', ($options['f_userowner'] == 1) ? '1' : '0');

    tpl_set_var('f_watched_disabled', ($usr['userid'] == 0) ? ' disabled="disabled"' : '');
    if ($usr['userid'] != 0)
        tpl_set_var('f_watched_disabled', ($options['f_watched'] == 1) ? ' checked="checked"' : '');
    tpl_set_var('hidopt_watched', ($options['f_watched'] == 1) ? '1' : '0');

    tpl_set_var('f_geokret_checked', ($options['f_geokret'] == 1) ? ' checked="checked"' : '');
    tpl_set_var('hidopt_geokret', ($options['f_geokret'] == 1) ? '1' : '0');

    if (isset($options['cacherating'])) {
        tpl_set_var('all_caches_checked', ($options['cacherating'] == 0) ? ' checked="checked"' : '');
        tpl_set_var('recommended_caches_checked', ($options['cacherating'] > 0) ? ' checked="checked"' : '');
        tpl_set_var('cache_min_rec', ($options['cacherating'] > 0) ? $options['cacherating'] : 0);
        tpl_set_var('min_rec_caches_disabled', ($options['cacherating'] == 0) ? ' disabled="disabled"' : '');
    }

    if (isset($options['cacherating']))
    {
        tpl_set_var('cacherating', htmlspecialchars($options['cacherating'], ENT_COMPAT, 'UTF-8'));
    }
    else
    {
        tpl_set_var('cacherating', '');
    }

    if (isset($options['region']))
    {
        tpl_set_var('region', htmlspecialchars($options['region'], ENT_COMPAT, 'UTF-8'));
    }
    else
    {
        tpl_set_var('region', '');
    }

    if (isset($options['country']))
    {
        tpl_set_var('country', htmlspecialchars($options['country'], ENT_COMPAT, 'UTF-8'));
    }
    else
    {
        tpl_set_var('country', '');
    }

    if (isset($options['cachetype']))
    {
        tpl_set_var('cachetype', htmlspecialchars($options['cachetype'], ENT_COMPAT, 'UTF-8'));
    }
    else
    {
        tpl_set_var('cachetype', '');
    }

    if (isset($options['cachesize_1']))
    {
        tpl_set_var('cachesize_1', htmlspecialchars($options['cachesize_1'], ENT_COMPAT, 'UTF-8'));
    }
    else
    {
        tpl_set_var('cachesize_1', '');
    }

    if (isset($options['cachesize_2']))
    {
        tpl_set_var('cachesize_2', htmlspecialchars($options['cachesize_2'], ENT_COMPAT, 'UTF-8'));
    }
    else
    {
        tpl_set_var('cachesize_2', '');
    }

    if (isset($options['cachesize_3']))
    {
        tpl_set_var('cachesize_3', htmlspecialchars($options['cachesize_3'], ENT_COMPAT, 'UTF-8'));
    }
    else
    {
        tpl_set_var('cachesize_3', '');
    }

    if (isset($options['cachesize_4']))
    {
        tpl_set_var('cachesize_4', htmlspecialchars($options['cachesize_4'], ENT_COMPAT, 'UTF-8'));
    }
    else
    {
        tpl_set_var('cachesize_4', '');
    }

    if (isset($options['cachesize_5']))
    {
        tpl_set_var('cachesize_5', htmlspecialchars($options['cachesize_5'], ENT_COMPAT, 'UTF-8'));
    }
    else
    {
        tpl_set_var('cachesize_5', '');
    }

    if (isset($options['cachesize_6']))
    {
        tpl_set_var('cachesize_6', htmlspecialchars($options['cachesize_6'], ENT_COMPAT, 'UTF-8'));
    }
    else
    {
        tpl_set_var('cachesize_6', '');
    }

    if (isset($options['cachesize_7']))
    {
        tpl_set_var('cachesize_7', htmlspecialchars($options['cachesize_7'], ENT_COMPAT, 'UTF-8'));
    }
    else
    {
        tpl_set_var('cachesize_7', '');
    }

    if (isset($options['cachevote_1']) && isset($options['cachevote_2']))
    {
        tpl_set_var('cachevote_1', htmlspecialchars($options['cachevote_1'], ENT_COMPAT, 'UTF-8'));
        tpl_set_var('cachevote_2', htmlspecialchars($options['cachevote_2'], ENT_COMPAT, 'UTF-8'));
    }
    else
    {
        tpl_set_var('cachevote_1', '');
        tpl_set_var('cachevote_2', '');
    }

    if (isset($options['cachenovote']))
    {
        tpl_set_var('cachenovote', htmlspecialchars($options['cachenovote'], ENT_COMPAT, 'UTF-8'));
    }
    else
    {
        tpl_set_var('cachenovote', '');
    }

    if (isset($options['cachedifficulty_1']) && isset($options['cachedifficulty_2']))
    {
        tpl_set_var('cachedifficulty_1', htmlspecialchars($options['cachedifficulty_1'], ENT_COMPAT, 'UTF-8'));
        tpl_set_var('cachedifficulty_2', htmlspecialchars($options['cachedifficulty_2'], ENT_COMPAT, 'UTF-8'));
    }
    else
    {
        tpl_set_var('cachedifficulty_1', '');
        tpl_set_var('cachedifficulty_2', '');
    }

    if (isset($options['cacheterrain_1']) && isset($options['cacheterrain_2']))
    {
        tpl_set_var('cacheterrain_1', htmlspecialchars($options['cacheterrain_1'], ENT_COMPAT, 'UTF-8'));
        tpl_set_var('cacheterrain_2', htmlspecialchars($options['cacheterrain_2'], ENT_COMPAT, 'UTF-8'));
    }
    else
    {
        tpl_set_var('cacheterrain_1', '');
        tpl_set_var('cacheterrain_2', '');
    }

    // cachename
    tpl_set_var('cachename', isset($options['cachename']) ? htmlspecialchars($options['cachename'], ENT_COMPAT, 'UTF-8') : '');

    // koordinaten
    if (!isset($options['lat_h']))
    {
        if ($usr !== false)
        {
            $rs = XDb::xSql('SELECT `latitude`, `longitude` FROM `user` WHERE `user_id`=\'' . XDb::xEscape($usr['userid']) . '\'');
            $record = XDb::xFetchArray($rs);
            $lon = $record['longitude'];
            $lat = $record['latitude'];
            XDb::xFreeResults($rs);

            if ($lon < 0)
            {
                tpl_set_var('lonE_sel', '');
                tpl_set_var('lonW_sel', ' selected="selected"');
                $lon = -$lon;
            }
            else
            {
                tpl_set_var('lonE_sel', ' selected="selected"');
                tpl_set_var('lonW_sel', '');
            }

            if ($lat < 0)
            {
                tpl_set_var('latN_sel', '');
                tpl_set_var('latS_sel', ' selected="selected"');
                $lat = -$lat;
            }
            else
            {
                tpl_set_var('latN_sel', ' selected="selected"');
                tpl_set_var('latS_sel', '');
            }

            $lon_h = floor($lon);
            $lat_h = floor($lat);
            $lon_min = ($lon - $lon_h) * 60;
            $lat_min = ($lat - $lat_h) * 60;

            tpl_set_var('lat_h', $lat_h);
            tpl_set_var('lon_h', $lon_h);
            tpl_set_var('lat_min', sprintf("%02.3f", $lat_min));
            tpl_set_var('lon_min', sprintf("%02.3f", $lon_min));
        }
        else
        {
            tpl_set_var('lat_h', '00');
            tpl_set_var('lon_h', '000');
            tpl_set_var('lat_min', '00.000');
            tpl_set_var('lon_min', '00.000');
            tpl_set_var('latN_sel', ' selected="selected"');
            tpl_set_var('latS_sel', '');
            tpl_set_var('lonE_sel', ' selected="selected"');
            tpl_set_var('lonW_sel', '');
        }
    }
    else
    {
        tpl_set_var('lat_h', isset($options['lat_h']) ? $options['lat_h'] : '00');
        tpl_set_var('lon_h', isset($options['lon_h']) ? $options['lon_h'] : '000');
        tpl_set_var('lat_min', isset($options['lat_min']) ? $options['lat_min'] : '00.000');
        tpl_set_var('lon_min', isset($options['lon_min']) ? $options['lon_min'] : '00.000');

        if ($options['lonEW'] == 'W')
        {
            tpl_set_var('lonE_sel', '');
            tpl_set_var('lonW_sel', 'selected="selected"');
        }
        else
        {
            tpl_set_var('lonE_sel', 'selected="selected"');
            tpl_set_var('lonW_sel', '');
        }

        if ($options['latNS'] == 'S')
        {
            tpl_set_var('latS_sel', 'selected="selected"');
            tpl_set_var('latN_sel', '');
        }
        else
        {
            tpl_set_var('latS_sel', '');
            tpl_set_var('latN_sel', 'selected="selected"');
        }
    }
    tpl_set_var('distance', isset($options['distance']) ? $options['distance'] : 20);

    if (!isset($options['unit'])) $options['unit'] = 'km';
    if ($options['unit'] == 'km')
    {
        tpl_set_var('sel_km', 'selected="selected"');
        tpl_set_var('sel_sm', '');
        tpl_set_var('sel_nm', '');
    }
    else if ($options['unit'] == 'sm')
    {
        tpl_set_var('sel_km', '');
        tpl_set_var('sel_sm', 'selected="selected"');
        tpl_set_var('sel_nm', '');
    }
    else if ($options['unit'] == 'nm')
    {
        tpl_set_var('sel_km', '');
        tpl_set_var('sel_sm', '');
        tpl_set_var('sel_nm', 'selected="selected"');
    }

    // plz
    tpl_set_var('plz', isset($options['plz']) ? htmlspecialchars($options['plz'], ENT_COMPAT, 'UTF-8') : '');
    tpl_set_var('ort', isset($options['ort']) ? htmlspecialchars($options['ort'], ENT_COMPAT, 'UTF-8') : '');

    // owner
    tpl_set_var('owner', isset($options['owner']) ? htmlspecialchars($options['owner'], ENT_COMPAT, 'UTF-8') : '');

    // finder
    tpl_set_var('finder', isset($options['finder']) ? htmlspecialchars($options['finder'], ENT_COMPAT, 'UTF-8') : '');

    //countryoptions
    $countriesoptions = $search_all_countries;
    $rs = XDb::xSql('SELECT `short` FROM `countries` WHERE `short` IN (SELECT DISTINCT `country` FROM `caches`) ');

    for ($i = 0; $i < XDb::xNumRows($rs); $i++)
    {
        $record = XDb::xFetchArray($rs);
        if ($record['short'] == $options['country'])
            $countriesoptions .= '<option value="' . htmlspecialchars($record['short'], ENT_COMPAT, 'UTF-8') . '" selected="selected">' . htmlspecialchars(tr($record['short']), ENT_COMPAT, 'UTF-8') . '</option>';
        else
            $countriesoptions .= '<option value="' . htmlspecialchars($record['short'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars(tr($record['short']), ENT_COMPAT, 'UTF-8') . '</option>';

        $countriesoptions .= "\n";
    }

    tpl_set_var('countryoptions', $countriesoptions);

    //regionoptions
    $regionsoptions = '<option value="" selected="selected">'.tr('all_regions').'</option>';
    tpl_set_var('regionoptions', $regionsoptions);


    // Typ skrzynki
    $cachetype_options = '';
                if(checkField('cache_type',$lang) )
                    $lang_db = XDb::xEscape($lang);
                else
                    $lang_db = "en";

    $rs = XDb::xSql("SELECT `id`, `$lang_db`, `icon_large` FROM `cache_type` ORDER BY `sort`");
    for ($i = 0; $i < XDb::xNumRows($rs); $i++)
    {
        $record = XDb::xFetchArray($rs);

        /*
        if ($record['id'] == $options['cachetype'])
            $cachetype_options .= '<option value="' . htmlspecialchars($record['id'], ENT_COMPAT, 'UTF-8') . '" selected="selected">' . htmlspecialchars($record[$default_lang], ENT_COMPAT, 'UTF-8') . '</option>';
        else
            $cachetype_options .= '<option value="' . htmlspecialchars($record['id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($record[$default_lang], ENT_COMPAT, 'UTF-8') . '</option>';
        */

        $c_rec_id = $record['id'] - 1;
        $cachetype_icon = $record['icon_large'];

//      if ($options['cachetype'][$c_rec_id] == '1') {
//          $cachetype_options .= '<input type="checkbox" name="cachetype_' . htmlspecialchars($record['id'], ENT_COMPAT, 'UTF-8') . '" value="1" id="l_cachetype_' . htmlspecialchars($record['id'], ENT_COMPAT, 'UTF-8') . '" class="checkbox" onclick="javascript:sync_options(this)" checked="checked" /><label for="l_cachetype_' . htmlspecialchars($record['id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($record[$default_lang], ENT_COMPAT, 'UTF-8') . '</label>';
//      } else {
//          $cachetype_options .= '<input type="checkbox" name="cachetype_' . htmlspecialchars($record['id'], ENT_COMPAT, 'UTF-8') . '" value="1" id="l_cachetype_' . htmlspecialchars($record['id'], ENT_COMPAT, 'UTF-8') . '" class="checkbox" onclick="javascript:sync_options(this)" /><label for="l_cachetype_' . htmlspecialchars($record['id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($record[$default_lang], ENT_COMPAT, 'UTF-8') . '</label>';
//      }

        $cachetype_icon = str_replace("mystery", "quiz", $cachetype_icon); // mystery is an outdated name, we use 'quiz' now :-)
        $cachetype_icon_bw = $cachetype_icon;
        $cachetype_icon    = str_replace(".png", "-i.png",    $cachetype_icon);
        $cachetype_icon_bw = str_replace(".png", "-i-bw.png", $cachetype_icon_bw);
        $cachetype_icon    = str_replace(".gif", "-i.png",    $cachetype_icon);
        $cachetype_icon_bw = str_replace(".gif", "-i-bw.png", $cachetype_icon_bw);


        $hidden_css = "position: absolute; visibility: hidden;"; // css required to hide an image

        // this marks saved user preference for searching, if 1, the cache is by default searched
        // and thus making the colour image visibile
        if (isset($options['cachetype'][$c_rec_id]) && $options['cachetype'][$c_rec_id] == '1') {
            $icon_hidden = "";
            $icon_bw_hidden = $hidden_css;
        } else {
            $icon_hidden = $hidden_css;
            $icon_bw_hidden = "";
        }


        $hidden_css = "position: absolute; visibility: hidden;";
        $cachetype_options .= '<img id="cachetype_' . htmlspecialchars($record['id'], ENT_COMPAT, 'UTF-8') . '"    src="' . htmlspecialchars($stylepath . "/images/" . $cachetype_icon   , ENT_COMPAT, 'UTF-8') . '" title="' . htmlspecialchars($record[$lang_db], ENT_COMPAT, 'UTF-8') . '" alt="' . htmlspecialchars($record[$lang_db], ENT_COMPAT, 'UTF-8') . '" onmousedown="javascript:switchCacheType(\'cachetype_' . htmlspecialchars($record['id'], ENT_COMPAT, 'UTF-8') . '\')" style="cursor: pointer;'.$icon_hidden.'" />';
        $cachetype_options .= '<img id="cachetype_' . htmlspecialchars($record['id'], ENT_COMPAT, 'UTF-8') . '_bw" src="' . htmlspecialchars($stylepath . "/images/" . $cachetype_icon_bw, ENT_COMPAT, 'UTF-8') . '" title="' . htmlspecialchars($record[$lang_db], ENT_COMPAT, 'UTF-8') . '" alt="' . htmlspecialchars($record[$lang_db], ENT_COMPAT, 'UTF-8') . '" onmousedown="javascript:switchCacheType(\'cachetype_' . htmlspecialchars($record['id'], ENT_COMPAT, 'UTF-8') . '\')" style="cursor: pointer;'.$icon_bw_hidden.'" />';
        if ($i == 2) { $cachetype_options .= '&nbsp;&nbsp;&nbsp;'; }
        $cachetype_options .= "\n";
    }

    tpl_set_var('cachetype_options', $cachetype_options);

    //Rozmiar skrzynki

    $cachesize_options = '';
                if(checkField('cache_size',$lang) )
                    $lang_db = XDb::xEscape($lang);
                else
                    $lang_db = "en";

    $rs = XDb::xSql("SELECT `id`, `$lang_db` FROM `cache_size` ORDER BY `id`");
    for ($i = 0; $i < XDb::xNumRows($rs); $i++)
    {
        $record = XDb::xFetchArray($rs);

        $cachesize_options .= '<input type="checkbox" name="cachesize_' . htmlspecialchars($record['id'], ENT_COMPAT, 'UTF-8') . '" value="1" id="l_cachesize_' . htmlspecialchars($record['id'], ENT_COMPAT, 'UTF-8') . '" class="checkbox" onclick="javascript:sync_options(this)" checked="checked" /><label for="l_cachesize_' . htmlspecialchars($record['id'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($record[$lang_db], ENT_COMPAT, 'UTF-8') . '</label>';

        $cachesize_options .= "\n";
    }

    tpl_set_var('cachesize_options', $cachesize_options);

    // gpxlogLimit
    if ($options['gpxLogLimit'] == false) tpl_set_var('gpxLogLimitUserChoice', 5);
    else tpl_set_var('gpxLogLimitUserChoice', $options['gpxLogLimit']);


function attr_jsline($tpl, $options, $id, $textlong, $iconlarge, $iconno, $iconundef, $category)
{
    $line = $tpl;

        $line = mb_ereg_replace('{id}', $id, $line);

        if (array_search($id, $options['cache_attribs']) === false)
        {
            if (array_search($id, $options['cache_attribs_not']) === false)
                $line = mb_ereg_replace('{state}', 0, $line);
            else
                $line = mb_ereg_replace('{state}', 2, $line);
        }
        else
            $line = mb_ereg_replace('{state}', 1, $line);

        $line = mb_ereg_replace('{text_long}', addslashes($textlong), $line);
        $line = mb_ereg_replace('{icon}', $iconlarge, $line);
        $line = mb_ereg_replace('{icon_no}', $iconno, $line);
        $line = mb_ereg_replace('{icon_undef}', $iconundef, $line);
        $line = mb_ereg_replace('{category}', $category, $line);

        return $line;
}

function attr_image($tpl, $options, $id, $textlong, $iconlarge, $iconno, $iconundef, $category)
{
    $line = $tpl;

    $line = mb_ereg_replace('{id}', $id, $line);
    $line = mb_ereg_replace('{text_long}', $textlong, $line);

    if (array_search($id, $options['cache_attribs']) === false)
    {
        if (array_search($id, $options['cache_attribs_not']) === false)
            $line = mb_ereg_replace('{icon}', $iconundef, $line);
        else
            $line = mb_ereg_replace('{icon}', $iconno, $line);
    }
    else
        $line = mb_ereg_replace('{icon}', $iconlarge, $line);
    return $line;
}

    // cache-attributes
    $attributes_jsarray = '';
    $attributes_img = '';
    $attributesCat2_img = '';


    // select attributes depend on specified language.
    $database = OcDb::instance();
    $query = "SELECT `id`, `text_long`, `icon_large`, `icon_no`, `icon_undef`, `category` FROM `cache_attrib` WHERE `language` LIKE :1 ORDER BY `id`";
    $s = $database->multiVariableQuery($query, strtoupper($lang));
    // if specified language is in database
    if($database->rowCount($s) <= 0) {
         // if we have not specified language in db, just use english.
        $s = $database->multiVariableQuery($query, 'EN');
    }
    $rs = $database->dbResultFetchAll($s);

    foreach ($rs as $record)
    {
        // icon specified
        $line = attr_jsline($cache_attrib_jsarray_line, $options, $record['id'], $record['text_long'], $record['icon_large'], $record['icon_no'], $record['icon_undef'], $record['category']);

        if ($attributes_jsarray != '') $attributes_jsarray .= ",\n";
        $attributes_jsarray .= $line;

        $line = attr_image($cache_attrib_img_line, $options, $record['id'], $record['text_long'], $record['icon_large'], $record['icon_no'], $record['icon_undef'], $record['category']);


        if ($record['category'] != 1)
            $attributesCat2_img .= $line;
        else
            $attributes_img .= $line;
    }
    $line = attr_jsline($cache_attrib_jsarray_line, $options, "99", tr("with_password"), $config['search-attr-icons']['password'][0], $config['search-attr-icons']['password'][1], $config['search-attr-icons']['password'][2], 0);
    $attributes_jsarray .= ",\n".$line;

    $line = attr_image($cache_attrib_img_line, $options, "99", tr("with_password"), $config['search-attr-icons']['password'][0], $config['search-attr-icons']['password'][1], $config['search-attr-icons']['password'][2], 0);
    $attributes_img .= $line;

    tpl_set_var('cache_attrib_list', $attributes_img);
    tpl_set_var('cache_attribCat2_list', $attributesCat2_img);
    tpl_set_var('attributes_jsarray', $attributes_jsarray);
    tpl_set_var('hidopt_attribs', implode(';', $options['cache_attribs']));
    tpl_set_var('hidopt_attribs_not', implode(';', $options['cache_attribs_not']));

    tpl_set_var('fulltext', '');
    tpl_set_var('ft_name_checked', 'checked="checked"');
    tpl_set_var('ft_desc_checked', '');
    tpl_set_var('ft_logs_checked', '');
    tpl_set_var('ft_pictures_checked', '');

    // fulltext options
    if ($options['searchtype'] == 'byfulltext')
    {
        if (!isset($options['fulltext'])) $options['fulltext'] = '';
        tpl_set_var('fulltext', htmlspecialchars($options['fulltext'], ENT_COMPAT, 'UTF-8'));

        if (isset($options['ft_name']) && $options['ft_name']==1)
            tpl_set_var('ft_name_checked', 'checked="checked"');
        else
            tpl_set_var('ft_name_checked', '');

        if (isset($options['ft_desc']) && $options['ft_desc']==1)
            tpl_set_var('ft_desc_checked', 'checked="checked"');
        else
            tpl_set_var('ft_desc_checked', '');

        if (isset($options['ft_logs']) && $options['ft_logs']==1)
            tpl_set_var('ft_logs_checked', 'checked="checked"');
        else
            tpl_set_var('ft_logs_checked', '');

        if (isset($options['ft_pictures']) && $options['ft_pictures']==1)
            tpl_set_var('ft_pictures_checked', 'checked="checked"');
        else
            tpl_set_var('ft_pictures_checked', '');
    }

    // errormeldungen
    tpl_set_var('ortserror', '');
    if (isset($options['error_plz']))
        tpl_set_var('ortserror', $error_plz);
    else if (isset($options['error_ort']))
        tpl_set_var('ortserror', $error_ort);
    else if (isset($options['error_locidnocoords']))
        tpl_set_var('ortserror', $error_locidnocoords);
    else if (isset($options['error_noort']))
        tpl_set_var('ortserror', $error_noort);

    tpl_set_var('fulltexterror', isset($options['error_nofulltext']) ? $error_nofulltext : '');

    tpl_BuildTemplate();
    exit;
}

function outputUniidSelectionForm($uniSql, $urlparams)
{
    global $tplname, $locline, $stylepath, $bgcolor1, $bgcolor2, $gns_countries;
    global $secondlocationname;

    require_once($stylepath . '/selectlocid.inc.php');

    unset($urlparams['queryid']);
    unset($urlparams['locid']);
    $urlparams['searchto'] = 'search' . $urlparams['searchtype'];
    unset($urlparams['searchtype']);

    $tplname = 'selectlocid';

    // urlparams zusammenbauen
    $urlparamString = '';
    foreach ($urlparams AS $name => $param)
    {
        // workaround for attribs
        if (is_array($param))
        {
            $pnew = '';
            foreach ($param AS $p)
                if ($pnew != '')
                    $pnew .= ';' . $p;
                else
                    $pnew .= $p;

            $param = $pnew;
        }

        if ($urlparamString != '')
            $urlparamString .= '&' . $name . '=' . urlencode($param);
        else
            $urlparamString = $name . '=' . urlencode($param);
    }
    $urlparamString .= '';

    XDb::xSql('CREATE TEMPORARY TABLE `uniids` ENGINE=MEMORY ' . $uniSql);
    XDb::xSql('ALTER TABLE `uniids` ADD PRIMARY KEY (`uni_id`)');

    // locidsite
    $locidsite = isset($_REQUEST['locidsite']) ? $_REQUEST['locidsite'] : 0;
    if (!is_numeric($locidsite)) $locidsite = 0;

    $rsCount = XDb::xSql('SELECT COUNT(*) `count` FROM `uniids`');
    $rCount = XDb::xFetchArray($rsCount);
    XDb::xFreeResults($rsCount);

    tpl_set_var('resultscount', $rCount['count']);

    // seitennummern erstellen
    $maxsite = ceil($rCount['count'] / 20) - 1;
    $pages = '';

    if ($locidsite > 0)
        $pages .= '<a href="search.php?' . $urlparamString . '&locidsite=0">{first_img}</a> <a href="search.php?' . $urlparamString . '&locidsite=' . ($locidsite - 1) . '">{prev_img}</a> ';
    else
        $pages .= '{first_img_inactive} {prev_img_inactive} ';

    $frompage = $locidsite - 3;
    if ($frompage < 1) $frompage = 1;

    $topage = $frompage + 8;
    if ($topage > $maxsite) $topage = $maxsite + 1;

    for ($i = $frompage; $i <= $topage; $i++)
    {
        if (($locidsite + 1) == $i)
        {
            $pages .= '<b>' . $i . '</b> ';
        }
        else
        {
            $pages .= '<a href="search.php?' . $urlparamString . '&locidsite=' . ($i - 1) . '">' . $i . '</a> ';
        }
    }

    if ($locidsite < $maxsite)
        $pages .= '<a href="search.php?' . $urlparamString . '&locidsite=' . ($locidsite + 1) . '">{next_img}</a> <a href="search.php?' . $urlparamString . '&locidsite=' . $maxsite . '">{last_img}</a> ';
    else
        $pages .= '{next_img_inactive} {last_img_inactive} ';

        $pages = mb_ereg_replace('{prev_img}', $prev_img, $pages);

        $pages = mb_ereg_replace('{next_img}', $next_img, $pages);
        $pages = mb_ereg_replace('{last_img}', $last_img, $pages);
        $pages = mb_ereg_replace('{first_img}', $first_img, $pages);

        $pages = mb_ereg_replace('{prev_img_inactive}', $prev_img_inactive, $pages);
        $pages = mb_ereg_replace('{next_img_inactive}', $next_img_inactive, $pages);
        $pages = mb_ereg_replace('{first_img_inactive}', $first_img_inactive, $pages);
        $pages = mb_ereg_replace('{last_img_inactive}', $last_img_inactive, $pages);

    tpl_set_var('pages', $pages);

    $rs = XDb::xSql('SELECT `gns_locations`.`rc` `rc`, `gns_locations`.`cc1` `cc1`, `gns_locations`.`admtxt1` `admtxt1`, `gns_locations`.`admtxt2` `admtxt2`, `gns_locations`.`admtxt3` `admtxt3`, `gns_locations`.`admtxt4` `admtxt4`, `gns_locations`.`uni` `uni_id`, `gns_locations`.`lon` `lon`, `gns_locations`.`lat` `lat`, `gns_locations`.`full_name` `full_name`, `uniids`.`olduni` `olduni` FROM `gns_locations`, `uniids` WHERE `uniids`.`uni_id`=`gns_locations`.`uni` ORDER BY `gns_locations`.`full_name`, `gns_locations`.`admtxt1` ASC LIMIT ' . ($locidsite * 20) . ', 20');

    $nr = $locidsite * 20 + 1;
    $locations = '';
    while ($r = XDb::xFetchArray($rs))
    {
        $thislocation = $locline;

        // locationsdings zusammenbauen
        $locString = '';
        if ($r['admtxt1'] != '')
        {
            if ($locString != '') $locString .= ' &gt; ';
            $locString .= htmlspecialchars($r['admtxt1'], ENT_COMPAT, 'UTF-8');
        }
        if ($r['admtxt2'] != '')
        {
            if ($locString != '') $locString .= ' &gt; ';
            $locString .= htmlspecialchars($r['admtxt2'], ENT_COMPAT, 'UTF-8');
        }
/*      if ($r['admtxt3'] != '')
        {
            if ($locString != '') $locString .= ' &gt; ';
            $locString .= htmlspecialchars($r['admtxt3'], ENT_COMPAT, 'UTF-8');
        }
*/      if ($r['admtxt4'] != '')
        {
            if ($locString != '') $locString .= ' &gt; ';
            $locString .= htmlspecialchars($r['admtxt4'], ENT_COMPAT, 'UTF-8');
        }

        $thislocation = mb_ereg_replace('{parentlocations}', $locString, $thislocation);

        // koordinaten ermitteln
        $coordString = help_latToDegreeStr($r['lat']) . ' ' . help_lonToDegreeStr($r['lon']);
        $thislocation = mb_ereg_replace('{coords}', htmlspecialchars($coordString, ENT_COMPAT, 'UTF-8'), $thislocation);

        if ($r['olduni'] != 0)
        {
            // der alte name wurde durch den native-wert ersetzt
            $thissecloc = $secondlocationname;

            $r['olduni'] = $r['olduni'] + 0;
            $rsSecLoc = XDb::xSql('SELECT full_name FROM gns_locations WHERE uni=' . $r['olduni']);
            $rSecLoc = XDb::xFetchArray($rsSecLoc);
            $thissecloc = mb_ereg_replace('{secondlocationname}', htmlspecialchars($rSecLoc['full_name'], ENT_COMPAT, 'UTF-8'), $thissecloc);
            XDb::xFreeResults($rsSecLoc);

            $thislocation = mb_ereg_replace('{secondlocationname}', $thissecloc, $thislocation);
        }
        else
            $thislocation = mb_ereg_replace('{secondlocationname}', '', $thislocation);

        $thislocation = mb_ereg_replace('{locationname}', htmlspecialchars($r['full_name'], ENT_COMPAT, 'UTF-8'), $thislocation);
        $thislocation = mb_ereg_replace('{urlparams}', $urlparamString . '&locid={locid}', $thislocation);
        $thislocation = mb_ereg_replace('{locid}', urlencode($r['uni_id']), $thislocation);
        $thislocation = mb_ereg_replace('{nr}', $nr, $thislocation);

        if ($nr % 2)
            $thislocation = mb_ereg_replace('{bgcolor}', $bgcolor1, $thislocation);
        else
            $thislocation = mb_ereg_replace('{bgcolor}', $bgcolor2, $thislocation);

        $nr++;
        $locations .= $thislocation . "\n";
    }
    XDb::xFreeResults($rs);

    tpl_set_var('locations', $locations);

    tpl_BuildTemplate();
    exit;
}

function outputLocidSelectionForm($locSql, $urlparams)
{
    global $tplname, $locline, $stylepath, $bgcolor1, $bgcolor2;

    require_once($stylepath . '/selectlocid.inc.php');

    unset($urlparams['queryid']);
    unset($urlparams['locid']);
    $urlparams['searchto'] = 'search' . $urlparams['searchtype'];
    unset($urlparams['searchtype']);

    $tplname = 'selectlocid';

    // urlparams zusammenbauen
    $urlparamString = '';
    foreach ($urlparams AS $name => $param)
    {
        // workaround for attribs
        if (is_array($param))
        {
            $pnew = '';
            foreach ($param AS $p)
                if ($pnew != '')
                    $pnew .= ';' . $p;
                else
                    $pnew .= $p;

            $param = $pnew;
        }

        if ($urlparamString != '')
            $urlparamString .= '&' . $name . '=' . urlencode($param);
        else
            $urlparamString = $name . '=' . urlencode($param);
    }
    $urlparamString .= '&locid={locid}';

    XDb::xSql('CREATE TEMPORARY TABLE `locids` ENGINE=MEMORY ' . $locSql);
    XDb::xSql('ALTER TABLE `locids` ADD PRIMARY KEY (`loc_id`)');

    $rs = XDb::xSql('SELECT `geodb_textdata`.`loc_id` `loc_id`, `geodb_textdata`.`text_val` `text_val` FROM `geodb_textdata`, `locids` WHERE `locids`.`loc_id`=`geodb_textdata`.`loc_id` AND `geodb_textdata`.`text_type`=500100000 ORDER BY `text_val`');

    $nr = 1;
    $locations = '';
    while ($r = XDb::xFetchArray($rs))
    {
        $thislocation = $locline;

        // locationsdings zusammenbauen
        $locString = '';
        $land = landFromLocid($r['loc_id']);
        if ($land != '') $locString .= htmlspecialchars($land, ENT_COMPAT, 'UTF-8');

        $rb = regierungsbezirkFromLocid($r['loc_id']);
        if ($rb != '') $locString .= ' &gt; ' . htmlspecialchars($rb, ENT_COMPAT, 'UTF-8');

        $lk = landkreisFromLocid($r['loc_id']);
        if ($lk != '') $locString .= ' &gt; ' . htmlspecialchars($lk, ENT_COMPAT, 'UTF-8');

        $thislocation = mb_ereg_replace('{parentlocations}', $locString, $thislocation);

        // koordinaten ermitteln
        $r['loc_id'] = $r['loc_id'] + 0;
        $rsCoords = XDb::xSql('SELECT `lon`, `lat` FROM `geodb_coordinates` WHERE loc_id=' . $r['loc_id'] . ' LIMIT 1');
        if ($rCoords = XDb::xFetchArray($rsCoords))
            $coordString = help_latToDegreeStr($rCoords['lat']) . ' ' . help_lonToDegreeStr($rCoords['lon']);
        else
            $coordString = '[keine Koordinaten vorhanden]';

        $thislocation = mb_ereg_replace('{coords}', htmlspecialchars($coordString, ENT_COMPAT, 'UTF-8'), $thislocation);
        $thislocation = mb_ereg_replace('{locationname}', htmlspecialchars($r['text_val'], ENT_COMPAT, 'UTF-8'), $thislocation);
        $thislocation = mb_ereg_replace('{urlparams}', $urlparamString, $thislocation);
        $thislocation = mb_ereg_replace('{locid}', urlencode($r['loc_id']), $thislocation);
        $thislocation = mb_ereg_replace('{nr}', $nr, $thislocation);
        $thislocation = mb_ereg_replace('{secondlocationname}', '', $thislocation);

        if ($nr % 2)
            $thislocation = mb_ereg_replace('{bgcolor}', $bgcolor1, $thislocation);
        else
            $thislocation = mb_ereg_replace('{bgcolor}', $bgcolor2, $thislocation);

        $nr++;
        $locations .= $thislocation . "\n";
    }
    tpl_set_var('locations', $locations);

    tpl_set_var('resultscount', XDb::xNumRows($rs));
    tpl_set_var('pages', $first_img_inactive.' '.$prev_img_inactive.' 1 '.$next_img_inactive.' '.$last_img_inactive);
    tpl_BuildTemplate();
    exit;
}
?>
