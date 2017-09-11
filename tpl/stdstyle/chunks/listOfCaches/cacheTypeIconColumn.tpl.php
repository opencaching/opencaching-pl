<?php

use lib\Objects\GeoCache\GeoCacheCommons;
use lib\Objects\GeoCache\GeoCacheLogCommons;

/** 
  This is column with cache icon.
  $date arg needs to contains:
		- type 	- type of teh cache (for example multi or virtual)
    - status - status of the cache (for example temp-unavailable or archived
    - user_sts - status for current user - for example found or not found etc.
*/


return function (array $data){

    $cacheIconSrc = GeoCacheCommons::CacheIconByType(
        $data['type'], $data['status'], $data['user_sts'] );


    $statusTitle = tr( GeoCacheCommons::CacheTypeTranslationKey($data['type']) );
    $statusTitle .= ', '. tr ( GeoCacheCommons::CacheStatusTranslationKey($data['status']));

    if(!is_null($data['user_sts'])){
        $statusTitle .= ', '. tr(GeoCacheLogCommons::typeTranslationKey($data['user_sts']));
    }

?>
    <img src="<?=$cacheIconSrc?>" class="icon16" alt="<?=$statusTitle?>"
         title="<?=$statusTitle?>">
<?php
};

