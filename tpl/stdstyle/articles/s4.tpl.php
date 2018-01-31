<?php
use Utils\Database\XDb;

    $rootpath = './';
    require_once($rootpath . 'lib/common.inc.php');
?>

<div class="content2-container">
<p class="content-title-noshade-size3">{{Stats_t4_01}}</p>
<table class="table full-width">
  <thead>
    <tr>
      <th class="align-center">{{Position}}</th>
      <th class="align-center">{{Stats_t4_03}}</th>
      <th>{{Stats_t4_04}}</th>
    </tr>
  </thead>
  <tbody>

<?php
$results = XDb::xSql(
    "SELECT `caches`.`founds` AS `count`, `caches`.`name`, `caches`.`cache_id`, `user`.`username`, `user`.`user_id`
    FROM `caches`
        INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id`
    WHERE `caches`.`type` NOT IN (4, 5, 6) ORDER BY `count` DESC, `caches`.`name` ASC");

$position = 0;
$prevCount = 0;
while ($result = XDb::xFetchArray($results)) {
    if ($result['count'] != $prevCount ) {
        $position++;
        $prevCount = $result['count'];
        if ($position == 1) {
            echo '<tr>';
        } elseif ($position % 2 == 0) {
            echo '</td></tr><tr class="element-even">';
        } else {
            echo '</td></tr><tr>';
        }
        echo "<td class=\"align-center\">" . $position . "</td><td class=\"align-center\">" .  $result['count'] . "</td><td>";
    } else {
        echo " | ";
    }
    echo '<a href="/viewcache.php?cacheid=' . $result['cache_id'] . '" class="links">' . $result['name'] . '</a> (<a href="/viewprofile.php?userid='. $result['user_id'] .'" class="links">' . $result['username'] . '</a>)';
}
?>
      </td>
    </tr>
  </tbody>
</table>
</div>