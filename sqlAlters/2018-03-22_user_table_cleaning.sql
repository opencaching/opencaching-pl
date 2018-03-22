-- 2018-03-22
-- @author: deg-pl

-- drop unused user.hide_flag
ALTER TABLE `user` DROP `hide_flag`;

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