<?php

use Utils\Database\OcDb;
use Utils\Gis\Gis;

/** class GetRegions
 *
 * this class find Counrty and region (administation district, for exapmle Poland, woj. Małopolskie)
 * returns array with names and codes. Compaibile also with table `cache_location`
 *
 * @params on input:
 *
 * float: $lat  - latitude
 * float: $lon  - longitude
 *
 *
 * Example returned
 * Array
 * (
 *    [adm1] => Polska
 *    [adm2] => Region Południowy
 *    [adm3] => Małopolskie
 *    [adm4] =>
 *    [code1] => PL
 *    [code2] => PL2
 *    [code3] => PL22
 *    [code4] =>
 *  )
 *
 *  (adm4 and code4 is returned when coordinates are in known huge city area. Not suer if it is useful, so be carefull with that)
 *
 * Example of use:
 * <?
 * $region = new GetRegions();
 * $regiony = $region->GetRegion($lat, $lon);
 * print_r ($regiony);
 * ?>
 *
 * @author Andrzej Łza Woźniak (some code I copied from former code.)
 */
class GetRegions
{

    /**
     *
     * @@param array $opt -  DB accesing data from opencaching config
     * @param float $lat geografical latitude (coordinates)
     * @param float $lon geografical longitude (coordinates)
     *
     * @return array with code and names of regions selected from input geografical coordinates.void
     */
    public function GetRegion($lat, $lon)
    {
        $lat_float = (float) $lat;
        $lon_float = (float) $lon;

        $sCode = '';
        $tmpqery = "SELECT `level`, `code`, AsText(`shape`) AS `geometry` FROM `nuts_layer` WHERE ST_WITHIN(GeomFromText('POINT($lon  $lat)'), `shape`) ORDER BY `level` DESC";

        $db = OcDb::instance();
        $s = $db->simpleQuery($tmpqery);
        $rsLayers = $db->dbResultFetchAll($s);

        foreach ($rsLayers as $rLayers) {
            if (Gis::ptInLineRing($rLayers['geometry'], 'POINT(' . $lon . ' ' . $lat . ')')) {
                $sCode = $rLayers['code'];
                break;
            }
        }

        if ($sCode != '') {
            $adm1 = null;
            $code1 = null;
            $adm2 = null;
            $code2 = null;
            $adm3 = null;
            $code3 = null;
            $adm4 = null;
            $code4 = null;

            if (mb_strlen($sCode) > 5)
                $sCode = mb_substr($sCode, 0, 5);

            if (mb_strlen($sCode) == 5) {
                $code4 = $sCode;
                $q = "SELECT `name` FROM `nuts_codes` WHERE `code`='$sCode' LIMIT 1";
                $s = $db->simpleQuery($q);
                $re = $db->dbResultFetchOneRowOnly($s);
                $adm4 = $re["name"];
                unset($re, $q);

                $sCode = mb_substr($sCode, 0, 4);
            }

            if (mb_strlen($sCode) == 4) {
                $code3 = $sCode;
                $q = "SELECT `name` FROM `nuts_codes` WHERE `code`='$sCode' LIMIT 1";

                $s = $db->simpleQuery($q);
                $re = $db->dbResultFetchOneRowOnly($s);

                $adm3 = $re["name"];
                unset($re, $q);
                $sCode = mb_substr($sCode, 0, 3);
            }

            if (mb_strlen($sCode) == 3) {
                $code2 = $sCode;
                $q = "SELECT `name` FROM `nuts_codes` WHERE `code`='$sCode' LIMIT 1";
                $s = $db->simpleQuery($q);
                $re = $db->dbResultFetch($s);
                $adm2 = $re["name"];
                unset($re, $q);
                $sCode = mb_substr($sCode, 0, 2);
            }

            if (mb_strlen($sCode) == 2) {

                $code1 = $sCode;
                // try to get localised name first
                $q = "SELECT `countries`.`pl` FROM `countries` WHERE `countries`.`short`='$sCode' LIMIT 1"; // TODO: country column should be localized
                $s = $db->simpleQuery($q);
                $re = $db->dbResultFetch($s);
                $adm1 = $re["pl"];
                unset($re, $q);

                if ($adm1 == null) {
                    $q = "SELECT `name` FROM `nuts_codes` WHERE `code`='$sCode' LIMIT 1";
                    $s = $db->simpleQuery($q);
                    $re = $db->dbResultFetch($s);
                    $adm1 = $re["name"];
                    unset($re, $q);
                }
            }

            $wynik['adm1'] = $adm1;
            $wynik['adm2'] = $adm2;
            $wynik['adm3'] = $adm3;
            $wynik['adm4'] = $adm4;
            $wynik['code1'] = $code1;
            $wynik['code2'] = $code2;
            $wynik['code3'] = $code3;
            $wynik['code4'] = $code4;
        } else
            $wynik = false;
        return $wynik;
    }

}

?>