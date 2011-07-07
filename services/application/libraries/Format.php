<?php
/**
 * Format class
 *
 * Help convert between various formats such as XML, JSON, CSV, etc.
 *
 * @author		Phil Sturgeon
 * @license		http://philsturgeon.co.uk/code/dbad-license
 */
class Format {

	// Array to convert
	protected $_data = array();

	// View filename
	protected $_from_type = null;

	/**
	 * Returns an instance of the Format object.
	 *
	 *     echo $this->format->factory(array('foo' => 'bar'))->to_xml();
	 *
	 * @param   mixed  general date to be converted
	 * @param   string  data format the file was provided in
	 * @return  Factory
	 */
	public function factory($data, $from_type = null)
	{
		// Stupid stuff to emulate the "new static()" stuff in this libraries PHP 5.3 equivilent
		$class = __CLASS__;
		return new $class($data, $from_type);
	}

	/**
	 * Do not use this directly, call factory()
	 */
	public function __construct($data = null, $from_type = null)
	{
		get_instance()->load->helper('inflector');
		
		// If the provided data is already formatted we should probably convert it to an array
		if ($from_type !== null)
		{
			if (method_exists($this, '_from_' . $from_type))
			{
				$data = call_user_func(array($this, '_from_' . $from_type), $data);
			}

			else
			{
				throw new Exception('Format class does not support conversion from "' . $from_type . '".');
			}
		}

		$this->_data = $data;
	}

	// FORMATING OUTPUT ---------------------------------------------------------

	public function to_array($data = null)
	{
		// If not just null, but nopthing is provided
		if ($data === null and ! func_num_args())
		{
			$data = $this->_data;
		}

		$array = array();

		foreach ((array) $data as $key => $value)
		{
			if (is_object($value) or is_array($value))
			{
				$array[$key] = $this->to_array($value);
			}

			else
			{
				$array[$key] = $value;
			}
		}

		return $array;
	}
	
	// Format GPX for output
	public function to_gpx( $data= null)
	{
		$data = $this->_data;
		$data = (array)$data;
		
	if(!array_key_exists("error",$data)){
	
	// set $wpchildren if exist additonaly wp for cache. It is for GSAK
	$wpchildren="";

	$gpx = 
'<?xml version="1.0" encoding="utf-8"?>
<gpx xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" version="1.0" creator="OpenCachingPL" xsi:schemaLocation="http://www.topografix.com/GPX/1/0 http://www.topografix.com/GPX/1/0/gpx.xsd http://www.groundspeak.com/cache/1/0/1 http://www.groundspeak.com/cache/1/0/1/cache.xsd http://www.gsak.net/xmlv1/5 http://www.gsak.net/xmlv1/5/gsak.xsd" xmlns="http://www.topografix.com/GPX/1/0"> 
	<name>Generated from Opencaching.pl</name>
	<desc>Generated from Opencaching.pl '.$wpchildren.'</desc>
	<author>OpenCaching.PL</author>
	<email>ocpl@opencaching.pl</email>
	<url>http://opencaching.pl</url>
	<urlname>Opencaching.pl - Geocaching w Polsce</urlname>
	<time>'.date("Y-m-d\TH:i:s\Z", time()).'</time>
	<keywords>opencaching, cache, geocache</keywords>
';

	// known by gpx
	$gpxType[1] = 'Unknown Cache'; 		//OC: Other;
	$gpxType[2] = 'Traditional Cache'; 	//OC: Traditional
	$gpxType[3] = 'Multi-cache'; 		//OC: Multi
	$gpxType[4] = 'Virtual Cache';		//OC: Virtual
	$gpxType[5] = 'Webcam Cache';		//OC: Webcam
	$gpxType[6] = 'Event Cache';		//OC: Event
	$gpxType[7] = 'Unknown Cache';		//OC: Quiz
	$gpxType[8] = 'Unknown Cache';		//OC: Moving
	$gpxType[9] = 'Unknown Cache';		//OC: Own Cache
	$gpxType[10] = 'Unknown Cache';		//OC: Own cache

	$gpxContainer[0] = 'Unknown';	//OC: Other
	$gpxContainer['Micro'] = 'Micro';		//OC: Micro
	$gpxContainer['Small'] = 'Small';		//OC: Small
	$gpxContainer['Regular'] = 'Regular';	//OC: Regular
	$gpxContainer['Large'] = 'Large';		//OC: Large
	$gpxContainer['Very large'] = 'Large';		//OC: Large
	$gpxContainer['No container'] = 'Other';	//OC: Virtual

	$gpxAvailable[0] = 'False';	//OC: Unavailable
	$gpxAvailable[1] = 'True';	//OC: Available
	$gpxAvailable[2] = 'False';	//OC: Unavailable
	$gpxAvailable[3] = 'False';	//OC: Archived
	$gpxAvailable[4] = 'False';	//OC: Hidded to approvers check
	$gpxAvailable[5] = 'False';	//OC: Blocked by OC Team
	
	$gpxArchived[0] = 'False';	//OC: Unavailable
	$gpxArchived[1] = 'False';	//OC: Available
	$gpxArchived[2] = 'False';	//OC: Unavailable
	$gpxArchived[3] = 'True';	//OC: Archived
	$gpxArchived[4] = 'False';	//OC: Hidden by approvers to check
	$gpxArchived[5] = 'False';	//OC: Blocked by OC Team

	$gpxLogType[0] = 'Write note';			//OC: Other
	$gpxLogType[1] = 'Found it'; 			//OC: Found
	$gpxLogType[2] = 'Didn\'t find it';		//OC: Not Found
	$gpxLogType[3] = 'Write note'; 			//OC: Note
	$gpxLogType[4] = 'Write note'; 			//OC: Note
	$gpxLogType[5] = 'Needs Maintenance'; 			//OC: Note
	$gpxLogType[6] = 'Needs Archived';			//OC: Other
	$gpxLogType[7] = 'Attended'; 			//OC: Found
	$gpxLogType[8] = 'Will Attend';		//OC: Not Found
	$gpxLogType[9] = 'Archive'; 			//OC: Note
	$gpxLogType[10] = 'Enable Listing'; 			//OC: Note
	$gpxLogType[11] = 'Temporarily Disable Listing'; 			//OC: Note
	$gpxLogType[12] = 'Post Reviewer Note'; 			//OC: Note


	if(isset($data["0"]["cache_id"])) {


		foreach ($data as $row)
		{
	
$gpx .= '<wpt lat="'.$row["latitude"].'" lon="'.$row["longitude"].'">
		<time>'.date("Y-m-d\TH:i:s\Z", strtotime($row["date_hidden"])).'</time>
		<name>'.$row["wp_oc"].'</name>
		<desc>'.cleanup_text($row["cache_name"]).' by '.cleanup_text($row["owner"]).', '.$row["type_name"].' (D:'.$row["difficulty"].'/ T:'.$row["terrain"].')</desc>
		<url>http://opencaching.pl/viewcache.php?cacheid='.$row["cache_id"].'</url>
		<urlname>'.$row["cache_name"].' by '.$row["owner"].', '.$row["type_name"].'</urlname>
		<sym>Geocache</sym>
		<type>Geocache|'.$gpxType[$row["type"]].'</type>
		<groundspeak:cache id="'.$row["cache_id"].'" available="'.$gpxAvailable[$row["status"]].'" archived="'.$gpxArchived[$row["status"]].'" xmlns:groundspeak="http://www.groundspeak.com/cache/1/0/1">
			<groundspeak:name>'.cleanup_text($row["cache_name"]).'</groundspeak:name>
			<groundspeak:placed_by>'.cleanup_text($row["owner"]).'</groundspeak:placed_by>
			<groundspeak:owner id="'.$row["owner_id"].'">'.$row["owner"].'</groundspeak:owner>
			<groundspeak:type>'.$gpxType[$row["type"]].'</groundspeak:type>
			<groundspeak:container>'.$gpxContainer[$row["size_name"]].'</groundspeak:container>
			<groundspeak:difficulty>'.str_replace('.0', '',$row["difficulty"]).'</groundspeak:difficulty>
			<groundspeak:terrain>'.str_replace('.0', '',$row["terrain"]).'</groundspeak:terrain>
			<groundspeak:country>'.$row["country"].'</groundspeak:country>
			<groundspeak:state>'.$row["region"].'</groundspeak:state>
			<groundspeak:short_description html="False">'.cleanup_text($row["short_desc"]).'</groundspeak:short_description>
			<groundspeak:long_description html="True">'.$row["desc"];
			

	    if(is_array($row["pictures"]))
	    {
	    $gpx.='&lt;br&gt; &lt;b&gt;Obrazki:&lt;/b&gt;&lt;br&gt;
	    ';
	    foreach ($row["pictures"] as $prow)
	    {
			if ($prow["spoiler"]==1)
			{ $thumb_url="http://opencaching.pl/tpl/stdstyle/images/thumb/thumbspoiler.gif";} 
			else { $thumb_url=$prow["thumb_url"];}

		    $gpx .='
		    &lt;a href="'.$prow['url'].'"&gt;&lt;img src="'.$thumb_url.'"&gt;&lt;/a&gt;&lt;br&gt;'.$prow['title'].'&lt;br&gt;
		    ';
	    
	    }
	}
			
		$gpx.='&lt;br&gt;&lt;br&gt;	
			&lt;b&gt;Ocena skrzynki:&lt;/b&gt; '.$row["score"].'&lt;br&gt;
			&lt;b&gt;Liczba rekomendacji:&lt;/b&gt; '.$row["recommend"].'&lt;br&gt;	
			</groundspeak:long_description>
			<groundspeak:encoded_hints>'.$row["hint"].'</groundspeak:encoded_hints>
	';
	    $gpx.='<oc:opencaching xmlns:oc="http://www.opencaching.pl/doc/opengpx/">
		    ';
	    $gpx.='<oc:cache>
			<oc:type_id>'.$row["type"].'</oc:type_id>
			<oc:type>'.$row["type_name"].'</oc:type>
			<oc:status_id>'.$row["status"].'</oc:status_id>
			<oc:status>'.$row["status_name"].'</oc:status>
			<oc:search_time>'.$row["search_time"].'</oc:search_time>
			<oc:way_length>'.$row["way_length"].'</oc:way_length>
		    </oc:cache>
		    <oc:ratings>
			<oc:score>'.$row["score"].'</oc:score>
			<oc:recommend>'.$row["recommend"].'</oc:recommend>
		    </oc:ratings>
		    ';
	    if(is_array($row["attributes"]))
	    {
	    $gpx.='<oc:attributes>
	    ';
	    foreach ($row["attributes"] as $arow)
	    {
		
		    $gpx .='<oc:attribute>
			<oc:id>'.$arow["id"].'</oc:id>
			<oc:name>'.$arow["name"].'</oc:name>
			</oc:attribute>
			';
	    
	    }
	$gpx.='</oc:attributes>
	';
	}
	$gpx.='</oc:opencaching>
	';


	    if(is_array($row["logs"]))
	    {

	$gpx.='	    <groundspeak:logs>
	';
	
	    foreach ($row["logs"] as $lrow)
	    {
	$gpx .= '
		<groundspeak:log id="'.$lrow["id"].'">
        	<groundspeak:date>'.date("Y-m-d\TH:i:s\Z", strtotime($lrow["date"])).'</groundspeak:date>
		<groundspeak:type>'.$lrow["type_name"].'</groundspeak:type>
		<groundspeak:finder id="'.$lrow["finder_id"].'">'.$lrow["finder"].'</groundspeak:finder>
		<groundspeak:text encoded="False">'.$lrow["text"].'</groundspeak:text>
		</groundspeak:log>
		';
	    }
	
	$gpx.='
		    </groundspeak:logs>
	';
	}
	$gpx.='    </groundspeak:cache>
	</wpt>
	'; 
	} // end if  cache_id exist in array
	
		
		} // end if array not empty
				
	// only logs
	if(isset($data["0"]["log"]["finder"]))
	
	{
		$gpx.='	    <groundspeak:logs xmlns:groundspeak="http://www.groundspeak.com/cache/1/0/1">
	';

	    	foreach ($data as $lrow)
		{	
	$gpx .= '
		<groundspeak:log id="'.$lrow["log"]["id"].'">
        	<groundspeak:date>'.date("Y-m-d\TH:i:s\Z", strtotime($lrow["log"]["date"])).'</groundspeak:date>
		<groundspeak:type>'.$lrow["log"]["type_name"].'</groundspeak:type>
		<groundspeak:finder id="'.$lrow["log"]["finder_id"].'">'.$lrow["log"]["finder"].'</groundspeak:finder>
		<groundspeak:text encoded="False">'.$lrow["log"]["text"].'</groundspeak:text>
		</groundspeak:log>
		';
	    }
	
	$gpx.='
		    </groundspeak:logs>
	';
    
	    
	} // end of only logs

	$gpx .= '</gpx>';

	} else {$gpx='<?xml version="1.0" encoding="utf-8"?>
	<gpx><error>'.$data["error"].'</error></gpx>';}
		return $gpx;

	}


	// Format XML for output
	public function to_xml($data = null, $structure = null, $basenode = 'xml')
	{
		if ($data === null and ! func_num_args())
		{
			$data = $this->_data;
		}

		// turn off compatibility mode as simple xml throws a wobbly if you don't.
		if (ini_get('zend.ze1_compatibility_mode') == 1)
		{
			ini_set('zend.ze1_compatibility_mode', 0);
		}

		if ($structure === null)
		{
			$structure = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$basenode />");
		}

		// Force it to be something useful
		if ( ! is_array($data) AND ! is_object($data))
		{
			$data = (array) $data;
		}

		foreach ($data as $key => $value)
		{
			// no numeric keys in our xml please!
			if (is_numeric($key))
            {
                // make string key...           
                $key = (singular($basenode) != $basenode) ? singular($basenode) : 'item';
            }

			// replace anything not alpha numeric
			$key = preg_replace('/[^a-z_\-0-9]/i', '', $key);

            // if there is another array found recrusively call this function
            if (is_array($value) || is_object($value))
            {
                $node = $structure->addChild($key);

                // recrusive call.
                $this->to_xml($value, $node, $key);
            }

            else
            {
                // add single node.
				$value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, "UTF-8");

				$structure->addChild($key, $value);
			}
		}

		return $structure->asXML();
	}

	// Format HTML for output
	public function to_html()
	{
		$data = $this->_data;
		
		// Multi-dimentional array
		if (isset($data[0]))
		{
			$headings = array_keys($data[0]);
		}

		// Single array
		else
		{
			$headings = array_keys($data);
			$data = array($data);
		}

		$ci = get_instance();
		$ci->load->library('table');

		$ci->table->set_heading($headings);

		foreach ($data as &$row)
		{
			$ci->table->add_row($row);
		}

		return $ci->table->generate();
	}

	// Format HTML for output
	public function to_csv()
	{
		$data = $this->_data;

		// Multi-dimentional array
		if (isset($data[0]))
		{
			$headings = array_keys($data[0]);
		}

		// Single array
		else
		{
			$headings = array_keys($data);
			$data = array($data);
		}

		$output = implode(',', $headings).PHP_EOL;
		foreach ($data as &$row)
		{
			$output .= '"'.implode('","', $row).'"'.PHP_EOL;
		}

		return $output;
	}

	// Encode as JSON
	public function to_json()
	{
		return json_encode($this->_data);
	}

	// Encode as Serialized array
	public function to_serialized()
	{
		return serialize($this->_data);
	}
	
	// Output as a string representing the PHP structure
	public function to_php()
	{
	    return var_export($this->_data, TRUE);
	}

	// Format XML for output
	protected function _from_xml($string)
	{
		return $string ? (array) simplexml_load_string($string, 'SimpleXMLElement', LIBXML_NOCDATA) : array();
	}

	// Format HTML for output
	// This function is DODGY! Not perfect CSV support but works with my REST_Controller
	protected function _from_csv($string)
	{
		$data = array();

		// Splits
		$rows = explode("\n", trim($string));
		$headings = explode(',', array_shift($rows));
		foreach ($rows as $row)
		{
			// The substr removes " from start and end
			$data_fields = explode('","', trim(substr($row, 1, -1)));

			if (count($data_fields) == count($headings))
			{
				$data[] = array_combine($headings, $data_fields);
			}
		}

		return $data;
	}

	// Encode as JSON
	private function _from_json($string)
	{
		return json_decode(trim($string));
	}

	// Encode as Serialized array
	private function _from_serialize($string)
	{
		return unserialize(trim($string));
	}

}

/* End of file format.php */
