-- 2019.09.29 kojoty
-- Refactoring of authorization subsystem
-- 

-- sys_logins is now used only to prevent brute-force attack on user account
-- only login fails are stored in DB for no longer than 1 hour


ALTER TABLE sys_logins COMMENT = 'login fails from last hour by IP address';
ALTER TABLE sys_logins DROP `success`;

ALTER TABLE sys_sessions COMMENT = 'sessions of logged users'; 

