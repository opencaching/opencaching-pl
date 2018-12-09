-- following: add table for new cronjob engine

CREATE TABLE `cronjobs` (
  `name` varchar(60) NOT NULL,
  `last_run` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `cronjobs`
  ADD PRIMARY KEY (`name`);
