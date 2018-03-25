-- 2018-03-22
-- @author: deg-pl

-- drop unused user.hide_flag
UPDATE `user` SET `verify_all`= 1 WHERE `hide_flag`= 1;
ALTER TABLE `user` DROP `hide_flag`;

-- drop user.country
ALTER TABLE `user` DROP `country`;

-- indexes for autoremove non activated users
ALTER TABLE `user` ADD INDEX(`last_login`);
ALTER TABLE `user` ADD INDEX(`is_active_flag`);
ALTER TABLE `user` ADD INDEX(`date_created`);

-- first try to add foregin keys in DB
-- admin_user_notes table
ALTER TABLE `admin_user_notes` ADD FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `admin_user_notes` ADD FOREIGN KEY (`admin_id`) REFERENCES `user`(`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
-- user_neighbourhoods table
ALTER TABLE `user_neighbourhoods` ADD FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
-- user_preferences table
ALTER TABLE `user_preferences` ADD FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
-- user_settings table
ALTER TABLE `user_settings` ADD FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;