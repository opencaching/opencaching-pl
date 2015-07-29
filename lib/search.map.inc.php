<?php

    global $content, $bUseZip, $sqldebug, $usr, $hide_coords, $dbcSearch;
    require_once ('lib/common.inc.php');
    set_time_limit(1800);

    if( !$usr && $hide_coords )
        die();

    $dbc = new dataBase();

    $sql = 'SELECT ';

    if (isset($lat_rad) && isset($lon_rad))
    {
        $sql .= getCalcDistanceSqlFormula($usr !== false, $lon_rad * 180 / 3.14159, $lat_rad * 180 / 3.14159, 0, $multiplier[$distance_unit]) . ' `distance`, ';
    }
    else
    {
        if ($usr === false)
        {
            $sql .= '0 distance, ';
        }
        else
        {
            //get the users home coords
            $sqlstr = "SELECT `latitude`, `longitude` FROM `user` WHERE `user_id`= :1";
            $dbc->multiVariableQuery($sqlstr, $usr['userid'] );
            $record_coords = $dbc->dbResultFetch();

            if ((($record_coords['latitude'] == NULL) || ($record_coords['longitude'] == NULL)) || (($record_coords['latitude'] == 0) || ($record_coords['longitude'] == 0)))
            {
                $sql .= '0 distance, ';
            }
            else
            {
                //TODO: load from the users-profile
                $distance_unit = 'km';

                $lon_rad = $record_coords['longitude'] * 3.14159 / 180;
                $lat_rad = $record_coords['latitude'] * 3.14159 / 180;

                $sql .= getCalcDistanceSqlFormula($usr !== false, $record_coords['longitude'], $record_coords['latitude'], 0, $multiplier[$distance_unit]) . ' `distance`, ';
            }
            $dbc->reset();
            unset($dbc);
        }
    }
    $sql .= '`caches`.`cache_id` `cache_id`, `caches`.`status` `status`, `caches`.`type` `type`, `caches`.`size` `size`, `caches`.`user_id` `user_id`, ';

    if ($usr === false)
    {
        $sql .= ' `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, 0 as cache_mod_cords_id FROM `caches` ';
    }
    else
    {
        $sql .= ' IFNULL(`cache_mod_cords`.`longitude`, `caches`.`longitude`) `longitude`, IFNULL(`cache_mod_cords`.`latitude`, `caches`.`latitude`) `latitude`, IFNULL(cache_mod_cords.id,0) as cache_mod_cords_id
        FROM `caches`
        LEFT JOIN `cache_mod_cords` ON `caches`.`cache_id` = `cache_mod_cords`.`cache_id` AND `cache_mod_cords`.`user_id` = ' . $usr['userid'];
    }
    $sql .= ' WHERE `caches`.`cache_id` IN (' . $sqlFilter . ')';
/*              ,AVG(`caches`.`longitude`) AS avglongitude, AVG(`caches`.`latitude`) AS avglatitude*/

    $sortby = $options['sort'];
    if (isset($lat_rad) && isset($lon_rad) && ($sortby == 'bydistance'))
    {
        $sql .= ' ORDER BY distance ASC';
    }
    else if ($sortby == 'bycreated')
    {
        $sql .= ' ORDER BY date_created DESC';
    }
    else // by name
    {
        $sql .= ' ORDER BY name ASC';
    }

        $sqlstr ='SELECT MAX(`caches`.`longitude`) AS maxlongitude, MAX(`caches`.`latitude`) AS maxlatitude,
                    MIN(`caches`.`longitude`) AS minlongitude, MIN(`caches`.`latitude`) AS minlatitude
                    FROM `caches` WHERE `caches`.`cache_id` IN ('.$sqlFilter.')';
        $dbcSearch->simpleQuery($sqlstr);

        $r = $dbcSearch->dbResultFetch();
        $minlat = $r['minlatitude'];
        $minlon = $r['minlongitude'];
        $maxlat = $r['maxlatitude'];
        $maxlon = $r['maxlongitude'];
        $dbcSearch->reset();

        // temporäre tabelle erstellen
        $dbcSearch->simpleQuery($sql);
        $cnt = 0;
        $hash = uniqid();
        $f = fopen($dynbasepath . "searchdata/".$hash, "w");
        while($r = $dbcSearch->dbResultFetch())
        {

            ++$cnt;
            fprintf($f, "%s\n", $r['cache_id']);
        }
        fclose($f);

        tpl_redirect("cachemap3.php?searchdata=".$hash."&fromlat=".$minlat."&fromlon=".$minlon."&tolat=".$maxlat."&tolon=".$maxlon);
        $dbcSearch->reset();
?>
