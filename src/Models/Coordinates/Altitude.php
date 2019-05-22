<?php
/**
 * This class allow to retrive altitude for selected point
 */
namespace src\Models\Coordinates;


use src\Utils\Debug\Debug;

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
        return self::getAltitudeFromDataScienceToolkit($coords);
    }

    /**
     * Google API is now PAID and OC code can't use it
     *
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
     * OPEN-ELEVATION doesn't work now (and it seems it will not work)!
     *
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

        if(isset($data->results) && is_array($data->results) && !empty($data->results) ){
            if(is_object($data->results[0])){
                if(isset($data->results[0]->elevation)){
                    return $data->results[0]->elevation;
                }
            }
        }

        return null;
    }

    private static function getAltitudeFromDataScienceToolkit(Coordinates $coords)
    {
        $url = "http://www.datasciencetoolkit.org/coordinates2statistics/" .
                        "{$coords->getLatitude()},{$coords->getLongitude()}?statistics=elevation";

        $resp = @file_get_contents($url);
        $data = json_decode($resp);
        /*
         [
          {
            "location": {
              "latitude": 37.769456,
              "longitude": -122.429128
            },
            "statistics": {
              "elevation": {
                "units": "meters",
                "value": 40,
                "source_name": "NASA and the CGIAR Consortium for Spatial Information",
                "description": "The height of the surface above sea level at this point."
              }
            }
          }
        ]
         */
        if(!empty($data) && isset($data[0]->statistics) && isset($data[0]->statistics->elevation)){
            $stats = $data[0]->statistics->elevation;
            if($stats->units == 'meters'){
                if (!is_numeric($stats->value)) {
                    Debug::errorLog("External service: datasciencetoolkit returns unexpected" .
                                    " non numeric value for coords: $coords->getAsText()?!");
                }
                return $stats->value;
            }
        }

        return null;
    }
}
