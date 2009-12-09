<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  Display some status information about the server and Opencaching
 ***************************************************************************/

	require('./lib/web.inc.php');
		$tops = array();


				// Alle Caches für diese Gruppe finden
				sql_temp_table('topLocationCaches');
				sql_temp_table('topFounds');
				sql_temp_table('topRatings');
				sql_temp_table('topResult');

               sql("CREATE TEMPORARY TABLE &topLocationCaches (`cache_id` INT(11) PRIMARY KEY) SELECT `caches`.`cache_id` FROM `caches` `caches`.`cache_id` WHERE `caches`.`topratings`>0);

				sql("CREATE TEMPORARY TABLE &topFounds (`cache_id` INT(11) PRIMARY KEY, `founds` INT(11)) 
				     SELECT &topLocationCaches.`cache_id`, 
				            COUNT(`cache_logs`.`cache_id`) `founds` 
				       FROM &topLocationCaches 
				  LEFT JOIN `cache_logs` ON &topLocationCaches.`cache_id`=`cache_logs`.`cache_id` AND 
				            `cache_logs`.`type`=1 AND 
				            `cache_logs`.`date`>'2007-02-21' 
				   GROUP BY &topLocationCaches.`cache_id`");
				sql("UPDATE &topFounds SET `founds`=0 WHERE ISNULL(`founds`)");

				sql("CREATE TEMPORARY TABLE &topRatings (`cache_id` INT(11) PRIMARY KEY, `ratings` INT(11)) SELECT `cache_rating`.`cache_id`, COUNT(`cache_rating`.`cache_id`) AS `ratings` FROM `cache_rating` INNER JOIN &topLocationCaches ON `cache_rating`.`cache_id`=&topLocationCaches.`cache_id` INNER JOIN `caches` ON `cache_rating`.`cache_id`=`caches`.`cache_id` WHERE `cache_rating`.`user_id`!=`caches`.`user_id` GROUP BY `cache_rating`.`cache_id`");

				sql("CREATE TEMPORARY TABLE &topResult (`idx` INT(11), `cache_id` INT(11) PRIMARY KEY, `ratings` INT(11), `founds` INT(11)) 
				     SELECT (&topRatings.`ratings`+1)*(&topRatings.`ratings`+1)/(&topFounds.`founds`+3)*100 AS `idx`, 
				            &topFounds.`cache_id`,
				            &topRatings.`ratings`, 
				            &topFounds.`founds`
				       FROM &topFounds 
				 INNER JOIN &topRatings ON &topFounds.`cache_id`=&topRatings.`cache_id` 
				   ORDER BY `idx` DESC LIMIT 11");

				if (sql_value("SELECT COUNT(*) FROM &topResult", 0) > 10)
				{
					$min_idx = sql_value("SELECT MIN(`idx`) FROM &topResult", 0);
					sql("DELETE FROM &topResult WHERE `idx`<='&1'", $min_idx);
				}

				$rsCaches = sql("SELECT &topResult.`idx`, 
				                        &topResult.`ratings`, 
				                        `caches`.`founds`, 
				                        &topResult.`founds` AS `foundAfterRating`, 
				                        &topResult.`cache_id`, 
				                        `caches`.`name`, 
				                        `caches`.`wp_oc` AS `wpoc`, 
				                        `user`.`username`,
				                        `user`.`user_id` AS `userid`
				                   FROM &topResult
				             INNER JOIN `caches` ON &topResult.`cache_id`=`caches`.`cache_id` 
				             INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id` 
				               ORDER BY `idx` DESC LIMIT 10");
/*
				$items = array();
				while ($rCaches = sql_fetch_assoc($rsCaches))
					$items[] = $rCaches;
				sql_free_result($rsCaches);

				sql_drop_temp_table('topLocationCaches');
				sql_drop_temp_table('topFounds');
				sql_drop_temp_table('topRatings');
				sql_drop_temp_table('topResult');

				$adm2Group['items'] = $items;

				if (count($adm2Group['items']) > 0)
					$adm1Group['adm2'][] = $adm2Group;

				$adm2Group = array();
			
			
			sql_free_result($rsAdm2);

			if (count($adm1Group['adm2']) > 0)
				$tops[] = $adm1Group;

		sql_free_result($rsAdm1);

		$tpl->assign('tops', $tops);
*/
?>
