DELIMITER ;;

-- DELIMITER must be in the first line.
-- Do not include the delimiter (double semicolon) in comments or strings.

-- Changes will be automatically installed on production sites.
-- On developer sites, use http://local.opencaching.pl/Admin.DbUpdate/run


DROP TRIGGER IF EXISTS `cachesBeforeInsert`;;

CREATE TRIGGER `cachesBeforeInsert` BEFORE INSERT ON `caches`
    FOR EACH ROW BEGIN
        SET NEW.`need_npa_recalc` = 1;
        SET NEW.`date_published` = (
            CASE
                WHEN NEW.`status` IN (1, 2) THEN NOW()
                ELSE NULL
            END
        );
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
            WHERE `user_id` = NEW.`user_id` AND `status` IN (1, 2, 3)
        ) AS `c`
        SET `user`.`hidden_count`=`c`.`hidden_count` WHERE `user`.`user_id` = NEW.`user_id`;
    END;;


DROP TRIGGER IF EXISTS cachesBeforeUpdate;;

CREATE TRIGGER `cachesBeforeUpdate` BEFORE UPDATE ON `caches`
    FOR EACH ROW BEGIN
        IF OLD.`longitude` != NEW.`longitude` OR OLD.`latitude` != NEW.`latitude` THEN
            SET NEW.`need_npa_recalc` = 1;
        END IF;
        IF OLD.`date_published` IS NULL AND NEW.`status` IN (1, 2) THEN
            SET NEW.`date_published` = NOW();
        END IF;
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
                WHERE `user_id` = NEW.`user_id` AND `status` IN (1, 2, 3)
            ) AS `c`
            SET `user`.`hidden_count` = `c`.`hidden_count`
            WHERE `user`.`user_id` = NEW.`user_id`;

            IF NEW.`user_id` != OLD.`user_id` THEN
                UPDATE `user`, (
                    SELECT COUNT(*) AS `hidden_count` FROM `caches`
                    WHERE `user_id` = OLD.`user_id` AND `status` IN (1, 2, 3)
                ) AS `c`
                SET `user`.`hidden_count` = `c`.`hidden_count`
                WHERE `user`.`user_id` = OLD.`user_id`;
            END IF;
        END IF;

    END;;


DROP TRIGGER IF EXISTS cachesBeforeDelete;;

CREATE TRIGGER `cachesBeforeDelete` BEFORE DELETE ON `caches`
    FOR EACH ROW BEGIN

        IF IFNULL(@allowdelete, 0) = 0 THEN

            -- protection against accidential cache deletion;
            -- call to nonexistent proc throws error

            CALL must_not_delete_caches();
        ELSE
            -- This is used e.g. for preparing developer VMs

            -- prevent recursive write access to caches table
            SET @deleting_cache = 1;

            -- owner's cache content & derived data
            DELETE FROM `caches_additions` WHERE `cache_id` = OLD.`cache_id`;
            DELETE FROM `cache_coordinates` WHERE `cache_id` = OLD.`cache_id`;
            DELETE FROM `cache_countries` WHERE `cache_id` = OLD.`cache_id`;
            DELETE FROM `cache_desc` WHERE `cache_id` = OLD.`cache_id`;
            DELETE FROM `cache_location` WHERE `cache_id` = OLD.`cache_id`;
            DELETE FROM `cache_npa_areas` WHERE `cache_id` = OLD.`cache_id`;
            DELETE FROM `caches_attributes` WHERE `cache_id` = OLD.`cache_id`;
            DELETE FROM `chowner` WHERE `cache_id` = OLD.`cache_id`;
            DELETE FROM `opensprawdzacz` WHERE `cache_id` = OLD.`cache_id`;
            DELETE FROM `PowerTrail_actionsLog` WHERE `cacheId` = OLD.`cache_id`;
            DELETE FROM `PowerTrail_cacheCandidate` WHERE `cacheId` = OLD.`cache_id`;
            DELETE FROM `powerTrail_caches` WHERE `cacheId` = OLD.`cache_id`;
            DELETE FROM `waypoints` WHERE `cache_id` = OLD.`cache_id`;

            -- log entries and other cache-related data by users
            DELETE FROM `badge_cache` WHERE `cache_id` = OLD.`cache_id`;
            DELETE FROM `cache_ignore` WHERE `cache_id` = OLD.`cache_id`;
            DELETE FROM `cache_logs` WHERE `cache_id` = OLD.`cache_id`;
            DELETE FROM `cache_mod_cords` WHERE `cache_id` = OLD.`cache_id`;
            DELETE FROM `cache_moved` WHERE `cache_id` = OLD.`cache_id`;
            DELETE FROM `cache_notes` WHERE `cache_id` = OLD.`cache_id`;
            DELETE FROM `cache_rating` WHERE `cache_id` = OLD.`cache_id`;
            DELETE FROM `cache_titled` WHERE `cache_id` = OLD.`cache_id`;
            DELETE FROM `cache_watches` WHERE `cache_id` = OLD.`cache_id`;
            DELETE FROM `geokret_log` WHERE `geocache_id` = OLD.`cache_id`;
            DELETE FROM `recommendation_plan` WHERE `cacheId` = OLD.`cache_id`;
            DELETE FROM `scores` WHERE `cache_id` = OLD.`cache_id`;

            -- admin data
            DELETE FROM `admin_user_notes` WHERE `cache_id` = OLD.`cache_id`;
            DELETE FROM `approval_status` WHERE `cache_id` = OLD.`cache_id`;
            DELETE FROM `reports` WHERE `cache_id` = OLD.`cache_id`;

            -- other data
            DELETE FROM `CACHE_ACCESS_LOGS` WHERE `cache_id` = OLD.`cache_id`;
            DELETE FROM `cache_arch` WHERE `cache_id` = OLD.`cache_id`;
            DELETE FROM `cache_visits2` WHERE `cache_id` = OLD.`cache_id`;
            DELETE FROM `notify_waiting` WHERE `cache_id` = OLD.`cache_id`;
            DELETE FROM `search_index` WHERE `cache_id` = OLD.`cache_id`;

            SET @deleting_cache = 0;

            -- There is also some OKAPI data, but it's temporary and will
            -- be cleaned up by cronjob.
        END IF;
    END;


DROP TRIGGER IF EXISTS cachesAfterDelete;;

CREATE TRIGGER `cachesAfterDelete` AFTER DELETE ON `caches`
    FOR EACH ROW BEGIN
        UPDATE `user`, (
            SELECT COUNT(*) AS `hidden_count` FROM `caches`
            WHERE `user_id` = OLD.`user_id` AND `status` IN (1, 2, 3)
        ) AS `c`
        SET `user`.`hidden_count` = `c`.`hidden_count`
        WHERE `user`.`user_id` = OLD.`user_id`;
    END;;


DELIMITER ;
