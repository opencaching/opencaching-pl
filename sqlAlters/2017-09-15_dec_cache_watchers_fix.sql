
-- 2017-09-15 - This is fix of procedure I deliver a few days ago
-- There was a typo:  "watcher=watcher+1" instead of "watcher=watcher-1"

DELIMITER ;;

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
