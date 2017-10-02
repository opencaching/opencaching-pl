<?php

global $rootpath;
require_once($rootpath . 'lib/common.inc.php');

// sitename and slogan international handling
$nodeDetect = substr($absolute_server_URI, - 3, 2);

$gpxHead = '<?xml version="1.0" encoding="utf-8"?>
<gpx xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://www.topografix.com/GPX/1/0 http://www.topografix.com/GPX/1/0/gpx.xsd http://www.groundspeak.com/cache/1/0/1 http://www.groundspeak.com/cache/1/0/1/cache.xsd http://www.gsak.net/xmlv1/5 http://www.gsak.net/xmlv1/5/gsak.xsd https://github.com/opencaching/gpx-extension-v1 https://raw.githubusercontent.com/following5/gpx-extension-v1/oc-logs/schema.xsd"
    xmlns="http://www.topografix.com/GPX/1/0" version="1.0"
    creator="' . convert_string($site_name) . '">

    <name>Cache Listing Generated from ' . convert_string($site_name) . '</name>
    <desc>Cache Listing Generated from ' . convert_string($site_name) . ' {wpchildren}</desc>
    <author>' . convert_string($site_name) . '</author>
    <email>' . $mail_oc . '</email>
    <url>' . $absolute_server_URI . '</url>
    <urlname>' . convert_string($site_name) . ' - ' . convert_string(tr('oc_subtitle_on_all_pages_' . $nodeDetect)) . '</urlname>
    <time>{time}</time>
    <keywords>cache, geocache</keywords>
';

$gpxLine = '
    <wpt lat="{lat}" lon="{lon}">
        <time>{time}</time>
        <name>{waypoint}</name>
        <desc>{mod_suffix}{cachename} ' . tr('from') . ' {owner}, {type_text} ({difficulty}/{terrain})</desc>
        <url>' . $absolute_server_URI . 'viewcache.php?wp={waypoint}</url>
        <urlname>{cachename} by {owner}, {type_text}</urlname>
        <sym>Geocache</sym>
        <type>Geocache|{type}</type>
        <groundspeak:cache id="{cacheid}" available="{available}" archived="{archived}" xmlns:groundspeak="http://www.groundspeak.com/cache/1/0/1">
            <groundspeak:name>{mod_suffix}{cachename}</groundspeak:name>
            <groundspeak:placed_by>{owner}</groundspeak:placed_by>
            <groundspeak:owner id="{owner_id}">{owner}</groundspeak:owner>
            <groundspeak:type>{type}</groundspeak:type>
            <groundspeak:container>{container}</groundspeak:container>
            <groundspeak:attributes>
{attributes}
            </groundspeak:attributes>
            <groundspeak:difficulty>{difficulty}</groundspeak:difficulty>
            <groundspeak:terrain>{terrain}</groundspeak:terrain>
            <groundspeak:country>{country}</groundspeak:country>
            <groundspeak:state>{region}</groundspeak:state>
            <groundspeak:short_description html="False">{shortdesc}</groundspeak:short_description>
            <groundspeak:long_description html="True">
                {desc}
                {rr_comment}
                &lt;br&gt;
                {images}
                &lt;br&gt;
                {personal_cache_note}
                &lt;br&gt;
                {extra_info}
            </groundspeak:long_description>
            <groundspeak:encoded_hints>{hints}</groundspeak:encoded_hints>
            <groundspeak:logs>
{gs_logs}
            </groundspeak:logs>
            <groundspeak:travelbugs>
{geokrety}
            </groundspeak:travelbugs>
        </groundspeak:cache>
        <oc:cache xmlns:oc="https://github.com/opencaching/gpx-extension-v1">
            <oc:type>{oc_type}</oc:type>
            <oc:size>{oc_size}</oc:size>
{oc_trip_time}
{oc_trip_distance}
            <oc:requires_password>{oc_password}</oc:requires_password>
{oc_other_codes}
            <oc:logs>
{oc_logs}
            </oc:logs>
        </oc:cache>
    </wpt>

{cache_waypoints}
';

$gpxAttributes = '                <groundspeak:attribute id="{attrib_id}" inc="{attrib_inc}">{attrib_text_long}</groundspeak:attribute>';

$gpxLog = '                <groundspeak:log id="{id}">
                    <groundspeak:date>{date}</groundspeak:date>
                    <groundspeak:type>{type}</groundspeak:type>
                    <groundspeak:finder id="{finder_id}">{username}</groundspeak:finder>
                    <groundspeak:text encoded="False">{text}</groundspeak:text>
                </groundspeak:log>
';

$gpxGeoKrety = '                <groundspeak:travelbug id="{geokret_id}" ref="{geokret_ref}">
                    <groundspeak:name>{geokret_name}</groundspeak:name>
                </groundspeak:travelbug>
';

$gpxWaypoints = '    <wpt lat="{wp_lat}" lon="{wp_lon}">
        <time>{time}</time>
        <name>{waypoint} {wp_stage}</name>
        <cmt>{desc}</cmt>
        <desc>{wp_type_name}</desc>
        <url>' . $absolute_server_URI . 'viewcache.php?wp={waypoint}</url>
        <urlname>{waypoint} {wp_stage}</urlname>
        <sym>{wp_type}</sym>
        <type>Waypoint|{wp_type}</type>
        <gsak:wptExtension xmlns:gsak="http://www.gsak.net/xmlv1/5">
        <gsak:Parent>{waypoint}</gsak:Parent>
        <gsak:Code>{waypoint} {wp_stage}</gsak:Code>
        <gsak:Child_Flag>false</gsak:Child_Flag>
        <gsak:Child_ByGSAK>false</gsak:Child_ByGSAK>
        </gsak:wptExtension>
    </wpt>
';

$gpxOcTripTime = '            <oc:trip_time>{time}</oc:trip_time>';
$gpxOcTripDistance = '            <oc:trip_distance>{distance}</oc:trip_distance>';
$gpxOcOtherCode = '            <oc:other_code>{code}</oc:other_code>';

$gpxOcLog = '                <oc:log id="{id}" uuid="{uuid}">
{oc_team_entry}                </oc:log>';
$gpxOcIsTeamEntry = '                    <oc:site_team_entry>true</oc:site_team_entry>
';

$gpxFoot = '</gpx>';

$gpxTimeFormat = 'Y-m-d\TH:i:s\Z';

// ************************************************************************
// Geocache status

$gpxAvailable[0] = 'False';     // OC: UNDEFINED
$gpxAvailable[1] = 'True';      // OC: STATUS_READY
$gpxAvailable[2] = 'False';     // OC: STATUS_UNAVAILABLE
$gpxAvailable[3] = 'False';     // OC: STATUS_ARCHIVED
$gpxAvailable[4] = 'False';     // OC: STATUS_WAITAPPROVERS
$gpxAvailable[5] = 'False';     // OC: STATUS_NOTYETAVAILABLE
$gpxAvailable[6] = 'False';     // OC: STATUS_BLOCKED

$gpxArchived[0] = 'False';      // OC: UNDEFINED
$gpxArchived[1] = 'False';      // OC: STATUS_READY
$gpxArchived[2] = 'False';      // OC: STATUS_UNAVAILABLE
$gpxArchived[3] = 'True';       // OC: STATUS_ARCHIVED
$gpxArchived[4] = 'False';      // OC: STATUS_WAITAPPROVERS
$gpxArchived[5] = 'False';      // OC: STATUS_NOTYETAVAILABLE
$gpxArchived[6] = 'True';       // OC: STATUS_BLOCKED

// ************************************************************************
// Geocache size

// These strings must not be translated
// Ref: see http://forum.opencaching.pl/viewtopic.php?p=121737#p121737
$gpxContainer[1] = 'Other';         // OC: SIZE_OTHER
$gpxContainer[2] = 'Micro';         // OC: SIZE_MICRO
$gpxContainer[3] = 'Small';         // OC: SIZE_SMALL
$gpxContainer[4] = 'Regular';       // OC: SIZE_REGULAR
$gpxContainer[5] = 'Large';         // OC: SIZE_LARGE
$gpxContainer[6] = 'Very Large';    // OC: SIZE_XLARGE
$gpxContainer[7] = 'No container';  // OC: SIZE_NONE
$gpxContainer[8] = 'Nano';          // OC: SIZE_NANO

// size strings for oc:size (OC GPX extension)
$gpxOcSize[1] = 'Other';
$gpxOcSize[2] = 'Micro';
$gpxOcSize[3] = 'Small';
$gpxOcSize[4] = 'Regular';
$gpxOcSize[5] = 'Large';
$gpxOcSize[6] = 'Very large';
$gpxOcSize[7] = 'No container';
$gpxOcSize[8] = 'Nano';

// ************************************************************************
// Geocache type

// Cache types (as known by Groundspeak GPX interpreters)
// Note: these names should be defined with cache types. See well_defined project.
$gpxType[1] = 'Unknown Cache';      // OC: TYPE_OTHERTYPE
$gpxType[2] = 'Traditional Cache';  // OC: TYPE_TRADITIONAL
$gpxType[3] = 'Multi-cache';        // OC: TYPE_MULTICACHE
$gpxType[4] = 'Virtual Cache';      // OC: TYPE_VIRTUAL
$gpxType[5] = 'Webcam Cache';       // OC: TYPE_WEBCAM
$gpxType[6] = 'Event Cache';        // OC: TYPE_EVENT
// OC specific cache types
$gpxType[7] = 'Unknown Cache';      // OC: TYPE_QUIZ
$gpxType[8] = 'Unknown Cache';      // OC: TYPE_MOVING
$gpxType[9] = 'Unknown Cache';      // OC: TYPE_GEOPATHFINAL
$gpxType[10] = 'Unknown Cache';     // OC: TYPE_OWNCACHE

// type strings for oc:type (OC GPX extension)
$gpxOcType[1] = 'Other Cache';
$gpxOcType[2] = 'Traditional Cache';
$gpxOcType[3] = 'Multi-cache';
$gpxOcType[4] = 'Virtual Cache';
$gpxOcType[5] = 'Webcam Cache';
$gpxOcType[6] = 'Event Cache';
$gpxOcType[7] = 'Quiz Cache';
$gpxOcType[8] = 'Moving Cache';
$gpxOcType[9] = 'Podcast Cache';
$gpxOcType[10] = 'Own Cache';

/* Groundspeak IDs:
2       Traditional Cache
3       Multi-cache
4       Virtual Cache
5       Letterbox Hybrid
6       Event Cache
8       Unknown Cache
11      Webcam Cache
12      Locationless (Reverse) Cache
13      Cache In Trash Out Event
137     Earthcache
453     Mega-Event Cache
1304    GPS Adventures Maze Exhibit
1858    Wherigo Cache
3653    Lost and Found Event Cache
3773    Groundspeak Headquarters Cache
4738    ???
7005    Giga-Event Cache
mega    Mega-Event Cache
earthcache  EarthCache

Source: GC Tour
https://gist.github.com/DieBatzen/5814dc7368c1034470c8/
*/

// OC type names
// Note: these names should be defined with cache types. See well_defined project.
$gpxGeocacheTypeText[1] = 'Unknown Cache';
$gpxGeocacheTypeText[2] = 'Traditional Cache';
$gpxGeocacheTypeText[3] = 'Multi-Cache';
$gpxGeocacheTypeText[4] = 'Virtual Cache';
$gpxGeocacheTypeText[5] = 'Webcam Cache';
$gpxGeocacheTypeText[6] = 'Event Cache';
$gpxGeocacheTypeText[7] = 'Puzzle Cache';
$gpxGeocacheTypeText[8] = 'Moving Cache';
$gpxGeocacheTypeText[9] = 'Podcast cache';
$gpxGeocacheTypeText[10] = 'Own cache';

// ************************************************************************
// Logs

// These strings must not be translated
$gpxLogType[0] = 'Write note';                          // OC: UNDEFINED
$gpxLogType[1] = 'Found it';                            // OC: LOGTYPE_FOUNDIT
$gpxLogType[2] = 'Didn\'t find it';                     // OC: LOGTYPE_DIDNOTFIND
$gpxLogType[3] = 'Write note';                          // OC: LOGTYPE_COMMENT
$gpxLogType[4] = 'Moved';                               // OC: LOGTYPE_MOVED
$gpxLogType[5] = 'Needs Maintenance';                   // OC: LOGTYPE_NEEDMAINTENANCE
$gpxLogType[6] = 'Maintenance Performed';               // OC: XXX_SERVICE_MADE
$gpxLogType[7] = 'Attended';                            // OC: LOGTYPE_ATTENDED
$gpxLogType[8] = 'Will Attend';                         // OC: XXX_WILL_ATTEND
$gpxLogType[9] = 'Archived';                            // OC: XXX_ARCHIVED
$gpxLogType[10] = 'Enable Listing';                     // OC: XXX_READY_TO_BE_FOUND
$gpxLogType[11] = 'Temporarily Disable Listing';        // OC: XXX_TEMPORARILY_UNAVAILABLE
$gpxLogType[12] = 'OC Team Comment';                    // OC: XXX_OC_TEAM_COMMENT
// Note: log types implementation incomplete.

// ************************************************************************
// Attributes

// 1st set of attributes - attributes that are Groundspeak equivalent
$gpxAttribID[1] = '52';     $gpxAttribName[1] = 'Night Cache';
$gpxAttribID[9] = '23';     $gpxAttribName[9] = 'Dangerous area';
$gpxAttribID[11] = '21';    $gpxAttribName[11] = 'Cliff / falling rocks';
$gpxAttribID[12] = '22';    $gpxAttribName[12] = 'Hunting';
$gpxAttribID[13] = '39';    $gpxAttribName[13] = 'Thorns';
$gpxAttribID[14] = '19';    $gpxAttribName[14] = 'Ticks';
$gpxAttribID[15] = '20';    $gpxAttribName[15] = 'Abandoned mines';
$gpxAttribID[16] = '17';    $gpxAttribName[16] = 'Poisonous plants';
$gpxAttribID[17] = '18';    $gpxAttribName[17] = 'Dangerous animals';
$gpxAttribID[18] = '25';    $gpxAttribName[18] = 'Parking available';
$gpxAttribID[19] = '26';    $gpxAttribName[19] = 'Public transportation';
$gpxAttribID[20] = '27';    $gpxAttribName[20] = 'Drinking water nearby';
$gpxAttribID[21] = '28';    $gpxAttribName[21] = 'Public restrooms nearby';
$gpxAttribID[22] = '29';    $gpxAttribName[22] = 'Telephone nearby';
$gpxAttribID[24] = '53';    $gpxAttribName[53] = 'Park and grab';
$gpxAttribID[25] = '9';     $gpxAttribName[25] = 'Significant Hike';
$gpxAttribID[26] = '11';    $gpxAttribName[26] = 'May require wading';
$gpxAttribID[28] = '10';    $gpxAttribName[28] = 'Difficult climbing';
$gpxAttribID[29] = '12';    $gpxAttribName[29] = 'May require swimming';
$gpxAttribID[36] = '2';     $gpxAttribName[36] = 'Access or parking fee';
$gpxAttribID[38] = '13';    $gpxAttribName[38] = 'Available at all times';
$gpxAttribID[39] = '13';    $gpxAttribName[39] = 'Available at all times';          $gptAttribInc[39] = '0';
//$gpxAttribID[40] = '14';    $gpxAttribName[40] = 'Recommended at night';            $gptAttribInc[39] = '0'; // OCUK mapping overlap
$gpxAttribID[41] = '6';     $gpxAttribName[41] = 'Recommended for kids';
$gpxAttribID[42] = '62';    $gpxAttribName[42] = 'Seasonal access';                 $gptAttribInc[39] = '0';
$gpxAttribID[44] = '24';    $gpxAttribName[44] = 'Wheelchair accessible';
//$gpxAttribID[44] = '15';    $gpxAttribName[44] = 'Available during winter'; // OCUK mapping overlap
$gpxAttribID[45] = '40';    $gpxAttribName[45] = 'Stealth required';
$gpxAttribID[46] = '51';    $gpxAttribName[46] = 'Special tool required';
//$gpxAttribID[49] = '3';     $gpxAttribName[49] = 'Climbing gear required'; // OCUK mapping overlap
//$gpxAttribID[51] = '5';     $gpxAttribName[51] = 'Scuba gear required'; // OCUK mapping overlap
$gpxAttribID[52] = '60';    $gpxAttribName[52] = 'Wireless Beacon';
$gpxAttribID[59] = '6';     $gpxAttribName[59] = 'Recommended for kids';
$gpxAttribID[80] = '13';    $gpxAttribName[80] = 'Available at all times';          $gptAttribInc[39] = '0';
$gpxAttribID[82] = '44';    $gpxAttribName[82] = 'Flashlignt required';
$gpxAttribID[83] = '51';    $gpxAttribName[83] = 'Special tool required';
$gpxAttribID[85] = '32';    $gpxAttribName[85] = 'Bicycles';
$gpxAttribID[86] = '4';     $gpxAttribName[86] = 'Boat required';
$gpxAttribID[90] = '23';    $gpxAttribName[90] = 'Dangerous area';
$gpxAttribID[91] = '14';    $gpxAttribName[91] = 'Recommended at night';
$gpxAttribID[155] = '47';   $gpxAttribName[155] = 'Field puzzle';

// 2nd set of attributes - OC only attributes, changed ID (+100) to be safe in OC-GC-mixed environments
$gpxAttribID[8] = '156';     $gpxAttribName[8] = 'Letterbox cache';
$gpxAttribID[10] = '110';    $gpxAttribName[10] = 'Active railway nearby';
$gpxAttribID[23] = '123';    $gpxAttribName[23] = 'First aid available';
$gpxAttribID[27] = '127';    $gpxAttribName[27] = 'Hilly area';
$gpxAttribID[30] = '130';    $gpxAttribName[30] = 'Point of interest';
$gpxAttribID[31] = '131';    $gpxAttribName[31] = 'Has a moving target';
$gpxAttribID[32] = '132';    $gpxAttribName[32] = 'A webcam is involved';
$gpxAttribID[33] = '133';    $gpxAttribName[33] = 'Hidden wihin enclosed rooms (caves, buildings etc.)';
$gpxAttribID[34] = '134';    $gpxAttribName[34] = 'Hidden under water';
$gpxAttribID[35] = '135';    $gpxAttribName[35] = 'No GPS required';
$gpxAttribID[37] = '137';    $gpxAttribName[37] = 'Overnight stay necessary';
$gpxAttribID[40] = '140';    $gpxAttribName[40] = 'GeoHotel';
//$gpxAttribID[41] = '141';    $gpxAttribName[41] = 'Not available during high tide'; // OCUK mapping overlap
$gpxAttribID[43] = '143';    $gpxAttribName[43] = 'Quick cache';
//$gpxAttribID[43] = '143';    $gpxAttribName[43] = 'Nature preserve / Breeding season'; // OCUK mapping overlap
$gpxAttribID[49] = '149';    $gpxAttribName[49] = 'Magnetic';
$gpxAttribID[47] = '147';    $gpxAttribName[47] = 'Compass required';
$gpxAttribID[48] = '148';    $gpxAttribName[48] = 'Bring your own pen';
$gpxAttribID[50] = '150';    $gpxAttribName[50] = 'Audio file';
//$gpxAttribID[50] = '150';    $gpxAttribName[50] = 'Cave equipment required'; // OCUK mapping overlap
$gpxAttribID[51] = '151';    $gpxAttribName[51] = 'Offset cache';
$gpxAttribID[53] = '153';    $gpxAttribName[53] = 'USB Dead Drop cache';
$gpxAttribID[54] = '154';    $gpxAttribName[54] = 'Near a Survey Marker';
//$gpxAttribID[54] = '154';    $gpxAttribName[54] = 'Investigation required'; // OCUK mapping overlap
$gpxAttribID[55] = '155';    $gpxAttribName[55] = 'Wherigo cache';
$gpxAttribID[56] = '156';    $gpxAttribName[56] = 'Letterbox cache';
//$gpxAttribID[56] = '156';    $gpxAttribName[56] = 'Mathematical problem'; // OCUK mapping overlap
$gpxAttribID[57] = '157';    $gpxAttribName[57] = 'Other cache type';
$gpxAttribID[58] = '158';    $gpxAttribName[58] = 'Ask owner for start conditions';
$gpxAttribID[60] = '160';    $gpxAttribName[60] = 'Hidden in natural surroundings (forests, mountains, etc.)';
$gpxAttribID[61] = '161';    $gpxAttribName[61] = 'Historic site';
$gpxAttribID[81] = '181';    $gpxAttribName[81] = 'You may need a shovel';
$gpxAttribID[84] = '184';    $gpxAttribName[84] = 'Access only on foot';
$gpxAttribID[106] = '106';   $gpxAttribName[106] = 'OC-only cache';
$gpxAttribID[156] = '156';   $gpxAttribName[156] = 'Aircraft required';
$gpxAttribID[157] = '157';   $gpxAttribName[157] = 'Rated on Handicaching.com';
$gpxAttribID[158] = '158';   $gpxAttribName[158] = 'Contains a Munzee';

// ************************************************************************
// Waypoints

$wptType[0] = 'Information';        // OC: UNDEFINED
$wptType[1] = 'Flag, Green';        // OC: TYPE_PHYSICAL
$wptType[2] = 'Flag, Blue';         // OC: TYPE_VIRTUAL
$wptType[3] = 'Flag, Red';          // OC: TYPE_FINAL
$wptType[4] = 'Waypoint';           // OC: TYPE_INTERESTING
$wptType[5] = 'Parking Area';       // OC: TYPE_PARKING
$wptType[6] = 'Trailhead';          // OC: TYPE_TRAILHEAD
