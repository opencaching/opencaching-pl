DELIMITER ;;


--
-- increment user-founds-counter for selected geopath
--
DROP PROCEDURE IF EXISTS inc_powertrail_progress;;

CREATE PROCEDURE inc_powertrail_progress(
    IN `user_id` int(11),
    IN `cache_id` int(11)
)
BEGIN
    DECLARE pt_id int(11) DEFAULT NULL;

    -- find powertrail of this cache
    SELECT PowerTrailId INTO pt_id
    FROM powerTrail_caches WHERE cacheId = cache_id LIMIT 1;

    IF pt_id IS NOT NULL THEN

        INSERT INTO powertrail_progress (user_id, pt_id, founds) VALUES (user_id, pt_id, 1)
        ON DUPLICATE KEY UPDATE founds = founds + 1;

    END IF;
END;;


--
-- decrement user-founds-counter for selected geopath
--
DROP PROCEDURE IF EXISTS dec_powertrail_progress;;

CREATE PROCEDURE dec_powertrail_progress(
    IN `p_user_id` int(11),
    IN `p_cache_id` int(11)
)
BEGIN
    DECLARE p_pt_id int(11) DEFAULT NULL;

    -- find powertrail of this cache
    SELECT PowerTrailId INTO p_pt_id
    FROM powerTrail_caches WHERE cacheId = p_cache_id LIMIT 1;

    IF p_pt_id IS NOT NULL THEN

        UPDATE powertrail_progress SET founds = founds - 1
        WHERE user_id = p_user_id AND pt_id = p_pt_id AND founds > 0;

    END IF;
END;;


--
-- callback triggered if there is a new log and counters should be incremented    
--
DROP PROCEDURE IF EXISTS inc_logs_stats;;
    
CREATE PROCEDURE inc_logs_stats(
    IN `type` int(11),
    IN `user_id` int(11),
    IN `cache_id` int(11)    
)
BEGIN
    CASE `type`
        WHEN 1 THEN -- FOUND
            CALL inc_powertrail_progress(user_id, cache_id);
        ELSE
            CALL nop();
    END CASE;
END;;


--
-- callback triggered if log is removed so counters should be decremented
--
DROP PROCEDURE IF EXISTS dec_logs_stats;;

CREATE PROCEDURE dec_logs_stats(
    IN `type` int(11),
    IN `user_id` int(11),
    IN `cache_id` int(11)    
)
BEGIN
    CASE `type`
        WHEN 1 THEN -- FOUND
            CALL dec_powertrail_progress(user_id, cache_id);
        ELSE
            CALL nop();
    END CASE;  
END;;


DROP TRIGGER IF EXISTS cl_insert;;  -- was merged to cache_logs_insert
DROP TRIGGER IF EXISTS cache_logs_insert;;

CREATE TRIGGER cache_logs_insert AFTER INSERT ON `cache_logs`
    FOR EACH ROW BEGIN
        IF NEW.`deleted` = 0 THEN
            -- new, not-deleted log is now added
            CALL inc_logs_stats(NEW.`type`, NEW.`user_id`, NEW.`cache_id`);

            IF (NEW.type = 1) THEN
                IF EXISTS (
                    SELECT 1
                    FROM user_finds
                    WHERE date = DATE(NEW.date)
                    AND user_id = NEW.user_id
                ) THEN
                    UPDATE user_finds SET number = number + 1
                    WHERE date = DATE(NEW.date) AND user_id = NEW.user_id;
                ELSE
                    INSERT INTO user_finds (date, user_id, number)
                    VALUES (NEW.date, NEW.user_id, 1);
                END IF;
            END IF;
        END IF;
    END;;
    

DROP TRIGGER IF EXISTS cl_update;;  -- was merged to cache_logs_update
DROP TRIGGER IF EXISTS cache_logs_update;;

CREATE TRIGGER cache_logs_update AFTER UPDATE ON `cache_logs`
    FOR EACH ROW BEGIN

        IF OLD.`deleted` = 0 AND NEW.`deleted` = 0 THEN
            -- update of active log
            IF OLD.`type` <> NEW.`type` THEN
                CALL dec_logs_stats(OLD.`type`, OLD.`user_id`, OLD.`cache_id`);
                CALL inc_logs_stats(NEW.`type`, NEW.`user_id`, NEW.`cache_id`);
            END IF;

        ELSEIF OLD.`deleted` = 1 AND NEW.`deleted` = 0 THEN
            -- log UNDELETE
            CALL inc_logs_stats(NEW.`type`, NEW.`user_id`, NEW.`cache_id`);

        ELSEIF OLD.`deleted` = 0 AND NEW.`deleted` = 1 THEN
            -- log DELETE
            CALL dec_logs_stats(OLD.`type`, OLD.`user_id`, OLD.`cache_id`);

        ELSE
            -- do NOTHING - update of removed log without status change
            CALL nop();
        END IF;

        IF (OLD.type = 1) THEN
            IF (NEW.deleted = 1 AND OLD.deleted = 0) OR
                NEW.type <> 1 OR
                DATE(NEW.date) <> DATE(OLD.date)
            THEN
                IF EXISTS (
                    SELECT 1 FROM user_finds
                    WHERE date = DATE(OLD.date) AND user_id = NEW.user_id
                ) THEN
                    UPDATE user_finds SET number = number - 1
                    WHERE date = DATE(OLD.date) AND user_id = NEW.user_id;
                END IF;
            END IF;
        END IF;

        IF (NEW.deleted = 0 AND NEW.type = 1) THEN
            IF (OLD.deleted = 1 OR
                OLD.type <> 1 OR
                DATE(NEW.date) <> DATE(OLD.date)
            ) THEN
                IF EXISTS (
                    SELECT 1 FROM user_finds
                    WHERE date = DATE(NEW.date) AND user_id = NEW.user_id
                ) THEN
                    UPDATE user_finds SET number = number + 1
                    WHERE date = DATE(NEW.date) AND user_id = NEW.user_id;
                ELSE
                    INSERT INTO user_finds (date, user_id, number)
                    VALUES (NEW.date, NEW.user_id, 1);
                END IF;
            END IF;
        END IF;

     END;;


DROP TRIGGER IF EXISTS cache_logs_delete;;

CREATE TRIGGER cache_logs_delete AFTER DELETE ON `cache_logs`
    FOR EACH ROW begin
        IF OLD.`deleted`=0 THEN
           -- not-deleted log is now removed
           CALL dec_logs_stats(OLD.`type`, OLD.`user_id`, OLD.`cache_id`);
        END IF;
    END;;


DELIMITER ;
