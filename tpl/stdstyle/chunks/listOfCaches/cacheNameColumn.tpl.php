<?php

use lib\Objects\GeoCache\GeoCacheCommons;

return function (array $data){

?>
    <a href="<?=GeoCacheCommons::GetCacheUrlByWp($data['wp_oc'])?>" target=”_blank”>
      <?=$data['name']?>
    </a>
<?php
};

