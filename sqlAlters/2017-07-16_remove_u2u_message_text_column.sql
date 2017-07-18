-- 2017.07.16 
--

-- remove mail_text column which stored text of user2user message
-- (in context of issue #1080)

ALTER TABLE `email_user` DROP `mail_text`;