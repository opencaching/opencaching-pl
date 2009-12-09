<?php
 /***************************************************************************
#!php -q
		
		Unicode Reminder メモ

		Ggf. muss die Location des php-Binaries angepasst werden.
		
		Erstellt die Trigger für die Tabelle caches
		
	***************************************************************************/

	$rootpath = '../../../';
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

	sql_dropTrigger('cacheRatingAfterInsert');

	sql("CREATE TRIGGER `cacheRatingAfterInsert` AFTER INSERT ON `cache_rating` 
				FOR EACH ROW 
					BEGIN 
						UPDATE `caches` SET `topratings`=(SELECT COUNT(*) FROM `cache_rating` WHERE `cache_rating`.`cache_id`=NEW.`cache_id`) WHERE `cache_id`=NEW.`cache_id`;
					END;");

	sql_dropTrigger('cacheRatingAfterUpdate');

	sql("CREATE TRIGGER `cacheRatingAfterUpdate` AFTER UPDATE ON `cache_rating` 
				FOR EACH ROW 
					BEGIN 
						IF OLD.`cache_id`!=NEW.`cache_id` THEN
							UPDATE `caches` SET `topratings`=(SELECT COUNT(*) FROM `cache_rating` WHERE `cache_rating`.`cache_id`=OLD.`cache_id`) WHERE `cache_id`=OLD.`cache_id`;
							UPDATE `caches` SET `topratings`=(SELECT COUNT(*) FROM `cache_rating` WHERE `cache_rating`.`cache_id`=NEW.`cache_id`) WHERE `cache_id`=NEW.`cache_id`;
						END IF;
					END;");

	sql_dropTrigger('cacheRatingAfterDelete');

	sql("CREATE TRIGGER `cacheRatingAfterDelete` AFTER DELETE ON `cache_rating` 
				FOR EACH ROW 
					BEGIN 
						UPDATE `caches` SET `topratings`=(SELECT COUNT(*) FROM `cache_rating` WHERE `cache_rating`.`cache_id`=OLD.`cache_id`) WHERE `cache_id`=OLD.`cache_id`;
					END;");

?>
