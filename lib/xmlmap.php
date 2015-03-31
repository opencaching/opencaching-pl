<?php

$rootpath = "../";
require_once('./common.inc.php');
require_once($rootpath . 'lib/caches.inc.php');

function getUsername($user_id)
{
    $sql = "SELECT username FROM user WHERE user_id=" . intval($user_id);
    return @mysql_result(@mysql_query($sql), 0);
}

if (isset($_GET['searchdata']) && preg_match('/^[a-f0-9]+/', $_GET['searchdata'])) {
    $searchdata = $_GET['searchdata'];
}

$latmin = sql_escape($_GET['latmin']);
$latmax = sql_escape($_GET['latmax']);
$lonmin = sql_escape($_GET['lonmin']);
$lonmax = sql_escape($_GET['lonmax']);

if (($latmin == $latmax) && ($lonmin == $lonmax)) {
    // Special case for showing marker for specific cache - just single coordinate provided
    // Use small buffer to handle this case
    $latmin -= 0.00001;
    $lonmin -= 0.00001;
    $latmax += 0.00001;
    $lonmax += 0.00001;
}

$user_id = intval($_GET['userid']);
$username = getUsername($user_id);

header('Content-Type: text/xml');

$writer = new XMLWriter();

$writer->openURI('php://output');
$writer->startDocument('1.0');
$writer->setIndent(4);
$writer->startElement('caches');

if (!isset($searchdata)) {

    if ($_GET['be_ftf'] == "true") {
        $own_not_attempt = "caches.founds>0";
        $_GET['h_temp_unavail'] = "true";
        $_GET['h_arch'] = "true";
    } else
        $own_not_attempt = "caches.cache_id IN (SELECT cache_id FROM cache_logs WHERE deleted=0 AND user_id='" . sql_escape($user_id) . "' AND (type=1 OR type=8))";

    $hide_by_type = "";
    if ($_GET['h_u'] == "true")
        $hide_by_type .= " AND caches.type<>1 ";
    if ($_GET['h_t'] == "true")
        $hide_by_type .= " AND caches.type<>2 ";
    if ($_GET['h_m'] == "true")
        $hide_by_type .= " AND caches.type<>3 ";
    if ($_GET['h_v'] == "true")
        $hide_by_type .= " AND caches.type<>4 ";
    if ($_GET['h_w'] == "true")
        $hide_by_type .= " AND caches.type<>5 ";
    if ($_GET['h_e'] == "true")
        $hide_by_type .= " AND caches.type<>6 ";
    if ($_GET['h_q'] == "true")
        $hide_by_type .= " AND caches.type<>7 ";
    if ($_GET['h_o'] == "true")
        $hide_by_type .= " AND caches.type<>8 ";
    if ($_GET['h_owncache'] == "true")
        $hide_by_type .= " AND caches.type<>10 ";
    if ($_GET['h_own'] == "true")
        $hide_by_type .= " AND caches.user_id<>" . $user_id . " ";
    if ($_GET['h_found'] == "true")
        $hide_by_type .= " AND IF($own_not_attempt, 1, 0)<>1 ";
    if ($_GET['be_ftf'] == "true")
        $hide_by_type .= " AND (IF($own_not_attempt, 1, 0)<>1 AND caches.status=1 AND caches.user_id<>" . $user_id . ") ";

    if ($_GET['powertrail_only'] == "true")
        $joins[] = "INNER JOIN powerTrail_caches ON powerTrail_caches.cacheId = caches.cache_id";

    if ($_GET['h_avail'] == "true")
        $hide_by_type .= " AND caches.status<>1 ";
    if ($_GET['h_temp_unavail'] == "true")
        $hide_by_type .= " AND caches.status<>2 ";
    if ($_GET['h_arch'] == "true")
        $hide_by_type .= " AND caches.status<>3 ";
    if ($_GET['h_noattempt'] == "true")
        $hide_by_type .= " AND IF($own_not_attempt, 1, 0)=1 ";
    if ($_GET['h_ignored'] == "true")
        $hide_by_type .= " AND cache_ignore.id IS NULL ";

    $t = floatval($_GET['min_score']);
    $min = ($t < 0) ? 1 : (($t < 1) ? 2 : (($t < 1.5) ? 3 : (($t < 2.2) ? 4 : 5)));
    $t = floatval($_GET['max_score']);
    $max = ($t < 0.7) ? 1 : (($t < 1.3) ? 2 : (($t < 2.2) ? 3 : (($t < 2.7) ? 4 : 5)));
    $allow_null = ($_GET['h_noscore'] == "true");
    if (($min == 1) && ($max == 5) && $allow_null) {
        $score_filter = "";
    } else {
        $divisors = array(-999, -1.0, 0.1, 1.4, 2.2, 999);
        $min = $divisors[$min - 1];
        $max = $divisors[$max];
        $score_filter = " AND ((caches.score >= $min and caches.score < $max and caches.votes >= 3)" .
                ($allow_null ? " or (caches.votes < 3)" : "") . ")";
    }

    // enable searching for ignored caches
    if ($_GET['h_ignored'] == "true") {
        $h_sel_ignored = "cache_ignore.id as ignored,";
        $h_ignored = " LEFT JOIN cache_ignore ON (cache_ignore.user_id=" . $user_id . " AND cache_ignore.cache_id=caches.cache_id) ";
    } else {
        $h_sel_ignored = "0 as ignored,";
        $h_ignored = "";
    }
    $joins[] = $h_ignored; 

    if ($_GET['h_nogeokret'] == "true")
        $filter_by_type_string .= " AND caches.cache_id IN (SELECT cache_id FROM caches WHERE wp_oc IN (SELECT wp FROM gk_item_waypoint WHERE id IN (SELECT id FROM gk_item WHERE stateid<>1 AND stateid<>4 AND stateid<>5 AND typeid<>2)) OR (wp_gc IN (SELECT wp FROM gk_item_waypoint WHERE id IN (SELECT id FROM gk_item WHERE stateid<>1 AND stateid<> 4 AND typeid<>2)) AND wp_gc <> '') OR (wp_nc IN (SELECT wp FROM gk_item_waypoint WHERE id IN (SELECT id FROM gk_item WHERE stateid<>1 AND stateid<>4 AND typeid<>2)) AND wp_nc <> '')) ";
    else
        $filter_by_type_string = "";

    $sql = "SELECT $h_sel_ignored caches.cache_id, IF($own_not_attempt, 1, 0) as found, caches.name, caches.node, user.username, caches.wp_oc as wp, caches.votes, caches.score, caches.topratings, caches.latitude, caches.longitude, caches.type, caches.size, caches.status as status, datediff(now(), caches.date_hidden) as old, caches.user_id, caches.founds, caches.notfounds 
            FROM user, caches
            ".implode(" ",$joins)."
            WHERE caches.user_id = user.user_id AND caches.status < 4 AND
                (caches.latitude BETWEEN $latmin AND $latmax) AND (caches.longitude BETWEEN $lonmin AND $lonmax)
                " . $hide_by_type . $filter_by_type_string . $score_filter . " LIMIT 1";


    // for foreign caches -------------------------------------------------------------------------------------


    $hide_by_type = "";
    if ($_GET['h_u'] == "true")
        $hide_by_type .= " AND foreign_caches.type<>1 ";
    if ($_GET['h_t'] == "true")
        $hide_by_type .= " AND foreign_caches.type<>2 ";
    if ($_GET['h_m'] == "true")
        $hide_by_type .= " AND foreign_caches.type<>3 ";
    if ($_GET['h_v'] == "true")
        $hide_by_type .= " AND foreign_caches.type<>4 ";
    if ($_GET['h_w'] == "true")
        $hide_by_type .= " AND foreign_caches.type<>5 ";
    if ($_GET['h_e'] == "true")
        $hide_by_type .= " AND foreign_caches.type<>6 ";
    if ($_GET['h_q'] == "true")
        $hide_by_type .= " AND foreign_caches.type<>7 ";
    if ($_GET['h_o'] == "true")
        $hide_by_type .= " AND foreign_caches.type<>8 ";
    if ($_GET['h_owncache'] == "true")
        $hide_by_type .= " AND foreign_caches.type<>10 ";
    //if( $_GET['h_own'] == "true" )
    //  $hide_by_type .= " AND foreign_caches.username<>'".$username."'";
    //if( $_GET['h_found'] == "true" )
    //  $hide_by_type .= " AND IF($own_not_attempt, 1, 0)<>1 ";
    //if( $_GET['be_ftf'] == "true" )
    //  $hide_by_type .= " AND (IF($own_not_attempt, 1, 0)<>1 AND foreign_caches.status=1 AND foreign_caches.user_id<>".$user_id.") ";
    if ($_GET['h_avail'] == "true")
        $hide_by_type .= " AND foreign_caches.status<>1 ";
    if ($_GET['h_temp_unavail'] == "true")
        $hide_by_type .= " AND foreign_caches.status<>2 ";
    if ($_GET['h_arch'] == "true")
        $hide_by_type .= " AND foreign_caches.status<>3 ";
    //if( $_GET['h_noattempt'] == "true" )
    //  $hide_by_type .= " AND IF($own_not_attempt, 1, 0)=1 ";
    // enable searching for ignored caches
    /*  if( $_GET['h_ignored'] == "true" )
      {
      $h_sel_ignored = "cache_ignore.id as ignored,";
      $h_ignored = " LEFT JOIN cache_ignore ON (cache_ignore.user_id='$user_id' AND cache_ignore.cache_id=foreign_caches.cache_id) ";
      }
      else
     */ {
        $h_sel_ignored = "";
        $h_ignored = "";
    }

    if ($_GET['h_nogeokret'] == "true")
        $filter_by_type_string = " AND foreign_caches.cache_id IN (SELECT cache_id FROM foreign_caches WHERE wp_oc IN (SELECT wp FROM gk_item_waypoint WHERE id IN (SELECT id FROM gk_item WHERE stateid<>1 AND stateid<>4 AND typeid<>2)) OR (wp_gc IN (SELECT wp FROM gk_item_waypoint WHERE id IN (SELECT id FROM gk_item WHERE stateid<>1 AND stateid<> 4 AND typeid<>2)) AND wp_gc <> '') OR (wp_nc IN (SELECT wp FROM gk_item_waypoint WHERE id IN (SELECT id FROM gk_item WHERE stateid<>1 AND stateid<>4 AND typeid<>2)) AND wp_nc <> '')) ";
    else
        $filter_by_type_string = "";


    $sql_foreign = "SELECT foreign_caches.cache_id, foreign_caches.name, foreign_caches.username, foreign_caches.node, foreign_caches.wp_oc as wp, foreign_caches.topratings, foreign_caches.latitude, foreign_caches.longitude, foreign_caches.type, foreign_caches.size, foreign_caches.status as status, datediff(now(), foreign_caches.date_hidden) as old, foreign_caches.founds, foreign_caches.notfounds 
                    FROM foreign_caches
                    WHERE foreign_caches.status < 4 AND
                        (foreign_caches.latitude BETWEEN $latmin AND $latmax) AND (foreign_caches.longitude BETWEEN $lonmin AND $lonmax)
                        " . $hide_by_type . $filter_by_type_string . " LIMIT 1";

    if (!($_GET['h_pl'] == "false")) {
        $query = mysql_query($sql);
        $cache = mysql_fetch_array($query);
    } else
        $cache = 0;

    if (!($_GET['h_de'] == "false")) {
        $query_foreign = mysql_query($sql_foreign);
        $cache_foreign = mysql_fetch_array($query_foreign);
    } else
        $cache_foreign = 0;

    if (($cache == 0) || ($cache['cache_id'] == ""))
        $cache = $cache_foreign;

} else { // searchdata

    mysql_query("CREATE TEMPORARY TABLE cache_ids (id INTEGER PRIMARY KEY) ENGINE=MEMORY;");
    mysql_query("LOAD DATA LOCAL INFILE '" . $dynbasepath . "/searchdata/" . $searchdata . "' INTO TABLE cache_ids FIELDS TERMINATED BY ' '  LINES TERMINATED BY '\\n' (id);");


    $own_not_attempt = "caches.cache_id IN (SELECT cache_id FROM cache_logs WHERE deleted=0 AND user_id='" . sql_escape($user_id) . "' AND (type=1 OR type=8))";

    $sql = "SELECT caches.cache_id, IF($own_not_attempt, 1, 0) as found, caches.name, caches.node, user.username, caches.wp_oc as wp, caches.votes, caches.score, caches.topratings, caches.latitude, caches.longitude, caches.type, caches.size, caches.status as status, datediff(now(), caches.date_hidden) as old, caches.user_id, caches.founds, caches.notfounds FROM user, caches
        WHERE caches.cache_id IN (SELECT * FROM cache_ids) AND caches.user_id = user.user_id AND caches.status < 4 AND
        (caches.latitude BETWEEN $latmin AND $latmax) AND (caches.longitude BETWEEN $lonmin AND $lonmax) LIMIT 1";

//      print $sql;
    $query = mysql_query($sql);
//      print mysql_error() . "\n";
    $cache = mysql_fetch_array($query);
    mysql_query("DROP TABLE cache_ids");
}

//while( $cache = mysql_fetch_array($query) )
{
    $writer->startElement("cache");

    $writer->writeAttribute('cache_id', $cache['cache_id']);
    @$writer->writeAttribute('name', addslashes($cache['name']));
    @$writer->writeAttribute('username', addslashes($cache['username']));
    $writer->writeAttribute('wp', $cache['wp']);
    $writer->writeAttribute('votes', $cache['votes']);
    $writer->writeAttribute('score', score2rating($cache['score']));
    $writer->writeAttribute('topratings', $cache['topratings']);
    $writer->writeAttribute('lat', $cache['latitude']);
    $writer->writeAttribute('lon', $cache['longitude']);
    $writer->writeAttribute('type', $cache['type']);
    $writer->writeAttribute('size', htmlspecialchars(cache_size_from_id($cache['size'], $lang), ENT_COMPAT, 'UTF-8'));
    $writer->writeAttribute('status', $cache['status']);
    $writer->writeAttribute('user_id', $cache['user_id']);
    $writer->writeAttribute('founds', $cache['founds']);
    $writer->writeAttribute('notfounds', $cache['notfounds']);
    $writer->writeAttribute('node', $cache['node']);

    // End cache
    $writer->endElement();
}

if (isset($query)) {
    mysql_free_result($query);
}
if (isset($query_foreign)) {
    mysql_free_result($query_foreign);
}

// End caches
$writer->endElement();
$writer->endDocument();
$writer->flush();
