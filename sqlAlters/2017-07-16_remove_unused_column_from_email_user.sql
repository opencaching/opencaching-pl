-- 2017.07.16 
--

-- column date_sent is never used
-- (in context of issue #983)

ALTER TABLE `email_user` DROP `date_sent`;