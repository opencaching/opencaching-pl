-- 2014-03-12 Missing index in cache_moved table
-- @author: starypatyk

ALTER TABLE `cache_moved` ADD INDEX `log_id` (`log_id`);
