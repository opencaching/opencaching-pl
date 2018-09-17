-- 2018-09-15
-- @author: deg-pl

-- Add missing PowerTrail indexes.

ALTER TABLE `PowerTrail` ADD FULLTEXT(`name`);
ALTER TABLE `PowerTrail` ADD INDEX(`type`);
ALTER TABLE `PowerTrail` ADD INDEX(`dateCreated`);
ALTER TABLE `PowerTrail` ADD INDEX(`cacheCount`);
ALTER TABLE `PowerTrail` ADD INDEX(`conquestedCount`);
ALTER TABLE `PowerTrail` ADD INDEX(`points`);