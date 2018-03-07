-- 2018-03-06
-- @author: deg-pl

-- New feature - users can receive notifications from more than one neighbourhood
ALTER TABLE `user_neighbourhoods` ADD `notify` BOOLEAN NOT NULL DEFAULT FALSE AFTER `radius`;
ALTER TABLE `user_neighbourhoods` ADD INDEX(`notify`);

-- table watches_waitingtypes is no longer needed - data was moved to Watchlist object
DROP TABLE IF EXISTS `watches_waitingtypes`;