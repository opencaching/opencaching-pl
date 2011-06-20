<?php defined('BASEPATH') OR exit('No direct script access allowed');

 

function getSqlDistanceFormula($latFrom,$lonFrom,  $maxDistance, $distanceMultiplier=1, $lonField='longitude', $latField='latitude', $tableName = 'caches')
{
	$lonFrom = $lonFrom + 0;
	$latFrom = $latFrom + 0;
	$maxDistance = $maxDistance + 0;
	$distanceMultiplier = $distanceMultiplier + 0;

	if (!mb_ereg_match('^[a-zA-Z][a-zA-Z0-9_]{0,59}$', $lonField))
		die('Fatal Error: invalid lonField');
	if (!mb_ereg_match('^[a-zA-Z][a-zA-Z0-9_]{0,59}$', $latField))
		die('Fatal Error: invalid latField');
	if (!mb_ereg_match('^[a-zA-Z][a-zA-Z0-9_]{0,59}$', $tableName))
		die('Fatal Error: invalid tableName');

	$b1_rad = sprintf('%01.5f', (90 - $latFrom) * 3.14159 / 180);
	$l1_deg = sprintf('%01.5f', $lonFrom);

	$lonField = '`' . $tableName . '`.`' . $lonField . '`';
	$latField = '`' . $tableName . '`.`' . $latField . '`';

	$r = 6370 * $distanceMultiplier;

	$retval = 'acos(cos(' . $b1_rad . ') * cos((90-' . $latField . ') * 3.14159 / 180) + sin(' . $b1_rad . ') * sin((90-' . $latField . ') * 3.14159 / 180) * cos((' . $l1_deg . '-' . $lonField . ') * 3.14159 / 180)) * ' . $r;
//        $ret=sprintf("%01.1f",$retval);
	return $retval;
}

       function cleanup_text($str)
        {
//			$str= tidy_html_description($str);
//          $str = PLConvert('UTF-8','POLSKAWY',$str);


//		return html2text($str);
	    $str=preg_replace ('/<[^>]*>/', '',$str);
	    
//          $str = strip_tags($str, "<p><br /><li>");
          // <p> -> nic
          // </p>, <br /> -> nowa linia
          $from[] = '<p>'; $to[] = '';
          $from[] = '<p *>'; $to[] = '';
          $from[] = '</p>'; $to[] = "\n";
          $from[] = '<br />'; $to[] = "\n";
          $from[] = '<br>'; $to[] = "\n";
	  $from[] = '<br>'; $to[] = "\n";
            
          $from[] = '<li>'; $to[] = " - ";
          $from[] = '</li>'; $to[] = "\n";
          
          $from[] = '&oacute;'; $to[] = 'o';
          $from[] = '&quot;'; $to[] = '"';
          $from[] = '&[^;]*;'; $to[] = '';
          
          $from[] = '&'; $to[] = '&amp;';
          $from[] = '<'; $to[] = '&lt;';
          $from[] = '>'; $to[] = '&gt;';
          $from[] = ']]>'; $to[] = ']] >';
	   $from[] = ''; $to[] = '';
              
          for ($i = 0; $i < count($from); $i++)
            $str = str_replace($from[$i], $to[$i], $str);
                                 
          return filterevilchars($str);
        }
        
	
        function filterevilchars($str)
	{
		return str_replace('[\\x00-\\x09|\\x0B-\\x0C|\\x0E-\\x1F]', '', $str);
	}

	function score2ratingnum($score)
	{
		if($score >= 2.2)
			return 4;
		else if($score >= 1.4)
			return 3;
		else if($score >= 0.1)
			return 2;
		else if($score >= -1.0)
			return 1;
		else
			return 0;
	}

	function score2rating($score)
	{
	// rating conversion array
	$ratingDesc = array(
			"s≈Çaba",
			"poni≈ºej przeciƒôtnej",
			"normalna",
			"dobra",
			"znakomita",
			);


		return $ratingDesc[score2ratingnum($score)];
	}

	function new2oldscore($score)
	{
		if($score == 4)
			return 3.0;
		else if($score == 3)
			return 1.7;
		else if($score == 2)
			return 0.7;
		else if($score == 1)
			return 0.5;
		else
			return -2.0;
	}


function PlConvert($source,$dest,$tekst)
{
    $source=strtoupper($source);
    $dest=strtoupper($dest);
    if($source==$dest) return $tekst;

    $chars['POLSKAWY']    =array('a','c','e','l','n','o','s','z','z','A','C','E','L','N','O','S','Z','Z');
    $chars['ISO-8859-2']  =array("\xB1","\xE6","\xEA","\xB3","\xF1","\xF3","\xB6","\xBC","\xBF","\xA1","\xC6","\xCA","\xA3","\xD1","\xD3","\xA6","\xAC","\xAF");
    $chars['WINDOWS-1250']=array("\xB9","\xE6","\xEA","\xB3","\xF1","\xF3","\x9C","\x9F","\xBF","\xA5","\xC6","\xCA","\xA3","\xD1","\xD3","\x8C","\x8F","\xAF");
    $chars['UTF-8']       =array('π','Ê','Í','≥','Ò','"\xF3','ú','ü','ø','•','∆',' ','£','—','”','å','è','Ø');
    $chars['ENTITIES']    =array('π','Ê','Í','≥','Ò','Û','ú','ü','ø','•','∆',' ','£','—','”','å','è','Ø');

    if(!isset($chars[$source])) return false;
    if(!isset($chars[$dest])) return false;
    
	$tekst = str_replace('a', 'a', $tekst);
	$tekst = str_replace('È', 'e', $tekst);

    return str_replace($chars[$source],$chars[$dest],$tekst);
}
			