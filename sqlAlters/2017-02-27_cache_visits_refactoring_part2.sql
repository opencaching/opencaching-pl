-- 2017-02-27: Refactoring of cache visits - part 2
-- @author: kojoty

-- because of mariaDB issue with defaults for sql type datetime default value for visit_date column is removed

ALTER TABLE `cache_visits2` CHANGE `visit_date` `visit_date` DATETIME NOT NULL;



