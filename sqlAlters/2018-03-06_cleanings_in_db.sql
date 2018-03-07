-- 2018-03-06
-- @author: deg-pl

-- table watches_waitingtypes is no longer needed - data was moved to Watchlist object
DROP TABLE IF EXISTS `watches_waitingtypes`;

-- See issue #1113. Last bulletin code I removed today
ALTER TABLE `user` DROP `get_bulletin`;

-- After password reminder refactoring, new_pw_date is not used
ALTER TABLE `user` DROP `new_pw_date`;

-- New feature - users can receive notifications from more than one neighbourhood
ALTER TABLE `user_neighbourhoods` ADD `notify` BOOLEAN NOT NULL DEFAULT FALSE AFTER `radius`;
ALTER TABLE `user_neighbourhoods` ADD INDEX(`notify`);
