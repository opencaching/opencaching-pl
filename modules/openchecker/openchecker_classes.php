<?php

use Utils\Database\XDb;
use lib\Objects\GeoCache\GeoCache;
use lib\Objects\GeoCache\Waypoint;

class OpenCheckerSetup {

/**
 * initial setup - setting default values used in OpenChecker
 */

    var $scriptname;
    var $count_limit;
    var $time_limit;
    var $caches_on_page;
    var $show_wpt_desc;

    function __construct() {
        $this->scriptname = 'openchecker.php';
        include __DIR__.'/../../lib/settings.inc.php';

        // Limit number of checks
        // how many times a user can try his answer
        $this->count_limit = $config['module']['openchecker']['limit'];
        // Time period for checks limit (minutes)
        // Minimum time that must elapse before next attempt is allowed.
        $this->time_limit = $config['module']['openchecker']['time'];
        // how many caches per page (list of caches with openchecker)
        $this->caches_on_page = $config['module']['openchecker']['page'];
        // Show final waypoint description when user got correct answer?
        $this->show_wpt_desc = $config['module']['openchecker']['show_final'];
    }
}

// end of init OpenChecker setup.

class convertLongLat {

    var $CoordsDecimal;

    function __construct($degree, $minutes) {
        $this->CoordsDecimal = $degree + $minutes / 60;
    }
}

class OpenCheckerCore {

    public function BruteForceCheck($OpenCheckerSetup) {
        tpl_set_var("section_3_start", '');
        tpl_set_var("section_3_stop", '');
        tpl_set_var("section_2_start", '<!--');
        tpl_set_var("section_2_stop", '-->');
        tpl_set_var("section_1_start", '<!--');
        tpl_set_var("section_1_stop", '-->');
        tpl_set_var("count_limit", $OpenCheckerSetup->count_limit);
        tpl_set_var("time_limit", $OpenCheckerSetup->time_limit);

        /**
         *  check how many times user tried to guess answer
         *   (anti brute force)
         */
        $now = date('U');
        if (isset($_SESSION['openchecker_counter'])) {
            // Too many attempts?
            if ($_SESSION['openchecker_counter'] >= $OpenCheckerSetup->count_limit) {
                $last_attempt = $_SESSION['openchecker_time'];
                $elapsed_time = $now - $last_attempt;
                if ($elapsed_time > $OpenCheckerSetup->time_limit * 60) {
                    // time expired, reset
                    $_SESSION['openchecker_counter'] = 1;
                    $_SESSION['openchecker_time'] = $now;
                } else {
                    $elapsed_time = round($OpenCheckerSetup->time_limit - ($elapsed_time / 60));
                    tpl_set_var("elapsed_time",$elapsed_time);
                    tpl_set_var("attempts_counter", $_SESSION["openchecker_counter"]);
                    tpl_set_var("result_title", tr('openchecker_attempts_too_many'));
                    tpl_set_var("score", '');
                    tpl_set_var("image_yesno", '<image src="tpl/stdstyle/images/blue/openchecker_stop.png" />');
                    tpl_set_var("result_text", tr('openchecker_attempts_info_01') . ' ' . $OpenCheckerSetup->count_limit . ' ' . tr('openchecker_attempts_info_02') . ' ' . $OpenCheckerSetup->time_limit . ' ' . tr('openchecker_attempts_info_03') . '<br />' . tr('openchecker_attempts_info_04') . ' ' . $elapsed_time . ' ' . tr('openchecker_attempts_info_05'));
                    tpl_set_var("section_4_start", '');
                    tpl_set_var("section_4_stop", '');
                    tpl_set_var("save_mod_coord", '');
                    tpl_set_var("waypoint_desc", '');
                    $this->Finalize();
                }
            } else {
                tpl_set_var("section_4_start", '<!--');
                tpl_set_var("section_4_stop", '-->');
                $last_attempt = isset($_SESSION['openchecker_time'])?$_SESSION['openchecker_time']:0;
                $elapsed_time = $now - $last_attempt;
                if ($elapsed_time > $OpenCheckerSetup->time_limit * 60) {
                    // time expired, reset count
                    $_SESSION['openchecker_counter'] = 1;
                    $_SESSION['openchecker_time'] = $now;
                } else {
                    // increment attempts, update time
                    $_SESSION['openchecker_counter'] = $_SESSION['openchecker_counter'] + 1;
                    $_SESSION['openchecker_time'] = $now;
                }
                tpl_set_var("attempts_counter", $_SESSION["openchecker_counter"]);
            }
        } else {
            // initialize limits for this user's profile
            $_SESSION['openchecker_counter'] = 1;
            $_SESSION['openchecker_time'] = $now;
            tpl_set_var("attempts_counter", $_SESSION["openchecker_counter"]);
            tpl_set_var("section_4_start", '<!--');
            tpl_set_var("section_4_stop", '-->');
        }
    }

    public function CoordsComparing($OpenCheckerSetup) {

        // get data from post.
        $degrees_N = XDb::xEscape($_POST['degrees_N']);
        $minutes_N = XDb::xEscape($_POST['minutes_N']);
        $degrees_E = XDb::xEscape($_POST['degrees_E']);
        $minutes_E = XDb::xEscape($_POST['minutes_E']);
        $cache_id = XDb::xEscape($_POST['cacheid']);

        $rs = XDb::xSql("SELECT `caches`.`name`,
                `caches`.`cache_id`,
                `caches`.`type`,
                `caches`.`user_id`,
                `caches`.`wp_oc`,
                `cache_type`.`icon_large`,
                `user`.`username`
                FROM   `caches`, `user`, `cache_type`
                WHERE  `caches`.`user_id` = `user`.`user_id`
                AND    `caches`.`type` = `cache_type`.`id`
                AND    `caches`.`cache_id` = ? ", $cache_id);

        tpl_set_var("section_2_start", '');
        tpl_set_var("section_2_stop", '');
        tpl_set_var("section_openchecker_form_start", '<!--');
        tpl_set_var("section_openchecker_form_stop", '-->');
        tpl_set_var("openchecker_not_enabled", '');

        if (!$record = Xdb::xFetchArray($rs)) {
            tpl_set_var("openchecker_wrong_cache", tr(openchecker_wrong_cache));
            tpl_set_var("section_2_start", '<!--');
            tpl_set_var("section_2_stop", '-->');
            tpl_set_var("section_5_start", '');
            tpl_set_var("section_5_stop", '');
            $this->Finalize();
        }

        tpl_set_var("wp_oc", $record['wp_oc']);
        tpl_set_var("cache_icon", '<img src="tpl/stdstyle/images/' . $record['icon_large'] . '" />');
        tpl_set_var("cacheid", $cache_id);
        tpl_set_var("user_name", $record['username']);
        tpl_set_var("cachename", $record['name']);
        tpl_set_var("user_id", $record['user_id']);

        Xdb::xFreeResults($rs);

        if ($degrees_N == '')
            $degrees_N = 0;
        if ($degrees_E == '')
            $degrees_E = 0;
        if ($minutes_N == '')
            $minutes_N = 0;
        if ($minutes_E == '')
            $minutes_E = 0;

        // converting from HH MM.MMM to DD.DDDDDD

        $coordN = new convertLongLat($degrees_N, $minutes_N);
        $coordE = new convertLongLat($degrees_E, $minutes_E);

        //setting long & lat. signs
        if ($degrees_N >= 0) {
            $NorS = "N";
        } else {
            $NorS = "S";
        };
        if ($degrees_E >= 0) {
            $EorW = "E";
        } else {
            $EorW = "W";
        };

        // geting data from database
        $conn = XDb::instance();
        $query = "SELECT `waypoints`.`wp_id`,
        `waypoints`.`type`,
        `waypoints`.`longitude`,
        `waypoints`.`latitude`,
        `waypoints`.`status`,
        `waypoints`.`type`,
        `waypoints`.`desc`,
        `waypoints`.`opensprawdzacz`,
        `opensprawdzacz`.`proby`,
        `opensprawdzacz`.`sukcesy`,
        `caches`.`type` as `cache_type`
        FROM   `waypoints`, `opensprawdzacz`, `caches`
        WHERE  `waypoints`.`cache_id`='$cache_id'
        AND `waypoints`.`opensprawdzacz` = 1
        AND `waypoints`.`type` = " . Waypoint::TYPE_FINAL . "
        AND `waypoints`.`cache_id`= `opensprawdzacz`.`cache_id`
        AND `waypoints`.`cache_id`= `caches`.`cache_id`
        ";

        $data = $conn->query($query);
        $data = $data->fetch(PDO::FETCH_ASSOC);

        $attempts_counter = $data['proby'] + 1;

        $coordN_master = $data['latitude'];
        $coordE_master = $data['longitude'];


        // comparing data from post with data from database
        if (
                (($coordN_master - $coordN->CoordsDecimal) < 0.00001) &&
                (($coordN_master - $coordN->CoordsDecimal) > -0.00001) &&
                (($coordE_master - $coordE->CoordsDecimal) < 0.00001) &&
                (($coordE_master - $coordE->CoordsDecimal) > -0.00001)
        ) {
            //puzzle solved - result ok
            $hits_counter = $data['sukcesy'] + 1;

            try {
                $pdo = XDb::instance();
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $updateCounters = $pdo->exec("UPDATE `opensprawdzacz` SET `proby`=" . $attempts_counter . ",`sukcesy`=" . $hits_counter . "  WHERE `cache_id` = " . $cache_id);
            } catch (PDOException $e) {
                echo "Error PDO Library: ($OpenCheckerSetup->scriptname) " . $e->getMessage();
                exit;
            }

            if (
                $data['cache_type'] == GeoCache::TYPE_QUIZ ||
                $data['cache_type'] == GeoCache::TYPE_OTHERTYPE ||
                $data['cache_type'] == GeoCache::TYPE_MULTICACHE
            ) {
                $post_viewcache_form = '
    <form name="post_coord" action="viewcache.php?cacheid=' . $cache_id . '" method="post">
        <button type="submit" name="userModifiedCoordsSubmited" value="modCoords" style="font-size:14px;">' . tr('openchecker_modify_coords_button') . '</button>
        <input type="hidden" name="userCoordsFinalLatitude" value="' . $coordN->CoordsDecimal . '"/>
        <input type="hidden" name="userCoordsFinalLongitude" value="' . $coordE->CoordsDecimal . '"/>
    </form>
                ';
            } else {
                $post_viewcache_form = '';
            }

            tpl_set_var("result_title", tr('openchecker_success'));
            tpl_set_var("image_yesno", '<image src="tpl/stdstyle/images/blue/openchecker_yes.png" />');
            tpl_set_var("save_mod_coord", $post_viewcache_form);
            if ($OpenCheckerSetup->show_wpt_desc) {
                $desc = '
    <p>&nbsp;</p>
    <div class="notice" style="width:100%">' . tr('openchecker_final_desc') . '</div>
    <div>
        <p>' . nl2br($data['desc'],true) . '</p>
    </div>
                ';
                tpl_set_var('waypoint_desc', $desc);
            } else {
                tpl_set_var('waypoint_desc','');
            }
        } else {
            //puzzle not solved - wrong result

            try {
                $pdo = XDb::instance();
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $updateCounters = $pdo->exec("UPDATE `opensprawdzacz` SET `proby`='$attempts_counter'  WHERE `cache_id` = $cache_id");
            } catch (PDOException $e) {
                echo "Error PDO Library: ($OpenCheckerSetup->scriptname) " . $e->getMessage();
                exit;
            }
            tpl_set_var("result_title", tr('openchecker_fail'));
            tpl_set_var("image_yesno", '<image src="tpl/stdstyle/images/blue/openchecker_no.png" />');
            tpl_set_var("save_mod_coord", '');
            tpl_set_var("waypoint_desc",'');
        }
        //tpl_set_var("score", $wspolrzedneN.'/'.$$coordN_master.'<br>'.$wspolrzedneE.'/'. $$coordE_master);
        tpl_set_var("score", '');

        // tpl_set_var("wsp_NS", );
        // tpl_set_var("wsp_EW", );
        tpl_set_var("result_text", tr('openchecker_your_coordinates') . '<b> ' . $NorS . ' ' . $degrees_N . '° ' . $minutes_N . '</b> / <b>' . $EorW . ' ' . $degrees_E . '° ' . $minutes_E . '</b>');
        tpl_set_var("cache_id", $cache_id);

        $this->Finalize();
        // goto Finalize;
    }

    public function DisplayAllOpenCheckerCaches($OpenCheckerSetup) {
        /**
         * Displays initial form for cache waypoint (OXxxxx) input
         *
         * and
         *
         * display list of caches in OpenChecker.
         */
        /**
         *  if isset $_GET['wp'] means that user entered cache waypoint and wants to search for this
         *  cache through OpenChecker.
         *  This part get cache waypoint from url, check if cache owner allow specified cahe for check by
         *  OpenChecker.
         *
         */
        if (isset($_GET['wp']) && $_GET['wp'] != '') {
            $this->cache_wp = XDb::xEscape($_GET['wp']);
            $this->cache_wp = strtoupper($this->cache_wp);
        } else {
            $openchecker_form = '
    <form action="' . $OpenCheckerSetup->scriptname . '" method="get" class="form-group-sm">
    ' . tr('openchecker_waypoint') . ':
            <input type="text" name="wp" maxlength="6" class="form-control input100"/>
            <button type="submit" name="submit" value="' . tr('openchecker_check') . '" class="btn btn-default btn-sm">' . tr('openchecker_check') . '</button>
    </form>
            ';
            tpl_set_var("section_2_start", '<!--');
            tpl_set_var("section_2_stop", '-->');

            if (isset($_GET['sort'])) {
                $sort_tmp = XDb::xEscape($_GET['sort']);
                switch ($sort_tmp) {
                    case 'owner':
                        $sort_column = '`user`.`username`';
                        break;
                    case 'name':
                        $sort_column = '`caches`.`name`';
                        break;
                    case 'wpt':
                        $sort_column = '`caches`.`wp_oc`';
                        break;
                    case 'attempts':
                        $sort_column = '`opensprawdzacz`.`proby`';
                        break;
                    case 'hits':
                        $sort_column = '`opensprawdzacz`.`sukcesy`';
                        break;

                    default:
                        $sort_column = '`caches`.`name`';
                        break;
                }
            } else
                $sort_column = '`caches`.`name`';


            $openchecker_query = "
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
        FROM
            `caches`
            LEFT JOIN `waypoints` ON (`caches`.`cache_id` = `waypoints`.`cache_id`)
            LEFT JOIN `opensprawdzacz` ON (`waypoints`.`cache_id` = `opensprawdzacz`.`cache_id`)
            LEFT JOIN `user` ON (`user`.`user_id` = `caches`.`user_id`)
            LEFT JOIN `cache_type` ON (`cache_type`.`id` = `caches`.`type`)
        WHERE
            `waypoints`.`opensprawdzacz` = 1
            AND `waypoints`.`type` = " . Waypoint::TYPE_FINAL . "
            and (`caches`.`status` = " . GeoCache::STATUS_READY . " || `caches`.`status` = " . GeoCache::STATUS_UNAVAILABLE . ")
        ORDER BY   $sort_column
        LIMIT   0, 1000
        ";

/*
 * Only show active (available and temporarily disabled) caches from SQL query to
 * obtain correct counters
 */

            $status = array(
                '1' => '<img src="tpl/stdstyle/images/log/16x16-found.png" border="0" alt="Gotowa do szukania">',
                '2' => '<img src="tpl/stdstyle/images/log/16x16-temporary.png" border="0" alt="Tymczasowo niedost�pna">',
                '3' => '<img src="tpl/stdstyle/images/log/16x16-dnf.png" border="0" alt="zarchiwizowana">',
                '4' => '<img src="tpl/stdstyle/images/log/16x16-temporary.png" border="0" alt="Ukryta do czasu weryfikacji">',
                '5' => '<img src="tpl/stdstyle/images/log/16x16-temporary.png" border="0" alt="jeszcze niedost�pna">',
                '6' => '<img src="tpl/stdstyle/images/log/16x16-dnf.png" border="0" alt="Zablokowana przez COG">'
            );

            $conn = XDb::instance();
            $conn->query('SET CHARSET utf8');
            $openchecker_caches = $conn->query($openchecker_query)->fetchAll();
            $openchecker_caches_count = count($openchecker_caches);

            $pag = new Pagination();

            $numbers = $pag->Paginate($openchecker_caches, $OpenCheckerSetup->caches_on_page);
            $result = $pag->fetchResult();

            $pagination = ' ';
            if (isset($_GET["sort"]))
                $sort = '&sort=' . $_GET["sort"];
            else
                $sort = '';

            if (isset($_GET["page"]))
                $tPage = XDb::xEscape($_GET["page"]);
            else
                $tPage = 1;
            if ($tPage > 1)
                $pagination .= '<a href="' . $OpenCheckerSetup->scriptname . '?page=' . ($tPage - 1) . $sort . '">[&lt; ' . tr('openchecker_page_prev') . ']</a> ';
            foreach ($numbers as $num) {
                if ($num == $tPage)
                    $pagination .= '<b>[' . $num . ']</b>';
                else
                    $pagination .= '<a href="' . $OpenCheckerSetup->scriptname . '?page=' . $num . $sort . '">[' . $num . ']</a> ';
            }
            if ($tPage < count($numbers))
                $pagination .= '<a href="' . $OpenCheckerSetup->scriptname . '?page=' . ($tPage + 1) . $sort . '">[' . tr('openchecker_page_next') . ' &gt;]</a> ';

            $caches_table = '';
            $attempts = 0;
            $hits = 0;

            foreach ($result as $cache_data) {
                $attempts = $attempts + $cache_data['proby'];
                $hits = $hits + $cache_data['sukcesy'];

                $caches_table .= '
        <tr>
            <td><a class="links" href="viewcache.php?wp=' . $cache_data['wp_oc'] . '">' . $cache_data['wp_oc'] . '</a></td>
            <td><a href="viewcache.php?wp=' . $cache_data['wp_oc'] . '"><img src="tpl/stdstyle/images/' . $cache_data['icon_small'] . '" /></a></td>
            <td><a class="links" href="' . $OpenCheckerSetup->scriptname . '?wp=' . $cache_data['wp_oc'] . '"> ' . $cache_data['name'] . '</a> </td>
            <td align="center">' . $status[$cache_data['status']] . '</td>
            <td><a href="viewprofile.php?userid=' . $cache_data['user_id'] . '">' . $cache_data['username'] . '</td>
            <td align="center">' . $cache_data['proby'] . '</td>
            <td align="center">' . $cache_data['sukcesy'] . '</td>
        </tr>
                ';
            }

            $caches_table .= '
        <tr>
            <td colspan="7"><img src="tpl/stdstyle/images/blue/dot_blue.png" height="1" width="100%"/></td>
        </tr>
        <tr>
            <td colspan="7"><img src="/tpl/stdstyle/images/misc/16x16-info.png" />'
                . tr('openchecker_count') . ' ' . $openchecker_caches_count . '
            </td>
        </tr>
        <tr>
            <td></td>
            <td colspan="2" align="right">'
                . tr('openchecker_legend') .
                '
            </td>
            <td align="center">
                ' . $status[1] . '<br />' . $status[2] . '
            </td>
            <td>
                (' . tr('ready_to_find') . ')<br />
                (' . tr('temp_unavailables') . ')
            </td>
            <td align="center">' . $attempts . '</td>
            <td align="center">' . $hits . '</td>
        </tr>
            ';

            $caches_table .= '<tr><td colspan="7"><br /><p align="center">' . $pagination . '</p></td></tr>';

            tpl_set_var("section_1_start", '');
            tpl_set_var("section_1_stop", '');
            tpl_set_var("section_3_start", '<!--');
            tpl_set_var("section_3_stop", '-->');
            tpl_set_var("section_4_start", '<!--');
            tpl_set_var("section_4_stop", '-->');
            tpl_set_var("section_openchecker_form_start", '<!--');
            tpl_set_var("section_openchecker_form_stop", '');
            tpl_set_var("openchecker_form", $openchecker_form);
            tpl_set_var("caches_table", $caches_table);

            $this->Finalize();
        }
    }

    public function Finalize() {
        tpl_BuildTemplate();
        exit;
    }

}
