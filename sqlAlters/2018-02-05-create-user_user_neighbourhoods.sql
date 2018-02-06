-- 2018-02-05
-- @author: deg-pl

--
-- New table `user_user_neighbourhoods` for store additional user MyNeighbourhoods 
--

CREATE TABLE IF NOT EXISTS `user_neighbourhoods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `seq` int(11) NOT NULL COMMENT 'Number in sequence',
  `name` tinytext NOT NULL,
  `longitude` double NOT NULL,
  `latitude` double NOT NULL,
  `radius` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_2` (`user_id`,`seq`),
  KEY `user_id` (`user_id`),
  KEY `seq` (`seq`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores additional user MyNeighborhood areas';

--
-- Modify table `user` to store user notify preferences
--

ALTER TABLE `user`
  ADD `notify_caches` BOOLEAN NOT NULL DEFAULT TRUE COMMENT 'Notify user about new caches'  AFTER `notify_radius`,
  ADD `notify_logs` BOOLEAN NOT NULL DEFAULT TRUE COMMENT 'Notify user about new logs'  AFTER `notify_caches`,
  ADD   INDEX  `notify_caches` (`notify_caches`),
  ADD   INDEX  `notify_logs` (`notify_logs`);