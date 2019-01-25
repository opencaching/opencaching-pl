DELIMITER ;;


--
-- update column cache.desc_languages with the list of available description tranlsations
--
DROP TRIGGER IF EXISTS cacheDescAfterInsert;;

CREATE TRIGGER cacheDescAfterInsert AFTER INSERT ON cache_desc
    FOR EACH ROW BEGIN
        UPDATE caches
        SET caches.desc_languages = (
            SELECT GROUP_CONCAT(language)
            FROM cache_desc AS cd
            WHERE cd.cache_id = NEW.cache_id
        )
        WHERE caches.cache_id = NEW.cache_id;
    END;;


DROP TRIGGER IF EXISTS cacheDescAfterUpdate;;

CREATE TRIGGER cacheDescAfterUpdate AFTER UPDATE ON cache_desc
    FOR EACH ROW BEGIN
        IF OLD.cache_id != NEW.cache_id OR OLD.language != NEW.language THEN

            UPDATE caches
            SET caches.desc_languages = (
                SELECT GROUP_CONCAT(language)
                FROM cache_desc AS cd
                WHERE cd.cache_id = NEW.cache_id
            )
            WHERE caches.cache_id = NEW.cache_id;
      
            IF OLD.cache_id != NEW.cache_id THEN
      
                UPDATE caches
                SET caches.desc_languages = (
                    SELECT GROUP_CONCAT(language)
                    FROM cache_desc AS cd
                    WHERE cd.cache_id = OLD.cache_id
                )
                WHERE caches.cache_id = OLD.cache_id;
          
            END IF;
        END IF;
    END;;


DROP TRIGGER IF EXISTS cacheDescAfterDelete;;

CREATE TRIGGER cacheDescAfterDelete AFTER DELETE ON cache_desc
   FOR EACH ROW BEGIN
      UPDATE caches
      SET caches.desc_languages = (
          SELECT GROUP_CONCAT(language)
          FROM cache_desc AS cd
          WHERE cd.cache_id = OLD.cache_id
      )
      WHERE caches.cache_id = OLD.cache_id;
    END;;


DELIMITER ;
