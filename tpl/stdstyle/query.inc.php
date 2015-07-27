<?php
/***************************************************************************
											./tpl/stdstyle/query.inc.php
															-------------------
		begin                : November 4 2005
		copyright            : (C) 2005 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

   Unicode Reminder ??

	***************************************************************************/
	
	$error_queryname_exists = '<tr><td colspan="2" class="errormsg">Podana nazwa istnieje już</td></tr>';
	$error_empty_name = '<tr><td colspan="2" class="errormsg">Musisz podać nazwe pod jaką ma być zapisane opcje szukania</td></tr>';
	$viewquery_line = '<tr>
											<td bgcolor="{bgcolor}" width="40%" style="text-align: left; vertical-align: middle;"><a href="search.php?queryid={queryid}">{queryname}</a></td>
											<td bgcolor="{bgcolor}" width="40%" style="text-align: left; vertical-align: middle;">
												<a href="search.php?queryid={queryid}&output=gpx&count=max&zip=1" title="GPS Exchange Format .gpx">GPX</a> 
												<a href="search.php?queryid={queryid}&output=gpxgc&count=max&zip=1" title="GPS Exchange Format (Groundspeak) .gpx">GPX GC</a>
												<a href="search.php?queryid={queryid}&output=loc&count=max&zip=1" title="Waypoint .loc">LOC</a> 
												<a href="search.php?queryid={queryid}&output=kml&count=max&zip=1" title="Google Earth .kml">KML</a> 
												<a href="search.php?queryid={queryid}&output=ov2&count=max&zip=1" title="TomTom POI .ov2">OV2</a> 
												<a href="search.php?queryid={queryid}&output=ovl&count=max&zip=1" title="TOP50-Overlay .ovl">OVL</a>
												<a href="search.php?queryid={queryid}&output=txt&count=max&zip=1" title="Tekst .txt">TXT</a> 
												<a href="search.php?queryid={queryid}&output=wpt&count=max&zip=1" title="Oziexplorer .wpt">WPT</a> 
												<a href="search.php?queryid={queryid}&output=uam&count=max&zip=1" title="AutoMapa .uam">UAM</a>
											</td>
											<td bgcolor="{bgcolor}" width="10%" style="text-align: right; vertical-align: middle;">[<a href="search.php?queryid={queryid}&showresult=0">Szukaj</a>]</td>
										</tr>
										<tr>
											<td colspan="3" bgcolor="{bgcolor}" style="text-align: right; vertical-align: middle;">[<a href="query.php?queryid={queryid}&action=delete">Usuń</a>]</td>
										</tr>
										';
	$noqueries = '<tr><td colspan="2">Nie ma żadnych zgromadzonych poszukiwań</td></tr>';
	
	$saveastext = 'Wybierz zapisane poszukiwania w celu nadpisania';
	$nosaveastext = 'nie ma zapisanych opcji poszukiwań';
	
	$bgcolor1 = '#eeeeee';
	$bgcolor2 = '#e0e0e0';
?>