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

	sql_dropTrigger('cacheDescBeforeInsert');

	sql("CREATE TRIGGER `cacheDescBeforeInsert` BEFORE INSERT ON `cache_desc` 
				FOR EACH ROW 
					BEGIN 
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							SET NEW.`date_created`=NOW();
							SET NEW.`last_modified`=NOW();
						END IF;
					END;");

	sql_dropTrigger('cacheDescAfterInsert');

	sql("CREATE TRIGGER `cacheDescAfterInsert` AFTER INSERT ON `cache_desc` 
				FOR EACH ROW 
					BEGIN 
						/* `caches`.`desc_languages` */
						UPDATE `caches`, (SELECT `cache_id`, GROUP_CONCAT(DISTINCT `language` ORDER BY `language` SEPARATOR ',') AS `lang` FROM `cache_desc` GROUP BY `cache_id`) AS `tbl2` SET `caches`.`desc_languages`=`tbl2`.`lang` WHERE `caches`.`cache_id`=`tbl2`.`cache_id` AND `tbl2`.`cache_id`=NEW.`cache_id`;
						/* `caches`.`default_desclang` */
						UPDATE `caches` SET `default_desclang`=(SELECT `languages`.`short` FROM `cache_desc`, `languages` WHERE `languages`.`short`=`cache_desc`.`language` AND `cache_desc`.`cache_id`=NEW.`cache_id` ORDER BY `languages`.`prio` LIMIT 1) WHERE `cache_id`=NEW.`cache_id`;
					END;");

	sql_dropTrigger('cacheDescBeforeUpdate');

	sql("CREATE TRIGGER `cacheDescBeforeUpdate` BEFORE UPDATE ON `cache_desc` 
				FOR EACH ROW 
					BEGIN 
						/* dont overwrite `last_modified` while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							SET NEW.`last_modified`=NOW();
						END IF;
					END;");

	sql_dropTrigger('cacheDescAfterUpdate');

	sql("CREATE TRIGGER `cacheDescAfterUpdate` AFTER UPDATE ON `cache_desc` 
				FOR EACH ROW 
					BEGIN 
						IF OLD.`cache_id` != NEW.`cache_id` OR OLD.`language` != NEW.`language` THEN
							/* `caches`.`desc_languages` */
							UPDATE `caches`, (SELECT `cache_id`, GROUP_CONCAT(DISTINCT `language` ORDER BY `language` SEPARATOR ',') AS `lang` FROM `cache_desc` GROUP BY `cache_id`) AS `tbl2` SET `caches`.`desc_languages`=`tbl2`.`lang` WHERE `caches`.`cache_id`=`tbl2`.`cache_id` AND `tbl2`.`cache_id`=NEW.`cache_id`;
							/* `caches`.`default_desclang` */
							UPDATE `caches` SET `default_desclang`=(SELECT `languages`.`short` FROM `cache_desc`, `languages` WHERE `languages`.`short`=`cache_desc`.`language` AND `cache_desc`.`cache_id`=NEW.`cache_id` ORDER BY `languages`.`prio` LIMIT 1) WHERE `cache_id`=NEW.`cache_id`;

							IF OLD.`cache_id` != NEW.`cache_id` THEN
								/* `caches`.`desc_languages` */
								UPDATE `caches`, (SELECT `cache_id`, GROUP_CONCAT(DISTINCT `language` ORDER BY `language` SEPARATOR ',') AS `lang` FROM `cache_desc` GROUP BY `cache_id`) AS `tbl2` SET `caches`.`desc_languages`=`tbl2`.`lang` WHERE `caches`.`cache_id`=`tbl2`.`cache_id` AND `tbl2`.`cache_id`=OLD.`cache_id`;
								/* `caches`.`default_desclang` */
								UPDATE `caches` SET `default_desclang`=(SELECT `languages`.`short` FROM `cache_desc`, `languages` WHERE `languages`.`short`=`cache_desc`.`language` AND `cache_desc`.`cache_id`=OLD.`cache_id` ORDER BY `languages`.`prio` LIMIT 1) WHERE `cache_id`=OLD.`cache_id`;
							END IF;
						END IF;
					END;");

	sql_dropTrigger('cacheDescAfterDelete');

	sql("CREATE TRIGGER `cacheDescAfterDelete` AFTER DELETE ON `cache_desc` 
				FOR EACH ROW 
					BEGIN 
						/* `caches`.`desc_languages` */
						UPDATE `caches`, (SELECT `cache_id`, GROUP_CONCAT(DISTINCT `language` ORDER BY `language` SEPARATOR ',') AS `lang` FROM `cache_desc` GROUP BY `cache_id`) AS `tbl2` SET `caches`.`desc_languages`=`tbl2`.`lang` WHERE `caches`.`cache_id`=`tbl2`.`cache_id` AND `tbl2`.`cache_id`=OLD.`cache_id`;
						/* `caches`.`default_desclang` */
						UPDATE `caches` SET `default_desclang`=(SELECT `languages`.`short` FROM `cache_desc`, `languages` WHERE `languages`.`short`=`cache_desc`.`language` AND `cache_desc`.`cache_id`=OLD.`cache_id` ORDER BY `languages`.`prio` LIMIT 1) WHERE `cache_id`=OLD.`cache_id`;
					END;");

?>
