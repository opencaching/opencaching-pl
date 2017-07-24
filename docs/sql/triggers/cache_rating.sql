DELIMITER ;;
    


DROP TRIGGER IF EXISTS cacheRatingAfterInsert;;

CREATE TRIGGER `cacheRatingAfterInsert` AFTER INSERT ON `cache_rating`
    FOR EACH ROW BEGIN
        UPDATE `caches` SET `topratings`=(
                SELECT COUNT(*) FROM `cache_rating` 
                WHERE `cache_rating`.`cache_id`=NEW.`cache_id`
            ) 
        WHERE `cache_id`=NEW.`cache_id`;
    END;;

    
    
DROP TRIGGER IF EXISTS cacheRatingAfterUpdate;;  
    
CREATE TRIGGER `cacheRatingAfterUpdate` AFTER UPDATE ON `cache_rating`
    FOR EACH ROW BEGIN
        IF OLD.`cache_id`!=NEW.`cache_id` THEN
            UPDATE `caches` SET `topratings`=(
                    SELECT COUNT(*) FROM `cache_rating` 
                    WHERE `cache_rating`.`cache_id`=OLD.`cache_id`
                  ) 
            WHERE `cache_id`=OLD.`cache_id`;
            
            UPDATE `caches` SET `topratings`=(
                    SELECT COUNT(*) FROM `cache_rating` 
                    WHERE `cache_rating`.`cache_id`=NEW.`cache_id`
                  ) 
            WHERE `cache_id`=NEW.`cache_id`;
        END IF;
    END;;

    
    
DROP TRIGGER IF EXISTS cacheRatingAfterDelete;;

CREATE TRIGGER `cacheRatingAfterDelete` AFTER DELETE ON `cache_rating`
		FOR EACH ROW BEGIN
    		UPDATE `caches` SET `topratings`=(
    		        SELECT COUNT(*) FROM `cache_rating` 
    		        WHERE `cache_rating`.`cache_id`=OLD.`cache_id`
		        ) 
        WHERE `cache_id`=OLD.`cache_id`;
		END;;

		
DELIMITER ;

