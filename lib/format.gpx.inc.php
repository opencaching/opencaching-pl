<?php

use src\Models\OcConfig\OcConfig;

$gpxHead = '<?xml version="1.0" encoding="utf-8"?>
<gpx xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://www.topografix.com/GPX/1/0 http://www.topografix.com/GPX/1/0/gpx.xsd http://www.groundspeak.com/cache/1/0/1 http://www.groundspeak.com/cache/1/0/1/cache.xsd http://www.gsak.net/xmlv1/5 http://www.gsak.net/xmlv1/5/gsak.xsd https://github.com/opencaching/gpx-extension-v1 https://raw.githubusercontent.com/opencaching/gpx-extension-v1/master/schema.xsd"
    xmlns="http://www.topografix.com/GPX/1/0" version="1.0"
    creator="' . convert_string(OcConfig::getSiteName()) . '">

    <name>Cache Listing Generated from ' . convert_string(OcConfig::getSiteName()) . '</name>
    <desc>Cache Listing Generated from ' . convert_string(OcConfig::getSiteName()) . ' {wpchildren}</desc>
    <author>' . convert_string(OcConfig::getSiteName()) . '</author>
    <email>' . OcConfig::getEmailAddrTechAdmin() . '</email>
    <url>' . $absolute_server_URI . '</url>
    <urlname>' . convert_string(OcConfig::getSiteName()) . ' - ' . convert_string(tr('oc_subtitle_on_all_pages_' . $config['ocNode'])) . '</urlname>
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

$gpxAttribute = '                <groundspeak:attribute id="{attrib_id}" inc="{attrib_inc}">{attrib_text_long}</groundspeak:attribute>';

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
        <gsak:Child_ByGSAK>false</gsak:Child_ByGSAK>
        <gsak:Child_Flag>false</gsak:Child_Flag>
        <gsak:Code>{waypoint} {wp_stage}</gsak:Code>
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

/* ***********************************************************************
  Attributes

  GPX ID mapping of all attributes of opencaching-pl member sites.
  Complete documentation here: https://wiki.opencaching.eu/index.php?title=Cache_attributes
*/

$gpxAttribID[9001] = '9001';        $gpxAttribName[9001] = 'Dogs not allowed';
$gpxAttribID[2] = '2';        $gpxAttribName[2] = 'Access or parking fee';
$gpxAttribID[3] = '3';        $gpxAttribName[3] = 'Climbing gear requried';
$gpxAttribID[4] = '4';        $gpxAttribName[4] = 'Boat required';
$gpxAttribID[5] = '5';        $gpxAttribName[5] = 'Diving equipment required';
$gpxAttribID[6] = '6';        $gpxAttribName[6] = 'Suitable for children';
$gpxAttribID[9] = '9';        $gpxAttribName[9] = 'Long walk or hike';
$gpxAttribID[10] = '10';        $gpxAttribName[10] = 'Some climbing (no gear needed)';
$gpxAttribID[11] = '11';        $gpxAttribName[11] = 'Swamp or marsh. May require wading';
$gpxAttribID[12] = '12';        $gpxAttribName[12] = 'Swimming required';
$gpxAttribID[13] = '13';        $gpxAttribName[13] = 'Available 24/7';
$gpxAttribID[9013] = '9013';        $gpxAttribName[9013] = 'Available only during open hours';
$gpxAttribID[14] = '14';        $gpxAttribName[14] = 'Recommended at night';
$gpxAttribID[9014] = '9014';        $gpxAttribName[9014] = 'NOT recommended at night';
$gpxAttribID[15] = '15';        $gpxAttribName[15] = 'Available during winter';
$gpxAttribID[9015] = '9015';        $gpxAttribName[9015] = 'NOT available during winter';
$gpxAttribID[17] = '17';        $gpxAttribName[17] = 'Poisonous plants';
$gpxAttribID[18] = '18';        $gpxAttribName[18] = 'Dangerous animals';
$gpxAttribID[19] = '19';        $gpxAttribName[19] = 'Ticks';
$gpxAttribID[20] = '20';        $gpxAttribName[20] = 'Abandoned mine(s)';
$gpxAttribID[21] = '21';        $gpxAttribName[21] = 'Cliffs / falling rocks hazard';
$gpxAttribID[22] = '22';        $gpxAttribName[22] = 'Hunting grounds';
$gpxAttribID[23] = '23';        $gpxAttribName[23] = 'Dangerous area';
$gpxAttribID[24] = '24';        $gpxAttribName[24] = 'Wheelchair accessible';
$gpxAttribID[25] = '25';        $gpxAttribName[25] = 'Parking area nearby';
$gpxAttribID[26] = '26';        $gpxAttribName[26] = 'Public transportation';
$gpxAttribID[27] = '27';        $gpxAttribName[27] = 'Drinking water nearby';
$gpxAttribID[28] = '28';        $gpxAttribName[28] = 'Public restrooms nearby';
$gpxAttribID[29] = '29';        $gpxAttribName[29] = 'Public phone nearby';
$gpxAttribID[32] = '32';        $gpxAttribName[32] = 'Bycicles allowed';
$gpxAttribID[39] = '39';        $gpxAttribName[39] = 'Thorns';
$gpxAttribID[40] = '40';        $gpxAttribName[40] = 'Stealth required';
$gpxAttribID[44] = '44';        $gpxAttribName[44] = 'Flashlight required';
$gpxAttribID[46] = '46';        $gpxAttribName[46] = 'Truck / RV allowed';
$gpxAttribID[47] = '47';        $gpxAttribName[47] = 'Puzzle can only be solved on-site';
$gpxAttribID[48] = '48';        $gpxAttribName[48] = 'UV light required';
$gpxAttribID[51] = '51';        $gpxAttribName[51] = 'Special tool / equipment required';
$gpxAttribID[52] = '52';        $gpxAttribName[52] = 'Night cache - can only be found at night';
$gpxAttribID[53] = '53';        $gpxAttribName[53] = 'Park and grab';
$gpxAttribID[54] = '54';        $gpxAttribName[54] = 'Abandoned structure / ruin';
$gpxAttribID[60] = '60';        $gpxAttribName[60] = 'Wireless beacon / Garmin Chirp™';
$gpxAttribID[9062] = '9062';        $gpxAttribName[9062] = 'Available all seasons';
$gpxAttribID[64] = '64';        $gpxAttribName[64] = 'Tree climbing required';
$gpxAttribID[106] = '106';        $gpxAttribName[106] = 'OPENCACHING only cache';
$gpxAttribID[108] = '108';        $gpxAttribName[108] = 'Letterbox';
$gpxAttribID[110] = '110';        $gpxAttribName[110] = 'Active railway nearby';
$gpxAttribID[123] = '123';        $gpxAttribName[123] = 'First aid available';
$gpxAttribID[127] = '127';        $gpxAttribName[127] = 'Hilly area';
$gpxAttribID[130] = '130';        $gpxAttribName[130] = 'Point of interest';
$gpxAttribID[131] = '131';        $gpxAttribName[131] = 'Moving target';
$gpxAttribID[132] = '132';        $gpxAttribName[132] = 'Webcam ';
$gpxAttribID[133] = '133';        $gpxAttribName[133] = 'Indoors, withing enclosed space (building, cave, etc)';
$gpxAttribID[134] = '134';        $gpxAttribName[134] = 'Under water';
$gpxAttribID[135] = '135';        $gpxAttribName[135] = 'No GPS required';
$gpxAttribID[137] = '137';        $gpxAttribName[137] = 'Overnight stay necessary';
$gpxAttribID[142] = '142';        $gpxAttribName[142] = 'Not available during high tide';
$gpxAttribID[143] = '143';        $gpxAttribName[143] = 'Nature preserve / Breeding season';
$gpxAttribID[147] = '147';        $gpxAttribName[147] = 'Compass required';
$gpxAttribID[150] = '150';        $gpxAttribName[150] = 'Cave equipment required';
$gpxAttribID[153] = '153';        $gpxAttribName[153] = 'Aircraft required';
$gpxAttribID[154] = '154';        $gpxAttribName[154] = 'Internet research required';
$gpxAttribID[156] = '156';        $gpxAttribName[156] = 'Mathematical or logical problem';
$gpxAttribID[157] = '157';        $gpxAttribName[157] = 'Other cache type';
$gpxAttribID[158] = '158';        $gpxAttribName[158] = 'Ask owner for start conditions';
$gpxAttribID[201] = '201';        $gpxAttribName[201] = 'Quick and easy cache';
$gpxAttribID[202] = '202';        $gpxAttribName[202] = 'GeoHotel for trackables';
$gpxAttribID[203] = '203';        $gpxAttribName[203] = 'Bring your own pen';
$gpxAttribID[204] = '204';        $gpxAttribName[204] = 'Attached using magnet(s)';
$gpxAttribID[205] = '205';        $gpxAttribName[205] = 'Information in  MP3 file';
$gpxAttribID[206] = '206';        $gpxAttribName[206] = 'Container placed at an offset from given coordinates';
$gpxAttribID[207] = '207';        $gpxAttribName[207] = 'Dead Drop USB container';
$gpxAttribID[208] = '208';        $gpxAttribName[208] = 'Benchmark - geodetic point';
$gpxAttribID[209] = '209';        $gpxAttribName[209] = 'Wherigo cartridge to play';
$gpxAttribID[210] = '210';        $gpxAttribName[210] = 'Hidden in natural surroundings';
$gpxAttribID[211] = '211';        $gpxAttribName[211] = 'Monument or historic site';
$gpxAttribID[212] = '212';        $gpxAttribName[212] = 'Shovel required';
$gpxAttribID[213] = '213';        $gpxAttribName[213] = 'Access only by walk';
$gpxAttribID[214] = '214';        $gpxAttribName[214] = 'Rated on Handicaching.com';
$gpxAttribID[215] = '215';        $gpxAttribName[215] = 'Contains a Munzee';
$gpxAttribID[216] = '216';        $gpxAttribName[216] = 'Contains advertising';
$gpxAttribID[217] = '217';        $gpxAttribName[217] = 'Military training area, some access restrictions - check before visit';
$gpxAttribID[218] = '218';        $gpxAttribName[218] = 'Caution, area under video surveillance';
$gpxAttribID[219] = '219';        $gpxAttribName[219] = 'Suitable to hold trackables';
$gpxAttribID[220] = '220';        $gpxAttribName[220] = 'Officially designated historical monument';
$gpxAttribID[999] = '999';        $gpxAttribName[999] = 'Log password';

// node map
$gpxNodemap = [2 => 'PL', 4 => 'DEVEL', 6 => 'UK', 10 => 'US', 14 => 'NL', 16 => 'RO'];

// ************************************************************************
// Waypoints

$wptType[0] = 'Information';        // OC: UNDEFINED
$wptType[1] = 'Flag, Green';        // OC: TYPE_PHYSICAL
$wptType[2] = 'Flag, Blue';         // OC: TYPE_VIRTUAL
$wptType[3] = 'Flag, Red';          // OC: TYPE_FINAL
$wptType[4] = 'Waypoint';           // OC: TYPE_INTERESTING
$wptType[5] = 'Parking Area';       // OC: TYPE_PARKING
$wptType[6] = 'Trailhead';          // OC: TYPE_TRAILHEAD
