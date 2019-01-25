DELIMITER ;;

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
    


--
-- user_finds - table used only by t102 stats...
--
DROP TRIGGER IF EXISTS cl_update;;

CREATE TRIGGER `cl_update` AFTER UPDATE ON `cache_logs`
    FOR EACH ROW begin
      
        -- log was deleted 
        IF ( old.type = 1 ) THEN
            IF ( new.deleted = 1 and old.deleted = 0  ) OR ( new.type <> 1 ) OR
                ( date(new.date) <> date( old.date )      
            ) THEN
             
                IF EXISTS (
                    SELECT 1 FROM user_finds 
                    WHERE date = date(old.date) AND user_id = new.user_id
                ) THEN 
                    
                    UPDATE user_finds SET number = number - 1 
                    WHERE date = date(old.date) and user_id = new.user_id;
                
                END if;                           
            END if;
        END if;

        -- log is curently deleted
        IF ( new.deleted = 0 and new.type = 1) THEN
        
            IF ( old.deleted = 1 ) OR ( old.type <> 1 ) OR
               ( date(new.date) <> date( old.date )  
            ) THEN
              
                IF EXISTS (
                    SELECT 1 FROM user_finds
                    WHERE date = date(new.date) and user_id = new.user_id
                ) THEN 
              
                  UPDATE user_finds SET number = number + 1 
                  WHERE date = date(new.date) and user_id = new.user_id;
                  
                ELSE
                        
                  INSERT into user_finds (date, user_id, number ) 
                  VALUES ( new.date, new.user_id, 1 );
                  
                END IF;
            END IF;      
        END if;      
  END;; -- FOR EACH ROW


DROP TRIGGER IF EXISTS cl_insert;;

CREATE TRIGGER `cl_insert` AFTER INSERT ON `cache_logs`
    FOR EACH ROW BEGIN 
      
        IF( new.deleted=0 AND new.type=1 ) THEN
            IF EXISTS (
                SELECT 1 FROM user_finds
                WHERE date = date( new.date ) AND user_id=new.user_id
            ) THEN
        
                UPDATE user_finds SET number=number+1 
                WHERE date = date( new.date ) AND user_id=new.user_id;

            ELSE 
            
                INSERT INTO user_finds( date, user_id, number )
                VALUES ( new.date, new.user_id, 1 );

            END IF ;
        END IF ;
    END;; -- FOR EACH ROW

    
DELIMITER ;
    