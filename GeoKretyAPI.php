<?php
/**
 * This class contain methods used to communicate with Geokrety, via Geokrety Api
 * (http://geokrety.org/api.php)
 * 
 * @author Andrzej Łza Woźniak 2012, 2013
 *
 */
class GeoKretyApi
{
	private $secid    = null;
	private $cacheWpt = null; 
	private $maxID    = null;
	
    function __construct($secid, $cacheWpt)
    {
    	$this->secid = $secid;
    	$this->cacheWpt = $cacheWpt;
    }
    
    /**
     * sends request to geokrety and receive all geokrets in user inventory
     * 
     * @return array contains all geokrets in user inventory
     */
	private function TakeUserGeokrets()
	{
	 $url = "http://geokrety.org/export2.php?secid=$this->secid&inventory=1";

	 return simplexml_load_file($url);
	}
	
	/**
	 * sends request to geokrety and receive all geokrets in specified cache
	 * 
	 * @return array contains all geokrets in cache
	 */	
	private function TakeGeoKretsInCache()
	{
		$url = "http://geokrety.org/export2.php?wpt=$this->cacheWpt";
		return simplexml_load_file($url);
	}
	
	
	
	/**
	 * Make html table-formatted list of user geokrets. ready to display anywhere.
	 * @return string (html)
	 */
	public function MakeGeokretList()
	{
		$krety = $this->TakeUserGeokrets();
		$lista = 'liczba geokretów u siebie: ' . count($krety->geokrety->geokret).'<br>';
		 
		$lista .= '<table>';
	 foreach ($krety->geokrety->geokret as $kret)
	 {
	 	$lista .= '<tr><td></td><td><a href="http://geokrety.org/konkret.php?id='.$kret->attributes()->id.'">'. $kret .'</a></td></tr>';
	 }
	 $lista .= '</table>';
	 echo $lista;
	}
	
	

	/**
	 * generate html-formatted list of all geokrets in user inventory.
	 * This string is used in logging cache (log.php, log_cache.tpl.php)
	 * 
	 * @return string (html)
	 */
	public function MakeGeokretSelector($cachename)
	{
		$krety = $this->TakeUserGeokrets();
 
		$selector = '<table>';
		$MaxNr = 0;
		$jsclear = 'onclick=this.value="" onblur="formDefault(this)"';
		foreach ($krety->geokrety->geokret as $kret)
		   {
		   	$MaxNr++;
		   	$selector .= '<tr>
					        <td>
					          <a href="http://geokrety.org/konkret.php?id='.$kret->attributes()->id.'">'.$kret.'</a>
					        </td>
					        <td>
					          <select id="GeoKretSelector'.$MaxNr.'" name="GeoKretIDAction'.$MaxNr.'[action]" onchange="GkActionMoved('.$MaxNr.')"><option value="-1">'.tr('GKApi13').'</option><option value="0">'.tr('GKApi12').'</option><option value="5">'.tr('GKApi14').'</option></select>
                              <input type="hidden" name="GeoKretIDAction'.$MaxNr.'[nr]" value="'.$kret->attributes()->nr.'"><span id="GKtxt'.$MaxNr.'" style="display: none">teść logu kreta: <input type="text" name="GeoKretIDAction'.$MaxNr.'[tx]" maxlength="80" size="50" value="w keszyku '.$cachename.'" '.$jsclear.' /></span>
                              <input type="hidden" name="GeoKretIDAction'.$MaxNr.'[id]" value="'.$kret->attributes()->id.'">
                              <input type="hidden" name="GeoKretIDAction'.$MaxNr.'[nm]" value="'.$kret.'" />		
                             </td>
					     </tr>';
		   }
		$selector .= '</table>';
		// $selector .= '<input type="hidden" name=MaxNr value="'.$MaxNr.'">';
		$this->maxID = $MaxNr; //value set for use in MakeGeokretInCacheSelector method.
		return $selector;
	}

	public function MakeGeokretInCacheSelector($cachename)
	{
		$krety = $this->TakeGeoKretsInCache();
	
		$selector = '<table>';
		$MaxNr = $this->maxID;
		$jsclear = 'onclick=this.value="" onblur="formDefault(this)"';
		foreach ($krety->geokrety->geokret as $kret)
		{
			$MaxNr++;
			$selector .= '<tr>
					        <td>
					          <a href="http://geokrety.org/konkret.php?id='.$kret->attributes()->id.'">'.$kret.'</a>
					        </td>
					        <td>
					          <select id="GeoKretSelector'.$MaxNr.'" name="GeoKretIDAction'.$MaxNr.'[action]" onchange="GkActionMoved('.$MaxNr.')"><option value="-1">'.tr('GKApi13').'</option><option value="1">'.tr('GKApi15').'</option><option value="2">'.tr('GKApi16').'</option><option value="3">'.tr('GKApi17').'</option></select>
                              <span id="GKtxt'.$MaxNr.'" style="display: none"> tracking code: <input type="text" maxlength="6" size="6"  name="GeoKretIDAction'.$MaxNr.'[nr]"> treść logu kreta: <input type="text" name="GeoKretIDAction'.$MaxNr.'[tx]" maxlength="40" size="50" value="Zabrano z keszyka '.$cachename.'" '.$jsclear.' /></span>
                              <input type="hidden" name="GeoKretIDAction'.$MaxNr.'[id]" value="'.$kret->attributes()->id.'" />
                              <input type="hidden" name="GeoKretIDAction'.$MaxNr.'[nm]" value="'.$kret.'" />
                            </td>
					     </tr>';
		}
		$selector .= '</table>';
		$selector .= '<input type="hidden" name=MaxNr value="'.$MaxNr.'">';
		return $selector;
	}	
	
	
	/**
	 * Function logs Geokret on geokrety.org using GeoKretyApi.
	 * @author Łza
	 * @param array $GeokretyArray
	 * @return boolean
	 */
	public function LogGeokrety($GeokretyArray)
	// TODO: obluga błędów zwracanych w xmlu przez geokrety.
	{ 
		/*
		print '----------<pre>';
		print_r ($GeokretyArray);
		print '</pre>-------------';
		*/
		
	    $postdata = http_build_query($GeokretyArray);
	
		$opts = array('http' =>
				array(
						'method'  => 'POST',
						'header'  => 'Content-type: application/x-www-form-urlencoded',
						'content' => $postdata
				)
		);
		
		$context = stream_context_create($opts);
		$result = file_get_contents('http://geokrety.org/ruchy.php', false, $context);
		print $result;
		// print '----------<pre>'; print_r($resultarray); print '</pre>-------------';
		
		if (!$result) 
		{
			$Tablica = print_r($GeokretyArray, true);
			$message = "przechwycono Blad z GeoKretyApi\r\n \r\n Tablica Logowania Geokreta:\r\n\r\n $Tablica \r\n\r\n  geokrety.org zwrocilo niepoprawny wynik (wynik nie jest w formacie xml, lub brak wyniku). \r\n Odpowiedz geoKretow ponizej: \r\n \r\n $result ";
			
			$headers = 'From: GeoKretyAPI on opencaching.pl' . "\r\n" .
            'Reply-To: rt@opencaching.pl' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

			mail('rt@opencaching.pl', 'GeoKretyApi returned error', $message, $headers);
			return false;
		}
		
		try
		{
		 $resultarray = simplexml_load_string($result);
		}
		catch(Exception $e) {
			$Tablica = print_r($GeokretyArray, true);
			$message = "przechwycono Blad z GeoKretyApi\r\n " .$e->getMessage() . "\n
			 \r\n Tablica Logowania Geokreta:\r\n\r\n $Tablica \r\n\r\n  geokrety.org zwrocilo niepoprawny wynik (wynik nie jest w formacie xml). \r\n 
			Odpowiedz geoKretow ponizej: \r\n \r\n $result ";
				
			$headers = 'From: GeoKretyAPI on opencaching.pl' . "\r\n" .
					'Reply-To: rt@opencaching.pl' . "\r\n" .
					'X-Mailer: PHP/' . phpversion();
			
			mail('rt@opencaching.pl', 'GeoKretyApi returned error', $message, $headers);
			return false;
		}
		if (!$resultarray) return false;
		
		$r = $this->xml2array($resultarray);
		$r['geokretId'] = $GeokretyArray['id'];
		
		/*
		 print '----------<pre>';
		 print_r ($r);
		 print '</pre>-------------';
		*/

		// return xml object converted to array.
		return  $r;
	}

	private function xml2array($xml)
	{
		$arr = array();
	
		foreach ($xml->children() as $r)
		{
			$t = array();
			if(count($r->children()) == 0)
			{
				$arr[$r->getName()] = strval($r);
			}
			else
			{
				$arr[$r->getName()][] = $this->xml2array($r);
			}
		}
		return $arr;
	}
	

}


class DbPdoConnect
{
	function __construct()
	{
	 include 'lib/settings.inc.php';
	 $this->server   = $opt['db']['server'];
	 $this->name     = $opt['db']['name'];
	 $this->username = $opt['db']['username'];
	 $this->password = $opt['db']['password'];
	}

	public function DbPdoConnect($query)
	{
		$dbh = new PDO("mysql:host=".$this->server.";dbname=".$this->name,$this->username,$this->password);
		$dbh -> query ('SET NAMES utf8');
		$dbh -> query ('SET CHARACTER_SET utf8_unicode_ci');
		
		$STH = $dbh -> prepare($query);
		$STH -> execute();

		return $STH -> fetch();
	}
}

?>