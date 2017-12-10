<?php

namespace lib\Objects\Coordinates;

use lib\Objects\BaseObject;

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
            "SELECT level, code, AsText(shape) AS geometry
            FROM nuts_layer
            WHERE ST_WITHIN( GeomFromText( :1 ), shape)
            ORDER BY level DESC",
            "POINT({$coords->getLongitude()} {$coords->getLatitude()})");

        while($row = $this->db->dbResultFetch($rs)){
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
        while($row = $this->db->dbResultFetch($rs)){
            $nutsNames[$row['code']] = $row['name'];
        }

        foreach($this->codes as $level=>$code){
            if(isset($nutsNames[$code])){
                $this->names[$level] = $nutsNames[$code];
            }else{
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

    public function getCountryName()
    {
        return $this->names[self::LEVEL_COUNTRY];
    }

    public function getDescription($separator='-'){

        // try to translate country name
        if (tr_available($this->codes[self::LEVEL_COUNTRY])){
            $country = tr($this->codes[self::LEVEL_COUNTRY]);
        } else {
            $country = $this->names[self::LEVEL_COUNTRY];
        }

        // try to detect region name in order 2-1-3 (NUTS-levels)
        // (smaller countries has e.g. only 3-level names)
        if(!empty($this->codes[self::LEVEL_2])){
            $region = $this->names[self::LEVEL_2];

        }elseif(!empty($this->codes[self::LEVEL_1])){
            $region = $this->names[self::LEVEL_1];

        }else{
            $region = $this->names[self::LEVEL_3];
        }

        return $country . $separator . $region;
    }

    public function setLevel($level, $code, $name)
    {
        $this->codes[$level] = $code;
        $this->names[$level] = $name;
    }

    /**
     * This function check if given code is a proper NUTS code in the OC db
     *
     * @param String $code - NUTS code for example 'PL63'
     * @return true if given code is found
     */
    public static function checkProvinceCode($code){

        return ( 0 < self::db()->multiVariableQueryValue(
            "SELECT COUNT(*) FROM nuts_codes WHERE code= :1 LIMIT 1", 0, $code)
            );
    }

    /**
     * Return name of region by given NUTS code
     * @param string $code
     */
    public static function getRegionName($code){

        return self::db()->multiVariableQueryValue(
            "SELECT name FROM nuts_codes WHERE code= :1 LIMIT 1", 'Unknown?', $code);
    }

}

