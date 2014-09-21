#!/usr/bin/php -q
<?php
 /***************************************************************************
                                            ./util/gns/gns_import.php
                                            -------------------------
        begin                : Mon October 31 2005
        copyright            : (C) 2005 The OpenCaching Group
        forum contact at     : http://www.opencaching.com/phpBB2

    ***************************************************************************/

 /***************************************************************************

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
    path/to/do-wget-url   util.sec/gns/mkadmtxt.php         mkadmtxt.html
    path/to/do-wget-url   util.sec/gns/mksearchindex.php    mksearchindex.html

    !!! Since this script only works on already downloaded files, there is
    no point in running in from a cron job at this time.

    *** updated to perform an import for PL, BENELUX, RO, so you need to
    *** download the files from the explicit links above.

    ***************************************************************************/

set_time_limit(0);

  $rootpath = '../../';
    require($rootpath . 'lib/clicompatbase.inc.php');

    /* defaults */
    $importfiles = array("pl.txt",
                        "nl.txt", "be.txt", "lu.txt",
                        "ro.txt"
                        ); # first download the file from the URLs above

    /* begin db connect */
    db_connect();
    if ($dblink === false)
    {
        echo 'Unable to connect to database';
        exit;
    }
    /* end db connect */

    sql("TRUNCATE TABLE gns_locations");

    foreach($importfiles as $filename)
        importGns($filename, $dblink);

    function importGns($filename, $dblink)
    {
        echo "Importing '$filename'...\n";
        $file = fopen($filename, "r");
        $cnt = 0;
        while($line = fgets($file, 4096))
        {
            if($cnt++ == 0) // skip first line
                continue;

            $gns =  mb_split("\t", $line);

            $sql = "INSERT IGNORE INTO gns_locations SET
                    rc = '" . sql_escape($gns[0]) . "',
                    ufi = '" . sql_escape($gns[1]) . "',
                    uni = '" . sql_escape($gns[2]) . "',
                    lat = '" . sql_escape($gns[3]) . "',
                    lon = '" . sql_escape($gns[4]) . "',
                    dms_lat = '" . sql_escape($gns[5]) . "',
                    dms_lon = '" . sql_escape($gns[6]) . "',
                    utm = '" . sql_escape($gns[7]) . "',
                    jog = '" . sql_escape($gns[8]) . "',
                    fc = '" . sql_escape($gns[9]) . "',
                    dsg = '" . sql_escape($gns[10]) . "',
                    pc = '" . sql_escape($gns[11]) . "',
                    cc1 = '" . sql_escape($gns[12]) . "',
                    adm1 = '" . sql_escape($gns[13]) . "',
                    adm2 = _utf8'" . sql_escape($gns[14]) . "',
                    dim = '" . sql_escape($gns[15]) . "',
                    cc2 = '" . sql_escape($gns[16]) . "',
                    nt = '" . sql_escape($gns[17]) . "',
                    lc = '" . sql_escape($gns[18]) . "',
                    SHORT_FORM = _utf8'" . sql_escape($gns[19]) . "',
                    GENERIC = _utf8'" . sql_escape($gns[20]) . "',
                    SORT_NAME = _utf8'" . sql_escape($gns[21]) . "',
                    FULL_NAME = _utf8'" . sql_escape($gns[22]) . "',
                    FULL_NAME_ND = _utf8'" . sql_escape($gns[23]) . "',
                    MOD_DATE = '" . sql_escape($gns[24]) . "'";

            if(!$resp = sql($sql, $dblink))
            {
                echo mysql_error($dblink); echo "\n";
            }
        }
        fclose($file);

        echo "$cnt Records imported\n";

        // ein paar Querschläger gleich korrigieren ...
        sql("UPDATE gns_locations SET full_name='Zeluce' WHERE uni=100528 LIMIT 1");
        sql("UPDATE gns_locations SET full_name='Zitaraves' WHERE uni=-2780984 LIMIT 1");
        sql("UPDATE gns_locations SET full_name='Zvabek' WHERE uni=105075 LIMIT 1");
    }
?>
