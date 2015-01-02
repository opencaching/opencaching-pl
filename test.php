<?php
require_once 'lib/kint/Kint.class.php';
require_once 'lib/common.inc.php';
ini_set('max_execution_time', 120);
error_reporting(-1);



$medals = new \lib\Controllers\MedalsController();
if ($medals->config->getMedalsModuleSwitchOn() === true) {
    $medals->checkAllUsersMedals();
}

dd($medals);


exit;

function fillAltitudeTable()
{
    $db = \lib\Database\DataBaseSingleton::Instance();
    $cachesAltitudeCount = 0;
    for ($i = 0; $i < 100; $i++) {
        $query = 'SELECT caches.`cache_id`, `latitude`, `longitude` FROM `caches` WHERE NOT EXISTS(SELECT * FROM caches_additions WHERE caches.cache_id=caches_additions.cache_id) LIMIT 50';
        $db->simpleQuery($query);
        $caches = $db->dbResultFetchAll();
        $gapiStr = 'http://maps.googleapis.com/maps/api/elevation/xml?locations=';
        if (count($caches) === 0) {
            print 'no caches to process';
            break;
        }
        foreach ($caches as $key => $cache) {
            $cache['latitude'] = $caches[$key]['latitude'] = number_format($cache['latitude'], 7, '.', '');
            $cache['longitude'] = $caches[$key]['longitude'] = number_format($cache['longitude'], 7, '.', '');
            $gapiStr .= $cache['latitude'] . ',' . $cache['longitude'] . '|';
        }
        $url = rtrim($gapiStr, "|");
        $altitudes = simplexml_load_file($url);
        if ($altitudes) {
            storeAlitudeToDb($altitudes, $caches, $cachesAltitudeCount);
        } else {
            d($url, $altitudes, $caches);
            break;
        }
        d($url, $altitudes, $caches);
    }
    print '<br><br>' . $cachesAltitudeCount . ' caches altitudes added';
}

function storeAlitudeToDb($altitudes, $caches, &$cachesAltitudeCount)
{
    $status = (string) $altitudes->status;
    if ($status !== 'OK') {
        print 'error occured';
        return;
    }
    $db = \lib\Database\DataBaseSingleton::Instance();
    $i = 0;
    foreach ($altitudes->result as $key => $value) {
        $lat = (string) $value->location->lat;
        $lon = (string) $value->location->lng;
        $alt = (string) $value->elevation;
        $altInt = (int) round($alt);
        if (round($caches[$i]['latitude'], 7) == $lat && round($caches[$i]['longitude'], 7) == $lon) {
            $query2 = 'INSERT INTO caches_additions (cache_id, altitude) VALUES (:2, :1) ';
            $db->multiVariableQuery($query2, $altInt, $caches[$i]['cache_id']);
        }
        // d($altInt, $key, $value, $lat, $lon, $caches[$i]);
        $i++;
        $cachesAltitudeCount++;
    }
    return $cachesAltitudeCount;
}

// include __DIR__.'/util.sec/notification/run_notify.php';
// include __DIR__.'/util.sec/geokrety/processGeokretyErrors.php';
