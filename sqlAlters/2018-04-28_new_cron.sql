-- 2018-04-04
-- @author: rapotek

DROP TABLE IF EXISTS `cron_history`;

CREATE TABLE `cron_history` (
    `section` VARCHAR(50) NOT NULL COMMENT 'the section where the task belong',
    `entrypoint` VARCHAR(512) NOT NULL COMMENT 'the task entry point, f.ex. function or method to execute',
    `uuid` CHAR(36) NOT NULL COMMENT 'the task uuid, unique for each execution',
    `scheduler_uuid` CHAR(36) NULL DEFAULT NULL COMMENT 'the uuid of scheduler task executing this one',
    `ttl` INTEGER NOT NULL DEFAULT 0 COMMENT 'the task time to live in seconds, 0 or less - infinite',
    `scheduled_time` INT(11) UNSIGNED NOT NULL COMMENT 'the timestamp when the task has been scheduled to start',
    `start_time` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT 'the timestamp when the task has started',
    `end_time` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT 'the timestmap when the task has finished',
    `result` BOOLEAN NULL DEFAULT NULL COMMENT 'the task result, undefined will be null',
    `output` VARCHAR(1024) DEFAULT NULL COMMENT 'the task stdout, first 1024 characters',
    `translate_output` BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'true if the output should be transated before display',
    `failed` BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'true if any Throwable has been thrown and caught while the task was running',
    `error_msg` VARCHAR(1024) NULL DEFAULT NULL COMMENT 'caught Throwable message if any',
    `translate_error_msg` BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'true if the error message should be transated before display',
    `last_modified` DATETIME NOT NULL COMMENT 'the row create or last update timestamp',
    PRIMARY KEY (`section`, `entrypoint`, `uuid`),
    KEY `scheduled_time` (`scheduled_time`),
    KEY `last_modified` (`last_modified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
