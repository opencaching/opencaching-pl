<?php
use Utils\Database\XDb;
/**
 * This script is used (can be loaded) by /search.php
 */

setlocale(LC_TIME, 'pl_PL.UTF-8'); // TODO: why it's pl_PL

global $content, $usr, $hide_coords, $lang, $dbcSearch;

set_time_limit(1800);


if ($usr || !$hide_coords) {
    //prepare the output
    $caches_per_page = 20;

    $query = 'SELECT ';

    if (isset($lat_rad) && isset($lon_rad))
        $query .= getCalcDistanceSqlFormula($usr !== false, $lon_rad * 180 / 3.14159, $lat_rad * 180 / 3.14159, 0, $multiplier[$distance_unit]) . ' `distance`, ';
    else {
        if ($usr === false)
            $query .= '0 distance, ';
        else {
            //get the users home coords
            $rs_coords = XDb::xSql("SELECT `latitude`, `longitude` FROM `user` WHERE `user_id`= ? LIMIT 1", $usr['userid']);
            $record_coords = XDb::xFetchArray($rs_coords);

            if ((($record_coords['latitude'] == NULL) || ($record_coords['longitude'] == NULL)) || (($record_coords['latitude'] == 0) || ($record_coords['longitude'] == 0)))
                $query .= '0 distance, ';
            else {
                $lon_rad = $record_coords['longitude'] * 3.14159 / 180;
                $lat_rad = $record_coords['latitude'] * 3.14159 / 180;

                $query .= getCalcDistanceSqlFormula(true, $record_coords['longitude'], $record_coords['latitude'], 0, 1) . ' `distance`, ';
            }
            XDb::xFreeResults($rs_coords);
        }
    }
    $query .= '`caches`.`cache_id`, `caches`.`wp_oc`';
    if ($usr === false)
        $query .= ' FROM `caches` ';
    else {
        $query .= ' FROM `caches`
                      LEFT JOIN `cache_mod_cords` ON `caches`.`cache_id` = `cache_mod_cords`.`cache_id` AND `cache_mod_cords`.`user_id` = '
                . $usr['userid'];
    }
    $query .= ' WHERE `caches`.`cache_id` IN (' . $queryFilter . ')';

    $sortby = $options['sort'];
    if (isset($lat_rad) && isset($lon_rad) && ($sortby == 'bydistance'))
        $query .= ' ORDER BY distance ASC';
    else if ($sortby == 'bycreated')
        $query .= ' ORDER BY date_created DESC';
    else // by name
        $query .= ' ORDER BY name ASC';

    //startat?
    $startat = isset($_REQUEST['startat']) ? $_REQUEST['startat'] : 0;
    if (!is_numeric($startat))
        $startat = 0;

    if (isset($_REQUEST['count']))
        $count = $_REQUEST['count'];
    else
        $count = $caches_per_page;

    $maxlimit = 1000000000;

    if ($count == 'max')
        $count = $maxlimit;
    if (!is_numeric($count))
        $count = 0;
    if ($count < 1)
        $count = 1;
    if ($count > $maxlimit)
        $count = $maxlimit;

    $queryLimit = ' LIMIT ' . $startat . ', ' . $count;

    // cleanup (old zipcontent lingers if zip-download is cancelled by user)
    $dbcSearch->simpleQuery('DROP TEMPORARY TABLE IF EXISTS `zipcontent`');

    // temporäre tabelle erstellen
    $dbcSearch->simpleQuery('CREATE TEMPORARY TABLE `zipcontent` ' . $query . $queryLimit);

    // echo $query;
    $s = $dbcSearch->simpleQuery('SELECT COUNT(*) `count` FROM `zipcontent`');
    $rCount = $dbcSearch->dbResultFetchOneRowOnly($s);

    $caches_count = $rCount['count'];

    if ($rCount['count'] == 1) {
        $s = $dbcSearch->simpleQuery(
            'SELECT `caches`.`wp_oc` `wp_oc` FROM `zipcontent`, `caches`
            WHERE `zipcontent`.`cache_id`=`caches`.`cache_id` LIMIT 1');
        $rName = $dbcSearch->dbResultFetchOneRowOnly($s);

        $sFilebasename = $rName['wp_oc'];
    } else {
        if ($options['searchtype'] == 'bywatched') {
            $sFilebasename = 'watched_caches';
        } elseif ($options['searchtype'] == 'bylist') {
            $sFilebasename = 'cache_list';
        } elseif ($options['searchtype'] == 'bypt') {
            $sFilebasename = $options['gpxPtFileName'];
        } else {
            $rsName = XDb::xSql(
                'SELECT `queries`.`name` `name` FROM `queries` WHERE `queries`.`id`= ? LIMIT 1',
                $options['queryid']);

            $rName = XDb::xFetchArray($rsName);
            XDb::xFreeResults($rsName);
            if (isset($rName['name']) && ($rName['name'] != '')) {
                $sFilebasename = trim($rName['name']);
                $sFilebasename = str_replace(" ", "_", $sFilebasename);
            } else {
                $sFilebasename = 'ocpl' . $options['queryid'];
            }
        }
    }

    // =======================================
    // I don't know what code above doing (it's horrible and I don't have enough time to analyze this code),
    // so I just modify existing piece of code from other output search.*.inc.php file.
    // == Limak (28.01.2012) ==

    $okapi_max_caches = get_max_caches_per_call();

    //zippart param in request is used for split ZIP files
    if (!isset($_REQUEST['zippart']))
        $_REQUEST['zippart'] = 0;
    $zippart = abs(intval($_REQUEST['zippart'])) + 0;
    $startat = ($zippart - 1) * $okapi_max_caches;

    // too much caches for one zip file - generate webpage instead
    if (($caches_count > $okapi_max_caches) && ($zippart == 0 || $startat >= $caches_count)) {
        $tplname = get_pagination_template();
        $tpl_subtitle = get_pagination_page_title(); // omfg :(

        tpl_set_var('zip_total_cache_count', $caches_count);
        tpl_set_var('zip_max_count', $okapi_max_caches);

        $links_content = '';
        $forlimit = intval($caches_count / $okapi_max_caches) + 1;
        for ($i = 1; $i <= $forlimit; $i++) {
            $link_content = generate_link_content($options['queryid'], $sFilebasename, $i);
            $links_content .= $link_content;
        }
        tpl_set_var('zip_links', $links_content);
        tpl_BuildTemplate();
    } else { // caches are less or equals then okapi_max_caches in one ZIP file limit - okey, return ZIP file
        // use 'LIMIT' only if it's needed
        if ($caches_count > $okapi_max_caches)
            $ziplimit = ' LIMIT ' . $startat . ',' . $okapi_max_caches;
        else
            $ziplimit = '';
        // OKAPI need only waypoints
        $s = $dbcSearch->simpleQuery('SELECT `caches`.`wp_oc` `wp_oc` FROM `zipcontent`, `caches` WHERE `zipcontent`.`cache_id`=`caches`.`cache_id`' . $ziplimit);

        $waypoints_tab = array();
        while ($r = $dbcSearch->dbResultFetch($s)) {
            // TODO: zalogować dostęp do kesza - o ile nie jest to w okapi
            $waypoints_tab[] = $r['wp_oc'];
        }
        $waypoints = implode("|", $waypoints_tab);

        if (!isset($_SESSION))
            session_start();# prevent downloading multiple parts at once
        // Including OKAPI's Facade. This is the only valid (and fast) interface to access
        // OKAPI services from within OC code.
        require_once($rootpath . 'okapi/facade.php');

        try {
            $okapi_response = call_okapi($usr, $waypoints, $lang, $sFilebasename, $zippart);

            // This outputs headers and the file content.
            $okapi_response->display();
        } catch (\okapi\BadRequest $e) {
            # In case of bad requests, simply output OKAPI's error response.
            # In case of other, internal errors, don't catch the error. This
            # will cause OKAPI's default error hangler to kick in (so the admins
            # will get informed).

            header('Content-Type: text/plain');
            echo $e;
            exit;
        }
        exit;
    }
}

