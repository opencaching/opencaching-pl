#!/usr/bin/php -q
<?php
/**
 * 19.04.2016, kojoty
 *
 * This script is obsolate and unmaintenance!!!
 *
 *
 */


/* * *************************************************************************

  This script imports data downloaded from GEOnet Names Server (GNS) into the
  table gns_locations.
  Homepage:       http://earth-info.nga.mil/gns/html/
  Download links by country:
  http://earth-info.nga.mil/gns/html/namefiles.htm
  Direct links:
  PL: http://earth-info.nga.mil/gns/html/cntyfile/pl.zip
  NL: http://earth-info.nga.mil/gns/html/cntyfile/nl.zip
  BE: http://earth-info.nga.mil/gns/html/cntyfile/be.zip
  LU: http://earth-info.nga.mil/gns/html/cntyfile/lu.zip
  RO: http://earth-info.nga.mil/gns/html/cntyfile/ro.zip

  This script only imports existing data in current directory.
  You must manually download the data files in this directory, extract the files
  from the archives, and then run this script on your own server like this:
  path/to/do-wget-url   util.sec/gns/gns_import.php       gns_import.html
  path/to/do-wget-url   util.sec/gns/mkadmtxt2.php        mkadmtxt.html
  path/to/do-wget-url   util.sec/gns/mksearchindex.php    mksearchindex.html

  !!! Since this script only works on already downloaded files, there is
  no point in running in from a cron job at this time.

 * ** updated to perform an import for PL, BENELUX, RO, so you need to
 * ** download the files from the explicit links above.

 * ************************************************************************* */
header('Content-Type: text/plain');
set_time_limit(0);

$rootpath = '../../';

/* defaults */
$importfiles = array("pl.txt", "pl_administrative_a.txt",
    "nl.txt", "nl_administrative_a.txt",
    "be.txt", "be_administrative_a.txt",
    "lu.txt", "lu_administrative_a.txt",
    "ro.txt", "ro_administrative_a.txt"
); # first download the file from the URLs above


sql("DROP TABLE IF EXISTS `gns_locations` ");

// the columns reflect one-to-one file structure
// there may be additional columns at the end of the table
sql("CREATE TABLE `gns_locations` (
        `RC` tinyint(4) NOT NULL DEFAULT '0',
        `UFI` int(11) NOT NULL DEFAULT '0',
        `UNI` int(11) NOT NULL DEFAULT '0',
        `LAT` double NOT NULL DEFAULT '0',
        `LON` double NOT NULL DEFAULT '0',
        `DMS_LAT` int(11),
        `DMS_LONG` int(11),
        `MGRS` varchar(4),
        `JOG` varchar(7),
        `FC` char(1),
        `DSG` varchar(5),
        `PC` tinyint(4),
        `CC1` char(2),
        `ADM1` char(2),
        `POP` varchar(200),
        `ELEV` int(11),
        `CC2` char(2),
        `NT` char(1),
        `LC` char(2),
        `SHORT_FORM` varchar(128),
        `GENERIC` varchar(128),
        `SORT_NAME` varchar(200),
        `FULL_NAME` varchar(200) COLLATE utf8_polish_ci,
        `FULL_NAME_ND` varchar(200),
        `SORT_NAME_RG` varchar(200),
        `FULL_NAME_RG` varchar(200),
        `FULL_NAME_ND_RG` varchar(200),
        `NOTE` varchar(200),
        `MODIFY_DATE` date,
        `DISPLAY` varchar(200),
        `NAME_RANK` int(11),
        `NAME_LINK` int(11),
        `TRANSL_CD` varchar(200),
        `NM_MODIFY_DATE` date,
        `ADMTXT1` varchar(120) COLLATE utf8_polish_ci,
        `ADMTXT3` varchar(120) COLLATE utf8_polish_ci,
        `ADMTXT4` varchar(120) COLLATE utf8_polish_ci,
        `ADMTXT2` varchar(120) COLLATE utf8_polish_ci,
        PRIMARY KEY (`uni`),
        KEY `ufi` (`ufi`),
        KEY `key1` (`DSG`, `CC1`,`ADM1`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
    ");

// TODO: The collate parameter should be instalation dependent.

foreach ($importfiles as $filename)
    importGns($filename);

function importGns($filename)
{
    if (isset($_GET['basepath'])) {
        $filename = $_GET['basepath'] . '/' . $filename;
    }
    echo "Importing '$filename'...\n";
    $file = fopen($filename, "r");
    if (!$file) {
        return;
    }
    $columns = mb_split("[\t ,]", "RC,UFI,UNI,LAT,LON,DMS_LAT,DMS_LONG,MGRS,JOG,FC,DSG,PC,CC1,ADM1,POP,ELEV,CC2,NT,LC,SHORT_FORM,GENERIC,SORT_NAME,FULL_NAME,FULL_NAME_ND,SORT_NAME_RG,FULL_NAME_RG,FULL_NAME_ND_RG,NOTE,MODIFY_DATE,DISPLAY,NAME_RANK,NAME_LINK,TRANSL_CD,NM_MODIFY_DATE");
    $utf_columns = mb_split("[\t ,]", "SHORT_FORM,GENERIC,SORT_NAME,FULL_NAME,FULL_NAME_ND,SORT_NAME_RG,FULL_NAME_RG,FULL_NAME_ND_RG,NOTE");
    $line_cnt = 0;
    $cnt = 0;
    while ($line = fgets($file, 4096)) {
        if ($line_cnt++ == 0) // skip first line
            continue;

        $gns = mb_split("\t", $line);
        $sql = "INSERT IGNORE INTO gns_locations SET";
        $is_first = true;
        for ($i = 0; $i < count($gns); $i++) {
            $item = $gns[$i];
            if ($item !== '') {
                if ($is_first) {
                    $sql .= "\n";
                    $is_first = false;
                } else {
                    $sql .= ",\n";
                }

                $column_name = $columns[$i];
                $is_utf8 = in_array($column_name, $utf_columns) ? '_utf8' : '';
                $sql .= "\t`$column_name` = $is_utf8'" . mysql_real_escape_string($item) . "'";
            }
        }

        if (!$resp = sql($sql)) {
            echo mysql_error();
            echo "\n";
        } else {
            $cnt++;
        }
    }
    fclose($file);

    echo "$line_cnt lines, $cnt records imported\n";
}
?>
