-- 2018-07-20
-- @author: harrieklomp

-- Show correct Danish flag.
-- When Danish cache description is added there is no flag shown. Now it is DA.
-- Please correct this sql alter when it does nor fit in other databases.
UPDATE `languages` SET `short` = 'DK' WHERE `languages`.`id` = 11;
