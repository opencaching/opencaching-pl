-- 2014-03-09 Missing index in PowerTrail_comments table
-- @author: starypatyk

ALTER TABLE `PowerTrail_comments` ADD INDEX `PowerTrailId` (`PowerTrailId`);
