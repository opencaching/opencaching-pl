<?php
/*******************************************************************************
 * ajaxGetRegionsByCountryCode.php
 *
 * retreive regions of specified country from db, generate option list.
 * Maybe in future refactore it just to return region list (array), and build
 * html by calling js function side.
 *
 *****/
$rootpath = __DIR__.DIRECTORY_SEPARATOR;
require_once $rootpath.'lib/common.inc.php';
require_once __DIR__.'/lib/db.php';

$db=new dataBase;
$countryCode = addslashes($_REQUEST['countryCode']);
$selectedRegion = $_REQUEST['selectedRegion'];

$query = "SELECT `code`, `name` FROM `nuts_codes` WHERE `code` LIKE '".$countryCode."__' ORDER BY `name` COLLATE utf8_polish_ci ASC";
$db->simpleQuery($query);
$regons = $db->dbResultFetchAll();
if(count($regons) == 0){
    if(isset($_REQUEST['searchForm']) && $_REQUEST['searchForm']==1){
        $regionoptions = '<option value="">'.tr('search01').'</option>';
    } else {
        $regionoptions = '<option value="-1">-</option>';
    }
} else {
    if(isset($_REQUEST['searchForm']) && $_REQUEST['searchForm']==1){
        $regionoptions = '<option value="">'.tr('search01').'</option>';
    } else {
        $regionoptions = '<option value="0">'.tr('select_regions').'</option>';
    }
    foreach ($regons as $record) {
        if ($record['code'] == $selectedRegion)
            $regionoptions .= '<option value="'.htmlspecialchars($record['code'],ENT_COMPAT,'UTF-8').'" selected="selected" >' . htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8') . '</option>';
        else
            $regionoptions .= '<option value="' . htmlspecialchars($record['code'], ENT_COMPAT, 'UTF-8') . '">' . htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8') . '</option>';

        $regionoptions .= "\n";
    }
}

echo $regionoptions;
?>