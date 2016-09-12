<?php

use Utils\Database\XDb;
/**
 * This script is used only by Map_v2 to display markers on the map
 */

$rootpath = "../";
require_once('./common.inc.php');

if ($usr == false) {
    echo "Error... User not logged!";
    exit;
}



$ENCODING = "UTF-8";

function onTheList($theArray, $item)
{
    for ($i = 0; $i < count($theArray); $i++) {
        if ($theArray[$i] == $item)
            return $i;
    }
    return -1;
}

function typeLetter($intType)
{
    switch ($intType) {
        case 1: return "U";
            break;      //unknown
        case 2: return "T";
            break;      //traditional
        case 3: return "M";
            break;      //multi
        case 4: return "V";
            break;      //virtual
        case 5: return "W";
            break;      //webcam
        case 6: return "E";
            break;      //event
        case 7: return "Q";
            break;      //quiz
        case 8: return "O";
            break;      //mOving
        case 9: return "C";
            break;      //math
        case 10: return "D";
            break; //Drive-in
        default: return "";
    }
}

function typeToInt($type)
{
    switch ($type) {
        case 'u': return 1;
            break;      //unknown
        case 't': return 2;
            break;      //traditional
        case 'm': return 3;
            break;      //multi
        case 'v': return 4;
            break;      //virtual
        case 'w': return 5;
            break;      //webcam
        case 'e': return 6;
            break;      //event
        case 'q': return 7;
            break;      //quiz
        case 'o': return 8;
            break;      //mOving
        case 'c': return 9;
            break;      //math
        case 'd': return 10;
            break; //Drive-in
        default: return "";
    }
}


$latNE = $_GET['latNE'];
$lonNE = $_GET['lonNE'];
$latSW = $_GET['latSW'];
$lonSW = $_GET['lonSW'];

if (!isset($_GET['caches'])) {
    $PER_PAGE = 250;
} else {
    $PER_PAGE = (int) ($_GET['caches']);
    if ($PER_PAGE < 10)
        $PER_PAGE = 10;
    if ($PER_PAGE > 500)
        $PER_PAGE = 500;
}

/*
http://local.opencaching.pl/lib/marker.php?
klaun=1&
latNE=53.39933109811506&
lonNE=18.60064679362017&
latSW=52.90520838678868&
lonSW=17.52673321940142&
page=0&
caches=[object%20CacheStorage]&
order=1&
filter=11111111111111111111111111111111001111111100001111
*/

$page = ((int) ($_GET['page'])) * $PER_PAGE;

if( !isset($_GET['u']) || $_GET['u'] != $usr['userid']){
    echo "Error... No user given of user different than logged user?!";
    exit;
}

$user_id = (int) $_GET['u'];

switch ($_GET['order']) {
    case "1":
        $ORDERBY = "cache_id DESC";
        break;
    case "2":
        $ORDERBY = "name";
        break;
    case "3":
        $ORDERBY = "old";
        break;
    default:
        $ORDERBY = "cache_id DESC";
}

$filter_by_type_string = "";
$typy = array('u', 't', 'm', 'v', 'w', 'e', 'q', 'o', 'c', 'd', 'I', 'W', 'Z', 'A', 'N', 'C', 'T', 'Y');

$filter = $_GET['filter'];

$only_active = " AND (caches.status = 1 OR caches.status = 2)";
for ($i = 0; $i < strlen($filter); $i++) {
    if ($i < 10) {
        if ($filter[$i] == 0)
            $filter_by_type_string .= " AND caches.type != " . ($i + 1);
    }
    else {
        if ($i == 10 && $filter[$i] == 0) // I
            $filter_by_type_string .= " AND cache_id NOT IN (SELECT cache_id FROM cache_ignore WHERE user_id='" . XDb::xEscape($user_id) . "')";

        if ($i == 11 && $filter[$i] == 0) // W
            $filter_by_type_string .= " AND cache_id NOT IN (SELECT cache_id FROM caches WHERE user_id='" . XDb::xEscape($user_id) . "')";

        if ($i == 12 && $filter[$i] == 0) // Z
            $filter_by_type_string .= " AND cache_id NOT IN (SELECT cache_id FROM cache_logs WHERE deleted=0 AND user_id='" . XDb::xEscape($user_id) . "' AND (type='1' OR type='8'))";


        if ($i == 13 && $filter[$i] == 0) // A
            $filter_by_type_string .= " AND (cache_id IN (SELECT cache_logs.cache_id FROM cache_logs WHERE deleted=0 AND cache_logs.user_id='" . XDb::xEscape($user_id) . "' AND (cache_logs.deleted=0 AND (cache_logs.type='1' OR cache_logs.type='8'))) OR caches.user_id='" . XDb::xEscape($user_id) . "')";

        if ($i == 14 && $filter[$i] == 0) // N
            $filter_by_type_string .= " AND caches.cache_id IN (SELECT cache_id FROM caches WHERE wp_oc IN (SELECT wp FROM gk_item_waypoint WHERE id IN (SELECT id FROM gk_item WHERE stateid<>1 AND stateid<>4 AND typeid<>2)) OR (wp_gc IN (SELECT wp FROM gk_item_waypoint WHERE id IN (SELECT id FROM gk_item WHERE stateid<>1 AND stateid<> 4 AND typeid<>2)) AND wp_gc <> '') OR (wp_nc IN (SELECT wp FROM gk_item_waypoint WHERE id IN (SELECT id FROM gk_item WHERE stateid<>1 AND stateid<>4 AND typeid<>2)) AND wp_nc <> '')) ";

        // 15 - RESERVED - DO NOT USE !!!
        if ($i == 16 && $filter[$i] == 0) // T
            $only_active .= " AND caches.status = 1";
        if ($i == 17 && $filter[$i] == 0) // Y
            $only_active .= " AND caches.status = 2";
    }
}

$result = XDb::xSql(
    "SELECT caches.cache_id, caches.name, user.username, caches.wp_oc as wp, caches.votes, caches.score,
            caches.topratings, caches.latitude, caches.longitude, caches.type, caches.status as status,
            datediff(now(), caches.date_hidden) as old, caches.user_id,
            IF(cache_id IN
                (
                    SELECT cache_id
                    FROM cache_logs
                    WHERE deleted=0 AND user_id= ?
                        AND (type=1 OR type=8)
                ), 1, 0
            ) as found
    FROM user, caches
    WHERE (caches.user_id = user.user_id)
        AND caches.latitude > ? AND caches.latitude < ?
        AND caches.longitude > ? AND caches.longitude < ?
        " . XDb::xEscape($only_active) . " " . ($filter_by_type_string) . "
    ORDER BY " . XDb::xEscape($ORDERBY) . "
    LIMIT " . XDb::xEscape($page) . ", " . XDb::xEscape($PER_PAGE),
    $user_id, $latSW, $latNE, $lonSW, $lonNE);


header('Content-type: text/xml');
echo "<?xml version=\"1.0\" encoding=\"" . $ENCODING . "\"?>\n";
echo "<markers>\n";
while ($res = XDb::xFetchArray($result)) {

    if (!isset($_REQUEST['print_list']) || onTheList($_SESSION['print_list'], $res['cache_id']) == -1)
        $druk = "druk=\"y\"";
    else
        $druk = "druk=\"n\"";

    $founds = XDb::xMultiVariableQueryValue(
        "SELECT count(*) FROM cache_logs
        WHERE deleted=0 AND cache_id = :1
            AND (type=1 OR type=8)",
        0, $res['cache_id']);

    $notfounds = XDb::xMultiVariableQueryValue(
        "SELECT count(*) FROM cache_logs
        WHERE deleted=0 AND cache_id = :1 AND type=2",
        0, $res['cache_id']);

    if ($res['votes'] > 2)
        $score = $res['score'];
    else
        $score = "";
    echo "<marker id=\"" . htmlspecialchars($res['cache_id']) . "\" name=\"" . htmlspecialchars($res['name']) . "\" lat=\"" . htmlspecialchars($res['latitude']) . "\" lng=\"" . htmlspecialchars($res['longitude']) . "\" owner=\"" . htmlspecialchars($res['username']) . "\" owner_id=\"" . htmlspecialchars($res['user_id']) . "\" type=\"" . htmlspecialchars(typeLetter($res['type'])) . "\" found=\"" . $res['found'] . "\" old=\"" . htmlspecialchars($res['old']) . "\" score=\"" . htmlspecialchars($score) . "\" topratings=\"" . htmlspecialchars($res['topratings']) . "\"  notfounds=\"" . htmlspecialchars($notfounds) . "\" votes=\"" . htmlspecialchars($res['votes']) . "\" founds=\"" . htmlspecialchars($founds) . "\" status=\"" . htmlspecialchars($res['status']) . "\" wp=\"" . htmlspecialchars($res['wp']) . "\" " . $druk . "/>\n";
}

$res2['num'] = XDb::xMultiVariableQueryValue(
    "SELECT count(cache_id) as num FROM caches
    WHERE caches.status = 1
        AND caches.latitude  > :1
        AND caches.latitude  < :2
        AND caches.longitude > :3
        AND caches.longitude < :4 ",
    0, $latSW, $latNE, $lonSW, $lonNE);

$pages = '';
for ($i = 0; $i < $res2['num'] / $PER_PAGE; $i++)
    $pages .= "<a href=\"javascript:load_data(" . $i . ");\">" . ($i + 1) . "</a> ";
$pages = htmlspecialchars($pages);
echo "<data count=\"" . $res2['num'] . "\" pager=\"" . $pages . "\" />\n";

echo "</markers>";

