-- 2017.08.07 deg
--

-- Refactoring of news
--

ALTER TABLE `news`
    ADD `title` TINYTEXT NULL DEFAULT NULL COMMENT 'Title of the news' AFTER `id`,
    CHANGE `content` `content` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `title`,
    ADD `user_id` INT(11) NOT NULL DEFAULT '0' COMMENT 'Author userID' AFTER `content`,
    ADD `edited_by` INT(11) NOT NULL DEFAULT '0' COMMENT 'UserID who last edited this news' AFTER `user_id`,
    ADD `hide_author` INT(1) NOT NULL DEFAULT '1' COMMENT 'Dislpay OC Team instead of author' AFTER `edited_by`,
    ADD `show_onmainpage` INT(1) NOT NULL DEFAULT '1' COMMENT 'Show news on mainpage' AFTER `hide_author`,
    ADD `show_notlogged` INT(1) NOT NULL DEFAULT '0' COMMENT 'Show news to not logged users' AFTER `show_onmainpage`,
    CHANGE `date_posted` `date_publication` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date from which to publish the news' AFTER `show_notlogged`,
    ADD `date_expiration` DATETIME NULL DEFAULT NULL AFTER `date_publication`,
    ADD `date_mainpageexp` DATETIME NULL DEFAULT NULL COMMENT 'End of publication on the main page' AFTER `date_expiration`,
    ADD `date_lastmod` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Last modification date' AFTER `date_mainpageexp`;

UPDATE `news` SET `date_mainpageexp` = `date_publication` + INTERVAL 31 DAY;
UPDATE `news` SET `show_onmainpage` = 0 WHERE `topic` != 2;
UPDATE `news` SET `show_notlogged` = 1 WHERE `display` = 1;
UPDATE `news` SET `date_expiration` = NOW() WHERE `display` = 0;

ALTER TABLE `news`
    ADD INDEX(`show_onmainpage`),
    ADD INDEX(`show_notlogged`),
    ADD INDEX(`date_publication`),
    ADD INDEX(`date_expiration`),
    ADD INDEX(`date_mainpageexp`);

ALTER TABLE `news`
    DROP `topic`,
    DROP `display`;

DROP TABLE news_topics;