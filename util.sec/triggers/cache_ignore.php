<?php
 /***************************************************************************
#!php -q
		
		Unicode Reminder メモ

		Ggf. muss die Location des php-Binaries angepasst werden.
		
		Erstellt die Trigger für die Tabelle cache_ignore
		
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

	sql_dropTrigger('cacheIgnoreAfterInsert');

	sql("CREATE TRIGGER `cacheIgnoreAfterInsert` AFTER INSERT ON `cache_ignore` 
				FOR EACH ROW 
					BEGIN 
						UPDATE `caches` SET `ignorer_count`=(SELECT COUNT(*) FROM `cache_ignore` WHERE `cache_id`=NEW.cache_id) WHERE `cache_id`=NEW.cache_id;
					END;");

	sql_dropTrigger('cacheIgnoreAfterUpdate');

	sql("CREATE TRIGGER `cacheIgnoreAfterUpdate` AFTER UPDATE ON `cache_ignore` 
				FOR EACH ROW 
					BEGIN 
						IF OLD.`cache_id`!=NEW.`cache_id` THEN
							UPDATE `caches` SET `ignorer_count`=(SELECT COUNT(*) FROM `cache_ignore` WHERE `cache_id`=OLD.cache_id) WHERE `cache_id`=OLD.cache_id;
							UPDATE `caches` SET `ignorer_count`=(SELECT COUNT(*) FROM `cache_ignore` WHERE `cache_id`=NEW.cache_id) WHERE `cache_id`=NEW.cache_id;
						END IF;
					END;");

	sql_dropTrigger('cacheIgnoreAfterDelete');

	sql("CREATE TRIGGER `cacheIgnoreAfterDelete` AFTER DELETE ON `cache_ignore` 
				FOR EACH ROW 
					BEGIN 
						UPDATE `caches` SET `ignorer_count`=(SELECT COUNT(*) FROM `cache_ignore` WHERE `cache_id`=OLD.cache_id) WHERE `cache_id`=OLD.cache_id;
					END;");

?>