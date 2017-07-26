
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


DELIMITER ;