<?php

use lib\Objects\CacheSet\CacheSetCommon;
use lib\Objects\CacheSet\CacheSet;

/**
  This is column with cache icon.
  $date arg needs to contains:

  TODO...
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

