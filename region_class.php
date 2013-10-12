<?php
/** class GetRegions
 *
 * this class find Counrty and region (administation district, for exapmle Poland, woj. Małopolskie)
 * returns array with names and codes. Compaibile also with table `cache_location`
 *
 * @params on input:
 *
 * array: $opt  - (data with connection setup for PDO library)
 * $lang,       - cross portal variable from common.inc.php
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
 * $regiony = $region->GetRegion($opt, $lang, $lat, $lon);
 * print_r ($regiony);
 * ?>
 *
 *@author Andrzej Łza Woźniak (some code I copied from former code.)
 */

require_once(__DIR__.'/lib/db.php'); 
 
class GetRegions {

	/**
	 * 
	 * @param array $opt -  database accesing data from opencaching config
	 * @param string $querry - MySQLquery
	 * @return data from database
	 */
	 
	private function my_sql($opt, $querry)	{
		try	{
			$pdo = new PDO("mysql:host=".$opt['db']['server'].";dbname=".$opt['db']['name'],$opt['db']['username'],$opt['db']['password']);
			$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$pdo -> exec("SET CHARACTER SET utf8");
			$sth = $pdo -> prepare($querry);
			$sth -> execute();
			$result = $sth -> fetchAll();
		} catch(PDOException $e) {
			echo "Error PDO Library: (region_class.php function my_sql) " . $e -> getMessage();
			exit;
		}
		return $result;
	}

	/**
	 * For use with single result (LIMIT 1)
	 * @param array $opt -  database accesing data from opencaching config
	 * @param string $querry - MySQLquery
	 * @return data from database
	 */
	private function one_from_mysql($opt, $query) {
		try {
			$DBH =  new PDO("mysql:host=".$opt['db']['server'].";dbname=".$opt['db']['name'],$opt['db']['username'],$opt['db']['password']);
			$DBH -> exec("SET CHARACTER SET utf8");
			$STH = $DBH -> prepare($query);
			$STH -> execute();
			$result = $STH -> fetch();
		} catch(PDOException $e) {
			echo "Error PDO Library: (region_class.php function my_sql) " . $e -> getMessage();
			exit;
		}
		return $result;
	}

	/**
	 * 
	 * @@param array $opt -  database accesing data from opencaching config
	 * @param unknown_type $lang
	 * @param float $lat geografical latitude (coordinates)
	 * @param float $lon geografical longitude (coordinates)
	 * 
	 * @return array with code and names of regions selected from input geografical coordinates.void
	 */
	public function GetRegion($opt, $lang, $lat, $lon) {
		require_once(__DIR__.'/lib/gis/gis.class.php');

		$lat_float = (float) $lat;
		$lon_float = (float) $lon;

		$sCode = '';
		$tmpqery = "SELECT `level`, `code`, AsText(`shape`) AS `geometry` FROM `nuts_layer` WHERE WITHIN(GeomFromText('POINT($lon  $lat)'), `shape`) ORDER BY `level` DESC";
		
		$db = new dataBase;
		$db->simpleQuery($tmpqery);
		$rsLayers = $db->dbResultFetchAll();
		
		// $rsLayers = $this->my_sql($opt, $tmpqery);

		foreach ($rsLayers as $rLayers)	{
			if (gis::ptInLineRing($rLayers['geometry'], 'POINT(' . $lon . ' ' . $lat . ')')) {
				$sCode = $rLayers['code'];
				break;
			}
		}

		if ($sCode != '') {
			$adm1 = null; $code1 = null;
			$adm2 = null; $code2 = null;
			$adm3 = null; $code3 = null;
			$adm4 = null; $code4 = null;

			if (mb_strlen($sCode) > 5) $sCode = mb_substr($sCode, 0, 5);

			if (mb_strlen($sCode) == 5) {
				$code4 = $sCode;
				$q = "SELECT `name` FROM `nuts_codes` WHERE `code`='$sCode'";
				$db->simpleQuery($q);
				$re = $db->dbResultFetch();
				// $re = $this::one_from_mysql($opt, $q);
				$adm4 = $re["name"];
				unset ($re, $q);

				$sCode = mb_substr($sCode, 0, 4);
			}

			if (mb_strlen($sCode) == 4) {
				$code3 = $sCode;
				$q = "SELECT `name` FROM `nuts_codes` WHERE `code`='$sCode'";
				
				$db->simpleQuery($q);
				$re = $db->dbResultFetch();
				// $re = $this::one_from_mysql($opt, $q);
				
				$adm3 = $re["name"];
				unset ($re, $q);
				$sCode = mb_substr($sCode, 0, 3);
			}

			if (mb_strlen($sCode) == 3) {
				$code2 = $sCode;
				$q = "SELECT `name` FROM `nuts_codes` WHERE `code`='$sCode'";
				$db->simpleQuery($q);
				$re = $db->dbResultFetch();
				//$re = $this::one_from_mysql($opt, $q);
				$adm2 = $re["name"];
				unset ($re, $q);
				$sCode = mb_substr($sCode, 0, 2);
			}

			if (mb_strlen($sCode) == 2) {

				$code1 = $sCode;
				// try to get localised name first
				$q = "SELECT `countries`.`pl` FROM `countries` WHERE `countries`.`short`='$sCode'";
				$db->simpleQuery($q);
				$re = $db->dbResultFetch();
				//$re = $this::one_from_mysql($opt, $q);
				$adm1 = $re["pl"];
				unset ($re, $q);

				if ($adm1 == null) {
					$q = "SELECT `name` FROM `nuts_codes` WHERE `code`='$sCode'";
					$db->simpleQuery($q);
					$re = $db->dbResultFetch();	
					// $re = one_from_mysql($opt, $q);
					$adm1  = $re["name"];
					unset ($re, $q);
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
		}
		else $wynik = false;
		return $wynik;
	}
}
?>