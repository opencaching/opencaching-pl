
DELIMITER ;;

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

