-- 2019.09.17 deg
-- Reports system refactoring
-- 

-- In near future users be able to report not only caches, but also PowerTrails
-- 'object_type' - will contains type of object reported: 1=cache, 2=PowerTrail
-- 'PowerTrail_id' - id of PowerTrail reported
-- 'cache_id' - now can be null - when PowerTrail is reported
--
ALTER TABLE `reports` ADD `object_type` INT NOT NULL DEFAULT '1' COMMENT '1 - cache, 2 - PowerTrail' AFTER `id`,
    ADD `PowerTrail_id` INT NULL AFTER `cache_id`,
    CHANGE `cache_id` `cache_id` INT(11) NULL DEFAULT NULL;
ALTER TABLE `reports` ADD INDEX(`type`),
    ADD INDEX(`responsible_id`);

-- Table reports_watches will be used to store who of OC Team observes (watches)
-- reports. E-mails with changes of status of reports will be sent only to
-- those OC Team members
--
CREATE TABLE `reports_watches` (
  `report_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Stores info about OC Team users who watches reports';

ALTER TABLE `reports_watches`
    ADD PRIMARY KEY (`report_id`,`user_id`),
    ADD INDEX( `report_id`);

-- E-mail schema can be edited by OC Team in visual editor.
-- PL OC Team requested versioning system of email schemas (like Wiki)
--
ALTER TABLE `email_schemas` ADD `version` INT NOT NULL DEFAULT '1' AFTER `name`,
    ADD `object_type` INT(11) NULL DEFAULT '1' COMMENT '1 - cache, 2 - PowerTrail' AFTER `version`, 
    ADD `author_id` INT NULL DEFAULT NULL AFTER `receiver`,
    ADD `date_created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER `author_id`,
    ADD `deleted` BOOLEAN NOT NULL DEFAULT FALSE AFTER `date_created`;
ALTER TABLE `email_schemas` ADD INDEX(`version`),
    ADD INDEX(`name`),
    ADD INDEX(`object_type`),
    ADD INDEX(`receiver`),
    ADD INDEX(`deleted`);