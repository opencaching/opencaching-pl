<?php

namespace lib\Objects\GeoCache;

use Utils\Database\OcDb;
/**
 * Description of Altitude
 *
 * @author Łza
 */
class Altitude
{

    /**
     * altitude in metres obove sea level
     * @var integer
     */
    private $altitude = null;

    /* @var $geoCache \lib\Objects\GeoCache\GeoCache */
    private $geoCache;

    public function __construct(\lib\Objects\GeoCache\GeoCache $geoCache)
    {
        $this->geoCache = $geoCache;
        $this->loadAltitudeFromDb();
    }

    private function loadAltitudeFromDb()
    {
        $db = OcDb::instance();

        $s = $db->multiVariableQuery(
            'SELECT `altitude` FROM `caches_additions` WHERE `cache_id` = :1 LIMIT 1',
            $this->geoCache->getCacheId());

        $dbResult = $db->dbResultFetchOneRowOnly($s);

        $this->altitude = $dbResult['altitude'];
    }

    /**
     * Retreive altitude from google Api, set $this->altitude and store it in db.
     */
    private function retreiveAltitudeFromGoogleApi()
    {
        $googleElevationApiUrl = 'http://maps.googleapis.com/maps/api/elevation/xml?locations=';
        $latitude = number_format($this->geoCache->getCoordinates()->getLatitude(), 7, '.', '');
        $longitude = number_format($this->geoCache->getCoordinates()->getLongitude(), 7, '.', '');
        $url = $googleElevationApiUrl . $latitude . ',' . $longitude;
        $altitude = simplexml_load_file($url);
        if ($altitude) {
            $this->storeAlitudeInDb($altitude);
            $status = (string) $altitude->status;
            if ($status !== 'OK') { /* error occured */
                return;
            }
            $altitudeFloat = (float) $altitude->result->elevation;
            $this->altitude = (int) round($altitudeFloat);
        }
    }

    private function storeAlitudeInDb()
    {
        $query = 'INSERT INTO `caches_additions` (`cache_id`, `altitude`, `altitude_update_datetime`)
                        VALUES (:2, :1, NOW())
                        ON DUPLICATE KEY UPDATE
                        `altitude` = :1, altitude_update_datetime = NOW()';
        $db = \lib\Database\DataBaseSingleton::Instance();
        $db->multiVariableQuery($query, $this->altitude, $this->geoCache->getCacheId());
    }

    /**
     * retreive geocache alitude from google API, then
     * compare witch user input altitude.
     *
     * If user input altitude to google api altitude difference is less than 50 meters, tread user
     * input altitude as corect one and use it, otherwise, use google api altitude
     *
     * finally store it in db
     *
     * @param float $userInputAltitude
     */
    public function pickAndStoreAltitude($userInputAltitude)
    {
        $this->retreiveAltitudeFromGoogleApi();
        if ( !is_null($userInputAltitude) &&
             $userInputAltitude < $this->altitude+50 &&
             $userInputAltitude > $this->altitude-50
           ) {

            $this->altitude = $userInputAltitude;
        }
        $this->storeAlitudeInDb();
    }

    public function getAltitude()
    {
        return $this->altitude;
    }

}
