<?php

global $rootpath;
require_once($rootpath . 'lib/common.inc.php');

// sitename and slogan international handling
$nodeDetect = substr($absolute_server_URI, - 3, 2);

$kmlHead = '<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://earth.google.com/kml/2.0">
    <Document>
        <!-- ACTIVE cache icons =========================================== -->
        <Style id="traditional">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/traditional_kml.png</href>
                </Icon>
            </IconStyle>
        </Style>
        <Style id="multi">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/multi_kml.png</href>
                </Icon>
            </IconStyle>
        </Style>
        <Style id="quiz">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/quiz_kml.png</href>
                </Icon>
            </IconStyle>
        </Style>
        <Style id="virtual">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/virtual_kml.png</href>
                </Icon>
            </IconStyle>
        </Style>
        <Style id="webcam">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/webcam_kml.png</href>
                </Icon>
            </IconStyle>
        </Style>
        <Style id="event">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/event_kml.png</href>
                </Icon>
            </IconStyle>
        </Style>
        <Style id="moving">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/moving_kml.png</href>
                </Icon>
            </IconStyle>
        </Style>
        <Style id="owncache">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/owncache_kml.png</href>
                </Icon>
            </IconStyle>
        </Style>
        <Style id="podcache">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/podcache_kml.png</href>
                </Icon>
            </IconStyle>
        </Style>
        <Style id="unknown">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/unknown_kml.png</href>
                </Icon>
            </IconStyle>
        </Style>

        <!-- DISABLED cache icons ========================================= -->
        <Style id="traditional-disabled">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/traditional_kml-disabled.png</href>
                </Icon>
            </IconStyle>
        </Style>
        <Style id="multi-disabled">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/multi_kml-disabled.png</href>
                </Icon>
            </IconStyle>
        </Style>
        <Style id="quiz-disabled">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/quiz_kml-disabled.png</href>
                </Icon>
            </IconStyle>
        </Style>
        <Style id="virtual-disabled">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/virtual_kml-disabled.png</href>
                </Icon>
            </IconStyle>
        </Style>
        <Style id="webcam-disabled">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/webcam_kml-disabled.png</href>
                </Icon>
            </IconStyle>
        </Style>
        <Style id="event-disabled">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/event_kml-disabled.png</href>
                </Icon>
            </IconStyle>
        </Style>
        <Style id="moving-disabled">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/moving_kml-disabled.png</href>
                </Icon>
            </IconStyle>
        </Style>
        <Style id="owncache-disabled">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/owncache_kml-disabled.png</href>
                </Icon>
            </IconStyle>
        </Style>
        <Style id="podcache-disabled">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/podcache_kml-disabled.png</href>
                </Icon>
            </IconStyle>
        </Style>
        <Style id="unknown-disabled">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/unknown_kml-disabled.png</href>
                </Icon>
            </IconStyle>
        </Style>

        <!-- ARCHIVED cache icons =========================================== -->
        <Style id="traditional-archived">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/traditional_kml-archived.png</href>
                </Icon>
            </IconStyle>
        </Style>
        <Style id="multi-archived">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/multi_kml-archived.png</href>
                </Icon>
            </IconStyle>
        </Style>
        <Style id="quiz-archived">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/quiz_kml-archived.png</href>
                </Icon>
            </IconStyle>
        </Style>
        <Style id="virtual-archived">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/virtual_kml-archived.png</href>
                </Icon>
            </IconStyle>
        </Style>
        <Style id="webcam-archived">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/webcam_kml-archived.png</href>
                </Icon>
            </IconStyle>
        </Style>
        <Style id="event-archived">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/event_kml-archived.png</href>
                </Icon>
            </IconStyle>
        </Style>
        <Style id="moving-archived">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/moving_kml-archived.png</href>
                </Icon>
            </IconStyle>
        </Style>
        <Style id="owncache-archived">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/owncache_kml-archived.png</href>
                </Icon>
            </IconStyle>
        </Style>
        <Style id="podcache-archived">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/podcache_kml-archived.png</href>
                </Icon>
            </IconStyle>
        </Style>
        <Style id="unknown-archived">
            <IconStyle>
                <Icon>
                    <href>{site_uri}tpl/stdstyle/images/cache/kml/unknown_kml-archived.png</href>
                </Icon>
            </IconStyle>
        </Style>
        
        <Folder>
            <Name>' . convert_string($site_name) . '</Name>
            <Open>0</Open>
';

$kmlHead = str_replace('{site_uri}', $absolute_server_URI, $kmlHead);

$kmlLine = '            <Placemark>
                <description>
                    <![CDATA[
                        <a href="' . $absolute_server_URI . 'viewcache.php?wp={cache_wp}">' . tr('search_kml_01') . '</a><br />
                        ' . tr('search_kml_02') . ' {username}<br />
                        &nbsp;<br />
                        <table cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td>{typeimgurl}</td>
                                <td>&nbsp;</td>
                                <td>' . tr('search_kml_03') . ' <b>{type}</b><br />
                                ' . tr('search_kml_04') . ' <b>{size}</b></td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                ' . tr('search_kml_05') . ' <b>{difficulty}</b> ' . tr('search_kml_06') . ' 5.0<br />
                                ' . tr('search_kml_07') . ' <b>{terrain}</b> ' . tr('search_kml_06') . ' 5.0
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">' . tr('search_kml_08') . ' <b style="{status-style}">{status}</b>
                                </td>
                            </tr>
                        </table>
                    ]]>
                </description>
                <name>{mod_suffix}{name}</name>
                <LookAt>
                    <longitude>{lon}</longitude>
                    <latitude>{lat}</latitude>
                    <range>5000</range>
                    <tilt>0</tilt>
                    <heading>0</heading>
                </LookAt>
                <styleUrl>#{icon}</styleUrl>
                <Point>
                    <coordinates>{lon},{lat},0</coordinates>
                </Point>
            </Placemark>
';

$kmlFoot = '
        </Folder>
    </Document>
</kml>';

// (unused ?)
$kmlTimeFormat = 'Y-m-d\TH:i:s\Z';

$kmlTypeIMG = '<img src="{site_uri}tpl/stdstyle/images/cache/{type}.png" alt="{type_text}" title="{type_text}" />';
$kmlTypeIMG = str_replace('{site_uri}', $absolute_server_URI, $kmlTypeIMG);

// ************************************************************************
// Geocache status

$kmlAvailable[0] = 'False';     // OC: UNDEFINED
$kmlAvailable[1] = 'True';      // OC: STATUS_READY
$kmlAvailable[2] = 'False';     // OC: STATUS_UNAVAILABLE
$kmlAvailable[3] = 'False';     // OC: STATUS_ARCHIVED
$kmlAvailable[4] = 'False';     // OC: STATUS_WAITAPPROVERS
$kmlAvailable[5] = 'False';     // OC: STATUS_NOTYETAVAILABLE
$kmlAvailable[6] = 'False';     // OC: STATUS_BLOCKED

$kmlArchived[0] = 'False';      // OC: UNDEFINED
$kmlArchived[1] = 'False';      // OC: STATUS_READY
$kmlArchived[2] = 'False';      // OC: STATUS_UNAVAILABLE
$kmlArchived[3] = 'True';       // OC: STATUS_ARCHIVED
$kmlArchived[4] = 'False';      // OC: STATUS_WAITAPPROVERS
$kmlArchived[5] = 'False';      // OC: STATUS_NOTYETAVAILABLE
$kmlArchived[6] = 'True';       // OC: STATUS_BLOCKED
    
// ************************************************************************
// Geocache type

// Cache types (as short_name)
// Note: these names should be defined with cache types. See well_defined project.
$kmlType[1] = 'unknown';            // OC: TYPE_OTHERTYPE
$kmlType[2] = 'traditional';        // OC: TYPE_TRADITIONAL
$kmlType[3] = 'multi';              // OC: TYPE_MULTICACHE
$kmlType[4] = 'virtual';            // OC: TYPE_VIRTUAL
$kmlType[5] = 'webcam';             // OC: TYPE_WEBCAM
$kmlType[6] = 'event';              // OC: TYPE_EVENT
// OC specific cache types 
$kmlType[7] = 'quiz';               // OC: TYPE_QUIZ
$kmlType[8] = 'moving';             // OC: TYPE_MOVING
$kmlType[9] = 'podcache';           // OC: TYPE_GEOPATHFINAL
$kmlType[10] = 'owncache';          // OC: TYPE_OWNCACHE

// OC type names
// Note: these names should be defined with cache types. See well_defined project.
$kmlGeocacheTypeText[1] = 'Unknown Cache';
$kmlGeocacheTypeText[2] = 'Traditional Cache';
$kmlGeocacheTypeText[3] = 'Multi-Cache';
$kmlGeocacheTypeText[4] = 'Virtual Cache';
$kmlGeocacheTypeText[5] = 'Webcam Cache';
$kmlGeocacheTypeText[6] = 'Event Cache';
$kmlGeocacheTypeText[7] = 'Puzzle Cache';
$kmlGeocacheTypeText[8] = 'Moving Cache';
$kmlGeocacheTypeText[9] = 'Podcast cache';
$kmlGeocacheTypeText[10] = 'Own cache';

// ************************************************************************
// Waypoints

// (unused)
$wptType[0] = 'Information';        // OC: UNDEFINED
$wptType[1] = 'Flag, Green';        // OC: TYPE_PHYSICAL
$wptType[2] = 'Flag, Blue';         // OC: TYPE_VIRTUAL
$wptType[3] = 'Flag, Red';          // OC: TYPE_FINAL
$wptType[4] = 'Waypoint';           // OC: TYPE_INTERESTING
$wptType[5] = 'Parking Area';       // OC: TYPE_PARKING
