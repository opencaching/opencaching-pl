<?php

  require('../lib/web.inc.php');
  sql('USE `tnke4`');

  echo '<table>' . "\n";

  $rs = sql('SELECT COUNT(`cache_logs`.`user_id`) `count`, `user`.`username` `username` FROM `cache_logs` INNER JOIN `user` ON (`user`.`user_id`=`cache_logs`.`user_id`) WHERE `cache_logs`.`deleted`=0 AND `cache_logs`.`type`=1 GROUP BY `cache_logs`.`user_id` ORDER BY `count` DESC LIMIT 25');
  while ($r = mysql_fetch_array($rs))
  {
    echo '<tr><td>' . $r['username'] . '</td><td>' . $r['count'] . '<td></tr>' . "\n";
  }
  mysql_free_result($rs);

  echo '</table>' . "\n";
?>

