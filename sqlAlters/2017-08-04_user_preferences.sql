-- 2017.07.26 
--

-- 

    
CREATE TABLE `user_preferences` (
  `user_id` int(11) NOT NULL,
  `key` varchar(25) NOT NULL COMMENT 'key identifies set of user preferences ',
  `value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='minor user setting in context of UI';


--
-- Indexes for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD PRIMARY KEY (`user_id`,`key`),
  ADD KEY `user_id` (`user_id`);
