<?php

class OpensprawdzaczSetup

/**
 * initial setup - setting default values used in OpenSprawdzacz
 */
{

    function __construct()
    {
        $this->scriptname = 'opensprawdzacz.php';

        $this->ile_prob = 10;        // declaration how many times user can try his answer per hour/session
        $this->limit_czasu = 60;     // [in minutes] - time which must elapse until next guess is possible.

        $this->caches_on_page = 25;   // how many caches is displayed in cache list at one time (pagination)
    }

}

// end of init Opensprawdzacz setup.

class convertLangLat
{

    var $CoordsDecimal;

    function __construct($degree, $minutes)
    {
        $this->CoordsDecimal = $degree + $minutes / 60;
    }

}

class OpensprawdzaczCore
{

    public function BruteForceCheck($OpensprawdzaczSetup)
    {
        tpl_set_var("sekcja_3_start", '');
        tpl_set_var("sekcja_3_stop", '');
        tpl_set_var("sekcja_2_start", '<!--');
        tpl_set_var("sekcja_2_stop", '-->');
        tpl_set_var("sekcja_1_start", '<!--');
        tpl_set_var("sekcja_1_stop", '-->');
        tpl_set_var("ile_prob", $OpensprawdzaczSetup->ile_prob);
        tpl_set_var("ile_czasu", $OpensprawdzaczSetup->limit_czasu);

        /**
         *  check how many times user tried to guess answer
         *   (anti brutal force)
         */
        if (isset($_SESSION['opensprawdzacz_licznik'])) {
            if ($_SESSION['opensprawdzacz_licznik'] >= $OpensprawdzaczSetup->ile_prob) {
                $czas_ostatniej_proby = $_SESSION['opensprawdzacz_czas'];
                $czas_teraz = date('U');
                $czas_jaki_uplynal = $czas_teraz - $czas_ostatniej_proby;
                tpl_set_var("czasss1", $czas_jaki_uplynal);
                if ($czas_jaki_uplynal > $OpensprawdzaczSetup->limit_czasu * 60) {
                    $_SESSION['opensprawdzacz_licznik'] = 1;
                    $_SESSION['opensprawdzacz_czas'] = $czas_teraz;
                } else {
                    // $_SESSION['opensprawdzacz_czas'] = date('U');
                    $czas_jaki_uplynal = round(60 - ($czas_jaki_uplynal / 60));
                    tpl_set_var("licznik_zgadywan", $_SESSION["opensprawdzacz_licznik"]);
                    tpl_set_var("test1", tr(os_zgad));
                    tpl_set_var("wynik", '');
                    tpl_set_var("ikonka_yesno", '<image src="tpl/stdstyle/images/blue/opensprawdzacz_stop.png" />');
                    tpl_set_var("sekcja_4_start", '');
                    tpl_set_var("sekcja_4_stop", '');
                    tpl_set_var("twoje_ws", tr('os_ma_max') . ' ' . $ile_prob . ' ' . tr('os_ma_na') . ' ' . $limit_czasu . ' ' . tr('os_godzine') . '<br /> ' . tr('os_mus') . ' ' . $czas_jaki_uplynal . ' ' . tr('os_minut_end'));
                    tpl_set_var("save_mod_coord", '');
                    $this->endzik();
                    // goto endzik;
                }
            } else {
                tpl_set_var("sekcja_4_start", '<!--');
                tpl_set_var("sekcja_4_stop", '-->');
                $czasss = $_SESSION['opensprawdzacz_czas'];
                // tpl_set_var("czasss1", $czasss);
                $_SESSION['opensprawdzacz_licznik'] = $_SESSION['opensprawdzacz_licznik'] + 1;
                $_SESSION['opensprawdzacz_czas'] = date('U');
                $czasss = ($_SESSION['opensprawdzacz_czas'] - $czasss);
                tpl_set_var("licznik_zgadywan", $_SESSION["opensprawdzacz_licznik"]);
                tpl_set_var("czasss1", $czasss);
                // tpl_set_var("czasss2", $_SESSION['opensprawdzacz_czas']);
            }
        } else {
            $_SESSION['opensprawdzacz_licznik'] = 1;
            tpl_set_var("licznik_zgadywan", $_SESSION["opensprawdzacz_licznik"]);
            tpl_set_var("sekcja_4_start", '<!--');
            tpl_set_var("sekcja_4_stop", '-->');
        }
    }

    public function CoordsComparing($opt)
    {

        // get data from post.
        $stopnie_N = mysql_real_escape_string($_POST['stopnie_N']);
        $minuty_N = mysql_real_escape_string($_POST['minuty_N']);
        $stopnie_E = mysql_real_escape_string($_POST['stopnie_E']);
        $minuty_E = mysql_real_escape_string($_POST['minuty_E']);
        $cache_id = mysql_real_escape_string($_POST['cacheid']);

        if ($stopnie_N == '')
            $stopnie_N = 0;
        if ($stopnie_E == '')
            $stopnie_E = 0;
        if ($minuty_N == '')
            $minuty_N = 0;
        if ($minuty_E == '')
            $minuty_E = 0;

        // converting from HH MM.MMM to DD.DDDDDD

        $wspolN = new convertLangLat($stopnie_N, $minuty_N);
        $wspolE = new convertLangLat($stopnie_E, $minuty_E);

        //setting long & lat. signs
        if ($stopnie_N >= 0) {
            $NorS = "N";
        } else {
            $NorS = "S";
        };
        if ($stopnie_E >= 0) {
            $EorW = "E";
        } else {
            $EorW = "W";
        };

        // geting data from database
        $conn = new PDO("mysql:host=" . $opt['db']['server'] . ";dbname=" . $opt['db']['name'], $opt['db']['username'], $opt['db']['password']);
        $pyt = "SELECT `waypoints`.`wp_id`,
        `waypoints`.`type`,
        `waypoints`.`longitude`,
        `waypoints`.`latitude`,
        `waypoints`.`status`,
        `waypoints`.`type`,
        `waypoints`.`opensprawdzacz`,
        `opensprawdzacz`.`proby`,
        `opensprawdzacz`.`sukcesy`,
        `caches`.`type`
        FROM   `waypoints`, `opensprawdzacz`, `caches`
        WHERE  `waypoints`.`cache_id`='$cache_id'
        AND `waypoints`.`opensprawdzacz` = 1
        AND `waypoints`.`type` = 3
        AND `waypoints`.`cache_id`= `opensprawdzacz`.`cache_id`
        AND `waypoints`.`cache_id`= `caches`.`cache_id`
        ";

        $dane = $conn->query($pyt);
        $dane = $dane->fetch(PDO::FETCH_ASSOC);

        $licznik_prob = $dane['proby'] + 1;

        $wspolrzedneN_wzorcowe = $dane['latitude'];
        $wspolrzedneE_wzorcowe = $dane['longitude'];


        // comparing data from post with data from database
        if (
                (($wspolrzedneN_wzorcowe - $wspolN->CoordsDecimal) < 0.00001) &&
                (($wspolrzedneN_wzorcowe - $wspolN->CoordsDecimal) > -0.00001) &&
                (($wspolrzedneE_wzorcowe - $wspolE->CoordsDecimal) < 0.00001) &&
                (($wspolrzedneE_wzorcowe - $wspolE->CoordsDecimal) > -0.00001)
        ) {
            //puzzle solved - resukt ok
            $licznik_sukcesow = $dane['sukcesy'] + 1;

            try {
                $pdo = new PDO("mysql:host=" . $opt['db']['server'] . ";dbname=" . $opt['db']['name'], $opt['db']['username'], $opt['db']['password']);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $updateCounters = $pdo->exec("UPDATE `opensprawdzacz` SET `proby`=" . $licznik_prob . ",`sukcesy`=" . $licznik_sukcesow . "  WHERE `cache_id` = " . $cache_id);
            } catch (PDOException $e) {
                echo "Error PDO Library: ($OpensprawdzaczSetup->scriptname) " . $e->getMessage();
                exit;
            }


            if ($dane['type'] == 7) {  //only for quiz type for time being
                $post_viewcache_form = '<form name="post_coord" action="viewcache.php?cacheid=' . $cache_id . '" method="post">
                                            <input type="submit" name="modCoords" value="' . tr('os_modify_coords_button') . '" />
                                            <input type="hidden" name="coordmod_lat_degree" value="' . $stopnie_N . '"/>
                                            <input type="hidden" name="coordmod_lon_degree" value="' . $stopnie_E . '"/>
                                            <input type="hidden" name="coordmod_lat" value="' . $minuty_N . '"/>
                                            <input type="hidden" name="coordmod_lon" value="' . $minuty_E . '"/>
                                            <input type="hidden" name="coordmod_latNS" value="' . $NorS . '"/>
                                            <input type="hidden" name="coordmod_lonEW" value="' . $EorW . '"/>
                                            <input type="hidden" name="save_requester" value="OpenChecker"/>
                                            </form>';
            } else {
                $post_viewcache_form = '';
            };

            tpl_set_var("test1", tr('os_sukces'));
            tpl_set_var("ikonka_yesno", '<image src="tpl/stdstyle/images/blue/opensprawdzacz_tak.png" />');
            tpl_set_var("save_mod_coord", $post_viewcache_form);
        } else {
            //puzzle not solved - restult wrong

            try {
                $pdo = new PDO("mysql:host=" . $opt['db']['server'] . ";dbname=" . $opt['db']['name'], $opt['db']['username'], $opt['db']['password']);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $updateCounters = $pdo->exec("UPDATE `opensprawdzacz` SET `proby`='$licznik_prob'  WHERE `cache_id` = $cache_id");
            } catch (PDOException $e) {
                echo "Error PDO Library: ($OpensprawdzaczSetup->scriptname) " . $e->getMessage();
                exit;
            }
            tpl_set_var("test1", tr('os_fail'));
            tpl_set_var("ikonka_yesno", '<image src="tpl/stdstyle/images/blue/opensprawdzacz_nie.png" />');
            tpl_set_var("save_mod_coord", '');
        }
        //tpl_set_var("wynik", $wspolrzedneN.'/'.$wspolrzedneN_wzorcowe.'<br>'.$wspolrzedneE.'/'. $wspolrzedneE_wzorcowe);
        tpl_set_var("wynik", '');



        // tpl_set_var("wsp_NS", );
        // tpl_set_var("wsp_EW", );
        tpl_set_var("twoje_ws", tr('os_twojews') . '<b> ' . $NorS . ' ' . $stopnie_N . '° ' . $minuty_N . '</b>/<b> ' . $EorW . ' ' . $stopnie_E . '° ' . $minuty_E . '</b>');
        tpl_set_var("cache_id", $cache_id);

        $this->endzik();
        // goto endzik;
    }

    public function DisplayAllOpensprawdzaczCaches($OpensprawdzaczSetup, $opt)
    {
        /**
         * Displays initial form for cache waypoint (OPXXXX) input
         *
         * and
         *
         * display list of caches in Opensprawdzacz.
         */
        /**
         *  if isset $_GET['op_keszynki'] means that user entered cache OP, and want search for this
         *  cache through Opensprawdzacz.
         *  This part get cache waypoint from url, check if cache owner allow specified cahe for check by
         *  OpenSprawdzacz
         *
         */
        if (isset($_GET['op_keszynki'])) {
            $this->cache_wp = mysql_real_escape_string($_GET['op_keszynki']);
            $this->cache_wp = strtoupper($this->cache_wp);
        } else {
            $formularz = '
                    <form action="' . $OpensprawdzaczSetup->scriptname . '" method="get">
                    ' . tr('os_podaj_waypoint') . ':
                            <input type="text" name="op_keszynki" maxlength="6"/>
                            <button type="submit" name="przeslanie_waypointa" value="' . tr('submit') . '" style="font-size:14px;width:160px"><b>' . tr('submit') . '</b></button>
                    </form>
                                    ';


            if (isset($_GET['sort'])) {
                $sort_tmp = mysql_real_escape_string($_GET['sort']);
                switch ($sort_tmp) {
                    case 'autor':
                        $sortowanie = '`user`.`username`';
                        break;
                    case 'nazwa':
                        $sortowanie = '`caches`.`name`';
                        break;
                    case 'wpt':
                        $sortowanie = '`caches`.`wp_oc`';
                        break;
                    case 'szczaly':
                        $sortowanie = '`opensprawdzacz`.`proby`';
                        break;
                    case 'sukcesy':
                        $sortowanie = '`opensprawdzacz`.`sukcesy`';
                        break;

                    default:
                        $sortowanie = '`caches`.`name`';
                        break;
                }
            } else
                $sortowanie = '`caches`.`name`';


            $zapytajka = "

        SELECT `waypoints`.`cache_id`,
        `waypoints`.`type`,
        `waypoints`.`stage`,
        `waypoints`.`desc`,
        `caches`.`name`,
        `caches`.`wp_oc`,
        `caches`.`user_id`,
        `caches`.`type`,
        `caches`.`status`,
        `user`.`username`,
        `cache_type`.`sort`,
        `cache_type`.`icon_small`,
        `opensprawdzacz`.`proby`,
        `opensprawdzacz`.`sukcesy`
        FROM   `waypoints`
        LEFT JOIN   `opensprawdzacz`
        ON   `waypoints`.`cache_id` = `opensprawdzacz`.`cache_id`,
        `caches`, `user`, `cache_type`
        WHERE   `waypoints`.`opensprawdzacz` = 1
        AND   `waypoints`.`type` = 3
        AND   `caches`.`type` = `cache_type`.`id`
        AND   `caches`.`user_id` = `user`.`user_id`
        AND   `waypoints`.`cache_id` = `caches`.`cache_id`
        ORDER BY   $sortowanie
        LIMIT   0, 1000

        ";


            $status = array(
                '1' => '<img src="tpl/stdstyle/images/log/16x16-found.png" border="0" alt="Gotowa do szukania">',
                '2' => '<img src="tpl/stdstyle/images/log/16x16-temporary.png" border="0" alt="Tymczasowo niedost�pna">',
                '3' => '<img src="tpl/stdstyle/images/log/16x16-dnf.png" border="0" alt="zarchiwizowana">',
                '4' => '<img src="tpl/stdstyle/images/log/16x16-temporary.png" border="0" alt="Ukryta do czasu weryfikacji">',
                '5' => '<img src="tpl/stdstyle/images/log/16x16-temporary.png" border="0" alt="jeszcze niedost�pna">',
                '6' => '<img src="tpl/stdstyle/images/log/16x16-dnf.png" border="0" alt="Zablokowana przez COG">'
            );

            $conn = new PDO("mysql:host=" . $opt['db']['server'] . ";dbname=" . $opt['db']['name'], $opt['db']['username'], $opt['db']['password']);
            $conn->query('SET CHARSET utf8');
            $keszynki_opensprawdzacza = $conn->query($zapytajka)->fetchAll();
            $ile_keszynek = count($keszynki_opensprawdzacza);


            $pag = new Pagination();
            // $dane = array("hej","dupa","laska", "scierwo");

            $numbers = $pag->Paginate($keszynki_opensprawdzacza, $OpensprawdzaczSetup->caches_on_page);
            $result = $pag->fetchResult();
            /*
              foreach ($result as $r)
              {
              echo "<div>aa$r</div>";
              }
             */
            $paginacja = ' ';
            if (isset($_GET["sort"]))
                $sort = '&sort=' . $_GET["sort"];
            else
                $sort = '';

            if (isset($_GET["page"]))
                $tPage = sql_escape($_GET["page"]);
            else
                $tPage = 1;
            if ($tPage > 1)
                $paginacja .= '<a href="' . $OpensprawdzaczSetup->scriptname . '?page=' . ($num - 1) . $sort . '">[<' . tr('os_f02') . ']</a> ';
            foreach ($numbers as $num) {
                if ($num == $tPage)
                    $paginacja .= '<b>[' . $num . ']</b>';
                else
                    $paginacja .= '<a href="' . $OpensprawdzaczSetup->scriptname . '?page=' . $num . $sort . '">[' . $num . ']</a> ';
            }
            if ($tPage < count($numbers))
                $paginacja .= '<a href="' . $OpensprawdzaczSetup->scriptname . '?page=' . ($tPage + 1) . $sort . '">[' . tr('os_f01') . ' &#62;]</a> ';


            $tabelka_keszynek = '';
            $proby = 0;
            $trafienia = 0;

            // foreach ($keszynki_opensprawdzacza as $dane_keszynek )
            foreach ($result as $dane_keszynek) {
                // $dane_keszynek = mysql_fetch_array($keszynki_opensprawdzacza);
                $proby = $proby + $dane_keszynek['proby'];
                $trafienia = $trafienia + $dane_keszynek['sukcesy'];

                if (($dane_keszynek['status'] == 1) || ($dane_keszynek['status'] == 2))
                    $tabelka_keszynek .= '
                            <tr>
        <td><a class="links" href="viewcache.php?wp=' . $dane_keszynek['wp_oc'] . '">' . $dane_keszynek['wp_oc'] . '</a></td>
        <td><a class="links" href="' . $OpensprawdzaczSetup->scriptname . '?op_keszynki=' . $dane_keszynek['wp_oc'] . '"> ' . $dane_keszynek['name'] . '</a> </td>
        <td><a href="viewcache.php?wp=' . $dane_keszynek['wp_oc'] . '"><img src="tpl/stdstyle/images/' . $dane_keszynek['icon_small'] . '" /></a></td>
        <td align="center">' . $status[$dane_keszynek['status']] . '</td>
        <td><a href="viewprofile.php?userid=' . $dane_keszynek['user_id'] . '">' . $dane_keszynek['username'] . '</td>
                <td align="center">' . $dane_keszynek['proby'] . '</td>
                            <td align="center">' . $dane_keszynek['sukcesy'] . '</td>
                        </tr>';
            }

            $tabelka_keszynek .= '
                <tr><td colspan="7"><img src="tpl/stdstyle/images/blue/dot_blue.png" height="1" width="100%"/></td></tr><tr>
                    <td><img src="/tpl/stdstyle/images/misc/16x16-info.png" /></td>
                    <td>' . tr('os_f00') . ': </td>
                    <td>' . $ile_keszynek . '</td>
                    <td align="center">
                        ' . $status[1] . '<br />' . $status[2] . '
                    </td>
                                <td>
                                (' . tr('log_type_available') . ')<br />
                        (' . tr('temp_unavailables') . ')
                    </td>
                    <td align="center">' . $proby . '</td>
                    <td align="center">' . $trafienia . '</td>
                </tr>
            </table>';

            $tabelka_keszynek .= '<br /><p align="center">' . $paginacja . '</p>';

            tpl_set_var("sekcja_1_start", '');
            tpl_set_var("sekcja_1_stop", '');
            tpl_set_var("sekcja_2_start", '<!--');
            tpl_set_var("sekcja_2_stop", '-->');
            tpl_set_var("sekcja_3_start", '<!--');
            tpl_set_var("sekcja_3_stop", '-->');
            tpl_set_var("sekcja_4_start", '<!--');
            tpl_set_var("sekcja_4_stop", '-->');
            tpl_set_var("sekcja_formularz_opensprawdzacza_start", '<!--');
            tpl_set_var("sekcja_formularz_opensprawdzacza_stop", '');
            tpl_set_var("formularz", $formularz);
            tpl_set_var("keszynki", $tabelka_keszynek);

            $this->endzik();
        }
    }

    public function endzik()
    {
        tpl_BuildTemplate();
        exit;
    }

}

?>