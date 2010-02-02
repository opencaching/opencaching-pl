1. Contact us to get the shape files

2. Import data with shp2mysql.pl to database bfn

You can find shp2mysql.pl in the mapserver-distrution from http://www.mapserver.org
Copy the script to /usr/local/bin and open it to configure database access parameters.
Depending on the mapserver and mysql version, you need to correct the SQL commands on line 72 and 80 from TYPE=MyISAM to ENGINE=MyISAM

The perl module geo/shapelib is needed. Try using cpan with "install Geo::Shapelib"

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
