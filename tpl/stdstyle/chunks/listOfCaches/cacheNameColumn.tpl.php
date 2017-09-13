<?php

use lib\Objects\GeoCache\GeoCacheCommons;

/**
	This is column which displays cache name.
  $date arg has to contains:
    - wp_oc - OC waypoint for example: OP1234
    - name - name of teh cache
*/

return function (array $data){

?>
    <a href="<?=GeoCacheCommons::GetCacheUrlByWp($data['wp_oc'])?>" target=”_blank”>
      <?=$data['name']?>
    </a>
<?php
};

