SET NAMES 'utf8';
DROP TABLE IF EXISTS `import_caches_date`;
CREATE TABLE  `import_caches_date` (
`node_id` INT NOT NULL ,
`updated` INT NOT NULL ,
PRIMARY KEY (  `node_id` )
) ENGINE = MYISAM