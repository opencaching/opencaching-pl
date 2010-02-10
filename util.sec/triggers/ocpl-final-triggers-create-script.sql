USE opencachin_ocpl;

DELIMITER ;;

DROP TRIGGER IF EXISTS cacheDescBeforeInsert;;

CREATE TRIGGER `cacheDescBeforeInsert` BEFORE INSERT ON `cache_desc`
  FOR EACH ROW
    BEGIN
      SET NEW.`date_created`=NOW();
    END;;

DROP TRIGGER IF EXISTS cacheDescAfterInsert;;

CREATE TRIGGER `cacheDescAfterInsert` AFTER INSERT ON `cache_desc`
  FOR EACH ROW
    BEGIN
      UPDATE `caches`, (SELECT `cache_id`, GROUP_CONCAT(DISTINCT `language` ORDER BY `language` SEPARATOR ',') AS `lang` FROM `cache_desc` GROUP BY `cache_id`) AS `tbl2` SET `caches`.`desc_languages`=`tbl2`.`lang` WHERE `caches`.`cache_id`=`tbl2`.`cache_id` AND `tbl2`.`cache_id`=NEW.`cache_id`;
    END;;

DROP TRIGGER IF EXISTS cacheDescAfterUpdate;;

CREATE TRIGGER `cacheDescAfterUpdate` AFTER UPDATE ON `cache_desc`
  FOR EACH ROW
    BEGIN
      IF OLD.`cache_id` != NEW.`cache_id` OR OLD.`language` != NEW.`language` THEN
        UPDATE `caches`, (SELECT `cache_id`, GROUP_CONCAT(DISTINCT `language` ORDER BY `language` SEPARATOR ',') AS `lang` FROM `cache_desc` GROUP BY `cache_id`) AS `tbl2` SET `caches`.`desc_languages`=`tbl2`.`lang` WHERE `caches`.`cache_id`=`tbl2`.`cache_id` AND `tbl2`.`cache_id`=NEW.`cache_id`;
        IF OLD.`cache_id` != NEW.`cache_id` THEN
          UPDATE `caches`, (SELECT `cache_id`, GROUP_CONCAT(DISTINCT `language` ORDER BY `language` SEPARATOR ',') AS `lang` FROM `cache_desc` GROUP BY `cache_id`) AS `tbl2` SET `caches`.`desc_languages`=`tbl2`.`lang` WHERE `caches`.`cache_id`=`tbl2`.`cache_id` AND `tbl2`.`cache_id`=OLD.`cache_id`;
        END IF;
      END IF;
    END;;

DROP TRIGGER IF EXISTS cacheDescAfterDelete;;

CREATE TRIGGER `cacheDescAfterDelete` AFTER DELETE ON `cache_desc`
  FOR EACH ROW
    BEGIN
      UPDATE `caches`, (SELECT `cache_id`, GROUP_CONCAT(DISTINCT `language` ORDER BY `language` SEPARATOR ',') AS `lang` FROM `cache_desc` GROUP BY `cache_id`) AS `tbl2` SET `caches`.`desc_languages`=`tbl2`.`lang` WHERE `caches`.`cache_id`=`tbl2`.`cache_id` AND `tbl2`.`cache_id`=OLD.`cache_id`; 
    END;;

DROP TRIGGER IF EXISTS cacheLocationBeforeInsert;;

CREATE TRIGGER `cacheLocationBeforeInsert` BEFORE INSERT ON `cache_location`
  FOR EACH ROW
    BEGIN
      SET NEW.`last_modified`=NOW();
    END;;

DROP TRIGGER IF EXISTS cacheLocationBeforeUpdate;;

CREATE TRIGGER `cacheLocationBeforeUpdate` BEFORE UPDATE ON `cache_location`
  FOR EACH ROW
    BEGIN
      SET NEW.`last_modified`=NOW();
    END;;

DROP TRIGGER IF EXISTS cacheRatingAfterInsert;;

CREATE TRIGGER `cacheRatingAfterInsert` AFTER INSERT ON `cache_rating`
  FOR EACH ROW
    BEGIN  
      UPDATE `caches` SET `topratings`=(SELECT COUNT(*) FROM `cache_rating` WHERE `cache_rating`.`cache_id`=NEW.`cache_id`) WHERE `cache_id`=NEW.`cache_id`;
    END;;

DROP TRIGGER IF EXISTS cacheRatingAfterUpdate;;

CREATE TRIGGER `cacheRatingAfterUpdate` AFTER UPDATE ON `cache_rating`
  FOR EACH ROW
    BEGIN  
      IF OLD.`cache_id`!=NEW.`cache_id` THEN
        UPDATE `caches` SET `topratings`=(SELECT COUNT(*) FROM `cache_rating` WHERE `cache_rating`.`cache_id`=OLD.`cache_id`) WHERE `cache_id`=OLD.`cache_id`;
        UPDATE `caches` SET `topratings`=(SELECT COUNT(*) FROM `cache_rating` WHERE `cache_rating`.`cache_id`=NEW.`cache_id`) WHERE `cache_id`=NEW.`cache_id`;
      END IF;
    END;;

DROP TRIGGER IF EXISTS cacheRatingAfterDelete;;

CREATE TRIGGER `cacheRatingAfterDelete` AFTER DELETE ON `cache_rating`
  FOR EACH ROW
    BEGIN
      UPDATE `caches` SET `topratings`=(SELECT COUNT(*) FROM `cache_rating` WHERE `cache_rating`.`cache_id`=OLD.`cache_id`) WHERE `cache_id`=OLD.`cache_id`;
    END;;

DROP TRIGGER IF EXISTS cachesAfterInsert;;

CREATE TRIGGER `cachesAfterInsert` AFTER INSERT ON `caches`
  FOR EACH ROW
    BEGIN
      INSERT IGNORE INTO `cache_coordinates` (`cache_id`, `date_modified`, `longitude`, `latitude`) 
                                      VALUES (NEW.`cache_id`, NOW(), NEW.`longitude`, NEW.`latitude`);
      INSERT IGNORE INTO `cache_countries` (`cache_id`, `date_modified`, `country`) 
                                      VALUES (NEW.`cache_id`, NOW(), NEW.`country`);
      UPDATE `user`, (SELECT COUNT(*) AS `hidden_count` FROM `caches` WHERE `user_id`=NEW.`user_id` AND `status` IN (1, 2, 3)) AS `c` SET `user`.`hidden_count`=`c`.`hidden_count` WHERE `user`.`user_id`=NEW.`user_id`;
    END;;

DROP TRIGGER IF EXISTS cachesAfterUpdate;;

CREATE TRIGGER `cachesAfterUpdate` AFTER UPDATE ON `caches`
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
    END;;

DROP TRIGGER IF EXISTS cachesAfterDelete;;

CREATE TRIGGER `cachesAfterDelete` AFTER DELETE ON `caches`
  FOR EACH ROW
    BEGIN
      DELETE FROM `cache_coordinates` WHERE `cache_id`=OLD.`cache_id`;
      DELETE FROM `cache_countries` WHERE `cache_id`=OLD.`cache_id`;
      UPDATE `user`, (SELECT COUNT(*) AS `hidden_count` FROM `caches` WHERE `user_id`=OLD.`user_id` AND `status` IN (1, 2, 3)) AS `c` SET `user`.`hidden_count`=`c`.`hidden_count` WHERE `user`.`user_id`=OLD.`user_id`;
    END;;

DELIMITER ;
