--
-- Adds date_published column to caches, default NULL
--
ALTER TABLE `caches` ADD COLUMN `date_published` DATETIME DEFAULT NULL;

--
-- Updates existing caches date_published for ready, temporary unavailable and
-- archived status.
-- cache.date_published computation algorithm:
-- if (
--    there is earliest non-admin log for the cache
--    and log.date_created <= cache.date_hidden
-- ) {
--    date_temp = log.date_created;
-- } else {
--    date_temp =  cache.date_hidden;
-- }
-- if (cache.date_created >= date_temp)
--    cache.date_published = cache.date_created;
-- } else {
--    cache.date_published = cache.date_temp;
-- }
--
UPDATE `caches` c
JOIN `caches` cp ON c.`cache_id` = cp.`cache_id`
LEFT OUTER JOIN (
    SELECT `cache_id`, MIN(`date_created`) date_first_log FROM `cache_logs`
    WHERE `type` <> 12
    GROUP BY `cache_id`
) cfl ON cfl.`cache_id` = cp.`cache_id`
SET cp.`date_published` = IFNULL(
    GREATEST(
        cp.`date_created`,
        IFNULL(
            LEAST(cp.`date_hidden`, cfl.`date_first_log`),
            cp.`date_hidden`
        )
    ),
    cp.`date_created`
) WHERE c.`status` IN (1, 2, 3);

--
-- Updates existing caches date_published for blocked status only if there is
-- a non-admin earliest log for it.
-- cache.date_published computation algorithm:
-- if (
--    log.date_created <= cache.date_hidden
-- ) {
--    date_temp = log.date_created;
-- } else {
--    date_temp =  cache.date_hidden;
-- }
-- if (cache.date_created >= date_temp)
--    cache.date_published = cache.date_created;
-- } else {
--    cache.date_published = cache.date_temp;
-- }
--
UPDATE `caches` c
JOIN `caches` cp ON c.`cache_id` = cp.`cache_id`
JOIN (
    SELECT `cache_id`, MIN(`date_created`) date_first_log FROM `cache_logs`
    WHERE `type` <> 12
    GROUP BY `cache_id`
) cfl ON cfl.`cache_id` = cp.`cache_id`
SET cp.`date_published` = GREATEST(
    cp.`date_created`,
    LEAST(cp.`date_hidden`, cfl.`date_first_log`)
) WHERE c.`status` = 6;

--
-- Triggers modifications:
--

DROP TRIGGER IF EXISTS `cachesBeforeInsert`;

--
-- Trigger already defined before, existing parts:
-- 'SET NEW.`need_npa_recalc`=1;'
-- Modification sets date_published to current datetime if status is 1 or 2
--
DELIMITER ;;
CREATE TRIGGER `cachesBeforeInsert` BEFORE INSERT ON `caches`
    FOR EACH ROW
        BEGIN
            SET NEW.`need_npa_recalc`=1;
            SET NEW.`date_published` = (
                CASE WHEN NEW.`status` IN (1, 2) THEN NOW() ELSE NULL END
            );
        END;;
DELIMITER ;

DROP TRIGGER IF EXISTS `cachesBeforeUpdate`;

--
-- Trigger already defined before, existing parts:
-- from 'IF OLD.`longitude`!=NEW.`longitude` OR' to the first 'END IF;'
-- Modification sets date_published to current datetime if status is 1
-- NOTICE: date_published is not set if cache is archived before publication.
--
DELIMITER ;;
CREATE TRIGGER `cachesBeforeUpdate` BEFORE UPDATE ON `caches`
    FOR EACH ROW
        BEGIN
            IF OLD.`longitude`!=NEW.`longitude` OR 
                OLD.`latitude`!=NEW.`latitude` THEN
                    SET NEW.`need_npa_recalc`=1;
            END IF;
            IF OLD.`date_published` IS NULL AND NEW.`status` IN (1, 2) THEN
                SET NEW.`date_published` = NOW();
            END IF;
        END;;
DELIMITER ;
