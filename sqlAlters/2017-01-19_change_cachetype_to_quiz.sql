-- 2017-01-19 Change cache type from Quiz to Puzzel.
-- @author: Harrie Klomp

UPDATE `cache_type` SET `en` = 'Puzzle cache' WHERE `cache_type`.`id` = 7;
