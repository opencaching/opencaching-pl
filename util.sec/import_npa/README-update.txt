Update for the older README.txt file

On loacal machine.
System where this is done on:
Installed fresh Ubuntu 14.04
Installed LAMP-server described on http://www.krizna.com/ubuntu/install-lamp-server-ubuntu-14-04/#apache
Installed phpmyadmin described on http://www.krizna.com/ubuntu/install-phpmyadmin-ubuntu-14-04/
Installed GRASS described on http://grasswiki.osgeo.org/wiki/Compile_and_Install_Ubuntu
Installed extra described on http://www.qgis.org/nl/site/forusers/alldownloads.html#ubuntu

1, Download from http://www.eea.europa.eu/data-and-maps/data/natura-5#tab-gis-data the latest shapefile version Natura2000_end2013_rev1_Shapefile.zip (this version is a ZIP archive 553.7 MB and contains whole Europe) and unzip it in his own map.

2, In terminal go to this unzipped map to convert the data to wgs84 with:
ogr2ogr -f "ESRI Shapefile" -t_srs EPSG:4326 -s_srs EPSG:3035  n2k100l_laea_wgs84.shp  source_shapefile.shp

3, In terminal create database "gis" with command "mysql -u root -p", or in phpmyadmin create a database "gis" and choose from collation "utf8_general_ci.

4, Import to mysql database "gis"
ogr2ogr -skipfailures -f "MySQL" MySQL:"gis,user=root,host=localhost,password=secret" -lco engine=MYISAM n2k100l_laea_wgs84.shp

5, Filter out own countrycode in database "gis".
In phpmyadmin do a "search" via "ms" and type your countrycode.
Change the "Number of rows:" to the total ammount of rows to get them all in one page and select them all and make a export from this.
Upload this gis.sql to a place on the server.

On the server.
6, Import uploaded database "gis" with phpmyadmin.

7, Move data from database "gis" to table "npa_areas" in oc?? database.
In phpmyadmin do sql command:

INSERT INTO `npa_areas`
SELECT
    0,
    NULL,
    `sitename` AS `sitename`,
    `sitecode` AS `sitecode`,
    `sitetype` AS `sitetype`,
    `SHAPE` AS `shape`
  FROM `gis`.`n2k100l_laea_wgs84`
  WHERE
    `sitecode` LIKE 'NL%'   (Where "NL" should be own countrycode.)

Repeat from point 5 for every country that is needed.

8, Change in database in table "caches" all "0" to "1" on column "need_npa_recalc"

9, Generating table content "cache_location".
Open terminal on the server and run "util.sec/cron/modules/cache_npa_areas.class.php". Repeat this command several times by checking column "need_npa_recalc" untill there are no more "1" in this column.

10, Remove the uploaded .sql file(s) to save diskspace.


NB. Somehow this all has problems with converting "sitename" to utf-8. The results ara not in utf-8. When someone has a solution for this please edit this file and share it with us.
Therefore is QGIS installed to see original "sitename" name list in utf-8 format. NL, BE, LU, RO is done by copy and paste into database.
