<?php

use lib\Objects\GeoCache\GeoCacheCommons;
use lib\Objects\GeoCache\GeoCacheLogCommons;

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

