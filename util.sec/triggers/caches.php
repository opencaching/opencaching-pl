<?php
 /***************************************************************************
#!php -q
		
		Unicode Reminder メモ

		Ggf. muss die Location des php-Binaries angepasst werden.
		
		Erstellt die Trigger für die Tabelle caches
		
	***************************************************************************/

	$rootpath = '../../';
  require_once($rootpath . 'lib/clicompatbase.inc.php');

  if (!file_exists($rootpath . 'util.sec/mysql_root/sql_root.inc.php'))
		die("\n" . 'install util.sec/mysql_root/sql_root.inc.php' . "\n\n");

  require_once($rootpath . 'util.sec/mysql_root/sql_root.inc.php');

/* begin db connect */
	db_root_connect();
	if ($dblink === false)
	{
		echo 'Unable to connect to database';
		exit;
	}
/* end db connect */

	sql_dropTrigger('cachesAfterInsert');

	sql("CREATE TRIGGER `cachesAfterInsert` AFTER INSERT ON `caches` 
				FOR EACH ROW 
					BEGIN 
						INSERT IGNORE INTO `cache_coordinates` (`cache_id`, `date_modified`, `longitude`, `latitude`) 
						                                VALUES (NEW.`cache_id`, NOW(), NEW.`longitude`, NEW.`latitude`);
						INSERT IGNORE INTO `cache_countries` (`cache_id`, `date_modified`, `country`) 
						                                VALUES (NEW.`cache_id`, NOW(), NEW.`country`);

						UPDATE `user`, (SELECT COUNT(*) AS `hidden_count` FROM `caches` WHERE `user_id`=NEW.`user_id` AND `status` IN (1, 2, 3)) AS `c` SET `user`.`hidden_count`=`c`.`hidden_count` WHERE `user`.`user_id`=NEW.`user_id`;
					END;");

	sql_dropTrigger('cachesAfterUpdate');

	sql("CREATE TRIGGER `cachesAfterUpdate` AFTER UPDATE ON `caches` 
				FOR EACH ROW 
					BEGIN 
						IF NEW.`longitude` != OLD.`longitude` OR NEW.`latitude` != OLD.`latitude` THEN 
							INSERT IGNORE INTO `cache_coordinates` (`cache_id`, `date_modified`, `longitude`, `latitude`) 
								VALUES (NEW.`cache_id`, NOW(), NEW.`longitude`, NEW.`latitude`); 
						END IF; 
						IF NEW.`country` != OLD.`country` THEN 
							INSERT IGNORE INTO `cache_countries` (`cache_id`, `date_modified`, `country`) 
								VALUES (NEW.`cache_id`, NOW(), NEW.`country`); 
						END IF;

						IF NEW.`status` != OLD.`status` OR NEW.`user_id` != OLD.`user_id` THEN
							UPDATE `user`, (SELECT COUNT(*) AS `hidden_count` FROM `caches` WHERE `user_id`=NEW.`user_id` AND `status` IN (1, 2, 3)) AS `c` SET `user`.`hidden_count`=`c`.`hidden_count` WHERE `user`.`user_id`=NEW.`user_id`;
							
							IF NEW.`user_id` != OLD.`user_id` THEN
								UPDATE `user`, (SELECT COUNT(*) AS `hidden_count` FROM `caches` WHERE `user_id`=OLD.`user_id` AND `status` IN (1, 2, 3)) AS `c` SET `user`.`hidden_count`=`c`.`hidden_count` WHERE `user`.`user_id`=OLD.`user_id`;
							END IF;
						END IF;
					END;");

	sql_dropTrigger('cachesAfterDelete');

	sql("CREATE TRIGGER `cachesAfterDelete` AFTER DELETE ON `caches` 
				FOR EACH ROW 
					BEGIN 
						DELETE FROM `cache_coordinates` WHERE `cache_id`=OLD.`cache_id`; 
						DELETE FROM `cache_countries` WHERE `cache_id`=OLD.`cache_id`; 

						UPDATE `user`, (SELECT COUNT(*) AS `hidden_count` FROM `caches` WHERE `user_id`=OLD.`user_id` AND `status` IN (1, 2, 3)) AS `c` SET `user`.`hidden_count`=`c`.`hidden_count` WHERE `user`.`user_id`=OLD.`user_id`;
					END;");

?>
