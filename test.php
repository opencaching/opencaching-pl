<?php

require_once 'lib/kint/Kint.class.php';
require_once 'lib/common.inc.php';
error_reporting(-1);

if(isset($_GET['alt']) && $_GET['alt'] == 1){
    $data = fillAltitudeTable();
    display($data);
}

if(isset($_GET['medal']) && $_GET['medal'] == 1){
    uzupełnianie_medali();
}

if(isset($_GET['php']) && $_GET['php'] == 1){
  phpinfo();
}

function uzupełnianie_medali(){
    ini_set('max_execution_time', 120);
    $medals = new \lib\Controllers\MedalsController();
    if ($medals->config->getMedalsModuleSwitchOn() === true) {
        $medals->checkAllUsersMedals();
    }
}


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
            print 'no more caches to process';
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
        $result[] = $altitudes;
    }
    print '<br><br>' . $cachesAltitudeCount . ' caches altitudes added';
    return $result;
}

function storeAlitudeToDb($altitudes, $caches, &$cachesAltitudeCount)
{
    $status = (string) $altitudes->status;
    if ($status !== 'OK') {
        print 'error occured';
        d($caches, $altitudes);
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



function display($data){
?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Simple markers</title>
    <style>
      html, body {
        height: 100%;
        margin: 0px;
        padding: 0px
      }
      #map-canvas {
          width: 400px;
          height: 400px;
        margin: 0px;
        padding: 0px
      }
    </style>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
    <script>
function initialize() {
  var myLatlng = new google.maps.LatLng(50.363882,20.044922);
  var mapOptions = {
    zoom: 4,
    center: myLatlng
  }
  var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

<?php
foreach($data as $key => $value){
    foreach($value->result as $altObj){
//d($value);
?>
    var marker = new google.maps.Marker({
      position: new google.maps.LatLng(<?= $altObj->location->lat ?>,<?= $altObj->location->lng ?>),
      map: map,
      title: 'Altitude: <?= $altObj->elevation ?>'
    });
<?php
}}
?>
}
google.maps.event.addDomListener(window, 'load', initialize);

    </script>
  </head>
  <body>
    <div id="map-canvas"></div>
  </body>
</html>

<?php
}

// include __DIR__.'/util.sec/notification/run_notify.php';
// include __DIR__.'/util.sec/geokrety/processGeokretyErrors.php';
?>