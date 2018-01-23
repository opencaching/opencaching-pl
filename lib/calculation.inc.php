<?php
/**
 * this is used only by search* scripts
 */

function getCalcDistanceSqlFormula($modEnabled, $lonFrom, $latFrom, $maxDistance,
        $distanceMultiplier = 1, $lonField = 'longitude', $latField = 'latitude',
        $tableName = 'caches', $modLonField = 'longitude', $modLatField = 'latitude',
        $modTableName = 'cache_mod_cords')
{
    if ($modEnabled) {
        return getSqlDistanceFormulaForModCoords(
            $lonFrom, $latFrom, $maxDistance, $distanceMultiplier, $lonField,
            $latField, $tableName, $modLonField, $modLatField, $modTableName);
    } else {
        return getSqlDistanceFormula($lonFrom, $latFrom, $maxDistance, $distanceMultiplier,
                $lonField, $latField, $tableName);
    }
}

//private - not used outside
function getSqlDistanceFormulaForModCoords($lonFrom, $latFrom, $maxDistance,
    $distanceMultiplier = 1, $lonField = 'longitude', $latField = 'latitude',
    $tableName = 'caches', $modLonField = 'longitude', $modLatField = 'latitude',
    $modTableName = 'cache_mod_cords')
{
    $lonFrom = $lonFrom + 0;
    $latFrom = $latFrom + 0;
    $maxDistance = $maxDistance + 0;
    $distanceMultiplier = $distanceMultiplier + 0;

    if (!mb_ereg_match('^[a-zA-Z][a-zA-Z0-9_]{0,59}$', $lonField))
        die('Fatal Error: invalid lonField');
    if (!mb_ereg_match('^[a-zA-Z][a-zA-Z0-9_]{0,59}$', $latField))
        die('Fatal Error: invalid latField');
    if (!mb_ereg_match('^[a-zA-Z][a-zA-Z0-9_]{0,59}$', $tableName))
        die('Fatal Error: invalid tableName');
    if (!mb_ereg_match('^[a-zA-Z][a-zA-Z0-9_]{0,59}$', $modLonField))
        die('Fatal Error: invalid modLonField');
    if (!mb_ereg_match('^[a-zA-Z][a-zA-Z0-9_]{0,59}$', $modLatField))
        die('Fatal Error: invalid modLatField');
    if (!mb_ereg_match('^[a-zA-Z][a-zA-Z0-9_]{0,59}$', $modTableName))
        die('Fatal Error: invalid modTableName');

    $b1_rad = sprintf('%01.5F', (90 - $latFrom) * 3.14159 / 180);
    $l1_deg = sprintf('%01.5F', $lonFrom);

    $lonField = '`' . $tableName . '`.`' . $lonField . '`';
    $latField = '`' . $tableName . '`.`' . $latField . '`';

    $modLonField = '`' . $modTableName . '`.`' . $modLonField . '`';
    $modLatField = '`' . $modTableName . '`.`' . $modLatField . '`';

    $r = 6370 * $distanceMultiplier;

    $retval = 'acos(cos(' . $b1_rad . ') * cos((90-IFNULL(' . $modLatField . ', ' . $latField . ')) * PI() / 180) + sin(' . $b1_rad . ') * sin((90-IFNULL(' . $modLatField . ', ' . $latField . ')) * PI() / 180) * cos((' . $l1_deg . ' - IFNULL(' . $modLonField . ', ' . $lonField . ')) * 3.14159 / 180)) * ' . $r;

    return $retval;
}



function getSqlDistanceFormula($lonFrom, $latFrom, $maxDistance, $distanceMultiplier = 1,
    $lonField = 'longitude', $latField = 'latitude', $tableName = 'caches')
{
    $lonFrom = $lonFrom + 0;
    $latFrom = $latFrom + 0;
    $maxDistance = $maxDistance + 0;
    $distanceMultiplier = $distanceMultiplier + 0;

    if (!mb_ereg_match('^[a-zA-Z][a-zA-Z0-9_]{0,59}$', $lonField))
        die('Fatal Error: invalid lonField');
    if (!mb_ereg_match('^[a-zA-Z][a-zA-Z0-9_]{0,59}$', $latField))
        die('Fatal Error: invalid latField');
    if (!mb_ereg_match('^[a-zA-Z][a-zA-Z0-9_]{0,59}$', $tableName))
        die('Fatal Error: invalid tableName');

    $b1_rad = sprintf('%01.5F', (90 - $latFrom) * 3.14159 / 180);
    $l1_deg = sprintf('%01.5F', $lonFrom);

    $lonField = '`' . $tableName . '`.`' . $lonField . '`';
    $latField = '`' . $tableName . '`.`' . $latField . '`';

    $r = 6370 * $distanceMultiplier;

    $retval = 'acos(cos(' . $b1_rad . ') * cos((90-' . $latField .
        ') * 3.14159 / 180) + sin(' . $b1_rad . ') * sin((90-' . $latField .
        ') * 3.14159 / 180) * cos((' . $l1_deg . '-' . $lonField .
        ') * 3.14159 / 180)) * ' . $r;

    return $retval;
}

function getMaxLat($lon, $lat, $distance, $distanceMultiplier = 1)
{
    return $lat + $distance / (111.12 * $distanceMultiplier);
}

function getMinLat($lon, $lat, $distance, $distanceMultiplier = 1)
{
    return $lat - $distance / (111.12 * $distanceMultiplier);
}

function getMaxLon($lon, $lat, $distance, $distanceMultiplier = 1)
{
    return $lon + $distance * 180 / (abs(sin((90 - $lat) * 3.14159 / 180)) * 6378 * $distanceMultiplier * 3.14159);
}

function getMinLon($lon, $lat, $distance, $distanceMultiplier = 1)
{
    return $lon - $distance * 180 / (abs(sin((90 - $lat) * 3.14159 / 180)) * 6378 * $distanceMultiplier * 3.14159);
}

