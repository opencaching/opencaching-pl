-- 2017-12-10 
-- @author: kojoty


-- cache-titled is used ordered by date so index should be usefull
ALTER TABLE `cache_titled` ADD INDEX( `date_alg` );

