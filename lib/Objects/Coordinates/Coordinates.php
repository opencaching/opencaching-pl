<?php

namespace lib\Objects\Coordinates;

/**
 * Coordinates keep and converts geographical coordinates
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
    private $latitude = 0;
    private $longitude = 0;

    /**
     * Default format from system setting
     * 
     * @var string
     */
    private $defaultFormat = self::COORDINATES_FORMAT_DECIMAL;

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
     * @param array $params - must contain latitude (float) and longitude (float)
     *            
     * example of use:
     * $params = array (
     *   latitude => 40.446321
     *   longitude => 79.982321
     * )
     * $coordinates = new \lib\Objects\GeoCache\Coordinates($params);
     *            
     */
    public function __construct(array $params)
    {
        if (isset($params['dbRow'])) {
            $this->loadFromDb($params['dbRow']);
        } else 
            if ($params['okapiRow']) {
                $this->loadFromOkapi($params['okapiRow']);
            }
    }

    /**
     * Load this class data based on data from DB
     * 
     * @param array $dbRow            
     */
    public function loadFromDb($dbRow)
    {
        if (isset($dbRow['latitude'])) {
            $this->latitude = (float) $dbRow['latitude'];
        }
        if (isset($dbRow['longitude'])) {
            $this->longitude = (float) $dbRow['longitude'];
        }
    }

    /**
     * Load this class data based on data from OKAPI
     * 
     * @param array $okapiLocation            
     */
    public function loadFromOkapi($okapiLocation)
    {
        list ($lat, $lon) = explode("|", $okapiLocation);
        $this->latitude = (float) $lat;
        $this->longitude = (float) $lon;
    }

    /**
     * returns latitude as string.
     * Result is in format selecdted by param $format. If param $format is not given, result is in default format.
     *
     * @param integer $format
     *            (optional) must be one of this class constants: COORDINATES_FORMAT_DECIMAL or COORDINATES_FORMAT_DEG_MIN or COORDINATES_FORMAT_DEG_MIN_SEC
     * @return string example of use:
     *         $latitude = $coordinates->getLatitudeString(\lib\Objects\GeoCache\Coordinates::COORDINATES_FORMAT_DEG_MIN);
     *        
     *         example of use:
     *         $latitude = $coordinates->getLatitudeString();
     *        
     */
    public function getLatitudeString($format = false)
    {
        if ($format === false) { /* pick defaut format */
            $format = $this->defaultFormat;
        }
        $prefix = $this->getLatitudeHemisphereSymbol() . ' ';
        switch ($format) {
            case self::COORDINATES_FORMAT_DECIMAL:
                return $prefix . $this->convertToDecString(abs($this->latitude));
            case self::COORDINATES_FORMAT_DEG_MIN:
                return $prefix . $this->convertToDegMin(abs($this->latitude));
            case self::COORDINATES_FORMAT_DEG_MIN_SEC:
                return $prefix . $this->convertToDegMinSec(abs($this->latitude));
        }
    }

    /**
     * returns latitude as string.
     * Result is in format selecdted by param $format. If param $format is not given, result is in default format.
     *
     * @param integer $format (optional) must be one of this class constants: 
     *     COORDINATES_FORMAT_DECIMAL or COORDINATES_FORMAT_DEG_MIN or COORDINATES_FORMAT_DEG_MIN_SEC
     * 
     * @return string 
     * 
     * example of use:
     *   $latitude = $coordinates->getLatitudeString(\lib\Objects\GeoCache\Coordinates::COORDINATES_FORMAT_DEG_MIN);
     *      
     * example of use:
     *   $latitude = $coordinates->getLatitudeString();
     *        
     */
    public function getLongitudeString($format = false)
    {
        if ($format === false) { /* pick defaut format */
            $format = $this->defaultFormat;
        }
        $prefix = $this->getLongitudeHemisphereSymbol() . ' ';
        switch ($format) {
            case self::COORDINATES_FORMAT_DECIMAL:
                return $prefix . $this->convertToDecString(abs($this->longitude));
            case self::COORDINATES_FORMAT_DEG_MIN:
                return $prefix . $this->convertToDegMin(abs($this->longitude));
            case self::COORDINATES_FORMAT_DEG_MIN_SEC:
                return $prefix . $this->convertToDegMinSec(abs($this->longitude));
        }
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    private function convertToDegMin($decimalCoordinate)
    {
        $degMinCoordinate = sprintf("%02d", floor($decimalCoordinate)) . '° ';
        $coordinate = $decimalCoordinate - floor($decimalCoordinate);
        $degMinCoordinate .= sprintf("%06.3f", round($coordinate * 60, 3)) . '\'';
        return $degMinCoordinate;
    }

    private function convertToDegMinSec($decimalCoordinate)
    {
        $degMinSecCoordinate = sprintf("%02d", floor($decimalCoordinate)) . '° ';
        $coordinate = $decimalCoordinate - floor($decimalCoordinate);
        $coordinate *= 60;
        $degMinSecCoordinate .= sprintf("%02d", floor($coordinate)) . '\' ';
        $latmin = $coordinate - floor($coordinate);
        $degMinSecCoordinate .= sprintf("%02.02f", $latmin * 60) . '\'\'';
        return $degMinSecCoordinate;
    }

    private function convertToDecString($coordinate, $afterComaPlacesCount = 5)
    {
        return sprintf('%.' . $afterComaPlacesCount . 'f', $coordinate) . '° ';
    }

    private function getLatitudeHemisphereSymbol()
    {
        if ($this->latitude < 0) {
            return 'S';
        } else {
            return 'N';
        }
    }

    private function getLongitudeHemisphereSymbol()
    {
        if ($this->longitude < 0) {
            return 'W';
        } else {
            return 'E';
        }
    }
}

