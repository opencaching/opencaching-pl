-- kojoty: new field in user table: role - once again - last sqlalter was buggy :(


-- drop previous version of role column 
ALTER TABLE `user` DROP `role`;


-- add "better" version of role column
ALTER TABLE `user` ADD `role` SET('ocTeamMember','advUser','newsPublisher','sysAdmin') NOT NULL 
COMMENT 'role of the user: ocTeamMember|advUser(confidential user-beta content)|newsPublisher|sysAdmins(tech. admin)' AFTER `email`, ADD INDEX `role_index` (`role`);


-- add 'ocTeam' role to all admin users
UPDATE user SET role = role|1 WHERE user.admin = 1;
