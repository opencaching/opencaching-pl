
-- SET watcher field default
ALTER TABLE caches CHANGE watcherwatcher INT(11) NULL DEFAULT '0';

-- fix all caches with watcher = null
UPDATE `caches` SET `watcher` = 0 WHERE `watcher` IS NULL 
