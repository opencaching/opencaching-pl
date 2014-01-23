<?php
  /*
    BBOX=2.38443,45.9322,20.7053,55.0289
  */

  $rootpath = '../../';
  require($rootpath . 'lib/common.inc.php');

  $bbox = isset($_REQUEST['BBOX']) ? $_REQUEST['BBOX'] : '0,0,0,0';
  $abox = split(',', $bbox);

  if (count($abox) != 4) exit;

  if (!is_numeric($abox[0])) exit;
  if (!is_numeric($abox[1])) exit;
  if (!is_numeric($abox[2])) exit;
  if (!is_numeric($abox[3])) exit;

  $lat_from = $abox[1];
  $lon_from = $abox[0];
  $lat_to = $abox[3];
  $lon_to = $abox[2];

//  if ((abs($lon_from - $lon_to) > 2)||(abs($lat_from - $lat_to) > 2))
//   {
//  $lon_from=$lon_to;
//  $lat_from=$lat_to;
//   }

  $rs = sql("SELECT `caches`.`cache_id` `cacheid`, `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, `caches`.`type` `type`, `caches`.`date_hidden` `date_hidden`, `caches`.`name` `name`, `cache_type`.`pl` `typedesc`, `cache_size`.`pl` `sizedesc`, `caches`.`terrain` `terrain`, `caches`.`difficulty` `difficulty`, `user`.`username` `username` FROM `caches`, `cache_type`, `cache_size`, `user` WHERE `caches`.`type`=`cache_type`.`id` AND `caches`.`size`=`cache_size`.`id` AND `caches`.`user_id`=`user`.`user_id` AND `caches`.`status`=1 AND `caches`.`longitude`>='" . sql_escape($lon_from) . "' AND `caches`.`longitude`<='" . sql_escape($lon_to) . "' AND `caches`.`latitude`>='" . sql_escape($lat_from) . "' AND `caches`.`latitude`<='" . sql_escape($lat_to) . "'");

  /*
   kml processing
  */

  $kmlLine =
'
<Placemark>
  <description><![CDATA[<a href="http://www.opencaching.pl/viewcache.php?cacheid={cacheid}">Zobacz więcej szczegółów skrzynki</a><br /> założona przez {username}<br />&nbsp;<br /><table cellspacing="0" cellpadding="0" border="0"><tr><td>{typeimgurl} </td><td>Rodzaj: {type}<br />Wielkosc: {{size}}</td></tr><tr><td colspan="2">Zadania: {difficulty} z 5.0<br />Teren: {terrain} z 5.0</td></tr></table>]]></description>
  <name>{name}</name>
  <LookAt>
    <longitude>{lon}</longitude>
    <latitude>{lat}</latitude>
    <range>5000</range>
    <tilt>0</tilt>
    <heading>3</heading>
  </LookAt>
  <styleUrl>#{icon}</styleUrl>
  <Point>
    <coordinates>{lon},{lat},0</coordinates>
  </Point>
</Placemark>
';

  $kmlHead =
'<?xml version="1.0" encoding="utf-8"?>
<kml xmlns="http://earth.google.com/kml/2.0">
<Document>
    <Style id="regular">
        <IconStyle>
            <scale>.4</scale>
            <Icon>
                <href>http://ocpl.geofizyka.pl/images/ge/regular.png</href>
            </Icon>
        </IconStyle>
    </Style>
    <Style id="multi">
        <IconStyle>
            <scale>.4</scale>
            <Icon>
                <href>http://ocpl.geofizyka.pl/images/ge/multi.png</href>
            </Icon>
        </IconStyle>
    </Style>
    <Style id="virtual">
        <IconStyle>
            <scale>.4</scale>
            <Icon>
                <href>http://ocpl.geofizyka.pl/images/ge/virtual.png</href>
            </Icon>
        </IconStyle>
    </Style>
    <Style id="webcam">
        <IconStyle>
            <scale>.4</scale>
            <Icon>
                <href>http://ocpl.geofizyka.pl/images/ge/webcam.png</href>
            </Icon>
        </IconStyle>
    </Style>
    <Style id="event">
        <IconStyle>
            <scale>.4</scale>
            <Icon>
                <href>http://ocpl.geofizyka.pl/images/ge/event.png</href>
            </Icon>
        </IconStyle>
    </Style>
    <Style id="other">
        <IconStyle>
            <scale>.4</scale>
            <Icon>
                <href>http://ocpl.geofizyka.pl/images/ge/other.png</href>
            </Icon>
        </IconStyle>
    </Style>
    <Folder>
        <Name>Geocaches (Opencaching) Polska</Name>
        <Open>0</Open>
';
  $kmlFoot = '</Folder></Document></kml>';
  $kmlTimeFormat = 'Y-m-d\TH:i:s\Z';

//  header("Content-type: application/vnd.google-earth.kml");
//  header("Content-Disposition: attachment; filename=ge.kml");

  echo $kmlHead;
$iso2utf8tr = array (
        "\261"=>"\xc4\x85", /* a */
        "\346"=>"\xc4\x87", /* c */
        "\352"=>"\xc4\x99", /* e */
        "\263"=>"\xc5\x82", /* l */
        "\361"=>"\xc5\x84", /* n */
        "\363"=>"\xc3\xb3", /* o- */
        "\266"=>"\xc5\x9b", /* s */
        "\274"=>"\xc5\xbc", /* z- */
        "\277"=>"\xc5\xba", /* z */
        "\241"=>"\xc4\x84", /* A */
        "\306"=>"\xc4\x86", /* C */
        "\312"=>"\xc4\x98", /* E */
        "\243"=>"\xc5\x81", /* L */
        "\321"=>"\xc5\x83", /* N */
        "\323"=>"\xc3\x93", /* O */
        "\246"=>"\xc5\x9a", /* S */
        "\254"=>"\xc5\xbb", /* Z */
        "\257"=>"\xc5\xb9" /* Z */
);


  while ($r = mysql_fetch_array($rs))
  {
    $thisline = $kmlLine;

    // icon suchen
    switch ($r['type'])
    {
      case 2:
        $icon = 'regular';
        $typeimgurl = '<img src="http://ocpl.geofizyka.pl/tpl/stdstyle/images/cache/traditional.png" alt="Tradycyjna" title="Tradycyjna" />';
        break;
      case 3:
        $icon = 'multi';
        $typeimgurl = '<img src="http://ocpl.geofizyka.pl/tpl/stdstyle/images/cache/multi.png" alt="Multicache" title="Multicache" />';
        break;
      case 4:
        $icon = 'virtual';
        $typeimgurl = '<img src="http://ocpl.geofizyka.pl/tpl/stdstyle/images/cache/virtual.png" alt="virtueller Cache" title="Wirtualna" />';
        break;
      case 5:
        $icon = 'webcam';
        $typeimgurl = '<img src="http://ocpl.geofizyka.pl/tpl/stdstyle/images/cache/webcam.png" alt="Webcam Cache" title="Webcam" />';
        break;
      case 6:
        $icon = 'event';
        $typeimgurl = '<img src="http://ocpl.geofizyka.pl/tpl/stdstyle/images/cache/event.png" alt="Wydarzenie" title="Wydarzenie" />';
        break;
      case 7:
        $icon = 'other';
        $typeimgurl = '<img src="http://ocpl.geofizyka.pl/tpl/stdstyle/images/cache/quiz.png" alt="Quiz" title="Quiz" />';
        break;
      case 9:
        $icon = 'other';
        $typeimgurl = '<img src="http://ocpl.geofizyka.pl/tpl/stdstyle/images/cache/moving.png" alt="Mobilna" title="Mobilna" />';
        break;
      default:
        $icon = 'other';
        $typeimgurl = '<img src="http://ocpl.geofizyka.pl/tpl/stdstyle/images/cache/unknown.png" alt="Nieznany typ" title="Nieznany typ" />';
        break;
    }
    $thisline = str_replace('{icon}', $icon, $thisline);
    $thisline = str_replace('{typeimgurl}', $typeimgurl, $thisline);

    $lat = sprintf('%01.5f', $r['latitude']);
    $thisline = str_replace('{lat}', $lat, $thisline);

    $lon = sprintf('%01.5f', $r['longitude']);
    $thisline = str_replace('{lon}', $lon, $thisline);

    $time = date($kmlTimeFormat, strtotime($r['date_hidden']));
    $thisline = str_replace('{{time}}', $time, $thisline);

$iso_string = $r['name'];
$utf8 = strtr($iso_string, $iso2utf8tr);

    $thisline = str_replace('{name}', xmlentities($utf8), $thisline);

    if (($r['status'] == 2) || ($r['status'] == 3))
    {
      if ($r['status'] == 2)
        $thisline = str_replace('{archivedflag}', 'Tymaczasowo niedostepna', $thisline);
      else
        $thisline = str_replace('{archivedflag}', 'Zarchiwizowana!, ', $thisline);
    }
    else
      $thisline = str_replace('{archivedflag}', '', $thisline);

    $thisline = str_replace('{type}', xmlentities($r['typedesc']), $thisline);
    $thisline = str_replace('{{size}}', xmlentities($r['sizedesc']), $thisline);

    $difficulty = sprintf('%01.1f', $r['difficulty'] / 2);
    $thisline = str_replace('{difficulty}', $difficulty, $thisline);

    $terrain = sprintf('%01.1f', $r['terrain'] / 2);
    $thisline = str_replace('{terrain}', $terrain, $thisline);

    $time = date($kmlTimeFormat, strtotime($r['date_hidden']));
    $thisline = str_replace('{{time}}', $time, $thisline);

    $thisline = str_replace('{username}', xmlentities($r['username']), $thisline);
    $thisline = str_replace('{cacheid}', xmlentities($r['cacheid']), $thisline);

    echo $thisline;
  }
  mysql_free_result($rs);

  echo $kmlFoot;
  exit;

function xmlentities($str)
{
  $from[0] = '&'; $to[0] = '&amp;';
  $from[1] = '<'; $to[1] = '&lt;';
  $from[2] = '>'; $to[2] = '&gt;';
  $from[3] = '"'; $to[3] = '&quot;';
  $from[4] = '\''; $to[4] = '&apos;';

  $str = str_replace($from, $to, $str);
  return $str;
}

?>
