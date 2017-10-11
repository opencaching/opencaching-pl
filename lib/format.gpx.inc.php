<?php

global $rootpath;
require_once($rootpath . 'lib/common.inc.php');

// sitename and slogan international handling
$nodeDetect = substr($absolute_server_URI, - 3, 2);

$gpxHead = '<?xml version="1.0" encoding="utf-8"?>
<gpx xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://www.topografix.com/GPX/1/0 http://www.topografix.com/GPX/1/0/gpx.xsd http://www.groundspeak.com/cache/1/0/1 http://www.groundspeak.com/cache/1/0/1/cache.xsd http://www.gsak.net/xmlv1/5 http://www.gsak.net/xmlv1/5/gsak.xsd"
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
{logs}
            </groundspeak:logs>
            <groundspeak:travelbugs>
{geokrety}
            </groundspeak:travelbugs>
        </groundspeak:cache>
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
$gpxContainer[0] = 'Unknown';       // OC: UNDEFINED
$gpxContainer[1] = 'Other';         // OC: SIZE_OTHER
$gpxContainer[2] = 'Micro';         // OC: SIZE_MICRO
$gpxContainer[3] = 'Small';         // OC: SIZE_SMALL
$gpxContainer[4] = 'Regular';       // OC: SIZE_REGULAR
$gpxContainer[5] = 'Large';         // OC: SIZE_LARGE
$gpxContainer[6] = 'Very Large';    // OC: SIZE_XLARGE
$gpxContainer[7] = 'No container';  // OC: SIZE_NONE
$gpxContainer[8] = 'Nano';          // OC: SIZE_NANO

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

/************************************************************************
Attributes

GPX ID mapping of all attributes of OC.PL .NL .RO .UK. .US, as of 3 October 2017.
If there is a matching DE attribute with other ID, the DE ID is given in the "DE" column.

IDs < 100 are original GC.com, 101-199 are pseudo-GC IDs for special Opencaching attributes.
Appended ".0" means inc="0".

Note that there are some redundant IDs, e.h. UK/RO 46 and NL/PL/US 83 both map to
GC 51 "Special tool required".
*/

// common assignments
$gpxAttribID[1] = '52';     $gpxAttribName[1] = 'Night cache';                // DE UK
$gpxAttribID[6] = '106';    $gpxAttribName[6] = 'OC-only cache';              // DE UK RO NL
$gpxAttribID[8] = '108';    $gpxAttribName[8] = 'Letterbox cache';            // DE UK RO
$gpxAttribID[9] = '23';     $gpxAttribName[9] = 'Dangerous area';             // DE UK RO
$gpxAttribID[10] = '110';   $gpxAttribName[10] = 'Active railway nearby';     // DE UK
$gpxAttribID[11] = '21';    $gpxAttribName[11] = 'Cliff / falling rocks';     // DE UK
$gpxAttribID[12] = '22';    $gpxAttribName[12] = 'Hunting';                   // DE UK RO NL
$gpxAttribID[13] = '39';    $gpxAttribName[13] = 'Thorns';                    // DE UK RO NL
$gpxAttribID[14] = '19';    $gpxAttribName[14] = 'Ticks';                     // DE UK RO NL
$gpxAttribID[15] = '20';    $gpxAttribName[15] = 'Abandoned mines';           // DE UK
$gpxAttribID[16] = '17';    $gpxAttribName[16] = 'Poisonous plants';          // DE UK
$gpxAttribID[17] = '18';    $gpxAttribName[17] = 'Dangerous animals';         // DE UK
$gpxAttribID[18] = '25';    $gpxAttribName[18] = 'Parking available';         // DE UK RO NL
$gpxAttribID[19] = '26';    $gpxAttribName[19] = 'Public transportation';     // DE UK
$gpxAttribID[20] = '27';    $gpxAttribName[20] = 'Drinking water nearby';     // DE UK RO
$gpxAttribID[21] = '28';    $gpxAttribName[21] = 'Public restrooms nearby';   // DE UK
$gpxAttribID[22] = '29';    $gpxAttribName[22] = 'Telephone nearby';          // DE UK
$gpxAttribID[23] = '123';   $gpxAttribName[23] = 'First aid available';       // DE UK
$gpxAttribID[24] = '53';    $gpxAttribName[24] = 'Park and grab';             // DE UK
$gpxAttribID[25] = '9';     $gpxAttribName[25] = 'Significant Hike';          // DE UK RO NL
$gpxAttribID[26] = '11';    $gpxAttribName[26] = 'May require wading';        // DE UK
$gpxAttribID[27] = '127';   $gpxAttribName[27] = 'Hilly area';                // DE UK
$gpxAttribID[28] = '10';    $gpxAttribName[28] = 'Difficult climbing';        // DE UK
$gpxAttribID[29] = '12';    $gpxAttribName[29] = 'May require swimming';      // DE UK
$gpxAttribID[30] = '130';   $gpxAttribName[30] = 'Point of interest';         // DE UK
$gpxAttribID[31] = '131';   $gpxAttribName[31] = 'Has a moving target';       // DE UK
$gpxAttribID[32] = '132';   $gpxAttribName[32] = 'A webcam is involved';      // DE UK
$gpxAttribID[33] = '133';   $gpxAttribName[33] = 'Hidden wihin enclosed room';// DE UK
$gpxAttribID[34] = '134';   $gpxAttribName[34] = 'Hidden under water';        // DE UK
$gpxAttribID[35] = '135';   $gpxAttribName[35] = 'No GPS required';           // DE UK
$gpxAttribID[36] = '2';     $gpxAttribName[36] = 'Access or parking fee';     // DE UK
$gpxAttribID[37] = '137';   $gpxAttribName[37] = 'Overnight stay necessary';  // DE UK
$gpxAttribID[38] = '13';    $gpxAttribName[38] = 'Available at all times';    // DE UK
$gpxAttribID[39] = '13.0';  $gpxAttribName[39] = 'Only available at specified times';
                                                                              // DE UK
$gpxAttribID[40] = '111';   $gpxAttribName[40] = 'Quick cache';               //       RO NL PL US
$gpxAttribID[41] = '6';     $gpxAttribName[41] = 'Recommended for kids';      // (59)     NL PL US
$gpxAttribID[42] = '62.0';  $gpxAttribName[42] = 'All seasons';               // DE UK
$gpxAttribID[43] = '112';   $gpxAttribName[43] = 'GeoHotel';                  //       RO NL PL
                                                                              // DE UK
$gpxAttribID[44] = '24';    $gpxAttribName[44] = 'Wheelchair accessible';     //       RO NL PL US
$gpxAttribID[45] = '40';    $gpxAttribName[45] = 'Stealth required';          //       RO NL
$gpxAttribID[46] = '51';    $gpxAttribName[46] = 'Special tool required';     // UK    RO         
$gpxAttribID[47] = '147';   $gpxAttribName[47] = 'Compass required';          // DE UK RO NL PL
$gpxAttribID[48] = '113';   $gpxAttribName[48] = 'Bring your own pen';        //       RO NL PL US
$gpxAttribID[49] = '149';   $gpxAttribName[49] = 'Magnetic';                  //       RO NL PL US       
$gpxAttribID[50] = '114';   $gpxAttribName[50] = 'Audio file';                //       RO NL PL       
$gpxAttribID[51] = '115';   $gpxAttribName[51] = 'Offset cache';              //       RO    PL US
$gpxAttribID[52] = '60';    $gpxAttribName[52] = 'Wireless Beacon';           //       RO NL PL US
$gpxAttribID[53] = '116';   $gpxAttribName[53] = 'USB Dead Drop cache';       //       RO NL PL
$gpxAttribID[54] = '117';   $gpxAttribName[54] = 'Near a Survey Marker';      //       RO NL PL
$gpxAttribID[55] = '118';   $gpxAttribName[55] = 'Wherigo cache';             //       RO    PL
$gpxAttribID[56] = '108';   $gpxAttribName[56] = 'Letterbox cache';           // 8        NL PL
$gpxAttribID[57] = '157';   $gpxAttribName[57] = 'Other cache type';          // DE UK
$gpxAttribID[58] = '158';   $gpxAttribName[58] = 'Ask owner for start conditions';
$gpxAttribID[59] = '6';     $gpxAttribName[59] = 'Recommended for kids';      // DE UK RO NL
$gpxAttribID[60] = '119';   $gpxAttribName[60] = 'Hidden in natural surroundings';
                                                                              //       RO NL PL US
$gpxAttribID[61] = '120';   $gpxAttribName[61] = 'Historic site';             //       RO NL PL US
$gpxAttribID[62] = '54';    $gpxAttribName[62] = 'Abandoned structure';       //          NL
$gpxAttribID[80] = '13.0';  $gpxAttribName[80] = 'Only available at specified times';
                                                                              // 39          PL US
$gpxAttribID[81] = '121';   $gpxAttribName[81] = 'You may need a shovel';     //       RO NL PL
$gpxAttribID[82] = '44';    $gpxAttribName[82] = 'Flashlignt required';       // 48 UK RO NL PL US
$gpxAttribID[83] = '51';    $gpxAttribName[83] = 'Special tool required';     // 46       NL PL US
$gpxAttribID[84] = '122';   $gpxAttribName[84] = 'Access only on foot';       //       RO NL PL
$gpxAttribID[85] = '32';    $gpxAttribName[85] = 'Bicycles';                  //       RO NL PL
    // TODO: https://github.com/opencaching/opencaching-pl/issues/1244
$gpxAttribID[86] = '4';     $gpxAttribName[86] = 'Boat required';             // 52 UK RO NL PL
$gpxAttribID[90] = '23';    $gpxAttribName[90] = 'Dangerous area';            //       RO NL PL US
$gpxAttribID[91] = '14';    $gpxAttribName[91] = 'Recommended at night';      // 9     RO NL PL
$gpxAttribID[155] = '47';   $gpxAttribName[155] = 'Field puzzle';             //    UK RO
$gpxAttribID[156] = '153';  $gpxAttribName[156] = 'Aircraft required';        // 53 UK
$gpxAttribID[157] = '125';  $gpxAttribName[157] = 'Rated on Handicaching.com';//    UK
$gpxAttribID[158] = '126';  $gpxAttribName[158] = 'Contains a Munzee';        //    UK

/*
ATTENTION:

If you add a new attribute to your OC site, follow these steps to assign a GPX ID:

1. Try to map it to an existing Groundspeak attribute. Consult the table in the
   upper section of okapi/services/attrs/attribute-definitions.xml for all known
   GS attribs. If no GS attribute is available:

2. Try to map it to an existing OCDE attribute. You will find all OCDE attributes here:
   https://github.com/OpencachingDeutschland/oc-server3/blob/development/sql/static-data/cache_attrib.sql
   The column "gc_id" contains the GPX ID for the attribute, and the column "gc_inc"
   the inc-value. If no OCDE attribute is available:

3. Use the first number from this list of unassigned GPX IDs, and remove if from the list:
   128, 129, 136, 138, 139, 140, 142, 144, 146, 148, 149, 151, 152, 155, 159, 160.

4. Inform the Okapi project about your new attribute, so that it will be added to
   okapi/services/attrs/attribute-definitions.
*/

// special UK assignemnts
$gpxAI['UK'][40] = '14.0';  $gpxAInm['UK'][40] = 'Not recommended at night';  // DE UK
$gpxAI['UK'][41] = '142';   $gpxAInm['UK'][41] = 'Not available during high tide';
    // Due to a typo in OCDE code, #41 maps to #142 instead of #141.             DE UK
    // It has inc="1" in spite of "negative logic", because there is
    // not matching positive attribute.
$gpxAI['UK'][43] = '143';   $gpxAInm['UK'][43] = 'Neature reserve / Breeding season';
$gpxAI['UK'][44] = '15';    $gpxAInm['UK'][44] = 'Available during winter';   // DE UK
$gpxAI['UK'][49] = '3';     $gpxAInm['UK'][49] = 'Climbing gear required';    // DE UK
$gpxAI['UK'][50] = '150';   $gpxAInm['UK'][50] = 'Cave equipment required';   // DE UK
$gpxAI['UK'][51] = '5';     $gpxAInm['UK'][51] = 'Scuba gear required';       // DE UK
$gpxAI['UK'][54] = '154';   $gpxAInm['UK'][54] = 'Investigation required';    // DE UK
$gpxAI['UK'][56] = '156';   $gpxAInm['UK'][56] = 'Mathematical problem';      // DE UK

// special US assignments
$gpxAI['US'][42] = '46';    $gpxAInm['US'][42] = 'Big rig friendly';          //                US
$gpxAI['US'][43] = '2';     $gpxAInm['US'][43] = 'Access fee';                // 36             US
$gpxAI['US'][45] = '19';    $gpxAInm['US'][45] = 'Ticks';                     // 14             US
$gpxAI['US'][46] = '18';    $gpxAInm['US'][46] = 'Snakes';                    // (17)           US
$gpxAI['US'][47] = '39';    $gpxAInm['US'][47] = 'Thorns';                    // 13             US
$gpxAI['US'][50] = '15';    $gpxAInm['US'][50] = 'Available during winter';   // 44             US
$gpxAI['US'][53] = '112';   $gpxAInm['US'][53] = 'GeoHotel';                  //                US
$gpxAI['US'][54] = '17';    $gpxAInm['US'][54] = 'Poisonous plants';          // 16             US
$gpxAI['US'][55] = '117';   $gpxAInm['US'][55] = 'Near a Survey Marker';      //                US
$gpxAI['US'][56] = '126';   $gpxAInm['US'][56] = 'Munzee';                    //                US
$gpxAI['US'][81] = '108';   $gpxAInm['US'][81] = 'Letterbox cache';           // 8              US
$gpxAI['US'][91] = '52';    $gpxAInm['US'][91] = 'Night cache';               // 1              US
$gpxAI['US'][92] = '106';   $gpxAInm['US'][92] = 'OC-only cache';             // 6              US
$gpxAI['US'][94] = '40';    $gpxAInm['US'][94] = 'Stealth required';          //                US
$gpxAI['US'][95] = '124';   $gpxAInm['US'][95] = 'Contains advertising';      //                US
$gpxAI['US'][96] = '147';   $gpxAInm['US'][96] = 'Compass required';          // 47             US

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
