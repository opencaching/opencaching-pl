CREATE TABLE IF NOT EXISTS `caches_additions` (
  `cache_id` int(11) NOT NULL COMMENT 'geocache identifier (table caches)',
  `altitude` int(11) DEFAULT '0' COMMENT 'geocache altitude',
  `altitude_update_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'keep date time of last altitude update',
  UNIQUE KEY `cache_id` (`cache_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='contain useful but not very important geocache information ';