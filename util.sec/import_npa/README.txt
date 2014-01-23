

1. Download from http://www.eea.europa.eu/data-and-maps/data/natura-2000 and unpack Natura2000_Public_end2009_100KLAEA.zip (ZIP archive  120.32 MB)  to data directory


2. Import data with ogr2ogr utlites http://www.gdal.org to database gis

first convert cooridantes to wgs84
ogr2ogr -f "ESRI Shapefile" -t_srs EPSG:4326 -s_srs EPSG:3035  n2k100l_laea_wgs84.shp  source_shapefile.shp

import to mysql database "gis"
ogr2ogr -f "MySQL" MySQL:"gis,user=root,host=localhost,password=secret" -lco engine=MYISAM n2k100k_laea_wgs84.shp

3. Call util.sec/import-npa/import_npa.php (please change in sql command ... WHERE `sitecode` LIKE 'PL%' from PL to your country code

With calling this command, the GIS data is copied from database gis to the oc table npa_areas.

4. (optional) Cleanup

You can now drop the table gis if you want.

6. Generating table content cache_location

Now execute util.sec/cron/modules/cache_npa_areas.class.php

If there is already content in table cache_npa_areas, you have to truncate the table to regenerete the entries. Refilling this table can take some time.
