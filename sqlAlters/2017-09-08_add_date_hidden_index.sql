-- 2017-09-07: OKAPI optimization (https://github.com/opencaching/okapi/issues/476)
-- @author: following5

ALTER TABLE `caches` ADD INDEX (`date_hidden`);
