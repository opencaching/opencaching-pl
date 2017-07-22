

CREATE TRIGGER `sysSessionsAfterInsert` AFTER INSERT ON `sys_sessions`
    FOR EACH ROW BEGIN
        UPDATE `user` SET `user`.`last_login`=NEW.`last_login` 
        WHERE `user`.`user_id`=NEW.`user_id`;
    END