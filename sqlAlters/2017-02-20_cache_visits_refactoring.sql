-- 2017-02-20: Refactoring of cache visits
-- @author: kojoty

-- old table contains dirty values which are useless
DROP TABLE IF EXISTS `cache_visits`;


CREATE TABLE `cache_visits` (
  `cache_id` int(11) NOT NULL,
  `user_id_ip` varchar(15) COMMENT 'user_id or used IP address',
  `type` varchar(1) NOT NULL COMMENT 'C=cache_visits; U=last_user_unique_visit; P=prepublication_user_visit',
  `count` int(11) NOT NULL DEFAULT '0',
  `visit_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for table `cache_visits`
--
ALTER TABLE `cache_visits`
  ADD PRIMARY KEY (`cache_id`,`user_id_ip`,`type`),
  ADD KEY `type` (`type`,`visit_date`);


