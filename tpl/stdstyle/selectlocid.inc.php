<?php

use Utils\Database\XDb;
$locline = '<tr><td width="50px">{nr}.&nbsp;</td><td> <a href="search.php?{urlparams}">{locationname}</a>{secondlocationname}</td></tr>
                            <tr><td width="50px">&nbsp;</td><td><font size="1" color="black">{coords}</font></td></tr>
                            <tr><td width="50px">&nbsp;</td><td style="padding-bottom:3px;"><font size="1" color="#001BBC">{parentlocations}</font></td></tr>';

$secondlocationname = '&nbsp;<font size="1">({secondlocationname})</font>';

function landFromLocid($locid)
{
    if (!is_numeric($locid))
        return '';

    XDb::xMultiVariableQueryValue(
        "SELECT `ld`.`text_val` `land`
        FROM `geodb_textdata` `ct`, `geodb_textdata` `ld`, `geodb_hierarchies` `hr`
        WHERE `ct`.`loc_id`=`hr`.`loc_id`
            AND `hr`.`id_lvl2`=`ld`.`loc_id`
            AND `ct`.`text_type`=500100000
            AND `ld`.`text_locale`='DE'
            AND `ld`.`text_type`=500100000
            AND `ct`.`loc_id`= :1
            AND `hr`.`id_lvl2`!=0",
        0, $locid);
}

function regierungsbezirkFromLocid($locid)
{
    if (!is_numeric($locid))
        return '';

    return XDb::xMultiVariableQueryValue(
        "SELECT `rb`.`text_val` `regierungsbezirk`
        FROM `geodb_textdata` `ct`, `geodb_textdata` `rb`, `geodb_hierarchies` `hr`
        WHERE `ct`.`loc_id`=`hr`.`loc_id`
            AND `hr`.`id_lvl4`=`rb`.`loc_id`
            AND `ct`.`text_type`=500100000
            AND `rb`.`text_type`=500100000
            AND `ct`.`loc_id`= :1
            AND `hr`.`id_lvl4`!=0",
        0, $locid);
}

function landkreisFromLocid($locid)
{
    if (!is_numeric($locid))
        return '';

    return XDb::xMultiVariableQueryValue(
        "SELECT `rb`.`text_val` `regierungsbezirk`
        FROM `geodb_textdata` `ct`, `geodb_textdata` `rb`, `geodb_hierarchies` `hr`
        WHERE `ct`.`loc_id`=`hr`.`loc_id`
            AND `hr`.`id_lvl5`=`rb`.`loc_id`
            AND `ct`.`text_type`=500100000
            AND `rb`.`text_type`=500100000
            AND `ct`.`loc_id`= :1
            AND `hr`.`id_lvl5`!=0",
        0, $locid);
}
