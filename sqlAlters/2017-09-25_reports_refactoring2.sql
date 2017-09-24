-- 2019.09.17 deg
-- Reports system refactoring
-- 

-- Support for R/O links to reports for users
-- 
ALTER TABLE `reports` ADD `secret` TINYTEXT NULL DEFAULT NULL AFTER `status`;

-- Copy email schema for both of cacheowner and submitter of report
-- 
INSERT INTO `email_schemas`(`name`, `version`, `object_type`, `shortdesc`, `text`, `receiver`, `author_id`, `date_created`, `deleted`) SELECT `name`, `version`, `object_type`, `shortdesc`, `text`, 0 AS receiver, `author_id`, `date_created`, `deleted` FROM `email_schemas` WHERE `receiver` = 2;

-- New table reports_log to store all activity related with report
--
CREATE TABLE `reports_log` (
  `id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `content` text COLLATE utf8_bin DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `poll_id` int(11) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE `reports_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `report_id` (`report_id`);

ALTER TABLE `reports_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- New table reports_poll to store polls in reports
--
  CREATE TABLE `reports_poll` (
  `id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `date_start` datetime NOT NULL,
  `date_end` datetime NOT NULL,
  `question` tinytext COLLATE utf8_bin NOT NULL,
  `ans1` tinytext COLLATE utf8_bin NOT NULL,
  `ans2` tinytext COLLATE utf8_bin NOT NULL,
  `ans3` tinytext COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE `reports_poll`
  ADD PRIMARY KEY (`id`),
  ADD KEY `report_id` (`report_id`),
  ADD KEY `date_end` (`date_end`);

ALTER TABLE `reports_poll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- New table reports_poll_vote to store votes in polls
--
CREATE TABLE `reports_poll_votes` (
  `poll_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE `reports_poll_votes`
  ADD PRIMARY KEY (`poll_id`,`user_id`),
  ADD KEY `vote` (`vote`),
  ADD KEY `poll_id` (`poll_id`);