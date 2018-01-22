<?php
/**
 * This class allow to retrive altitude for selected point
 */
namespace lib\Objects\Coordinates;


class Altitude
{
    /**
     * Return altitude of the point under coordinates
     *
     * @param Coordinates $coords
     * @return int|NULL
     */
    public static function getAltitude(Coordinates $coords)
    {
        // use OpenElevation service by default
        return round(self::getAltitudeFromOpenElevation($coords));
        //return round(self::getAltitudeFromGoogle($coords));
    }

    /**
     * Retrivet altitude from Google elevaton service
     *
     * @param Coordinates $coords
     * @return float|NULL
     */
    private static function getAltitudeFromGoogle(Coordinates $coords)
    {
        global $googlemap_key;

        $url = "https://maps.googleapis.com/maps/api/elevation/json?".
                "locations={$coords->getLatitude()},{$coords->getLongitude()}".
                "&key=$googlemap_key";

        $resp = @file_get_contents($url);
        $data = json_decode($resp);

        if(is_object($data) && isset($data->status)){
            if($data->status=='OK' && is_array($data->results) && !empty($data->results)){
                if(isset($data->results[0]->elevation)){
                    return $data->results[0]->elevation;
                }
            }
        }

        return null;
    }

    /**
     * Retrive altitude data from open-elevation.com service
     * This service is free to use.
     *
     * @param Coordinates $coords
     * @return int|NULL
     */
    private static function getAltitudeFromOpenElevation(Coordinates $coords)
    {
        $url = "https://api.open-elevation.com/api/v1/lookup?".
               "locations={$coords->getLatitude()},{$coords->getLongitude()}";

        $resp = @file_get_contents($url);
        $data = json_decode($resp);

        d($data);

        if(isset($data->results) && is_array($data->results) && !empty($data->results) ){
            if(is_object($data->results[0])){
                if(isset($data->results[0]->elevation)){
                    return $data->results[0]->elevation;
                }
            }
        }

        return null;
    }
}
