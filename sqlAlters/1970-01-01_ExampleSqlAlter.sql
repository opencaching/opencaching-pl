-- this example file contain database structture changes for commit.
-- if you perform any change on db structure, please add db alter in file using following schema:


-- begin of example --

-- YYYY-MM-DD Feature name
-- @author: your name or nick
ALTER TABLE example_table CHANGE example_column `example_column_aaa` TINYINT(1) NOT NULL DEFAULT '1' COMMENT 'some comment';
UPDATE `example_table_2` SET `example_column_2`=1 WHERE 1

-- end of example
