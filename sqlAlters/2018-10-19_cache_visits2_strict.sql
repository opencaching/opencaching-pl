-- 2018-10-19
-- @author: rapotek

--
-- Alters the cache_visits2 table user_id_ip column adding empty string as default.
-- The alter is made to make the column ready for strict mode inserts with no value provided for it
-- The procedure is only to ensure there are not nulls in the column, i.e. values not provided in inserts
-- for not null varchar column are converted to empty strings by mysql in no strict mode. Otherwise there
-- could be an inconsistency in data and duplicate 'C'-type rows for cache_id after alter is done.
--
DROP PROCEDURE IF EXISTS make_cache_visits2_more_strict;

DELIMITER ;;

CREATE PROCEDURE make_cache_visits2_more_strict()
BEGIN
    SELECT COUNT(*) INTO @nullUserIdIps FROM `cache_visits2` WHERE user_id_ip IS NULL;
    IF @nullUserIdIps = 0 THEN
        ALTER TABLE `cache_visits2` MODIFY `user_id_ip` VARCHAR(15) NOT NULL DEFAULT '' COMMENT 'user_id or used IP address';
    END IF;
END;;

DELIMITER ;

CALL make_cache_visits2_more_strict;

DROP PROCEDURE IF EXISTS make_cache_visits2_more_strict;

--
-- Displays 'OK' as the Result if the column default has been modified to empty string
-- or 'Not OK' if the column default remains NULL
--
SELECT CONCAT(IFNULL(COLUMN_DEFAULT, 'Not '), 'OK') AS Result FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='cache_visits2' AND COLUMN_NAME='user_id_ip';

