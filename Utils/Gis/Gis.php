<?php
namespace Utils\Gis;

use lib\Objects\Coordinates\Coordinates;

class Gis
{
    const PI = M_PI;

    const DEGREE_LENGTH = 111.12;
    // length of one degree in KM (only for latitude!)
    const EQUATORIAL_RADIUS = 6378;
    // radius of the earth on the equator

    /**
     * Return the length [in degrees] of the given distance [in km] at meridian
     */
    public static function distanceInDegreesLat($distance)
    {
        return $distance / self::DEGREE_LENGTH;
    }

    /**
     * Return the length in degrees of the given distance at parallel [in km] at the given latitude
     * (Note that lenght of degree is different at different latitudes!)
     */
    public static function distanceInDegreesLon($distance, $lat)
    {
        return $distance * 180 / (abs(sin((90 - $lat) * M_PI / 180)) * 6378 * M_PI);
    }

    static function ptInLineRing($sGeometry, $sPoint)
    {
        // thanks to Roger Boily, Gis Consulant
        // posted at http://dev.mysql.com/doc/refman/5.1/en/functions-that-test-spatial-relationships-between-geometries.html
        $counter = 0;
        // get rid of unnecessary stuff
        $sGeometry = str_replace("LINESTRING", "", $sGeometry);
        $sGeometry = str_replace("(", "", $sGeometry);
        $sGeometry = str_replace(")", "", $sGeometry);
        $sPoint = str_replace("POINT", "", $sPoint);
        $sPoint = str_replace("(", "", $sPoint);
        $sPoint = str_replace(")", "", $sPoint);

        // make an array of points of the polygon
        $polygon = explode(",", $sGeometry);

        // get the x and y coordinate of the point
        $p = explode(" ", $sPoint);
        $px = $p[0];
        $py = $p[1];

        // number of points in the polygon
        $n = count($polygon);
        $poly1 = $polygon[0];
        for ($i = 1; $i <= $n; $i ++) {
            $poly1XY = explode(" ", $poly1);
            $poly1x = $poly1XY[0];
            $poly1y = $poly1XY[1];
            $poly2 = $polygon[$i % $n];
            $poly2XY = explode(" ", $poly2);
            $poly2x = $poly2XY[0];
            $poly2y = $poly2XY[1];

            if ($py > min($poly1y, $poly2y)) {
                if ($py <= max($poly1y, $poly2y)) {
                    if ($px <= max($poly1x, $poly2x)) {
                        if ($poly1y != $poly2y) {
                            $xinters = ($py - $poly1y) * ($poly2x - $poly1x) / ($poly2y - $poly1y) + $poly1x;
                            if ($poly1x == $poly2x || $px <= $xinters) {
                                $counter ++;
                            }
                        }
                    }
                }
            }
            $poly1 = $poly2;
        } // end of While each polygon

        if ($counter % 2 == 0) {
            return (false); // outside
        } else {
            return (true); // inside
        }

        return true;
    }

    /**
     * Returns the name of given bearing
     *
     * @param float $bearing
     * @param number $shortText
     * @return string - name of the bearing in long or short form
     */
    public static function bearing2Text($bearing, $shortText = FALSE)
    {
        if ($shortText) {
            $form = 'bearingshort';
        } else {
            $form = 'bearinglong';
        }

        if (! is_numeric($bearing)) {
            return 'N/A';
        }

        if ($bearing < 11.25 || $bearing > 348.75)
            return tr($form . '_n');
        if ($bearing < 33.75)
            return tr($form . '_nne');
        if ($bearing < 56.25)
            return tr($form . '_ne');
        if ($bearing < 78.75)
            return tr($form . '_ene');
        if ($bearing < 101.25)
            return tr($form . '_e');
        if ($bearing < 123.75)
            return tr($form . '_ese');
        if ($bearing < 146.25)
            return tr($form . '_se');
        if ($bearing < 168.75)
            return tr($form . '_sse');
        if ($bearing < 191.25)
            return tr($form . '_s');
        if ($bearing < 213.75)
            return tr($form . '_ssw');
        if ($bearing < 236.25)
            return tr($form . '_sw');
        if ($bearing < 258.75)
            return tr($form . '_wsw');
        if ($bearing < 281.25)
            return tr($form . '_w');
        if ($bearing < 303.75)
            return tr($form . '_wnw');
        if ($bearing < 326.25)
            return tr($form . '_nw');
        if ($bearing <= 348.75)
            return tr($form . '_nnw');
        else
            return 'N/A';
    }

    /**
     * Calculate bearing between two points
     *
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return string|number
     */
    public static function calcBearing($lat1, $lon1, $lat2, $lon2){

        if ($lat1 == $lat2 && $lon1 == $lon2) {
            return '-';
        }

        if ($lat1 == $lat2)
            $lat1 += 0.0000166;
        if ($lon1 == $lon2)
            $lon1 += 0.0000166;

        $rad_lat1 = $lat1 / 180.0 * M_PI;
        $rad_lon1 = $lon1 / 180.0 * M_PI;
        $rad_lat2 = $lat2 / 180.0 * M_PI;
        $rad_lon2 = $lon2 / 180.0 * M_PI;

        $delta_lon = $rad_lon2 - $rad_lon1;
        $bearing = atan2(
            sin($delta_lon) * cos($rad_lat2), cos($rad_lat1) * sin($rad_lat2) - sin($rad_lat1) * cos($rad_lat2) * cos($delta_lon));
        $bearing = 180.0 * $bearing / M_PI;

        // Calculated bearing is between <-180;180> degrees; normalize it ot the 0-360 deg form:
        if ($bearing < 0.0)
            $bearing = $bearing + 360.0;

        return $bearing;
    }

    public static function calcBearingBetween(Coordinates $c1, Coordinates $c2){
        return self::calcBearing($c1->getLatitude(), $c1->getLongitude(), $c2->getLatitude(), $c2->getLongitude());
    }

    public static function distance($latFrom, $lonFrom, $latTo, $lonTo, $distanceMultiplier = 1){
        $distance = acos(
            cos((90 - $latFrom) * M_PI / 180) * cos((90 - $latTo) * M_PI / 180) +
            sin((90 - $latFrom) * M_PI / 180) * sin((90 - $latTo) * M_PI / 180) *
            cos(($lonFrom - $lonTo) * M_PI / 180)) * self::EQUATORIAL_RADIUS * $distanceMultiplier;

        if ($distance < 0) $distance = 0;

        return $distance;
    }

    public static function distanceBetween(Coordinates $c1, Coordinates $c2){
        return self::distance($c1->getLatitude(), $c1->getLongitude(), $c2->getLatitude(), $c2->getLongitude());
    }
}

