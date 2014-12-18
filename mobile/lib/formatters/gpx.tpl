<?xml version="1.0" encoding="utf-8"?>
<gpx xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
     xsi:schemaLocation="http://www.topografix.com/GPX/1/0 http://www.topografix.com/GPX/1/0/gpx.xsd http://geocaching.com.au/geocache/1 http://geocaching.com.au/geocache/1/geocache.xsd http://www.gsak.net/xmlv1/5 http://www.gsak.net/xmlv1/5/gsak.xsd"
     xmlns="http://www.topografix.com/GPX/1/0" version="1.0" creator="OpenCachingPL">
    <desc>Cache Listing Generated from Opencaching.pl </desc>
    <author>OpenCaching.PL</author>
    <url>http://www.opencaching.pl</url>
    <urlname>www.opencaching.pl</urlname>
    <time>{$date}T{$time}Z</time>
        {section name=i loop=$znalezione}
        <wpt lat="{$znalezione[i].latitude}" lon="{$znalezione[i].longitude}">
            <time>{$znalezione[i].date_hidden}T00:00:00Z</time>
            <name>{$znalezione[i].wp_oc}</name>
            <desc>{$znalezione[i].name} by {$znalezione[i].owner}, {$znalezione[i].type}</desc>
            <src>www.opencaching.pl</src>
            <url>http://www.opencaching.pl/viewcache.php?cacheid={$znalezione[i].cache_id}</url>
            <urlname>{$znalezione[i].name}</urlname>
            <sym>Geocache</sym>
            <type>Geocache|{$znalezione[i].type}</type>
            <geocache status="{$znalezione[i].status}" xmlns="http://geocaching.com.au/geocache/1">
                <name>{$znalezione[i].name}</name>
                <owner>{$znalezione[i].owner}</owner>
                <locale></locale>
                <state></state>
                <country></country>
                <type>{$znalezione[i].type}</type>
                <container>{$znalezione[i].size}</container>
                <difficulty>{$znalezione[i].difficulty}</difficulty>
                <terrain>{$znalezione[i].terrain}</terrain>
                <summary html="false">{$znalezione[i].short_desc}</summary>
                <description html="true">{$znalezione[i].desc}</description>
                <hints>{$znalezione[i].hint}</hints>
                <licence></licence>
                <logs>
                    <log id="1"><time>{$date}T{$time}Z</time><geocacher>SYSTEM</geocacher><text>Atrybuty: {$znalezione[i].attr}</text></log>

                    {section name=j loop=$znalezione[i].logs}
                        <log id="{$znalezione[i].logs[j].id}">
                            <time>{$znalezione[i].logs[j].date}T{$znalezione[i].logs[j].time}Z</time>
                            <geocacher>{$znalezione[i].logs[j].username}</geocacher>
                            <type>{$znalezione[i].logs[j].type}</type>
                            <text>{$znalezione[i].logs[j].text}</text>
                        </log>
                    {/section}

                </logs>
                <geokrety>
                    {section name=j loop=$znalezione[i].geokrets}
                        <geokret id="0" ref="0">
                            <gkname>{$znalezione[i].geokrets[j].name}</gkname>
                        </geokret>
                    {/section}
                </geokrety>
            </geocache>
        </wpt>
    {/section}

</gpx>