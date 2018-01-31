-- 2018-02-01
-- @author: deg-pl


-- add index to improve speed of /articles.php?page=s4
ALTER TABLE `caches` ADD INDEX(`founds`);