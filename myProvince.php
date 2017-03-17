<?php
use lib\Objects\GeoCache\GeoCacheLog;
use lib\Objects\ChunkModels\ListOfCaches;
use Utils\Gis\Region;
use lib\Objects\ApplicationContainer;


if (! isset($rootpath))
    $rootpath = '';

// include template handling
require_once ($rootpath . 'lib/common.inc.php');
require_once ($rootpath . 'lib/calculation.inc.php');
require_once ($rootpath . 'lib/cache_icon.inc.php');
require_once ($stylepath . '/lib/icons.inc.php');

if ($error) {
    // TODO:
    exit;
}

// user logged in?
if ($usr == false) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);
}

$applicationContainer = ApplicationContainer::Instance();
$db = $applicationContainer->db;

// get user record
$user_id = $applicationContainer->getLoggedUser()->getUserId();
tpl_set_var('userid', $user_id);


tpl_set_tplname('myProvince');
$view = tpl_getView();

// load chunk 'list of caches'
$view->loadChunk('listOfCaches');

if( !isset($_GET['province']) ||
    !Region::checkProvinceCode($_GET['province']) ){

        echo "<p><b>[temporary error message] There is no 'province' parameter in url or this parameter value is wrong!</b></p>";

        echo "<p>Check wiki page for proper region codes:
            <a href='https://en.wikipedia.org/wiki/Nomenclature_of_Territorial_Units_for_Statistics'>
            https://en.wikipedia.org/wiki/Nomenclature_of_Territorial_Units_for_Statistics</a></p>";

        echo "<p>For Poland it can be found here:<a href='https://pl.wikipedia.org/wiki/Jednostki_NUTS_w_Polsce'>
            https://pl.wikipedia.org/wiki/Jednostki_NUTS_w_Polsce</a></p>";

        echo "<p>For example proper URL for Pomorskie voivodeship in Poland is:
            <a href='myProvince.php?province=PL63'>
            https://opencaching.pl/myProvince.php?province=PL63</a></p>";

        echo "<p>Note: Only NUTS level-2 codes are supported now!</p>";
        exit;
}


$province = $_GET['province'];
$view->setVar('provinceName', Region::getRegionName($province));


$db->multiVariableQuery(
    "CREATE TEMPORARY TABLE province_caches ENGINE=MEMORY
    SELECT c.cache_id, wp_oc, c.type, c.name, longitude, latitude, date_hidden,
          date_created, difficulty, terrain, founds, c.status, user_id,
          pt.id AS ptId, LEFT(pt.name, 64) AS ptName, pt.type As ptType
    FROM `caches` AS c
        JOIN cache_location AS cl ON c.cache_id = cl.cache_id
        LEFT JOIN powerTrail_caches
            ON ( c.cache_id = powerTrail_caches.cacheId )
        LEFT JOIN PowerTrail AS pt
            ON ( pt.id = powerTrail_caches.PowerTrailId
                AND pt.status = 1 )
    WHERE cl.code3 = :1", $province);


$db->simpleQuery(
    'ALTER TABLE province_caches
            ADD PRIMARY KEY (`cache_id`),
            ADD INDEX(`cache_id`),
            ADD INDEX (`wp_oc`),
            ADD INDEX(`type`),
            ADD INDEX(`name`),
            ADD INDEX(`user_id`),
            ADD INDEX(`date_hidden`),
            ADD INDEX(`date_created`)' );

/* ===================================================================================== */
/* Newest caches */
/* ===================================================================================== */

$rs = $db->simpleQuery(
    "SELECT u.user_id, u.username, c.cache_id, c.name, c.longitude, c.latitude,
            c.date_hidden, c.date_created,
            IF((c.date_hidden > c.date_created), c.date_hidden, c.date_created) AS date,
            c.difficulty, c.terrain, ct.icon_large, c.type AS cache_type,
            c.ptId, c.ptName, c.ptType
    FROM cache_type AS ct, province_caches AS c
        INNER JOIN user AS u ON (c.user_id = u.user_id)
    WHERE c.type != 6
        AND c.status = 1
        AND c.type = ct.id
        AND c.date_created <= NOW()
        AND c.date_hidden <= NOW()
    ORDER BY date DESC, c.cache_id DESC
    LIMIT 0, 10");

$newCaches = new ListOfCaches();
while($record = $db->dbResultFetch($rs)){

    $cache = $newCaches->getCacheModel();

    $cache->icon = myninc::checkCacheStatusByUser($record, $user_id);
    $cache->date = htmlspecialchars(date($dateFormat, strtotime($record['date'])));
    $cache->cacheName = htmlspecialchars($record['name']);
    $cache->cacheId = $record['cache_id'];
    $cache->userName = htmlspecialchars($record['username']);

    $cache->ptEnabled = ($record['ptId'] != NULL);
    $cache->ptId = $record['ptId'];
    $cache->ptName = $record['ptName'];
    $cache->ptIcon = getPtIconByType( $record['ptType'] );

    $newCaches->addCache($cache);
}

$view->setVar('newCaches', $newCaches);


/* ===================================================================================== */
/* Incomming events */
/* ===================================================================================== */

$rs = $db->simpleQuery(
    "SELECT u.user_id, u.username, c.cache_id, c.name,
         c.longitude, c.latitude, c.date_hidden,
         c.date_created, c.type AS cache_type, c.difficulty,
         c.terrain, ct.icon_large,
         c.ptId, c.ptName, c.ptType
     FROM user AS u, cache_type AS ct, province_caches AS c
     WHERE c.user_id = u.user_id
        AND c.type = 6
        AND c.status=1
        AND c.type = ct.id
        AND c.date_hidden >= curdate()
     ORDER BY `date_hidden` ASC
     LIMIT 0 , 10");

$incommingEvents = new ListOfCaches();
while($record = $db->dbResultFetch($rs)){

    $cache = $incommingEvents->getCacheModel();

    $cache->icon = myninc::checkCacheStatusByUser($record, $user_id);
    $cache->date = htmlspecialchars(date($dateFormat, strtotime($record['date_hidden'])));
    $cache->cacheName = htmlspecialchars($record['name']);
    $cache->cacheId = $record['cache_id'];
    $cache->userName = htmlspecialchars($record['username']);
    $cache->userId = htmlspecialchars($record['user_id']);

    $cache->ptEnabled = ($record['ptId'] != NULL);
    $cache->ptId = $record['ptId'];
    $cache->ptName = $record['ptName'];
    $cache->ptIcon = getPtIconByType( $record['ptType'] );

    $incommingEvents->addCache($cache);
}

$view->setVar('incommingEvents', $incommingEvents);


/* ===================================================================================== */
/* FTF's */
/* ===================================================================================== */

$rs = $db->simpleQuery(
    "SELECT u.user_id, u.username, c.cache_id, c.name, c.longitude, c.latitude,
            c.date_hidden, c.date_created,
            IF((c.date_hidden > c.date_created), c.date_hidden, c.date_created) AS date,
            c.difficulty, c.terrain, ct.icon_large, c.type AS cache_type,
            c.ptId, c.ptName, c.ptType
    FROM province_caches AS c
        INNER JOIN `user` AS u ON (c.user_id = u.user_id),
        cache_type AS ct
    WHERE c.type != 6
        AND c.status =1
        AND c.type = ct.id
        AND c.founds = 0
    ORDER BY date DESC, c.cache_id DESC
    LIMIT 0, 10");

$ftfs = new ListOfCaches();
while($record = $db->dbResultFetch($rs)){

    $cache = $ftfs->getCacheModel();
    $cache->icon = myninc::checkCacheStatusByUser($record, $user_id);
    $cache->date = htmlspecialchars(date($dateFormat, strtotime($record['date'])));
    $cache->cacheName = htmlspecialchars($record['name']);
    $cache->cacheId = $record['cache_id'];
    $cache->userName = htmlspecialchars($record['username']);
    $cache->userId = htmlspecialchars($record['user_id']);

    $cache->ptEnabled = ($record['ptId'] != NULL);
    $cache->ptId = $record['ptId'];
    $cache->ptName = $record['ptName'];
    $cache->ptIcon = getPtIconByType( $record['ptType'] );

    $ftfs->addCache($cache);
}

$view->setVar('ftfs', $ftfs);

/* ===================================================================================== */
/* Newest comments */
/* ===================================================================================== */

// Read just log IDs first - this gets easily optimized

// find 11 newest, not deleted logs for caches from selected province
$rs = $db->simpleQuery(
    "SELECT cache_logs.id
    FROM cache_logs
    WHERE cache_logs.deleted = 0
        AND cache_logs.cache_id IN (
            SELECT cache_id FROM province_caches
        )
    ORDER BY cache_logs.date_created DESC
    LIMIT 0, 10");


$lastLogsIds = $db->dbResultFetchAll($rs, PDO::FETCH_COLUMN);

if(is_null($lastLogsIds) || empty($lastLogsIds)){
    $lastLogsIds = array('-1');
}

// Now use a set of log IDs to retrieve all other necessary information
$rs = $db->simpleQuery(
    "SELECT
        cl.id, cl.cache_id, cl.type AS log_type,
        cl.date AS log_date, cl.text AS log_text,
        cl.text_html,

        c.name AS cache_name,
        c.wp_oc AS wp_name, c.type AS cache_type,
        c.longitude, c.latitude, c.user_id,

        u.username, u.user_id AS luser_id,

        log_types.icon_small,
        IF(ISNULL(cr.cache_id), 0, 1) AS `recommended`,
        COUNT(gk_item.id) AS geokret_in,
        c.ptId, c.ptName, c.ptType

    FROM province_caches AS c
        INNER JOIN cache_logs AS cl ON (c.cache_id = cl.cache_id)
        INNER JOIN user AS u ON (cl.user_id = u.user_id)
        INNER JOIN log_types ON (cl.type = log_types.id)
        INNER JOIN cache_type ON (c.type = cache_type.id)
        LEFT JOIN cache_rating AS cr
            ON (cl.cache_id=cr.cache_id AND cl.user_id=cr.user_id)
        LEFT JOIN gk_item_waypoint ON (gk_item_waypoint.wp = c.wp_oc)
        LEFT JOIN gk_item
            ON (gk_item.id = gk_item_waypoint.id
                AND gk_item.stateid<>1 AND gk_item.stateid<>4
                AND gk_item.typeid<>2 AND gk_item.stateid !=5)
    WHERE cl.id IN (" . implode(',',$lastLogsIds) . ")
        AND c.cache_id = cl.cache_id
    GROUP BY cl.id
    ORDER BY cl.date_created DESC LIMIT 0, 10");

$lastLogs = new ListOfCaches();
$lastLogs->enableLogTooltip();

while($record = $db->dbResultFetch($rs)){

    // Hide username of Adimin user in admin's logs comments
    if ($record['log_type'] == 12 && ! $usr['admin']) {
        $record['user_id'] = '0';
        $record['user_name'] = tr('cog_user_name');
    }


    $cache = $lastLogs->getCacheModel();
    $cache->icon = myninc::checkCacheStatusByUser($record, $user_id);
    $cache->date = htmlspecialchars(date($dateFormat, strtotime($record['log_date'])));
    $cache->cacheName = htmlspecialchars($record['cache_name']);
    $cache->cacheId = $record['cache_id'];
    $cache->userName = htmlspecialchars($record['username']);
    $cache->userId = htmlspecialchars($record['luser_id']);

    $cache->logIcon = 'tpl/stdstyle/images/'.$record['icon_small']; //TODO: hardcoded PATH

    $logtext = '<b>'.$record['username'].'</b>:<br/>';
    $logtext .= GeoCacheLog::cleanLogTextForToolTip($record['log_text']);

    $cache->logText = htmlspecialchars($logtext, ENT_COMPAT, 'UTF-8');

    $cache->ptEnabled = ($record['ptId'] != NULL);
    $cache->ptId = $record['ptId'];
    $cache->ptName = $record['ptName'];
    $cache->ptIcon = getPtIconByType( $record['ptType'] );

    $lastLogs->addCache($cache);
}

$view->setVar('lastLogs', $lastLogs);

/* ===================================================================================== */
/* TOP recommended caches */
/* ===================================================================================== */

$rs = $db->simpleQuery(
    "SELECT u.user_id, u.username, c.cache_id, c.name, c.longitude, c.latitude,
        c.date_hidden, c.date_created, c.difficulty, c.terrain, ct.icon_large,
        c.type AS cache_type, count(cr.cache_id) AS toprate,
        c.ptId, c.ptName, c.ptType
     FROM province_caches AS c
        INNER JOIN user AS u ON (c.user_id = u.user_id)
        LEFT JOIN cache_rating AS cr ON (c.cache_id = cr.cache_id),
        cache_type AS ct
     WHERE c.type != 6
        AND cr.cache_id = c.cache_id
        AND c.status = 1
        AND c.type = ct.id
     GROUP BY c.cache_id
     ORDER BY toprate DESC, c.name ASC
     LIMIT 0 , 11");

$topRecos = new ListOfCaches();
$topRecos->enableRecoColumn();

while($record = $db->dbResultFetch($rs)){

    $cache = $topRecos->getCacheModel();
    $cache->icon = myninc::checkCacheStatusByUser($record, $user_id);
    $cache->date = htmlspecialchars(date($dateFormat, strtotime($record['date_hidden'])));
    $cache->cacheName = htmlspecialchars($record['name']);
    $cache->cacheId = $record['cache_id'];
    $cache->userName = htmlspecialchars($record['username']);
    $cache->userId = htmlspecialchars($record['user_id']);
    $cache->recoNum = $record['toprate'];

    $cache->ptEnabled = ($record['ptId'] != NULL);
    $cache->ptId = $record['ptId'];
    $cache->ptName = $record['ptName'];
    $cache->ptIcon = getPtIconByType( $record['ptType'] );

    $topRecos->addCache($cache);
}

$view->setVar('topRecos', $topRecos);

/* ===================================================================================== */
/* Titled caches (pl:skrzynki tygodnia) */
/* ===================================================================================== */

global $titled_cache_period_prefix; //from node config

//translation of the title caches can be different for nodes and languages...
tpl_set_var('titledCaches_title', tr($titled_cache_period_prefix.'_titled_caches'));

$rs = $db->simpleQuery(
    "SELECT u.user_id, u.username,
            c.cache_id, c.name,
            cache_titled.date_alg AS date,
            c.type AS cache_type,
            c.ptId, c.ptName, c.ptType
     FROM cache_titled
        JOIN province_caches AS c ON cache_titled.cache_id = c.cache_id
        JOIN user AS u ON c.user_id = u.user_id
     WHERE c.status = 1
     ORDER BY cache_titled.date_alg DESC
     LIMIT 0 , 11");

$titledCaches = new ListOfCaches();
while($record = $db->dbResultFetch($rs)){

    $cache = $titledCaches->getCacheModel();
    $cache->icon = myninc::checkCacheStatusByUser($record, $user_id);
    $cache->date = htmlspecialchars(date($dateFormat, strtotime($record['date'])));
    $cache->cacheName = htmlspecialchars($record['name']);
    $cache->cacheId = $record['cache_id'];
    $cache->userName = htmlspecialchars($record['username']);
    $cache->userId = htmlspecialchars($record['user_id']);

    $cache->ptEnabled = ($record['ptId'] != NULL);
    $cache->ptId = $record['ptId'];
    $cache->ptName = $record['ptName'];
    $cache->ptIcon = getPtIconByType( $record['ptType'] );

    $titledCaches->addCache($cache);
}

$view->setVar('enableTitleCaches', ($titledCaches->getCachesCount()>0));
$view->setVar('titledCaches', $titledCaches);



// make the template and send it out
tpl_BuildTemplate();

