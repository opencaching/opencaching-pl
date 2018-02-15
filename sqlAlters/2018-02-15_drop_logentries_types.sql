-- 2018-02-15
-- @author: deg-pl

--
-- Data from logentries_types was migrated into Log object, so we dont need this table
DROP TABLE IF EXISTS `logentries_types`;

--
-- logentries.module duplicates data with logentries.eventid, so we'll drop this column,
-- but first set default to null, to easier and safer code change.
ALTER TABLE `logentries` CHANGE `module` `module` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;