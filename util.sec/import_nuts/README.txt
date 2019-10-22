
UPDATE: 01.11.2016 (kojoty):

- NUTS source data: http://ec.europa.eu/eurostat/web/gisco/geodata/reference-data/administrative-units-statistical-units/nuts#nuts13
- download "shape" package

Conversion NUTS layers data -> OC nuts_layers db tables:
- ogr2ogr - command line tool - part of GDAL(http://www.gdal.org/ogr2ogr.html)
- to convert layers use command like:

  ogr2ogr -f MySQL MySQL:<your-tmp-db-name>,host=<your-host>,user=<your-db-user>,password=<your-db-pass> ./data/NUTS_RG_03M_2013.shp

  as a result you should got db with raw layers - it needs to be imported to OC nuts_layers by something like:

  INSERT nuts_layer(level, code, shape) SELECT stat_levl_, nuts_id, geomfromtext(astext(shape)) FROM <your-tmp0db-name>.nuts_rg_03m_2013

  geomfromtext(astext(shape)) was necessary because by default POLIGONS has SRID=1 - after it SRID = 0

Conversion of NUTS codes -> OC nuts_codes db table:
- util.sec/import_nuts/codes.php script can be used to import codes from csv file (NUTS_AT...csv file)
- as a workaroud for chars encoding issues I used openoffice-calc to open dbf file and export data to csv file with UTF-8 encoding and then import proper data to OC db

After everything cache_location table should be recreated...

--------------------------------------------------------------------

OLD VERSION:

1. Download and unpack NUTS_RG_03M_2003 to data directory

http://epp.eurostat.ec.europa.eu/portal/page/portal/gisco/popups/references/administrative_units_statistical_units_1
(1:3 Mill.)

2. Import data with shp2mysql.pl to database gis

You can find shp2mysql.pl in the mapserver-distrution from http://mapserver.gis.umn.edu/download/current/
Copy the script to /usr/local/bin and open it to configure database access parameters. Depending on the mapserver and mysql version, you need to correct the SQL commands on line 72 and 80 from TYPE=MyISAM to ENGINE=MyISAM

The perl module geo/shapelib is needed. Try using cpan with "install Geo:Shapelib"

You need an empty database called "gis" to import the data in step 3

Now go to the directory util2/import/nuts/data and execute the following command:

shp2mysql.pl NUTS_RG_03M_2003

With this command shp2mysql will import the unpacked shape file to the gis-database. All needed tables will be created by shp2mysql. The import can take a few minutes ... it generates mysql tables with ~128 MB

3. Call util2/import/nuts/import_data.php

With calling this command, the GIS data is copied from database gis to the oc table nuts_layer.

4. Call util2/import/nuts/codes.php

With calling this command, the GIS data is copied from database gis to the oc table nuts_codes.

5. (optional) Cleanup

You can now drop the table gis if you want.

6. Generating table content cache_location

Now execute util2/cron/runcron.php

If there is already content in table cache_location, you have to truncate the table to regenerete the entries. Refilling this table can take some time.
