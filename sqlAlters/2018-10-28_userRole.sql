-- kojoty: new field in user table: role


ALTER TABLE `user` ADD `role` SET('ocTeam','advUser','sysAdmin') NULL DEFAULT NULL 
COMMENT 'role of the user: ocTeam|advUser (confidential users with access to tested func.)|sysAdmin (for admins only)' AFTER `email`, ADD INDEX `roleIndex` (`role`);

-- add 'ocTeam' role to all admin users
UPDATE user SET role = role|1 WHERE user.admin = 1;
