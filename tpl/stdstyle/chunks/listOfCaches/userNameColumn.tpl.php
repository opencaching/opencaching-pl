<?php

use lib\Objects\User\UserCommons;

/**
	This is column which displays user name.
  $date arg has to contains:
    - userId - user identifier
    - userName - user nickname
*/

return function (array $data) {
?>
    <a href="<?=UserCommons::GetUserProfileUrl($data['userId'])?>" target=”_blank”>
      <?=$data['userName']?>
    </a>
<?php
};

