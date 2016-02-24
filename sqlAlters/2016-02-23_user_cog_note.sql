--
-- Adding field to database for keeping the COG-note about user
--

ALTER TABLE `user` ADD `cog_note` VARCHAR( 4096 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'COG note for user' AFTER `verify_all`
