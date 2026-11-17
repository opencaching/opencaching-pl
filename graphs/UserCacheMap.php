<?php

use src\Models\OcConfig\OcConfig;
use src\Utils\Database\XDb;

require_once (__DIR__ . '/../lib/common.inc.php');

if (OcConfig::getOcNode() !== 'pl') {
    http_response_code(404);
    exit;
}

$userId = filter_input(INPUT_GET, 'userid', FILTER_VALIDATE_INT);
$mapType = isset($_GET['type']) ? $_GET['type'] : '';
if ($userId === false || $userId === null || $userId < 1 || !in_array($mapType, array('created', 'found'), true)) {
    http_response_code(400);
    exit;
}

$image = ImageCreateFromGIF(__DIR__ . '/../images/PLmapa.gif');
if ($image === false) {
    http_response_code(500);
    exit;
}

$voivodeshipPositions = array(
    'PL11' => array(110, 138), // Lodzkie
    'PL12' => array(155, 108), // Mazowieckie
    'PL21' => array(135, 208), // Malopolskie
    'PL22' => array(103, 188), // Slaskie
    'PL31' => array(200, 150), // Lubelskie
    'PL32' => array(180, 200), // Podkarpackie
    'PL33' => array(146, 170), // Swietokrzyskie
    'PL34' => array(195, 75), // Podlaskie
    'PL41' => array(65, 115), // Wielkopolskie
    'PL42' => array(26, 55), // Zachodniopomorskie
    'PL43' => array(19, 100), // Lubuskie
    'PL51' => array(35, 149), // Dolnoslaskie
    'PL52' => array(78, 169), // Opolskie
    'PL61' => array(90, 85), // Kujawskie
    'PL62' => array(145, 50), // Warminskie
    'PL63' => array(85, 43) // Pomorskie
);

if ($mapType === 'created') {
    $query = "SELECT cache_location.code3 region_code, COUNT(*) cache_count
        FROM caches, cache_location
        WHERE caches.user_id = ?
            AND caches.status <> 4 AND caches.status <> 5
            AND cache_location.code3 IN ('PL11','PL12','PL21','PL22','PL31','PL32','PL33','PL34','PL41','PL42','PL43','PL51','PL52','PL61','PL62','PL63')
            AND caches.cache_id = cache_location.cache_id
        GROUP BY cache_location.code3";
} else {
    $query = "SELECT cache_location.code3 region_code, COUNT(*) cache_count
        FROM cache_logs, cache_location
        WHERE cache_logs.user_id = ? AND cache_logs.type = '1'
            AND cache_logs.deleted = '0'
            AND cache_location.code3 IN ('PL11','PL12','PL21','PL22','PL31','PL32','PL33','PL34','PL41','PL42','PL43','PL51','PL52','PL61','PL62','PL63')
            AND cache_logs.cache_id = cache_location.cache_id
        GROUP BY cache_location.code3";
}

$resultSet = XDb::xSql($query, $userId);

$colorWhite = ImageColorAllocate($image, 255, 255, 255);
$colorBorder = ImageColorAllocate($image, 70, 70, 70);
$colorBlack = ImageColorAllocate($image, 0, 0, 0);
$colorRed = ImageColorAllocate($image, 255, 0, 0);
$colorBlue = ImageColorAllocate($image, 0, 0, 255);

$fontFile = __DIR__ . '/../resources/fonts/arial.ttf';

while ($result = XDb::xFetchArray($resultSet)) {
    $textPosition = $voivodeshipPositions[$result['region_code']];
    ImageTTFText($image, 14, 0, $textPosition[0], $textPosition[1], $colorBlack, $fontFile, $result['cache_count']);
}

$imageName = 'mapstat' . ($mapType === 'created' ? '-created' : '') . $userId . '.jpg';
$imagePath = OcConfig::getDynFilesPath() . 'images/statpics/' . $imageName;

if (!Imagejpeg($image, $imagePath, 80)) {
    ImageDestroy($image);
    http_response_code(500);
    exit;
}
ImageDestroy($image);

header('Cache-Control: no-cache, no-store, must-revalidate');
header('Location: /images/statpics/' . $imageName . '?rand=' . rand(), true, 302);
exit;
