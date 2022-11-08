<?php

/** @noinspection PhpUnusedPrivateMethodInspection */

/**
 * This class allow to retrieve altitude for selected point
 */

namespace src\Models\Coordinates;

use src\Models\OcConfig\OcConfig;
use src\Utils\Debug\Debug;

class Altitude
{
    /**
     * Return altitude of the point under coordinates
     */
    public static function getAltitude(Coordinates $coords): ?int
    {
        return self::getAltitudeFromOpenTopoData($coords);
    }

    /**
     * Google API is now PAID and OC code can't use it
     *
     * Retrieve altitude from Google elevation service
     */
    private static function getAltitudeFromGoogle(Coordinates $coords): ?float
    {
        $googleMapKey = OcConfig::instance()->getGoogleMapKey();
        $url = 'https://maps.googleapis.com/maps/api/elevation/json?'
            . "locations={$coords->getLatitude()},{$coords->getLongitude()}"
            . "&key={$googleMapKey}";

        $resp = @file_get_contents($url);
        $data = json_decode($resp);

        if (is_object($data) && isset($data->status)) {
            if ($data->status == 'OK' && is_array($data->results) && ! empty($data->results)) {
                if (isset($data->results[0]->elevation)) {
                    return $data->results[0]->elevation;
                }
            }
        }

        return null;
    }

    /**
     * Retrieve altitude data from open-elevation.com service
     * This service is free to use.
     */
    private static function getAltitudeFromOpenElevation(Coordinates $coords): ?int
    {
        $url = 'https://api.open-elevation.com/api/v1/lookup?'
            . "locations={$coords->getLatitude()},{$coords->getLongitude()}";

        $resp = @file_get_contents($url);
        $data = json_decode($resp);

        if (isset($data->results) && is_array($data->results) && ! empty($data->results)) {
            if (is_object($data->results[0])) {
                if (isset($data->results[0]->elevation)) {
                    return $data->results[0]->elevation;
                }
            }
        }

        return null;
    }

    // Data Science Toolkit doesn't seem not work
    private static function getAltitudeFromDataScienceToolkit(Coordinates $coords): ?int
    {
        $url = 'http://dstk.britecorepro.com/coordinates2statistics/'
            . "{$coords->getLatitude()},{$coords->getLongitude()}?statistics=elevation";

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
        if (! empty($data) && isset($data[0]->statistics, $data[0]->statistics->elevation)) {
            $stats = $data[0]->statistics->elevation;

            if ($stats->units == 'meters') {
                if (! is_numeric($stats->value)) {
                    Debug::errorLog(
                        'External service: datasciencetoolkit returns unexpected'
                        . " non numeric value for coords: {$coords->getAsText()}()?!"
                    );
                }

                return (int) $stats->value;
            }
        } elseif (! empty($data) && isset($data[0]->statistics)) {
            // If coords are on ocean, DataScienceToolkit response with empty statistics section.
            return 0;
        }

        return null;
    }

    private static function getAltitudeFromOpenTopoData(Coordinates $coords): ?int
    {
        $url = 'https://api.opentopodata.org/v1/test-dataset?locations='
            . $coords->getLatitude() . ',' . $coords->getLongitude();

        $resp = @file_get_contents($url);
        $data = json_decode($resp, true);
        /*
            {
                "results": [{
                    "elevation": 815.0,
                    "location": {
                        "lat": 56.0,
                        "lng": 123.0
                    },
                    "dataset": "test-dataset"
                }],
                "status": "OK"
            }
        */
        if (
            ! empty($data)
            && isset($data['results'], $data['results'][0], $data['results'][0]['elevation'], $data['status'])
            && $data['status'] == 'OK'
        ) {
            $elevation = $data['results'][0]['elevation'];

            if (! is_numeric($elevation)) {
                Debug::errorLog(
                    'External service: opentopodata returns unexpected'
                    . " non numeric value for coords: {$coords->getAsText()}()"
                );
            }

            return (int) $elevation;
        }

        return null;
    }
}
