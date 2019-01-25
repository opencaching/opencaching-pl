DELIMITER ;;

-- DELIMITER must be in the first line.
-- Do not include the delimiter (double semicolon) in comments or strings. 

-- Changes will be automatically installed on production sites.
-- On developer sites, use http://local.opencaching.pl/Admin.DbUpdate/run


--
-- decrement cache watcher column - counter of users watched given cache.
--
DROP PROCEDURE IF EXISTS dec_cache_watchers;;

CREATE PROCEDURE dec_cache_watchers(
    IN `p_cache_id` INT(11)
)
BEGIN
    UPDATE caches SET watcher = watcher - 1
    WHERE `p_cache_id` = `cache_id` AND watcher > 0 LIMIT 1;
END;;


--
-- increment cache watcher column - counter of users watched given cache
--
DROP PROCEDURE IF EXISTS inc_cache_watchers;;

CREATE PROCEDURE inc_cache_watchers(
    IN `p_cache_id` INT(11)
)
BEGIN
    UPDATE caches SET watcher=watcher + 1
    WHERE `p_cache_id` = `cache_id` LIMIT 1;
END;;


DROP TRIGGER IF EXISTS cache_watches_insert;;

CREATE TRIGGER cache_watches_insert AFTER INSERT ON `cache_watches`
    FOR EACH ROW begin
           CALL inc_cache_watchers(NEW.`cache_id`);        
    END;;


DROP TRIGGER IF EXISTS cache_watches_delete;;

CREATE TRIGGER cache_watches_delete AFTER DELETE ON `cache_watches`
    FOR EACH ROW begin
           CALL dec_cache_watchers(OLD.`cache_id`);        
    END;;
    
    
DELIMITER ;
