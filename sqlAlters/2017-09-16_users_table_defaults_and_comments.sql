
-- 2017-09-16 - small improvements around watch-mail settings + a few unused collumns drop



-- add comment with column desc
ALTER TABLE `user` CHANGE `watchmail_mode` `watchmail_mode` INT(11) NOT NULL DEFAULT '1' COMMENT '0=daily; 1=hourly; 2=weekly';

-- add comment with column desc + default change
ALTER TABLE `user` CHANGE `watchmail_day` `watchmail_day` INT(11) NOT NULL DEFAULT '7' COMMENT '1=mon; 7=sun';

-- add comment with column desc
ALTER TABLE `user` CHANGE `ozi_filips` `ozi_filips` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'path to the op dir at mobile device';


-- drop unused collumn from user table
ALTER TABLE `user` DROP `was_loggedin`;
ALTER TABLE `user` DROP `login_id`;
ALTER TABLE `user` DROP `post_news`;
ALTER TABLE `user` DROP `pmr_flag`;