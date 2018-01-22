-- 2018-01-22
-- @author: kojoty


-- remove altitude_update_datetime coliumn from cache_additions table 
ALTER TABLE caches_additions DROP altitude_update_datetime;

-- change default atitude value to NULL
ALTER TABLE `caches_additions` CHANGE `altitude` `altitude` INT(11) NULL DEFAULT NULL COMMENT 'geocache altitude';
