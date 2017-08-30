-- 2017.08.30 kojoty
--

-- There is unneeded counter of watched caches by user in table user.
-- Lets drop it.

ALTER TABLE user DROP COLUMN cache_watches;

-- Every cache has its counter of watchers. 
-- This counter was updated in code but for exampel not by OKAPI and mobile page.
-- Now it will be updated by triggers.


DELIMITER ;;

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
    UPDATE caches SET watcher=watcher+1
    WHERE `p_cache_id` = `cache_id` AND watcher > 0 LIMIT 1;
END ;;

--
-- This trigger increments cache watcher column - counter of users watched 
-- given cache.
--

DROP TRIGGER IF EXISTS cache_watches_insert;;

CREATE TRIGGER cache_watches_insert AFTER INSERT ON `cache_watches`
    FOR EACH ROW begin
           CALL inc_cache_watchers(NEW.`cache_id`);        
    END;;

--
-- This trigger decrements cache watcher column - counter of users watched 
-- given cache.
--
    
DROP TRIGGER IF EXISTS cache_watches_delete;;

CREATE TRIGGER cache_watches_delete AFTER DELETE ON `cache_watches`
    FOR EACH ROW begin
           CALL dec_cache_watchers(OLD.`cache_id`);        
    END;;
    

DELIMITER ;