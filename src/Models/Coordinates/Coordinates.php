<?php

namespace src\Models\Coordinates;

/**
 * Coordinates keep and converts geographical coordinates
 *
 * @author Andrzej Lza Wozniak
 *
 */
class Coordinates
{

    /**
     * coordinates in casual decimal format (DD.DDDDDDDD)
     *
     * @var float
     */
    private $latitude = null;
    private $longitude = null;

    /**
     * Default format from system setting
     */
    const COORDINATES_FORMAT_DEFAULT = self::COORDINATES_FORMAT_DEG_MIN;

    /**
     * decimal degrees: 40.446321° N 79.982321° W
     */
    const COORDINATES_FORMAT_DECIMAL = 1;

    /**
     * degrees decimal minutes: 40° 26.767′ N 79° 58.933′ W
     */
    const COORDINATES_FORMAT_DEG_MIN = 2;

    /**
     * degrees minutes seconds: 40° 26′ 46″ N 79° 58′ 56″ W
     */
    const COORDINATES_FORMAT_DEG_MIN_SEC = 3;

    /**
     *
     * @param array $params
     *            - must contain latitude (float) and longitude (float)
     *
     *            example of use:
     *            $params = array (
     *            latitude => 40.446321
     *            longitude => 79.982321
     *            )
     *            $coordinates = new Coordinates($params);
     *
     */
    public function __construct(array $params = [])
    {
        if (isset($params['dbRow'])) {
            $this->loadFromDb($params['dbRow']);
        } elseif (isset($params['okapiRow'])) {
            $this->loadFromOkapi($params['okapiRow']);
        }
    }

    /**
     * @param $lat
     * @param $lon
     * @return Coordinates|null
     */
    public static function FromCoordsFactory($lat, $lon)
    {
        $coords = new Coordinates();
        $coords->setLatitude($lat);
        $coords->setLongitude($lon);
        if (!$coords->areCordsReasonable()) {
            return null;
        }

        return $coords;
    }


    /**
     * Load this class data based on data from DB
     *
     * @param array $dbRow
     */
    public function loadFromDb($dbRow)
    {
        if (isset($dbRow['latitude'], $dbRow['longitude'])) {
            $this->latitude = (float)$dbRow['latitude'];
            $this->longitude = (float)$dbRow['longitude'];
        } else {
            // at least one cord is NULL => improper cords
            $this->latitude = null;
            $this->longitude = null;
        }
    }

    /**
     * Load this class data based on data from OKAPI
     *
     * @param string $okapiLocation
     */
    public function loadFromOkapi($okapiLocation)
    {
        list ($lat, $lon) = explode("|", $okapiLocation);
        $this->latitude = (float)$lat;
        $this->longitude = (float)$lon;
    }

    /**
     * returns latitude as string.
     * Result is in format selected by param $format. If param $format is not given, result is in default format.
     *
     * @param integer $format
     *            (optional) must be one of this class constants: COORDINATES_FORMAT_DECIMAL or COORDINATES_FORMAT_DEG_MIN or COORDINATES_FORMAT_DEG_MIN_SEC
     * @return string example of use:
     *         $latitude = $coordinates->getLatitudeString(Coordinates::COORDINATES_FORMAT_DEG_MIN);
     *
     *         example of use:
     *         $latitude = $coordinates->getLatitudeString();
     *
     */
    public function getLatitudeString($format = self::COORDINATES_FORMAT_DEFAULT): string
    {
        if (is_null($this->latitude)) {
            return '';
        }

        $prefix = $this->getLatitudeHemisphereSymbol() . '&nbsp;';
        switch ($format) {
            case self::COORDINATES_FORMAT_DECIMAL:
                return $prefix . $this->convertToDecString(abs($this->latitude));
            case self::COORDINATES_FORMAT_DEG_MIN:
                return $prefix . $this->convertToDegMin(abs($this->latitude));
            case self::COORDINATES_FORMAT_DEG_MIN_SEC:
                return $prefix . $this->convertToDegMinSec(abs($this->latitude));
        }
        return '';
    }

    /**
     * returns latitude as string.
     * Result is in format selected by param $format. If param $format is not given, result is in default format.
     *
     * @param integer $format
     *            (optional) must be one of this class constants:
     *            COORDINATES_FORMAT_DECIMAL or COORDINATES_FORMAT_DEG_MIN or COORDINATES_FORMAT_DEG_MIN_SEC
     *
     * @return string
     *
     * example of use:
     *         $latitude = $coordinates->getLatitudeString(Coordinates::COORDINATES_FORMAT_DEG_MIN);
     *
     *         example of use:
     *         $latitude = $coordinates->getLatitudeString();
     *
     */
    public function getLongitudeString($format = self::COORDINATES_FORMAT_DEFAULT): string
    {
        if (is_null($this->longitude)) {
            return '';
        }

        $prefix = $this->getLongitudeHemisphereSymbol() . '&nbsp;';
        switch ($format) {
            case self::COORDINATES_FORMAT_DECIMAL:
                return $prefix . $this->convertToDecString(abs($this->longitude));
            case self::COORDINATES_FORMAT_DEG_MIN:
                return $prefix . $this->convertToDegMin(abs($this->longitude));
            case self::COORDINATES_FORMAT_DEG_MIN_SEC:
                return $prefix . $this->convertToDegMinSec(abs($this->longitude));
        }
        return '';
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * returns parts of the coordinate according to given format
     * @param int $format
     * @return array()
     */
    public function getLongitudeParts($format = self::COORDINATES_FORMAT_DEFAULT): array
    {
        $prefix = $this->getLongitudeHemisphereSymbol();

        list($deg, $min, $sec) = $this->getParts($this->longitude);

        return $this->getFormattedPartsArray($format, $prefix, $deg, $min, $sec);
    }

    /**
     * returns parts of the coordinate according to given format
     * @param int $format
     * @return array()
     */
    public function getLatitudeParts($format = self::COORDINATES_FORMAT_DEFAULT): array
    {
        $prefix = $this->getLatitudeHemisphereSymbol();

        list($deg, $min, $sec) = $this->getParts($this->latitude);

        return $this->getFormattedPartsArray($format, $prefix, $deg, $min, $sec);

    }

    private function getFormattedPartsArray($format, $prefix, $deg, $min, $sec): array
    {
        switch ($format) {
            case self::COORDINATES_FORMAT_DECIMAL:
                return array($prefix, sprintf("%02d", $deg));

            case self::COORDINATES_FORMAT_DEG_MIN:
                return array($prefix, sprintf("%02d", floor($deg)), sprintf("%06.3f", $min));

            case self::COORDINATES_FORMAT_DEG_MIN_SEC:
                return array($prefix, sprintf("%02d", floor($deg)), sprintf("%02d", floor($min)), sprintf("%03d", $sec));
        }
        return [];
    }


    /**
     * return true if cords in object are set to reasonable values
     */
    public function areCordsReasonable(): bool
    {
        return ($this->latitude >= -90 && $this->latitude <= 90 &&
            $this->longitude >= -180 && $this->longitude <= 180);
    }

    /**
     *
     * @param float $latitude
     * @return Coordinates
     */
    public function setLatitude($latitude): Coordinates
    {
        $this->latitude = $latitude;
        return $this;
    }

    /**
     *
     * @param int $longitude
     * @return Coordinates
     */
    public function setLongitude($longitude): Coordinates
    {
        $this->longitude = $longitude;
        return $this;
    }

    /**
     * Returns coordinates string to display
     * @param int $format
     * @return string
     */
    public function getAsText(int $format = self::COORDINATES_FORMAT_DEFAULT): string
    {
        return $this->getLatitudeString($format) . ' ' . $this->getLongitudeString($format);
    }

    public function getAsOpenLayersFormat(): string
    {
        return '{ lat:' . $this->getLatitude() . ', lon:' . $this->getLongitude() . ' }';
    }

    /**
     * Return TRUE if given coords are the same
     *
     * @param Coordinates $coords
     * @return bool
     */
    public function areSameAs(Coordinates $coords): bool
    {

        $latA = $this->getLatitudeParts();
        $latB = $coords->getLatitudeParts();
        foreach ($latA as $key => $part) {
            if ($latA[$key] != $latB[$key]) {
                return false;
            }
        }

        $lotA = $this->getLongitudeParts();
        $lotB = $coords->getLongitudeParts();
        foreach ($lotA as $key => $part) {
            if ($lotA[$key] != $lotB[$key]) {
                return false;
            }
        }

        return true;
    }

    private function getParts($coordinate): array
    {
        $deg = abs($coordinate);
        $min = 60 * ($deg - floor($deg));
        $sec = 3600 * ($deg - floor($deg)) - 60 * floor($min);
        return array($deg, $min, $sec);
    }

    private function convertToDegMin($decimalCoordinate): string
    {
        $degMinCoordinate = sprintf("%02d", floor($decimalCoordinate)) . '°&nbsp;';
        $coordinate = $decimalCoordinate - floor($decimalCoordinate);
        $degMinCoordinate .= sprintf("%06.3f", round($coordinate * 60, 3)) . '\'';
        return $degMinCoordinate;
    }

    private function convertToDegMinSec($decimalCoordinate): string
    {
        $degMinSecCoordinate = sprintf("%02d", floor($decimalCoordinate)) . '°&nbsp;';
        $coordinate = $decimalCoordinate - floor($decimalCoordinate);
        $coordinate *= 60;
        $degMinSecCoordinate .= sprintf("%02d", floor($coordinate)) . '\'&nbsp;';
        $latMin = $coordinate - floor($coordinate);
        $degMinSecCoordinate .= sprintf("%02.02f", $latMin * 60) . '\'\'';
        return $degMinSecCoordinate;
    }

    private function convertToDecString($coordinate, $afterComaPlacesCount = 5): string
    {
        return sprintf('%.' . $afterComaPlacesCount . 'f', $coordinate) . '°&nbsp;';
    }

    private function getLatitudeHemisphereSymbol(): string
    {
        if ($this->latitude < 0) {
            return 'S';
        } else {
            return 'N';
        }
    }

    private function getLongitudeHemisphereSymbol(): string
    {
        if ($this->longitude < 0) {
            return 'W';
        } else {
            return 'E';
        }
    }

    /**
     * @param float $lon
     * @return string
     * @deprecated
     *
     * This is old-school method moved from common.inc.php
     * It will be removed - DO NOT USE IT!
     *
     * decimal longitude to string E/W hhh°mm.mmm
     *
     */
    public static function donNotUse_lonToDegreeStr($lon)
    {
        if ($lon < 0) {
            $retVal = 'W ';
            $lon = -$lon;
        } else {
            $retVal = 'E ';
        }

        $retVal = $retVal . sprintf("%02d", floor($lon)) . '° ';
        $lon = $lon - floor($lon);
        $retVal = $retVal . sprintf("%06.3f", round($lon * 60, 3)) . '\'';

        return $retVal;
    }

    /**
     * @param float $lat
     * @return string
     * @deprecated
     *
     * This is old-school method moved from common.inc.php
     * It will be removed - DO NOT USE IT!
     *
     * decimal latitude to string N/S hh°mm.mmm
     *
     */
    public static function donNotUse_latToDegreeStr($lat)
    {
        if ($lat < 0) {
            $retVal = 'S ';
            $lat = -$lat;
        } else {
            $retVal = 'N ';
        }
        $retVal = $retVal . sprintf("%02d", floor($lat)) . '° ';
        $lat = $lat - floor($lat);
        $retVal = $retVal . sprintf("%06.3f", round($lat * 60, 3)) . '\'';
        return $retVal;
    }

}
