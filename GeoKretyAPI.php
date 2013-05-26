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
	private $server   = null;
	private $rtEmailAddress = null;
	private $GeoKretyDeveloperEmailAddress = null;
	
    function __construct($secid, $cacheWpt)
    {
    	include 'lib/settings.inc.php';
		$this->server = $absolute_server_URI;
    	$this->secid = $secid;
    	$this->cacheWpt = $cacheWpt;
		$this->rtEmailAddress = $dberrormail;
		$this->geoKretyDeveloperEmailAddress = $geoKretyDeveloperEmailAddress;
		
		// $this->emailOnError('$e->getMessage()', $url, $result, 'function: '.__FUNCTION__ .'line # '.__LINE__.' in '.__FILE__);	
		
    }
    
    /**
     * sends request to geokrety and receive all geokrets in user inventory
     * 
     * @return array contains all geokrets in user inventory
     */
	private function TakeUserGeokrets()
	{
		libxml_use_internal_errors(true);	
		$url = "http://geokrety.org/export2.php?secid=$this->secid&inventory=1";
		try 
		{
	 		$result = simplexml_load_file($url);
		} 
		catch (Exception $e) 
		{
			$this->emailOnError($e->getMessage(), $url, $result, 'function: '.__FUNCTION__ .'line # '.__LINE__.' in '.__FILE__);
			return false;
		}
		
		if ($result === false) {
			$errorGK = '';
			foreach(libxml_get_errors() as $error) {
        			$errorGK .= $error->message.', ';
    			}
			$this->emailOnError($errorGK, $url, $result, 'function: '.__FUNCTION__ .'line # '.__LINE__.' in '.__FILE__);
		}
		
		return $result;
	}
	
	/**
	 * sends request to geokrety and receive all geokrets in specified cache
	 * 
	 * @return array contains all geokrets in cache
	 */	
	private function TakeGeoKretsInCache()
	{
		libxml_use_internal_errors(true);	
		$url = "http://geokrety.org/export2.php?wpt=$this->cacheWpt";
		try		
		{
	 		$result = false; // simplexml_load_file($url);
		} 
		catch (Exception $e) 
		{
			$this->emailOnError($e->getMessage(), $url, $result, 'function: '.__FUNCTION__ .' line # '.__LINE__.' in '.__FILE__);
			return false;
		}
		/*
		if ($result === false) {
			$errorGK = '';
			foreach(libxml_get_errors() as $error) {
        			$errorGK .= ('Error loading XML file ' . $error->message);
    			}
			$this->emailOnError($errorGK, $url, $result, 'function: '.__FUNCTION__ .'line # '.__LINE__.' in '.__FILE__);
		}
		*/
		return $result;
	}
	
	
	
	/**
	 * Make html table-formatted list of user geokrets. ready to display anywhere.
	 * @return string (html)
	 */
	public function MakeGeokretList()
	{
		$krety = $this->TakeUserGeokrets();
		$lista = tr('GKApi23').': ' . count($krety->geokrety->geokret).'<br>';
		 
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
 		
 		if ($krety === false) return '';
		
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
                              <input type="hidden" name="GeoKretIDAction'.$MaxNr.'[nr]" value="'.$kret->attributes()->nr.'"><span id="GKtxt'.$MaxNr.'" style="display: none">teść logu kreta: <input type="text" name="GeoKretIDAction'.$MaxNr.'[tx]" maxlength="80" size="50" value="'.tr('GKApi24').' '.$cachename.'" '.$jsclear.' /></span>
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
		if ($krety == false) return '';
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
                              <span id="GKtxt'.$MaxNr.'" style="display: none"> tracking code: <input type="text" maxlength="6" size="6"  name="GeoKretIDAction'.$MaxNr.'[nr]"> '.tr('GKApi25').': <input type="text" name="GeoKretIDAction'.$MaxNr.'[tx]" maxlength="40" size="50" value="'.tr('GKApi26').' '.$cachename.'" '.$jsclear.' /></span>
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
						'content' => $postdata,
				)
		);
		
		$context = stream_context_create($opts);
		$result = file_get_contents('http://geokrety.org/ruchy.php', false, $context);
		print $result;
		// print '----------<pre>'; print_r($resultarray); print '</pre>-------------';
		
		if (!$result) 
		{
			$this->emailOnError($error, print_r($GeokretyArray, true), $result, 'function: '.__FUNCTION__ .'line # '.__LINE__.' in '.__FILE__);
			return false;
		}
		
		try
		{
		 $resultarray = simplexml_load_string($result);
		}
		catch(Exception $e) 
		{
			$this->emailOnError($e->getMessage(), print_r($GeokretyArray, true), $result, 'line # '.__LINE__.' in '.__FILE__);
			return false;
		}
		if ($resultarray) $r = $this->xml2array($resultarray);
		else $r['errors'][0]['error'] = tr(GKApi22);
		
		$r['geokretId'] = $GeokretyArray['id'];
		$r['geokretName'] = $GeokretyArray['nm'];
		
		/*
		 print '----------<pre>';
		 print_r ($r);
		 print '</pre>-------------';
		*/

		return  $r;
	}

	private function emailOnError($error = '', $Tablica = '', $result, $errorLocation = 'Unknown error location')
	{
		$message = "GeoKretyApi error report: \r\n " .$error . "\n
			 \r\n location of error: $errorLocation \n
			 \r\n Tablica Logowania Geokreta:\r\n\r\n $Tablica \r\n\r\n  geokrety.org zwrocilo niepoprawny wynik (wynik nie jest w formacie xml). \r\n 
			data i czas: ".date('Y-m-d H:i:s')."
			Odpowiedz geoKretow ponizej: \r\n \r\n $result ";
				
		$headers = 'From: GeoKretyAPI on opencaching.pl' . "\r\n" .
					'Reply-To: rt@opencaching.pl' . "\r\n" .
					'X-Mailer: PHP/' . phpversion();
		
		// check if working developer server or production.
		if ($this->server == 'http://local.opencaching.pl/') {
			$rtAddress = array('user@ocpl-devel');
		}
		else {
			$rtAddress = array($this->rtEmailAddress, $this->geoKretyDeveloperEmailAddress);
		}
			
		foreach ($rtAddress as $email) {
			// mail($email, 'GeoKretyApi returned error', $message, $headers);
		}

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
?>