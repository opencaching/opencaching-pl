<div class="content2-container">
  <div class="content2-pagetitle">
    <img src="tpl/stdstyle/images/blue/stat1.png" class="icon32" alt="">
    {{statistics}}
  </div>

<?php
use Utils\Database\XDb;

global $lang, $rootpath;

if (!isset($rootpath))
    $rootpath = './';

//include template handling
require_once($rootpath . 'lib/common.inc.php');

$tops = array();
?>

<center><table style="padding-left:32px; padding-bottom:32px; line-heigh: 1.6em; font-size: 12px;">
  <tr>
    <td align="center" style="font-size: 16px;"><b>{{index_01}}</b></td>
  </tr>
  <tr>
    <td>{{index_02}}</td>
  <tr>
    <td><img src="images/rating-star.png" alt="Recommendations"> {{index_03}}</td>
  </tr>
  <tr>
    <td><img src="tpl/stdstyle/images/log/16x16-found.png" class="icon16" alt="Found"> {{index_04}}</td>
  </tr>
  <tr>
    <td>{{index_05}}</td>
  </tr>
  <tr>
    <td align="center"><img src="images/tops-formula.png" alt="Formula"></td>
  </tr>
</table></center>

<table cellspacing="2" cellpadding="1" style="margin-left: 10px;line-high: 1.6em; font-size: 12px;" width="97%">
  <tr>
    <td><strong>{{index_06}}</strong></td>
    <td><img src="images/rating-star.png" alt=""></td>
    <td><img src="tpl/stdstyle/images/log/16x16-found.png" class="icon16" alt=""></td>
    <td><strong>{{cache}}</strong></td>
    <td><strong>{{user}}</strong></td>
  </tr>
  <tr>
    <td colspan="5"><hr></td>
  </tr>

<?php
XDb::xSql(
    "CREATE TEMPORARY TABLE topFounds (`cache_id` INT(11) PRIMARY KEY, `founds` INT(11))
    SELECT `caches`.`cache_id`,
            COUNT(`cache_logs`.`cache_id`) `founds`
    FROM `caches`
        LEFT JOIN `cache_logs` ON `caches`.`cache_id`=`cache_logs`.`cache_id`
            AND `cache_logs`.`type`=1
            AND `cache_logs`.`deleted`=0
    GROUP BY `caches`.`cache_id`");

XDb::xSql("UPDATE `topFounds` SET `founds`=0 WHERE ISNULL(`founds`)");

XDb::xSql(
    "CREATE TEMPORARY TABLE topRatings (`cache_id` INT(11) PRIMARY KEY, `ratings` INT(11))
    SELECT `cache_rating`.`cache_id`, COUNT(`cache_rating`.`cache_id`) AS `ratings`
    FROM `cache_rating`
        INNER JOIN `caches` ON `cache_rating`.`cache_id`=`caches`.`cache_id`
    WHERE `cache_rating`.`user_id`!=`caches`.`user_id`
    GROUP BY `cache_rating`.`cache_id`");

XDb::xSql(
    "CREATE TEMPORARY TABLE topResult (`idx` INT(11), `cache_id` INT(11) PRIMARY KEY, `ratings` INT(11), `founds` INT(11))
    SELECT (`topRatings`.`ratings`+1)*(topRatings.`ratings`+1)/(IFNULL(`topFounds`.`founds`,0)/10+1)*100 AS `idx`,
           `topFounds`.`cache_id`, `topRatings`.`ratings`, `topFounds`.`founds`
    FROM `topFounds`
        INNER JOIN `topRatings` ON `topFounds`.`cache_id`=`topRatings`.`cache_id`
        INNER JOIN `caches` ON `topFounds`.`cache_id`=`caches`.`cache_id`
    ORDER BY `idx` DESC");

if ( 10 < XDb::xSimpleQueryValue("SELECT COUNT(*) FROM `topResult`", 0) ) {

    $min_idx = XDb::xSimpleQueryValue(
        "SELECT `idx` FROM `topResult` ORDER BY `idx` DESC LIMIT 99999, 1", 0);

    XDb::xSql(
        "DELETE FROM `topResult` WHERE `idx`< ? ", $min_idx);
}

$rsCaches = XDb::xSql(
    "SELECT `topResult`.`idx`, `topResult`.`ratings`, `caches`.`founds`,
            `topResult`.`founds` AS `foundAfterRating`, `topResult`.`cache_id`,
            `caches`.`name`, `caches`.`wp_oc` AS `wpoc`, `user`.`username`,
            `user`.`user_id` AS `userid`
    FROM `topResult`
        INNER JOIN `caches` ON `topResult`.`cache_id`=`caches`.`cache_id`
        INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id`
    WHERE `topResult`.`cache_id` = `caches`.`cache_id`
        AND `caches`.`type` <> 6
        AND `caches`.`status` = 1
    ORDER BY `idx` DESC");

$items = array();
while ( $rCaches = XDb::xFetchArray($rsCaches) ) {

    $widthB = round(100 * ($rCaches['idx'] / 200) / 1, 0);

    $line = '<tr><td><span class="content-title-noshade txt-blue08" >{index}</span></td><td><span class="content-title-noshade txt-green10">{rating}</span></td><td><span class="content-title-noshade txt-green10">{fbr}</span></td><td><a class="links" href="viewcache.php?cacheid={cacheid}" target="_blank">{name}</a></td><td><a class="links" href="viewprofile.php?userid={userid}" target="_blank">{username}</a></td></tr>';

    $line = str_replace('{index}', $rCaches['idx'], $line);
    $line = str_replace('{rating}', $rCaches['ratings'], $line);
    $line = str_replace('{fbr}', $rCaches['founds'], $line);
    $line = str_replace('{far}', $rCaches['foundAfterRating'], $line);
    $line = str_replace('{username}', $rCaches['username'], $line);
    $line = str_replace('{widthB}', $widthB, $line);
    $line = str_replace('{userid}', $rCaches['userid'], $line);
    $line = str_replace('{cacheid}', $rCaches['cache_id'], $line);
    $line = str_replace('{name}', $rCaches['name'], $line);
    echo $line;
}
XDb::xFreeResults($rsCaches);

XDb::xSql('DROP TEMPORARY TABLE topFounds');
XDb::xSql('DROP TEMPORARY TABLE topRatings');
XDb::xSql('DROP TEMPORARY TABLE topResult');
?>

<tr><td colspan="5"><hr></td></tr>
</table>
</div>