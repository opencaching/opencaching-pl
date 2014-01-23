<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder ??
 *
 *  Display some status information about the server and Opencaching
 ***************************************************************************/

    require('./lib/common.inc.php');

    $tops = array();
    echo '<center><table><tr><td align=center><font size=+0><b>Ranking skrzynek wg liczonego indeksu</b></font><br />(w nawiasie liczba znalezien po otrzymaniu rekomendacji)</td></tr>';
    echo '<tr><td><center><table bgcolor=white><tr><td align="right">Indeks</td><td align="center"><img src="/images/rating-star.png" border="0" alt="" /></td><td align="center"><img src="/tpl/stdstyle/images/log/16x16-found.png" class="icon16" alt="" /></td><td></td></tr>';

  mysql_query("SET NAMES 'utf8'");
    // Alle Caches für diese Gruppe finden
    sql("CREATE TEMPORARY TABLE topFounds (`cache_id` INT(11) PRIMARY KEY, `founds` INT(11))
         SELECT `caches`.`cache_id`,
                COUNT(`cache_logs`.`cache_id`) `founds`
           FROM `caches`
      LEFT JOIN `cache_logs` ON `caches`.`cache_id`=`cache_logs`.`cache_id` AND
                `cache_logs`.`type`=1 AND
                `cache_logs`.`date`>'2007-02-21' AND
                `cache_logs`.`deleted`=0
       GROUP BY `caches`.`cache_id`");
    sql("UPDATE `topFounds` SET `founds`=0 WHERE ISNULL(`founds`)");

    sql("CREATE TEMPORARY TABLE topRatings (`cache_id` INT(11) PRIMARY KEY, `ratings` INT(11)) SELECT `cache_rating`.`cache_id`, COUNT(`cache_rating`.`cache_id`) AS `ratings` FROM `cache_rating` INNER JOIN `caches` ON `cache_rating`.`cache_id`=`caches`.`cache_id` WHERE `cache_rating`.`user_id`!=`caches`.`user_id` GROUP BY `cache_rating`.`cache_id`");

    sql("CREATE TEMPORARY TABLE topResult (`idx` INT(11), `cache_id` INT(11) PRIMARY KEY, `ratings` INT(11), `founds` INT(11))
         SELECT (`topRatings`.`ratings`+1)*(topRatings.`ratings`+1)/(`topFounds`.`founds`+`caches`.`founds`/10+3)*100 AS `idx`,
                `topFounds`.`cache_id`,
                `topRatings`.`ratings`,
                `topFounds`.`founds`
           FROM `topFounds`
     INNER JOIN `topRatings` ON `topFounds`.`cache_id`=`topRatings`.`cache_id`
     INNER JOIN `caches` ON `topFounds`.`cache_id`=`caches`.`cache_id`
       ORDER BY `idx` DESC LIMIT 25");

    if (sqlValue("SELECT COUNT(*) FROM `topResult`", 0) > 20)
    {
        $min_idx = sqlValue("SELECT `idx` FROM `topResult` ORDER BY `idx` DESC LIMIT 19, 1", 0);
        sql("DELETE FROM `topResult` WHERE `idx`<'&1'", $min_idx);
    }

  mysql_query("SET NAMES 'utf8'");
    $rsCaches = sql("SELECT `topResult`.`idx`,
                            `topResult`.`ratings`,
                            `caches`.`founds`,
                            `topResult`.`founds` AS `foundAfterRating`,
                            `topResult`.`cache_id`,
                            `caches`.`name`,
                            `caches`.`wp_oc` AS `wpoc`,
                            `user`.`username`,
                            `user`.`user_id` AS `userid`
                       FROM `topResult`
                 INNER JOIN `caches` ON `topResult`.`cache_id`=`caches`.`cache_id`
                 INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id`
                   ORDER BY `idx` DESC");

    $items = array();
    while ($rCaches = sql_fetch_assoc($rsCaches))
    {
$widthB = round(100 * ($rCaches[idx]/200)/1,0);

//  echo $rCaches['idx'] . ' ' . $rCaches['name'] . '<br />';
//  $line = '<tr><td><a href=http://www.opencaching.pl/viewcache.php?cacheid={cacheid} target=_blank>{name}</a> (<b>{username}</b>)</td><td align=right>&nbsp;(<b>{count}</b>)&nbsp;</td><td><img src=/graphs/images/leftbar.gif /><img src=/graphs/images/mainbar.gif height=14 width={widthB} /><img src=/graphs/images/rightbar.gif /> </td> </tr>';
    $line = '<tr><td width="40px" align="right">{index}</td><td width="40px" align="center">{rating}</td><td  width="60px" align="center">{fbr}&nbsp;({far})</td><td><a href=http://www.opencaching.pl/viewcache.php?cacheid={cacheid} target=_blank>{name}</a> &nbsp;({username})</td></tr>';
        $line = str_replace('{index}', $rCaches[idx], $line);
        $line = str_replace('{rating}', $rCaches[ratings], $line);
        $line = str_replace('{fbr}', $rCaches[founds], $line);
        $line = str_replace('{far}', $rCaches[foundAfterRating], $line);
        $line = str_replace('{username}', $rCaches[username], $line);
        $line = str_replace('{widthB}', $widthB, $line);
        $line = str_replace('{cacheid}', $rCaches[cache_id], $line);
        $line = str_replace('{name}', $rCaches[name], $line);
        echo $line;
    }
    sql_free_result($rsCaches);

    sql('DROP TEMPORARY TABLE topFounds');
    sql('DROP TEMPORARY TABLE topRatings');
    sql('DROP TEMPORARY TABLE topResult');
    echo '</table></td></tr><tr><td>Wzór na Indeks = (a+1)* (a+1)/(b+3) *100<br />a = liczba rekomendacji<br />b = liczba znalezien po 21.02.2007(data od kiedy można przydzielać rekomendacje)</td></tr></table></center><br />';
?>
