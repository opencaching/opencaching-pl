<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder ăĄă˘
 ***************************************************************************/

	$rootpath = '../../';
	require_once($rootpath.'lib/clicompatbase.inc.php');

/* begin with some constants */

	$sDateformat = 'Y-m-d H:i:s';

/* end with some constants */

class importCaches
{
	
	function copyFile($url,$dirname){
    @$file = fopen ($url, "r");
    if (!$file) {
        echo"<font color=red>Failed to copy $url!</font><br>";
        return false;
    }else {
        $filename = basename($url);
        $fc = fopen($dirname.$filename, "w");
        while (!feof ($file)) {
           $line = fread ($file, 1028);
           fwrite($fc,$line);
        }
        fclose($fc);
        echo "<font color=blue>File $url saved to PC!</font><br>";
        return true;
    }
	}

	
	function run()
	{
		/* begin db connect */
		db_connect();
		if ($dblink === false)
		{
			echo 'Unable to connect to database';
			exit;
		}
	/* end db connect */

		$xmlfile = $this->loadXML();
		if ($xmlfile == false) 
		{
			return;
		}
		$this->importXML($xmlfile);
		$this->removeXML($xmlfile);
		db_disconnect();
	}

	/* get file from XML interface 
	 * and return path of saved xml
	 * or false on error
	 */
	function loadXML()
	{
		global $opt;
		global $dynbasepath;
		
		switch( $_GET['country'] )
		{
			case 'cz':
				$oc_country = "cz";
				$node_id = 3;
				break;
			case 'uk':
				$oc_country = "org.uk";
				$node_id = 6;
				break;
			default:
				$oc_country = "de";
				$node_id = 1;
		}
		
		if( $_GET['country'] == 'cz' )
		{
			
		}
		
		@mkdir($dynbasepath . 'tmp/importcaches');
		$path = $dynbasepath . 'tmp/importcaches/import.xml';

		$this->removeXML($path);
		
		$sql = "SELECT value FROM sysconfig WHERE name='importcaches_".sql_escape($oc_country)."_lastupdate'";
		$last_updated = mysql_result(mysql_query($sql),0);
		$modifiedsince = strtotime($last_updated);
		
		//$modifiedsince = strtotime(getSysConfig('geokrety_lastupdate', '0'));
		//echo 'dddd=http://geokrety.org/export.php?modifiedsince=' . date('YmdHis', $modifiedsince - 1);
		set_time_limit(300);
		$copy_from = 'http://www.opencaching.'.$oc_country.'/xml/ocxml11.php?modifiedsince=' . date('YmdHis', $modifiedsince - 14400).'&session=0&cache=1&zip=0';
		//$copy_from = "http://www.opencaching.cz/download/zip/ocxml11/442/442-1-2.xml";
		if (!copy($copy_from, $path))
		{
		echo "NIE";
			return false;
		}
		return $path;
	}

	/* remove given file
	 */
	function removeXML($file)
	{
		@unlink($file);
	}

	/* import the given XML file
	 */
	function importXML($file)
	{
		global $opt;

    $xr = new XMLReader();
    if (!$xr->open($file))
    {
      $xr->close();
			return;
    }	
$xr->read();
$xr->read();
    //while ($xr->read() && !($xr->name == 'oc11xml'));

		/*if ($xr->nodeType != XMLReader::ELEMENT)
    {
      echo 'error: First element expected, aborted' . "\n";
      return;
    }*/
    if ($xr->name != 'oc11xml')
    {
      echo 'error: First element not valid, aborted' . "\n";
      return;
    }

		$startupdate = $xr->getAttribute('date');
    if ($startupdate == '')
    {
			
      echo 'error: Date attribute not valid, aborted' . "\n";
      return;
    }

    while ($xr->read() && $xr->name != 'cache') ;

    $nRecordsCount = 0;
    do
    {
      if ($xr->nodeType == XMLReader::ELEMENT)
      {
				$element = $xr->expand();
				switch ($xr->name)
				{
					case 'cache':
						$this->importCache($element);
						break;
					case 'moves':
						$this->importMove($element);
						break;
				}

				$nRecordsCount++;
      }
    }
    while ($xr->next());

    $xr->close();
		// de or cz
		$oc_country = "de";
		$node_id = 1;
		if( $_GET['country'] == 'cz' )
		{
			$oc_country = "cz";
			$node_id = 3;
		}
		sql("UPDATE sysconfig SET value = '".sql_escape($startupdate)."' WHERE name='importcaches_".sql_escape($oc_country)."_lastupdate'");
	}

	function importCache($element)
	{
		global $opt;

		$id = $this->GetNodeAttribute($element, 'id', 'id')+0;
		$cachenode = $this->GetNodeAttribute($element, 'id', 'node')+0;
		
		$userid = $this->GetNodeAttribute($element, 'userid', 'id')+0;
		$userid = $userid + $cachenode*100000000+100000000;
		$useruuid = addslashes($this->GetNodeAttribute($element, 'userid', 'uuid'));
		
		$username = addslashes($this->GetNodeValue($element, 'userid'));
		
		$name = addslashes($this->GetNodeValue($element, 'name'));

		$longitude = ($this->GetNodeValue($element, 'longitude'))+0;
		$latitude = ($this->GetNodeValue($element, 'latitude'))+0;

		$typeid = $this->GetNodeAttribute($element, 'type', 'id')+0;
		
		$statusid = $this->GetNodeAttribute($element, 'status', 'id')+0;
		
		$countryid = addslashes($this->GetNodeAttribute($element, 'country', 'id'));
		
		$sizeid = $this->GetNodeAttribute($element, 'size', 'id')+0;
		
		$desclanguages = addslashes($this->GetNodeValue($element, 'desclanguages'));
		
		$difficulty = $this->GetNodeValue($element, 'difficulty')+0;
		
		$terrain = $this->GetNodeValue($element, 'terrain')+0;

		$waylength = $this->GetNodeAttribute($element, 'rating', 'waylength')+0;
		
		$needtime = $this->GetNodeAttribute($element, 'rating', 'needtime')+0;
		
		$ocwaypoint = addslashes($this->GetNodeAttribute($element, 'waypoints', 'oc'));
		
		$gcwaypoint = addslashes($this->GetNodeAttribute($element, 'waypoints', 'gccom'));
		
		$ncwaypoint = addslashes($this->GetNodeAttribute($element, 'waypoints', 'nccom'));
		
		$datehidden = addslashes($this->GetNodeValue($element, 'datehidden'));
		
		$datecreated = addslashes($this->GetNodeValue($element, 'datecreated'));
		
		$lastmodified = addslashes($this->GetNodeValue($element, 'lastmodified'));
		
		$oc_country = "de";
		$node_id = 1;
		if( $_GET['country'] == 'cz' )
		{
			$oc_country = "cz";
			$node_id = 3;
		}
		
		$sql = "REPLACE INTO foreign_caches (cache_id, user_id, username, name, longitude, latitude, last_modified, date_created, type, status, country, date_hidden, desc_languages, size, difficulty, terrain, uuid, search_time, way_length, wp_gc, wp_nc, wp_oc, default_desclang, node) VALUES ($id, $userid, '$username', '$name', $longitude, $latitude, '$lastmodified', '$datecreated', $typeid, $statusid, '$countryid', '$datehidden', '$desclanguages', $sizeid, $difficulty, $terrain, '$useruuid', $needtime, $waylength, '$gcwaypoint', '$ncwaypoint', '$ocwaypoint', UCASE('".$oc_country."'), $node_id)";
		
		@mysql_query($sql);
	}

	function GetNodeValue(&$domnode, $element)
	{
		$subnode = $domnode->getElementsByTagName($element);
		if ($subnode->length < 1)
			return '';
		else
			return $subnode->item(0)->nodeValue;
	}

	function GetNodeAttribute(&$domnode, $element, $attr)
	{
		$subnode = $domnode->getElementsByTagName($element);
		if ($subnode->length < 1)
			return '';
		else
			return $subnode->item(0)->getAttribute($attr);
	}
}

$importcaches = new importCaches();
$importcaches->run();

?>
