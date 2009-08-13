<?php
function getMapType($value)
{
	switch( $value ) 
	{
		case 0:
			return "G_NORMAL_MAP";
		case 1:
			return "G_SATELLITE_MAP";
		case 2:
			return "G_HYBRID_MAP";
		case 3:
			return "G_PHYSICAL_MAP";
		default:
			return "G_NORMAL_MAP";
	}
}

function onTheList($theArray, $item)
{
	for( $i=0;$i<count($theArray);$i++)
	{
		if( $theArray[$i] == $item )
			return $i;
	}
	return -1;
}

function getDBFilter($user_id)
{
	$filter = array("h_u"=>1,
									"h_t"=>1,
									"h_m"=>1,
									"h_v"=>1,
									"h_w"=>1,
									"h_e"=>1,
									"h_q"=>1,
									"h_o"=>1,
									"h_ignored"=>0,
									"h_own"=>1,
									"h_found"=>1,
									"h_noattempt"=>1,
									"h_nogeokret"=>1,
									"signes"=>1,
									"h_avail"=>0,
									"h_temp_unavail"=>1,
									"map_type"=>3,
									"h_arch"=>0,
									"be_ftf"=>0,
									"h_pl"=>1,
									"h_de"=>1,
									"min_score"=>0,
									"max_score"=>6,
									"h_noscore"=>1
									); // default filter
	$query = mysql_query("SELECT * from map_settings WHERE `user_id`=$user_id");
	while($row = mysql_fetch_assoc($query))
	{ 
		$filter["h_u"] = $row['unknown'];
		$filter["h_t"] = $row['traditional'];
		$filter["h_m"] = $row['multicache'];
		$filter["h_v"] = $row['virtual'];
		$filter["h_w"] = $row['webcam'];
		$filter["h_e"] = $row['event'];
		$filter["h_q"] = $row['quiz'];
		$filter["h_o"] = $row['mobile'];
		$filter["h_ignored"] = $row['ignored'];
		$filter["h_own"] = $row['own'];
		$filter["h_found"] = $row['found'];
		$filter["h_noattempt"] = $row['notyetfound'];
		$filter["h_nogeokret"] = $row['geokret'];
		$filter["signes"] = $row['showsign'];
		$filter["h_avail"] = $row['active'];
		$filter["h_temp_unavail"] = $row['notactive'];
		$filter["map_type"] = $row['maptype'];
		$filter["h_arch"] = $row['archived'];
		$filter["be_ftf"] = $row['be_ftf'];
		$filter["h_de"] = $row['de'];
		$filter["h_pl"] = $row['pl'];
		$filter["min_score"] = $row['min_score'];
		$filter["max_score"] = $row['max_score'];
		$filter["h_noscore"] = $row['noscore'];
	}
	
	return $filter;
}

require_once('./lib/common.inc.php');
$tplname = 'cachemap3';
tpl_set_var('bodyMod', ' onload="load()" onunload="GUnload()"');
//tpl_set_var('BodyMod', ' onload="load()" onunload="GUnload()"');
global $usr;
global $get_userid;
global $filter;
global $caches_list;
global $language;
global $lang;

$user_id = '';

$get_userid = intval($_REQUEST['userid']);
//user logged in?
if ($usr == false)
{
	$target = urlencode(substr($_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'], 1));
	header('Location: login.php?target='.$target);
}
else
{
	session_start();

	tpl_set_var('score', $language[$lang]['score']);
	tpl_set_var('recommendations', $language[$lang]['recommendations']);
	tpl_set_var('created_by', $language[$lang]['created_by']);
	tpl_set_var('found', $language[$lang]['found']);
	tpl_set_var('not_found', $language[$lang]['not_found']);
	tpl_set_var('add_to', $language[$lang]['add_to']);
	tpl_set_var('remove_from', $language[$lang]['remove_from']);
	tpl_set_var('scored', $language[$lang]['scored']);
	tpl_set_var('to_print_list', $language[$lang]['to_print_list']);
	tpl_set_var('active_caches_in_area', $language[$lang]['active_caches_in_area']);
	tpl_set_var('shown', $language[$lang]['shown']);
	tpl_set_var('next', $language[$lang]['next']);
	tpl_set_var('printer_friendly', $language[$lang]['printer_friendly']);
	tpl_set_var('user_map', $language[$lang]['user_map']);
	tpl_set_var('current_zoom', $language[$lang]['current_zoom']);
	tpl_set_var('colors', $language[$lang]['colors']);
	tpl_set_var('yellow', $language[$lang]['yellow']);
	tpl_set_var('green', $language[$lang]['green']);
	tpl_set_var('gray', $language[$lang]['gray']);
	tpl_set_var('red', $language[$lang]['red']);
	tpl_set_var('last_10_days', $language[$lang]['last_10_days']);
	tpl_set_var('own', $language[$lang]['own']);
	tpl_set_var('rest', $language[$lang]['rest']);
	tpl_set_var('max_caches_per_page', $language[$lang]['max_caches_per_page']);
	tpl_set_var('sort_by', $language[$lang]['sort_by']);
	tpl_set_var('by_name', $language[$lang]['by_name']);
	tpl_set_var('by_date', $language[$lang]['by_date']);
	tpl_set_var('hide_caches_type', $language[$lang]['hide_caches_type']);
	tpl_set_var('unknown_type', $language[$lang]['unknown_type']);
	tpl_set_var('traditional', $language[$lang]['traditional']);
	tpl_set_var('multicache', $language[$lang]['multicache']);
	tpl_set_var('virtual', $language[$lang]['virtual']);
	tpl_set_var('webcam', $language[$lang]['webcam']);
	tpl_set_var('event', $language[$lang]['event']);
	tpl_set_var('quiz', $language[$lang]['quiz']);
	tpl_set_var('moving', $language[$lang]['moving']);
	tpl_set_var('hide_caches', $language[$lang]['hide_caches']);
	tpl_set_var('ignored', $language[$lang]['ignored']);
	tpl_set_var('not_yet_found', $language[$lang]['not_yet_found']);
	tpl_set_var('temp_unavailables', $language[$lang]['temp_unavailables']);
	tpl_set_var('ready_to_find', $language[$lang]['ready_to_find']);
	tpl_set_var('archived', $language[$lang]['archived_plural']);
	tpl_set_var('other_options', $language[$lang]['other_options']);
	tpl_set_var('show_signes', $language[$lang]['show_signes']);
	tpl_set_var('filter', $language[$lang]['filter']);
	tpl_set_var('without_geokret', $language[$lang]['without_geokret']);
	tpl_set_var('founds', $language[$lang]['founds']);
	tpl_set_var('be_ftf_label', $language[$lang]['be_ftf_label']);
	tpl_set_var('attended', $language[$lang]['attendends']);
	tpl_set_var('will_attend', $language[$lang]['will_attend']);
	tpl_set_var('h_pl_label', $language[$lang]['h_pl_label']);
	tpl_set_var('h_de_label', $language[$lang]['h_de_label']);
	tpl_set_var('from', $language[$lang]['from']);
	tpl_set_var('to', $language[$lang]['to']);
	tpl_set_var('score_label', $language[$lang]['score']);
	tpl_set_var('show_noscore', $language[$lang]['with_hidden_score']);
	
	tpl_set_var('sc', intval($_GET['sc']));
	
	if( $get_userid == '')
		$user_id = $usr['userid'];
	else 
		$user_id = $get_userid;
		
	tpl_set_var('userid', $user_id);

	$rs = mysql_query("SELECT `latitude`, `longitude`, `username` FROM `user` WHERE `user_id`='$user_id'");
	$record = mysql_fetch_array($rs);
	if( ($_REQUEST['lat'] != "" && $_REQUEST['lon'] != "") && ($_REQUEST['lat'] != 0 && $_REQUEST['lon'] != 0))
	{
		$coordsXY=$_REQUEST['lat'].",".$_REQUEST['lon'];
		$coordsX=$_REQUEST['lat'];
		if( $_REQUEST['inputZoom'] != "" )
			tpl_set_var('zoom', $_REQUEST['inputZoom']);
		else
			tpl_set_var('zoom', 11);
	}
	else
	{
		$coordsXY="$record[latitude],$record[longitude]";
		$coordsX="$record[latitude]";
		if ($coordsX=="" || $coordsX==0) 
		{
			$coordsXY="52.5,19.2";
			tpl_set_var('zoom', 6);
		}
		else
			tpl_set_var('zoom', 11);
	}
	
	if( isset($_REQUEST['print_list']) && $_REQUEST['print_list'] == 'y')
	{
		// add cache to print (do not duplicate items)
		if( count($_SESSION['print_list']) == 0 )
			$_SESSION['print_list'] = array();
		if( onTheList($_SESSION['print_list'], $_REQUEST['cacheid']) == -1 )
			array_push($_SESSION['print_list'],$_REQUEST['cacheid']);
	}
	if( isset($_REQUEST['print_list']) && $_REQUEST['print_list'] == 'n')
	{
		// remove cache from print list
		while( onTheList($_SESSION['print_list'], $_REQUEST['cacheid']) != -1 )
			unset($_SESSION['print_list'][onTheList($_SESSION['print_list'], $_REQUEST['cacheid'])]);
		$_SESSION['print_list'] = array_values($_SESSION['print_list']);
	}
		
	tpl_set_var('coords', $coordsXY);
	tpl_set_var('username', $record[username]);
	tpl_set_var('map_width', isset($_GET['print'])?($x_print."px"):("99%"));
	tpl_set_var('map_height', isset($_GET['print'])?$y_print:("512")."px"); 
	
	$filter = getDBFilter($usr['userid']);
		tpl_set_var("min_sel0", "");
		tpl_set_var("min_sel1", "");
		tpl_set_var("min_sel2", "");
		tpl_set_var("min_sel3", "");
		tpl_set_var("min_sel4", "");
		tpl_set_var("min_sel5", "");
		tpl_set_var("min_sel6", "");
		tpl_set_var("max_sel0", "");
		tpl_set_var("max_sel1", "");
		tpl_set_var("max_sel2", "");
		tpl_set_var("max_sel3", "");
		tpl_set_var("max_sel4", "");
		tpl_set_var("max_sel5", "");
		tpl_set_var("max_sel6", "");
	foreach($filter as $key=>$value)
	{
		$value = intval($value);
		if( $key == "min_score" || $key == "max_score")
		{
			if( $key == "min_score" )
				$minmax = "min";
			else
				$minmax = "max";
			
			tpl_set_var($minmax."_sel".$value, 'selected="selected"');
			tpl_set_var($key, $value);
			continue;
		}
		
		if( !($key == "h_avail" || $key == "h_temp_unavail" || $key == "h_pl" || $key == "h_de" || $key == "be_ftf" || $key == "map_type" || $key == "signes" || $key == "h_noscore"))
		{
			// workaround for reversed values
			$value = 1-$value;
		}
		
		if( $value )
			$chk = ' checked="checked"';
		else
			$chk = "";
		//echo "k=".$key.":".$chk."(".($value).")<br>";
		tpl_set_var($key."_checked", $chk);
		if( $key == "map_type" )
			tpl_set_var($key, getMapType($value));
		else
			tpl_set_var($key, $value?"true":"");
	
	}
	
	foreach($filter as $key)
	/*if( isset( $_POST['submit'] ) )
	{
			$makeFilterResult = makeDBFilter();
			setDBFilter($usr['userid'],$makeFilterResult);
			$filter = $makeFilterResult;
	}*/

	/*SET YOUR MAP CODE HERE*/
	tpl_set_var('cachemap_header', '<script src="http://maps.google.com/maps?file=api&amp;v=2.99&amp;key='.$googlemap_key.'" type="text/javascript"></script>
	<script language="JavaScript1.2" type="text/javascript">
	<!-- 
		function saveMapType()
		{
			var ajaxRequest;  // The variable that makes Ajax possible!
			try{
				// Opera 8.0+, Firefox, Safari
				ajaxRequest = new XMLHttpRequest();
			} catch (e){
				// Internet Explorer Browsers
				try{
					ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
				} catch (e) {
					try{
						ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
					} catch (e){
						// Something went wrong
						alert("Wystąpił błąd podczas zapisywania typu mapy.");
						return false;
					}
				}
			}
			// Create a function that will receive data sent from the server
			ajaxRequest.onreadystatechange = function(){
				if(ajaxRequest.readyState == 4){
					//document.myForm.time.value = ajaxRequest.responseText;
				}
			}

			var mapid;
			switch (map.getCurrentMapType()) {
				case G_NORMAL_MAP:
					mapid = "0";
				break;
				case G_SATELLITE_MAP:
					mapid = "1";
				break;
				case G_HYBRID_MAP:
					mapid = "2";
				break;
				case G_PHYSICAL_MAP:
					mapid = "3";
				break;
				default:
					mapid = "0";
			}
			var queryString = "?maptype=" + mapid+"&h_u="+document.getElementById(\'h_u\').checked+"&h_t="+document.getElementById(\'h_t\').checked+"&h_m="+document.getElementById(\'h_m\').checked+"&h_v="+document.getElementById(\'h_v\').checked+"&h_w="+document.getElementById(\'h_w\').checked+"&h_e="+document.getElementById(\'h_e\').checked+"&h_q="+document.getElementById(\'h_q\').checked+"&h_o="+document.getElementById(\'h_o\').checked+"&h_ignored="+document.getElementById(\'h_ignored\').checked+"&h_own="+document.getElementById(\'h_own\').checked+"&h_found="+document.getElementById(\'h_found\').checked+"&h_noattempt="+document.getElementById(\'h_noattempt\').checked+"&h_nogeokret="+document.getElementById(\'h_nogeokret\').checked+"&h_avail="+document.getElementById(\'h_avail\').checked+"&h_temp_unavail="+document.getElementById(\'h_temp_unavail\').checked+"&h_arch="+document.getElementById(\'h_arch\').checked+"&signes="+document.getElementById(\'signes\').checked+"&be_ftf="+document.getElementById(\'be_ftf\').checked+"&h_pl="+document.getElementById(\'h_pl\').checked+"&h_de="+document.getElementById(\'h_de\').checked+"&min_score="+document.getElementById(\'min_score\').value+"&max_score="+document.getElementById(\'max_score\').value+"&h_noscore="+document.getElementById(\'h_noscore\').checked;
			
			ajaxRequest.open("GET", "cachemapsettings.php" + queryString, true);
			ajaxRequest.send(null); 
			
		}		 
		
		 if(document.getElementsByTagName) onload = function(){
    document.getElementsByTagName("BODY")[0].onclick = saveMapType;
		window.onbeforeunload = saveMapType;
		}

		
		//-->
	</script>
	');
	tpl_BuildTemplate(); 
}
?>

