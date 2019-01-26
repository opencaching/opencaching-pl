DELIMITER ;;

-- DELIMITER must be in the first line.
-- Do not include the delimiter (double semicolon) in comments or strings. 

-- Changes will be automatically installed on production sites.
-- On developer sites, use http://local.opencaching.pl/Admin.DbUpdate/run


DROP PROCEDURE IF EXISTS dec_cache_watchers;;  -- merged to cache_watches_delete
DROP PROCEDURE IF EXISTS inc_cache_watchers;;  -- merged to cache_watches_insert


DROP TRIGGER IF EXISTS cache_watches_insert;;

CREATE TRIGGER cache_watches_insert AFTER INSERT ON `cache_watches`
    FOR EACH ROW begin
        UPDATE caches SET watcher = watcher + 1
        WHERE `cache_id` = NEW.cache_id;
    END;;


DROP TRIGGER IF EXISTS cache_watches_delete;;

CREATE TRIGGER cache_watches_delete AFTER DELETE ON `cache_watches`
    FOR EACH ROW begin
        IF IFNULL(@deleting_cache, 0) = 0 THEN
            UPDATE caches SET watcher = watcher - 1
            WHERE `cache_id` = OLD.cache_id AND watcher > 0;
        END IF;
    END;;
    
    
DELIMITER ;
