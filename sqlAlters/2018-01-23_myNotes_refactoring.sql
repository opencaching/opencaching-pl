-- 2018-01-23
-- @author: kojoty


-- it is needed to remove all duplicates in cache_notes table 
-- (for some number of caches there are more than one note created by user)
DROP TABLE IF EXISTS cache_notes_temp;
CREATE TABLE cache_notes_temp AS
  SELECT * FROM cache_notes GROUP BY cache_id, user_id;
  
ALTER TABLE `cache_notes_temp` ADD PRIMARY KEY( `cache_id`, `user_id` );
ALTER TABLE `cache_notes_temp` ADD INDEX( `note_id` );
ALTER TABLE `cache_notes_temp` ADD INDEX( `user_id` );


ALTER TABLE cache_notes_temp COMMENT = 'User notes for geocaches';
ALTER TABLE cache_notes_temp CHANGE `desc` `desc` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'User note for geocache';

DROP TABLE cache_notes;
RENAME TABLE cache_notes_temp TO cache_notes;


-- there should be also drop of note_id and desc_html columns but it needs OKAPI changes
-- I will change it separately 


-- it is needed to remove all duplicates in cache_mod_cords table 
-- (for some number of caches there are more than one note created by user)

DROP TABLE IF EXISTS cache_mod_cords_temp;
CREATE TABLE cache_mod_cords_temp AS
  SELECT `cache_id`,`user_id`,`date`,`longitude`,`latitude` 
  FROM cache_mod_cords GROUP BY cache_id, user_id;
  
ALTER TABLE `cache_mod_cords_temp` ADD PRIMARY KEY( `cache_id`, `user_id`);
ALTER TABLE `cache_mod_cords_temp` CHANGE `user_id` `user_id` INT(11) NOT NULL AFTER `cache_id`;
ALTER TABLE `cache_mod_cords_temp` ADD INDEX( `user_id`);
ALTER TABLE `cache_mod_cords_temp` CHANGE `date` `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE cache_mod_cords_temp COMMENT = 'Custom coordinates of geocache set by user';

DROP TABLE cache_mod_cords;
RENAME TABLE cache_mod_cords_temp TO cache_mod_cords;

-- cache_visits is no more used anywhere - cache_visits2 is used instead
DROP TABLE cache_visits;
ALTER TABLE cache_visits2 COMMENT = 'User visits at geocache webpage counter';

