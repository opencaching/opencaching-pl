-- 2017-02-20: Refactoring of cache visits
-- @author: kojoty

-- This is the first stage of changes: new table (cache_visits2)


CREATE TABLE `cache_visits2` (
  `cache_id` int(11) NOT NULL,
  `user_id_ip` varchar(15) COMMENT 'user_id or used IP address',
  `type` varchar(1) NOT NULL COMMENT 'C=cache_visits; U=last_user_unique_visit; P=prepublication_user_visit',
  `count` int(11) NOT NULL DEFAULT '0',
  `visit_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for table `cache_visits2`
--
ALTER TABLE `cache_visits2`
  ADD PRIMARY KEY (`cache_id`,`user_id_ip`,`type`),
  ADD KEY `type` (`type`,`visit_date`);


