#!/usr/local/bin/php -q
<?php
 /***************************************************************************
		
		Unicode Reminder メモ

		Ggf. muss die Location des php-Binaries angepasst werden.
		
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

	/* Triggers
	 */
	sql_dropTrigger('cachesBeforeInsert');
	sql("CREATE TRIGGER `cachesBeforeInsert` BEFORE INSERT ON `caches` 
				FOR EACH ROW 
					BEGIN 
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							SET NEW.`date_created`=NOW();
							SET NEW.`last_modified`=NOW();
						END IF;
						SET NEW.`need_npa_recalc`=1;
					END;");

	sql_dropTrigger('cachesAfterInsert');
	sql("CREATE TRIGGER `cachesAfterInsert` AFTER INSERT ON `caches` 
				FOR EACH ROW 
					BEGIN 
						INSERT IGNORE INTO `cache_coordinates` (`cache_id`, `date_created`, `longitude`, `latitude`) 
						                                VALUES (NEW.`cache_id`, NOW(), NEW.`longitude`, NEW.`latitude`);
						INSERT IGNORE INTO `cache_countries` (`cache_id`, `date_created`, `country`) 
						                                VALUES (NEW.`cache_id`, NOW(), NEW.`country`);

						CALL sp_update_hiddenstat(NEW.`user_id`, FALSE);

            IF NEW.`status`=1 THEN
              CALL sp_notify_new_cache(NEW.`cache_id`, NEW.`longitude`, NEW.`latitude`);
            END IF;
					END;");

	sql_dropTrigger('cachesBeforeUpdate');
	sql("CREATE TRIGGER `cachesBeforeUpdate` BEFORE UPDATE ON `caches` 
				FOR EACH ROW 
					BEGIN 
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							IF OLD.`cache_id`!=NEW.`cache_id` OR 
							   OLD.`uuid`!=NEW.`uuid` OR 
							   OLD.`node`!=NEW.`node` OR 
							   OLD.`date_created`!=NEW.`date_created` OR 
							   OLD.`user_id`!=NEW.`user_id` OR 
							   OLD.`name`!=NEW.`name` OR 
							   OLD.`longitude`!=NEW.`longitude` OR 
							   OLD.`latitude`!=NEW.`latitude` OR 
							   OLD.`type`!=NEW.`type` OR 
							   OLD.`status`!=NEW.`status` OR 
							   OLD.`country`!=NEW.`country` OR 
							   OLD.`date_hidden`!=NEW.`date_hidden` OR 
							   OLD.`size`!=NEW.`size` OR 
							   OLD.`difficulty`!=NEW.`difficulty` OR 
							   OLD.`terrain`!=NEW.`terrain` OR 
							   OLD.`logpw`!=NEW.`logpw` OR 
							   OLD.`search_time`!=NEW.`search_time` OR 
							   OLD.`way_length`!=NEW.`way_length` OR 
							   OLD.`wp_gc`!=NEW.`wp_gc` OR 
							   OLD.`wp_nc`!=NEW.`wp_nc` OR 
							   OLD.`wp_oc`!=NEW.`wp_oc` OR 
							   OLD.`default_desclang`!=NEW.`default_desclang` OR 
							   OLD.`date_activate`!=NEW.`date_activate` THEN

								SET NEW.`last_modified`=NOW();
							END IF;

							IF OLD.`status`!=NEW.`status` THEN
								CALL sp_touch_cache(OLD.`cache_id`, FALSE);
							END IF;
						END IF;

						IF OLD.`longitude`!=NEW.`longitude` OR 
						   OLD.`latitude`!=NEW.`latitude` THEN
							SET NEW.`need_npa_recalc`=1;
						END IF;
					END;");

	sql_dropTrigger('cachesAfterUpdate');
	sql("CREATE TRIGGER `cachesAfterUpdate` AFTER UPDATE ON `caches` 
				FOR EACH ROW 
					BEGIN 
						IF NEW.`longitude` != OLD.`longitude` OR NEW.`latitude` != OLD.`latitude` THEN 
							INSERT IGNORE INTO `cache_coordinates` (`cache_id`, `date_created`, `longitude`, `latitude`) 
								VALUES (NEW.`cache_id`, NOW(), NEW.`longitude`, NEW.`latitude`); 
						END IF; 
						IF NEW.`country` != OLD.`country` THEN 
							INSERT IGNORE INTO `cache_countries` (`cache_id`, `date_created`, `country`) 
								VALUES (NEW.`cache_id`, NOW(), NEW.`country`); 
						END IF;
						IF NEW.`user_id`!=OLD.`user_id` THEN
							CALL sp_update_hiddenstat(OLD.`user_id`, TRUE);
							CALL sp_update_hiddenstat(NEW.`user_id`, FALSE);
						END IF;
            IF OLD.`status`=5 AND NEW.`status`=1 THEN
              CALL sp_notify_new_cache(NEW.`cache_id`, NEW.`longitude`, NEW.`latitude`);
            END IF;
					END;");

	sql_dropTrigger('cachesAfterDelete');
	sql("CREATE TRIGGER `cachesAfterDelete` AFTER DELETE ON `caches` 
				FOR EACH ROW 
					BEGIN 
						DELETE FROM `cache_coordinates` WHERE `cache_id`=OLD.`cache_id`;
						DELETE FROM `cache_countries` WHERE `cache_id`=OLD.`cache_id`;
						DELETE FROM `cache_npa_areas` WHERE `cache_id`=OLD.`cache_id`;
						CALL sp_update_hiddenstat(OLD.`user_id`, TRUE);
						INSERT IGNORE INTO `removed_objects` (`localId`, `uuid`, `type`, `node`) VALUES (OLD.`cache_id`, OLD.`uuid`, 2, OLD.`node`);
					END;");

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
						CALL sp_update_caches_descLanguages(NEW.`cache_id`);
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
						IF OLD.`language`!=NEW.`language` OR OLD.`cache_id`!=NEW.`cache_id` THEN
							IF OLD.`cache_id`!=NEW.`cache_id` THEN
								CALL sp_update_caches_descLanguages(OLD.`cache_id`);
							END IF;
							CALL sp_update_caches_descLanguages(NEW.`cache_id`);
						END IF;
					END;");

	sql_dropTrigger('cacheDescAfterDelete');
	sql("CREATE TRIGGER `cacheDescAfterDelete` AFTER DELETE ON `cache_desc` 
				FOR EACH ROW 
					BEGIN 
						INSERT IGNORE INTO `removed_objects` (`localId`, `uuid`, `type`, `node`) VALUES (OLD.`id`, OLD.`uuid`, 3, OLD.`node`);
						CALL sp_update_caches_descLanguages(OLD.`cache_id`);
					END;");

	sql_dropTrigger('cacheIgnoreAfterInsert');
	sql("CREATE TRIGGER `cacheIgnoreAfterInsert` AFTER INSERT ON `cache_ignore` 
				FOR EACH ROW 
					BEGIN 
						CALL sp_update_ignorestat(NEW.`cache_id`, FALSE);
					END;");

	sql_dropTrigger('cacheIgnoreAfterUpdate');
	sql("CREATE TRIGGER `cacheIgnoreAfterUpdate` AFTER UPDATE ON `cache_ignore` 
				FOR EACH ROW 
					BEGIN 
						IF NEW.`cache_id`!=OLD.`cache_id` THEN
							CALL sp_update_ignorestat(OLD.`cache_id`, TRUE);
							CALL sp_update_ignorestat(NEW.`cache_id`, FALSE);
						END IF;
					END;");

	sql_dropTrigger('cacheIgnoreAfterDelete');
	sql("CREATE TRIGGER `cacheIgnoreAfterDelete` AFTER DELETE ON `cache_ignore` 
				FOR EACH ROW 
					BEGIN 
						CALL sp_update_ignorestat(OLD.`cache_id`, TRUE);
					END;");

	sql_dropTrigger('cacheLocationBeforeInsert');
	sql("CREATE TRIGGER `cacheLocationBeforeInsert` BEFORE INSERT ON `cache_location` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`last_modified`=NOW();
					END;");

	sql_dropTrigger('cacheLocationBeforeUpdate');
	sql("CREATE TRIGGER `cacheLocationBeforeUpdate` BEFORE UPDATE ON `cache_location` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`last_modified`=NOW();
					END;");

	sql_dropTrigger('cacheLogsBeforeInsert');
	sql("CREATE TRIGGER `cacheLogsBeforeInsert` BEFORE INSERT ON `cache_logs` 
				FOR EACH ROW 
					BEGIN 
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							SET NEW.`date_created`=NOW();
							SET NEW.`last_modified`=NOW();
						END IF;
					END;");

	sql_dropTrigger('cacheLogsAfterInsert');
	sql("CREATE TRIGGER `cacheLogsAfterInsert` AFTER INSERT ON `cache_logs` 
				FOR EACH ROW 
					BEGIN 
	          CALL sp_update_logstat(NEW.`cache_id`, NEW.`user_id`, NEW.`type`, FALSE);
					END;");

	sql_dropTrigger('cacheLogsBeforeUpdate');
	sql("CREATE TRIGGER `cacheLogsBeforeUpdate` BEFORE UPDATE ON `cache_logs` 
				FOR EACH ROW 
					BEGIN 
						/* dont overwrite `last_modified` while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							IF NEW.`id`!=OLD.`id` OR
							   NEW.`uuid`!=OLD.`uuid` OR
							   NEW.`node`!=OLD.`node` OR
							   NEW.`date_created`!=OLD.`date_created` OR
							   NEW.`cache_id`!=OLD.`cache_id` OR
							   NEW.`user_id`!=OLD.`user_id` OR
							   NEW.`type`!=OLD.`type` OR
							   NEW.`date`!=OLD.`date` OR
							   NEW.`text`!=OLD.`text` OR
							   NEW.`text_html`!=OLD.`text_html` THEN
						
								SET NEW.`last_modified`=NOW();
							END IF;
						END IF;
					END;");

	sql_dropTrigger('cacheLogsAfterUpdate');
	sql("CREATE TRIGGER `cacheLogsAfterUpdate` AFTER UPDATE ON `cache_logs` 
				FOR EACH ROW 
					BEGIN 
						IF OLD.`cache_id`!=NEW.`cache_id` OR OLD.`user_id`!=NEW.`user_id` OR OLD.`type`!=NEW.`type` THEN
							CALL sp_update_logstat(OLD.`cache_id`, OLD.`user_id`, OLD.`type`, TRUE);
							CALL sp_update_logstat(NEW.`cache_id`, NEW.`user_id`, NEW.`type`, FALSE);
						END IF;
					END;");

	sql_dropTrigger('cacheLogsAfterDelete');
	sql("CREATE TRIGGER `cacheLogsAfterDelete` AFTER DELETE ON `cache_logs` 
				FOR EACH ROW 
					BEGIN 
						CALL sp_update_logstat(OLD.`cache_id`, OLD.`user_id`, OLD.`type`, TRUE);
						INSERT IGNORE INTO `removed_objects` (`localId`, `uuid`, `type`, `node`) VALUES (OLD.`id`, OLD.`uuid`, 1, OLD.`node`);
					END;");

	sql_dropTrigger('cacheRatingAfterInsert');
	sql("CREATE TRIGGER `cacheRatingAfterInsert` AFTER INSERT ON `cache_rating` 
				FOR EACH ROW 
					BEGIN 
						CALL sp_update_topratingstat(NEW.`cache_id`, FALSE);
					END;");

	sql_dropTrigger('cacheRatingAfterUpdate');
	sql("CREATE TRIGGER `cacheRatingAfterUpdate` AFTER UPDATE ON `cache_rating` 
				FOR EACH ROW 
					BEGIN 
						IF NEW.`cache_id`!=OLD.`cache_id` THEN
							CALL sp_update_topratingstat(OLD.`cache_id`, TRUE);
							CALL sp_update_topratingstat(NEW.`cache_id`, FALSE);
						END IF;
					END;");

	sql_dropTrigger('cacheRatingAfterDelete');
	sql("CREATE TRIGGER `cacheRatingAfterDelete` AFTER DELETE ON `cache_rating` 
				FOR EACH ROW 
					BEGIN 
						CALL sp_update_topratingstat(OLD.`cache_id`, TRUE);
					END;");

	sql_dropTrigger('cacheVisitsBeforeInsert');
	sql("CREATE TRIGGER `cacheVisitsBeforeInsert` BEFORE INSERT ON `cache_visits` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`last_modified`=NOW();
					END;");

	sql_dropTrigger('cacheVisitsBeforeUpdate');
	sql("CREATE TRIGGER `cacheVisitsBeforeUpdate` BEFORE UPDATE ON `cache_visits` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`last_modified`=NOW();
					END;");

	sql_dropTrigger('cacheWatchesAfterInsert');
	sql("CREATE TRIGGER `cacheWatchesAfterInsert` AFTER INSERT ON `cache_watches` 
				FOR EACH ROW 
					BEGIN 
						CALL sp_update_watchstat(NEW.`cache_id`, FALSE);
					END;");

	sql_dropTrigger('cacheWatchesAfterUpdate');
	sql("CREATE TRIGGER `cacheWatchesAfterUpdate` AFTER UPDATE ON `cache_watches` 
				FOR EACH ROW 
					BEGIN 
						IF NEW.`cache_id`!=OLD.`cache_id` THEN
							CALL sp_update_watchstat(OLD.`cache_id`, TRUE);
							CALL sp_update_watchstat(NEW.`cache_id`, FALSE);
						END IF;
					END;");

	sql_dropTrigger('cacheWatchesAfterDelete');
	sql("CREATE TRIGGER `cacheWatchesAfterDelete` AFTER DELETE ON `cache_watches` 
				FOR EACH ROW 
					BEGIN 
						CALL sp_update_watchstat(OLD.`cache_id`, TRUE);
					END;");

	sql_dropTrigger('emailUserBeforeInsert');
	sql("CREATE TRIGGER `emailUserBeforeInsert` BEFORE INSERT ON `email_user` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`date_created`=NOW();
					END;");

	sql_dropTrigger('logentriesBeforeInsert');
	sql("CREATE TRIGGER `logentriesBeforeInsert` BEFORE INSERT ON `logentries` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`date_created`=NOW();
					END;");

	sql_dropTrigger('newsBeforeInsert');
	sql("CREATE TRIGGER `newsBeforeInsert` BEFORE INSERT ON `news` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`date_created`=NOW();
					END;");

	sql_dropTrigger('picturesBeforeInsert');
	sql("CREATE TRIGGER `picturesBeforeInsert` BEFORE INSERT ON `pictures` 
				FOR EACH ROW 
					BEGIN 
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							SET NEW.`date_created`=NOW();
							SET NEW.`last_modified`=NOW();
						END IF;
					END;");

	sql_dropTrigger('picturesAfterInsert');
	sql("CREATE TRIGGER `picturesAfterInsert` AFTER INSERT ON `pictures` 
				FOR EACH ROW 
					BEGIN 
						IF NEW.`object_type`=1 THEN
							CALL sp_update_cachelog_picturestat(NEW.`object_id`, FALSE);
						ELSEIF NEW.`object_type`=2 THEN
							CALL sp_update_cache_picturestat(NEW.`object_id`, FALSE);
						END IF;
					END;");

	sql_dropTrigger('picturesBeforeUpdate');
	sql("CREATE TRIGGER `picturesBeforeUpdate` BEFORE UPDATE ON `pictures` 
				FOR EACH ROW 
					BEGIN 
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							SET NEW.`last_modified`=NOW();
						END IF;
					END;");

	sql_dropTrigger('picturesAfterUpdate');
	sql("CREATE TRIGGER `picturesAfterUpdate` AFTER UPDATE ON `pictures` 
				FOR EACH ROW 
					BEGIN 
						IF OLD.`object_type`!=NEW.`object_type` OR OLD.`object_id`!=NEW.`object_id` THEN
							IF OLD.`object_type`=1 THEN
								CALL sp_update_cachelog_picturestat(OLD.`object_id`, TRUE);
							ELSEIF OLD.`object_type`=2 THEN
								CALL sp_update_cache_picturestat(OLD.`object_id`, TRUE);
							END IF;
							IF NEW.`object_type`=1 THEN
								CALL sp_update_cachelog_picturestat(NEW.`object_id`, FALSE);
							ELSEIF NEW.`object_type`=2 THEN
								CALL sp_update_cache_picturestat(NEW.`object_id`, FALSE);
							END IF;
						END IF;
					END;");

	sql_dropTrigger('picturesAfterDelete');
	sql("CREATE TRIGGER `picturesAfterDelete` AFTER DELETE ON `pictures` 
				FOR EACH ROW 
					BEGIN 
						INSERT IGNORE INTO `removed_objects` (`localId`, `uuid`, `type`, `node`) VALUES (OLD.`id`, OLD.`uuid`, 6, OLD.`node`);
						IF OLD.`object_type`=1 THEN
							CALL sp_update_cachelog_picturestat(OLD.`object_id`, TRUE);
						ELSEIF OLD.`object_type`=2 THEN
							CALL sp_update_cache_picturestat(OLD.`object_id`, TRUE);
						END IF;
					END;");

	sql_dropTrigger('removedObjectsBeforeInsert');
	sql("CREATE TRIGGER `removedObjectsBeforeInsert` BEFORE INSERT ON `removed_objects` 
				FOR EACH ROW 
					BEGIN 
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							SET NEW.`removed_date`=NOW();
						END IF;
					END;");

	sql_dropTrigger('sysLoginsBeforeInsert');
	sql("CREATE TRIGGER `sysLoginsBeforeInsert` BEFORE INSERT ON `sys_logins` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`date_created`=NOW();
					END;");

	sql_dropTrigger('sysTransBeforeInsert');
	sql("CREATE TRIGGER `sysTransBeforeInsert` BEFORE INSERT ON `sys_trans` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`last_modified`=NOW();
					END;");

	sql_dropTrigger('sysTransBeforeUpdate');
	sql("CREATE TRIGGER `sysTransBeforeUpdate` BEFORE UPDATE ON `sys_trans` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`last_modified`=NOW();
					END;");

	sql_dropTrigger('sysTransTextBeforeInsert');
	sql("CREATE TRIGGER `sysTransTextBeforeInsert` BEFORE INSERT ON `sys_trans_text` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`last_modified`=NOW();
					END;");

	sql_dropTrigger('sysTransTextBeforeUpdate');
	sql("CREATE TRIGGER `sysTransTextBeforeUpdate` BEFORE UPDATE ON `sys_trans_text` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`last_modified`=NOW();
					END;");

	sql_dropTrigger('userBeforeInsert');
	sql("CREATE TRIGGER `userBeforeInsert` BEFORE INSERT ON `user` 
				FOR EACH ROW 
					BEGIN 
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							SET NEW.`date_created`=NOW();
							SET NEW.`last_modified`=NOW();
						END IF;
					END;");

	sql_dropTrigger('userBeforeUpdate');
	sql("CREATE TRIGGER `userBeforeUpdate` BEFORE UPDATE ON `user` 
				FOR EACH ROW 
					BEGIN 
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							IF NEW.`user_id`!=OLD.`user_id` OR 
							   NEW.`uuid`!=OLD.`uuid` OR 
							   NEW.`node`!=OLD.`node` OR 
							   NEW.`date_created`!=OLD.`date_created` OR 
							   NEW.`username`!=OLD.`username` OR 
							   NEW.`pmr_flag`!=OLD.`pmr_flag` THEN
							   
								SET NEW.`last_modified`=NOW();
							END IF;
						END IF;
					END;");

	sql_dropTrigger('userAfterDelete');
	sql("CREATE TRIGGER `userAfterDelete` AFTER DELETE ON `user` 
				FOR EACH ROW 
					BEGIN 
						INSERT IGNORE INTO `removed_objects` (`localId`, `uuid`, `type`, `node`) VALUES (OLD.`user_id`, OLD.`uuid`, 4, OLD.`node`);
					END;");

	sql_dropTrigger('userDelegatesBeforeInsert');
	sql("CREATE TRIGGER `userDelegatesBeforeInsert` BEFORE INSERT ON `user_delegates` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`date_created`=NOW();
					END;");

	sql_dropTrigger('userDelegatesBeforeUpdate');
	sql("CREATE TRIGGER `userDelegatesBeforeUpdate` BEFORE UPDATE ON `user_delegates` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`date_created`=NOW();
					END;");

	sql_dropTrigger('watchesNotifiedBeforeInsert');
	sql("CREATE TRIGGER `watchesNotifiedBeforeInsert` BEFORE INSERT ON `watches_notified` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`date_created`=NOW();
					END;");

	sql_dropTrigger('watchesWaitingBeforeInsert');
	sql("CREATE TRIGGER `watchesWaitingBeforeInsert` BEFORE INSERT ON `watches_waiting` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`date_created`=NOW();
					END;");

	sql_dropTrigger('xmlsessionBeforeInsert');
	sql("CREATE TRIGGER `xmlsessionBeforeInsert` BEFORE INSERT ON `xmlsession` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`date_created`=NOW();
					END;");

	sql_dropTrigger('cacheAdoptionBeforeInsert');
	sql("CREATE TRIGGER `cacheAdoptionBeforeInsert` BEFORE INSERT ON `cache_adoption` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`date_created`=NOW();
					END;");

	sql_dropTrigger('cacheAdoptionBeforeUpdate');
	sql("CREATE TRIGGER `cacheAdoptionBeforeUpdate` BEFORE UPDATE ON `cache_adoption` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`date_created`=NOW();
					END;");

	sql_dropTrigger('userStatpicBeforeInsert');
	sql("CREATE TRIGGER `userStatpicBeforeInsert` BEFORE INSERT ON `user_statpic` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`date_created`=NOW();
					END;");

	sql_dropTrigger('sysSessionsAfterInsert');
	sql("CREATE TRIGGER `sysSessionsAfterInsert` AFTER INSERT ON `sys_sessions` 
				FOR EACH ROW 
					BEGIN 
						UPDATE `user` SET `user`.`last_login`=NEW.`last_login` WHERE `user`.`user_id`=NEW.`user_id`;
					END;");

	sql_dropTrigger('cacheAttributesAfterInsert');
	sql("CREATE TRIGGER `cacheAttributesAfterInsert` AFTER INSERT ON `caches_attributes` 
				FOR EACH ROW 
					BEGIN 
						UPDATE `caches` SET `last_modified`=NOW() WHERE `cache_id`=NEW.`cache_id`;
					END;");

	sql_dropTrigger('cacheAttributesAfterUpdate');
	sql("CREATE TRIGGER `cacheAttributesAfterUpdate` AFTER UPDATE ON `caches_attributes` 
				FOR EACH ROW 
					BEGIN 
						UPDATE `caches` SET `last_modified`=NOW() WHERE `cache_id`=NEW.`cache_id`;
						IF OLD.`cache_id`!=NEW.`cache_id` THEN
							UPDATE `caches` SET `last_modified`=NOW() WHERE `cache_id`=OLD.`cache_id`;
						END IF;
					END;");

	sql_dropTrigger('cacheAttributesAfterDelete');
	sql("CREATE TRIGGER `cacheAttributesAfterDelete` AFTER DELETE ON `caches_attributes` 
				FOR EACH ROW 
					BEGIN 
						UPDATE `caches` SET `last_modified`=NOW() WHERE `cache_id`=OLD.`cache_id`;
					END;");
?>