
--
-- Fixes of issue https://github.com/opencaching/opencaching-pl/issues/515
--

-- SET watcher field default
ALTER TABLE caches CHANGE watcherwatcher INT(11) NULL DEFAULT '0';

-- fix all caches with watcher = null
UPDATE `caches` SET `watcher` = 0 WHERE `watcher` IS NULL;



--
-- Fixes of issue https://github.com/opencaching/opencaching-pl/issues/467
--

-- recalculate user stats
UPDATE `user` SET `founds_count` = (SELECT count(*) FROM `cache_logs` WHERE `cache_logs`.`user_id` = `user`.`user_id` AND `type` IN (1,7) AND `deleted` =0 );
