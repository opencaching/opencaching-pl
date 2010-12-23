<?php

/***************************************************************************
	*
	*   This program is free software; you can redistribute it and/or modify
	*   it under the terms of the GNU General Public License as published by
	*   the Free Software Foundation; either version 2 of the License, or
	*   (at your option) any later version.
	*
	***************************************************************************/

/****************************************************************************

   Unicode Reminder メモ

	 display all watches of this user

 ****************************************************************************/

	//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');

	//Preprocessing
	if ($error == false)
	{
		//user logged in?
		if ($usr == false)
		{
		    $target = urlencode(tpl_get_current_page());
		    tpl_redirect('login.php?target='.$target);
		}
		else
		{
			include($stylepath . '/mylist.inc.php');
			$tplname = 'myroute';
			

if ( !$error ) {
exec("/usr/bin/gpsbabel"." -i kml -f $upload_filename -x interpolate,distance=0.25k -o kml -F $upload_filename");
$xml = simplexml_load_file($upload_filename);
foreach ( $xml->Document->Folder as $xmlelement ) {
foreach ( $xmlelement->Folder as $folder ) {
foreach ( $folder->Placemark->LineString->coordinates as $coordinates ) {
if ( $coordinates ) {
$coords_raw = explode(" ",trim($coordinates));
	foreach ( $coords_raw as $coords_raw_part ) {
		if ( $coords_raw_part ) {	
		$coords_raw_parts = explode(",",$coords_raw_part);
		$coords[] = $coords_raw_parts[0];
		$coords[] = $coords_raw_parts[1];
		}
	}
	}
	}
	}
	}
}


if (!$error){
for( $i=0; $i<count($coords)-1; $i=$i+2 ) {
$points[] = array("lon"=>$coords[$i],"lat"=>$coords[$i+1]);
if ( ($coords[$i]+0==0) OR ($coords[$i+1]+0==0) ) {
$error .= "Invalid Co-ords found in import file.<br>\n";
break;
}
}
}


				}
				
			}
		
	}

	//make the template and send it out
	tpl_BuildTemplate();
?>
