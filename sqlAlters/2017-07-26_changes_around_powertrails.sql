-- 2017.07.26 
--

-- new table for fast counting of power-trail progress by user

DROP TABLE IF EXISTS `powertrail_progress`;
CREATE TABLE `powertrail_progress` (
  `user_id` int(11) NOT NULL,
  `pt_id` int(11) NOT NULL,
  `founds` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `powertrail_progress` ADD PRIMARY KEY( `user_id`, `pt_id`);
ALTER TABLE `powertrail_progress` ADD INDEX( `pt_id`);
ALTER TABLE `powertrail_progress` ADD INDEX( `user_id`);


-- additional indexes for other tables

ALTER TABLE `PowerTrail` ADD INDEX(`status`);
ALTER TABLE `powerTrail_caches` ADD INDEX(`cacheId`);
ALTER TABLE `PowerTrail_actionsLog` ADD INDEX(`PowerTrailId`);
ALTER TABLE `PowerTrail_cacheCandidate` ADD INDEX(`cacheId`);
ALTER TABLE `PowerTrail_owners` ADD INDEX(`PowerTrailId`);



DELIMITER ;;

--
-- This procedure increment user-founds-counter for selected geopath 
-- It is called from cache_logs trigger!    
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
        
        INSERT INTO powertrail_progress (user_id,pt_id,founds) VALUES (user_id,pt_id,1)
        ON DUPLICATE KEY UPDATE founds=founds+1;
      
    END IF;
END ;;


--
-- This procedure decrements user-founds-counter for selected geopath 
-- It is called from cache_logs trigger!    
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
        
        UPDATE powertrail_progress SET founds=founds-1
        WHERE user_id=p_user_id AND pt_id=p_pt_id AND founds>0;
      
    END IF;
END ;;


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
END ;;

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
END ;;

--
-- Just do nothing
--
DROP PROCEDURE IF EXISTS nop;;

CREATE PROCEDURE nop()
BEGIN
  
END ;;



DROP TRIGGER IF EXISTS cache_logs_insert;;

CREATE TRIGGER cache_logs_insert AFTER INSERT ON `cache_logs`
    FOR EACH ROW begin
        IF NEW.`deleted`=0 THEN
           -- new, not-deleted log is now added
           CALL inc_logs_stats(NEW.`type`, NEW.`user_id`, NEW.`cache_id`);        
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


DROP TRIGGER IF EXISTS cache_logs_update;;

CREATE TRIGGER cache_logs_update AFTER UPDATE ON `cache_logs`
    FOR EACH ROW begin
        IF OLD.`deleted`=0 AND NEW.`deleted`=0 THEN
            -- update of active log
            IF OLD.`type` <> NEW.`type` THEN
                CALL dec_logs_stats(OLD.`type`, OLD.`user_id`, OLD.`cache_id`);
                CALL inc_logs_stats(NEW.`type`, NEW.`user_id`, NEW.`cache_id`);  
            END IF;
        
        ELSEIF OLD.`deleted`=1 AND NEW.`deleted`=0 THEN
            -- log UNDELETE
            CALL inc_logs_stats(NEW.`type`, NEW.`user_id`, NEW.`cache_id`);
        
        ELSEIF OLD.`deleted`=0 AND NEW.`deleted`=1 THEN
            -- log DELETE
            CALL dec_logs_stats(OLD.`type`, OLD.`user_id`, OLD.`cache_id`);
        
        ELSE -- OLD.deleted=1 AND NEW.deleted=1
            -- do NOTHING - update of removed log without status change
            CALL nop();    
        END IF;
    END;;
    