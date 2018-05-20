-- 2018-05-20
-- @author: deg-pl

-- Clear rules_confirmed flag.
-- It is GDPR related - from 2018-05-25 every user will have to confirm read new rules
UPDATE `user` SET `rules_confirmed` = 0 WHERE 1;
