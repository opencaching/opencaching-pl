<?php
namespace lib\Objects\Coordinates;

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
     *
     * @var string
     */
    private $defaultFormat = self::COORDINATES_FORMAT_DEG_MIN;

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
    public function __construct(array $params = null)
    {
        if (isset($params['dbRow'])) {
            $this->loadFromDb($params['dbRow']);
        } elseif ($params['okapiRow']) {
            $this->loadFromOkapi($params['okapiRow']);
        }
    }

    public static function FromCoordsFactory($lat, $lon)
    {
        $coords = new Coordinates();
        $coords->setLatitude($lat);
        $coords->setLongitude($lon);
        if(!$coords->areCordsReasonable()){
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
            $this->latitude = (float) $dbRow['latitude'];
            $this->longitude = (float) $dbRow['longitude'];
        } else {
            // at least one cord is NULL => improper cords
            $this->latitude = null;
            $this->longitude = null;
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
     *         $latitude = $coordinates->getLatitudeString(Coordinates::COORDINATES_FORMAT_DEG_MIN);
     *
     *         example of use:
     *         $latitude = $coordinates->getLatitudeString();
     *
     */
    public function getLatitudeString($format = false)
    {
        if(is_null($this->latitude)) {
            return null;
        }

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
    public function getLongitudeString($format = false)
    {
        if( is_null($this->longitude) ){
            return null;
        }

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

    /**
     * returns parts of the coordinate according to given format
     * @return array()
     */
    public function getLongitudeParts($format = false)
    {
        if ($format === false) { /* pick defaut format */
            $format = $this->defaultFormat;
        }

        $prefix = $this->getLongitudeHemisphereSymbol();

        list($deg, $min, $sec) = $this->getParts($this->longitude);

        return $this->getFormattedPartsArray($format, $prefix, $deg, $min, $sec);
    }


    /**
     * returns parts of the coordinate according to given format
     * @return array()
     */
    public function getLatitudeParts($format = false)
    {
        if ($format === false) { /* pick defaut format */
            $format = $this->defaultFormat;
        }

        $prefix = $this->getLatitudeHemisphereSymbol();

        list($deg, $min, $sec) = $this->getParts($this->latitude);

        return $this->getFormattedPartsArray($format, $prefix, $deg, $min, $sec);

    }

    private function getFormattedPartsArray($format, $prefix, $deg, $min, $sec)
    {
        switch ($format) {
            case self::COORDINATES_FORMAT_DECIMAL:
                return array($prefix, sprintf("%02d",$deg));

            case self::COORDINATES_FORMAT_DEG_MIN:
                return array($prefix, sprintf("%02d",floor($deg)), sprintf("%06.3f",$min));

            case self::COORDINATES_FORMAT_DEG_MIN_SEC:
                return array($prefix, sprintf("%02d",floor($deg)), sprintf("%02d",floor($min)), sprintf("%03d",$sec));
        }
    }


    /**
     * return true if cords in object are set to reasonable values
     */
    public function areCordsReasonable()
    {
        return (!empty($this->latitude) && !empty($this->latitude) &&
            $this->latitude >= -90 && $this->latitude <= 90 &&
            $this->longitude >= -180 && $this->longitude <= 180);
    }

    /**
     *
     * @param float $latitude
     * @return Coordinates
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
        return $this;
    }

    /**
     *
     * @param int $longitude
     * @return Coordinates
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
        return $this;
    }

    /**
     * Returns coordinates string to display
     */
    public function getAsText($format = false){
        return "&#10;". $this->getLatitudeString($format)."&#10;".$this->getLongitudeString($format);
    }

    /**
     * Return TRUE if given coords are the same
     *
     * @param Coordinates $coords
     * @return boolean
     */
    public function areSameAs(Coordinates $coords){
        return $this->latitude == $coords->getLatitude() &&
               $this->longitude == $coords->getLongitude();
    }

    private function getParts($coordinate)
    {
        $deg = abs($coordinate);
        $min = 60 * ($deg - floor($deg));
        $sec = 3600 * ($deg - floor($deg)) - 60 * floor($min);
        return array($deg, $min, $sec);
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

/*
 THIS IS OLD VERSION OF FUNCTION WHICH CONVERT LAT-LON COORDS TO UTM
 DO WE NEED UTM AT ALL?
 IF YES LET'S USE THIS:
 https://www.phpclasses.org/browse/file/10671.html

function ll2utm($Lat, $Lon, $NS = null, $EW = null)
{
    if ($Lat > 90)
        die("Invalid Latitude");
    if ($Lon > 180)
        die("Invalid Lonitude");
//$NS=(strtoupper(($NS)=='S') && ($Lat>0));
//$EW=(strtoupper($EW)=='W');
    if ($Lat < 0) {
        $Lat = -$Lat;
        $NS = 'S';
    } else {
        $NS = 'N';
    };
    if ($Lon < 0) {
        $Lon = -$Lon;
        $EW = 'W';
    } else {
        $EW = 'E';
    };

    $Deg2Rad = pi() / 180.0;
//$Alpha100km='VQLFAVQLFAWRMGBWRMGBXSNHCXSNHCYTOJDYTOJDZUPKEZUPKEVQLFAVQLFAWRMGBWRMGBXSNHCXSNHCYTOJDYTOJDZUPKEZUPKE';
// UTM WGS84 Ellipsoid
    $F0 = 0.9996;
    $A1 = 6378137.0 * $F0;
    $B1 = 6356752.3142 * $F0;
    $K0 = 0;
    $N0 = 0;
    $E0 = 500000;
    $N1 = ($A1 - $B1) / ($A1 + $B1); // n
    $N2 = $N1 * $N1;
    $N3 = $N2 * $N1;
    $E2 = (($A1 * $A1) - ($B1 * $B1)) / ($A1 * $A1); // e^2
// przeliczenia
    $K = $Lat * $Deg2Rad;
    $L = $Lon * $Deg2Rad;
    $SINK = sin($K);
    $COSK = cos($K);
    $TANK = $SINK / $COSK;
    $TANK2 = $TANK * $TANK;
    $COSK2 = $COSK * $COSK;
    $COSK3 = $COSK2 * $COSK;
    $K3 = $K - $K0;
    $K4 = $K + $K0;



    $Merid = floor(($Lon) / 6) * 6 + 3;
    if (($Lat >= 72) && ($Lon >= 0)) {
        if ($Lon < 9)
            $Merid = 3;
        else if ($Lon < 21)
            $Merid = 15;
        else if ($Lon < 33)
            $Merid = 27;
        else if ($Lon < 42)
            $Merid = 39;
    }
    if (($Lat >= 56) && ($Lat < 64)) {
        if (($Lon >= 3) && ($Lon < 12))
            $Merid = 9;
    }
    $MeridEW = $Merid < 0;
    if ($MeridEW) {
        $MeridianEW = 'W';
    } else {
        $MeridianEW = 'E';
    }
    $Meridian = abs($Merid);
    $L0 = $Merid * $Deg2Rad; // Lon of True Origin (3,9,15 etc)
    // ArcofMeridian
    $J3 = $K3 * (1 + $N1 + 1.25 * ($N2 + $N3));
    $J4 = sin($K3) * cos($K4) * (3 * ($N1 + $N2 + 0.875 * $N3));
    $J5 = sin(2 * $K3) * cos(2 * $K4) * (1.875 * ($N2 + $N3));
    $J6 = sin(3 * $K3) * cos(3 * $K4) * 35 / 24 * $N3;
    $M = ($J3 - $J4 + $J5 - $J6) * $B1;

    // VRH2
    $Temp = 1 - $E2 * $SINK * $SINK;
    $V = $A1 / sqrt($Temp);
    $R = $V * (1 - $E2) / $Temp;
    $H2 = $V / $R - 1.0;

    $P = $L - $L0;
    $P2 = $P * $P;
    $P4 = $P2 * $P2;
    $J3 = $M + $N0;
    $J4 = $V / 2 * $SINK * $COSK;
    $J5 = $V / 24 * $SINK * ($COSK3) * (5 - ($TANK2) + 9 * $H2);
    $J6 = $V / 720 * $SINK * $COSK3 * $COSK2 * (61 - 58 * ($TANK2) + $TANK2 * $TANK2);
    $North = $J3 + $P2 * $J4 + $P4 * $J5 + $P4 * $P2 * $J6;
//      if ($NS) $North=$North+10000000.0; // UTM S hemisphere , nie wiem dlaczego ale w oryginale dodawa³o siê 10000000.0
    $J7 = $V * $COSK;
    $J8 = $V / 6 * $COSK3 * ($V / $R - $TANK2);
    $J9 = $V / 120 * $COSK3 * $COSK2;
    $J9 = $J9 * (5 - 18 * $TANK2 + $TANK2 * $TANK2 + 14 * $H2 - 58 * $TANK2 * $H2);
    $East = $E0 + $P * $J7 + $P2 * $P * $J8 + $P4 * $P * $J9;
    $IEast = round($East);
    $INorth = round($North); // should strictly be trunc
    $Easting = $IEast;
    $Northing = $INorth;
    $EastStr = '' + abs($IEast);
    $NorthStr = '' + abs($INorth);
    //while (EastStr.length<7) EastStr='0'+EastStr;
    $EastStr = sprintf("%07.0f", $EastStr);
    //while (NorthStr.length<7) NorthStr='0'+NorthStr;
    $NorthStr = sprintf("%07.0f", $NorthStr);
    $GR100km = substr($EastStr, 1, 2 - 1) . substr($NorthStr, 1, 2 - 1);
    $GRremainder = substr($EastStr, 2, 7 - 2) . ' ' . substr($NorthStr, 2, 7 - 2);


    // UTM
    $LonZone = ($Merid - 3) / 6 + 31;
    if ($LonZone % 1 != 0)
        $GR = 'non-UTM central meridian';
    else {
        if ($IEast < 100000 || $Lat < -80 || $IEast > 899999 || $Lat >= 84)
            $GR = 'outside UTM grid area';
        else {
            $Letters = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
            $Pos = round($Lat / 8 - 0.5) + 10 + 2;
            $LatZone = substr($Letters, $Pos, 1);
            if ($LatZone > 'X')
                $LatZone = 'X';
            $Pos = round(abs($INorth) / 100000 - 0.5);
            while ($Pos > 19)
                $Pos = $Pos - 20;
            if ($LonZone % 2 == 0) {
                $Pos = $Pos + 5;
                if ($Pos > 19)
                    $Pos = $Pos - 20;
            }
            $N100km = substr($Letters, $Pos, 1);
            $Pos = $GR100km / 10 - 1;
            $P = $LonZone;
            while ($P > 3)
                $P = $P - 3;
            $Pos = $Pos + (($P - 1) * 8);
            $E100km = substr($Letters, $Pos, 1);
            $GR = $LonZone . $LatZone . $E100km . $N100km . ' ' . $GRremainder;
        }
    }

    return array($LonZone, $LatZone, $NS, $Northing, $EW, $Easting);

}
 */
