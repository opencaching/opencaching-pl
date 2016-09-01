<?php
/**
 * This is helper class which provide method for NUTS data quering
 */
namespace Utils\Gis;


use Utils\Database\XDb;

class Region {

    /**
     * This function check if given code is a proper NUTS code in the OC db
     *
     * @param String $code - NUTS code for example 'PL63'
     * @return true if given code is found
     */
    public static function checkProvinceCode($code){

        return ( 0 < XDb::xMultiVariableQueryValue(
            "SELECT COUNT(*) FROM nuts_codes WHERE code= :1 LIMIT 1", 0, $code)
            );
    }

    /**
     * Return name of region by given NUTS code
     * @param unknown $code
     */
    public static function getRegionName($code){
        return XDb::xMultiVariableQueryValue(
            "SELECT name FROM nuts_codes WHERE code= :1 LIMIT 1", 'Unknown?', $code);
    }

}