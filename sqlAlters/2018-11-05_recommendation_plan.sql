-- makotka: new table to store planned recommendations

CREATE TABLE `recommendation_plan` ( `cacheId` INT NOT NULL , `userId`INT NOT NULL , `logId` INT NOT NULL ) ENGINE = InnoDB;
ALTER TABLE `recommendation_plan` ADD PRIMARY KEY( `cacheId`, `userId`);