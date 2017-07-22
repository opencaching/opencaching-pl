DELIMITER ;;


DROP TRIGGER IF EXISTS cacheDescAfterInsert;;

CREATE TRIGGER `cacheDescAfterInsert` AFTER INSERT ON `cache_desc`
		FOR EACH ROW BEGIN
    		UPDATE `caches`, (
    		          SELECT `cache_id`, GROUP_CONCAT(DISTINCT `language` ORDER BY `language` SEPARATOR ',') AS `lang` 
    		          FROM `cache_desc` GROUP BY `cache_id`
		          ) AS `tbl2` 
        SET `caches`.`desc_languages`=`tbl2`.`lang` 
        WHERE `caches`.`cache_id`=`tbl2`.`cache_id` 
            AND `tbl2`.`cache_id`=NEW.`cache_id`;
		END;;

		
		
DROP TRIGGER IF EXISTS cacheDescAfterUpdate;;
		
CREATE TRIGGER `cacheDescAfterUpdate` AFTER UPDATE ON `cache_desc`
		FOR EACH ROW BEGIN
				IF OLD.`cache_id` != NEW.`cache_id` OR OLD.`language` != NEW.`language` THEN
    				UPDATE `caches`, (
    				            SELECT `cache_id`, GROUP_CONCAT(DISTINCT `language` ORDER BY `language` SEPARATOR ',') AS `lang` 
    				            FROM `cache_desc` GROUP BY `cache_id`
			            ) AS `tbl2` 
            SET `caches`.`desc_languages`=`tbl2`.`lang` 
            WHERE `caches`.`cache_id`=`tbl2`.`cache_id` 
                AND `tbl2`.`cache_id`=NEW.`cache_id`;
		
    				IF OLD.`cache_id` != NEW.`cache_id` THEN
				        UPDATE `caches`, (
				                SELECT `cache_id`, GROUP_CONCAT(DISTINCT `language` ORDER BY `language` SEPARATOR ',') AS `lang` 
				                FROM `cache_desc` GROUP BY `cache_id`
		                ) AS `tbl2` 
                SET `caches`.`desc_languages`=`tbl2`.`lang` 
                WHERE `caches`.`cache_id`=`tbl2`.`cache_id` 
                    AND `tbl2`.`cache_id`=OLD.`cache_id`;
				    END IF;
				END IF;
		END;;

		
		
DROP TRIGGER IF EXISTS cacheDescAfterDelete;;
		
CREATE TRIGGER `cacheDescAfterDelete` AFTER DELETE ON `cache_desc`
		FOR EACH ROW BEGIN
    		UPDATE `caches`, (
		                SELECT `cache_id`, GROUP_CONCAT(DISTINCT `language` ORDER BY `language` SEPARATOR ',') AS `lang` 
		                FROM `cache_desc` GROUP BY `cache_id`
                ) AS `tbl2` 
        SET `caches`.`desc_languages`=`tbl2`.`lang` 
        WHERE `caches`.`cache_id`=`tbl2`.`cache_id` 
            AND `tbl2`.`cache_id`=OLD.`cache_id`;
		END;;


DELIMITER ;
