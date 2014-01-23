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
    function getCountryFromNodeId( $node_id )
    {
        switch( $node_id )
        {

            /* Well known node id's - required for synchronization
             * 1 Opencaching Germany (www.opencaching.de)
             * 2 Opencaching Poland (www.opencaching.pl)
             * 3 Opencaching Tschechien (www.opencaching.cz)
             * 4 Local Development
             * 5 Opencaching Entwicklung Deutschland (devel.opencaching.de)
             * 6 OC UK
             * 7 OC SE
             * 8 OC NO
             * 14 Opencaching Netherlands (www.opencaching.nl)
             */

            case 14:
                $oc_country = "nl";
                break;
            case 8:
                $oc_country = "no";
                break;
            case 7:
                $oc_country = "se";
                break;
            case 6:
                $oc_country = "org.uk";
                break;
            case 3:
                $oc_country = "cz";
                break;
            case 2:
                $oc_country = "pl";
                break;
            case 1:
            default:
                $oc_country = "de";
        }
        return $oc_country;
    }

    function copyFile($url,$dirname)
    {
    @$file = fopen ($url, "r");
    if (!$file)
        {
      echo"<font color=red>Failed to copy $url!</font><br />";
      return false;
    }
        else
        {
      $filename = basename($url);
      $fc = fopen($dirname.$filename, "w");
      while (!feof ($file))
            {
        $line = fread ($file, 1028);
        fwrite($fc,$line);
      }
      fclose($fc);
      echo "<font color=blue>File $url saved to PC!</font><br />";
      return true;
    }
    }


    function run($node_id)
    {
        /* begin db connect */
        db_connect();
        if ($dblink === false)
        {
            echo 'Unable to connect to database';
            exit;
        }
        /* end db connect */

        $xmlfile = $this->loadXML($node_id);
        if ($xmlfile == false)
        {
            db_disconnect();
            return false;
        }
        $retValue = $this->importXML($xmlfile, $node_id);

        $this->removeXML($xmlfile);
        db_disconnect();

        return $retValue;
    }

    /* get file from XML interface
     * and return path of saved xml
     * or false on error
     */
    function loadXML( $node_id )
    {
        global $opt;
        global $dynbasepath;

        @mkdir($dynbasepath . 'tmp/importcaches');
        $path = $dynbasepath . 'tmp/importcaches/import.xml';

        $this->removeXML($path);

        $sql = "SELECT updated FROM import_caches_date WHERE node_id = ".sql_escape($node_id);
        $query = mysql_query($sql);

        // get the timestamp of last update for this node_id
        $modifiedsince = ( @mysql_num_rows($query) > 0 ? @mysql_result( $query, 0 ) : 14400 );

        set_time_limit(300);
        $copy_from = 'http://www.opencaching.'.($this->getCountryFromNodeId( $node_id )).'/xml/ocxml11.php?modifiedsince=' . date('YmdHis', $modifiedsince - 14400).'&session=0&cache=1&zip=0';
        //$copy_from = "http://www.opencaching.cz/download/zip/ocxml11/442/442-1-2.xml";

        $fp = fopen( $path, 'wb' );

        $options = array(
        CURLOPT_RETURNTRANSFER => false,     // return web page
        CURLOPT_HEADER         => false,    // don't return headers
                CURLOPT_FILE                     => $fp,
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_USERAGENT      => "opencaching.pl", // who am i
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 300,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
    );

    $ch = curl_init( $copy_from );

    curl_setopt_array( $ch, $options );
    if( curl_exec( $ch ) === false )
        {
            echo "Unable to synchronize with opencaching.".($this->getCountryFromNodeId( $node_id ))."\n";
            echo "ERROR:".curl_error( $ch )."\n";
        }

        curl_close( $ch );
        fclose( $fp );
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
    function importXML($file, $node_id)
    {
        global $opt;

    $xr = new XMLReader();
    if (!$xr->open($file))
    {
      $xr->close();
            return false;
    }
        $xr->read();
        $xr->read();

    if ($xr->name != 'oc11xml')
    {
      echo 'error: First element not valid, aborted' . "\n";
      return false;
    }

        $startupdate = strtotime($xr->getAttribute('date'));
    if ($startupdate == '')
    {

      echo 'error: Date attribute not valid, aborted' . "\n";
      return false;
    }

    while ($xr->read() && $xr->name != 'cache');

    $nRecordsCount = 0;
    do
    {
      if ($xr->nodeType == XMLReader::ELEMENT)
      {
                $element = $xr->expand();
                switch ($xr->name)
                {
                    case 'cache':
                        $this->importCache($element, $node_id);
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

        sql("INSERT INTO import_caches_date (updated, node_id ) VALUES ('".sql_escape($startupdate)."','".sql_escape($node_id)."')
            ON DUPLICATE KEY UPDATE updated = '".sql_escape($startupdate)."'");
        return true;
    }

    function importCache($element, $node_id)
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


        $sql = "REPLACE INTO foreign_caches (cache_id, user_id, username, name, longitude, latitude, last_modified, date_created, type, status, country, date_hidden, desc_languages, size, difficulty, terrain, uuid, search_time, way_length, wp_gc, wp_nc, wp_oc, default_desclang, node) VALUES ($id, $userid, '$username', '$name', $longitude, $latitude, '$lastmodified', '$datecreated', $typeid, $statusid, '$countryid', '$datehidden', '$desclanguages', $sizeid, $difficulty, $terrain, '$useruuid', $needtime, $waylength, '$gcwaypoint', '$ncwaypoint', '$ocwaypoint', UCASE('".($this->getCountryFromNodeId( $node_id ))."'), $node_id)";

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

//$valid_ocnodes = array(1,/*2,3,6,*/7,8); // do not import caches from local oc server

// Temporarily disable import from NO due to unknown problems on the other side.
// This leaves import from DE only.
$valid_ocnodes = array(1); // do not import caches from local oc server

// iterate through all valid Opencaching nodes
foreach( $valid_ocnodes as $valid_ocnode )
{

    if( $importcaches->run( $valid_ocnode ) == false )
    {
        echo "Caches from opencaching.".$importcaches->getCountryFromNodeId($valid_ocnode)." update FAILED.<br/>\n";
    }
    else
    {
        //echo "Caches from opencaching.".$importcaches->getCountryFromNodeId($valid_ocnode)." updated SUCCESSFULLY.<br/>\n";
    }
}

?>
