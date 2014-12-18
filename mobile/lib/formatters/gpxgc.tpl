<?xml version="1.0" encoding="utf-8"?>
<gpx xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" version="1.0" creator="OpenCachingPL" xsi:schemaLocation="http://www.topografix.com/GPX/1/0 http://www.topografix.com/GPX/1/0/gpx.xsd http://www.groundspeak.com/cache/1/0/1 http://www.groundspeak.com/cache/1/0/1/cache.xsd http://www.gsak.net/xmlv1/5 http://www.gsak.net/xmlv1/5/gsak.xsd" xmlns="http://www.topografix.com/GPX/1/0">
    <name>Cache Listing Generated from Opencaching.pl</name>
    <desc>Cache Listing Generated from Opencaching.pl </desc>
    <author>OpenCaching.PL</author>
    <email>ocpl@opencaching.pl</email>
    <url>http://www.opencaching.pl</url>
    <urlname>Opencaching.pl - Geocaching w Polsce</urlname>
    <time>{$date}T{$time}Z</time>
    <keywords>cache, geocache</keywords>

    {section name=i loop=$znalezione}
        <wpt lat="{$znalezione[i].latitude}" lon="{$znalezione[i].longitude}">
            <time>{$znalezione[i].date_hidden}T00:00:00Z</time>
            <name>{$znalezione[i].wp_oc}</name>
            <desc>{$znalezione[i].name} by {$znalezione[i].owner}, {$znalezione[i].type}</desc>
            <url>http://www.opencaching.pl/viewcache.php?cacheid={$znalezione[i].cache_id}</url>
            <urlname>{$znalezione[i].name}</urlname>
            <sym>Geocache</sym>
            <type>Geocache|{$znalezione[i].type}</type>

            <groundspeak:cache id="{$znalezione[i].cache_id}" available="True" archived="False" xmlns:groundspeak="http://www.groundspeak.com/cache/1/0/1">

                <groundspeak:name>{$znalezione[i].name}</groundspeak:name>
                <groundspeak:placed_by>nubes{$znalezione[i].owner}</groundspeak:placed_by>
                <groundspeak:owner id="{$znalezione[i].user_id}">{$znalezione[i].owner}</groundspeak:owner>
                <groundspeak:type>{$znalezione[i].type}</groundspeak:type>
                <groundspeak:container>{$znalezione[i].size}</groundspeak:container>
                <groundspeak:difficulty>{$znalezione[i].difficulty}</groundspeak:difficulty>
                <groundspeak:terrain>{$znalezione[i].terrain}</groundspeak:terrain>
                <groundspeak:country></groundspeak:country>
                <groundspeak:state></groundspeak:state>
                <groundspeak:short_description html="False">{$znalezione[i].short_desc}</groundspeak:short_description>
                <groundspeak:long_description html="True">{$znalezione[i].short_desc}</groundspeak:long_description>
                <groundspeak:encoded_hints>{$znalezione[i].hint}</groundspeak:encoded_hints>

                <groundspeak:logs>

                    <groundspeak:log id="1">
                        <groundspeak:date>{$date}T{$time}Z</groundspeak:date>
                        <groundspeak:finder id="0">SYSTEM</groundspeak:finder>
                        <groundspeak:text encoded="False">Atrybuty: {$znalezione[i].attr}</groundspeak:text>
                    </groundspeak:log>

                    {section name=j loop=$znalezione[i].logs}
                        <groundspeak:log id="{$znalezione[i].logs[j].id}">
                            <groundspeak:date>{$znalezione[i].logs[j].date}T{$znalezione[i].logs[j].time}Z</groundspeak:date>
                            <groundspeak:finder id="{$znalezione[i].logs[j].user_id}">{$znalezione[i].logs[j].username}</groundspeak:finder>
                            <groundspeak:type>{$znalezione[i].logs[j].type}</groundspeak:type>
                            <groundspeak:text encoded="False">{$znalezione[i].logs[j].text}</groundspeak:text>
                        </groundspeak:log>
                    {/section}

                </groundspeak:logs>

                <groundspeak:travelbugs>
                    {section name=j loop=$znalezione[i].geokrets}
                        <groundspeak:travelbug id="0" ref="0">
                            <groundspeak:name>{$znalezione[i].geokrets[j].name}</groundspeak:name>
                        </groundspeak:travelbug>
                    {/section}
                </groundspeak:travelbugs>

            </groundspeak:cache>
        </wpt>
    {/section}

</gpx>