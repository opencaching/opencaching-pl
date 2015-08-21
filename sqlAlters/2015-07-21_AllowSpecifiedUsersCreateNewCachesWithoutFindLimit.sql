--
-- Struktura tabeli dla tabeli `user_settings`
--

CREATE TABLE IF NOT EXISTS `user_settings` (
  `user_id` int(11) NOT NULL,
  `newcaches_no_limit` tinyint(4) NOT NULL COMMENT 'ignore finds limit for creating new cache. (User always may create new cache)',
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;