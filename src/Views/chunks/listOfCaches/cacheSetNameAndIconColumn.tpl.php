<?php

use src\Models\CacheSet\CacheSetCommon;
use src\Models\CacheSet\CacheSet;

/**
 * This is column with cache icon.
 * $date arg needs to contains:
 *
 * $row['type'] => '',
 * $row['id'] => '',
 * $row['name'] => '',
 *
*/


return function (array $data){

    $iconSrc = CacheSetCommon::GetTypeIcon($data['type']);
    $iconTitle = tr(CacheSetCommon::GetTypeTranslationKey($data['type']));
    $cacheSetUrl = CacheSet::getCacheSetUrlById($data['id']);

?>
    <img src="<?=$iconSrc?>" class="icon16" alt="<?=$iconTitle?>" title="<?=$iconTitle?>">
    <a href="<?=$cacheSetUrl?>" target=”_blank”>
      <?=$data['name']?>
    </a>
<?php
};
