ALTER TABLE `geokret_log` ADD `last_try` DATETIME NULL DEFAULT NULL AFTER `geokret_name`, ADD INDEX `last_try_index` (`last_try`);
