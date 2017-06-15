
-- 14.06.2017, kojoty
--

-- uuids for poweerTrail/geopaths/cachesets :) logs
--
ALTER TABLE `PowerTrail_comments` ADD `uuid` VARCHAR(36) NULL DEFAULT NULL AFTER `deleted`;
ALTER TABLE `PowerTrail_comments` ADD INDEX(`uuid`);

