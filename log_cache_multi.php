<?php
require_once('./lib/common.inc.php');

$no_tpl_build = false;
if ($usr == false || (!isset($_FILES['userfile']) && !isset($_SESSION['log_cache_multi_data'])) )
{
	tpl_redirect('log_cache_multi_send.php');
}
else
{
	require_once($rootpath . 'lib/caches.inc.php');
	require($stylepath . '/log_cache.inc.php');

	$tplname = 'log_cache_multi';
	$myHtml = "";

	$statusy = array();
	$statusy = fcGetStatusyEn();

      	// moje dane o skrzynkach z pliku...	
      	$dane = array();

	if(isset($_FILES['userfile']))
	{
		// usuwam zapamietane a nieaktualne juz dane...
		unset($_SESSION['log_cache_multi_data']);
		unset($_SESSION['filter_to']);
		unset($_SESSION['filter_from']);

	      	// czy wyslalo sie ok?
	      	if($_FILES['userfile']['error'] != 0 ) {
	      		// jesli nie to jaki blad?
	      		if($_FILES['userfile']['error'] == 2) {
	      			die("Plik zbyt du¿y");
	      		}
	      		exit;
	      	}
	      	
	      	// czy ktos cos nie kombinuje?
	      	if (!is_uploaded_file($_FILES['userfile']['tmp_name']))
	      	{
	      		die("Coœ nie tak z wysy³aniem pliku, spróbuj ponownie...");
	      	}
	      	
	      	// wczytuje plik
	      	$some_file = $_FILES['userfile']['tmp_name'];
	      	$filesize = filesize($some_file);
	      	$fp = fopen($some_file, "r");
	      	$filecontent = fread($fp, $filesize);
	      	fclose($fp);
	      	// kasuje tymczasowy plik uploadu
	      	unlink($_FILES['userfile']['tmp_name']);
	      	unset($_FILES['userfile']);
	      	
	      	// sprawdz czy utf16 i konwert jesli tak
	      	if( ( $filesize >= 2)
	      	    &&
	      	    (
	      	      ($filecontent[0] == 0x00 || $filecontent[1] == 0x00)
	      	      ||
	      	      ($filecontent[0] == 0xFF && $filecontent[1] == 0xFE)
	      	      ||
	      	      ($filecontent[0] == 0xFE || $filecontent[1] == 0xFF)
	      	    )
	      	) {
	      		$filecontent = utf16_to_utf8($filecontent);
	      	}
	      	
	      	$filecontent = explode("\r\n", $filecontent);
	
	      	$dane_i = -1;
	
	      	// parsuje plik
	    	foreach($filecontent as $line)
	    	{
	    		$rec = explode(",", trim($line));
	    		if(count($rec) >= 4) {
	    			// wyglada na skrzynke
	    			if(substr($rec[0], 0, 2) == "OP") {
		    			$dane_i++;
		    			$dane[$dane_i]['kod_str'] = $rec[0];
	    				$dane[$dane_i]['typ_kodu'] = "OP";
	    				if(strlen($listaKodowOP) > 0) {
	    					$listaKodowOP .= ",";
	    				}
	    				$listaKodowOP .= "'".$dane[$dane_i]['kod_str']."'";
		    			// kod
		    			// czas
		    			$regex = "/(.+)-(.+)-(.+)T(.+):(.+)Z/";
		    			$ileMatches = preg_match($regex, trim($rec[1]), $matches);
		    			if(count($matches) >=6) {
		    				$dane[$dane_i]['rok'] = $matches[1];
		    				$dane[$dane_i]['msc'] = $matches[2];
		    				$dane[$dane_i]['dzien'] = $matches[3];
		    				$dane[$dane_i]['godz'] = $matches[4];
		    				$dane[$dane_i]['min'] = $matches[5];
		    				$dane[$dane_i]['timestamp'] = mktime($matches[4], $matches[5], 0, $matches[2], $matches[3], $matches[1]);
		    				$dane[$dane_i]['data'] = date("Y-m-d H:i", $dane[$dane_i]['timestamp']);
		    				unset($matches);
		    			}
		    			//status
		    			$dane[$dane_i]['status'] = isset($statusy[trim($rec[2])]) ? $statusy[trim($rec[2])] : 0;
		    			// komentarz
		    			$dane[$dane_i]['koment'] = str_replace("\"", "", trim($rec[3]));
	    			}
	    		}
	    	}
	    	// plik sparsowany...

		$_SESSION['log_cache_multi_data'] = $dane;
	}// EOF jesli jest plik wyslany to parsowanie...

	if(isset($_SESSION['log_cache_multi_data']))
	{
		$dane = $_SESSION['log_cache_multi_data'];

	      	// pomocna lista do WHEREa
	      	$listaKodowOP = "";
	      	$minTimeStamp = time();
	      	$maxTimeStamp = 1;
	      	foreach($dane as $k=>$v)
	      	{
			if(strlen($listaKodowOP) > 0) {
				$listaKodowOP .= ",";
			}
			$listaKodowOP .= "'".$v['kod_str']."'";
			if($v['timestamp'] < $minTimeStamp) $minTimeStamp = $v['timestamp'];
			if($v['timestamp'] > $maxTimeStamp) $maxTimeStamp = $v['timestamp'];
	      	}
	      	
		// lista identyfikatorow cache ktore znalazlem w bazie
		$cacheIdList = "";
	
	      	// dociagam informacje o nazwie i id skrzynki...			
	      	if(strlen($listaKodowOP) > 0)
	      	{
	      		$rs = sql("SELECT * FROM `caches` WHERE `wp_oc` IN (".$listaKodowOP.")");
	      		if (mysql_num_rows($rs) != 0)
	      		{
	      			$i=0;
	      			while($i < mysql_num_rows($rs))
	      			{
	      				$record = sql_fetch_array($rs);
	      				// dodanie dodatkowych info do odpowiedniej skrzynki:
	      				foreach($dane as $k=>$v)
	      				{
	      					if($v['kod_str'] == $record['wp_oc'])
	      					{
	      						$v['got_sql_info'] = true;
	      						$v['cache_id'] = $record['cache_id'];
	      						$v['cache_type'] = $record['type'];
	      						$v['cache_name'] = $record['name'];
	      						$dane[$k] = $v;
			    				if(strlen($cacheIdList) > 0) {
			    					$cacheIdList .= ",";
			    				}
			    				$cacheIdList .= "'".$record['cache_id']."'";
	      					}
	      				}
	      				$i++;
	      			}
	      		}
	      	}
	
		// dociagam info o ostatniej aktywnosci dla kazdej skrzynki
		if(strlen($cacheIdList) > 0)
		{
			$rs = sql("SELECT c.* FROM (SELECT cache_id, MAX(date) date FROM `cache_logs` WHERE user_id='".sql_escape($usr['userid'])."' AND cache_id IN (".$cacheIdList.") GROUP BY cache_id) as x INNER JOIN `cache_logs` as c ON c.cache_id = x.cache_id AND c.date = x.date");
			if (mysql_num_rows($rs) != 0)
			{
				$i=0;
				while($i < mysql_num_rows($rs))
				{
					$record = sql_fetch_array($rs);
					foreach($dane as $k=>$v)
					{
						if($v['cache_id'] == $record['cache_id'])
						{
							$v['got_last_activity'] = true;
							$v['last_date'] = substr($record['date'], 0, strlen($record['date'])-3);
							$v['last_status'] = $record['type'];
							$dane[$k] = $v;
						}
					}
					$i++;
				}
			}
		}

		// filtrowanie...		
		// wczytanie wartosci filtrow, a jesli nie ma to odpowiednio min i max wartosc z pliku tak by wszystkie byly.
		if(isset($_POST['filter_from']) && false !== strtotime($_POST['filter_from']))
		{
			$filter_from = strtotime($_POST['filter_from']);
		} else if(isset($_SESSION['filter_from'])) {
			$filter_from = $_SESSION['filter_from'];		
		} else {
			$filter_from = $minTimeStamp;
		}
		$_SESSION['filter_from'] = $filter_from;
		if(isset($_POST['filter_to']) && false !== strtotime($_POST['filter_to']))
		{
			$filter_to = strtotime($_POST['filter_to']);
		} else if(isset($_SESSION['filter_to'])) {
			$filter_to = $_SESSION['filter_to'];
		} else {
			$filter_to = $maxTimeStamp;
		}
		$_SESSION['filter_to'] = $filter_to;



		
		// lece po wszystkim i kolejne opracje:
		$daneFiltrowane = array();			
		foreach($dane as $k=>$v)
		{
			// dodaje mass komentarze dla filtrowanych skrzynek:
			if(isset($_POST['submitCommentsForm']) && isset($_POST['logtext']))
			{
				$v['koment'] .= " ".$_POST['logtext'];
			}		
			
			if(isset($_POST['SubmitShiftTimeMinusOne']))
			{
				$v['timestamp'] = $v['timestamp'] - (60*60);
    				$v['data'] = date("Y-m-d H:i", $v['timestamp']);
			}
			
			if(isset($_POST['SubmitShiftTimePlusOne']))
			{
				$v['timestamp'] = $v['timestamp'] + (60*60);
    				$v['data'] = date("Y-m-d H:i", $v['timestamp']);
			}

			if($v['timestamp'] <= $filter_to && $v['timestamp'] >= $filter_from)
			{
				$doFiltra = true;
			} else {
				$doFiltra = false;
			}
			
			$dane[$k] = $v;
			if($doFiltra) {
				$daneFiltrowane[$k] = $v; // uzywam $k by miec te same klucze co oryginalna tablica, przyda sie pozniej.
			}


		}
		
		// odswiezone dane do sesji:
		$_SESSION['log_cache_multi_data'] = $dane;
		// oryginalna tablice mam zapisana w sesji, wiec tu spokojnie moge nadpisac do prezentacji.
		$dane = $daneFiltrowane;
	}
	
	tpl_set_var('filter_from', date("Y-m-d H:i", $filter_from));
	tpl_set_var('filter_to', date("Y-m-d H:i", $filter_to));
	tpl_set_var('log_cache_multi_html', $myHtml);
	
} // EOF user logged i jest plik

if ($no_tpl_build == false)
{
	//make the template and send it out
	tpl_BuildTemplate(false);
}


function utf16_to_utf8($str) {
	$c0 = ord($str[0]);
	$c1 = ord($str[1]);

	if ($c0 == 0xFE && $c1 == 0xFF) {
		$str = substr($str, 2);
		$be = true;
	} else if ($c0 == 0xFF && $c1 == 0xFE) {
		$str = substr($str, 2);
		$be = false;
	} else if ($c0 != 0x00 && $c1 == 0x00) {
		$be = false;
	} else if ($c0 == 0x00 && $c1 != 0x00) {
		$be = true;
	} else {
		return $str;
	}

	$len = strlen($str);
	$dec = '';
	for ($i = 0; $i < $len; $i += 2) {
		$c = ($be) ? ord($str[$i]) << 8 | ord($str[$i + 1]) : ord($str[$i + 1]) << 8 | ord($str[$i]);

		if ($c >= 0x0001 && $c <= 0x007F) {
			$dec .= chr($c);
		} else if ($c > 0x07FF) {
			$dec .= chr(0xE0 | (($c >> 12) & 0x0F));
			$dec .= chr(0x80 | (($c >>  6) & 0x3F));
			$dec .= chr(0x80 | (($c >>  0) & 0x3F));
		} else {
			$dec .= chr(0xC0 | (($c >>  6) & 0x1F));
			$dec .= chr(0x80 | (($c >>  0) & 0x3F));
		}
	}
	return $dec;
}


function fcGetStatusyEn()
{
	$statusy = array();
	$statusy['Found it'] = 1; // Znaleziona
	$statusy['Didn\'t find it'] = 2; // Nie znaleziona
	$statusy['Unattempted'] = 3; // Komentarz
	$statusy['Needs Maintenance'] = 5; // Potrzebny serwis
	return $statusy;
}


?>