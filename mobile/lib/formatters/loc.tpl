<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<loc version="1.0" src="www.opencaching.pl">

    {section name=i loop=$znalezione}
        <waypoint>
            <name id="{$znalezione[i].wp_oc}"><![CDATA[{$znalezione[i].name} by {$znalezione[i].owner}, {$znalezione[i].type}]]></name>
            <coord lat="{$znalezione[i].latitude}" lon="{$znalezione[i].longitude}"/>
            <type>Geocache</type>
            <link text="Cache Details">http://www.opencaching.pl/viewcache.php?cacheid={$znalezione[i].cache_id}</link>
        </waypoint>
    {/section}

</loc>