<?php

namespace src\Models\Coordinates;

use src\Models\BaseObject;
use src\Utils\I18n\I18n;

/**
 * Class represents location of the point in NUTS nomenclature.
 *
 * see: https://en.wikipedia.org/wiki/Nomenclature_of_Territorial_Units_for_Statistics
 * for details.
 *
 */
class NutsLocation extends BaseObject
{
    const LEVEL_COUNTRY = 0;
    const LEVEL_1 = 1;
    const LEVEL_2 = 2;
    const LEVEL_3 = 3;

    private $codes = [];
    private $names = [];

    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Load NUTS data from DB for given coordinates
     *
     * @param Coordinates $coords
     */
    private function loadFromCoords(Coordinates $coords)
    {

        $rs = $this->db->multiVariableQuery(
            "SELECT `level`, code, AsText(shape) AS geometry
            FROM nuts_layer
            WHERE ST_WITHIN( GeomFromText( :1 ), shape)
            ORDER BY `level` DESC",
            "POINT({$coords->getLongitude()} {$coords->getLatitude()})");

        while ($row = $this->db->dbResultFetch($rs)) {
            $this->codes[$row['level']] = $row['code'];
        }

        $this->loadNamesForCodes();

    }

    /**
     * Match name of every code in object.
     */
    private function loadNamesForCodes()
    {
        $limit = count($this->codes);
        $codesStr = implode("','", $this->codes);

        $rs = $this->db->simpleQuery(
            "SELECT code, name FROM nuts_codes
            WHERE code IN ( '$codesStr' )
            LIMIT $limit");

        $nutsNames = [];
        while ($row = $this->db->dbResultFetch($rs)) {
            $nutsNames[$row['code']] = $row['name'];
        }

        foreach ($this->codes as $level => $code) {
            if (isset($nutsNames[$code])) {
                $this->names[$level] = $nutsNames[$code];
            } else {
                $this->names[$level] = null;
            }
        }
    }

    public static function fromCoordsFactory(Coordinates $coords)
    {
        $obj = new self();
        $obj->loadFromCoords($coords);
        return $obj;
    }

    /**
     * @return string
     */
    public function getCountryName()
    {
        // try to translate country name
        if (I18n::isTranslationAvailable($this->codes[self::LEVEL_COUNTRY])) {
            return tr($this->codes[self::LEVEL_COUNTRY]);
        } else {
            return $this->names[self::LEVEL_COUNTRY];
        }
    }

    /**
     * @return mixed|string
     */
    public function getRegionName()
    {
        // try to detect region name in order 2-1-3 (NUTS-levels)
        // (smaller countries has e.g. only 3-level names)
        if (!empty($this->codes[self::LEVEL_2])) {
            $region = $this->names[self::LEVEL_2];

        } elseif (!empty($this->codes[self::LEVEL_1])) {
            $region = $this->names[self::LEVEL_1];

        } else {
            if (!empty($this->codes[self::LEVEL_3])) {
                $region = $this->names[self::LEVEL_3];
            } else {
                $region = '?';
                // bug in NUTS data?! country present, no level names!?
                //Debug::errorLog("NUTS data error? No code for ".$this->codes[self::LEVEL_COUNTRY]);
            }
        }
        return $region;
    }

    /**
     * @param string $separator
     * @return string
     */
    public function getDescription($separator = '-')
    {

        if (!isset($this->codes[self::LEVEL_COUNTRY])) {
            // location is unknown
            return "? $separator ?";
        }

        return $this->getCountryName() . $separator . $this->getRegionName();
    }

    public function getCode($level)
    {
        if (isset($this->codes[$level])) {
            return $this->codes[$level];
        } else {
            return null;
        }
    }

    public function getName($level)
    {
        if (isset($this->names[$level])) {
            return $this->names[$level];
        } else {
            return null;
        }
    }

    public function setLevel($level, $code, $name)
    {
        $this->codes[$level] = $code;
        $this->names[$level] = $name;
    }

    public function getDataTable()
    {
        return [
            'adm1' => $this->getName(NutsLocation::LEVEL_COUNTRY),
            'adm2' => $this->getName(NutsLocation::LEVEL_1),
            'adm3' => $this->getName(NutsLocation::LEVEL_2),
            'adm4' => $this->getName(NutsLocation::LEVEL_3),

            'code1' => $this->getCode(NutsLocation::LEVEL_COUNTRY),
            'code2' => $this->getCode(NutsLocation::LEVEL_1),
            'code3' => $this->getCode(NutsLocation::LEVEL_2),
            'code4' => $this->getCode(NutsLocation::LEVEL_3),
        ];
    }

    /**
     * @return TRUE if any of location data is not NULL
     */
    public function isAnyDataFound()
    {
        foreach ($this->codes as $code) {
            if (!is_null($code)) {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * This function check if given code is a proper NUTS code in the OC db
     *
     * @param String $code - NUTS code for example 'PL63'
     * @return true if given code is found
     */
    public static function checkProvinceCode($code)
    {

        return (0 < self::db()->multiVariableQueryValue(
                "SELECT COUNT(*) FROM nuts_codes WHERE code= :1 LIMIT 1", 0, $code)
        );
    }

//    /**
//     * Return name of region by given NUTS code
//     * @param string $code
//     */
//    public static function getRegionName($code){
//
//        return self::db()->multiVariableQueryValue(
//            "SELECT name FROM nuts_codes WHERE code= :1 LIMIT 1", 'Unknown?', $code);
//    }

    /**
     * Returns list of the regions by given country code
     *
     * @param string $countryCode
     * @return array
     */
    public static function getRegionsListByCountryCode($countryCode)
    {
        $countryCode .= '__'; // add sql wildcard (two letters)
        $db = self::db();
        return $db->dbResultFetchAll($db->multiVariableQuery(
            "SELECT code, name FROM nuts_codes
             WHERE code LIKE :1
             ORDER BY name ASC", $countryCode));
    }

}