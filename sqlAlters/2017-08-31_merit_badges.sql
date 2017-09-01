-- 2017-08-31: Merit Badges
-- @author: triPPer

-- new tables for MB


CREATE TABLE `badges` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `category_id` int(11) NOT NULL,
  `sequence` int(11) NOT NULL,
  `picture` varchar(255) NOT NULL,
  `trigger_type` int(11) NOT NULL COMMENT '    NONE = 0;       CRON = 1;      LOG_CACHE = 2;     LOG_CACHE_AUTHOR = 3;     TITLED_CACHE = 4;      TITLED_CACHE_AUTHOR = 5;         LOG_GEOPATH = 6;     LOG_GEOPATH_AUTHOR = 7;     RECOMMENDATION = 8;',
  `belonging_query` text NOT NULL,
  `gained_query` text NOT NULL,
  `short_description` char(100) NOT NULL,
  `description` text NOT NULL,
  `cfg_period_threshold` char(1) NOT NULL COMMENT 'Table in badge.php: Level of the badge. Column: (P)eriod or (T)hreshold',
  `cfg_show_positions` char(1) NOT NULL COMMENT '- none,  L - List M - Map',
  `graphic_author` text NOT NULL,
  `description_author` text NOT NULL,
  `attendant` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for table `badges`
--
ALTER TABLE `badges`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT dla tabeli `badges`
--
ALTER TABLE `badges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;



-- ----------------------------------------------------------------



CREATE TABLE `badge_area` (
  `badge_id` int(11) NOT NULL,
  `shape` geometry NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for table `badge_area`
--
ALTER TABLE `badge_area`
  ADD PRIMARY KEY (`badge_id`);


-- ----------------------------------------------------------------


CREATE TABLE `badge_cache` (
  `badge_id` int(11) NOT NULL,
  `cache_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ----------------------------------------------------------------


CREATE TABLE `badge_categories` (
  `id` int(11) NOT NULL,
  `sequence` int(11) NOT NULL,
  `name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for table `badge_categories`
--
ALTER TABLE `badge_categories`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT dla tabeli `badge_categories`
--
ALTER TABLE `badge_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


-- ----------------------------------------------------------------


CREATE TABLE `badge_levels` (
  `badge_id` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `threshold` int(11) NOT NULL,
  `picture` varchar(255) DEFAULT NULL COMMENT 'path to the picuture of level'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for table `badge_levels`
--
ALTER TABLE `badge_levels`
  ADD PRIMARY KEY (`badge_id`,`level`) USING BTREE;


-- ----------------------------------------------------------------


CREATE TABLE `badge_user` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `badge_id` int(11) NOT NULL,
  `level_id` int(11) NOT NULL,
  `level_date` datetime NOT NULL,
  `prev_val` int(11) NOT NULL,
  `curr_val` int(11) NOT NULL,
  `next_val` int(11) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for table `badge_user`
--
ALTER TABLE `badge_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`badge_id`);

--
-- AUTO_INCREMENT dla tabeli `badge_user`
--
ALTER TABLE `badge_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

