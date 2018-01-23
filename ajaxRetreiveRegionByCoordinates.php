<?php
/**
 * This callback is used by newcache to select region based on coords
 */

use lib\Objects\Coordinates\Coordinates;
use lib\Objects\Coordinates\NutsLocation;

require_once __DIR__ . '/lib/ClassPathDictionary.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    print 'please login!';
    exit;
}

if( !isset($_REQUEST['lat'], $_REQUEST['lon']) ){
    print 'no coords?!';
    exit;
}

$coords = Coordinates::FromCoordsFactory($_REQUEST['lat'], $_REQUEST['lon']);
$nutsLocation = NutsLocation::fromCoordsFactory($coords);

// this is old format still used by newcache page
$wynik['adm1'] = $nutsLocation->getName(NutsLocation::LEVEL_COUNTRY);
$wynik['adm2'] = $nutsLocation->getName(NutsLocation::LEVEL_1);
$wynik['adm3'] = $nutsLocation->getName(NutsLocation::LEVEL_2);
$wynik['adm4'] = $nutsLocation->getName(NutsLocation::LEVEL_3);

$wynik['code1'] = $nutsLocation->getCode(NutsLocation::LEVEL_COUNTRY);
$wynik['code2'] = $nutsLocation->getCode(NutsLocation::LEVEL_1);
$wynik['code3'] = $nutsLocation->getCode(NutsLocation::LEVEL_2);
$wynik['code4'] = $nutsLocation->getCode(NutsLocation::LEVEL_3);

echo json_encode($wynik);
