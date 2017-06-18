
-- 19.06.2017, deg
--

-- 4 tiggers can be easily replaced by right tables structures
--
ALTER TABLE `cache_desc` CHANGE `date_created` `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `cache_location` CHANGE `last_modified` `last_modified` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `caches` CHANGE `need_npa_recalc` `need_npa_recalc` TINYINT(1) NOT NULL DEFAULT '1';
DROP TRIGGER IF EXISTS `cacheDescBeforeInsert`;
DROP TRIGGER IF EXISTS `cacheLocationBeforeUpdate`;
DROP TRIGGER IF EXISTS `cacheLocationBeforeInsert`;
DROP TRIGGER IF EXISTS `cachesBeforeInsert`;