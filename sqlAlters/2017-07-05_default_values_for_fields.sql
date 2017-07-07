-- 2017.07.05 deg
--

-- add default values (see issue #1079)
--

ALTER TABLE `PowerTrail` CHANGE `centerLatitude` `centerLatitude` FLOAT NOT NULL DEFAULT '0', CHANGE `centerLongitude` `centerLongitude` FLOAT NOT NULL DEFAULT '0', CHANGE `image` `image` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', CHANGE `conquestedCount` `conquestedCount` INT(11) NOT NULL DEFAULT '0', CHANGE `points` `points` FLOAT NOT NULL DEFAULT '0';

ALTER TABLE `PowerTrail_actionsLog` CHANGE `cacheId` `cacheId` INT(11) NOT NULL DEFAULT '0';

ALTER TABLE `powerTrail_caches` CHANGE `isFinal` `isFinal` SMALLINT(6) NOT NULL DEFAULT '0' COMMENT 'if cache is final cache = 1, not final cache = 0';

ALTER TABLE `caches` CHANGE `wp_qc` `wp_qc` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'QualityCaching',  CHANGE `default_desclang` `default_desclang` CHAR(2) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';

ALTER TABLE `reports` CHANGE `note` `note` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';

ALTER TABLE `email_user` CHANGE `date_sent` `date_sent` DATETIME NULL DEFAULT NULL;

ALTER TABLE `routes` CHANGE `options` `options` BLOB NULL DEFAULT NULL;
