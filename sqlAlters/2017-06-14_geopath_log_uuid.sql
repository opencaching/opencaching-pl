-- 14.06.2017, kojoty
--

-- uuids for poweerTrail/geopaths/cachesets :) logs
--
ALTER TABLE `PowerTrail_comments` ADD `uuid` VARCHAR(36) NULL DEFAULT NULL AFTER `deleted`;
ALTER TABLE `PowerTrail_comments` ADD INDEX(`uuid`);


-- generation of new uuid for each powertrail

UPDATE PowerTrail SET uuid=(
    SELECT CONCAT(SUBSTR(( @u:= UPPER( md5( UUID() ) ) ),1,8),'-',SUBSTR(@u,9,4),'-',SUBSTR(@u,13,4),'-',SUBSTR(@u,17,4),'-',SUBSTR(@u,21,12))
);
 

-- generation of new uuid for each powertrail log entry

UPDATE PowerTrail_comments SET uuid=(
    SELECT CONCAT(SUBSTR(( @u:= UPPER( md5( UUID() ) ) ),1,8),'-',SUBSTR(@u,9,4),'-',SUBSTR(@u,13,4),'-',SUBSTR(@u,17,4),'-',SUBSTR(@u,21,12))
);

