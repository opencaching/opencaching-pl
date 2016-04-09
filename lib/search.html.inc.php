<?php

function findColumn($name, $type = "C")
{
    global $colNameSearch;

    for ($i = 0; $i < 19; $i ++) {
        if ($colNameSearch[$i][$type] == $name)
            return $i;
    }

    return - 1;
}

function fHideColumn($nr, $set)
{
    global $cookie, $selectList, $NrColVisable, $colNameSearch, $NrColSortSearch;

    $sNameColumnsSearch = "NCSearch" . $nr;

    if (isset($_REQUEST["C" . $nr])) {
        $C = 1;
        if ($set) {
            $cookie->set($sNameColumnsSearch, 1);
            $cookie->header();
        }
    } else {
        if (! isset($_REQUEST["notinit"])) // first ent.
{
            $C = 0;
            if ($cookie->is_set($sNameColumnsSearch))
                $C = $cookie->get($sNameColumnsSearch);
        } else // next ent.
{
            if ($set) {
                $cookie->set($sNameColumnsSearch, 0);
                $cookie->header();
            }
            $C = 0;
        }
    }

    if (! $set)
        return $C;

    if ($C == 1) {
        echo "<script type='text/javascript'>
        gct.hideColumns([$nr]);
        </script>";
    } else {
        $descCol = $colNameSearch[$nr]['O'];
        $NrColVisable += 1;
        if ($NrColSortSearch != $NrColVisable)
            $selectList .= "<option value=$NrColVisable>$descCol</option>";
        else
            $selectList .= "<option selected='selected' value=$NrColVisable>$descCol</option>";
    }

    return $C;
}

global $dbcSearch, $usr, $lang, $hide_coords, $cookie, $NrColSortSearch, $OrderSortSearch, $SearchWithSort, $TestStartTime, $queryFilter;
require_once ($stylepath . '/lib/icons.inc.php');
require_once ('lib/cache_icon.inc.php');
set_time_limit(1800);

$dbc = new dataBase();

$sNrColumnsSortSearch = "NrColumnsSortSearch";
$sOrderSortSearch = "OrderSortSearch";

$colNameSearch = array(
    0 => array(
        "C" => "CacheID",
        "O" => "CacheID"
    ),
    1 => array(
        "C" => "",
        "O" => tr('cache_type')
    ),
    2 => array(
        "C" => tr('name_label'),
        "O" => tr('cache_label')
    ),
    3 => array(
        "C" => tr('short_description'),
        "O" => tr('short_description')
    ),
    4 => array(
        "C" => tr('owner'),
        "O" => tr('CacheOwner')
    ),
    5 => array(
        "C" => tr('Hidden'),
        "O" => tr('date_hidden_label')
    ),
    6 => array(
        "C" => tr('FNC'),
        "O" => tr('FoundNotFoundComment')
    ),
    7 => array(
        "C" => tr('F'),
        "O" => tr('Found')
    ),
    8 => array(
        "C" => tr('N'),
        "O" => tr('NotFound')
    ),
    9 => array(
        "C" => tr('C'),
        "O" => tr('Comment')
    ),
    10 => array(
        "C" => "<img src='images/rating-star.png'>",
        "O" => tr('RecommendationNumber')
    ),
    11 => array(
        "C" => tr('Entry'),
        "O" => tr('TypeDateLastEntry')
    ),
    12 => array(
        "C" => tr('type'),
        "O" => tr('LastTypeEntry')
    ),
    13 => array(
        "C" => tr('date_logged'),
        "O" => tr('LastEntryDate')
    ),
    14 => array(
        "C" => tr('content'),
        "O" => tr('LastEntryContent')
    ),
    15 => array(
        "C" => tr('Coordinates'),
        "O" => tr('Coordinates')
    ),
    16 => array(
        "C" => tr('Distance'),
        "O" => tr('DirectionDistance')
    ),
    17 => array(
        "C" => tr('TT'),
        "O" => tr('TaskTerainDifficulty')
    ),
    18 => array(
        "C" => "",
        "O" => tr('srch_Send_to_GPS')
    )
);

$sDefCol4Search = "DefCol4Search";
if (! $cookie->is_set($sDefCol4Search)) {
    $cookie->set("NCSearch3", "1");
    $cookie->set("NCSearch6", "1");
    $cookie->set("NCSearch7", "1");
    $cookie->set("NCSearch8", "1");
    $cookie->set("NCSearch9", "1");
    $cookie->set("NCSearch12", "1");
    $cookie->set("NCSearch13", "1");
    $cookie->set("NCSearch14", "1");
    $cookie->set("NCSearch15", "1");
    $cookie->set("NCSearch16", "1");
    $cookie->set("NCSearch17", "1");
    $cookie->set($sNrColumnsSortSearch, "-1");
    $cookie->set($sOrderSortSearch, "M");

    $cookie->set($sDefCol4Search, "Y");
    $cookie->header();
}

if (! isset($_REQUEST["NrColSort"])) {
    $NrColSortSearch = 1;

    if ($cookie->is_set($sNrColumnsSortSearch))
        $NrColSortSearch = $cookie->get($sNrColumnsSortSearch);
    else
        $cookie->set($sNrColumnsSortSearch, $NrColSortSearch);
} else {
    $NrColSortSearch = $_REQUEST["NrColSort"];
    $cookie->set($sNrColumnsSortSearch, $NrColSortSearch);
}

// //////////////////////////////////

if (! isset($_REQUEST["OrderSortSearch"])) {
    $OrderSortSearch = "M";

    if ($cookie->is_set($sOrderSortSearch))
        $OrderSortSearch = $cookie->get($sOrderSortSearch);
    else
        $cookie->set($sOrderSortSearch, $OrderSortSearch);
} else {
    $OrderSortSearch = $_REQUEST["OrderSortSearch"];
    $cookie->set($sOrderSortSearch, $OrderSortSearch);
}

$cookie->header();

// build SQL-list
$countselect = mb_eregi_replace('^SELECT `cache_id`', 'SELECT COUNT(`cache_id`) `count`', $queryFilter);
$countselect = mb_eregi_replace('^SELECT `caches` `cache_id`', 'SELECT COUNT(`caches`.`cache_id`) `count`', $countselect);
$countselect = mb_eregi_replace('^SELECT `caches`.`cache_id` `cache_id`', 'SELECT COUNT(`caches`.`cache_id`) `count`', $countselect);
$countselect = mb_eregi_replace('^SELECT `result_caches`.`cache_id`', 'SELECT COUNT(`result_caches`.`cache_id`) `count`', $countselect);
$countselect = mb_eregi_replace('^SELECT `result_caches`.`cache_id` `cache_id`', 'SELECT COUNT(`result_caches`.`cache_id`) `count`', $countselect);

$dbcSearch->simpleQuery($countselect);
$r = $dbcSearch->dbResultFetch();
$resultcount = $r['count'];

tpl_set_var('results_count', $resultcount);

if ($resultcount <= 5000 && $NrColSortSearch != - 1) {
    $SearchWithSort = true;
    $tplname = 'search.result.caches'; // prepare the output
    $caches_per_page = 999999;
    $cache_line = tpl_do_translate(read_file($stylepath . '/search.result.caches.row.tpl.php')); // build lines
} else {
    $SearchWithSort = false;
    $tplname = 'search.result.caches'; // without sort
    $caches_per_page = 20;
    $cache_line = tpl_do_translate(read_file($stylepath . '/search.result.caches.row.tpl.php')); // build lines
}

if ($resultcount)
    $caches_output = '';
else
    $caches_output = '<div class="errormsg" style="font-size:13px;text-align:center;"><b>' . tr('CachesNotMatchCryteria') . '</b></br></br></br></div>';

$CalcDistance = true;
if ($SearchWithSort && fHideColumn(findColumn(tr('Distance')), false) == 1)
    $CalcDistance = false;

$CalcCoordinates = true;
if (fHideColumn(findColumn(tr('Coordinates')), false) == 1)
    $CalcCoordinates = false;

$CalcSendToGPS = true;
if (fHideColumn(findColumn(tr('srch_Send_to_GPS'), "O"), false) == 1)
    $CalcSendToGPS = false;

$CalcFNC = true;
if (fHideColumn(findColumn(tr('FNC')), false) == 1 && fHideColumn(findColumn(tr('F')), false) == 1 && fHideColumn(findColumn(tr('N')), false) == 1 && fHideColumn(findColumn(tr('C')), false) == 1)
    $CalcFNC = false;

$CalcEntry = true;
if (fHideColumn(findColumn(tr('Entry')), false) == 1)
    $CalcEntry = false;

if ($CalcSendToGPS)
    $CalcCoordinates = true;

if ($CalcDistance)
    $CalcCoordinates = true;

$distance_unit = 'km';

$$query = 'SELECT ';

if (isset($lat_rad) && isset($lon_rad)) {
    if ($CalcDistance)
        $query .= getCalcDistanceSqlFormula($usr !== false, $lon_rad * 180 / 3.14159, $lat_rad * 180 / 3.14159, 0, $multiplier[$distance_unit]) . ' `distance`, ';
} else {
    if ($usr === false) {
        if ($CalcDistance)
            $query .= '0 distance, ';
    } elseif ($CalcDistance) {
        // get the users home coords
        $dbc->multiVariableQuery("SELECT `latitude`, `longitude` FROM `user` WHERE `user_id`=:1", $usr['userid']);
        $record_coords = $dbc->dbResultFetch();

        if ((($record_coords['latitude'] == NULL) || ($record_coords['longitude'] == NULL)) || (($record_coords['latitude'] == 0) || ($record_coords['longitude'] == 0))) {
            $query .= '0 distance, ';
        } else {
            // TODO: load from the users-profile
            $distance_unit = 'km';

            $lon_rad = $record_coords['longitude'] * 3.14159 / 180;
            $lat_rad = $record_coords['latitude'] * 3.14159 / 180;

            $query .= getCalcDistanceSqlFormula($usr !== false, $record_coords['longitude'], $record_coords['latitude'], 0, $multiplier[$distance_unit]) . ' `distance`, ';
        }
        $dbc->reset();
    }
}
$query .= '   `caches`.`name` `name`, `caches`.`status` `status`, `caches`.`wp_oc` `wp_oc`,
                `caches`.`difficulty` `difficulty`, `caches`.`terrain` `terrain`, `caches`.`desc_languages` `desc_languages`,
                `caches`.`date_created` `date_created`, `caches`.`type` `cache_type`, `caches`.`cache_id` `cache_id`,
                `user`.`username` `username`, `user`.`user_id` `user_id`,
                `cache_type`.`icon_large` `icon_large`,
                `caches`.`founds` `founds`, `caches`.`topratings` `toprating`, cache_desc.short_desc short_desc ';
if ($usr === false) {
    if ($CalcCoordinates)
        $query .= ', `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, 0 as cache_mod_cords_id ';

    $query .= ' FROM `caches` ';
} else {
    if ($CalcCoordinates) {
        $query .= ', IFNULL(`cache_mod_cords`.`longitude`, `caches`.`longitude`) `longitude`, IFNULL(`cache_mod_cords`.`latitude`,
                            `caches`.`latitude`) `latitude`, IFNULL(cache_mod_cords.id,0) as cache_mod_cords_id';
    }

    $query .= ' FROM `caches` ';

    if ($CalcCoordinates) {
        $query .= ' LEFT JOIN `cache_mod_cords` ON `caches`.`cache_id` = `cache_mod_cords`.`cache_id` AND `cache_mod_cords`.`user_id` = ' . $usr['userid'] . ' ';
    }
}
$query .= ' LEFT JOIN cache_desc ON cache_desc.cache_id=caches.cache_id AND cache_desc.language=\'' . $lang . '\',
            `user`, cache_type
        WHERE `caches`.`user_id`=`user`.`user_id`
        AND `caches`.`cache_id` IN (' . $queryFilter . ')
        AND `cache_type`.`id`=`caches`.`type` ';
$sortby = $options['sort'];

if (! $SearchWithSort) // without interactive sort
{
    if (isset($lat_rad) && isset($lon_rad) && ($sortby == 'bydistance')) {
        $query .= ' ORDER BY distance ASC';
    } else
        if ($sortby == 'bycreated') {
            $query .= ' ORDER BY date_created DESC';
        } else // by name
{
            $query .= ' ORDER BY name ASC';
        }
}

// startat?
$startat = isset($_REQUEST['startat']) ? $_REQUEST['startat'] : 0;
if (! is_numeric($startat))
    $startat = 0;
if (! is_numeric($caches_per_page))
    $caches_per_page = 20;
$startat = floor($startat / $caches_per_page) * $caches_per_page;
$query .= ' LIMIT ' . $startat . ', ' . $caches_per_page;

$dbcSearch->simpleQuery($query);

$tr_Coord_have_been_modified = tr('srch_Coord_have_been_modified');
$tr_Recommended = tr('srch_Recommended');
$tr_Send_to_GPS = tr('srch_Send_to_GPS');

for ($i = 0; $i < $dbcSearch->rowCount(); $i ++) {
    $caches_record = $dbcSearch->dbResultFetch();

    // modified coords
    if ($CalcCoordinates) {
        if ($caches_record['cache_mod_cords_id'] > 0) { // check if we have user coords
            $caches_record['coord_modified'] = true; // mark as coords midified
        } else {
            $caches_record['coord_modified'] = false;
        }
    }
    $tmpline = $cache_line;

    list ($iconname, $inactive) = getCacheIcon($usr['userid'], $caches_record['cache_id'], $caches_record['status'], $caches_record['user_id'], $caches_record['icon_large']);

    $tmpline = str_replace('{icon_large}', $iconname, $tmpline);
    // sp2ong

    $tmpline = str_replace('{date_created}', date($dateFormat, strtotime($caches_record['date_created'])), $tmpline);
    $tmpline = str_replace('{date_created_sort}', date($logdateformat_ymd, strtotime($caches_record['date_created'])), $tmpline);

    $ratingA = $caches_record['toprating'];
    if ($ratingA > 0)
        $ratingimg = '<img src="images/rating-star.png" alt="' . $tr_Recommended . '" title="' . $tr_Recommended . '" />';
    else
        $ratingimg = '';
    $tmpline = str_replace('{toprating}', $ratingA, $tmpline);
    $tmpline = str_replace('{ratpic}', $ratingimg, $tmpline);

    $login = 0;
    if ($usr == false) {
        $tmpline = str_replace('{long}', tr('please_login'), $tmpline);
        $tmpline = str_replace('{lat}', tr('to_see_coords'), $tmpline);
    } else {
        if ($CalcCoordinates) {
            $tmpline = str_replace('{long}', htmlspecialchars(help_lonToDegreeStr($caches_record['longitude'])), $tmpline);
            if ($caches_record['coord_modified'] == true) {
                $tmpline = str_replace('{mod_cord_style}', 'style="color:orange;" alt ="' . $tr_Coord_have_been_modified . '" title="' . $tr_Coord_have_been_modified . '"', $tmpline);
                $tmpline = str_replace('{mod_suffix}', '[F]', $tmpline);
            } else {
                $tmpline = str_replace('{mod_cord_style}', '', $tmpline);
                $tmpline = str_replace('{mod_suffix}', '', $tmpline);
            }
            $tmpline = str_replace('{lat}', htmlspecialchars(help_latToDegreeStr($caches_record['latitude'])), $tmpline);
        }
    }
    ;
    $tmpline = str_replace('{cachetype}', htmlspecialchars(cache_type_from_id($caches_record['cache_type'], $lang), ENT_COMPAT, 'UTF-8'), $tmpline);

    // sp2ong short_desc ermitteln TODO: nicht die erste sondern die richtige wĂ¤hlen
    $tmpline = str_replace('{wp_oc}', htmlspecialchars($caches_record['wp_oc'], ENT_COMPAT, 'UTF-8'), $tmpline);
    ;

    if ($CalcCoordinates) {
        $tmpline = str_replace('{latitude}', htmlspecialchars($caches_record['latitude'], ENT_COMPAT, 'UTF-8'), $tmpline);
        ;
        $tmpline = str_replace('{longitude}', htmlspecialchars($caches_record['longitude'], ENT_COMPAT, 'UTF-8'), $tmpline);
        ;
    }

    $tmpline = str_replace('{short_desc}', htmlspecialchars(PrepareText($caches_record['short_desc']), ENT_COMPAT, 'UTF-8'), $tmpline);

    $dDiff = abs(dateDiff('d', $caches_record['date_created'], date('Y-m-d')));
    if ($dDiff < $caches_olddays)
        $tmpline = str_replace('{new}', $caches_newstring, $tmpline);
    else
        $tmpline = str_replace('{new}', '', $tmpline);

    $tmpline = str_replace('{diffpic}', icon_difficulty("diff", $caches_record['difficulty']), $tmpline);
    $tmpline = str_replace('{terrpic}', icon_difficulty("terr", $caches_record['terrain']), $tmpline);

    $typy = array(
        0 => 0,
        1 => 0,
        2 => 0
    );
    if ($CalcFNC) {

        $dbc->multiVariableQuery(
            'SELECT count(cache_logs.type) as typy, cache_logs.type as type
            FROM `cache_logs`, `log_types`
            WHERE `cache_logs`.`cache_id`= :1
                AND `cache_logs`.`deleted`=0
                AND `log_types`.`id`=`cache_logs`.`type`
            GROUP BY cache_logs.type
            ORDER BY cache_logs.type ASC', $caches_record['cache_id']);

        $typy_i = 0;

        while ($row = $dbc->dbResultFetch()) {
            $typy[($row['type'] - 1)] = $row['typy'];
        }

        $tmpline = str_replace('{logtypes1}', "<span " . str_pad($typy[0], 5, 0, STR_PAD_LEFT) . " style='color:green'>" . $typy[0] . "</span>.<span style='color:red'>" . $typy[1] . "</span>.<span style='color:black'>" . $typy[2] . "</span>", $tmpline);

        $dbc->reset();
    }
    $tmpline = str_replace('{find}', $typy[0], $tmpline);
    $tmpline = str_replace('{notfind}', $typy[1], $tmpline);
    $tmpline = str_replace('{comment}', $typy[2], $tmpline);

    // das letzte found suchen
    if ($CalcEntry) {

        $dbc->multiVariableQuery(
            'SELECT `cache_logs`.`id` `id`, `cache_logs`.`type` `type`, `cache_logs`.`date` `date`,
                   `log_types`.`icon_small` `icon_small`,
                    cache_logs.text AS log_text
            FROM `cache_logs`, `log_types`
            WHERE `cache_logs`.`deleted`=0
                AND `cache_logs`.`cache_id`= :1
                AND `log_types`.`id`=`cache_logs`.`type`
            ORDER BY `cache_logs`.`date` DESC LIMIT 1', $caches_record['cache_id']);

        if ($row = $dbc->dbResultFetch()) {
            $tmpline = str_replace('{logimage1}', icon_log_type($row['icon_small'], "") . '<a href=\'viewlogs.php?cacheid=' . htmlspecialchars($caches_record['cache_id'], ENT_COMPAT, 'UTF-8') . '#' . htmlspecialchars($row['id'], ENT_COMPAT, 'UTF-8') . '\'>{gray_s}' . date($logdateformat, strtotime($row['date'])) . '{gray_e}</a>', $tmpline);

            $log_text = PrepareText($row['log_text']);

            $tmpline = str_replace('{logimage2}', "<span='" . date($logdateformat_ymd, strtotime($row['date'])) . "'/>" . icon_log_type($row['icon_small'], $log_text) . date($dateFormat, strtotime($row['date'])), $tmpline);
            $tmpline = str_replace('{logtype}', icon_log_type($row['icon_small'], $log_text), $tmpline);
            $tmpline = str_replace('{logdate}', date($logdateformat_ymd, strtotime($row['date'])), $tmpline);
            $tmpline = str_replace('{logdesc}', $log_text, $tmpline);

            $tmpline = str_replace('{logdate1}', "", $tmpline); //
        } else {
            $tmpline = str_replace('{logimage1}', "<img src='images/trans.gif' border='0' width='16' height='16' />", $tmpline);
            $tmpline = str_replace('{logimage2}', "", $tmpline);
            $tmpline = str_replace('{logdate1}', "", $tmpline);
            $tmpline = str_replace('{logdate}', "", $tmpline);
            $tmpline = str_replace('{logtype}', "", $tmpline);
            $tmpline = str_replace('{logdesc}', "", $tmpline);
        }
        $dbc->reset();
    }
    $lastlogs = "";

    if ($CalcDistance) {
        // und jetzt noch die Richtung ...
        if ($caches_record['distance'] > 0 && ($usr || ! $hide_coords)) {
            $tmpline = str_replace('{direction}', Bearing2Text(calcBearing($lat_rad / 3.14159 * 180, $lon_rad / 3.14159 * 180, $caches_record['latitude'], $caches_record['longitude']), 1), $tmpline);
        } else
            $tmpline = str_replace('{direction}', '', $tmpline);
    }

    $desclangs = '';
    $aLangs = mb_split(',', $caches_record['desc_languages']);
    foreach ($aLangs as $thislang) {
        $desclangs .= '<a href="viewcache.php?cacheid=' . urlencode($caches_record['cache_id']) . '&amp;desclang=' . urlencode($thislang) . '" style="text-decoration:none;"><b><font color="blue">' . htmlspecialchars($thislang, ENT_COMPAT, 'UTF-8') . '</font></b></a> ';
    }
    $tmpline = str_replace('{desclangs}', $desclangs, $tmpline);
    if ($usr || ! $hide_coords) {
        if ($CalcCoordinates) {
            if ($caches_record['coord_modified'] == true) {
                $mod_suffix_garmin = '(F)';
            } else {
                $mod_suffix_garmin = '';
            }
            ;
        }
        if ($CalcSendToGPS) {
            $tmpline = str_replace('{sendtogps}', ("<a href=\"#\" onclick=\"javascript:window.open('garmin.php?lat=" . $caches_record['latitude'] . "&amp;long=" . $caches_record['longitude'] . "&amp;wp=" . $caches_record['wp_oc'] . "&amp;name=" . urlencode($mod_suffix_garmin . $caches_record['name']) . "&amp;popup=y','Send_To_GPS','width=450,height=160,resizable=no,scrollbars=0')\"><img src='/images/garmin.jpg' alt='Send to GPS' title='" . $tr_Send_to_GPS . "' border='0' /></a>"), $tmpline);
            $tmpline = str_replace('{sendtogpsnew}', "<a href='#' onclick=\\\"javascript:window.open('garmin.php?lat=" . $caches_record['latitude'] . "&amp;long=" . $caches_record['longitude'] . "&amp;wp=" . $caches_record['wp_oc'] . "&amp;name=" . urlencode($mod_suffix_garmin . $caches_record['name']) . "&amp;popup=y','Send_To_GPS','width=450,height=160,resizable=no,scrollbars=0')\\\"><img src='tpl/stdstyle/images/blue/gps-receiving-32.png' alt='Send to GPS' title='" . $tr_Send_to_GPS . "' border='0'  height='16' width='16' /></a>", $tmpline);
        }
    } else {
        $tmpline = str_replace('{sendtogps}', "", $tmpline);
    }

    $tmpline = str_replace('{cachename}', htmlspecialchars($caches_record['name'], ENT_COMPAT, 'UTF-8'), $tmpline);
    $tmpline = str_replace('{cachenameBIG}', strtoupper(trChar(htmlspecialchars(trim($caches_record['name']), ENT_COMPAT, 'UTF-8'))), $tmpline);
    $tmpline = str_replace('{urlencode_cacheid}', htmlspecialchars(urlencode($caches_record['cache_id']), ENT_COMPAT, 'UTF-8'), $tmpline);
    $tmpline = str_replace('{urlencode_userid}', htmlspecialchars(urlencode($caches_record['user_id']), ENT_COMPAT, 'UTF-8'), $tmpline);
    $tmpline = str_replace('{username}', htmlspecialchars($caches_record['username'], ENT_COMPAT, 'UTF-8'), $tmpline);
    $tmpline = str_replace('{usernameBIG}', strtoupper(trChar(htmlspecialchars($caches_record['username'], ENT_COMPAT, 'UTF-8'))), $tmpline);
    $tmpline = str_replace('{CacheID}', $caches_record['cache_id'], $tmpline);

    if ($CalcDistance) {
        if ($usr || ! $hide_coords) {
            $dist = htmlspecialchars(sprintf("%01.1f", $caches_record['distance']), ENT_COMPAT, 'UTF-8');
            $tmpline = str_replace('{distance}', $dist, $tmpline);
            $tmpline = str_replace('{distance_pad}', str_pad($dist, 5, 0, STR_PAD_LEFT), $tmpline);
        } else
            $tmpline = str_replace('{distance}', "", $tmpline);
    }

    $tmpline = str_replace('{position}', $i + $startat + 1, $tmpline);

    // backgroundcolor of line
    if (($i % 2) == 1)
        $bgcolor = $bgcolor2;
    else
        $bgcolor = $bgcolor1;

    if ($inactive) {
        // $bgcolor = $bgcolor_inactive;
        $tmpline = str_replace('{gray_s}', "<span class='text_gray'>", $tmpline);
        $tmpline = str_replace('{gray_e}', "</span>", $tmpline);
    } else {
        $tmpline = str_replace('{gray_s}', "", $tmpline);
        $tmpline = str_replace('{gray_e}', "", $tmpline);
    }

    $tmpline = str_replace('{bgcolor}', $bgcolor, $tmpline);

    $caches_output .= $tmpline;
}

unset($dbc);

tpl_set_var('results', $caches_output);

// 4test
$TestStopTime = new DateTime('now');
$insecond = $TestStartTime->diff($TestStopTime);
tpl_set_var('insecond', $insecond->format('%s'));

// more than one page?
if ($startat > 0) {
    $pages = '<a href="search.php?queryid=' . $options['queryid'] . '&amp;startat=0">{first_img}</a> <a href="search.php?queryid=' . $options['queryid'] . '&amp;startat=' . ($startat - $caches_per_page) . '">{prev_img}</a> ';
} else {
    $pages = '{first_img_inactive} {prev_img_inactive} ';
}

$frompage = ($startat / $caches_per_page) - 3;
if ($frompage < 1)
    $frompage = 1;

$maxpage = ceil($resultcount / $caches_per_page);

$topage = $frompage + 8;
if ($topage > $maxpage)
    $topage = $maxpage;

if ($topage > 1) {
    for ($i = $frompage; $i <= $topage; $i ++) {
        if (($startat / $caches_per_page + 1) == $i) {
            $pages .= ' <b>' . $i . '</b>';
        } else {
            $pages .= ' <a href="search.php?queryid=' . $options['queryid'] . '&amp;startat=' . (($i - 1) * $caches_per_page) . '">' . $i . '</a>';
        }
    }

    if ($startat / $caches_per_page < ($maxpage - 1)) {
        $pages .= ' <a href="search.php?queryid=' . $options['queryid'] . '&amp;startat=' . ($startat + $caches_per_page) . '">{next_img}</a> <a href="search.php?queryid=' . $options['queryid'] . '&amp;startat=' . (($maxpage - 1) * $caches_per_page) . '">{last_img}</a> ';
    } else {
        $pages .= ' {next_img_inactive} {last_img_inactive}';
    }

    $pages = mb_ereg_replace('{prev_img}', $prev_img, $pages);
    $pages = mb_ereg_replace('{next_img}', $next_img, $pages);
    $pages = mb_ereg_replace('{last_img}', $last_img, $pages);
    $pages = mb_ereg_replace('{first_img}', $first_img, $pages);

    $pages = mb_ereg_replace('{prev_img_inactive}', $prev_img_inactive, $pages);
    $pages = mb_ereg_replace('{next_img_inactive}', $next_img_inactive, $pages);
    $pages = mb_ereg_replace('{first_img_inactive}', $first_img_inactive, $pages);
    $pages = mb_ereg_replace('{last_img_inactive}', $last_img_inactive, $pages);
    tpl_set_var('pages', $pages);
} else {
    tpl_set_var('pages', '');
}

$lhideColumns = 'search.php?queryid=' . $options['queryid'] . '&amp;startat=0';
tpl_set_var('lhideColumns', $lhideColumns);

// speichern-link
if ($usr === false)
    tpl_set_var('safelink', '');
else
    tpl_set_var('safelink', str_replace('{queryid}', $options['queryid'], $safelink));

    // downloads
if ($usr || ! $hide_coords)
    tpl_set_var('queryid', $options['queryid']);
tpl_set_var('startat', $startat);

tpl_set_var('startatp1', $startat + 1);

    //if (($resultcount - $startat) < 500)
        tpl_set_var('endat', $startat + $resultcount - $startat);
    //else
        //tpl_set_var('endat', $startat + 500);

    // kompatibilitĂ¤t!
    if ($distance_unit == 'sm')
        tpl_set_var('distanceunit', 'mi');
    else if ($distance_unit == 'nm')
        tpl_set_var('distanceunit', 'sm');
    else
        tpl_set_var('distanceunit', $distance_unit);

    if ($usr !== false){
        $queryid = $options['queryid'];
        $google_kml_link = $absolute_server_URI . "search.php?queryid=$queryid&output=kml&startat=$startat";
        $google_kml_link .= requestSigner::get_signature_text();

        $google_kml_link_all = $google_kml_link.'&count=max&zip=1';

        $domain = substr(trim($absolute_server_URI,'/'),-2,2); // It should be done better

        $google_maps_link = "//maps.google.$domain/maps?f=q&hl=$lang&geocode=&q=";
        $google_maps_link_all = "//maps.google.$domain/maps?f=q&hl=$lang&geocode=&ie=UTF8&z=7&q=";


        $google_maps_link = htmlentities($google_maps_link).urlencode($google_kml_link);
        $google_maps_link_all = htmlentities($google_maps_link_all).urlencode($google_kml_link_all);

        tpl_set_var('google_maps_link', $google_maps_link);
        tpl_set_var('google_maps_link_all', $google_maps_link_all);
    }



    tpl_BuildTemplate();


function trChar( $word )
{
    $word = str_replace("Ą", "A|", $word);
    $word = str_replace("ą", "A|", $word);

    $word = str_replace("Ć", "C|", $word);
    $word = str_replace("ć", "C|", $word);

    $word = str_replace("Ę", "E|", $word);
    $word = str_replace("ę", "E|", $word);

    $word = str_replace("Ł", "L|", $word);
    $word = str_replace("ł", "L|", $word);

    $word = str_replace("Ń", "N|", $word);
    $word = str_replace("ń", "N|", $word);

    $word = str_replace("Ó", "O|", $word);
    $word = str_replace("ó", "O|", $word);

    $word = str_replace("Ś", "S|", $word);
    $word = str_replace("ś", "S|", $word);

    $word = str_replace("Ź", "Z|", $word);
    $word = str_replace("ź", "Z|", $word);

    $word = str_replace("Ż", "Ż|", $word);
    $word = str_replace("ż", "Ż|", $word);

    return $word;
}

function PrepareText( $text )
{
    $log_text = strip_tags( $text, '');
    $log_text = str_replace("\r\n", " ",$log_text);
    $log_text = str_replace("\n", " ",$log_text);
    $log_text = str_replace("'", "-",$log_text);
    $log_text = str_replace("\"", " ",$log_text);
    $log_text = str_replace("\\", " ",$log_text);

    return $log_text;
}


