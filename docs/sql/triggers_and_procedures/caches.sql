DELIMITER ;;

--
--
--
DROP TRIGGER IF EXISTS `cachesBeforeInsert`;;

CREATE TRIGGER `cachesBeforeInsert` BEFORE INSERT ON `caches`
FOR EACH ROW BEGIN
    SET NEW.`need_npa_recalc`=1;
    SET NEW.`date_published` = (
        CASE WHEN NEW.`status` IN (1, 2) THEN NOW() ELSE NULL END
    );
END;;

DROP TRIGGER IF EXISTS cachesBeforeUpdate;;

CREATE TRIGGER `cachesBeforeUpdate` BEFORE UPDATE ON `caches`
FOR EACH ROW BEGIN
    IF OLD.`longitude`!=NEW.`longitude` OR OLD.`latitude`!=NEW.`latitude` THEN
        SET NEW.`need_npa_recalc`=1;
    END IF;
    IF OLD.`date_published` IS NULL AND NEW.`status` IN (1, 2) THEN
        SET NEW.`date_published` = NOW();
    END IF;
END;;

DROP TRIGGER IF EXISTS cachesAfterDelete;;

CREATE TRIGGER `cachesAfterDelete` AFTER DELETE ON `caches`
FOR EACH ROW BEGIN
DELETE FROM `cache_coordinates` WHERE `cache_id`=OLD.`cache_id`;
DELETE FROM `cache_countries` WHERE `cache_id`=OLD.`cache_id`;
DELETE FROM `cache_npa_areas` WHERE `cache_id`=OLD.`cache_id`;
UPDATE `user`, (
        SELECT COUNT(*) AS `hidden_count` FROM `caches`
        WHERE `user_id`=OLD.`user_id` AND `status` IN (1, 2, 3)
    ) AS `c`
SET `user`.`hidden_count`=`c`.`hidden_count`
WHERE `user`.`user_id`=OLD.`user_id`;
    END;;

DROP TRIGGER IF EXISTS cachesAfterInsert;;

CREATE TRIGGER `cachesAfterInsert` AFTER INSERT ON `caches`
FOR EACH ROW BEGIN
INSERT IGNORE INTO `cache_coordinates` (`cache_id`, `date_modified`, `longitude`, `latitude`)
VALUES (NEW.`cache_id`, NOW(), NEW.`longitude`, NEW.`latitude`);

INSERT IGNORE INTO `cache_countries` (`cache_id`, `date_modified`, `country`)
VALUES (NEW.`cache_id`, NOW(), NEW.`country`);

UPDATE `user`, (
        SELECT COUNT(*) AS `hidden_count` FROM `caches`
        WHERE `user_id`=NEW.`user_id` AND `status` IN (1, 2, 3)
    ) AS `c`
SET `user`.`hidden_count`=`c`.`hidden_count` WHERE `user`.`user_id`=NEW.`user_id`;
    END;;


DROP TRIGGER IF EXISTS cachesAfterUpdate;;

CREATE TRIGGER `cachesAfterUpdate` AFTER UPDATE ON `caches`
    FOR EACH ROW BEGIN

      IF NEW.`longitude` != OLD.`longitude` OR NEW.`latitude` != OLD.`latitude` THEN
        INSERT IGNORE INTO `cache_coordinates` (`cache_id`, `date_modified`, `longitude`, `latitude`)
        VALUES (NEW.`cache_id`, NOW(), NEW.`longitude`, NEW.`latitude`);
    END IF;

    IF NEW.`country` != OLD.`country` THEN
        INSERT IGNORE INTO `cache_countries` (`cache_id`, `date_modified`, `country`)
        VALUES (NEW.`cache_id`, NOW(), NEW.`country`);
    END IF;

    IF NEW.`status` != OLD.`status` OR NEW.`user_id` != OLD.`user_id` THEN
        UPDATE `user`, (
                    SELECT COUNT(*) AS `hidden_count` FROM `caches`
                    WHERE `user_id`=NEW.`user_id` AND `status` IN (1, 2, 3)
                ) AS `c`
            SET `user`.`hidden_count`=`c`.`hidden_count`
            WHERE `user`.`user_id`=NEW.`user_id`;

        IF NEW.`user_id` != OLD.`user_id` THEN
        UPDATE `user`, (
                    SELECT COUNT(*) AS `hidden_count` FROM `caches`
                    WHERE `user_id`=OLD.`user_id` AND `status` IN (1, 2, 3)
                ) AS `c`
            SET `user`.`hidden_count`=`c`.`hidden_count`
            WHERE `user`.`user_id`=OLD.`user_id`;
    END IF;
END IF;

END;; --FOR EACH ROW




--
-- This procedure increment cache-watchers-counter (column watchers) for selected cache
-- It is called from cache_watches trigger.
--
DROP PROCEDURE IF EXISTS inc_cache_watchers;;

CREATE PROCEDURE inc_cache_watchers (
    IN `p_cache_id` int(11)
)
BEGIN
    UPDATE caches SET watcher=watcher+1
    WHERE `p_cache_id` = `cache_id` LIMIT 1;
END ;;

--
-- This procedure decrement cache-watchers-counter (column watchers) for selected cache
-- It is called from cache_watches trigger.
--
DROP PROCEDURE IF EXISTS dec_cache_watchers;;

CREATE PROCEDURE dec_cache_watchers (
    IN `p_cache_id` int(11)
)
BEGIN
    UPDATE caches SET watcher=watcher-1
    WHERE `p_cache_id` = `cache_id` AND watcher > 0 LIMIT 1;
END ;;


DELIMITER ;
